<?php 
require("baglanti.php");
session_start();

if (!isset($_SESSION['personel'])) {
    header('Location: login.php');
    exit;
}

// Güvenlik kontrolü - yilsiparis parametresi yoksa veya boşsa ana sayfaya yönlendir
if (!isset($_GET['yilsiparis']) || empty($_GET['yilsiparis'])) {
    // id parametresi ile mi gelmiş?
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        // id'den siparişi bul
        $id_sql = sprintf(
            "SELECT yilsiparis FROM siparis WHERE id='%s'",
            mysqli_real_escape_string($baglanti, $_GET['id'])
        );
        $id_sonuc = mysqli_query($baglanti, $id_sql);
        if ($id_sonuc && mysqli_num_rows($id_sonuc) > 0) {
            $id_satir = mysqli_fetch_assoc($id_sonuc);
            $siparis_no = $id_satir['yilsiparis'];
        } else {
            header('Location: index.php');
            exit;
        }
    } else {
        header('Location: index.php');
        exit;
    }
} else {
    $siparis_no = $_GET['yilsiparis'];
}

// Sipariş bilgilerini getir
$deger_sql = sprintf(
    "SELECT * FROM siparis WHERE yilsiparis ='%s'",
    mysqli_real_escape_string($baglanti, $siparis_no)
);
$deger_sonuc = mysqli_query($baglanti, $deger_sql);

// Sipariş bulunamadıysa ana sayfaya yönlendir
if (!$deger_sonuc || mysqli_num_rows($deger_sonuc) == 0) {
    header('Location: index.php');
    exit;
}

$deger_satir = mysqli_fetch_assoc($deger_sonuc);

// Güvenli veri alma fonksiyonu
function guvenli_deger($deger_satir, $anahtar, $varsayilan = '') {
    return isset($deger_satir[$anahtar]) ? $deger_satir[$anahtar] : $varsayilan;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Karataşcam Sipariş Formu</title>
    <style type="text/css">
        #banner {
            height: 80px;
            width: 980px;
            padding-top: 20px;
            padding-left: 20px;
        }
        #content {
            width: 1000px;
            min-height: 350px;
        }
        #footer {
            height: 90px;
            width: 1000px;
        }
        #yetkinlik {
            float: right;
            height: 40px;
            width: 400px;
            padding-top: 10px;
        }
        .yazibold {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            font-weight: bold;
            color: #000;
            text-decoration: none;
        }
        .footter {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            text-decoration: none;
            line-height: 15px;
        }
        #pdf {
            float: right;
            width: 140px;
        }
        #banner #pdf a img {
            padding-right: 10px;
            padding-left: 10px;
        }
        .buttons {
            margin-top: 15px;
        }
        .buttons a {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
            margin-right: 10px;
        }
        .buttons a:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
<div id="banner"><a href="coklu-sonuc.php"><img src="image/logo-karatascam.png" width="408" height="55" border="0" /></a>
<table width="200" border="0" align="right" cellpadding="0" cellspacing="0">
  <tr>
    <th align="center" scope="row"><a href="siparispdf.php?yilsiparis=<?php echo guvenli_deger($deger_satir, 'yilsiparis'); ?>" target="_blank"><img src="image/pdf.png" alt="" width="50" height="60" /></a></th>
    <td align="center"><a href="imalatpdf.php?yilsiparis=<?php echo guvenli_deger($deger_satir, 'yilsiparis'); ?>" target="_blank"><img src="image/pdf.png" alt="" width="50" height="60" /></a></td>
  </tr>
  <tr class="footter">
    <th scope="row">Sipariş (fiyatlı)</th>
    <td>İmalat (Fiyatsız)</td>
  </tr>
