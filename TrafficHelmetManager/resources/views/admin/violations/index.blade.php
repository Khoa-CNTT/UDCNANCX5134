@extends('Admin.layouts.app')
@section('title', 'Phát Hiện Vi Phạm')
@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Xem Trực Tiếp</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang Chủ</a></li>
                    <li class="breadcrumb-item active">Xem Trực Tiếp</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <!-- /.row -->
        <div class="row">
            <div class="col-8">
                <div class="card card-primary">
                    <div class="card-header font-weight-bold">  
                        CAMERA TRỰC TIẾP
                    </div>
                    <div class="card-body text-center">
                        <video id="liveCamera" autoplay playsinline muted style="width: 100%;"></video>
                    </div>
                </div>
                <!-- /.card -->
            </div>
            <div class="col-4">
                <div class="card card-secondary">
                    <div class="card-header font-weight-bold">  
                        DANH SÁCH VI PHẠM TRONG KHUNG HÌNH
                    </div>
                    <div class="card-body">
                        <div class="row" id="violationList">

                        </div>
                    </div>
                </div>
            </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
</section>
<script>
    async function renderViolations(result) {

        console.log(result);

        const container = document.getElementById("violationList");
        container.innerHTML = ''; // Xóa cũ nếu có

        const getCurrentTime = () => {
            const now = new Date();
            const time = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            const date = now.toLocaleDateString('vi-VN');
            return `${time} - ${date}`;
        }

        result.motorcyclists.forEach((item, index) => {

                const licensePlateHtml = (item.license_plate_img && item.license_plate_img !== false)
                    ? `<img src="${item.license_plate_img}" class="border" style="height: 150px; width: 150px;" alt="Biển số">`
                    : `<span class="text-muted"><em>Chưa xác định</em></span>`;

                const html = `
                    <div class="col-12 mb-3">
                        <div class="d-flex align-items-center border p-2 rounded shadow-sm">
                            <img src="${item.motorcyclist_img}" class="img-thumbnail mr-3" style="width: 200px; height: 300px;">
                            <div class="flex-grow-1">
                                <div class="mb-2">
                                    <span class="font-weight-bold">Biển số:</span><br>
                                    ${licensePlateHtml}
                                </div>
                                <div class="mb-2">
                                    <span class="font-weight-bold">Thời gian:</span><br>
                                    ${getCurrentTime()}
                                </div>
                                <span class="badge badge-danger">Không đội mũ</span>
                            </div>
                        </div>
                    </div>
                `;

            container.insertAdjacentHTML("beforeend", html);
        });
    }


    const video = document.getElementById('liveCamera');
    const canvas = document.createElement('canvas');

    // Hàm mở webcam
    async function startCamera() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
            video.srcObject = stream;
        } catch (err) {
            alert("Không thể mở camera: " + err.message);
        }
    }

    // Hàm chụp frame và gửi về server
    async function captureAndSendFrame() {
        if (video.videoWidth === 0 || video.videoHeight === 0) {
            console.warn('Chưa có kích thước video, bỏ qua frame');
            return;
        }

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        canvas.toBlob(async function(blob) {
            if (!blob) {
                console.error("Không thể tạo blob từ canvas");
                return;
            }

            const formData = new FormData();
            formData.append('image', blob, 'frame.jpg');

            try {
                const response = await fetch('http://localhost:5000/detect-frame', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                console.log('Kết quả từ AI:', result);

                if (result.motorcyclists && result.motorcyclists.length > 0) {
                    renderViolations(result);
                }

            } catch (error) {
                console.error('Lỗi khi gửi ảnh:', error);
            }
        }, 'image/jpeg');
    }

    // Mỗi 5 giây gửi 1 frame
    setInterval(captureAndSendFrame, 500);

    // Khởi động camera
    window.addEventListener('DOMContentLoaded', startCamera);
</script>

@endsection