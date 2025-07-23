<?php
require("baglanti.php");
session_start();

// Giriş kontrolü
if (!isset($_SESSION['personel'])) {
    header('Location: login.php');
    exit;
}

// Veritabanı yapısını kontrol et ve güncelle
$tablo_kontrol = mysqli_query($baglanti, "SHOW TABLES LIKE 'musteriler'");
if (mysqli_num_rows($tablo_kontrol) == 0) {
    // Müşteriler tablosu yoksa oluştur
    $musteri_tablo_sql = "CREATE TABLE IF NOT EXISTS musteriler (
        id int(11) NOT NULL AUTO_INCREMENT,
        isim varchar(50) NOT NULL,
        soyisim varchar(50) NOT NULL,
        evtel varchar(20) DEFAULT NULL,
        ceptel varchar(20) DEFAULT NULL,
        email varchar(100) DEFAULT NULL,
        adres text,
        semt varchar(50) DEFAULT NULL,
        olusturma_tarih TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    mysqli_query($baglanti, $musteri_tablo_sql);
}

// Sipariş tablosunda musteri_id alanı var mı kontrol et
$musteri_id_kontrol = mysqli_query($baglanti, "SHOW COLUMNS FROM siparis LIKE 'musteri_id'");
$musteri_id_var = mysqli_num_rows($musteri_id_kontrol) > 0;

if (!$musteri_id_var) {
    // musteri_id alanı yoksa ekle
    $alan_ekle_sql = "ALTER TABLE siparis ADD COLUMN musteri_id int(11) DEFAULT 0 AFTER id";
    mysqli_query($baglanti, $alan_ekle_sql);
}

// Sipariş kaydet işlemi
$mesaj = '';
$hata = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['siparis_kaydet'])) {
    // Form verilerini al
    $isim = mysqli_real_escape_string($baglanti, $_POST['isim']);
    $soyisim = mysqli_real_escape_string($baglanti, $_POST['soyisim']);
    $evtel = mysqli_real_escape_string($baglanti, $_POST['evtel']);
    $ceptel = mysqli_real_escape_string($baglanti, $_POST['ceptel']);
    $email = mysqli_real_escape_string($baglanti, $_POST['email']);
    $adres = mysqli_real_escape_string($baglanti, $_POST['adres']);
    $semt = mysqli_real_escape_string($baglanti, $_POST['semt']);
    $urun = mysqli_real_escape_string($baglanti, $_POST['urun']);
    $aciklama = mysqli_real_escape_string($baglanti, $_POST['aciklama']);
    $satis = mysqli_real_escape_string($baglanti, $_POST['satis']);
    $nt = mysqli_real_escape_string($baglanti, $_POST['nt']);
    $fiyat = floatval($_POST['fiyat']);
    $kapora = floatval($_POST['kapora']);
    $durum = mysqli_real_escape_string($baglanti, $_POST['durum']);
    
    // Sipariş tarihi
    $starih = mysqli_real_escape_string($baglanti, $_POST['starih']);
    $s_time = mysqli_real_escape_string($baglanti, $_POST['s_time']);
    
    // Teslimat tarihi
    $ttarih2 = mysqli_real_escape_string($baglanti, $_POST['ttarih2']);
    $e_time = mysqli_real_escape_string($baglanti, $_POST['e_time']);
    
    // Fiyat hesaplamaları
    $fiyat_sn = $fiyat * 1.18; // KDV dahil fiyat (%18 KDV)
    $kalan = $fiyat - $kapora;
    $kdvkalan = $fiyat_sn - $kapora;
    
    // Sipariş numarası oluştur 
    $yil = date("Y"); // Yıl
    $rastgele = mt_rand(100000, 999999); // 6 haneli rastgele sayı
    
    // Benzersiz sipariş numarası (YIL + RASTGELE SAYI)
    $siparis_no = $yil . "-" . $rastgele;
    
    // Transaction başlat
    mysqli_autocommit($baglanti, FALSE);
    $islem_basarili = true;
    
    // İşlem başarılı olduğunda mesajı saklayacak değişken
    $musteri_mesaj = '';
    $musteri_id = 0;
    
    try {
        // Müşteri var mı kontrol et
        $musteri_kontrol = "SELECT id FROM musteriler WHERE ceptel = '$ceptel' LIMIT 1";
        $musteri_sonuc = mysqli_query($baglanti, $musteri_kontrol);
        
        if ($musteri_sonuc && mysqli_num_rows($musteri_sonuc) > 0) {
            // Müşteri zaten var, ID'sini al
            $musteri_satir = mysqli_fetch_assoc($musteri_sonuc);
            $musteri_id = $musteri_satir['id'];
            $musteri_mesaj = "Mevcut müşteri bilgileri kullanıldı.";
            
            // Müşteri bilgilerini güncelle
            $musteri_guncelle = "UPDATE musteriler SET 
                isim = '$isim', 
                soyisim = '$soyisim', 
                evtel = '$evtel', 
                email = '$email', 
                adres = '$adres', 
                semt = '$semt' 
                WHERE id = $musteri_id";
                
            if (!mysqli_query($baglanti, $musteri_guncelle)) {
                throw new Exception("Müşteri güncellenirken hata oluştu: " . mysqli_error($baglanti));
            }
        } else {
            // Yeni müşteri ekle
            $musteri_ekle = "INSERT INTO musteriler (isim, soyisim, evtel, ceptel, email, adres, semt) 
                VALUES ('$isim', '$soyisim', '$evtel', '$ceptel', '$email', '$adres', '$semt')";
            
            if (mysqli_query($baglanti, $musteri_ekle)) {
                $musteri_id = mysqli_insert_id($baglanti);
                $musteri_mesaj = "Yeni müşteri kaydı oluşturuldu.";
            } else {
                throw new Exception("Müşteri kaydedilirken hata oluştu: " . mysqli_error($baglanti));
            }
        }
        
        // Müşteri işlemi başarılıysa siparişi kaydet
        if ($musteri_id > 0) {
            // Siparişi veritabanına kaydet
            if ($musteri_id_var) {
                // musteri_id alanı varsa
                $sql = "INSERT INTO siparis (
                        musteri_id, yilsiparis, isim, soyisim, evtel, ceptel, email, adres, semt,
                        urun, aciklama, starih, s_time, ttarih2, e_time,
                        satis, nt, fiyat, kalan, fiyat_sn, kdvkalan, kapora, durum
                    ) VALUES (
                        $musteri_id, '$siparis_no', '$isim', '$soyisim', '$evtel', '$ceptel', '$email', '$adres', '$semt',
                        '$urun', '$aciklama', '$starih', '$s_time', '$ttarih2', '$e_time',
                        '$satis', '$nt', $fiyat, $kalan, $fiyat_sn, $kdvkalan, $kapora, '$durum'
                    )";
            } else {
                // musteri_id alanı yoksa eski sorguyu kullan
                $sql = "INSERT INTO siparis (
                        yilsiparis, isim, soyisim, evtel, ceptel, email, adres, semt,
                        urun, aciklama, starih, s_time, ttarih2, e_time,
                        satis, nt, fiyat, kalan, fiyat_sn, kdvkalan, kapora, durum
                    ) VALUES (
                        '$siparis_no', '$isim', '$soyisim', '$evtel', '$ceptel', '$email', '$adres', '$semt',
                        '$urun', '$aciklama', '$starih', '$s_time', '$ttarih2', '$e_time',
                        '$satis', '$nt', $fiyat, $kalan, $fiyat_sn, $kdvkalan, $kapora, '$durum'
                    )";
            }
                
            if (!mysqli_query($baglanti, $sql)) {
                throw new Exception("Sipariş kaydedilirken hata oluştu: " . mysqli_error($baglanti));
            }
            
            // İşlem başarılı
            $mesaj = "Sipariş kaydedildi. Sipariş Numarası: $siparis_no. $musteri_mesaj";
            
            // Değişiklikleri kaydet
            mysqli_commit($baglanti);
            
            // Sipariş başarıyla kaydedildiğinde formu temizle
            $_POST = array();
        }
    } catch (Exception $e) {
        // Hata durumunda değişiklikleri geri al
        mysqli_rollback($baglanti);
        $islem_basarili = false;
        $hata = $e->getMessage();
    }
    
    // İşlem sonunda otomatik commit'i tekrar aktif et
    mysqli_autocommit($baglanti, TRUE);
}

