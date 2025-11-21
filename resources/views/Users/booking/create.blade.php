@extends('Users.servicehome')

@section('content')
<style>
/* Import Google Fonts */
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Poppins:wght@300;400;500;600&display=swap');

/* Global Styling */
body {
  font-family: 'Poppins', sans-serif;
}

.booking-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 3rem 1rem;
}

.booking-title {
  font-family: 'Playfair Display', serif;
  font-size: 2.5rem;
  font-weight: 700;
  color: #2c3e50;
  text-align: center;
  margin-bottom: 1rem;
  position: relative;
  padding-bottom: 1rem;
}

.booking-title:after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 80px;
  height: 3px;
  background: linear-gradient(90deg, #d4af37, #f4e5c3, #d4af37);
}

.booking-subtitle {
  text-align: center;
  color: #7f8c8d;
  margin-bottom: 3rem;
  font-size: 1.1rem;
}

/* Form Styling */
.form-section {
  background: #fff;
  border-radius: 20px;
  padding: 2.5rem;
  box-shadow: 0 10px 40px rgba(0,0,0,0.08);
  margin-bottom: 2rem;
}

.form-label {
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 0.5rem;
  font-size: 0.95rem;
  letter-spacing: 0.3px;
}

.form-control, .form-select {
  border: 2px solid #e8ecef;
  border-radius: 12px;
  padding: 0.75rem 1rem;
  font-size: 0.95rem;
  transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
  border-color: #d4af37;
  box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.15);
}

/* Slot Picker */
.slot-picker {
  border: none;
  border-radius: 20px;
  overflow: hidden;
  background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
  box-shadow: 0 10px 40px rgba(0,0,0,0.08);
}

.slot-toolbar {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.5rem;
  background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
  color: #fff;
  border-bottom: none;
}

.slot-toolbar strong {
  font-family: 'Playfair Display', serif;
  font-size: 1.3rem;
  font-weight: 600;
}

.slot-legend {
  margin-left: auto;
  display: flex;
  gap: 1.5rem;
  font-size: 0.85rem;
}

.slot-legend span {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: rgba(255,255,255,0.9);
}

