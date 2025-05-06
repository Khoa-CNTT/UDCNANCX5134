from flask import Flask, request, jsonify, send_from_directory
import torch
import cv2
import numpy as np
import base64
import os
import random
import string
from flask_cors import CORS


app = Flask(__name__)
CORS(app)  # Cho phép tất cả origin gọi API


# Tạo thư mục results nếu chưa tồn tại
if not os.path.exists('results'):
    os.makedirs('results')

# Load mô hình YOLOv5 custom
model = torch.hub.load('yolov5', 'custom', path='models/best.pt', source='local')

@app.route('/results/<path:filename>')
def serve_results(filename):
    # Trả về file trong thư mục "results"
    return send_from_directory(os.path.join(os.getcwd(), 'results'), filename)

def random_string(length=8):
    """Tạo tên thư mục ngẫu nhiên"""
    letters = string.ascii_lowercase
    return ''.join(random.choice(letters) for i in range(length))

# Hàm chuyển ảnh sang base64
def image_to_base64(image):
    _, buffer = cv2.imencode('.jpg', image)
    base64_str = base64.b64encode(buffer).decode('utf-8')
    return "data:image/jpeg;base64," + base64_str

# Endpoint nhận và xử lý ảnh
@app.route('/detect-frame', methods=['POST'])
def detect_frame():
    if 'image' not in request.files:
        return jsonify({'error': 'No image file provided'}), 400

    file = request.files['image']
    if file and file.filename == '':
        return jsonify({'error': 'No selected file'}), 400

    img_bytes = file.read()
    if not img_bytes:
        return jsonify({'error': 'Empty image file'}), 400

    nparr = np.frombuffer(img_bytes, np.uint8)
    frame = cv2.imdecode(nparr, cv2.IMREAD_COLOR)
    if frame is None:
        return jsonify({'error': 'Failed to decode image'}), 400

    # Dự đoán bằng YOLOv5 (RGB)
    results = model(frame[..., ::-1])
    detections = results.pandas().xyxy[0].to_dict(orient="records")

    output = []
    motorcyclist_detected = False

    for idx, det in enumerate(detections):
        label = det["name"]
        confidence = det["confidence"]
        if label == "motorcyclist" and confidence >= 0.7:
            xmin, ymin, xmax, ymax = int(det["xmin"]), int(det["ymin"]), int(det["xmax"]), int(det["ymax"])

            # Kiểm tra helmet trong phạm vi motorcyclist
            helmet_detected = False
            for obj in detections:
                if obj["name"] == "helmet" and obj["confidence"] >= 0.3:
                    hxmin, hymin = int(obj["xmin"]), int(obj["ymin"])
                    hxmax, hymax = int(obj["xmax"]), int(obj["ymax"])
                    if xmin <= hxmin and xmax >= hxmax and ymin <= hymin and ymax >= hymax:
                        helmet_detected = True
                        break  # Nếu đã đội mũ thì bỏ qua

            # ➤ Chỉ xử lý nếu KHÔNG đội mũ
            if not helmet_detected:
                motorcyclist_detected = True

                # Lấy ảnh motorcyclist
                motorcyclist_img = frame[ymin:ymax, xmin:xmax]
                motorcyclist_img_base64 = image_to_base64(motorcyclist_img)

                # Kiểm tra và lấy ảnh biển số
                license_plate_img = None
                for obj in detections:
                    if obj["name"] == "licenseplate":
                        pxmin, pymin = int(obj["xmin"]), int(obj["ymin"])
                        pxmax, pymax = int(obj["xmax"]), int(obj["ymax"])
                        if xmin <= pxmin and xmax >= pxmax and ymin <= pymin and ymax >= pymax:
                            license_plate_img = frame[pymin:pymax, pxmin:pxmax]
                            break

                license_plate_img_base64 = image_to_base64(license_plate_img) if license_plate_img is not None else None

                # Trả kết quả
                output.append({
                    "motorcyclist_img": motorcyclist_img_base64,
                    "license_plate_img": license_plate_img_base64,
                    "helmet_detected": helmet_detected
                })

    return jsonify({
        "motorcyclists": output,
        "total_motorcyclists": len(output),
        "motorcyclist_detected": motorcyclist_detected
    })

if __name__ == '__main__':
    app.run(debug=True)
