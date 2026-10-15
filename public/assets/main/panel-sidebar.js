"use strict";

document.addEventListener("DOMContentLoaded", () => {
  const sidebar = document.querySelector(".sidebar-nav");
  if (!sidebar) {
    return;
  }

  const activeLink = sidebar.querySelector(
    ".nav-link.active:not([data-bs-toggle='collapse'])",
  );
  const submenus = sidebar.querySelectorAll(".collapse");

  submenus.forEach((submenu) => {
    const shouldBeOpen = activeLink !== null && submenu.contains(activeLink);
    submenu.classList.toggle("show", shouldBeOpen);
    const trigger = sidebar.querySelector(
      `[data-bs-toggle='collapse'][data-bs-target="#${submenu.id}"]`,
    );
    trigger?.setAttribute("aria-expanded", String(shouldBeOpen));
  });
});
