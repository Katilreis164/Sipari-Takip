<?php
// Hata gösterimini aç
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Veritabanı Bağlantı Testi</h1>";

try {
    // Manuel bağlantı testi
    $sunucu = "localhost"; 
    $kullanici = "root";   
    $sifre = "";          
    $veritabani = "siparistakip";
    
    echo "<h2>1. Doğrudan Bağlantı Testi</h2>";
    
    $conn = new mysqli($sunucu, $kullanici, $sifre);
    echo "MySQL sunucusuna bağlantı başarılı! <br>";
    
    // Veritabanı var mı kontrol et
    $db_check = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$veritabani'");
    if ($db_check->num_rows > 0) {
        echo "Veritabanı mevcut: $veritabani <br>";
        
        // Veritabanını seç
        $conn->select_db($veritabani);
        echo "Veritabanı seçildi <br>";
        
        // Tablolar kontrol et
        $tables = $conn->query("SHOW TABLES");
        echo "<strong>Mevcut tablolar:</strong> <br>";
        
        if ($tables->num_rows > 0) {
            while($row = $tables->fetch_assoc()) {
                echo "- " . $row["Tables_in_".$veritabani] . "<br>";
            }
        } else {
            echo "Veritabanında hiç tablo bulunmuyor. <br>";
        }
        
        // Personel tablosunu kontrol et
        $personel_check = $conn->query("SHOW TABLES LIKE 'personel'");
        if ($personel_check->num_rows > 0) {
            echo "<br><strong>Personel tablosu kontrol ediliyor:</strong> <br>";
            
            // Personel kaydı var mı?
            $personel_records = $conn->query("SELECT * FROM personel");
            echo "Personel kaydı sayısı: " . $personel_records->num_rows . "<br>";
            
            if ($personel_records->num_rows > 0) {
                echo "<table border='1'>";
                echo "<tr><th>ID</th><th>Kullanıcı</th><th>Şifre</th></tr>";
                
                while($row = $personel_records->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["id"] . "</td>";
                    echo "<td>" . $row["kullanici"] . "</td>";
                    echo "<td>" . $row["sifre"] . "</td>";
                    echo "</tr>";
                }
                
                echo "</table>";
            } else {
                echo "Personel tablosunda hiç kayıt yok. <br>";
                
                // Kullanıcı ekle
                $insert_result = $conn->query("INSERT INTO personel (kullanici, sifre) VALUES 
                    ('admin', '1234'),
                    ('kullanici1', '1234'),
                    ('kullanici2', '1234'),
                    ('kullanici3', '1234')");
                
                if ($insert_result) {
                    echo "Kullanıcılar başarıyla eklendi! <br>";
                } else {
                    echo "Kullanıcı eklenirken hata: " . $conn->error . "<br>";
                }
            }
        } else {
            echo "<br>Personel tablosu mevcut değil! <br>";
            
            // Personel tablosunu oluştur
            $create_table = $conn->query("CREATE TABLE personel (
                id int(11) NOT NULL AUTO_INCREMENT,
                kullanici varchar(50) NOT NULL,
                sifre varchar(50) NOT NULL,
                son_giris datetime DEFAULT NULL,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
            
            if ($create_table) {
                echo "Personel tablosu oluşturuldu! <br>";
                
                // Kullanıcı ekle
                $insert_result = $conn->query("INSERT INTO personel (kullanici, sifre) VALUES 
                    ('admin', '1234'),
                    ('kullanici1', '1234'),
                    ('kullanici2', '1234'),
                    ('kullanici3', '1234')");
                
                if ($insert_result) {
                    echo "Kullanıcılar başarıyla eklendi! <br>";
                } else {
                    echo "Kullanıcı eklenirken hata: " . $conn->error . "<br>";
                }
            } else {
                echo "Personel tablosu oluşturulurken hata: " . $conn->error . "<br>";
            }
        }
    } else {
        echo "Veritabanı mevcut değil: $veritabani <br>";
        
        // Veritabanını oluştur
        if ($conn->query("CREATE DATABASE IF NOT EXISTS $veritabani DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci")) {
            echo "Veritabanı oluşturuldu: $veritabani <br>";
        } else {
            echo "Veritabanı oluşturma hatası: " . $conn->error . "<br>";
        }
    }
    
    $conn->close();
    
    echo "<h2>2. Baglanti.php Dosyası ile Test</h2>";
    
    // baglanti.php ile test
    require("baglanti.php");
    echo "Baglanti.php ile bağlantı başarılı! <br>";
    
    // Test sorgusu
    $test_query = $baglanti->query("SELECT 1 as test");
    
    if ($test_query) {
        $row = $test_query->fetch_assoc();
        echo "Test sorgusu başarılı: " . $row['test'] . "<br>";
    } else {
        echo "Test sorgusu başarısız: " . $baglanti->error . "<br>";
    }
    
    echo "<h2>3. Giriş Testi</h2>";
    
    // Test kullanıcısı ara
    $login_test = $baglanti->query("SELECT * FROM personel WHERE kullanici = 'kullanici1' AND sifre = '1234'");
    
    if ($login_test && $login_test->num_rows > 0) {
        echo "Giriş testi başarılı! Kullanıcı bulundu. <br>";
        echo "<strong>Şunlarla giriş yapabilirsiniz:</strong> <br>";
        echo "Kullanıcı: kullanici1, Şifre: 1234 <br>";
    } else {
        echo "Giriş testi başarısız. Kullanıcı bulunamadı. Hata: " . $baglanti->error . "<br>";
    }
    
} catch (Exception $e) {
    echo "HATA: " . $e->getMessage();
}
?> 