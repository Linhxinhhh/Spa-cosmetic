@extends('Users.servicehome')

@section('title', 'Lịch hẹn của tôi')

@section('content')
<h3 class="mb-3">Lịch hẹn của tôi</h3>

@if($sessions->isEmpty())
    <p class="text-muted">Bạn chưa có lịch nào.</p>
@else
    <ul class="list-group">
        @foreach($sessions as $s)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <strong>{{ $s->scheduled_start?->format('d/m/Y H:i') }}</strong>
                    <div class="small text-muted">
                        Buổi {{ $s->session_no }} - Kế hoạch #{{ $s->treatment_plan_id }}
                    </div>
                </div>
                <span class="badge bg-{{ $s->status === 'completed' ? 'success' : ($s->status === 'canceled' ? 'danger' : 'secondary') }}">
                    {{ ucfirst($s->status) }}
                </span>
            </li>
        @endforeach
    </ul>
@endif
@endsection
