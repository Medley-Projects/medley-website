// Populates the contact form's CSRF token without converting index.html to PHP.
// Fetches a per-session token from csrf-token.php (same-origin, allowed by
// connect-src 'self') and injects it into input[name="csrf_token"].
// External file (not inline) so it complies with script-src 'self'.
(function () {
  document.addEventListener('DOMContentLoaded', function () {
    var fields = document.querySelectorAll('input[name="csrf_token"]');
    if (!fields.length) return;
    fetch('csrf-token.php', { credentials: 'same-origin' })
      .then(function (res) {
        if (!res.ok) throw new Error('token fetch failed');
        return res.json();
      })
      .then(function (data) {
        if (data && data.token) {
          for (var i = 0; i < fields.length; i++) {
            fields[i].value = data.token;
          }
        }
      })
      .catch(function () {
        // Leave token empty; form_send.php will reject with a 403 + message.
      });
  });
})();
