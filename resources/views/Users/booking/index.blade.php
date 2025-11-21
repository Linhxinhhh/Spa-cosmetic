@extends('Users.servicehome')

@section('content')
<div class="container py-4">

    <h3 class="mb-3">Lịch hẹn của tôi</h3>

    @if($appointments->isEmpty())
        <p class="text-muted">Bạn chưa đặt lịch dịch vụ nào.</p>
    @else

        @foreach($appointments as $a)

            <!-- Nút mở popup -->
          
              <button class="btn btn-outline-primary mb-2"
                    data-bs-toggle="modal"
                    data-bs-target="#rescheduleModal{{ $a->appointment_id }}">
                <i class="fas fa-calendar-alt"></i> Dời lịch
            </button>
            <div class="card shadow-sm mb-3 border-0" style="border-left: 5px solid #ff8a00;">
                <div class="card-body d-flex justify-content-between">
                    
                    <div>
                        <h5 class="fw-bold">{{ $a->service->service_name }}</h5>

                        <div class="text-muted">
                            Ngày: {{ date('d/m/Y', strtotime($a->appointment_date)) }} <br>
                            Giờ: {{ $a->start_time }} – {{ $a->end_time }}
                        </div>

                        <div class="mt-2">
                            <span class="badge 
                                @if($a->status=='pending') bg-warning
                                @elseif($a->status=='confirmed') bg-primary
                                @elseif($a->status=='completed') bg-success
                                @else bg-danger
                                @endif
                            ">
                                @if($a->status=='pending') Chờ xác nhận
                                @elseif($a->status=='confirmed') Đã xác nhận
                                @elseif($a->status=='completed') Hoàn thành
                                @else Đã hủy
                                @endif
                            </span>
                        </div>
                    </div>

                    <div>
                        <span class="badge rounded-pill text-white" style="background:#ff8a00;">
                            Trải nghiệm
                        </span>
                    </div>
                    
                </div>
                
            </div>

            <!-- Modal Reschedule -->
            <div class="modal fade" id="rescheduleModal{{ $a->appointment_id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <div class="modal-header bg-warning text-white">
                            <h5 class="modal-title">Dời lịch hẹn</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <form action="{{ route('users.booking.reschedule', $a->appointment_id) }}" method="POST">
                            @csrf

                            <div class="modal-body">

                                <div class="mb-3">
                                    <label class="form-label">Ngày mới:</label>
                                    <input type="date" name="appointment_date"
                                           class="form-control"
                                           value="{{ $a->appointment_date }}" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Giờ bắt đầu:</label>
                                    <input type="time" name="start_time"
                                           class="form-control"
                                           value="{{ $a->start_time }}" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Giờ kết thúc:</label>
                                    <input type="time" name="end_time"
                                           class="form-control"
                                           value="{{ $a->end_time }}" required>
                                </div>

                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    Hủy
                                </button>
                                <button type="submit" class="btn btn-warning text-white">
                                    Xác nhận dời lịch
                                </button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>

        @endforeach

        <div class="mt-3">
            {{ $appointments->links() }}
        </div>

    @endif

</div>
@endsection
