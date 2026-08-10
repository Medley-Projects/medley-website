(function () {
  "use strict";

  /** Scroll reveal */
  function initReveal() {
    var nodes = document.querySelectorAll(".reveal");
    if (!nodes.length || !("IntersectionObserver" in window)) {
      nodes.forEach(function (el) {
        el.classList.add("is-visible");
      });
      return;
    }

    var io = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add("is-visible");
            io.unobserve(entry.target);
          }
        });
      },
      { root: null, rootMargin: "0px 0px -8% 0px", threshold: 0.12 }
    );

    nodes.forEach(function (el) {
      io.observe(el);
    });
  }

  /** Navbar shadow when scrolled */
  function initNavbarScroll() {
    var nav = document.querySelector(".site-navbar");
    if (!nav) return;

    var onScroll = function () {
      if (window.scrollY > 12) {
        nav.classList.add("is-scrolled");
      } else {
        nav.classList.remove("is-scrolled");
      }
    };

    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
  }

  /** Legacy marquee: duplicate list items only when markup exists */
  function initMarqueeClone() {
    var root = document.documentElement;
    var marqueeContent = document.querySelector("ul.marquee-content");
    if (!marqueeContent || !marqueeContent.children.length) return;

    var displayed = getComputedStyle(root).getPropertyValue("--marquee-elements-displayed").trim();
    var n = parseInt(displayed, 10);
    if (!n || n < 1) n = 1;

    root.style.setProperty("--marquee-elements", String(marqueeContent.children.length));

    for (var i = 0; i < n; i++) {
      marqueeContent.appendChild(marqueeContent.children[i].cloneNode(true));
    }
  }

  function initContactMailto() {
    if (typeof sendEmail === "function" && document.getElementById("send_email")) {
      sendEmail();
    }
  }

  /** Deep links (QR codes) to RF hazard sign accordion items */
  function initSignDeepLink() {
    var hash = window.location.hash;
    if (!hash || hash.length < 2) return;

    var raw = hash.slice(1);
    var accordion = document.getElementById("rf-hazard-signs");

    // Pages hosting the sign accordion: open and scroll to the matching item.
    if (accordion) {
      var target = document.getElementById(raw);
      if (!target) target = document.getElementById("collapse-" + raw);
      if (!target || !target.classList.contains("accordion-collapse")) return;

      var button = document.querySelector('[data-bs-target="#' + target.id + '"]');

      var reveal = function () {
        var header = button ? button.closest(".accordion-header") : null;
        (header || target).scrollIntoView({ behavior: "smooth", block: "start" });
      };

      if (window.bootstrap && bootstrap.Collapse) {
        var instance = bootstrap.Collapse.getOrCreateInstance(target, { toggle: false });
        target.addEventListener("shown.bs.collapse", reveal, { once: true });
        instance.show();
      } else {
        target.classList.add("show");
        if (button) {
          button.classList.remove("collapsed");
          button.setAttribute("aria-expanded", "true");
        }
        reveal();
      }
      return;
    }

    // Home page: forward sign hashes to the RF Compliance page.
    var isHome = !!document.querySelector(".bg-header");
    if (!isHome || document.getElementById(raw)) return;
    if (raw === "rf-hazard-signs") {
      window.location.replace("rf-compliance.html#rf-hazard-signs");
      return;
    }
    window.location.replace("rf-compliance.html#collapse-" + raw);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", function () {
      initReveal();
      initNavbarScroll();
      initMarqueeClone();
      initContactMailto();
      initSignDeepLink();
    });
  } else {
    initReveal();
    initNavbarScroll();
    initMarqueeClone();
    initContactMailto();
    initSignDeepLink();
  }
})();

/** Contact form mailto builder (global for inline handlers) */
function sendEmail() {
  var link = document.getElementById("send_email");
  if (!link) return;

  function val(id) {
    var el = document.getElementById(id);
    return el ? el.value.trim() : "";
  }

  var fields = [
    ["Name", val("f_name")],
    ["Company Name", val("f_company")],
    ["Address", val("f_address")],
    ["City", val("f_city")],
    ["State/Prov", val("f_state")],
    ["Country", val("f_country")],
    ["Zip", val("f_zip")],
    ["Phone No", val("f_phone")],
    ["Fax", val("f_fax")],
    ["Email", val("f_email")],
    ["Message", val("f_message")]
  ];

  var lines = ["Form details below.", ""];
  fields.forEach(function (pair) {
    if (pair[1]) lines.push(pair[0] + ": " + pair[1]);
  });

  var email = "info@medleynetworks.com";
  var href =
    "mailto:" + email + "?subject=" + encodeURIComponent("Medley Networks form submissions") + "&body=" +
    encodeURIComponent(lines.join("\r\n"));
  link.setAttribute("href", href);
}
