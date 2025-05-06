@extends('Admin.layouts.app')
@section('title', 'Đổi Thông Tin')
@section('content')

    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="mb-0">Đổi Thông Tin</h1>
                <ol class="breadcrumb float-sm-right mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang Chủ</a></li>
                    <li class="breadcrumb-item active">Đổi Thông Tin</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <form action="{{ route('admin.profile.update') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="username" class="form-label">Tên đăng nhập</label>
                                    <input type="text" name="username" class="form-control" id="username"
                                        value="{{ old('username', auth()->user()->username) }}" required>
                                    @error('username')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Địa chỉ Email</label>
                                    <input type="email" name="email" class="form-control" id="email"
                                        value="{{ old('email', auth()->user()->email) }}" required>
                                    @error('email')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <hr class="my-4">

                                <h5>Đổi mật khẩu</h5>

                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                                    <input type="password" name="current_password" class="form-control"
                                        id="current_password" placeholder="Nhập mật khẩu hiện tại">
                                    @error('current_password')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="new_password" class="form-label">Mật khẩu mới</label>
                                    <input type="password" name="new_password" class="form-control" id="new_password" placeholder="Nhập mật khẩu mới">
                                    @error('new_password')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="new_password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                                    <input type="password" name="new_password_confirmation" class="form-control"
                                        id="new_password_confirmation" placeholder="Nhập lại mật khẩu mới">
                                </div>

                                <button type="submit" class="btn btn-primary">Lưu Thông Tin</button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection