<?php
// Veritabanı bağlantı bilgileri
$host = "localhost";
$username = "root";
$password = ""; // MySQL'de kullandığınız şifre
$dbname = "siparistakip";

// Bağlantı oluştur
$conn = new mysqli($host, $username, $password, $dbname);

// Bağlantı hatasını kontrol et
if ($conn->connect_error) {
    die("Veritabanı bağlantısı başarısız oldu: " . $conn->connect_error);
}

try {
    // Veritabanı bağlantısı yapılıyor
    include('baglanti.php');

    // Sipariş numarası oluştur
    $yil = date("Y"); // Yıl
    $rastgele = mt_rand(100000, 999999); // 6 haneli rastgele sayı
    
    // Benzersiz sipariş numarası (YIL + RASTGELE SAYI)
    $siparis_no = $yil . "-" . $rastgele;

    // Test amaçlı müşteri bilgileri
    $musteri_adi = "Test Müşteri " . date("d.m.Y H:i:s");
    $tarih = date("Y-m-d H:i:s");

    // Veritabanındaki sipariş tablosunu yeniden oluştur
    $conn->query("DROP TABLE IF EXISTS siparis");
    $sql_tablo = "CREATE TABLE siparis (
        id int(11) NOT NULL AUTO_INCREMENT,
        yilsiparis varchar(50) NOT NULL,
        isim varchar(50) NOT NULL,
        soyisim varchar(50) NOT NULL DEFAULT '',
        evtel varchar(20) DEFAULT NULL,
        ceptel varchar(20) DEFAULT NULL,
        email varchar(100) DEFAULT NULL,
        adres text DEFAULT NULL,
        semt varchar(50) DEFAULT NULL,
        urun varchar(100) DEFAULT NULL,
        aciklama text DEFAULT NULL,
        starih date DEFAULT NULL,
        s_time time DEFAULT NULL,
        ttarih2 date DEFAULT NULL,
        e_time time DEFAULT NULL,
        satis varchar(50) DEFAULT NULL,
        nt text DEFAULT NULL,
        fiyat decimal(10,2) DEFAULT NULL,
        kalan decimal(10,2) DEFAULT NULL,
        fiyat_sn decimal(10,2) DEFAULT NULL,
        kdvkalan decimal(10,2) DEFAULT NULL,
        kapora decimal(10,2) DEFAULT NULL,
        durum varchar(50) DEFAULT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY yilsiparis (yilsiparis)
    )";

    if ($conn->query($sql_tablo) === TRUE) {
        echo "Sipariş tablosu yeniden oluşturuldu.<br>";
    } else {
        echo "Tablo oluşturma hatası: " . $conn->error . "<br>";
    }

    // Test siparişi ekle (50 farklı sipariş oluştur)
    for ($i = 1; $i <= 50; $i++) {
        // Rastgele sipariş no
        $yil = date("Y"); // Yıl
        $rastgele = mt_rand(100000, 999999); // 6 haneli rastgele sayı
        
        // Benzersiz sipariş numarası (YIL + RASTGELE SAYI)
        $siparis_no = $yil . "-" . $rastgele;
        $musteri_adi = "Test Müşteri " . $i;
        
        // SQL sorgusu
        $sql = "INSERT INTO siparis (yilsiparis, isim, soyisim, starih) 
                VALUES ('$siparis_no', '$musteri_adi', 'Soyadı $i', '$tarih')";

        if ($conn->query($sql) === TRUE) {
            echo "Sipariş #$i başarıyla eklendi: $siparis_no <br>";
        } else {
            echo "Hata: " . $sql . "<br>" . $conn->error . "<br>";
        }
    }

    // Siparişleri listele
    $sonuc_sql = "SELECT id, yilsiparis, isim, soyisim, starih FROM siparis ORDER BY id";
    $sonuc = $conn->query($sonuc_sql);

    if ($sonuc->num_rows > 0) {
        echo "<h3>Eklenen Siparişler:</h3>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Sipariş No</th><th>Müşteri</th><th>Tarih</th></tr>";
        
        while($row = $sonuc->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["id"] . "</td>";
            echo "<td>" . $row["yilsiparis"] . "</td>";
            echo "<td>" . $row["isim"] . " " . $row["soyisim"] . "</td>";
            echo "<td>" . $row["starih"] . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "Siparişler bulunamadı.";
    }

    // Bağlantıyı kapat
    $conn->close();

    echo "<p><a href='listele.php'>Sipariş Listesi Sayfasına Git</a></p>";
} catch (Exception $e) {
    echo "Hata: " . $e->getMessage();
}
?>