// Satış personeli listesini çek
$personel_sql = "SELECT * FROM kisiler ORDER BY adsoyad";
$personel_sonuc = mysqli_query($baglanti, $personel_sql);

// PHP tarafında Türkçe tarih ayarları
setlocale(LC_TIME, 'tr_TR.UTF-8', 'tr_TR', 'tr', 'turkish');

// Zaman dilimini Türkiye olarak ayarla
date_default_timezone_set('Europe/Istanbul');

// Tarihleri Türkçe formata çeviren fonksiyon
function formatTarihTr($tarih) {
    if (empty($tarih)) return '';
    
    $aylar = array(
        'January' => 'Ocak',
        'February' => 'Şubat',
        'March' => 'Mart',
        'April' => 'Nisan',
        'May' => 'Mayıs',
        'June' => 'Haziran',
        'July' => 'Temmuz',
        'August' => 'Ağustos',
        'September' => 'Eylül',
        'October' => 'Ekim',
        'November' => 'Kasım',
        'December' => 'Aralık'
    );
    
    $date = new DateTime($tarih);
    $formatlanmisTarih = $date->format('d F Y');
    
    foreach ($aylar as $en => $tr) {
        $formatlanmisTarih = str_replace($en, $tr, $formatlanmisTarih);
    }
    
    return $formatlanmisTarih;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>KARATAŞCAM - Sipariş Girişi</title>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/tr.js"></script>
    <style type="text/css">
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
        h2 {
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        table td, table th {
            padding: 8px;
            vertical-align: top;
        }
        
        input[type=text], input[type=number], input[type=email], input[type=tel], textarea, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        textarea {
            height: 100px;
            resize: vertical;
        }
        
        input[type=submit], .button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        
        input[type=submit]:hover, .button:hover {
            background-color: #45a049;
        }
        
        .success {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        
        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        
        .form-section {
            background-color: #f9f9f9;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #e9e9e9;
        }
        
        .form-section h3 {
            margin-top: 0;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        
        /* Tarih seçicileri için özel stiller */
        input[type="date"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: Arial, sans-serif;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Yeni Sipariş Girişi</h2>
        
        <?php if ($mesaj): ?>
            <div class="success"><?php echo $mesaj; ?></div>
        <?php endif; ?>
        
        <?php if ($hata): ?>
            <div class="error"><?php echo $hata; ?></div>
        <?php endif; ?>
        
        <form method="post" action="">
            <div class="form-section">
                <h3>Müşteri Bilgileri</h3>
                <table>
                    <tr>
                        <td width="20%"><label for="isim">Ad:</label></td>
                        <td><input type="text" id="isim" name="isim" value="<?php echo isset($_POST['isim']) ? $_POST['isim'] : ''; ?>" required></td>
                        <td width="20%"><label for="soyisim">Soyad:</label></td>
                        <td><input type="text" id="soyisim" name="soyisim" value="<?php echo isset($_POST['soyisim']) ? $_POST['soyisim'] : ''; ?>" required></td>
                    </tr>
                    <tr>
                        <td><label for="evtel">Ev Telefonu:</label></td>
                        <td><input type="tel" id="evtel" name="evtel" value="<?php echo isset($_POST['evtel']) ? $_POST['evtel'] : ''; ?>"></td>
                        <td><label for="ceptel">Cep Telefonu:</label></td>
                        <td><input type="tel" id="ceptel" name="ceptel" value="<?php echo isset($_POST['ceptel']) ? $_POST['ceptel'] : ''; ?>" required></td>
                    </tr>
                    <tr>
                        <td><label for="email">E-mail:</label></td>
                        <td colspan="3"><input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>"></td>
                    </tr>
                    <tr>
                        <td><label for="adres">Adres:</label></td>
                        <td colspan="3"><textarea id="adres" name="adres" required><?php echo isset($_POST['adres']) ? $_POST['adres'] : ''; ?></textarea></td>
                    </tr>
                    <tr>
                        <td><label for="semt">Semt:</label></td>
                        <td colspan="3"><input type="text" id="semt" name="semt" value="<?php echo isset($_POST['semt']) ? $_POST['semt'] : ''; ?>" required></td>
                    </tr>
                </table>
            </div>
            
            <div class="form-section">
                <h3>Ürün Bilgileri</h3>
                <table>
                    <tr>
                        <td width="20%"><label for="urun">Ürün:</label></td>
                        <td colspan="3">
                            <select id="urun" name="urun" required>
                                <option value="">Seçiniz</option>
                                <option value="Ayna" <?php echo (isset($_POST['urun']) && $_POST['urun'] == 'Ayna') ? 'selected' : ''; ?>>Ayna</option>
                                <option value="Cam" <?php echo (isset($_POST['urun']) && $_POST['urun'] == 'Cam') ? 'selected' : ''; ?>>Cam</option>
                                <option value="Duşakabin" <?php echo (isset($_POST['urun']) && $_POST['urun'] == 'Duşakabin') ? 'selected' : ''; ?>>Duşakabin</option>
                                <option value="Cam Masa" <?php echo (isset($_POST['urun']) && $_POST['urun'] == 'Cam Masa') ? 'selected' : ''; ?>>Cam Masa</option>
                                <option value="Cam Kapı" <?php echo (isset($_POST['urun']) && $_POST['urun'] == 'Cam Kapı') ? 'selected' : ''; ?>>Cam Kapı</option>
                                <option value="Cam Merdiven" <?php echo (isset($_POST['urun']) && $_POST['urun'] == 'Cam Merdiven') ? 'selected' : ''; ?>>Cam Merdiven</option>
                                <option value="Cam Korkuluk" <?php echo (isset($_POST['urun']) && $_POST['urun'] == 'Cam Korkuluk') ? 'selected' : ''; ?>>Cam Korkuluk</option>
                                <option value="Cam Çatı" <?php echo (isset($_POST['urun']) && $_POST['urun'] == 'Cam Çatı') ? 'selected' : ''; ?>>Cam Çatı</option>
                                <option value="Diğer" <?php echo (isset($_POST['urun']) && $_POST['urun'] == 'Diğer') ? 'selected' : ''; ?>>Diğer</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="aciklama">Sipariş Ürünü ve Ölçüsü:</label></td>
                        <td colspan="3"><textarea id="aciklama" name="aciklama" required><?php echo isset($_POST['aciklama']) ? $_POST['aciklama'] : ''; ?></textarea></td>
                    </tr>
                </table>
            </div>
            
            <div class="form-section">
                <h3>Sipariş ve Teslimat Bilgileri</h3>
                <table>
                    <tr>
                        <td width="20%"><label for="starih">Sipariş Tarihi:</label></td>
                        <td><input type="date" id="starih" name="starih" value="<?php echo isset($_POST['starih']) ? $_POST['starih'] : date('Y-m-d'); ?>" required></td>
                        <td width="20%"><label for="s_time">Saati:</label></td>
                        <td><input type="time" id="s_time" name="s_time" value="<?php echo isset($_POST['s_time']) ? $_POST['s_time'] : date('H:i'); ?>" required></td>
                    </tr>
                    <tr>
                        <td><label for="ttarih2">Teslimat Tarihi:</label></td>
                        <td><input type="date" id="ttarih2" name="ttarih2" value="<?php echo isset($_POST['ttarih2']) ? $_POST['ttarih2'] : ''; ?>" required></td>
                        <td><label for="e_time">Saati:</label></td>
                        <td><input type="time" id="e_time" name="e_time" value="<?php echo isset($_POST['e_time']) ? $_POST['e_time'] : ''; ?>" required></td>
                    </tr>
                    <tr>
                        <td><label for="satis">Sipariş Alan:</label></td>
                        <td colspan="3">
                            <select id="satis" name="satis" required>
                                <option value="">Seçiniz</option>
                                <?php
                                while ($personel = mysqli_fetch_assoc($personel_sonuc)) {
                                    $selected = (isset($_POST['satis']) && $_POST['satis'] == $personel['adsoyad']) ? 'selected' : '';
                                    echo "<option value='{$personel['adsoyad']}' $selected>{$personel['adsoyad']}</option>";
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="form-section">
                <h3>Ödeme ve Durum Bilgileri</h3>
                <table>
                    <tr>
                        <td width="20%"><label for="fiyat">Fiyat (KDV'siz):</label></td>
                        <td><input type="number" id="fiyat" name="fiyat" step="0.01" value="<?php echo isset($_POST['fiyat']) ? $_POST['fiyat'] : ''; ?>" required></td>
                        <td width="20%"><label for="kapora">Kapora:</label></td>
                        <td><input type="number" id="kapora" name="kapora" step="0.01" value="<?php echo isset($_POST['kapora']) ? $_POST['kapora'] : '0.00'; ?>" required></td>
                    </tr>
                    <tr>
                        <td><label for="durum">Durum:</label></td>
                        <td colspan="3">
                            <select id="durum" name="durum" required>
                                <option value="Üretim" <?php echo (isset($_POST['durum']) && $_POST['durum'] == 'Üretim') ? 'selected' : ''; ?>>Üretim</option>
                                <option value="Hazır" <?php echo (isset($_POST['durum']) && $_POST['durum'] == 'Hazır') ? 'selected' : ''; ?>>Hazır</option>
                                <option value="Tamamlandı" <?php echo (isset($_POST['durum']) && $_POST['durum'] == 'Tamamlandı') ? 'selected' : ''; ?>>Tamamlandı</option>
                                <option value="Tamamlanmadı!" <?php echo (isset($_POST['durum']) && $_POST['durum'] == 'Tamamlanmadı!') ? 'selected' : ''; ?>>Tamamlanmadı!</option>
                                <option value="İptal" <?php echo (isset($_POST['durum']) && $_POST['durum'] == 'İptal') ? 'selected' : ''; ?>>İptal</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="nt">Notlar:</label></td>
                        <td colspan="3"><textarea id="nt" name="nt"><?php echo isset($_POST['nt']) ? $_POST['nt'] : ''; ?></textarea></td>
                    </tr>
                </table>
            </div>
            
            <div style="text-align: center; margin-top: 20px;">
                <input type="submit" name="siparis_kaydet" value="Siparişi Kaydet">
                <a href="index.php" class="button" style="margin-left: 10px; text-decoration: none; display: inline-block;">Ana Sayfa</a>
            </div>
        </form>
    </div>
    
    <script>
        $(function() {
            // Tarih seçicileri aktifleştir
            $("#starih").datepicker({ dateFormat: 'yy-mm-dd' });
            $("#ttarih2").datepicker({ dateFormat: 'yy-mm-dd' });
            
            // Fiyat hesaplamaları
            $("#fiyat, #kapora").on('input', function() {
                var fiyat = parseFloat($("#fiyat").val()) || 0;
                var kapora = parseFloat($("#kapora").val()) || 0;
                var fiyatKdv = fiyat * 1.18;
                var kalan = fiyat - kapora;
                var kdvKalan = fiyatKdv - kapora;
                
                // Kullanıcıya göstermek istersen burada alanlar ekleyebilirsin
                console.log("KDV'li Fiyat: " + fiyatKdv.toFixed(2));
                console.log("Kalan: " + kalan.toFixed(2));
                console.log("KDV'li Kalan: " + kdvKalan.toFixed(2));
            });
        });
        
        // Moment.js Türkçe ayarları
        moment.locale('tr');
        
        document.addEventListener('DOMContentLoaded', function() {
            // Tüm tarih giriş alanlarını bul
            const tarihInputlar = document.querySelectorAll('input[type="date"]');
            
            // Her tarih giriş alanı için Türkçe ayarları uygula
            tarihInputlar.forEach(function(input) {
                // Tarayıcı dil ayarını değiştir
                input.setAttribute('lang', 'tr');
                
                // Placeholder text ekle
                if (!input.getAttribute('placeholder')) {
                    input.setAttribute('placeholder', 'GG/AA/YYYY');
                }
                
                // Bugünün tarihini Türkçe format ile göster (isteğe bağlı)
                if (input.value === '') {
                    const bugun = new Date();
                    input.valueAsDate = bugun;
                }
            });
        });
    </script>
</body>
</html>
