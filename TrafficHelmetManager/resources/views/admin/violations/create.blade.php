@extends('Admin.layouts.app')
@section('title', 'Tải Lên Vi Phạm')
@section('content')

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Tải Lên Vi Phạm</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang Chủ</a></li>
                    <li class="breadcrumb-item active">Tải Lên Vi Phạm</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Camera và Canvas -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white font-weight-bold">
                        Chọn Ảnh Từ Thiết Bị
                    </div>
                    <div class="card-body position-relative text-center">

                        <!-- Input file (ẩn) -->
                        <input type="file" id="imageInput" accept="image/*" style="display: none;">
                        
                         <!-- Button chọn ảnh -->
                         <button class="btn btn-outline-primary mb-3" onclick="document.getElementById('imageInput').click();">
                            <i class="fas fa-image"></i> Chọn Ảnh Vi Phạm
                        </button>

                        <!-- Hiển thị ảnh được chọn -->
                        <div style="position: relative; display: inline-block; width: 100%;">
                            <img id="previewImage" style="width: 100%; border-radius: 10px; display: none;">
                            <canvas id="canvasOverlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none;"></canvas>
                        </div>
                    
                    </div>
                </div>
            </div>

            <!-- Danh sách vi phạm -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-secondary text-white font-weight-bold">
                        Danh Sách Vi Phạm
                    </div>
                    <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                        <div class="row" id="violationList">
                            <!-- Các vi phạm sẽ được hiển thị ở đây -->
                            <!-- Ví dụ 1 mục: -->
                            {{-- 
                            <div class="col-12 mb-3">
                                <div class="card border-danger">
                                    <img src="link_anh.jpg" class="card-img-top" alt="Vi phạm">
                                    <div class="card-body p-2">
                                        <h6 class="card-title text-danger">Không đội mũ bảo hiểm</h6>
                                        <p class="card-text"><small>Biển số: 59X2-12345</small></p>
                                    </div>
                                </div>
                            </div>
                            --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    const imageInput = document.getElementById("imageInput");
    const previewImage = document.getElementById("previewImage");
    const canvas = document.getElementById("canvasOverlay");
    const ctx = canvas.getContext("2d");
    const sendBtn = document.getElementById("captureBtn");

    imageInput.addEventListener("change", function () {
        const file = this.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            previewImage.src = e.target.result;
            previewImage.style.display = "block";
            sendBtn.disabled = false;

            // Delay để canvas vẽ đúng kích thước ảnh
            setTimeout(() => {
                canvas.width = previewImage.clientWidth;
                canvas.height = previewImage.clientHeight;
                ctx.clearRect(0, 0, canvas.width, canvas.height);
            }, 100);
        };
        reader.readAsDataURL(file);
    });

    sendBtn.addEventListener("click", async () => {
        const tempCanvas = document.createElement("canvas");
        tempCanvas.width = previewImage.naturalWidth;
        tempCanvas.height = previewImage.naturalHeight;
        tempCanvas.getContext("2d").drawImage(previewImage, 0, 0);

        const blob = await new Promise(resolve => tempCanvas.toBlob(resolve, "image/jpeg"));
        const formData = new FormData();
        formData.append("image", blob, "upload.jpg");

        const res = await fetch("/detect-frame", {
            method: "POST",
            body: formData
        });
        const data = await res.json();

        // Clear canvas
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Vẽ bounding boxes
        data.motorcyclists.forEach(obj => {
            const box = obj.bbox;
            const scaleX = canvas.width / previewImage.naturalWidth;
            const scaleY = canvas.height / previewImage.naturalHeight;

            const x = box.xmin * scaleX;
            const y = box.ymin * scaleY;
            const width = (box.xmax - box.xmin) * scaleX;
            const height = (box.ymax - box.ymin) * scaleY;

            ctx.strokeStyle = "red";
            ctx.lineWidth = 2;
            ctx.strokeRect(x, y, width, height);
            ctx.font = "14px Arial";
            ctx.fillStyle = "red";
            ctx.fillText("No Helmet", x + 5, y - 5);

            // Thêm vào danh sách
            const div = document.createElement("div");
            div.className = "col-12 mb-3";
            div.innerHTML = `
                <div class="card border-danger">
                    <img src="${obj.motorcyclist_img}" class="card-img-top" alt="Vi phạm">
                    <div class="card-body p-2">
                        <h6 class="card-title text-danger">Không đội mũ bảo hiểm</h6>
                        <p class="card-text"><small>Biển số: ${obj.license_plate || 'Không rõ'}</small></p>
                    </div>
                </div>`;
            document.getElementById("violationList").appendChild(div);
        });
    });
</script>
@endsection
