<?php
// Hata gösterimini aç
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Veritabanı bilgileri
$sunucu = "localhost";
$kullanici = "root";
$sifre = ""; // MySQL root şifreniz, varsayılan olarak boş

// Başlık
echo "<h1>Veritabanı Test ve Kurulum</h1>";

// MySQL'e bağlan
$baglanti = mysqli_connect($sunucu, $kullanici, $sifre);
if (!$baglanti) {
    die("MySQL'e bağlanırken hata oluştu: " . mysqli_connect_error());
}
echo "MySQL'e başarıyla bağlandı.<br>";

// siparistakip veritabanını temizle ve oluştur
$sql = "DROP DATABASE IF EXISTS siparistakip";
if (mysqli_query($baglanti, $sql)) {
    echo "Eski siparistakip veritabanı silindi.<br>";
} else {
    echo "Hata: " . mysqli_error($baglanti) . "<br>";
}

$sql = "CREATE DATABASE siparistakip DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
if (mysqli_query($baglanti, $sql)) {
    echo "siparistakip veritabanı başarıyla oluşturuldu.<br>";
} else {
    echo "Hata: " . mysqli_error($baglanti) . "<br>";
}

// siparistakip veritabanını seç
mysqli_select_db($baglanti, "siparistakip");

// personel tablosunu oluştur
$sql = "CREATE TABLE personel (
    id INT(11) NOT NULL AUTO_INCREMENT,
    kullanici VARCHAR(50) NOT NULL,
    sifre VARCHAR(50) NOT NULL,
    son_giris DATETIME DEFAULT NULL,
    PRIMARY KEY (id)
)";

if (mysqli_query($baglanti, $sql)) {
    echo "personel tablosu başarıyla oluşturuldu.<br>";
} else {
    echo "Hata: " . mysqli_error($baglanti) . "<br>";
}

// Kullanıcıları ekle
$sql = "INSERT INTO personel (kullanici, sifre) VALUES 
    ('admin', MD5('1234')),
    ('kullanici1', MD5('1234')),
    ('kullanici2', MD5('1234')),
    ('kullanici3', MD5('1234'))";

if (mysqli_query($baglanti, $sql)) {
    echo "Kullanıcılar başarıyla eklendi.<br>";
} else {
    echo "Hata: " . mysqli_error($baglanti) . "<br>";
}

// siparis tablosunu oluştur
$sql = "CREATE TABLE siparis (
    id int(11) NOT NULL AUTO_INCREMENT,
    yilsiparis varchar(50) NOT NULL,
    isim varchar(50) NOT NULL,
    soyisim varchar(50) NOT NULL,
    evtel varchar(20) DEFAULT NULL,
    ceptel varchar(20) DEFAULT NULL,
    email varchar(100) DEFAULT NULL,
    adres text,
    semt varchar(50) DEFAULT NULL,
    urun varchar(100) DEFAULT NULL,
    aciklama text,
    starih date DEFAULT NULL,
    s_time time DEFAULT NULL,
    ttarih2 date DEFAULT NULL,
    e_time time DEFAULT NULL,
    satis varchar(50) DEFAULT NULL,
    nt text,
    fiyat decimal(10,2) DEFAULT NULL,
    kalan decimal(10,2) DEFAULT NULL,
    fiyat_sn decimal(10,2) DEFAULT NULL,
    kdvkalan decimal(10,2) DEFAULT NULL,
    kapora decimal(10,2) DEFAULT NULL,
    durum varchar(50) DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY yilsiparis (yilsiparis)
)";

if (mysqli_query($baglanti, $sql)) {
    echo "siparis tablosu başarıyla oluşturuldu.<br>";
} else {
    echo "Hata: " . mysqli_error($baglanti) . "<br>";
}

// Kullanıcıları kontrol et
$sql = "SELECT * FROM personel";
$result = mysqli_query($baglanti, $sql);

if ($result) {
    $kullanici_sayisi = mysqli_num_rows($result);
    echo "<h3>Veritabanındaki Kullanıcılar ($kullanici_sayisi):</h3>";
    
    if ($kullanici_sayisi > 0) {
        echo "<table border='1' style='border-collapse: collapse; margin: 20px auto; width: 80%; max-width: 500px; background-color: #f8f9fa;'>";
        echo "<tr style='background-color: #4e73df; color: white;'><th style='padding: 10px;'>ID</th><th style='padding: 10px;'>Kullanıcı</th><th style='padding: 10px;'>Şifre</th></tr>";
        
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td style='padding: 8px; text-align: center;'>" . $row['id'] . "</td>";
            echo "<td style='padding: 8px; text-align: center; font-weight: bold;'>" . $row['kullanici'] . "</td>";
            echo "<td style='padding: 8px; text-align: center;'>*****</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        echo "<p style='text-align: center; font-weight: bold; color: #4e73df;'>Giriş yaparken kullanıcı adını tam olarak yukarıdaki gibi seçmelisiniz!</p>";
    } else {
        echo "Veritabanında hiç kullanıcı bulunamadı.";
    }
} else {
    echo "Kullanıcı sorgusu çalıştırılırken hata oluştu: " . mysqli_error($baglanti) . "<br>";
}

// Diğer tabloları da oluştur
$sql = "CREATE TABLE resimler (
    id int(11) NOT NULL AUTO_INCREMENT,
    siparisno varchar(20) NOT NULL,
    resim varchar(255) NOT NULL,
    PRIMARY KEY (id)
)";