</table>
</div>
<div id="content">
  <table width="920" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <th width="218" align="left" valign="top" class="yazibold" scope="row">Siparis No</th>
      <td width="14" valign="top">:</td>
      <td colspan="3"><?php echo guvenli_deger($deger_satir, 'yilsiparis'); ?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">Ad</th>
      <td valign="top">:</td>
      <td colspan="3"><?php echo guvenli_deger($deger_satir, 'isim'); ?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">Soyad</th>
      <td valign="top">:</td>
      <td colspan="3"><?php echo guvenli_deger($deger_satir, 'soyisim'); ?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row"><p>Ev&nbsp;Telefonu</p></th>
      <td valign="top">:</td>
      <td colspan="3"><?php echo guvenli_deger($deger_satir, 'evtel'); ?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row"><p>Cep Telefonu</p></th>
      <td valign="top">:</td>
      <td colspan="3"><?php echo guvenli_deger($deger_satir, 'ceptel'); ?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">E-mail</th>
      <td valign="top">:</td>
      <td colspan="3"><?php echo guvenli_deger($deger_satir, 'email'); ?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">Adres</th>
      <td valign="top">:</td>
      <td><?php echo guvenli_deger($deger_satir, 'adres'); ?>
      <td class="yazibold">Semt:      
      <td><?php echo guvenli_deger($deger_satir, 'semt'); ?>            
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">Ürün</th>
      <td valign="top">:</td>
      <td colspan="3"><?php echo guvenli_deger($deger_satir, 'urun'); ?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">Sipariş Ürünü ve Ölçüsü</th>
      <td valign="top">:</td>
      <td colspan="3"><?php echo guvenli_deger($deger_satir, 'aciklama'); ?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">Sipariş Tarihi </th>
      <td valign="top">:</td>
      <td width="514"><?php echo guvenli_deger($deger_satir, 'starih'); ?></td>
      <td width="85" class="yazibold">Saati:</td>
      <td width="89"><?php echo guvenli_deger($deger_satir, 's_time'); ?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">Teslimat Tarihi </th>
      <td valign="top">:</td>
      <td><?php echo guvenli_deger($deger_satir, 'ttarih2'); ?></td>
      <td class="yazibold">Saati:</td>
      <td><?php echo guvenli_deger($deger_satir, 'e_time'); ?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">Sipariş Alan</th>
      <td valign="top">:</td>
      <td colspan="3"><?php echo guvenli_deger($deger_satir, 'satis'); ?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">Notlar</th>
      <td valign="top">:</td>
      <td colspan="3" class="yazi"><?php echo guvenli_deger($deger_satir, 'nt'); ?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">Tutar (KDV'siz)</th>
      <td valign="top">:</td>
      <td><?php echo guvenli_deger($deger_satir, 'fiyat'); ?></td>
      <td class="yazibold"> Kalan: </td>
      <td class="yazi"><?php echo guvenli_deger($deger_satir, 'kalan'); ?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">Son Tutar (KDV Dahil)</th>
      <td valign="top">:</td>
      <td class="yazi"><?php echo guvenli_deger($deger_satir, 'fiyat_sn'); ?></td>
      <td class="yazibold">Kalan(kdvli):</td>
      <td class="yazi"><?php echo guvenli_deger($deger_satir, 'kdvkalan'); ?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">Alınan Miktar</th>
      <td valign="top">:</td>
      <td colspan="3" class="yazi"><?php echo guvenli_deger($deger_satir, 'kapora'); ?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">Durum</th>
      <td valign="top">:</td>
      <td colspan="3" class="yazi"><?php echo guvenli_deger($deger_satir, 'durum'); ?></td>
    </tr>
  </table>
  <table width="920" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <th width="419" align="center" scope="row">
      <td width="501" align="center">
	    <p>&nbsp;</p>
	    <p><span class="yazibold">Sipariş Veren</span><br />	  
	      <?php echo guvenli_deger($deger_satir, 'isim'); ?> &nbsp; <?php echo guvenli_deger($deger_satir, 'soyisim'); ?></p>
      <p>&nbsp;</p></td>
    </tr>
  </table>
  
  <div class="buttons">
    <a href="guncelle.php?siparis_no3=<?php echo guvenli_deger($deger_satir, 'yilsiparis'); ?>">Siparişi Güncelle</a>
    <a href="coklu-sonuc.php">Tüm Siparişlere Dön</a>
  </div>
</div>
<div id="footer">
  <div id="yetkinlik"><img src="image/yetkinlik.png" /></div>
  <p class="footter"><strong>Karataş Ayna Kristal Cam Mob. İnş. Nak. San. ve Tic. Ltd. Şti <br />
    </strong><strong><br />
      </strong>Çalım Sok. No:19 Siteler - Ankara / Türkiye <br />
    T: +90 312 348 9162 |  
    F: +90 312 348 7078<br />
    info@karatascam.com.tr |
  www.karatascam.com.tr</p>
  <p>&nbsp;</p>
</div>
</body>
</html>
