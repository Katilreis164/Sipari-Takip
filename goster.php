<?php require("baglanti.php");


session_start();
$_SESSION['siparisno']='2024-'.$_SESSION['siparisno2'] ;
//$_SESSION['siparisno']='2014-89' ;
$siparis_no2 = $_SESSION['siparisno'];
$busiparis =$_SESSION['siparisno'];




$deger_sql = sprintf(
	"SELECT *
	 FROM siparis
	 WHERE yilsiparis ='%s' ",
	 mysql_real_escape_string($siparis_no2, $baglanti));
$deger_sonuc = mysql_query($deger_sql, $baglanti);
$deger_toplam = mysql_num_rows($deger_sonuc);
$deger_satir = mysql_fetch_assoc($deger_sonuc);


$urunekle='<form action="resimekle2.php" method="post" enctype="multipart/form-data" name="form1" target="_blank" id="form2">
        <label>
        <div align="right">Ürün Resmi Ekle:
          <input type="file" name="dosya" id="dosya" />
          <input type="submit" name="button2" id="button2" value="Gönder" />
          <br />
        </div>
        </label>
      </form>        '; 
 $krokiekle='<form action="krokiekle2.php" method="post" enctype="multipart/form-data" name="form2"  target="_blank"  id="form2">
        <label>
        <div align="right">Adres Çizimi Ekle:
          <input type="file" name="dosya1" id="dosya1" />
          <input type="submit" name="button3" id="button3" value="Gönder" />
          <br />
        </div>
        </label>
      </form>        '; 






?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

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
.yazi {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 14px;
	color: #000;
	text-decoration: none;
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
</style>



</head>


<body>
<div id="banner">

  <div id="pdf"><a href="siparispdf.php?yilsiparis=<?php echo $deger_satir['yilsiparis'] ?>" target="_blank"><img src="image/pdf.png" width="50" height="60" /></a></div>
<a href="http://www.karatascam.com.tr"><img src="image/logo-karatascam.png" width="408" height="55" border="0" /></a></div>
<div id="content">


  <table width="940" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <th width="218" align="left" valign="top" class="yazibold" scope="row">Siparis No</th>
      <td width="14" valign="top">:</td>
      <td colspan="3" class="yazi"><?php echo $deger_satir['yilsiparis'];?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">Ad</th>
      <td valign="top">:</td>
      <td colspan="3" class="yazi"><?php echo $deger_satir['isim'];?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">Soyad</th>
      <td valign="top">:</td>
      <td colspan="3" class="yazi"><?php echo $deger_satir['soyisim'];?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row"><p>Ev&nbsp;Telefonu</p></th>
      <td valign="top">:</td>
      <td colspan="3" class="yazi"><?php echo $deger_satir['evtel'];?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row"><p>Cep Telefonu</p></th>
      <td valign="top">:</td>
      <td colspan="3" class="yazi"><?php echo $deger_satir['ceptel'];?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">E-mail</th>
      <td valign="top">:</td>
      <td colspan="3" class="yazi"><?php echo $deger_satir['email'];?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">Adres</th>
      <td valign="top">:</td>
      <td width="478" class="yazi"><?php echo $deger_satir['adres'];?>
      <td class="yazibold">Semt      
      :
      <td class="yazi"><?php echo $deger_satir['semt'];?>            
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">Ürün</th>
      <td valign="top">:</td>
      <td colspan="3" class="yazi">   <?php echo $deger_satir['urun'];?> 
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">Sipariş Ürünü ve Ölçüsü</th>
      <td valign="top">:</td>
      <td colspan="3" class="yazi"><?php echo $deger_satir['aciklama'];?>
        
      </td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">Sipariş Tarihi </th>
      <td valign="top">:</td>
      <td class="yazi"><?php echo $deger_satir['starih'];?></td>
      <td width="99" colspan="-1" class="yazibold">Saati:</td>
      <td width="131" colspan="-1" class="yazi"><?php echo $deger_satir['s_time'];?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">Teslimat Tarihi </th>
      <td valign="top">:</td>
      <td class="yazi"><?php echo $deger_satir['ttarih2'];?></td>
      <td colspan="-1" class="yazibold">Saati:</td>
      <td colspan="-1" class="yazi"><?php echo $deger_satir['e_time'];?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">Sipariş Alan</th>
      <td valign="top">:</td>
      <td colspan="3" class="yazi"><?php echo $deger_satir['satis'];?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">Notlar</th>
      <td valign="top">:</td>
      <td colspan="3" class="yazi"><?php echo $deger_satir['nt'];?></td>
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
      <td class="yazibold">Kalan(kdvli):</td>
      <td class="yazi"><?php echo $deger_satir['kdvkalan'];?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">Alınan Miktar</th>
      <td valign="top">:</td>
      <td colspan="3" class="yazi"> <?php echo $deger_satir['kapora'];?></td>
    </tr>
    <tr>
      <th align="left" valign="top" class="yazibold" scope="row">&nbsp;</th>
      <td valign="top">&nbsp;</td>
      <td colspan="3">&nbsp;</td>
    </tr>
  </table>
  <table width="940" border="0" cellspacing="0" cellpadding="0">

  <tr class="yazibold">
      <th colspan="2" align="center" scope="row"><p>&nbsp;</p>
   
    </tr>
  <tr class="yazibold">
    <th align="left" scope="row"><span class="yazi">
      <?php 
	  
	  //veritabanı-resim bağlantısı için

$resim_sql = sprintf(
	"SELECT *
	 FROM  resimler 
	 Where siparisno = '%s'",
mysql_real_escape_string($deger_satir['yilsiparis'], $baglanti));
$resim_sonuc = mysql_query($resim_sql, $baglanti);
$resim_toplam = mysql_num_rows($resim_sonuc);
$resim_satir = mysql_fetch_assoc($resim_sonuc);

				  if($resim_satir['siparisno'] == $deger_satir['yilsiparis']){
			  echo'<a href="resim/'.$resim_satir['resim'].'" target="_blank">'.$resim_satir['resim'].'</a> ';
				  } else{ 
					echo 'Ürün ile ilgili resim bulunmamaktadır.';
					echo $urunekle; } ?>            
    </span>
    <th align="left" scope="row"><span class="yazi">
    <?php 
	  
	  //veritabanı-harita bağlantısı için

$kroki_sql = sprintf(
	"SELECT *
	 FROM  krokiler K, siparis S
	 Where siparisno = '%s' ",
mysql_real_escape_string($deger_satir['yilsiparis'], $baglanti));
$kroki_sonuc = mysql_query($kroki_sql, $baglanti);
$kroki_toplam = mysql_num_rows($kroki_sonuc);
$kroki_satir = mysql_fetch_assoc($kroki_sonuc);

				  if($kroki_satir['siparisno'] == $deger_satir['yilsiparis']){
			  echo'<a href="kroki/'.$kroki_satir['kroki'].'" target="_blank">'.$kroki_satir['kroki'].'</a> ';
				  } else{ 
					echo 'Adres ile ilgili kroki çizim bulunmatakdır.';
					echo $krokiekle;
					   }?>  </td>
    </tr>    <tr>
      <th width="470" align="center" scope="row">
      <td width="470" align="center">
	    <p>&nbsp;</p>
	    <p><span class="yazibold">Sipariş Veren</span><br />	  
	      <span class="yazi"><?php echo $deger_satir['isim'];?> &nbsp; <?php echo $deger_satir['soyisim'];?></span></p>
      <p>&nbsp;</p></td>
    </tr>
  </table>
  <p>&nbsp;</p>

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
