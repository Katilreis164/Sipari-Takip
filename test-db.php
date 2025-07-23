<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Veritabanı Bağlantı Testi</h2>";

$host = "localhost";
$username = "root";
$password = ""; // MySQL şifreniz - boş bırakıldı
$dbname = "siparistakip";

try {
    $conn = new mysqli($host, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Bağlantı hatası: " . $conn->connect_error);
    }
    
    echo "<p style='color:green;'>Veritabanı bağlantısı başarılı!</p>";
    
    // Tabloları kontrol edelim
    $result = $conn->query("SHOW TABLES");
    
    if ($result) {
        echo "<h3>Veritabanındaki Tablolar:</h3>";
        echo "<ul>";
        while($row = $result->fetch_array()) {
            echo "<li>" . $row[0] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color:red;'>Veritabanında tablo bulunamadı veya sorgu başarısız oldu.</p>";
    }
    
    $conn->close();
} catch (Exception $e) {
    echo "<p style='color:red;'><b>Hata:</b> " . $e->getMessage() . "</p>";
}
?>