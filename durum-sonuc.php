<?php 
require("baglanti.php");
session_start();

$kayit_sayisi = 10; //Sayfada gösterilecek toplam kayıt sayısı
if(isset($_GET['goster'])){
    $kayit_goster = $_GET['goster'];
    $ileri = $_GET['goster'] + $kayit_sayisi;
    $geri = $_GET['goster'] - $kayit_sayisi;
}
else {
    $kayit_goster = 0;
    $ileri = $kayit_sayisi;
    $geri = 0;
}

// Durum filtreleme
if(isset($_GET['durum'])){
    try {
        $durum = "%" . $_GET['durum'] . "%";
        $durum2 = $_GET['durum'];
        $_SESSION['durum2'] = $durum2;
        $_SESSION['durum'] = $durum2;
        $deg = "&durum=";

        $deger_sql = sprintf(
            "SELECT * FROM siparis WHERE durum LIKE '%s' LIMIT %d, %d",
            mysqli_real_escape_string($baglanti, $durum),
            $kayit_goster,
            $kayit_sayisi
        );
        $deger_sonuc = mysqli_query($baglanti, $deger_sql);
        if (!$deger_sonuc) {
            throw new Exception("Sorgu çalıştırılamadı: " . mysqli_error($baglanti));
        }
        $deger_toplam = mysqli_num_rows($deger_sonuc);

        $tks_sql = sprintf(
            "SELECT * FROM siparis WHERE durum LIKE '%s'",
            mysqli_real_escape_string($baglanti, $durum)
        );
        $tks_result = mysqli_query($baglanti, $tks_sql);
        if (!$tks_result) {
            throw new Exception("Toplam kayıt sayısı hesaplanamadı: " . mysqli_error($baglanti));
        }
        $tks_toplam = mysqli_num_rows($tks_result);
    } catch (Exception $e) {
        // Hata durumunda varsayılan değerler atama
        $deger_sonuc = false;
        $deger_toplam = 0;
        $tks_toplam = 0;
        
        // Hata mesajını gösterme (isteğe bağlı)
        // echo "<div class='error'>" . $e->getMessage() . "</div>";
    }
}
// Session'dan durum bilgisini al
elseif(isset($_SESSION['durum'])){
    try {
        $durum = "%" . $_SESSION['durum'] . "%";
        $durum2 = $_SESSION['durum'];
        $_SESSION['durum'] = $durum2;
        $deg = "&durum=";

        $deger_sql = sprintf(
            "SELECT * FROM siparis WHERE durum LIKE '%s' LIMIT %d, %d",
            mysqli_real_escape_string($baglanti, $durum),
            $kayit_goster,
            $kayit_sayisi
        );
        $deger_sonuc = mysqli_query($baglanti, $deger_sql);
        if (!$deger_sonuc) {
            throw new Exception("Sorgu çalıştırılamadı: " . mysqli_error($baglanti));
        }
        $deger_toplam = mysqli_num_rows($deger_sonuc);

        $tks_sql = sprintf(
            "SELECT * FROM siparis WHERE durum LIKE '%s'",
            mysqli_real_escape_string($baglanti, $durum)
        );
        $tks_result = mysqli_query($baglanti, $tks_sql);
        if (!$tks_result) {
            throw new Exception("Toplam kayıt sayısı hesaplanamadı: " . mysqli_error($baglanti));
        }
        $tks_toplam = mysqli_num_rows($tks_result);
    } catch (Exception $e) {
        // Hata durumunda varsayılan değerler atama
        $deger_sonuc = false;
        $deger_toplam = 0;
        $tks_toplam = 0;
    }
}
// İsim veya soyisim araması
elseif(isset($_POST['soyisim'])){
    try {
        $soyisim = "%" . $_POST['soyisim'] . "%";

        $deger_sql = sprintf(
            "SELECT * FROM siparis WHERE isim LIKE '%s' OR soyisim LIKE '%s' LIMIT %d, %d",
            mysqli_real_escape_string($baglanti, $soyisim),
            mysqli_real_escape_string($baglanti, $soyisim),
            $kayit_goster,
            $kayit_sayisi
        );
        $deger_sonuc = mysqli_query($baglanti, $deger_sql);
        if (!$deger_sonuc) {
            throw new Exception("Sorgu çalıştırılamadı: " . mysqli_error($baglanti));
        }
        $deger_toplam = mysqli_num_rows($deger_sonuc);
        
        $tks_sql = sprintf(
            "SELECT * FROM siparis WHERE isim LIKE '%s' OR soyisim LIKE '%s'",
            mysqli_real_escape_string($baglanti, $soyisim),
            mysqli_real_escape_string($baglanti, $soyisim)
        );
        $tks_result = mysqli_query($baglanti, $tks_sql);
        if (!$tks_result) {
            throw new Exception("Toplam kayıt sayısı hesaplanamadı: " . mysqli_error($baglanti));
        }
        $tks_toplam = mysqli_num_rows($tks_result);
    } catch (Exception $e) {
        // Hata durumunda varsayılan değerler atama
        $deger_sonuc = false;
        $deger_toplam = 0;
        $tks_toplam = 0;
    }
}
// Hiçbir filtreleme veya arama yapılmadıysa, tüm siparişleri getir
else {
    try {
        $deger_sql = sprintf(
            "SELECT * FROM siparis LIMIT %d, %d",
            $kayit_goster,
            $kayit_sayisi
        );
        $deger_sonuc = mysqli_query($baglanti, $deger_sql);
        if (!$deger_sonuc) {
            throw new Exception("Sorgu çalıştırılamadı: " . mysqli_error($baglanti));
        }
        $deger_toplam = mysqli_num_rows($deger_sonuc);
        
        $tks_sql = "SELECT COUNT(*) AS toplam FROM siparis";
        $tks_result = mysqli_query($baglanti, $tks_sql);
        if (!$tks_result) {
            throw new Exception("Toplam kayıt sayısı hesaplanamadı: " . mysqli_error($baglanti));
        }
        $tks_row = mysqli_fetch_assoc($tks_result);
        $tks_toplam = $tks_row['toplam'];
    } catch (Exception $e) {
        // Hata durumunda varsayılan değerler atama
        $deger_sonuc = false;
        $deger_toplam = 0;
        $tks_toplam = 0;
        
        // Hata mesajını gösterme (isteğe bağlı)
        // echo "<div class='error'>" . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Karataşcam Siparis Sistemi</title>
    <style type="text/css">
    #banner {
        height: 100px;
        width: 940px;
        margin-right: auto;
        margin-left: auto;
        padding-top: 20px;
        padding-left: 20px;
    }
    body {
        background-attachment: fixed;
        background-image: url(image/bg.png);
        background-repeat: no-repeat;    
    }
    #content {
        webkit-border-radius: 25px 25px 20px 20px;
        -moz-border-radius: 25px 25px 20px 20px;
        border-radius: 25px 25px 20px 20px    ;
        background-color:#FFFFFF;
        min-height: 350px;
        width: 940px;
        margin-right: auto;
        margin-left: auto;
        padding: 10px;
    }
    #footer {
        width: 940px;
        margin-right: auto;
        margin-left: auto;
        padding-left: 20px;
    }

    #yetkinlik {
        float: right;
        height: 40px;
        width: 185px;
        padding-top: 10px;
    }


    .yazi {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 14px;
        color: #000;
        text-decoration: none;
    }
    .yazib a {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 15px;
        color: #000;
        text-decoration: none;
    }
    .yazib a :hover{
        font-family: Arial, Helvetica, sans-serif;
        font-size: 15px;
        color: #900;
        text-decoration: none;
    }
    .footter {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
        text-decoration: none;
        line-height: 15px;
    }
    .baslik {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 13px;
        font-weight: bold;
        color: #000;
        text-decoration: none;
    }
    .style2 {font-family: Arial, Helvetica, sans-serif}
    </style>
