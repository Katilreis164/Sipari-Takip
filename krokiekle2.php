<?php
require("baglanti.php");
session_start();

// Giriş kontrolü
if (!isset($_SESSION['personel'])) {
    header('Location: login.php');
    exit;
}

// Sipariş numarasını al
$siparisNo = isset($_GET['yilsiparis']) ? $_GET['yilsiparis'] : '';

// Dosya yüklendiğinde çalışacak kod
if(isset($_POST['button3'])) {
    // Dosya yükleme klasörü kontrolü
    $target_dir = "kroki/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    // Dosya adını güvenli hale getir
    $fileName = basename($_FILES["dosya1"]["name"]);
    $safeFileName = preg_replace("/[^A-Za-z0-9_\-\.]/", '', $fileName);
    $safeFileName = time() . '_' . $safeFileName;
    $target_file = $target_dir . $safeFileName;
    
    // Sadece izin verilen uzantılara izin ver
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    if($fileType != "jpg" && $fileType != "png" && $fileType != "jpeg" && $fileType != "gif" && $fileType != "pdf") {
        echo "Üzgünüz, sadece JPG, JPEG, PNG, GIF ve PDF dosyalarına izin verilmektedir.";
        exit;
    }
    
    // Dosyayı yükle
    if (move_uploaded_file($_FILES["dosya1"]["tmp_name"], $target_file)) {
        // Veritabanına kaydet
        $sql = "INSERT INTO krokiler (siparisno, kroki) VALUES (?, ?)";
        $stmt = mysqli_prepare($baglanti, $sql);
        mysqli_stmt_bind_param($stmt, 'ss', $siparisNo, $safeFileName);
        
        if (mysqli_stmt_execute($stmt)) {
            echo "<div style='color:green; font-weight:bold;'>Kroki başarıyla yüklendi.</div>";
        } else {
            echo "<div style='color:red; font-weight:bold;'>Veritabanı hatası: " . mysqli_error($baglanti) . "</div>";
        }
    } else {
        echo "<div style='color:red; font-weight:bold;'>Dosya yükleme hatası.</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kroki Yükleme</title>
    <style type="text/css">
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h2 {
            color: #333;
        }
        .container {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Adres Krokisi Yükle - <?php echo $siparisNo; ?></h2>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="file" name="dosya1" id="dosya1" /><br><br>
            <input type="submit" name="button3" id="button3" value="Yükle" />
        </form>
        <p><a href="javascript:window.close();">Kapat</a></p>
    </div>
</body>
</html>
