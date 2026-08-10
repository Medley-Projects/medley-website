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

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", function () {
      initReveal();
      initNavbarScroll();
      initMarqueeClone();
      initContactMailto();
    });
  } else {
    initReveal();
    initNavbarScroll();
    initMarqueeClone();
    initContactMailto();
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
