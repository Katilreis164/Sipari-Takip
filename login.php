<?php
// Hata mesajlarını gösterme
error_reporting(0);
ini_set('display_errors', 0);

// Oturumu başlat
session_start();

// URL'den temizle parametresi geldiğinde tüm oturumu temizle
if (isset($_GET['temizle']) && $_GET['temizle'] == 1) {
    // Tüm session değişkenlerini temizle
    $_SESSION = array();
    
    // Session çerezini sil
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Oturumu yok et
    session_destroy();
    
    // Tüm çerezleri temizle
    foreach ($_COOKIE as $key => $value) {
        setcookie($key, '', time() - 3600, '/');
    }
    
    // Yeni bir oturum başlat
    session_start();
}

// Kullanıcı zaten giriş yapmışsa ana sayfaya yönlendir
if (isset($_SESSION['personel'])) {
    header("Location: index.php");
    exit;
}

include 'baglanti.php';

$giris_hatasi = false;
$hata_mesaji = "";

// Form gönderilip gönderilmediğini kontrol et
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kullanici = trim($_POST['kullanici']); // Boşlukları temizle
    $sifre = $_POST['sifre'];

    // Kullanıcı adı ve şifreyi kontrol et (şifre MD5 ile hashlenmiş)
    $hashedSifre = md5($sifre);
    $sql = "SELECT * FROM personel WHERE kullanici = '$kullanici' AND sifre = '$hashedSifre'";
    $sonuc = $baglanti->query($sql);

    if ($sonuc && $sonuc->num_rows > 0) {
        // Giriş başarılı
        $row = $sonuc->fetch_assoc();
        $_SESSION['personel'] = $row['kullanici'];
        $_SESSION['kullanici_id'] = $row['id'];

        // Son giriş tarihini güncelle
        $sql_son_giris = "UPDATE personel SET son_giris = NOW() WHERE id = " . $row['id'];
        $baglanti->query($sql_son_giris);

        header("Location: index.php");
        exit;
    } else {
        // Giriş başarısız
        $giris_hatasi = true;
        $hata_mesaji = "Kullanıcı adı veya şifre hatalı!";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap - Sipariş Takip Sistemi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
    // Sayfa yüklendiğinde PHP session kontrolünü bypas etmek için çerezleri temizle
    window.onload = function() {
        // URL'de temizle parametresi varsa çerezleri temizle
        if (window.location.href.indexOf('temizle=1') !== -1) {
            document.cookie.split(";").forEach(function(cookie) {
                document.cookie = cookie.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/");
            });
            console.log("Çerezler temizlendi!");
            // Temizle parametresini kaldır
            history.replaceState(null, null, 'login.php');
        }
    };
    </script>
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h2 {
            color: #333;
            font-weight: 600;
        }
        .btn-primary {
            background-color: #4e73df;
            border-color: #4e73df;
            width: 100%;
            font-weight: 600;
        }
        .form-label {
            font-weight: 500;
        }
        .error-message {
            color: #dc3545;
            font-size: 14px;
            margin-top: 15px;
            text-align: center;
        }
        .db-test-link {
            display: block;
            text-align: center;
            font-size: 14px;
            margin-top: 20px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="login-container">
                    <div class="login-header">
                        <h2>Sipariş Takip Sistemi</h2>
                        <p class="text-muted">Lütfen giriş yapın</p>
                    </div>

                    <?php if ($giris_hatasi): ?>
                    <div class="alert alert-danger">
                        <?php echo $hata_mesaji; ?>
                    </div>
                    <?php endif; ?>

                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <div class="mb-3">
                            <label for="kullanici" class="form-label">Kullanıcı Adı</label>
                            <select name="kullanici" id="kullanici" class="form-select" required>
                                <option value="">Kullanıcı Seçin</option>
                                <option value="admin">admin</option>
                                <option value="kullanici1">kullanici1</option>
                                <option value="kullanici2">kullanici2</option>
                                <option value="kullanici3">kullanici3</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="sifre" class="form-label">Şifre</label>
                            <input type="password" class="form-control" id="sifre" name="sifre" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Giriş Yap</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
