from flask import Flask, request, jsonify
import torch
import cv2
import numpy as np
import os
import random
import string

app = Flask(__name__)

# Tạo thư mục results nếu chưa tồn tại
if not os.path.exists('results'):
    os.makedirs('results')

# Load mô hình YOLOv5 custom
model = torch.hub.load('yolov5', 'custom', path='models/best.pt', source='local')

def random_string(length=8):
    """Tạo tên thư mục ngẫu nhiên"""
    letters = string.ascii_lowercase
    return ''.join(random.choice(letters) for i in range(length))

@app.route('/detect-frame', methods=['POST'])
def detect_frame():
    if 'image' not in request.files:
        return jsonify({'error': 'No image file provided'}), 400

    file = request.files['image']

    # Kiểm tra nếu file rỗng
    if file and file.filename == '':
        return jsonify({'error': 'No selected file'}), 400

    img_bytes = file.read()

    # Kiểm tra nếu img_bytes rỗng
    if not img_bytes:
        return jsonify({'error': 'Empty image file'}), 400

    # Đọc ảnh từ byte -> numpy -> BGR
    nparr = np.frombuffer(img_bytes, np.uint8)

    # Kiểm tra xem ảnh có thể đọc được không
    frame = cv2.imdecode(nparr, cv2.IMREAD_COLOR)
    if frame is None:
        return jsonify({'error': 'Failed to decode image'}), 400

    # Dự đoán YOLOv5 (convert sang RGB trước)
    results = model(frame[..., ::-1])  # RGB

    detections = results.pandas().xyxy[0].to_dict(orient="records")

    output = []
    motorcyclist_detected = False

    # Tạo thư mục cha ngẫu nhiên
    parent_folder = os.path.join('results', random_string())
    os.makedirs(parent_folder, exist_ok=True)

    # Duyệt qua tất cả các đối tượng được phát hiện
    for idx, det in enumerate(detections):
        label = det["name"]
        if label == "motorcyclist":  # Kiểm tra nếu là motorcyclist
            motorcyclist_detected = True
            xmin, ymin, xmax, ymax = int(det["xmin"]), int(det["ymin"]), int(det["xmax"]), int(det["ymax"])

            # Tạo thư mục cho motorcyclist
            motorcyclist_folder = os.path.join(parent_folder, f'motorcyclist{idx + 1}')
            os.makedirs(motorcyclist_folder, exist_ok=True)

            # Tạo thư mục con cho motorcyclist
            motorcyclist_subfolder = os.path.join(motorcyclist_folder, 'motorcyclist')
            os.makedirs(motorcyclist_subfolder, exist_ok=True)

            # Lưu ảnh motorcyclist
            motorcyclist_img = frame[ymin:ymax, xmin:xmax]
            motorcyclist_img_path = os.path.join(motorcyclist_subfolder, 'motorcyclist.jpg')
            cv2.imwrite(motorcyclist_img_path, motorcyclist_img)

            # Tạo thư mục con cho license plate
            license_plate_folder = os.path.join(motorcyclist_folder, 'licenseplate')
            os.makedirs(license_plate_folder, exist_ok=True)

            # Kiểm tra nếu có biển số và cắt biển số trong phạm vi motorcyclist
            license_plate_img = None
            for obj in detections:
                if obj["name"] == "licenseplate":
                    # Kiểm tra biển số có nằm trong phạm vi motorcyclist không
                    plate_xmin, plate_ymin, plate_xmax, plate_ymax = int(obj["xmin"]), int(obj["ymin"]), int(obj["xmax"]), int(obj["ymax"])
                    if xmin <= plate_xmin and xmax >= plate_xmax and ymin <= plate_ymin and ymax >= plate_ymax:
                        license_plate_img = frame[plate_ymin:plate_ymax, plate_xmin:plate_xmax]
                        license_plate_path = os.path.join(license_plate_folder, 'license_plate.jpg')
                        cv2.imwrite(license_plate_path, license_plate_img)
                        break  # Nếu tìm thấy biển số, không cần tiếp tục tìm

            # Kiểm tra helmet (nếu có) và cắt helmet trong phạm vi motorcyclist
            helmet_detected = False
            for obj in detections:
                if obj["name"] == "helmet":
                    helmet_xmin, helmet_ymin, helmet_xmax, helmet_ymax = int(obj["xmin"]), int(obj["ymin"]), int(obj["xmax"]), int(obj["ymax"])

                    # Kiểm tra helmet có nằm trong phạm vi motorcyclist không
                    if xmin <= helmet_xmin and xmax >= helmet_xmax and ymin <= helmet_ymin and ymax >= helmet_ymax:
                        # Cắt phần helmet
                        helmet_img = frame[helmet_ymin:helmet_ymax, helmet_xmin:helmet_xmax]
                        helmet_folder = os.path.join(motorcyclist_folder, 'helmet')
                        os.makedirs(helmet_folder, exist_ok=True)
                        helmet_path = os.path.join(helmet_folder, "helmet.jpg")
                        cv2.imwrite(helmet_path, helmet_img)
                        helmet_detected = True
                        break  # Không cần tiếp tục tìm helmet nữa

            output.append({
                "motorcyclist_folder": motorcyclist_folder,
                "motorcyclist_img_path": motorcyclist_img_path,
                "license_plate_path": license_plate_path if license_plate_img is not None else "Not detected",
                "helmet_detected": helmet_detected
            })

    return jsonify({
        "motorcyclists": output,
        "total_motorcyclists": len(output),
        "motorcyclist_detected": motorcyclist_detected
    })

if __name__ == '__main__':
    app.run(debug=True)