.slot-legend i {
  display: inline-block;
  width: 18px;
  height: 18px;
  border-radius: 6px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.i-full { background: linear-gradient(135deg, #95a5a6, #7f8c8d); }
.i-free { background: linear-gradient(135deg, #2ecc71, #27ae60); }
.i-active { background: linear-gradient(135deg, #d4af37, #c49a2a); }

/* Day Strip */
.daystrip {
  display: grid;
  grid-template-columns: 50px repeat(7, 1fr) 50px;
  gap: 0;
  background: #fff;
  padding: 1rem;
  border-bottom: 2px solid #f0f3f5;
}

.daybtn {
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
  background: transparent;
  height: 70px;
  cursor: pointer;
  font-size: 24px;
  color: #7f8c8d;
  transition: all 0.3s ease;
  border-radius: 12px;
}

.daybtn.nav:hover {
  background: #f8f9fa;
  color: #d4af37;
  transform: scale(1.1);
}

.daytab {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 1rem 0.5rem;
  cursor: pointer;
  background: #f8f9fa;
  border-radius: 15px;
  margin: 0 0.25rem;
  transition: all 0.3s ease;
  border: 2px solid transparent;
  position: relative;
  overflow: hidden;
}

.daytab:before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(212, 175, 55, 0.1), rgba(244, 229, 195, 0.1));
  opacity: 0;
  transition: opacity 0.3s ease;
  z-index: 0;
}

.daytab:hover:before {
  opacity: 1;
}

.daytab > * {
  position: relative;
  z-index: 1;
}

.daytab div {
  font-weight: 600;
  font-size: 0.9rem;
  color: #2c3e50;
  margin-bottom: 0.25rem;
}

.daytab small {
  font-size: 0.85rem;
  color: #7f8c8d;
  font-weight: 500;
}

.daytab:hover {
  transform: translateY(-3px);
  box-shadow: 0 5px 20px rgba(212, 175, 55, 0.2);
  border-color: #d4af37;
}

.daytab.active {
  background: linear-gradient(135deg, #d4af37 0%, #c49a2a 100%);
  box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4);
  border-color: #d4af37;
  transform: translateY(-5px);
}

.daytab.active div,
.daytab.active small {
  color: #fff !important;
  font-weight: 600;
}

/* Slot Grid */
.slot-grid {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: 1rem;
  padding: 1.5rem;
  background: #fff;
}

@media (max-width: 1200px) {
  .slot-grid { grid-template-columns: repeat(4, 1fr); }
}

@media (max-width: 768px) {
  .slot-grid { grid-template-columns: repeat(3, 1fr); }
  .daystrip { grid-template-columns: 40px repeat(7, 1fr) 40px; }
  .daytab { padding: 0.75rem 0.25rem; }
}

@media (max-width: 576px) {
  .slot-grid { grid-template-columns: repeat(2, 1fr); }
}

.slot {
  padding: 1.2rem 0.5rem;
  text-align: center;
  border: 2px solid #e8ecef;
  border-radius: 12px;
  cursor: pointer;
  background: #fff;
  font-size: 1.1rem;
  font-weight: 600;
  color: #2c3e50;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}

.slot:before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(46, 204, 113, 0.1), rgba(39, 174, 96, 0.1));
  opacity: 0;
  transition: opacity 0.3s ease;
}

.slot > * {
  position: relative;
  z-index: 1;
}

/* FIX: Style cho disabled (hết chỗ hoặc quá khứ) - dùng màu xám đậm như legend, chữ trắng, bỏ gạch ngang */
.slot.disabled {
  color: #fff;
  background: linear-gradient(135deg, #95a5a6, #7f8c8d);
  cursor: not-allowed;
  border-color: #7f8c8d;
  position: relative;
}

/* BỎ GẠCH NGANG CHO DISABLED (không cần cho "hết chỗ") */
/* .slot.disabled:after {
  content: '';
  position: absolute;
  top: 50%;
  left: 10%;
  right: 10%;
  height: 2px;
  background: #bdc3c7;
  transform: translateY(-50%);
} */

.slot.free {
  background: linear-gradient(135deg, #e8f8f0 0%, #d5f4e6 100%);
  border-color: #a8e6cf;
  color: #27ae60;
}

.slot.free:hover {
  transform: translateY(-5px) scale(1.05);
  box-shadow: 0 10px 25px rgba(46, 204, 113, 0.3);
  border-color: #2ecc71;
  background: linear-gradient(135deg, #d5f4e6 0%, #a8e6cf 100%);
}

.slot.free:before {
  opacity: 1;
}

.slot.active {
  background: linear-gradient(135deg, #d4af37 0%, #c49a2a 100%);
  color: #fff;
  border-color: #d4af37;
  box-shadow: 0 10px 30px rgba(212, 175, 55, 0.5);
  transform: translateY(-5px) scale(1.08);
}

.slot.active:after {
  content: '✓';
  position: absolute;
  top: 5px;
  right: 8px;
  font-size: 1.2rem;
  color: #fff;
}

/* Submit Button */
.btn-submit {
  background: linear-gradient(135deg, #d4af37 0%, #c49a2a 100%);
  border: none;
  color: #fff;
  padding: 1rem 3rem;
  border-radius: 50px;
  font-size: 1.1rem;
  font-weight: 600;
  letter-spacing: 0.5px;
  box-shadow: 0 10px 30px rgba(212, 175, 55, 0.4);
  transition: all 0.3s ease;
  font-family: 'Poppins', sans-serif;
}

.btn-submit:hover {
  transform: translateY(-3px);
  box-shadow: 0 15px 40px rgba(212, 175, 55, 0.5);
  background: linear-gradient(135deg, #c49a2a 0%, #b38a25 100%);
}

.btn-submit i {
  margin-right: 0.5rem;
}

/* Alerts */
.alert {
  border-radius: 15px;
  border: none;
  padding: 1rem 1.5rem;
  margin-bottom: 2rem;
  box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.alert-success {
  background: linear-gradient(135deg, #d5f4e6 0%, #a8e6cf 100%);
  color: #27ae60;
}

.alert-danger {
  background: linear-gradient(135deg, #fadbd8 0%, #f5b7b1 100%);
  color: #c0392b;
}

/* Loading State */
.slot-grid.loading {
  position: relative;
  min-height: 200px;
}

.slot-grid.loading:after {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 50px;
  height: 50px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #d4af37;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: translate(-50%, -50%) rotate(0deg); }
  100% { transform: translate(-50%, -50%) rotate(360deg); }
}

/* Responsive Typography */
@media (max-width: 768px) {
  .booking-title { font-size: 2rem; }
  .form-section { padding: 1.5rem; }
  .slot-toolbar { padding: 1rem; }
  .slot-toolbar strong { font-size: 1.1rem; }
  .slot { font-size: 1rem; padding: 1rem 0.5rem; }
}
</style>

@php
  $effectiveServiceId = old('service_id', $selectedServiceId ?? null);
@endphp

<div class="booking-container">
  <h2 class="booking-title">Đặt Lịch Hẹn</h2>
  <p class="booking-subtitle">Trải nghiệm dịch vụ spa đẳng cấp của chúng tôi</p>

  @if(session('success'))
    <div class="alert alert-success">
      <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    </div>
  @endif
  
  @if(session('error'))
    <div class="alert alert-danger">
      <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
    </div>
  @endif

  <form action="{{ route('users.booking.store') }}" method="POST">
    @csrf

    <div class="form-section">
      <div class="row g-4">
       {{-- Họ tên --}}
        <div class="col-md-6">
          <label class="form-label"><i class="fas fa-user me-2"></i>Họ tên *</label>
          <input
            type="text"
            name="full_name"
            class="form-control"
            value="{{ old('full_name', $customer->name ?? $authUser->name ?? '') }}"
            {{ $authUser ? 'readonly' : '' }}
            required
          >
          @error('full_name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- Điện thoại --}}
        <div class="col-md-6">
          <label class="form-label"><i class="fas fa-phone me-2"></i>Số điện thoại *</label>
          <input
            type="text"
            name="phone"
            class="form-control"
            value="{{ old('phone', $customer->phone ?? $authUser->phone ?? '') }}"
            {{ $authUser ? 'readonly' : '' }}
            required
          >
          @error('phone') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- Email --}}
        <div class="col-md-6">
          <label class="form-label"><i class="fas fa-envelope me-2"></i>Email</label>
          <input
            type="email"
            name="email"
            class="form-control"
            value="{{ old('email', $customer->email ?? $authUser->email ?? '') }}"
            {{ $authUser ? 'readonly' : '' }}
            placeholder="Nhập email (tùy chọn)"
          >
          @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label"><i class="fas fa-spa me-2"></i>Dịch vụ *</label>
          
          @if(!is_null($effectiveServiceId))
            <select class="form-select" disabled>
              @foreach($services as $service)
                <option value="{{ $service->service_id }}"
                  {{ (int)$effectiveServiceId === (int)$service->service_id ? 'selected' : '' }}>
                  {{ $service->service_name }}@if(!empty($service->duration)) — {{ $service->duration }} phút @endif
                </option>
              @endforeach
            </select>
            <input type="hidden" name="service_id" value="{{ $effectiveServiceId }}">
          @else
            <select name="service_id" class="form-select" required>
              <option value="">-- Chọn dịch vụ --</option>
              @foreach($services as $service)
                <option value="{{ $service->service_id }}"
                  {{ (int)old('service_id') === (int)$service->service_id ? 'selected' : '' }}>
                  {{ $service->service_name }}@if(!empty($service->duration)) — {{ $service->duration }} phút @endif
                </option>
              @endforeach
            </select>
          @endif
          @error('service_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="col-12">
          <label class="form-label"><i class="fas fa-comment-alt me-2"></i>Ghi chú</label>
          <textarea name="notes" class="form-control" rows="3" placeholder="Thêm ghi chú cho cuộc hẹn của bạn (tùy chọn)">{{ old('notes') }}</textarea>
        </div>
      </div>
    </div>

    <div class="form-section">
      <label class="form-label d-block mb-3"><i class="fas fa-calendar-alt me-2"></i>Chọn Ngày & Giờ *</label>

      <div class="slot-picker" id="slotPicker" data-service-id="{{ $effectiveServiceId ?? '' }}">
        <div class="slot-toolbar">
          <strong>Chọn Thời Gian</strong>
          <div class="slot-legend">
            <span><i class="i-full"></i>Hết chỗ</span>
            <span><i class="i-free"></i>Còn chỗ</span>
            <span><i class="i-active"></i>Đã chọn</span>
          </div>
        </div>

        <div class="daystrip">
          <button type="button" class="daybtn nav" data-dir="-1" aria-label="Tuần trước">‹</button>
          <div class="daytab" role="button" tabindex="0"></div>
          <div class="daytab" role="button" tabindex="0"></div>
          <div class="daytab" role="button" tabindex="0"></div>
          <div class="daytab" role="button" tabindex="0"></div>
          <div class="daytab" role="button" tabindex="0"></div>
          <div class="daytab" role="button" tabindex="0"></div>
          <div class="daytab" role="button" tabindex="0"></div>
          <button type="button" class="daybtn nav" data-dir="1" aria-label="Tuần sau">›</button>
        </div>

        <div class="slot-grid" id="slotGrid"></div>
      </div>

      <input type="hidden" name="appointment_date" id="appointment_date" value="{{ old('appointment_date') }}">
      <input type="hidden" name="start_time" id="start_time" value="{{ old('start_time') }}">

      @error('appointment_date') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
      @error('start_time') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
    </div>

    <div class="text-center">
      <button type="submit" class="btn btn-submit">
        <i class="fas fa-calendar-check"></i> Xác nhận đặt lịch
      </button>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const el = document.getElementById('slotPicker');
  if (!el) return;

  const grid = document.getElementById('slotGrid');
  const dateInput = document.getElementById('appointment_date');
  const timeInput = document.getElementById('start_time');

  // ==== Utils ====
  function pad(n){ return n<10?'0'+n:n }
  function startOfDay(d){ d = new Date(d); d.setHours(0,0,0,0); return d }
  function parseDateYmd(ymd){
    const [y,m,d] = (ymd||'').split('-').map(Number);
    if(!y || !m || !d) return startOfDay(new Date());
    return new Date(y, m-1, d);
  }
  function toYmd(d){ return d.getFullYear()+'-'+pad(d.getMonth()+1)+'-'+pad(d.getDate()) }
  function addDays(d,n){ const x=new Date(d); x.setDate(x.getDate()+n); return x }
  function addMinutes(hhmm,step){
    const [h,m]=hhmm.split(':').map(Number);
    const t=h*60+m+step;
    return pad(Math.floor(t/60))+':'+pad(t%60);
  }

  // ==== Sinh slot ====
  const stepMin = 30, startHM=[9,0], endHM=[17,0];
  function genSlots(){
    const out=[];
    let cur=pad(startHM[0])+':'+pad(startHM[1]);
    const end=pad(endHM[0])+':'+pad(endHM[1]);
    while(true){
      out.push(cur);
      if(cur===end) break;
      cur=addMinutes(cur,stepMin);
    }
    return out;
  }

  let serviceId = el.dataset.serviceId || null;

  const serviceSelect = document.querySelector('select[name="service_id"]');
  if (serviceSelect) {
    serviceId = serviceSelect.value || serviceId;
    serviceSelect.addEventListener('change', () => {
      serviceId = serviceSelect.value || null;
      el.dataset.serviceId = serviceId || '';
      timeInput.value = '';
      selectedTime = '';
      renderSlots();
    });
  }

  let weekStart = startOfDay(new Date());
  let selectedDate = dateInput.value ? parseDateYmd(dateInput.value) : startOfDay(new Date());
  let selectedTime = timeInput.value || '';

  function renderDays(){
    const tabs = el.querySelectorAll('.daytab');
    const weekdays = ['CN','Th 2','Th 3','Th 4','Th 5','Th 6','Th 7'];
    tabs.forEach((t,i)=>{
      const d = addDays(weekStart,i);
      t.innerHTML = `<div>${weekdays[d.getDay()]}</div><small>${pad(d.getDate())}/${pad(d.getMonth()+1)}</small>`;
      const dstr = toYmd(d);
      t.dataset.date = dstr;
      t.classList.toggle('active', dstr === toYmd(selectedDate));
      t.onclick = ()=>{
        selectedDate = parseDateYmd(t.dataset.date);
        dateInput.value = t.dataset.date;
        selectedTime = '';
        timeInput.value = '';
        renderSlots();
        tabs.forEach(x=>x.classList.remove('active'));
        t.classList.add('active');
      };
    });
    if(!dateInput.value) dateInput.value = toYmd(selectedDate);
  }

  async function fetchBooked(dateStr){
    if(!serviceId) return [];
    try{
      const url = `{{ url('/api/availability') }}?service_id=${encodeURIComponent(serviceId)}&date=${encodeURIComponent(dateStr)}&t=${Date.now()}`;
      const res = await fetch(url, {
        headers:{
          'X-Requested-With':'XMLHttpRequest',
          'Accept':'application/json',
          'Cache-Control':'no-cache'
        },
        cache:'no-store'
      });
      if(!res.ok) return [];
      let arr = await res.json();
      return arr.map(s => s.length > 5 ? s.slice(0,5) : s);
    }catch(e){
      console.error('Fetch error:', e);
      return [];
    }
  }

  function isPastSlot(dateStr,hhmm){
    const [hh,mm] = hhmm.split(':').map(Number);
    const dt = parseDateYmd(dateStr);
    dt.setHours(hh, mm, 0, 0);
    return dt < new Date();
  }

  async function renderSlots(){
    grid.classList.add('loading');
    grid.innerHTML = '';

    if(!serviceId){
      grid.classList.remove('loading');
      grid.innerHTML = '<div class="p-4 text-center w-100" style="grid-column: 1/-1; color: #7f8c8d;"><i class="fas fa-info-circle me-2"></i>Vui lòng chọn dịch vụ trước khi chọn giờ</div>';
      return;
    }

    const dateStr = toYmd(selectedDate);
    const booked = await fetchBooked(dateStr);

    setTimeout(() => {
      grid.innerHTML = '';
      
      genSlots().forEach(hhmm=>{
        const btn = document.createElement('button');
        btn.type='button';
        btn.className='slot';
        btn.textContent = hhmm;

        const isBooked = booked.includes(hhmm);
        const past = isPastSlot(dateStr, hhmm);

        if(isBooked || past){
          btn.classList.add('disabled');
          btn.disabled = true;
          btn.title = isBooked ? 'Khung giờ đã được đặt' : 'Khung giờ đã qua';
        } else {
          btn.classList.add('free');
          btn.title = 'Nhấn để chọn khung giờ này';
        }

        if(selectedTime===hhmm && dateInput.value===dateStr){
          btn.classList.remove('free');
          btn.classList.add('active');
          btn.setAttribute('aria-pressed','true');
        }

        btn.onclick = ()=>{
          if(btn.disabled) return;
          grid.querySelectorAll('.slot.active').forEach(x=>{
            x.classList.remove('active');
            x.classList.add('free');
            x.removeAttribute('aria-pressed');
          });
          btn.classList.add('active');
          btn.classList.remove('free');
          btn.setAttribute('aria-pressed','true');
          selectedTime = hhmm;
          timeInput.value = hhmm;
          dateInput.value = dateStr;
        };

        grid.appendChild(btn);
      });
      
      grid.classList.remove('loading');
    }, 300);
  }

  el.querySelectorAll('.daybtn.nav').forEach(b=>{
    b.onclick = ()=>{
      const dir = Number(b.dataset.dir||1);
      weekStart = addDays(weekStart, dir*7);
      renderDays();
      renderSlots();
    };
  });

  renderDays();
  renderSlots();
});
</script>
@endpush