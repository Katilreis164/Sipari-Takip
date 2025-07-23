<?php
// Hata gösterimini aç
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Veritabanı bağlantısı
include('baglanti.php');

echo "<h1>Veritabanı Sıfırlama ve Yeniden Oluşturma</h1>";

// Sipariş tablosunu temizle
$sql = "TRUNCATE TABLE siparis";
if ($baglanti->query($sql)) {
    echo "Sipariş tablosu temizlendi.<br>";
} else {
    echo "Hata: " . $baglanti->error . "<br>";
}

// Örnek siparişler oluşturalım
echo "<h3>Yeni formatta örnek siparişler oluşturuluyor...</h3>";

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
    $semt = "Kadıköy";
    
    // Sipariş ekle
    $sql = "INSERT INTO siparis (yilsiparis, isim, soyisim, evtel, ceptel, adres, semt, urun, aciklama, 
            starih, ttarih2, fiyat, kapora, kalan, fiyat_sn, kdvkalan, durum) 
            VALUES ('$siparis_no', '$isim', '$soyisim', '$evtel', '$ceptel', '$adres', '$semt', 
            'Cam', 'Test sipariş #$i', '$siparis_tarih', '$teslim_tarih', 
            $fiyat, $kapora, $kalan, $fiyat_kdv, $kdv_kalan, '$durum')";
    
    if ($baglanti->query($sql)) {
        echo "Örnek sipariş #$i başarıyla oluşturuldu: $siparis_no<br>";
    } else {
        echo "Hata: " . $baglanti->error . "<br>";
    }
}

// Bağlantıyı kapat
$baglanti->close();

echo "<h2>Tüm işlemler tamamlandı!</h2>";
echo '<p>Veritabanı sıfırlandı ve yeni format (YIL-SAYI) ile örnek siparişler oluşturuldu.</p>';
echo '<p><a href="listele.php" style="display: inline-block; padding: 10px 20px; background-color: #4285f4; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">Sipariş Listesine Git</a></p>';
?> 