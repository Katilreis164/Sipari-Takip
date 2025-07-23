<?php
/**
 * Veritabanı bağlantı dosyası - PHP 8.x uyumlu
 */

// Hata gösterim ayarları
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Bağlantı bilgileri
$sunucu = "localhost";
$kullanici = "root";
$sifre = ""; // MySQL şifreniz, varsayılan olarak boş
$veritabani = "siparistakip";

// Bağlantıyı oluştur
$baglanti = mysqli_connect($sunucu, $kullanici, $sifre, $veritabani);

// Bağlantı kontrolü
if (!$baglanti) {
    die("Veritabanı bağlantısı başarısız oldu: " . mysqli_connect_error());
}

// Türkçe karakter desteği için UTF-8 ayarı
mysqli_set_charset($baglanti, "utf8mb4");

// Hata ayıklama - dosyanın çalıştığını kontrol eden mesaj
// echo "<small>Veritabanı bağlantısı başarılı</small><br>";

// PHP sonlandığında bağlantıyı otomatik kapat
function baglantiyi_kapat() {
    global $baglanti;
    if ($baglanti) {
        mysqli_close($baglanti);
    }
}
register_shutdown_function('baglantiyi_kapat');
?>
