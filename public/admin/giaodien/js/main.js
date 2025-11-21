if (!document.querySelector('link[href*="font-awesome"]')) {
    const link=document.createElement('link');link.rel='stylesheet';
    link.href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css';
    document.head.appendChild(link);
}

function updateDurationPreview() {
  const input = document.querySelector('#duration');
  if (!input) return;
  const val = parseInt(input.value || '0', 10);
  const d = document.querySelector('#durationDisplay');
  const h = document.querySelector('#hourDisplay');
  if (!d || !h) return;
  d.textContent = val + ' phút';
  const hours = Math.floor(val / 60);
  const minutes = val % 60;
  h.textContent = hours > 0 ? `${hours} giờ ${minutes} phút` : `${minutes} phút`;
}
document.addEventListener('input', e => {
  if (e.target && e.target.id === 'duration') updateDurationPreview();
});
document.addEventListener('DOMContentLoaded', updateDurationPreview);

// Image preview + drag/drop (for .file-upload-area with data-target)
function bindDropzone(zone) {
  const targetSel = zone.getAttribute('data-target');
  const input = document.querySelector(targetSel);
  const uploadUI = zone.querySelector('.upload-ui');
  const previewUI = zone.querySelector('.preview-ui');
  const img = zone.querySelector('.preview-img');

  const showPreview = file => {
    const reader = new FileReader();
    reader.onload = e => {
      if (img) img.src = e.target.result;
      if (uploadUI) uploadUI.classList.add('d-none');
      if (previewUI) previewUI.classList.remove('d-none');
    };
    reader.readAsDataURL(file);
  };

  zone.addEventListener('click', () => input && input.click());
  zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('dragover'); });
  zone.addEventListener('dragleave', e => { e.preventDefault(); zone.classList.remove('dragover'); });
  zone.addEventListener('drop', e => {
    e.preventDefault(); zone.classList.remove('dragover');
    if (e.dataTransfer.files?.length) {
      input.files = e.dataTransfer.files;
      showPreview(input.files[0]);
    }
  });
  if (input) {
    input.addEventListener('change', () => {
      if (input.files?.length) showPreview(input.files[0]);
    });
  }
}
document.querySelectorAll('[data-dropzone]').forEach(bindDropzone);

// Simple required check (frontend). Backend vẫn validate.
document.getElementById('serviceEditForm')?.addEventListener('submit', e => {
  const required = ['service_name','category_id','duration','status'];
  let ok = true;
  required.forEach(name => {
    const el = document.querySelector(`[name="${name}"]`);
    if (el && !String(el.value || '').trim()) {
      el.classList.add('is-invalid'); ok = false;
    } else if (el) { el.classList.remove('is-invalid'); }
  });
  if (!ok) {
    e.preventDefault();
    alert('Vui lòng điền đầy đủ thông tin bắt buộc!');
  }
});
// Duration preview
function updateDurationPreview() {
  const input = document.querySelector('#duration');
  const box = document.querySelector('#durationPreview');
  if (!input || !box) return;
  const val = parseInt(input.value || '0', 10);
  if (val > 0) {
    const d = document.querySelector('#durationDisplay');
    const h = document.querySelector('#hourDisplay');
    d.textContent = `${val} phút`;
    const hours = Math.floor(val / 60);
    const minutes = val % 60;
    h.textContent = hours > 0 ? `${hours} giờ ${minutes} phút` : `${minutes} phút`;
    box.style.display = 'block';
  } else {
    box.style.display = 'none';
  }
}
document.addEventListener('input', e => {
  if (e.target && e.target.id === 'duration') updateDurationPreview();
});
document.addEventListener('DOMContentLoaded', updateDurationPreview);

// Dropzone binding
function bindDropzone(zone) {
  const targetSel = zone.getAttribute('data-target');
  const input = document.querySelector(targetSel);
  const uploadUI = zone.querySelector('.upload-ui');
  const previewUI = zone.querySelector('.preview-ui');
  const img = zone.querySelector('.preview-img');

  const showPreview = file => {
    const reader = new FileReader();
    reader.onload = e => {
      if (img) img.src = e.target.result;
      uploadUI?.classList.add('d-none');
      previewUI?.classList.remove('d-none');
    };
    reader.readAsDataURL(file);
  };

  zone.addEventListener('click', () => input?.click());
  zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('dragover'); });
  zone.addEventListener('dragleave', e => { e.preventDefault(); zone.classList.remove('dragover'); });
  zone.addEventListener('drop', e => {
    e.preventDefault(); zone.classList.remove('dragover');
    if (e.dataTransfer.files?.length) {
      input.files = e.dataTransfer.files;
      showPreview(input.files[0]);
    }
  });
  input?.addEventListener('change', () => {
    if (input.files?.length) showPreview(input.files[0]);
  });
}
document.querySelectorAll('[data-dropzone]').forEach(bindDropzone);

// Simple required check (frontend) – backend vẫn validate
document.getElementById('serviceForm')?.addEventListener('submit', e => {
  const required = ['service_name','category_id','type','duration','status'];
  let ok = true;
  required.forEach(name => {
    const el = document.querySelector(`[name="${name}"]`);
    if (el && !String(el.value || '').trim()) { el.classList.add('is-invalid'); ok = false; }
    else if (el) { el.classList.remove('is-invalid'); }
  });
  if (!ok) {
    e.preventDefault();
    alert('Vui lòng điền đầy đủ thông tin bắt buộc!');
  }
});


