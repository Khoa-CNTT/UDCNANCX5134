@extends('Admin.layouts.app')
@section('title', 'Tải Lên Vi Phạm')
@section('content')

<section class="content-header">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="mb-0">Tải Lên Vi Phạm</h1>
            <ol class="breadcrumb float-sm-right mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang Chủ</a></li>
                <li class="breadcrumb-item active">Tải Lên Vi Phạm</li>
            </ol>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">

            <!-- Cột ảnh -->
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white font-weight-bold">
                        Chọn Hình Ảnh Vi Phạm
                    </div>
                    <div class="card-body text-center">
                        <input type="file" id="imageInput" accept="image/*" style="display: none;">
                        <button class="btn btn-outline-primary mb-3" onclick="document.getElementById('imageInput').click();">
                            <i class="fas fa-image"></i> Chọn Ảnh
                        </button>
                        <img id="previewImage" class="img-fluid rounded shadow border rounded w-100" style="display: none; margin-left: auto; margin-right: auto;">
                        <canvas id="canvasOverlay" class="position-absolute top-0 start-0 w-100 h-100" style="pointer-events: none;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Kết quả -->
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white font-weight-bold">
                        Kết Quả Vi Phạm
                    </div>
                    <div class="card-body">
                        <div id="violationList" class="row"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<script>
    const imageInput = document.getElementById("imageInput");
    const previewImage = document.getElementById("previewImage");
    const violationList = document.getElementById("violationList");

    function getCurrentTime() {
        const now = new Date();
        const time = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        const date = now.toLocaleDateString('vi-VN');
        return `${time} - ${date}`;
    }

    async function renderViolations(result) {
        console.log(result);
        violationList.innerHTML = '';

        if (!result.motorcyclists || result.motorcyclists.length === 0) {
            violationList.innerHTML = `
                <div class="col-12 text-center text-muted">
                    <i class="fas fa-info-circle"></i> Không phát hiện vi phạm.
                </div>`;
            return;
        }

        result.motorcyclists.forEach((item) => {
            const licensePlateHtml = (item.license_plate_img && item.license_plate_img !== false)
                ? `<img src="${item.license_plate_img}" class="border rounded" style="height: 150px; width: 150px;" alt="Biển số">`
                : `<span class="text-muted"><em>Chưa xác định</em></span>`;

            const html = `
                <div class="col-12 mb-3">
                    <div class="d-flex align-items-center border p-2 rounded shadow-sm">
                        <img src="${item.motorcyclist_img}" class="img-thumbnail me-3" style="width: 200px; height: 300px;">
                        <div class="flex-grow-1" style="margin-left: 15px;">
                            <div class="mb-2">
                                <b class="fw-bold">Biển số:</b><br>
                                ${licensePlateHtml}
                            </div>
                            <div class="mb-2">
                                <b class="fw-bold">Thời gian:</b><br>
                                ${getCurrentTime()}
                            </div>
                            <span class="badge bg-danger">Không đội mũ</span>
                        </div>
                    </div>
                </div>
            `;
            violationList.insertAdjacentHTML("beforeend", html);
        });
    }

    imageInput.addEventListener("change", function () {
        const file = this.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            previewImage.src = e.target.result;
            previewImage.style.display = "block";
        };
        reader.readAsDataURL(file);

        const formData = new FormData();
        formData.append('image', file);

        fetch('http://127.0.0.1:5000/detect-frame', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => renderViolations(data))
        .catch(error => {
            console.error("Lỗi:", error);
            alert("Không thể gửi ảnh lên server.");
        });
    });
</script>

@endsection
