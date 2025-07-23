<?php require("baglanti.php");

session_start();
if(isset($_POST['siparis_no3'])){
    $_SESSION['siparis_no3'] = $_POST['siparis_no3'];
    
    $siparis_no2 = $_POST['siparis_no3'];
    $busiparis = $_POST['siparis_no3'];
    
    $deger_sql = sprintf(
        "SELECT * FROM siparis WHERE yilsiparis ='%s'",
        mysqli_real_escape_string($baglanti, $siparis_no2)
    );
    $deger_sonuc = mysqli_query($baglanti, $deger_sql);
    $deger_toplam = mysqli_num_rows($deger_sonuc);
    $deger_satir = mysqli_fetch_assoc($deger_sonuc);
}
elseif(isset($_GET['siparis_no3'])){
    $_SESSION['siparis_no3'] = $_GET['siparis_no3'];
    
    $siparis_no2 = $_GET['siparis_no3'];
    $busiparis = $_GET['siparis_no3'];
    
    $deger_sql = sprintf(
        "SELECT * FROM siparis WHERE yilsiparis ='%s'",
        mysqli_real_escape_string($baglanti, $siparis_no2)
    );
    $deger_sonuc = mysqli_query($baglanti, $deger_sql);
    $deger_toplam = mysqli_num_rows($deger_sonuc);
    $deger_satir = mysqli_fetch_assoc($deger_sonuc);
}

$adim1=  '<option value="Üretim" selected="selected">Üretim</option>
<option value="Hazır">Hazır</option>
           <option value="Tamamlandı">Tamamlandı</option>
		   <option value="İptal">İptal</option>';
		   
$adim2=  '<option value="Tamamlandı" selected="selected">Tamamlandı</option>
           <option value="Üretim">Üretim</option>
		   <option value="İptal">İptal</option>
		   <option value="Hazır">Hazır</option>';
		   
$adim3=  '<option value="Tamamlanmadı!" selected="selected">Tamamlanmadı!</option>
           <option value="Üretim">Üretim</option>
		   <option value="Tamamlandı">Tamamlandı</option>
		   <option value="İptal">İptal</option>
		   <option value="Hazır">Hazır</option>';
		    
$adim4=  '<option value="DolmakÜzere!" selected="selected">DolmakÜzere!</option>
           <option value="Üretim">Üretim</option>
		   <option value="Tamamlandı">Tamamlandı</option>
		   <option value="İptal">İptal</option>
		   <option value="Hazır">Hazır</option>';
		   
$adim5=  '<option value="İptal" selected="selected">İptal</option>
           <option value="Üretim">Üretim</option>
		   <option value="Tamamlandı">Tamamlandı</option>
		   <option value="Tamamlanmadı!">Tamamlanmadı!</option>
		     <option value="Hazır">Hazır</option>';
		   
		   $adim6=  '<option value="Hazır" selected="selected">Hazır</option>
           <option value="Üretim">Üretim</option>
		   <option value="Tamamlandı">Tamamlandı</option>
		   <option value="Tamamlanmadı!">Tamamlanmadı!</option>
		    <option value="İptal">İptal</option>';
		   



