/* Medley Networks — shared interactions (vanilla JS) */
(function () {
  "use strict";

  var header = document.getElementById("siteHeader");
  var toggle = document.getElementById("navToggle");
  var nav = document.getElementById("siteNav");

  /* Mobile navigation */
  function closeNav() {
    if (nav && nav.classList.contains("open")) {
      nav.classList.remove("open");
      toggle.classList.remove("open");
      toggle.setAttribute("aria-expanded", "false");
    }
  }
  if (toggle && nav) {
    toggle.addEventListener("click", function (e) {
      e.stopPropagation();
      var open = nav.classList.toggle("open");
      toggle.classList.toggle("open", open);
      toggle.setAttribute("aria-expanded", open ? "true" : "false");
    });
    nav.addEventListener("click", function (e) {
      if (e.target.closest("a")) closeNav();
    });
    document.addEventListener("click", function (e) {
      if (!e.target.closest(".site-header")) closeNav();
    });
  }

  /* Subtle header elevation on scroll */
  if (header && "IntersectionObserver" in window) {
    var sentinel = document.createElement("div");
    sentinel.style.position = "absolute";
    sentinel.style.top = "0";
    sentinel.style.height = "1px";
    sentinel.style.width = "1px";
    document.body.appendChild(sentinel);
    new IntersectionObserver(function (entries) {
      header.classList.toggle("is-scrolled", !entries[0].isIntersecting);
    }).observe(sentinel);
  }

  /* Accordion */
  var accItems = document.querySelectorAll(".acc-item");
  accItems.forEach(function (item) {
    var head = item.querySelector(".acc-head");
    if (!head) return;
    head.addEventListener("click", function () {
      var open = item.classList.toggle("open");
      var panel = item.querySelector(".acc-panel");
      if (panel) {
        if (open) {
          panel.style.maxHeight = panel.scrollHeight + "px";
        } else {
          panel.style.maxHeight = "0px";
        }
      }
      if (open) {
        accItems.forEach(function (other) {
          if (other !== item && other.classList.contains("open")) {
            other.classList.remove("open");
            var p = other.querySelector(".acc-panel");
            if (p) p.style.maxHeight = "0px";
          }
        });
      }
    });
  });

  /* Open accordion when deep-linked (e.g. index.html#rf_safety_signage_guidelines) */
  function openDeepLink() {
    var hash = window.location.hash;
    if (!hash) return;
    var target = document.getElementById(hash.substring(1));
    if (!target) return;
    if (target.classList && target.classList.contains("acc-head")) {
      target.click();
      setTimeout(function () {
        var rect = target.getBoundingClientRect();
        if (rect.top < 0 || rect.bottom > window.innerHeight) {
          target.scrollIntoView({ behavior: "smooth", block: "center" });
        }
      }, 350);
    }
  }
  if (window.location.hash && accItems.length) openDeepLink();
  window.addEventListener("hashchange", openDeepLink);
})();
