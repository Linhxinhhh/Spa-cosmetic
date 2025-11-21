window.toggleDropdown = function (id) {
  const dropdown = document.getElementById(`dropdown-${id}`);
  const all = document.querySelectorAll('[id^="dropdown-"]');
  all.forEach(dd => { if (dd.id !== `dropdown-${id}`) dd.classList.add('hidden'); });
  if (dropdown) dropdown.classList.toggle('hidden');
};

document.addEventListener('DOMContentLoaded', function () {
  // Đóng dropdown khi click ngoài
  document.addEventListener('click', function (e) {
    const isToggleBtn = e.target.closest('[onclick^="toggleDropdown"]');
    if (!isToggleBtn) {
      document.querySelectorAll('[id^="dropdown-"]').forEach(d => d.classList.add('hidden'));
    }
  });

  // Tự động submit form filter khi đổi select/date (nếu phần tử thuộc 1 <form>)
  document.querySelectorAll('select[name], input[type="date"]').forEach(el => {
    el.addEventListener('change', () => {
      if (el.form) el.form.submit();
    });
  });
});