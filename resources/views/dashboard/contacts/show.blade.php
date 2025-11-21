@extends('dashboard.layouts.app')
@section('page-title','Liên hệ #'.$contact->contact_id)

@push('styles')
<style>
  /* Variables & animations */
  @keyframes fadeInUp { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
  @keyframes pulse { 0%, 100% { opacity:1; } 50% { opacity:.7; } }
  
  /* Header moderne avec dégradé subtil */
  .page-header {
    background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
    border-radius: 20px; 
    padding: 2rem; 
    margin-bottom: 1.5rem;
    color: #fff; 
    position: relative; 
    overflow: hidden; 
    box-shadow: 0 20px 40px rgba(102,126,234,.25);
    animation: fadeInUp .5s ease;
  }
  .page-header::before {
    content: "";
    position: absolute;
    top: -50%;
    right: -10%;
    width: 300px;
    height: 300px;
    background: rgba(255,255,255,.08);
    border-radius: 50%;
    animation: pulse 3s ease-in-out infinite;
  }
  .page-header h1 { 
    font-weight: 800; 
    margin: 0; 
    font-size: 1.75rem;
    text-shadow: 0 2px 10px rgba(0,0,0,.1);
  }
  .page-sub { opacity: .9; font-size: .9rem; }

  /* Badges de statut plus modernes */
  .status-badge { 
    padding: 8px 16px; 
    border-radius: 20px; 
    font-weight: 600; 
    font-size: .85rem; 
    letter-spacing: .5px;
    box-shadow: 0 4px 12px rgba(0,0,0,.15);
    transition: all .3s ease;
  }
  .status-badge:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,.2); }
  .status-open { background: linear-gradient(135deg,#f59e0b 0%,#d97706 100%); color:#fff;  }
  .status-processing { background: linear-gradient(135deg,#06b6d4 0%,#0ea5e9 100%); color:#fff; }
  .status-done { background: linear-gradient(135deg,#10b981 0%,#059669 100%); color:#fff; }

  /* Cards avec effet glassmorphism */
  .card { 
    border: none; 
    background: rgba(255,255,255,.95);
    backdrop-filter: blur(10px);
    box-shadow: 0 8px 32px rgba(0,0,0,.08); 
    border-radius: 16px;
    transition: all .3s ease;
    animation: fadeInUp .6s ease;
  }
  .card:hover { 
    transform: translateY(-4px); 
    box-shadow: 0 12px 48px rgba(0,0,0,.12); 
  }
  .card-header { 
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 2px solid #dee2e6; 
    font-weight: 700;
    border-radius: 16px 16px 0 0 !important;
    padding: 1rem 1.25rem;
    color: #495057;
  }

  /* Formulaires élégants */
  .form-select, .form-control { 
    border-radius: 12px; 
    border: 2px solid #e9ecef;
    padding: .65rem 1rem;
    transition: all .3s ease;
  }
  .form-control:focus, .form-select:focus { 
    border-color: #667eea; 
    box-shadow: 0 0 0 4px rgba(102,126,234,.15);
    transform: translateY(-1px);
  }

  /* Boutons avec effets */
  .btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none; 
    border-radius: 12px; 
    font-weight: 600;
    padding: .65rem 1.5rem;
    box-shadow: 0 4px 16px rgba(102,126,234,.35);
    transition: all .3s ease;
  }
  .btn-primary:hover { 
    transform: translateY(-2px); 
    box-shadow: 0 8px 24px rgba(102,126,234,.45); 
  }
  .btn-primary:active { transform: translateY(0); }

  .btn-back { 
    border-radius: 12px; 
    border: 2px solid #e9ecef; 
    
    font-weight: 600;
    transition: all .3s ease;
  }
  .btn-back:hover { 
    border-color: #667eea; 
    
    transform: translateX(-4px);
    box-shadow: 0 4px 12px rgba(102,126,234,.15); 
  }

  /* Timeline redessinée */
  .timeline { 
    position: relative; 
    margin: 0; 
    padding-left: 2rem; 
  }
  .timeline::before {
    content: ""; 
    position: absolute; 
    left: .6rem; 
    top: .5rem; 
    bottom: .5rem; 
    width: 3px; 
    background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
    border-radius: 3px;
  }
  .tl-item { 
    position: relative; 
    padding: .5rem 0 1rem 0;
    animation: fadeInUp .7s ease;
  }
  .tl-item::before {
    content: "";
    position: absolute;
    left: -1.5rem;
    top: .6rem;
    width: 12px;
    height: 12px;
    background: #667eea;
    border: 3px solid #fff;
    border-radius: 50%;
    box-shadow: 0 0 0 3px rgba(102,126,234,.2);
  }
  .tl-meta { 
    font-size: .82rem; 
    color: #6c757d; 
    margin-bottom: .4rem;
    font-weight: 500;
  }
  .tl-bubble {
    background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);
    border: 2px solid #e9ecef; 
    border-radius: 14px; 
    padding: .85rem 1rem; 
    white-space: pre-wrap;
    box-shadow: 0 2px 8px rgba(0,0,0,.04);
    transition: all .3s ease;
  }
  .tl-bubble:hover {
    border-color: #667eea;
    box-shadow: 0 4px 16px rgba(102,126,234,.12);
  }
  .tl-tag { 
    display: inline-flex; 
    align-items: center; 
    gap: .4rem; 
    font-size: .75rem; 
    color: #6c757d;
    background: #f8f9fa;
    padding: 2px 8px;
    border-radius: 6px;
  }

  /* Message contact avec style amélioré */
  pre.contact-message {
    background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);
    border: 2px solid #e9ecef; 
    border-radius: 14px;
    padding: 1rem; 
    white-space: pre-wrap; 
    margin: 0;
    line-height: 1.6;
    box-shadow: 0 2px 8px rgba(0,0,0,.04);
  }

  /* Alert success amélioré */
  .alert-success {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    border-radius: 12px;
    border: none;
    animation: fadeInUp .5s ease;
  }

  .alert-warning {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    border-radius: 12px;
    border: none;
  }

  /* Info sections */
  .info-label {
    color: #6c757d;
    font-weight: 600;
    font-size: .88rem;
    margin-bottom: .25rem;
  }
  .info-value {
    color: #212529;
    font-weight: 500;
  }

  /* Responsive amélioré */
  @media (max-width: 768px) {
    .page-header { padding: 1.5rem; border-radius: 16px; }
    .page-header h1 { font-size: 1.4rem; }
    .card { margin-bottom: 1rem; }
  }

  /* Smooth scrolling */
  html { scroll-behavior: smooth; }
</style>
@endpush

@section('content')
<div class="container-xl py-4">

  {{-- HEADER --}}
  <div class="page-header">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div style="position: relative; z-index: 1;">
        <h1 class="mb-2">
          <i class="fas fa-envelope-open-text me-2"></i> Liên hệ #{{ $contact->contact_id }}
        </h1>
        <div class="page-sub">
          <i class="fas fa-clock me-1"></i> Tạo lúc: {{ $contact->created_at?->format('d/m/Y H:i') }}
          @if($contact->responded_at)
            <span class="mx-2">·</span>
            <i class="fas fa-reply me-1"></i> Phản hồi: {{ $contact->responded_at->format('d/m/Y H:i') }}
          @endif
        </div>
      </div>
      @php
        $cls = match($contact->status){
          'open' => 'status-open',
          'processing' => 'status-processing',
          'done' => 'status-done',
          default => 'status-open'
        };
        $label = $contact->status==='open' ? 'Mới' : ($contact->status==='processing' ? 'Đang xử lý' : 'Hoàn tất');
      @endphp
      <span class="status-badge {{ $cls }}" style="position: relative; z-index: 1;">
        <i class="fas fa-circle me-1" style="font-size: .5rem;"></i>{{ $label }}
      </span>
    </div>
  </div>

  <div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('admin.contacts.index') }}" class="btn btn-light btn-back">
      <i class="fas fa-arrow-left me-2"></i> Quay lại danh sách
    </a>
    @if(session('success'))
      <div class="alert alert-success border-0 shadow-sm py-2 px-4 mb-0">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
      </div>
    @endif
  </div>

  <div class="row g-4">
    {{-- LEFT --}}
    <div class="col-lg-7">
      <div class="card mb-4">
        <div class="card-header">
          <i class="fas fa-info-circle me-2"></i> Thông tin liên hệ
        </div>
        <div class="card-body p-4">
          <div class="row gy-3">
            <div class="col-sm-6">
              <div class="info-label"><i class="fas fa-user me-1"></i> Khách hàng</div>
              <div class="info-value">{{ $contact->name }}</div>
            </div>
            <div class="col-sm-6">
              <div class="info-label"><i class="fas fa-phone me-1"></i> Liên lạc</div>
              <div class="info-value">
                {{ $contact->phone }}
                @if($contact->email) 
                  <br><a href="mailto:{{ $contact->email }}" class="text-decoration-none">{{ $contact->email }}</a>
                @endif
              </div>
            </div>
            @if($contact->subject)
            <div class="col-12">
              <div class="info-label"><i class="fas fa-tag me-1"></i> Chủ đề</div>
              <div class="info-value">{{ $contact->subject }}</div>
            </div>
            @endif
            <div class="col-12">
              <div class="info-label mb-2"><i class="fas fa-comment-dots me-1"></i> Nội dung</div>
              <pre class="contact-message">{{ $contact->message }}</pre>
            </div>
          </div>

          <div class="mt-4 pt-3 border-top">
            <form action="{{ route('admin.contacts.status',$contact) }}" method="post" class="d-flex align-items-center gap-3">
              @csrf @method('PATCH')
              <span class="info-label mb-0"><i class="fas fa-tasks me-1"></i> Trạng thái:</span>
              <select class="form-select form-select-sm w-auto" name="status" onchange="this.form.submit()" style="min-width: 150px;">
                <option value="open"       @selected($contact->status==='open')>🔴 Mới</option>
                <option value="processing" @selected($contact->status==='processing')>🔵 Đang xử lý</option>
                <option value="done"       @selected($contact->status==='done')>🟢 Hoàn tất</option>
              </select>
            </form>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <i class="fas fa-comments me-2"></i> Lịch sử phản hồi
        </div>
        <div class="card-body p-4">
          @if($contact->replies->isNotEmpty())
            <div class="timeline">
              @foreach($contact->replies as $r)
                <div class="tl-item">
                  <div class="tl-meta">
                    <i class="fas fa-calendar-alt me-1"></i> {{ $r->created_at?->format('d/m/Y H:i') }}
                    <span class="tl-tag">
                      <i class="fas fa-{{ $r->admin_id ? 'user-shield' : 'user' }}"></i> 
                      {{ $r->admin_id ? 'Admin' : 'User' }}
                    </span>
                    <span class="tl-tag">
                      <i class="fas fa-{{ $r->via === 'email' ? 'envelope' : 'sticky-note' }}"></i> 
                      {{ strtoupper($r->via) }}
                    </span>
                  </div>
                  <div class="tl-bubble">{!! nl2br(e($r->message)) !!}</div>
                </div>
              @endforeach
            </div>
          @else
            <div class="text-center text-muted py-4">
              <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
              <p class="mb-0">Chưa có phản hồi nào</p>
            </div>
          @endif
        </div>
      </div>
    </div>

    {{-- RIGHT --}}
    <div class="col-lg-5">
      <div class="card" style="position: sticky; top: 20px;">
        <div class="card-header">
          <i class="fas fa-reply me-2"></i> Trả lời người dùng
        </div>
        <div class="card-body p-4">
          {{-- Cảnh báo nếu thiếu email --}}
          @if(!$contact->email)
            <div class="alert alert-warning">
              <i class="fas fa-exclamation-triangle me-2"></i>
              Liên hệ này <strong>không có địa chỉ email</strong>. Bạn chỉ có thể thêm <em>Ghi chú nội bộ</em>.
            </div>
          @else
            <div class="alert alert-info border-0 mb-3" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);">
              <i class="fas fa-at me-2"></i> Người nhận: 
              <a href="mailto:{{ $contact->email }}" class="text-decoration-none fw-bold">{{ $contact->email }}</a>
            </div>
          @endif

          {{-- Lỗi validate --}}
          @if ($errors->any())
            <div class="alert alert-danger py-2">
              @foreach ($errors->all() as $e)
                <div class="small"><i class="fas fa-times-circle me-1"></i>{{ $e }}</div>
              @endforeach
            </div>
          @endif

          <form action="{{ route('admin.contacts.reply',$contact) }}" method="post" id="replyForm">
            @csrf

            <div class="mb-3">
              <label class="form-label fw-bold">
                <i class="fas fa-paper-plane me-3"></i> Hình thức gửi
              </label>
              <select  style="min-width: 180px;" name="via" class="form-select" {{ !$contact->email ? 'disabled' : '' }}>
                <option value="note"  {{ old('via','note')==='note' ? 'selected' : '' }}>📝 Ghi chú nội bộ</option>
                <option value="email" {{ old('via')==='email' ? 'selected' : '' }} {{ !$contact->email ? 'disabled' : '' }}>
                  ✉️ Gửi email
                </option>
              </select>
              @if(!$contact->email)
                <input type="hidden" name="via" value="note">
              @endif
            </div>

            @if($contact->email)
              <div class="mb-3">
                <label class="form-label fw-bold">
                  <i class="fas fa-heading me-1"></i> Tiêu đề email
                </label>
                <input type="text" name="email_subject" class="form-control"
                       value="{{ old('email_subject', $contact->subject ? ('Phản hồi: '.$contact->subject) : 'Phản hồi liên hệ #'.$contact->contact_id) }}"
                       placeholder="Nhập tiêu đề email...">
              </div>
            @endif

            <div class="mb-3">
              <label class="form-label fw-bold">
                <i class="fas fa-pen me-1"></i> Nội dung phản hồi
              </label>
              <textarea name="message" rows="6"
                        class="form-control @error('message') is-invalid @enderror"
                        placeholder="Nhập nội dung phản hồi của bạn..." required>{{ old('message') }}</textarea>
              @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            {{-- Tùy chọn đưa Q&A vào mục FAQ công khai --}}
            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox"
                    name="publish_to_faq" value="1" id="publish_to_faq"
                    {{ old('publish_to_faq') ? 'checked' : '' }}>
              <label class="form-check-label" for="publish_to_faq">
                Đưa câu hỏi & trả lời vào FAQ (hiển thị công khai)
              </label>
            </div>

            <button class="btn btn-primary w-100" id="replyBtn">
              <i class="fas fa-paper-plane me-2"></i> Gửi phản hồi
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Chống bấm đúp submit
  document.addEventListener('DOMContentLoaded', function(){
    const f = document.getElementById('replyForm');
    const btn = document.getElementById('replyBtn');
    f?.addEventListener('submit', function(){
      btn.disabled = true;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Đang gửi...';
    });
  });

  // Nạp FA nếu layout chưa có
  if (!document.querySelector('link[href*="font-awesome"]')) {
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css';
    document.head.appendChild(link);
  }
</script>
@endpush