if (mysqli_query($baglanti, $sql)) {
    echo "resimler tablosu başarıyla oluşturuldu.<br>";
} else {
    echo "Hata: " . mysqli_error($baglanti) . "<br>";
}

$sql = "CREATE TABLE krokiler (
    id int(11) NOT NULL AUTO_INCREMENT,
    siparisno varchar(20) NOT NULL,
    kroki varchar(255) NOT NULL,
    PRIMARY KEY (id)
)";

if (mysqli_query($baglanti, $sql)) {
    echo "krokiler tablosu başarıyla oluşturuldu.<br>";
} else {
    echo "Hata: " . mysqli_error($baglanti) . "<br>";
}

$sql = "CREATE TABLE kisiler (
    id int(11) NOT NULL AUTO_INCREMENT,
    adsoyad varchar(100) NOT NULL,
    PRIMARY KEY (id)
)";

if (mysqli_query($baglanti, $sql)) {
    echo "kisiler tablosu başarıyla oluşturuldu.<br>";
} else {
    echo "Hata: " . mysqli_error($baglanti) . "<br>";
}

$sql = "INSERT INTO kisiler (adsoyad) VALUES 
    ('Ahmet Yılmaz'),
    ('Ayşe Demir'),
    ('Mehmet Kaya')";

if (mysqli_query($baglanti, $sql)) {
    echo "Örnek satış personeli başarıyla eklendi.<br>";
} else {
    echo "Hata: " . mysqli_error($baglanti) . "<br>";
}

// Örnek siparişler oluşturalım
echo "<h3>Örnek siparişler oluşturuluyor...</h3>";

// Sipariş sayısı
$siparis_sayisi = 10;

// Örnek siparişleri ekle
for ($i = 1; $i <= $siparis_sayisi; $i++) {
    // Yıl
    $yil = date("Y");
    // Rastgele sayı
    $rastgele = mt_rand(100000, 999999);
    
    // Yeni sipariş numarası formatı (YIL-SAYI)
    $siparis_no = $yil . "-" . $rastgele;
    
    // Rastgele durumlar
    $durumlar = array("Üretim", "Hazır", "Tamamlandı");
    $durum = $durumlar[array_rand($durumlar)];
    
    // Rastgele tarihler (son 30 gün içinde)
    $gun = rand(1, 30);
    $siparis_tarih = date('Y-m-d', strtotime("-$gun days"));
    $teslim_tarih = date('Y-m-d', strtotime("-$gun days +7 days"));
    
    // Rastgele fiyat (1000-10000 arası)
    $fiyat = rand(1000, 10000);
    $kapora = round($fiyat * 0.3, 2); // %30 kapora
    $kalan = $fiyat - $kapora;
    $fiyat_kdv = round($fiyat * 1.18, 2); // %18 KDV
    $kdv_kalan = $fiyat_kdv - $kapora;
    
    // Rastgele müşteri bilgileri
    $isimler = array("Ahmet", "Mehmet", "Ayşe", "Fatma", "Ali", "Veli", "Zeynep");
    $soyisimler = array("Yılmaz", "Kaya", "Demir", "Çelik", "Şahin", "Yıldız", "Öztürk");
    $isim = $isimler[array_rand($isimler)];
    $soyisim = $soyisimler[array_rand($soyisimler)];
    
    // Telefon
    $evtel = "0212" . rand(1000000, 9999999);
    $ceptel = "053" . rand(1, 9) . rand(1000000, 9999999);
    
    // Adres
    $adres = "Örnek Mahallesi, Test Sokak, No:" . rand(1, 100) . ", İstanbul";
    
    // Sipariş ekle
    $sql = "INSERT INTO siparis (yilsiparis, isim, soyisim, evtel, ceptel, adres, urun, aciklama, 
            starih, ttarih2, fiyat, kapora, kalan, fiyat_sn, kdvkalan, durum) 
            VALUES ('$siparis_no', '$isim', '$soyisim', '$evtel', '$ceptel', '$adres', 
            'Cam', 'Test sipariş #$i', '$siparis_tarih', '$teslim_tarih', 
            $fiyat, $kapora, $kalan, $fiyat_kdv, $kdv_kalan, '$durum')";
    
    if (mysqli_query($baglanti, $sql)) {
        echo "Örnek sipariş #$i başarıyla oluşturuldu: $siparis_no<br>";
    } else {
        echo "Hata: " . mysqli_error($baglanti) . "<br>";
    }
}

// Bağlantıyı kapat
mysqli_close($baglanti);

echo "<h2>Tüm işlemler tamamlandı!</h2>";
echo '<div style="margin: 30px 0;">';
echo '<a href="login.php" style="display: inline-block; padding: 10px 20px; background-color: #4285f4; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">Giriş Sayfasına Git</a>';
echo '</div>';

echo '<script>';
echo 'function clearSessionAndCookies() {';
echo '  document.cookie.split(";").forEach(function(cookie) {';
echo '    document.cookie = cookie.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/");';
echo '  });';
echo '  alert("Çerezler temizlendi! Şimdi giriş sayfasına yönlendiriliyorsunuz.");';
echo '  window.location.href = "login.php";';
echo '}';
echo '</script>';

echo '<div style="margin-top: 20px;">';
echo '<button onclick="clearSessionAndCookies()" style="padding: 10px 20px; background-color: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">Çerezleri Temizle ve Giriş Yap</button>';
echo '</div>';
?> 