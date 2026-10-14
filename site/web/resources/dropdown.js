(function () {
  "use strict";

  const dropdown = document.querySelector("#user-dropdown");
  const checkbox = document.querySelector("#user-menu-toggle");
  const label = document.querySelector("#user-menu-label");
  const menu = document.querySelector("#user-dropdown-menu");

  if (!dropdown || !checkbox || !label || !menu) {
    return;
  }

  document.addEventListener("click", (event) => {
    if (!dropdown.contains(event.target)) {
      checkbox.checked = false;
    }
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape" && checkbox.checked) {
      checkbox.checked = false;
      label.focus();
    }
  });

  menu.addEventListener("keydown", (event) => {
    const links = menu.querySelectorAll("a");
    const currentIndex = Array.from(links).indexOf(document.activeElement);

    if (event.key === "ArrowDown") {
      event.preventDefault();
      const nextIndex = (currentIndex + 1) % links.length;
      links[nextIndex].focus();
    } else if (event.key === "ArrowUp") {
      event.preventDefault();
      const prevIndex = currentIndex <= 0 ? links.length - 1 : currentIndex - 1;
      links[prevIndex].focus();
    }
  });

  checkbox.addEventListener("keydown", (event) => {
    if (event.key === "Enter" || event.key === " ") {
      event.preventDefault();
      checkbox.checked = !checkbox.checked;
      if (checkbox.checked) {
        setTimeout(() => {
          const firstLink = menu.querySelector("a");
          if (firstLink) {
            firstLink.focus();
          }
        }, 100);
      }
    }
  });

  const updateAriaExpanded = () => {
    checkbox.setAttribute("aria-expanded", checkbox.checked ? "true" : "false");
  };

  checkbox.addEventListener("change", updateAriaExpanded);
  updateAriaExpanded();
})();
