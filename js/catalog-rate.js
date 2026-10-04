// Password visibility toggle for the catalog-rate.php login form.
// External file (not inline) so it complies with the site's
// Content-Security-Policy: script-src 'self' ...
(function () {
  document.addEventListener('DOMContentLoaded', function () {
    var input = document.getElementById('catalog_password');
    var toggle = document.getElementById('toggle-catalog-password');
    if (!input || !toggle) return;
    toggle.addEventListener('click', function () {
      var show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      toggle.textContent = show ? 'Hide' : 'Show';
      toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
      toggle.setAttribute('aria-pressed', show ? 'true' : 'false');
      input.focus();
    });
  });
})();
