<?php


//Sistem Güvenlik
require("baglanti.php");
session_start();
//echo  $_POST['ttarih2'];


//echo  $_POST['yilsiparis'];
//echo $_SESSION['ttarih2'];
$deger_sql = sprintf(
	"SELECT *
	 FROM siparis
	 WHERE yilsiparis ='%s' ",
	 mysqli_real_escape_string($baglanti, $_POST['yilsiparis']));
$deger_sonuc = mysqli_query($baglanti, $deger_sql);
$deger_toplam = mysqli_num_rows($deger_sonuc);
$deger_satir = mysqli_fetch_assoc($deger_sonuc);

$eventsiparis=$_POST['yilsiparis'];
echo  $_POST['durum'];


if ($_POST['durum']== 'Üretim'){
	$category_id='1';
	$notify = '-1';
	
	
	
	}
	elseif ($_POST['durum']== 'Tamamlandı'){
		$category_id= '2';
		$notify = '-3';
	
		}
		elseif ($_POST['durum']== 'Tamamlanmadı!'){
		$category_id= '3';
		$notify = '-2';
		
		}
			elseif ($_POST['durum']== 'İptal'){
		$category_id= '5';
		$notify = '-3';
		
		}
		elseif ($_POST['durum']== 'Hazır'){
		$category_id= '7';
		$notify = '-3';
		
		}
		
		
		//echo  	$category_id;
//$siparis_no2 = $_SESSION['siparisno'];
//$busiparis =$_SESSION['siparisno'];

if(isset($_POST['durum'])){
	$guncelle_sql = sprintf(
		"UPDATE siparis SET durum = '%s' WHERE yilsiparis= '%s' AND id = '%s'",
		mysqli_real_escape_string($baglanti, $_POST['durum']),
		mysqli_real_escape_string($baglanti, $_POST['yilsiparis']),
		mysqli_real_escape_string($baglanti, $_POST['siparis_id'])
	);
	$guncelle = mysqli_query($baglanti, $guncelle_sql);
	
	// Başarı mesajını session'a kaydedelim
	$_SESSION['mesaj'] = "Sipariş durumu başarıyla güncellendi";
	
	// events tablosu olmadığı için bu kısmı atlayabiliriz
	/*
	$guncelle_sql = sprintf(
		"UPDATE events SET category_id = '%s' , notify= '%s' WHERE eventsiparis= '%s'",
		mysqli_real_escape_string($baglanti, $category_id),
		mysqli_real_escape_string($baglanti, $notify),	
		mysqli_real_escape_string($baglanti, $eventsiparis)
	);
	$guncelle = mysqli_query($baglanti, $guncelle_sql);
	*/
	
	// İşlem sonrası sipariş listesine yönlendir
	header("Location: listele.php");
	exit();
}

?>