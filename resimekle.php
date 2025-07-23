<?php
require("baglanti.php");
session_start();

// Giriş kontrolü
if (!isset($_SESSION['personel'])) {
    header('Location: login.php');
    exit;
}

// Sipariş numarasını al
$siparisNo = isset($_POST['siparis_no2']) ? $_POST['siparis_no2'] : '';

// Resim yükleme klasörü kontrolü
if (!file_exists("resim/")) {
    mkdir("resim/", 0777, true);
}

// Kroki yükleme klasörü kontrolü
if (!file_exists("kroki/")) {
    mkdir("kroki/", 0777, true);
}

// Dosya yüklendi mi?
if (isset($_POST['button4']) && !empty($siparisNo)) {
    // Sipariş bilgilerini getir
    $sql = "SELECT * FROM siparis WHERE yilsiparis = ?";
    $stmt = mysqli_prepare($baglanti, $sql);
    mysqli_stmt_bind_param($stmt, 's', $siparisNo);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $siparis = mysqli_fetch_assoc($result);
    } else {
        echo "<script>alert('Sipariş bulunamadı!');</script>";
        exit;
    }
}

// Ürün resmi yükleme işlemi
if (isset($_POST['button2'])) {
    if (!empty($_FILES['dosya']['name'])) {
        // Dosya adını güvenli hale getir
        $fileName = basename($_FILES["dosya"]["name"]);
        $safeFileName = preg_replace("/[^A-Za-z0-9_\-\.]/", '', $fileName);
        $safeFileName = time() . '_' . $safeFileName;
        $target_file = "resim/" . $safeFileName;
        
        // Sadece resim uzantılarına izin ver
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            echo "<script>alert('Üzgünüz, sadece JPG, JPEG, PNG ve GIF dosyalarına izin verilmektedir.');</script>";
        } else {
            // Dosyayı yükle
            if (move_uploaded_file($_FILES["dosya"]["tmp_name"], $target_file)) {
                // Veritabanına kaydet
                $sql = "INSERT INTO resimler (siparisno, resim) VALUES (?, ?)";
                $stmt = mysqli_prepare($baglanti, $sql);
                mysqli_stmt_bind_param($stmt, 'ss', $siparisNo, $safeFileName);
                
                if (mysqli_stmt_execute($stmt)) {
                    echo "<script>alert('Ürün resmi başarıyla yüklendi.');</script>";
                } else {
                    echo "<script>alert('Veritabanı hatası: " . mysqli_error($baglanti) . "');</script>";
                }
            } else {
                echo "<script>alert('Dosya yükleme hatası.');</script>";
            }
        }
    }
}

// Kroki yükleme işlemi
if (isset($_POST['button3'])) {
    if (!empty($_FILES['dosya1']['name'])) {
        // Dosya adını güvenli hale getir
        $fileName = basename($_FILES["dosya1"]["name"]);
        $safeFileName = preg_replace("/[^A-Za-z0-9_\-\.]/", '', $fileName);
        $safeFileName = time() . '_' . $safeFileName;
        $target_file = "kroki/" . $safeFileName;
        
        // Sadece izin verilen uzantılara izin ver
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if($fileType != "jpg" && $fileType != "png" && $fileType != "jpeg" && $fileType != "gif" && $fileType != "pdf") {
            echo "<script>alert('Üzgünüz, sadece JPG, JPEG, PNG, GIF ve PDF dosyalarına izin verilmektedir.');</script>";
        } else {
            // Dosyayı yükle
            if (move_uploaded_file($_FILES["dosya1"]["tmp_name"], $target_file)) {
                // Veritabanına kaydet
                $sql = "INSERT INTO krokiler (siparisno, kroki) VALUES (?, ?)";
                $stmt = mysqli_prepare($baglanti, $sql);
                mysqli_stmt_bind_param($stmt, 'ss', $siparisNo, $safeFileName);
                
                if (mysqli_stmt_execute($stmt)) {
                    echo "<script>alert('Adres krokisi başarıyla yüklendi.');</script>";
                } else {
                    echo "<script>alert('Veritabanı hatası: " . mysqli_error($baglanti) . "');</script>";
                }
            } else {
                echo "<script>alert('Dosya yükleme hatası.');</script>";
            }
        }
    }
}

