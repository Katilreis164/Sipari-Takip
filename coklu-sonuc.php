<?php 
require("baglanti.php");
session_start();

if (!isset($_SESSION['personel'])) {
    header('Location: login.php');
    exit;
}

// Kayıt Gezinti Sistemi
$kayit_sayisi = 10;

if (isset($_GET['goster'])) {
    $kayit_goster = $_GET['goster'];
    $ileri = $_GET['goster'] + $kayit_sayisi;
    $geri = $_GET['goster'] - $kayit_sayisi;
} else {
    $kayit_goster = 0;
    $ileri = $kayit_sayisi;
    $geri = 0;
}

//satis ilk gelis
if(isset( $_GET['satis'])){
    $satis = "%".$_GET['satis']."%";
    $satis2= $_GET['satis'];
    $_SESSION['satis2'] = $satis2;

    $satis2 = preg_replace('/\s\s+/', '+', $satis2);

    $_SESSION['satis'] =$satis2;
    $deg= "&satis=";

    $deger_sql = sprintf(
        "SELECT *
         FROM siparis
         WHERE satis LIKE '%s' 
         LIMIT $kayit_goster, $kayit_sayisi",
        mysqli_real_escape_string($baglanti, $satis)
    );
    $deger_sonuc = mysqli_query($baglanti, $deger_sql);
    $deger_toplam = mysqli_num_rows($deger_sonuc);

    $tks_sql = sprintf("SELECT * FROM siparis
         WHERE satis LIKE '%s'",
        mysqli_real_escape_string($baglanti, $satis));
    $tks_toplam = mysqli_num_rows(mysqli_query($baglanti, $tks_sql));

} elseif( isset( $_SESSION['satis'])){
    $satis = "%".$_SESSION['satis']."%";
    $satis2= $_SESSION['satis'];
    $_SESSION['satis'] =$satis2;
    $deg= "&satis=";

    $deger_sql = sprintf(
        "SELECT *
         FROM siparis
         WHERE satis LIKE '%s' 
         LIMIT $kayit_goster, $kayit_sayisi",
        mysqli_real_escape_string($baglanti, $satis)
    );
    $deger_sonuc = mysqli_query($baglanti, $deger_sql);
    $deger_toplam = mysqli_num_rows($deger_sonuc);

    $tks_sql = sprintf("SELECT * FROM siparis
         WHERE satis LIKE '%s'",
        mysqli_real_escape_string($baglanti, $satis));
    $tks_toplam = mysqli_num_rows(mysqli_query($baglanti, $tks_sql));

} elseif( isset( $_GET['soyisim']) ){
    $soyisim = "%".$_GET['soyisim']."%";
    $soyisim2= $_GET['soyisim'];
    $_SESSION['soyisim2'] = $soyisim2;

    $deger_sql = sprintf(
        "SELECT *
         FROM siparis
         WHERE isim LIKE '%s' OR soyisim LIKE '%s' 
          LIMIT $kayit_goster, $kayit_sayisi",
        mysqli_real_escape_string($baglanti, $soyisim),
        mysqli_real_escape_string($baglanti, $soyisim));
    $deger_sonuc = mysqli_query($baglanti, $deger_sql);
    $deger_toplam = mysqli_num_rows($deger_sonuc);

    $tks_sql = sprintf("SELECT * FROM siparis
         WHERE isim LIKE '%s' OR soyisim LIKE '%s'",
        mysqli_real_escape_string($baglanti, $soyisim),
        mysqli_real_escape_string($baglanti, $soyisim));
    $tks_toplam = mysqli_num_rows(mysqli_query($baglanti, $tks_sql));

} elseif( isset( $_SESSION['soyisim2']) ){
    $soyisim = "%".$_SESSION['soyisim2']."%";
    $soyisim2= $_SESSION['soyisim2'];
    $_SESSION['soyisim2'] = $soyisim2;

    $deger_sql = sprintf(
        "SELECT *
         FROM siparis
         WHERE isim LIKE '%s' OR soyisim LIKE '%s' 
          LIMIT $kayit_goster, $kayit_sayisi",
        mysqli_real_escape_string($baglanti, $soyisim),
        mysqli_real_escape_string($baglanti, $soyisim));
    $deger_sonuc = mysqli_query($baglanti, $deger_sql);
    $deger_toplam = mysqli_num_rows($deger_sonuc);

    $tks_sql = sprintf("SELECT * FROM siparis
         WHERE isim LIKE '%s' OR soyisim LIKE '%s'",
        mysqli_real_escape_string($baglanti, $soyisim),
        mysqli_real_escape_string($baglanti, $soyisim));
    $tks_toplam = mysqli_num_rows(mysqli_query($baglanti, $tks_sql));
}
// Varsayılan sorgu
else {
    $deger_sql = "SELECT * FROM siparis ORDER BY id DESC LIMIT $kayit_goster, $kayit_sayisi";
    $deger_sonuc = mysqli_query($baglanti, $deger_sql);
    $deger_toplam = mysqli_num_rows($deger_sonuc);

    $tks_sql = "SELECT * FROM siparis";
    $tks_toplam = mysqli_num_rows(mysqli_query($baglanti, $tks_sql));
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Karataşcam Sipariş Sistemi</title>
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
            -webkit-border-radius: 25px 25px 20px 20px;
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
        .search-form {
            margin-bottom: 20px;
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
        }
        .search-form input, .search-form button {
            padding: 8px;
            margin-right: 5px;
        }
        .search-form button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>

<body>
<div id="banner">
  <p><img src="image/logo-karatascam.png" width="408" height="55" /></p>
</div>
<div id="content">
  <div class="search-form">
    <form action="" method="get">
      <input type="text" name="soyisim" placeholder="Müşteri adı veya soyadı ile ara...">
      <button type="submit">Ara</button>
    </form>
    <form action="" method="get">
      <input type="text" name="satis" placeholder="Satış personeli ile ara...">
      <button type="submit">Ara</button>
    </form>
  </div>

  <p class="yazi"><span class="baslik">Toplam Kayıt Sayısı:</span> <?php echo $tks_toplam; ?></p>
  <p class="yazi"><span class="baslik">Kayıt Aralığı:</span> <?php echo $kayit_goster + 1 ?> - <?php echo min($ileri, $tks_toplam) ?></p>
<table width="900" border="0" align="center" cellpadding="5" cellspacing="0">
  <tr class="baslik">
    <th width="65" nowrap="nowrap" scope="row">Sipariş No</th>
    <th width="87" nowrap="nowrap">Ad</th>
    <th width="72" nowrap="nowrap">Soyad</th>
    <th width="55" nowrap="nowrap">Ürün</th>
    <th width="284" nowrap="nowrap">Sipariş ve Ürün Özellikleri</th>
    <th width="91" nowrap="nowrap">Sipariş Alan</th>
    <th width="121" nowrap="nowrap">Durum</th>
    <th width="45" nowrap="nowrap">&nbsp;</th>
  </tr>
  <?php 
  if ($tks_toplam < 10) {
    $kayit_sayisi = $tks_toplam;
  } elseif($tks_toplam == 10) {
    $kayit_sayisi = 10;
  } elseif($ileri > $tks_toplam) {
    $a = $tks_toplam % 10; 
    $kayit_sayisi = $a;
  }

  for($i=0; $i<$kayit_sayisi; $i++){
    $deger_satir = mysqli_fetch_assoc($deger_sonuc);
    if (!$deger_satir) {
      break;
    }
  ?>
  <tr class="baslik">
    <td height="1" colspan="7" class="yazib" scope="row"><hr /></td>
    <td height="1" class="yazib" scope="row">&nbsp;</td>
    </tr>
  
  <tr bgcolor="#F6FBFA">
    <th class="yazi" scope="row">  <?php echo $deger_satir['yilsiparis'] ?></th>
    <td class="yazi"><?php echo $deger_satir['isim'] ?></td>
    <td class="yazi"><?php echo $deger_satir['soyisim'] ?></td>
    <td class="yazi"><?php echo $deger_satir['urun'] ?></td>
    <td class="yazi"><?php echo $deger_satir['aciklama'] ?></td>
    <td class="yazi"><?php echo $deger_satir['satis'] ?></td>
     <td class="yazi"><?php echo $deger_satir['durum'] ?></td>
    <td><a href="aramasonuc.php?yilsiparis=<?php echo $deger_satir['yilsiparis'] ?>">Ayrıntı</a></td>
    

  </tr>
  <?php } ?>
  <tr>
    <th scope="row">&nbsp;</th>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
 
</table>
 <?php if($ileri > $kayit_sayisi && $kayit_goster > "0"){   ?>

<a href="coklu-sonuc.php?goster=<?php echo $geri ?>">önceki</a>
    <?php } ?>
     | 
    <?php if($ileri < $tks_toplam){
	?>
     <a href="coklu-sonuc.php?goster=<?php echo $ileri; ?>">sonraki</a>
    <?php } ?>
</div>
  <div id="footer"><div id="yetkinlik"><img src="image/yetkinlikler.png"  /></div>
  <p class="footter"><strong>Karataş Ayna Kristal Cam Mob. İnş. Nak. San. ve Tic. Ltd. Şti <br />
   <br />
      </strong>Çalım Sok. No:19 Siteler - Ankara / Türkiye <br />
    T: +90 312 348 9162 |  
    F: +90 312 348 7078<br />
    info@karatascam.com.tr |
  www.karatascam.com.tr</p>
<p>&nbsp;</p></div>

</body>
</html>