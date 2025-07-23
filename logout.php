<?php
// Oturumu başlat
session_start();

// Tüm oturum değişkenlerini sıfırlama
$_SESSION = array();

// Oturum çerezini silme
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Oturumu yok et
session_destroy();

// Tüm tarayıcı çerezlerini silme
foreach ($_COOKIE as $key => $value) {
    setcookie($key, '', time() - 3600, '/');
}

// Login sayfasına yönlendirme
header("Location: login.php?temizle=1");
exit;
?>
