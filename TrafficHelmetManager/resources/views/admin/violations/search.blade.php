@extends('Admin.layouts.app')
@section('title', 'Tra Cứu Vi Phạm')
@section('content')

    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="mb-0">Tra Cứu Vi Phạm</h1>
                <ol class="breadcrumb float-sm-right mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang Chủ</a></li>
                    <li class="breadcrumb-item active">Tra Cứu Vi Phạm</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <form action="{{ route('admin.violations.search') }}" method="GET" class="row g-3">
                                <div class="col-md-4">
                                    <input type="text" name="query" class="form-control" placeholder="Nhập ID hoặc biển số"
                                        value="{{ request('query') }}">
                                </div>
                                <div class="col-md-3">
                                    <select name="status" class="form-select form-control">
                                        <option value="">-- Trạng thái --</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xác
                                            minh</option>
                                        <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Đã
                                            xác minh</option>
                                        <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Đã xử
                                            lý</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="date" name="violation_date" class="form-control" placeholder="Ngày vi phạm"
                                        value="{{ request('violation_date') }}">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">Tìm kiếm</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Toàn bộ danh sách -->
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th class="text-center">Người tố cáo</th>
                                        <th class="text-center">Biển số</th>
                                        <th class="text-center">Ảnh xe vi phạm</th>
                                        <th class="text-center">Ảnh biển số</th>
                                        <th class="text-center">Ngày phát hiện</th>
                                        <th class="text-center">Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($hasFilter)
                                        @if($violations->count())
                                            @forelse ($violations as $violation)
                                                <tr id="violation-row-{{ $violation->id }}">
                                                    <td class="text-center">{{ $violation->id }}</td>
                                                    <td class="text-center">{{ $violation->user->name ?? 'Chưa xác định' }}</td>
                                                    <td class="text-center violation-plate">
                                                        {{ $violation->plate_number ?? 'Chưa xác định' }}</td>
                                                    <td class="text-center">
                                                        @if($violation->image_url)
                                                            <img src="{{ $violation->image_url }}" alt="Xe vi phạm" width="100">
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if($violation->plate_image)
                                                            <img src="{{ $violation->plate_image }}" alt="Biển số" width="100">
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $violation->violation_time ? $violation->violation_time->format('d/m/Y H:i') : 'N/A' }}
                                                    </td>
                                                    <td class="text-center violation-status">
                                                        @php
                                                            $statusClass = [
                                                                'pending' => 'bg-warning',
                                                                'verified' => 'bg-info',
                                                                'resolved' => 'bg-success',
                                                            ][$violation->status] ?? 'bg-secondary';

                                                            $statusText = [
                                                                'pending' => 'Chờ xác minh',
                                                                'verified' => 'Đã xác minh',
                                                                'resolved' => 'Đã xử lý',
                                                            ][$violation->status] ?? 'Không xác định';
                                                        @endphp

                                                        <span class="badge {{ $statusClass }}">
                                                            {{ $statusText }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center">Chưa có vi phạm nào để hiển thị!</td>
                                                </tr>
                                            @endforelse
                                        @else
                                            
                                        @endif
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        @if($hasFilter)
                            @if($violations->count())
                                <div class="card-footer clearfix">
                                    {{ $violations->links('pagination::bootstrap-4') }}
                                </div>
                            @endif
                        @endif
                        
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection