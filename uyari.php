<?php 
require("baglanti.php");
$todayT = mktime(12,0,0); //12:00
$todayD00 = date("Y-m-d", $todayT); //today
$todayD30 = date("Y-m-d", $todayT + 259200); //today + 3 days
$todayD32 = date("Y-m-d", $todayT + 172800); //today + 2days
$todayD31 = date("Y-m-d", $todayT + 86400); //today + 1 days

/*echo $todayD00 ;
echo '*-*';
echo $todayD30;
echo '*-*';
echo $todayT;
echo '*-*';*/

function tarihkarsilastir($ilk_tarih,$son_tarih){
$ilk = strtotime($ilk_tarih);
$son = strtotime($son_tarih);
if ($ilk-$son > 0)
{
return 1;
}
else
{
return 0;
}
}

$deger_sql = sprintf(
	"SELECT *
	FROM events"
	);
$deger_sonuc = mysqli_query($baglanti, $deger_sql);
$deger_toplam = mysqli_num_rows($deger_sonuc);


//$sontarih= $todayD30;
$e='-2';



//echo  $deger_satir['e_date'];

for($i=0; $i<$deger_toplam;$i++){
  $deger_satir = mysqli_fetch_assoc($deger_sonuc); 
 //echo $deger_satir['e_date'];
 

if  (($todayD30 == $deger_satir['e_date'] or $todayD32 == $deger_satir['e_date'] or $todayD31 == $deger_satir['e_date'])  and ($deger_satir['notify'] == $e or  $deger_satir['notify'] == "-1" )) {
	//echo $deger_satir['notify'] ;
	//echo $todayD30;
	//echo $deger_satir['e_date'];
	// echo $deger_satir['eventsiparis'];
	// echo $deger_satir['category_id'];
	 $category_id = 4; 
	 $durum= 'DolmakÜzere!';
				$guncelle_sql = sprintf(
		"UPDATE events SET category_id = '%s' WHERE eventsiparis= '%s'",
		mysqli_real_escape_string($baglanti, $category_id),	
		mysqli_real_escape_string($baglanti, $deger_satir['eventsiparis']));
	$guncelle = mysqli_query($baglanti, $guncelle_sql);
	$guncelle2_sql = sprintf
		(
		"UPDATE siparis SET durum = '%s' WHERE yilsiparis= '%s'",
		mysqli_real_escape_string($baglanti, $durum),	
		mysqli_real_escape_string($baglanti, $deger_satir['eventsiparis']));
	$guncelle2 = mysqli_query($baglanti, $guncelle2_sql);
	//header("Location: index.php");
			
		
	}
	
	elseif (($todayD00 == $deger_satir['e_date']) and ($deger_satir['notify'] == $e or $deger_satir['notify'] == "-1" )){
		//echo $deger_satir['notify'] ;
		//echo 'oldu';
			//echo $deger_satir['e_date'];
 //echo $deger_satir['eventsiparis'];
 //echo $deger_satir['category_id'];
	 $category_id = 3; 
	 $durum= 'Tamamlanmadı!';
	 
	$guncelle_sql = sprintf(
		"UPDATE events SET category_id = '%s' WHERE eventsiparis= '%s'",
		mysqli_real_escape_string($baglanti, $category_id),	
		mysqli_real_escape_string($baglanti, $deger_satir['eventsiparis']));
	$guncelle = mysqli_query($baglanti, $guncelle_sql);
				$guncelle2_sql = sprintf
		(
		"UPDATE siparis SET durum = '%s' WHERE yilsiparis= '%s'",
		mysqli_real_escape_string($baglanti, $durum),	
		mysqli_real_escape_string($baglanti, $deger_satir['eventsiparis']));
	$guncelle2 = mysqli_query($baglanti, $guncelle2_sql);
	//header("Location: index.php");
	
	}	
 elseif ($deger_satir['category_id'] == "3" or  $deger_satir['category_id'] == "7"){	
 $tarih1= $todayD00;
$tarih2= $deger_satir['e_date'];
//echo "*** ";
//echo $tarih2;
//echo " ***";

	if(tarihkarsilastir($tarih1,$tarih2)) {
	//echo "Birinci Tarih Buyuk";
	$ilk2 = strtotime($tarih2);
	$tarihh2= date("Y-m-d", $ilk2 + 86400);
	//echo $tarihh2;
	$guncelle_sql = sprintf(
		"UPDATE events SET e_date = '%s' WHERE eventsiparis= '%s'",
		mysqli_real_escape_string($baglanti, $tarihh2),	
		mysqli_real_escape_string($baglanti, $deger_satir['eventsiparis']));
	$guncelle = mysqli_query($baglanti, $guncelle_sql);
	}
	else{
	//$tarihh2= date("Y-m-d", $tarih2 + 86400);
	
	}
	}

}
// Öncelikle Fonksiyonu tanımlıyoruz....


//Diyelimki elimizde aşağıdaki gibi iki tarih olsun ve bunların hangisini büyük olduğunu öğrenmemiz gerekiyor
//  Fonsiyonumuzu kullanarak bunu rahatlıkla yapabiliriz...
//$tarih1="2007-10-11 ";
//$tarih2="2007-10-12 ";

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
</body>
</html>