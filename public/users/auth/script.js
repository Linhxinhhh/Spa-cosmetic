// Toggle show/hide password
document.querySelectorAll('.toggle').forEach(btn => {
  btn.addEventListener('click', () => {
    const target = document.querySelector(btn.dataset.toggle);
    if (!target) return;
    target.type = target.type === 'password' ? 'text' : 'password';
  });
});