?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Güncelleme</title>
    <link href="assets/css/style.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        .update-form {
            width: 100%;
            max-width: 100%;
            padding: 0;
            margin: 0;
        }
        .update-form .form-group {
            margin-bottom: 15px;
        }
        .update-form .form-control {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 20px;
            width: 100%;
        }
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
}
</style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Sipariş Güncelleme</h1>
        <div class="update-form">
  <table width="940" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <th width="218" align="left" class="yazibold" scope="row">Siparis No</th>
      <td width="14">:</td>
      <td colspan="3"><?php echo $deger_satir['yilsiparis'];?></td>
    </tr>
    <tr>
      <th align="left" class="yazibold" scope="row">Ad</th>
      <td>:</td>
      <td colspan="3"><?php echo $deger_satir['isim'];?></td>
    </tr>
    <tr>
      <th align="left" class="yazibold" scope="row">Soyad</th>
      <td>:</td>
      <td colspan="3"><?php echo $deger_satir['soyisim'];?></td>
    </tr>
    <tr>
      <th align="left" class="yazibold" scope="row"><p>Ev&nbsp;Telefonu</p></th>
      <td>:</td>
      <td colspan="3"><?php echo $deger_satir['evtel'];?></td>
    </tr>
    <tr>
      <th align="left" class="yazibold" scope="row"><p>Cep Telefonu</p></th>
      <td>:</td>
      <td colspan="3"><?php echo $deger_satir['ceptel'];?></td>
    </tr>
    <tr>
      <th align="left" class="yazibold" scope="row">E-mail</th>
      <td>:</td>
      <td colspan="3"><?php echo $deger_satir['email'];?></td>
    </tr>
    <tr>
      <th align="left" class="yazibold" scope="row">Adres</th>
      <td>:</td>
      <td><?php echo $deger_satir['adres'];?>
      <td><strong>Semt:</strong>      
    <td><?php echo $deger_satir['semt'];?></tr>
    <tr>
      <th align="left" class="yazibold" scope="row">Ürün</th>
      <td>:</td>
      <td colspan="3">   <?php echo $deger_satir['urun'];?> 
    </tr>
    <tr>
      <th align="left" class="yazibold" scope="row">Sipariş Ürünü ve Ölçüsü</th>
      <td>:</td>
      <td colspan="3"><?php echo $deger_satir['aciklama'];?>
        
      </td>
    </tr>
    <tr>
      <th align="left" class="yazibold" scope="row">Sipariş Tarihi </th>
      <td>:</td>
      <td width="486"><?php echo $deger_satir['starih'];?></td>
      <td width="127" colspan="-1" class="yazibold">Saati:</td>
      <td width="95" colspan="-1"><?php echo $deger_satir['s_time'];?></td>
    </tr>
    <tr>
      <th align="left" class="yazibold" scope="row">Teslimat Tarihi </th>
      <td>:</td>
      <td><?php echo $deger_satir['ttarih2'];?></td>
      <td colspan="-1" class="yazibold">Saati:</td>
      <td colspan="-1"><?php echo $deger_satir['e_time'];?></td>
    </tr>
    <tr>
      <th align="left" class="yazibold" scope="row">Sipariş Alan</th>
      <td>:</td>
      <td colspan="3"><?php echo $deger_satir['satis'];?></td>
    </tr>
    <tr>
      <th align="left" class="yazibold" scope="row">Notlar</th>
      <td>:</td>
      <td colspan="3"><?php echo $deger_satir['nt'];?></td>
    </tr>
    <tr>
      <th align="left" class="yazibold" scope="row">Tutar (KDV'siz)</th>
      <td>:</td>
      <td><?php echo $deger_satir['fiyat'];?></td>
      <td class="yazibold"> Kalan: </td>
      <td class="yazi"><?php echo $deger_satir['kalan'];?></td>
    </tr>
    <tr>
      <th align="left" class="yazibold" scope="row">Son Tutar (KDV Dahil)</th>
      <td>:</td>
      <td class="yazi"><?php echo $deger_satir['fiyat_sn'];?></td>
      <td class="yazibold">Kalan:</td>
      <td class="yazi"><?php echo $deger_satir['fiyat'];?></td>
    </tr>
    <tr>
      <th align="left" class="yazibold" scope="row">Kapora</th>
      <td>:</td>
      <td colspan="3"> <?php echo $deger_satir['kapora'];?></td>
    </tr>
    <tr>
      <th align="left" class="yazibold" scope="row">Durum</th>
      <td>:</td>
      <td colspan="3"><form action="guncelle2.php" method="post" enctype="multipart/form-data" name="form1" id="form1">
        <label for="durum"></label>
        <select name="durum" id="durum">
          <?php if ($deger_satir['durum'] == 'Üretim'){
            echo $adim1;
        }elseif ($deger_satir['durum'] == 'Tamamlandı'){
            echo  $adim2;    
                }
                elseif ($deger_satir['durum'] == 'Tamamlanmadı!'){
            echo  $adim3;    
                }
                elseif ($deger_satir['durum'] == 'DolmakÜzere!'){
            echo  $adim4;    
                }
                elseif ($deger_satir['durum'] == 'İptal'){
            echo  $adim5;    
                }
                            elseif ($deger_satir['durum'] == 'Hazır'){
                                echo  $adim6;    
                            }
          ?>
        </select>
                        <input type="hidden" name="yilsiparis" value="<?php echo $deger_satir['yilsiparis']; ?>" />
                        <input type="hidden" name="siparis_id" value="<?php echo $deger_satir['id']; ?>" />
                        <input type="submit" name="guncelle" value="Durumu Güncelle" style="margin-left:10px; padding:5px 10px; background-color:#4CAF50; color:white; border:none; border-radius:4px; cursor:pointer;" />
      </form></td>
    </tr>
  </table>
  <table width="940" border="0" cellspacing="0" cellpadding="0">

  <tr class="yazibold">
      <th colspan="2" align="center" scope="row"><p>&nbsp;</p>
   
    </tr>
  <tr class="yazibold">
    <th align="left" scope="row"><?php 
	  
	  //veritabanı-resim bağlantısı için

$resim_sql = sprintf(
	"SELECT *
	 FROM  resimler 
	 Where siparisno = '%s'",
mysqli_real_escape_string($baglanti, $busiparis));
$resim_sonuc = mysqli_query($baglanti, $resim_sql);
$resim_toplam = mysqli_num_rows($resim_sonuc);
$resim_satir = mysqli_fetch_assoc($resim_sonuc);

                      if($resim_toplam > 0 && isset($resim_satir['resim']) && $resim_satir['resim'] == $siparis_no2.'.jpg'){
			  echo'<a href="resim/'.$resim_satir['resim'].'" target="_blank">Ürün Resmini Göster</a> ';
				  } else{ 
					echo 'Ürün ile ilgili resim bulunmamaktadır.';
				 } ?>            
    <th align="left" scope="row"><?php 
	  
	  //veritabanı-harita bağlantısı için

$kroki_sql = sprintf(
	"SELECT *
	 FROM  krokiler K, siparis S
	 Where siparisno = '%s' ",
mysqli_real_escape_string($baglanti, $busiparis));
$kroki_sonuc = mysqli_query($baglanti, $kroki_sql);
$kroki_toplam = mysqli_num_rows($kroki_sonuc);

$kroki_satir = mysqli_fetch_assoc($kroki_sonuc);

if($kroki_toplam > 0 && isset($kroki_satir['kroki']) && $kroki_satir['kroki'] == $siparis_no2.'.jpg'){
    echo'<a href="kroki/'.$kroki_satir['kroki'].'" target="_blank">Adres ile İlgili Çizimi Göster </a> ';
} else { 
    echo 'Adres ile ilgili kroki çizim bulunmamaktadır.';
} ?>        
  </tr>
    <tr>
      <th width="470" align="center" scope="row">
      <td width="470" align="center">
	    <p>&nbsp;</p>
	    <p><span class="yazibold">Sipariş Veren</span><br />	  
	      <?php echo $deger_satir['isim'];?> &nbsp; <?php echo $deger_satir['soyisim'];?></p>
      <p>&nbsp;</p></td>
    </tr>
  </table>
  <p>&nbsp;</p>
</div>
</div>
</body>
</html>