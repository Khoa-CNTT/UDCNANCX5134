@extends('Admin.layouts.app')
@section('title', 'Xử Lý Vi Phạm')
@section('content')

<section class="content-header">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="mb-0">Xử Lý Vi Phạm</h1>
            <ol class="breadcrumb float-sm-right mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang Chủ</a></li>
                <li class="breadcrumb-item active">Xử Lý Vi Phạm</li>
            </ol>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
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
                                    <th class="text-center">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($violations as $violation)
                                <tr id="violation-row-{{ $violation->id }}">
                                    <td class="text-center">{{ $violation->id }}</td>
                                    <td class="text-center">{{ $violation->user->name ?? 'Chưa xác định' }}</td>
                                    <td class="text-center violation-plate">{{ $violation->plate_number ?? 'Chưa xác định' }}</td>
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
                                    <td class="text-center">{{ $violation->violation_time ? $violation->violation_time->format('d/m/Y H:i') : 'N/A' }}</td>
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
                                    <td class="text-center">
                                        <a href="#" class="btn btn-sm btn-primary btn-handle"
                                            data-id="{{ $violation->id }}"
                                            data-plate="{{ $violation->plate_number }}"
                                            data-status="{{ $violation->status }}"
                                            data-toggle="modal" data-target="#handleModal">
                                            Xử Lý
                                        </a>
                                        <button class="btn btn-sm btn-danger delete-btn" data-toggle="modal" data-target="#deleteModal" data-id="{{ $violation->id }}">
                                            Xóa
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Không có vi phạm nào.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer clearfix">
                        {{ $violations->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Modal xác nhận xóa -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="deleteForm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa vi phạm này?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <button type="button" data-id="" class="btn btn-danger confirm-delete" data-dismiss="modal">Xóa</button>
            </div>
        </div>
    </form>
  </div>
</div>
<div class="modal fade" id="handleModal" tabindex="-1" role="dialog" aria-labelledby="handleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="handleForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Xử lý vi phạm</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="violationId">
          <div class="form-group">
            <label>Biển số</label>
            <input type="text" id="plateInput" class="form-control" placeholder="VD: 29A-123.45">
          </div>
          <div class="form-group">
            <label>Trạng thái</label>
            <select id="statusInput" class="form-control">
              <option value="pending">Chờ xác minh</option>
              <option value="verified">Đã xác minh</option>
              <option value="resolved">Đã xử lý</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
          <button type="submit" class="btn btn-primary">Cập nhật</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).on('click', '.delete-btn', function () {
        var violationId = $(this).data("id");
        $(".confirm-delete").data("id", violationId);
    });

    $(document).on('click', '.confirm-delete', function () {
        var violationId = $(this).data("id");
        var url = "{{ route('admin.violations.delete', ':id') }}".replace(':id', violationId);
        $.ajax({
            url: url,
            success: function (response) {
                $('#violation-row-' + violationId).fadeOut(1000, function () {
                    $(this).remove();
                });
            },
            error: function (xhr) {
                console.log(xhr.responseText);
                alert('Có lỗi xảy ra khi xóa!');
            }
        });
    });

    $(document).on('click', '.btn-handle', function () {
        const id = $(this).data('id');
        const plate_number = $(this).data('plate');
        const status = $(this).data('status');

        $('#violationId').val(id);
        $('#plateInput').val(plate_number);
        $('#statusInput').val(status);

    });

    $('#handleForm').submit(function (e) {
        e.preventDefault();

        const id = $('#violationId').val();
        const plate_number = $('#plateInput').val();
        const status = $('#statusInput').val();
        var url = "{{ route('admin.violations.update', ':id') }}".replace(':id', id);
        $.ajax({
            url: url,
            method: 'PUT',
            data: {
                plate_number: plate_number,
                status: status,
                _token: '{{ csrf_token() }}'
            },
            success: function (res) {
                var row = $('#violation-row-' + id);
                row.find('.violation-plate').text(plate_number);

                if(status == 'pending') {
                    row.find('.violation-status').html('<span class="badge bg-warning">Chờ xác minh</span>');
                } else if (status == 'verified') {
                    row.find('.violation-status').html('<span class="badge bg-info">Đã xác minh</span>');
                } else if (status == 'resolved') {
                    row.find('.violation-status').html('<span class="badge bg-success">Đã xử lý</span>');

                }
            },
            error: function (xhr) {
                alert('Có lỗi xảy ra!');
                console.log(xhr.responseText);
            }
        });
    });
</script>
@endsection