</head>

<body>
<div id="banner">
  <p><img src="image/logo-karatascam.png" width="408" height="55" /></p>
</div>
<div id="content">
 
  <p class="yazi"><span class="baslik">Toplam Kayıt Sayısı:</span> <?php echo $tks_toplam; ?></p>
  <p class="yazi"><span class="baslik">Kayıt Aralığı:</span> <?php echo $kayit_goster + 1 ?> - <?php echo $ileri ?></p>
  <table width="900" border="0" align="center" cellpadding="5" cellspacing="0">
    <tr class="baslik">
    <th width="65" nowrap="nowrap" scope="row">Sipariş No</th>
    <th width="87" nowrap="nowrap">Ad</th>
    <th width="72" nowrap="nowrap">Soyad</th>
    <th width="55" nowrap="nowrap">Ürün</th>
    <th width="215" nowrap="nowrap">Siparis ve Ürün Özellikleri</th>
    <th width="108" nowrap="nowrap">Siparis Alan</a></th>
    <th width="86" nowrap="nowrap">Durum</th>
    <th width="87" nowrap="nowrap">Kalan</th>
    <th width="45" nowrap="nowrap">&nbsp;</th>
  </tr>
   <?php 
  if ($tks_toplam < 10){
    $kayit_sayisi = $tks_toplam;
  } elseif($tks_toplam == 10){
    $kayit_sayisi = 10;
  } elseif($ileri > $tks_toplam){
    $a = $tks_toplam % 10; 
    $kayit_sayisi = $a;
  }
            
  if($tks_toplam > 0){
    for($i=0; $i<$kayit_sayisi; $i++){
      $deger_satir = mysqli_fetch_assoc($deger_sonuc);
  ?>
  <tr class="baslik">
    <td height="1" colspan="8" class="yazib" scope="row"><hr /></td>
    <td height="1" class="yazib" scope="row">&nbsp;</td>
    </tr>
  
  <tr bgcolor="#F6FBFA">
    <th class="yazi" scope="row"><?php echo $deger_satir['yilsiparis'] ?></th>
    <td class="yazi"><?php echo $deger_satir['isim'] ?></td>
    <td class="yazi"><?php echo $deger_satir['soyisim'] ?></td>
    <td class="yazi"><?php echo $deger_satir['urun'] ?></td>
    <td class="yazi"><?php echo $deger_satir['aciklama'] ?></td>
    <td class="yazi"><?php echo $deger_satir['satis'] ?></td>
     <td class="yazi"><?php echo $deger_satir['durum'] ?></td>
     <td class="yazi"><?php echo $deger_satir['kalan'] ?></td>
    <td><a href="ayrinti.php?yilsiparis=<?php echo $deger_satir['yilsiparis'] ?>">Ayrıntı</a></td>
    <td><a href="guncelle.php?siparis_no3=<?php echo $deger_satir['yilsiparis'] ?>">Güncelle</a></td>
  </tr>
  <?php }
  } // tks_toplam > 0 if'inin kapanışı
  ?>
  <tr>
    <th scope="row">&nbsp;</th>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
 
