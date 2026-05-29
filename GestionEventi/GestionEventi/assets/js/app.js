document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.getElementById('menuToggle');
  const nav = document.getElementById('mainNav');
  if (toggle && nav) {
    toggle.addEventListener('click', () => {
      const isOpen = nav.classList.toggle('open');
      toggle.setAttribute('aria-expanded', String(isOpen));
    });
  }

  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', evt => {
      if (!confirm(el.dataset.confirm)) evt.preventDefault();
    });
  });

  document.querySelectorAll('input, select, textarea').forEach(field => {
    field.addEventListener('invalid', () => field.classList.add('field-error'));
    field.addEventListener('input', () => field.classList.remove('field-error'));
  });
});
