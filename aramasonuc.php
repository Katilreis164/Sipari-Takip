<?php 
require("baglanti.php");
session_start();

// Giriş kontrolü ekleyelim
if (!isset($_SESSION['personel'])) {
    header('Location: login.php');
    exit;
}

// Sipariş numarasını değişkenlere ekleyelim
$yilsiparis = isset($_GET['yilsiparis']) ? $_GET['yilsiparis'] : '';

$urunekle = '<form action="resimekle2.php?yilsiparis=' . $yilsiparis . '" method="post" enctype="multipart/form-data" name="form1" target="_blank" id="form2">
        <label>
        <div align="right">Ürün Resmi Ekle:
          <input type="file" name="dosya" id="dosya" />
          <input type="submit" name="button2" id="button2" value="Gönder" />
          <br />
        </div>
        </label>
      </form>'; 

$krokiekle = '<form action="krokiekle2.php?yilsiparis=' . $yilsiparis . '" method="post" enctype="multipart/form-data" name="form2" target="_blank" id="form2">
        <label>
        <div align="right">Adres Çizimi Ekle:
          <input type="file" name="dosya1" id="dosya1" />
          <input type="submit" name="button3" id="button3" value="Gönder" />
          <br />
        </div>
        </label>
      </form>';

// Sipariş bulunamadığında veya arama yapılmadığında görüntülenecek mesaj
$no_results_message = '<div style="text-align:center; padding:20px; background-color:#f8d7da; color:#721c24; border:1px solid #f5c6cb; border-radius:5px; margin:20px 0;">
    <h3>Sipariş Bulunamadı</h3>
    <p>Girdiğiniz kriterlere uygun sipariş kaydı bulunamadı veya arama yapmadınız.</p>
    <p><a href="index.php" style="color:#721c24; font-weight:bold;">Ana Sayfaya Dön</a></p>
</div>';

// Sipariş numarası POST ile geldiyse
if(isset($_POST['siparis_no']) && !empty($_POST['siparis_no'])){
    $siparis_no2 = $_POST['siparis_no'];
    $busiparis = $_POST['siparis_no'];
    $deger_sql = sprintf(
        "SELECT *
         FROM siparis
         WHERE yilsiparis ='%s'",
        mysqli_real_escape_string($baglanti, $siparis_no2)
    );
    $deger_sonuc = mysqli_query($baglanti, $deger_sql);
    $deger_toplam = mysqli_num_rows($deger_sonuc);
    
    if($deger_toplam > 0) {
        $deger_satir = mysqli_fetch_assoc($deger_sonuc);
        $siparisVar = true;
    } else {
        $siparisVar = false;
    }
}
// Sipariş numarası GET ile geldiyse
elseif(isset($_GET['yilsiparis']) && !empty($_GET['yilsiparis'])){
    $siparis_no = $_GET['yilsiparis'];
    $busiparis = $_GET['yilsiparis'];
    $deger_sql = sprintf(
        "SELECT *
         FROM siparis
         WHERE yilsiparis ='%s'",
        mysqli_real_escape_string($baglanti, $siparis_no)
    );
    $deger_sonuc = mysqli_query($baglanti, $deger_sql);
    $deger_toplam = mysqli_num_rows($deger_sonuc);
    
    if($deger_toplam > 0) {
        $deger_satir = mysqli_fetch_assoc($deger_sonuc);
        $siparisVar = true;
    } else {
        $siparisVar = false;
    }
}
// Müşteri ismi parametresi geldiyse
elseif(isset($_POST['musteri_isim']) && !empty($_POST['musteri_isim'])){
    $musteri_isim = $_POST['musteri_isim'];
    
    // İsim veya soyisime göre arama yap (LIKE operatörü ile benzer sonuçları da getir)
    $deger_sql = sprintf(
        "SELECT *
         FROM siparis
         WHERE isim LIKE '%%%s%%' OR soyisim LIKE '%%%s%%'
         ORDER BY starih DESC",
        mysqli_real_escape_string($baglanti, $musteri_isim),
        mysqli_real_escape_string($baglanti, $musteri_isim)
    );
    $deger_sonuc = mysqli_query($baglanti, $deger_sql);
    $deger_toplam = mysqli_num_rows($deger_sonuc);
    
    if($deger_toplam > 0) {
        // Birden fazla sonuç varsa, coklu-sonuc.php sayfasına yönlendir
        if($deger_toplam > 1) {
            $_SESSION['son_sorgu'] = $deger_sql;
            $_SESSION['soyisim2'] = $musteri_isim; // coklu-sonuc.php için gerekli
            header("Location: coklu-sonuc.php");
            exit;
        } else {
            // Tek sonuç varsa, o siparişi göster
            $deger_satir = mysqli_fetch_assoc($deger_sonuc);
            $busiparis = $deger_satir['yilsiparis'];
            $siparisVar = true;
        }
    } else {
        $siparisVar = false;
    }
}
// Soyadı parametresi geldiyse
elseif(isset($_POST['soyisim']) && !empty($_POST['soyisim'])){
    $siparis_no2 = $_POST['soyisim'];
    
    $deger_sql = sprintf(
        "SELECT *
         FROM siparis
         WHERE soyisim ='%s'",
        mysqli_real_escape_string($baglanti, $siparis_no2)
    );
    $deger_sonuc = mysqli_query($baglanti, $deger_sql);
    $deger_toplam = mysqli_num_rows($deger_sonuc);
    
    if($deger_toplam > 0) {
        $deger_satir = mysqli_fetch_assoc($deger_sonuc);
        $busiparis = $deger_satir['yilsiparis'];
        $siparisVar = true;
    } else {
        $siparisVar = false;
    }
} else {
    // Hiçbir arama yapılmadı
    $siparisVar = false;
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
            margin-left: 40px;
        }
        #content {
            width: 1000px;
            min-height: 350px;
            margin-left: 40px;
        }
        #footer {
            height: 90px;
            width: 1000px;
            margin-left: 40px;
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
            width: 100px;
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
<div id="banner">
  <?php if($siparisVar): ?>
  <table width="200" border="0" align="right" cellpadding="0" cellspacing="0">
    <tr>
      <th align="center" scope="row"><a href="siparispdf.php?yilsiparis=<?php echo $deger_satir['yilsiparis'] ?>" target="_blank"><img src="image/pdf.png" alt="" width="50" height="60" /></a></th>
      <td align="center"><a href="imalatpdf.php?yilsiparis=<?php echo $deger_satir['yilsiparis'] ?>" target="_blank"><img src="image/pdf.png" alt="" width="50" height="60" /></a></td>
    </tr>
    <tr class="footter">
      <th scope="row">Sipariş (fiyatlı)</th>
      <td>İmalat (Fiyatsız)</td>
    </tr>
  </table>
  <?php endif; ?>
  <div id="logo">
    <img src="image/logo-karatascam.png" width="408" height="55" />
  </div>
</div>
<div id="content">

