<?php
// Destroys the catalog-rate.php session and returns to the login screen.
$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
if (PHP_VERSION_ID >= 70300) {
    session_set_cookie_params(array(
        'lifetime' => 0,
        'path' => '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ));
}
session_start();
unset($_SESSION['catalog_authed']);
session_regenerate_id(true);
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Location: catalog-rate.php', true, 303);
exit;
