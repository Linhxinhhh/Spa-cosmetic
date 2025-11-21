@extends('dashboard.layouts.app')

@section('title', 'Tạo kế hoạch liệu trình')

@section('content')
<h4 class="mb-3">Tạo kế hoạch liệu trình</h4>

<div class="card">
    <div class="card-body">
        <form id="plan-form">
            @csrf
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Khách hàng</label>
                    <select name="customer_id" class="form-select" required>
                        <option value="">-- Chọn khách hàng --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id ?? $c->customer_id }}">{{ $c->name ?? $c->full_name ?? $c->email }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Dịch vụ (Lẻ)</label>
                    <select name="single_service_id" class="form-select">
                        <option value="">-- Không, tôi chọn Gói --</option>
                        @foreach($singleServices as $s)
                            <option value="{{ $s->service_id }}">{{ $s->service_name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Chỉ chọn 1 trong 2: Lẻ hoặc Gói.</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Gói dịch vụ</label>
                    <select name="package_service_id" class="form-select">
                        <option value="">-- Không, tôi chọn Lẻ --</option>
                        @foreach($packageServices as $s)
                            <option value="{{ $s->service_id }}">{{ $s->service_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">Ngày bắt đầu</label>
                    <input type="date" name="start_date" value="{{ now()->toDateString() }}" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Ngày ưu tiên (T2=1...CN=0)</label>
                    <input type="text" name="preferred_dow" class="form-control" placeholder="vd: 1,3,5">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Khung giờ từ</label>
                    <input type="time" name="time_from" value="09:00" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Khung giờ đến</label>
                    <input type="time" name="time_to" value="20:00" class="form-control">
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="button" id="btn-preview" class="btn btn-outline-primary btn-sm">
                    Xem trước lịch
                </button>
                <button type="button" id="btn-save" class="btn btn-success btn-sm" disabled>
                    Lưu kế hoạch
                </button>
            </div>
        </form>
    </div>
</div>

<div class="mt-4" id="preview-wrapper" style="display:none;">
    <h5>Lịch các buổi (preview)</h5>
    <div id="preview-table"></div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('btn-preview').addEventListener('click', function () {
    const form = document.getElementById('plan-form');
    const fd = new FormData(form);

    // chuyển preferred_dow thành array
    const dowStr = fd.get('preferred_dow') || '';
    const dows = dowStr
        .split(',')
        .map(s => s.trim())
        .filter(Boolean)
        .map(Number);

    const body = {
        _token: fd.get('_token'),
        customer_id: fd.get('customer_id'),
        single_service_id: fd.get('single_service_id') || null,
        package_service_id: fd.get('package_service_id') || null,
        start_date: fd.get('start_date'),
        preferred_dow: dows,
        preferred_time_range: [fd.get('time_from'), fd.get('time_to')],
    };

    fetch("{{ route('admin.treatment-plans.preview') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': fd.get('_token'),
        },
        body: JSON.stringify(body)
    })
    .then(res => res.json())
    .then(data => {
        // data.sessions là mảng buổi
        const wrap = document.getElementById('preview-wrapper');
        const tbl = document.getElementById('preview-table');
        wrap.style.display = 'block';

        let html = `<table class="table table-sm table-bordered">
            <thead><tr>
                <th>#</th><th>Ngày giờ</th><th>Nhân viên</th><th>Phòng</th><th>Trạng thái</th>
            </tr></thead><tbody>`;
        data.sessions.forEach((s, idx) => {
            html += `<tr>
                <td>${idx+1}</td>
                <td>${s.scheduled_start} → ${s.scheduled_end}</td>
                <td>${s.staff_id ?? '-'}</td>
                <td>${s.room_id ?? '-'}</td>
                <td><span class="badge bg-secondary">draft</span></td>
            </tr>`;
        });
        html += `</tbody></table>`;
        tbl.innerHTML = html;

        // lưu tạm json để submit
        window.__previewSessions = data.sessions;
        window.__previewPlan = data.plan;

        document.getElementById('btn-save').disabled = false;
    })
    .catch(err => {
        alert('Không xem được preview');
        console.error(err);
    });
});

document.getElementById('btn-save').addEventListener('click', function () {
    if (!window.__previewSessions) {
        alert('Bạn cần xem trước trước khi lưu.');
        return;
    }
    const form = document.getElementById('plan-form');
    const fd = new FormData(form);

    const payload = {
        _token: fd.get('_token'),
        customer_id: fd.get('customer_id'),
        single_service_id: fd.get('single_service_id') || null,
        package_service_id: fd.get('package_service_id') || null,
        start_date: fd.get('start_date'),
        preferred_dow: (fd.get('preferred_dow')||'').split(',').map(s=>s.trim()).filter(Boolean).map(Number),
        preferred_time_range: [fd.get('time_from'), fd.get('time_to')],
        sessions: window.__previewSessions
    };

    fetch("{{ route('admin.treatment-plans.store') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': fd.get('_token'),
        },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.redirect) {
            window.location = data.redirect;
        } else {
            alert('Đã lưu kế hoạch');
        }
    })
    .catch(err => {
        alert('Không lưu được');
        console.error(err);
    });
});
</script>
@endpush