</table>

<?php if($tks_toplam == 0): ?>
<div style="text-align:center; padding:20px; margin:20px 0; background-color:#f8d7da; color:#721c24; border:1px solid #f5c6cb; border-radius:5px;">
  <h3>Kayıt Bulunamadı</h3>
  <p>Arama kriterlerinize uygun kayıt bulunamadı veya veritabanında kayıt yok.</p>
  <p><a href="index.php" style="color:#721c24; font-weight:bold;">Ana Sayfaya Dön</a></p>
</div>
<?php else: ?>

<?php if($ileri > $kayit_sayisi and $kayit_goster > 0): ?>
    <a href="durum-sonuc.php?goster=<?php echo $geri; ?><?php echo isset($deg) ? $deg.$durum2 : ''; ?>">önceki</a>
<?php endif; ?>
     | 
<?php if($ileri < $tks_toplam): ?>
     <a href="durum-sonuc.php?goster=<?php echo $ileri; ?><?php echo isset($deg) ? $deg.$durum2 : ''; ?>">sonraki</a>
<?php endif; ?>

<?php endif; // tks_toplam == 0 ?>
</div>
  <div id="footer">
    <div id="yetkinlik">
        <img src="image/yetkinlikler.png" />
    </div>
  </div>
  <p class="footter"><strong>Karataş Ayna Kristal Cam Mob. İnş. Nak. San. ve Tic. Ltd. Şti <br />
    </strong><strong><br />
      </strong>Çalım Sok. No:19 Siteler - Ankara / Türkiye <br />
    T: +90 312 348 9162 |  
    F: +90 312 348 7078<br />
    info@karatascam.com.tr |
  www.karatascam.com.tr</p>
<p>&nbsp;</p></div>

</body>
</html>