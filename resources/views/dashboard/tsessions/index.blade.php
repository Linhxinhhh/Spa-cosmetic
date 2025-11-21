@extends('dashboard.layouts.app')

@section('title','Theo dõi các buổi')

@section('content')
<h4 class="mb-3">Các buổi điều trị</h4>

<form method="GET" class="row g-2 mb-3">
    <div class="col-auto">
        <input type="date" name="date" value="{{ request('date') }}" class="form-control">
    </div>
    <div class="col-auto">
        <select name="status" class="form-select">
            <option value="">-- Trạng thái --</option>
            @foreach(['scheduled','confirmed','completed','canceled','missed'] as $st)
                <option value="{{ $st }}" {{ request('status')==$st?'selected':'' }}>{{ $st }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-auto">
        <button class="btn btn-primary btn-sm">Lọc</button>
    </div>
</form>

<table class="table table-sm table-bordered align-middle">
    <thead>
        <tr>
            <th>#</th>
            <th>KH</th>
            <th>Dịch vụ</th>
            <th>Buổi</th>
            <th>Thời gian</th>
            <th>Trạng thái</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    @foreach($sessions as $s)
        <tr>
            <td>{{ $s->id }}</td>
            <td>{{ $s->plan->customer->name ?? '-' }}</td>
            <td>{{ $s->plan->packageService->service_name ?? $s->plan->singleService->service_name ?? '-' }}</td>
            <td>{{ $s->session_no }} @if($s->packageStep) — {{ $s->packageStep->title }} @endif</td>
            <td>{{ $s->scheduled_start?->format('d/m/Y H:i') }} → {{ $s->scheduled_end?->format('H:i') }}</td>
            <td><span class="badge bg-secondary">{{ $s->status }}</span></td>
            <td>
                <a href="{{ route('admin.tsessions.edit',$s) }}" class="btn btn-outline-secondary btn-sm">Sửa</a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

{{ $sessions->withQueryString()->links() }}
@endsection