// Mevcut resimleri getir
$sql = "SELECT * FROM resimler WHERE siparisno = ?";
$stmt = mysqli_prepare($baglanti, $sql);
mysqli_stmt_bind_param($stmt, 's', $siparisNo);
mysqli_stmt_execute($stmt);
$resim_result = mysqli_stmt_get_result($stmt);

// Mevcut krokileri getir
$sql = "SELECT * FROM krokiler WHERE siparisno = ?";
$stmt = mysqli_prepare($baglanti, $sql);
mysqli_stmt_bind_param($stmt, 's', $siparisNo);
mysqli_stmt_execute($stmt);
$kroki_result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Karataşcam Resim ve Kroki Yükleme</title>
    <style type="text/css">
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 0 auto;
        }
        
        h2 {
            color: #333;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        
        .form-section {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }
        
        .gallery {
            display: flex;
            flex-wrap: wrap;
            margin-top: 10px;
        }
        
        .gallery-item {
            margin: 5px;
            border: 1px solid #ddd;
            padding: 5px;
            background-color: white;
            border-radius: 3px;
            text-align: center;
        }
        
        .gallery-item img {
            max-width: 150px;
            max-height: 150px;
        }
        
        .file-name {
            font-size: 12px;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 150px;
            white-space: nowrap;
        }
        
        input[type=file], input[type=submit] {
            margin: 10px 0;
        }
        
        input[type=submit] {
            background-color: #4CAF50;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        input[type=submit]:hover {
            background-color: #45a049;
        }
        
        .no-files {
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Resim ve Kroki Yükleme - Sipariş No: <?php echo $siparisNo; ?></h2>
        
        <div class="form-section">
            <h3>Ürün Resimleri</h3>
            <form action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="siparis_no2" value="<?php echo $siparisNo; ?>">
                <input type="file" name="dosya" id="dosya">
                <br>
                <input type="submit" name="button2" value="Ürün Resmi Yükle">
            </form>
            
            <h4>Yüklü Resimler</h4>
            <div class="gallery">
                <?php
                if (mysqli_num_rows($resim_result) > 0) {
                    while ($row = mysqli_fetch_assoc($resim_result)) {
                        echo '<div class="gallery-item">';
                        echo '<a href="resim/' . $row['resim'] . '" target="_blank">';
                        echo '<img src="resim/' . $row['resim'] . '" alt="Ürün Resmi">';
                        echo '</a>';
                        echo '<div class="file-name">' . $row['resim'] . '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p class="no-files">Henüz yüklenmiş ürün resmi bulunmamaktadır.</p>';
                }
                ?>
            </div>
        </div>
        
        <div class="form-section">
            <h3>Adres Krokileri</h3>
            <form action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="siparis_no2" value="<?php echo $siparisNo; ?>">
                <input type="file" name="dosya1" id="dosya1">
                <br>
                <input type="submit" name="button3" value="Adres Krokisi Yükle">
            </form>
            
            <h4>Yüklü Krokiler</h4>
            <div class="gallery">
                <?php
                if (mysqli_num_rows($kroki_result) > 0) {
                    while ($row = mysqli_fetch_assoc($kroki_result)) {
                        $ext = strtolower(pathinfo('kroki/' . $row['kroki'], PATHINFO_EXTENSION));
                        
                        echo '<div class="gallery-item">';
                        echo '<a href="kroki/' . $row['kroki'] . '" target="_blank">';
                        
                        if ($ext == 'pdf') {
                            // PDF dosyası için ikon göster
                            echo '<div style="width:150px;height:150px;display:flex;align-items:center;justify-content:center;background:#f5f5f5">';
                            echo '<span style="font-size:36px;">PDF</span>';
                            echo '</div>';
                        } else {
                            // Resim dosyası ise resmi göster
                            echo '<img src="kroki/' . $row['kroki'] . '" alt="Kroki">';
                        }
                        
                        echo '</a>';
                        echo '<div class="file-name">' . $row['kroki'] . '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p class="no-files">Henüz yüklenmiş adres krokisi bulunmamaktadır.</p>';
                }
                ?>
            </div>
        </div>
        
        <p><a href="aramasonuc.php?yilsiparis=<?php echo $siparisNo; ?>">Sipariş Detayları Sayfasına Dön</a></p>
    </div>
</body>
</html>
