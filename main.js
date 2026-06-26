document.addEventListener("DOMContentLoaded", () => {
  const toggle = document.querySelector(".menu-toggle");
  const links = document.querySelector(".nav-links");
  if (toggle && links) {
    toggle.addEventListener("click", () => links.classList.toggle("open"));
  }

  setTimeout(() => {
    document.querySelectorAll(".flash").forEach(f => {
      f.style.opacity = "0";
      f.style.transform = "translateY(-8px)";
    });
  }, 3500);
});
