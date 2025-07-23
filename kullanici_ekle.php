<?php
// Hata gösterimini aç
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Veritabanı bilgileri
$sunucu = "localhost"; 
$kullanici = "root";   
$sifre = "";          
$veritabani = "siparistakip";

echo "<h1>Kullanıcı Ekleme</h1>";

try {
    // Doğrudan bağlantı
    $baglanti = new mysqli($sunucu, $kullanici, $sifre, $veritabani);
    
    if ($baglanti->connect_error) {
        throw new Exception("Bağlantı hatası: " . $baglanti->connect_error);
    }
    
    echo "Veritabanına bağlantı başarılı! <br>";
    
    // Kullanıcıları temizle
    $baglanti->query("DELETE FROM personel");
    echo "Tüm kullanıcılar silindi! <br>";
    
    // Kullanıcıları ekle
    $insert = "INSERT INTO personel (kullanici, sifre) VALUES 
        ('admin', '1234'),
        ('kullanici1', '1234'),
        ('kullanici2', '1234'),
        ('kullanici3', '1234')";
        
    if ($baglanti->query($insert)) {
        echo "Kullanıcılar başarıyla eklendi! <br>";
    } else {
        throw new Exception("Kullanıcı eklenirken hata: " . $baglanti->error);
    }
    
    // Kullanıcıları listele
    $result = $baglanti->query("SELECT * FROM personel");
    if ($result && $result->num_rows > 0) {
        echo "<h2>Kullanıcı Listesi:</h2>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Kullanıcı</th><th>Şifre</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["id"] . "</td>";
            echo "<td>" . $row["kullanici"] . "</td>";
            echo "<td>" . $row["sifre"] . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        throw new Exception("Kullanıcı listesi alınamadı: " . $baglanti->error);
    }
    
    $baglanti->close();
    
    echo "<br><p>İşlemler tamamlandı! <a href='login.php'>Şimdi giriş sayfasına gidebilirsiniz.</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color:red;font-weight:bold;'>HATA: " . $e->getMessage() . "</p>";
} 