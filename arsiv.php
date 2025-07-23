<?php
require("baglanti.php");
session_start();

// Oturum kontrolü
if(!isset($_SESSION['personel'])) {
    header("Location: login.php");
    exit;
}

// Veritabanında arsiv sütunu yoksa ekle
$checkColumn = mysqli_query($baglanti, "SHOW COLUMNS FROM siparis LIKE 'arsiv'");
if(mysqli_num_rows($checkColumn) == 0) {
    mysqli_query($baglanti, "ALTER TABLE siparis ADD COLUMN arsiv TINYINT(1) DEFAULT 0");
}

$mesaj = '';
$hata = '';
$siparisBilgileri = null;

// Sipariş arama işlemi
if(isset($_POST['siparis_no']) && !empty($_POST['siparis_no'])) {
    $siparis_no = mysqli_real_escape_string($baglanti, $_POST['siparis_no']);
    
    $sql = "SELECT * FROM siparis WHERE yilsiparis = '$siparis_no' AND arsiv = 0";
    $sonuc = mysqli_query($baglanti, $sql);
    
    if(mysqli_num_rows($sonuc) > 0) {
        $siparisBilgileri = mysqli_fetch_assoc($sonuc);
    } else {
        $hata = "Sipariş bulunamadı veya zaten arşivlenmiş.";
    }
}

// Arşivleme işlemi
if(isset($_POST['arsivle']) && isset($_POST['siparis_id'])) {
    $siparis_id = mysqli_real_escape_string($baglanti, $_POST['siparis_id']);
    $siparis_no = mysqli_real_escape_string($baglanti, $_POST['siparis_no_arsiv']);
    
    $sql = "UPDATE siparis SET arsiv = 1 WHERE id = '$siparis_id'";
    
    if(mysqli_query($baglanti, $sql)) {
        $mesaj = "Sipariş başarıyla arşivlendi.";
        $siparisBilgileri = null; // Formu temizle
    } else {
        $hata = "Arşivleme sırasında bir hata oluştu: " . mysqli_error($baglanti);
    }
}

// Arşivdeki siparişleri gösterme
$arsivdekiler = [];
if(isset($_GET['arsiv_goster']) && $_GET['arsiv_goster'] == 1) {
    $sql = "SELECT yilsiparis, isim, soyisim, starih FROM siparis WHERE arsiv = 1 ORDER BY id DESC";
    $sonuc = mysqli_query($baglanti, $sql);
    
    while($row = mysqli_fetch_assoc($sonuc)) {
        $arsivdekiler[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>KARATAŞCAM - Sipariş Arşivle</title>
    <meta charset="utf-8">
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 20px;
            background-color: #e6f2f2;
            background-image: linear-gradient(to bottom right, #e6f2f2, #d9f0f0);
        }
        .container { 
            width: 80%; 
            margin: 0 auto; 
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px; 
            border-radius: 5px; 
            box-shadow: 0 0 10px rgba(0,0,0,0.1); 
        }
        h1 { text-align: center; color: #333; margin-bottom: 30px; }
        form { 
            margin: 20px 0; 
            background-color: #f0f8f8;
            padding: 15px;
            border-radius: 8px;
        }
        label { display: block; margin: 10px 0 5px; font-weight: bold; }
        input[type="text"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .btn { 
            padding: 12px 20px; 
            background-color: #4285f4; 
            color: white;
            border: none; 
            cursor: pointer; 
            border-radius: 4px; 
            font-size: 16px; 
            margin-top: 10px;
        }
        .btn-arsiv { background-color: #f44336; }
        .btn:hover { opacity: 0.9; }
        .message { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 12px; text-align: left; }
        th { background-color: #f0f8f8; }
        .siparis-detay { background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .nav-link { margin-left: 10px; text-decoration: none; color: #4285f4; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="image/logo-karatascam.png" alt="KARATAŞCAM Logo" style="height: 60px;">
            <div>
                <a href="index.php" class="nav-link">Ana Sayfa</a>
                <a href="arsiv.php<?php echo isset($_GET['arsiv_goster']) ? '' : '?arsiv_goster=1'; ?>" class="nav-link">
                    <?php echo isset($_GET['arsiv_goster']) ? 'Arşivleme Sayfası' : 'Arşivlenmiş Siparişler'; ?>
                </a>
            </div>
        </div>

        <h1>Sipariş Arşivleme</h1>
        
        <?php if($mesaj): ?>
            <div class="message success"><?php echo $mesaj; ?></div>
        <?php endif; ?>
        
        <?php if($hata): ?>
            <div class="message error"><?php echo $hata; ?></div>
        <?php endif; ?>
        
        <?php if(!isset($_GET['arsiv_goster'])): ?>
            <!-- Sipariş Arama Formu -->
            <form method="post" action="">
                <label for="siparis_no">Arşivlenecek Sipariş Numarası:</label>
                <input type="text" id="siparis_no" name="siparis_no" placeholder="Örn: 2023-A-001" required>
                <button type="submit" class="btn">Siparişi Bul</button>
            </form>
            
            <?php if($siparisBilgileri): ?>
                <!-- Sipariş Bilgileri -->
                <div class="siparis-detay">
                    <h2>Sipariş Detayları</h2>
                    <table>
                        <tr>
                            <th>Sipariş No:</th>
                            <td><?php echo $siparisBilgileri['yilsiparis']; ?></td>
                        </tr>
                        <tr>
                            <th>Müşteri:</th>
                            <td><?php echo $siparisBilgileri['isim'] . ' ' . $siparisBilgileri['soyisim']; ?></td>
                        </tr>
                        <tr>
                            <th>Ürün:</th>
                            <td><?php echo $siparisBilgileri['urun']; ?></td>
                        </tr>
                        <tr>
                            <th>Sipariş Tarihi:</th>
                            <td><?php echo $siparisBilgileri['starih']; ?></td>
                        </tr>
                        <tr>
                            <th>Durum:</th>
                            <td><?php echo $siparisBilgileri['durum']; ?></td>
                        </tr>
                    </table>
                    
                    <!-- Arşivleme Onay Formu -->
                    <form method="post" action="" onsubmit="return confirm('Bu siparişi gerçekten arşivlemek istiyor musunuz? Bu işlem geri alınamaz!');">
                        <input type="hidden" name="siparis_id" value="<?php echo $siparisBilgileri['id']; ?>">
                        <input type="hidden" name="siparis_no_arsiv" value="<?php echo $siparisBilgileri['yilsiparis']; ?>">
                        <button type="submit" name="arsivle" class="btn btn-arsiv">Bu Siparişi Arşivle</button>
                    </form>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <!-- Arşivlenmiş Siparişler Listesi -->
            <h2>Arşivlenmiş Siparişler</h2>
            
            <?php if(empty($arsivdekiler)): ?>
                <p>Arşivde sipariş bulunmamaktadır.</p>
            <?php else: ?>
                <table>
                    <tr>
                        <th>Sipariş No</th>
                        <th>Müşteri</th>
                        <th>Sipariş Tarihi</th>
                    </tr>
                    <?php foreach($arsivdekiler as $siparis): ?>
                    <tr>
                        <td><?php echo $siparis['yilsiparis']; ?></td>
                        <td><?php echo $siparis['isim'] . ' ' . $siparis['soyisim']; ?></td>
                        <td><?php echo $siparis['starih']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html> 