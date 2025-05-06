<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ Thống Báo Cáo Vi Phạm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
        }

        .navbar {
            background-color: #004c8c;
        }

        .navbar-brand,
        .nav-link {
            color: white;
        }

        .navbar-nav .nav-link:hover {
            color: white;
            text-decoration: underline;
        }

        .header {
            background-color: #004c8c;
            color: white;
            padding: 10px 0;
            text-align: center;
        }

        .main-content {
            margin-top: 30px;
            padding: 20px;
        }

        .footer {
            background-color: #004c8c;
            color: white;
            padding: 10px 0;
            padding-top: 28px;
            text-align: center;
            bottom: 0;
            width: 100%;
        }

        .card {
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-submit {
            background-color: #007bff;
            color: white;
            border: none;
        }

        .btn-submit:hover {
            background-color: #0056b3;
        }

        .image-preview-container {
            margin-top: 15px;
            display: none;
            text-align: center;
        }

        .image-preview-container img {
            max-width: 100%;
            height: 250px;
            object-fit: cover;
            border: 3px solid #007bff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .image-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .input-file-container {
            border: 2px dashed #007bff;
            padding: 20px;
            border-radius: 10px;
            background-color: #ffffff;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }

        .input-file-container:hover {
            background-color: #f0f8ff;
        }

        .input-file-container input[type="file"] {
            display: none;
        }

        .input-file-container .icon {
            font-size: 50px;
            color: #007bff;
        }

        .input-file-container .text {
            font-size: 18px;
            color: #007bff;
            margin-top: 10px;
        }

        .card-title {
            font-size: 24px;
            color: #004c8c;
            font-weight: bold;
        }

        .card-body {
            padding: 30px;
        }

        .recent-reports {
            margin-top: 30px;
        }

        .report-card {
            margin-bottom: 20px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .report-status {
            font-weight: bold;
            color: #28a745;
        }

        .report-status.pending {
            color: #ffc107;
        }

        .report-status.rejected {
            color: #dc3545;
        }

        .search-bar {
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
        }
        .card-text {
            font-size: 0.95rem;
        }

    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Báo Cáo Vi Phạm</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto d-flex justify-content-center">
                    <li class="nav-item mx-4">
                        <a class="nav-link active" href="#">
                            <i class="bi bi-house-door-fill"></i> Trang Chủ
                        </a>
                    </li>
                    <li class="nav-item mx-4">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">
                            <i class="bi bi-person-circle"></i> Đăng Nhập
                        </a>
                    </li>
                    <li class="nav-item mx-4">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#registerModal">
                            <i class="bi bi-person-plus-fill"></i> Đăng Ký
                        </a>
                    </li>
                    <li class="nav-item mx-4">
                        <a class="nav-link" href="#searchViolation">
                            <i class="bi bi-search"></i> Tra Cứu Vi Phạm
                        </a>
                    </li>
                </ul>
            </div>            
        </div>
    </nav>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Modal Đăng Nhập -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Đăng Nhập</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label for="username" class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control" id="username" placeholder="Nhập tên đăng nhập">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" id="password" placeholder="Nhập mật khẩu">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Đăng Nhập</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Đăng Ký -->
    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registerModalLabel">Đăng Ký</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label for="username" class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control" id="username" placeholder="Nhập tên đăng nhập">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" placeholder="Nhập email">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" id="password" placeholder="Nhập mật khẩu">
                        </div>
                        <button type="submit" class="btn btn-success w-100">Đăng Ký</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <main class="main-content">
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title text-center mb-4">Chọn Ảnh Vi Phạm</h3>
                    <p class="text-center mb-4">Hãy chọn ảnh từ thiết bị của bạn để báo cáo vi phạm giao thông. Ảnh sẽ được hiển thị dưới đây để bạn kiểm tra lại trước khi gửi.</p>
                    <form>
                        <div class="form-group">
                            <div class="image-container">
                                <label for="violation-image" class="input-file-container">
                                    <span class="icon">&#128247;</span>
                                    <span class="text">Chọn ảnh từ thiết bị của bạn</span>
                                    <input type="file" class="form-control" id="violation-image" accept="image/*" onchange="previewImageAndSend()">
                                </label>
                            </div>
                        </div>

                        <!-- Image Preview -->
                        <div class="image-preview-container" id="image-preview">
                            <h5>Ảnh đã chọn:</h5>
                            <img id="preview-img" src="" alt="Image preview">
                        </div>
                    </form>
                </div>
            </div>
            <div class="card mt-4">
                <div class="card-body">
                    <h3 class="card-title text-center mb-4">Danh Sách Vi Phạm</h3>
                    <p class="text-center mb-4">Danh sách vi phạm mới nhất được người dùng báo cáo trên hệ thống sẽ được hiển thị dưới đây.</p>
                    <div id="violation-results" class="recent-reports">
                        <div class="row" id="violation-grid"></div> <!-- grid nằm trong đây -->
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2025 Bộ Giao Thông Vận Tải | Hệ Thống Báo Cáo Vi Phạm Giao Thông</p>
    </footer>

    <script>
        function renderViolations(data) {
            const grid = document.getElementById('violation-grid');

            if (!data.motorcyclist_detected || !data.motorcyclists.length) {
                grid.insertAdjacentHTML('afterbegin', `<div class="col-12 text-center"><p>Không phát hiện vi phạm nào.</p></div>`);
                return;
            }

            data.motorcyclists.forEach((motorcyclist, index) => {
                const statusText = motorcyclist.helmet_detected ? 'Đã đội mũ bảo hiểm' : 'Không đội mũ';
                const statusClass = motorcyclist.helmet_detected ? 'approved' : 'rejected';
                const imageSrc = motorcyclist.motorcyclist_img || 'placeholder.jpg';
                const plateImg = motorcyclist.license_plate_img;

                const reportHTML = `
                    <div class="col-md-3 mb-2">
                        <div class="card shadow-sm border rounded-3">
                            <img src="${imageSrc}" class="card-img-top" alt="Ảnh người điều khiển" style="height: 350px;">
                            <div class="card-body" style="padding: var(--bs-card-spacer-y) var(--bs-card-spacer-x);;">
                                <h5 class="card-title"><span class="badge ${statusClass === 'approved' ? 'bg-success' : 'bg-danger'}">${statusText}</span></h5>
                                <div class="d-flex">
                                    <div>
                                        <p class="card-text"><strong>Thời gian:</strong> ${new Date().toLocaleString()}</p>
                                    </div>
                                    <div>
                                    ${plateImg ? `
                                        <div>
                                            <img src="${plateImg}" alt="License Plate" class="img-thumbnail" style="max-width: 100%;">
                                        </div>` : `
                                        <div>
                                            <img alt="" class="img-thumbnail" style="max-width: 100%;">
                                        </div>`}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Append lên đầu danh sách
                grid.insertAdjacentHTML('afterbegin', reportHTML);
            });
        }
        function previewImageAndSend() {
            const fileInput = document.getElementById('violation-image');
            const file = fileInput.files[0];
            const preview = document.getElementById('preview-img');
            const previewContainer = document.getElementById('image-preview');

            if (file) {
                // Hiển thị ảnh preview
                const reader = new FileReader();
                reader.onload = function () {
                    preview.src = reader.result;
                    previewContainer.style.display = 'block';
                };
                reader.readAsDataURL(file);

                // Tạo FormData và gửi ảnh
                const formData = new FormData();
                formData.append('image', file);

                fetch('http://127.0.0.1:5000/detect-frame', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    renderViolations(data); // Tùy bạn định nghĩa hàm này
                })
                .catch(error => {
                    console.error("Lỗi:", error);
                    alert("Không thể gửi ảnh lên server.");
                });
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gyb3f3k6griG0gAXfvH+iY5pa8BwvP4XzQHfDvbs8TdxIKKpzV" crossorigin="anonymous"></script>
</body>

</html>