<?php if($siparisVar): ?>
  <table width="920" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <th width="218" align="left" valign="top" class="yazibold" scope="row">Siparis No</th>
      <td width="14" valign="top">:</td>
      <td colspan="3"><?php echo $deger_satir['yilsiparis'];?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">Ad</th>
      <td valign="top">:</td>
      <td colspan="3"><?php echo $deger_satir['isim'];?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">Soyad</th>
      <td valign="top">:</td>
      <td colspan="3"><?php echo $deger_satir['soyisim'];?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row"><p>Ev&nbsp;Telefonu</p></th>
      <td valign="top">:</td>
      <td colspan="3"><?php echo $deger_satir['evtel'];?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row"><p>Cep Telefonu</p></th>
      <td valign="top">:</td>
      <td colspan="3"><?php echo $deger_satir['ceptel'];?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">E-mail</th>
      <td valign="top">:</td>
      <td colspan="3"><?php echo $deger_satir['email'];?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">Adres</th>
      <td valign="top">:</td>
      <td><?php echo $deger_satir['adres'];?>
      <td nowrap="nowrap" class="yazibold">Semt:      
      <td nowrap="nowrap"><?php echo $deger_satir['semt'];?>            
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">Ürün</th>
      <td valign="top">:</td>
      <td colspan="3"><?php echo $deger_satir['urun'];?> 
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">Sipariş Ürünü ve Ölçüsü</th>
      <td valign="top">:</td>
      <td colspan="3"><?php echo $deger_satir['aciklama'];?>
        
      </td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">Sipariş Tarihi </th>
      <td valign="top">:</td>
      <td width="493"><?php echo $deger_satir['starih'];?></td>
      <td width="106" class="yazibold">Saati:</td>
      <td width="89"><?php echo $deger_satir['s_time'];?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">Teslimat Tarihi </th>
      <td valign="top">:</td>
      <td><?php echo $deger_satir['ttarih2'];?></td>
      <td class="yazibold">Saati:</td>
      <td><?php echo $deger_satir['e_time'];?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">Sipariş Alan</th>
      <td valign="top">:</td>
      <td colspan="3"><?php echo $deger_satir['satis'];?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">Notlar</th>
      <td valign="top">:</td>
      <td colspan="3"><?php echo $deger_satir['nt'];?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">Tutar (KDV'siz)</th>
      <td valign="top">:</td>
      <td><?php echo $deger_satir['fiyat'];?></td>
      <td class="yazibold"> Kalan: </td>
      <td class="yazi"><?php echo $deger_satir['kalan'];?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">Son Tutar (KDV Dahil)</th>
      <td valign="top">:</td>
      <td class="yazi"><?php echo $deger_satir['fiyat_sn'];?></td>
      <td class="yazibold">Kalan(kdv'li):</td>
      <td class="yazi"><?php echo $deger_satir['kdvkalan'];?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">Alınan Miktar</th>
      <td valign="top">:</td>
      <td colspan="3" class="yazi"><?php echo $deger_satir['kapora'];?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">Durum</th>
      <td valign="top">:</td>
      <td colspan="3" class="yazi"><?php echo $deger_satir['durum'];?></td>
    </tr>
  </table>
  <table width="920" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <th align="center" scope="row">      
      <td align="center">&nbsp;</td>
    </tr>
    <tr class="yazibold">
      <th align="left" scope="row"><?php 
	  
	  //veritabanı-resim bağlantısı için
      $resim_sql = sprintf(
          "SELECT *
           FROM resimler 
           WHERE siparisno = '%s'",
          mysqli_real_escape_string($baglanti, $busiparis));
      $resim_sonuc = mysqli_query($baglanti, $resim_sql);
      $resim_toplam = mysqli_num_rows($resim_sonuc);
      
      if ($resim_toplam > 0) {
          $resim_satir = mysqli_fetch_assoc($resim_sonuc);
          echo '<a href="resim/'.$resim_satir['resim'].'" target="_blank">'.$resim_satir['resim'].'</a> ';
      } else { 
          echo 'Ürün ile ilgili resim bulunmamaktadır.';
          echo $urunekle; 
      }
      ?>
      </th>
      <th align="left" scope="row"><?php 
	  
	  //veritabanı-harita bağlantısı için
      $kroki_sql = sprintf(
          "SELECT *
           FROM krokiler
           WHERE siparisno = '%s'",
          mysqli_real_escape_string($baglanti, $busiparis));
      $kroki_sonuc = mysqli_query($baglanti, $kroki_sql);
      $kroki_toplam = mysqli_num_rows($kroki_sonuc);
      
      if ($kroki_toplam > 0) {
          $kroki_satir = mysqli_fetch_assoc($kroki_sonuc);
          echo '<a href="kroki/'.$kroki_satir['kroki'].'" target="_blank">'.$kroki_satir['kroki'].' </a> ';
      } else { 
          echo 'Adres ile ilgili kroki çizim bulunmatakdır.';
          echo $krokiekle;
      }
      ?>
      </th>
    </tr>
    <tr>
      <th width="419" align="center" scope="row">
      <td width="501" align="center"><p><span class="yazibold">Sipariş Veren</span><br />	  
      <?php echo $deger_satir['isim'];?> &nbsp; <?php echo $deger_satir['soyisim'];?></p></td>
    </tr>
  </table>
  
  <div class="buttons">
    <a href="guncelle.php?siparis_no3=<?php echo $deger_satir['yilsiparis']; ?>">Siparişi Güncelle</a>
    <a href="coklu-sonuc.php">Tüm Siparişlere Dön</a>
  </div>
<?php else: ?>
  <!-- Sipariş bulunamadığında görünecek mesaj -->
  <?php echo $no_results_message; ?>
<?php endif; ?>
</div>
<div id="footer">
  <div id="yetkinlik"><img src="image/yetkinlikler.png" /></div>
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
