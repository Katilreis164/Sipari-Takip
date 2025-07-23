<?php
// Hata raporlamasını kapat ve çıktı tamponlamayı başlat
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

require("baglanti.php");
session_start();

// Güvenlik kontrolü
if (!isset($_SESSION['personel'])) {
    header('Location: login.php');
    exit;
}

// Siparişi belirleme - önce yilsiparis parametresini kontrol et
if (isset($_GET['yilsiparis']) && !empty($_GET['yilsiparis'])) {
    $siparisNo = $_GET['yilsiparis'];
} 
// Yoksa id parametresi ile dene
elseif (isset($_GET['id']) && !empty($_GET['id'])) {
    // ID'den sipariş numarasını bul
    $id_sql = "SELECT yilsiparis FROM siparis WHERE id = '".mysqli_real_escape_string($baglanti, $_GET['id'])."'";
    $id_sonuc = mysqli_query($baglanti, $id_sql);
    
    if ($id_sonuc && mysqli_num_rows($id_sonuc) > 0) {
        $id_satir = mysqli_fetch_assoc($id_sonuc);
        $siparisNo = $id_satir['yilsiparis'];
    } else {
        // Sipariş bulunamadı
        echo "<script>alert('Sipariş bulunamadı!');</script>";
        echo "<script>window.close();</script>";
        exit;
    }
} else {
    // Geçerli parametre yok
    echo "<script>alert('Geçersiz sipariş parametresi!');</script>";
    echo "<script>window.close();</script>";
    exit;
}

// Siparişi getir
$sql = "SELECT * FROM siparis WHERE yilsiparis = '".mysqli_real_escape_string($baglanti, $siparisNo)."'";
$result = mysqli_query($baglanti, $sql);

// Sipariş var mı kontrol et
if (!$result || mysqli_num_rows($result) == 0) {
    echo "<script>alert('Sipariş bulunamadı!');</script>";
    echo "<script>window.close();</script>";
    exit;
}

$row = mysqli_fetch_assoc($result);

// Güvenli veri alma fonksiyonu
function guvenli_deger($row, $anahtar, $varsayilan = '') {
    return isset($row[$anahtar]) ? $row[$anahtar] : $varsayilan;
}

// TCPDF kütüphanesini ekleyin
if (file_exists('tcpdf/tcpdf.php')) {
    require_once('tcpdf/tcpdf.php');
} else {
    die('TCPDF kütüphanesi bulunamadı. Lütfen <a href="https://github.com/tecnickcom/TCPDF/archive/refs/heads/main.zip">buradan</a> indirip tcpdf klasörüne yükleyin.');
}

// PDF oluştur
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Belge bilgilerini ayarla
$pdf->SetCreator('Karataşcam Sipariş Takip Sistemi');
$pdf->SetAuthor('Karataşcam');
$pdf->SetTitle('Sipariş: ' . $siparisNo);
$pdf->SetSubject('Sipariş Formu');

// Varsayılan başlık ve altbilgi ayarlarını kapat
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Sayfa ekle
$pdf->AddPage();

// Şirket logosu
if (file_exists('image/logo-karatascam.png')) {
    $pdf->Image('image/logo-karatascam.png', 15, 10, 80, 0, 'PNG');
}

$pdf->SetFont('dejavusans', 'B', 14);
$pdf->Cell(0, 10, 'SİPARİŞ FORMU', 0, 1, 'C');

$pdf->SetFont('dejavusans', '', 10);
$pdf->Ln(10);

// Sipariş bilgilerini ekle
$pdf->SetFont('dejavusans', 'B', 10);
$pdf->Cell(40, 7, 'Sipariş No:', 0, 0);
$pdf->SetFont('dejavusans', '', 10);
$pdf->Cell(50, 7, $siparisNo, 0, 1);

$pdf->SetFont('dejavusans', 'B', 10);
$pdf->Cell(40, 7, 'Ad Soyad:', 0, 0);
$pdf->SetFont('dejavusans', '', 10);
$pdf->Cell(50, 7, $row['isim'] . ' ' . $row['soyisim'], 0, 1);

$pdf->SetFont('dejavusans', 'B', 10);
$pdf->Cell(40, 7, 'Telefon:', 0, 0);
$pdf->SetFont('dejavusans', '', 10);
$pdf->Cell(50, 7, $row['ceptel'], 0, 1);

$pdf->SetFont('dejavusans', 'B', 10);
$pdf->Cell(40, 7, 'Adres:', 0, 0);
$pdf->SetFont('dejavusans', '', 10);
$pdf->MultiCell(0, 7, $row['adres'] . ' - ' . $row['semt'], 0, 'L');

$pdf->SetFont('dejavusans', 'B', 10);
$pdf->Cell(40, 7, 'Ürün:', 0, 0);
$pdf->SetFont('dejavusans', '', 10);
$pdf->Cell(50, 7, $row['urun'], 0, 1);

$pdf->SetFont('dejavusans', 'B', 10);
$pdf->Cell(40, 7, 'Açıklama:', 0, 0);
$pdf->SetFont('dejavusans', '', 10);
$pdf->MultiCell(0, 7, strip_tags($row['aciklama']), 0, 'L');

// Tarih bilgileri
$pdf->SetFont('dejavusans', 'B', 10);
$pdf->Cell(40, 7, 'Sipariş Tarihi:', 0, 0);
$pdf->SetFont('dejavusans', '', 10);
$pdf->Cell(50, 7, $row['starih'] . ' ' . $row['s_time'], 0, 1);

$pdf->SetFont('dejavusans', 'B', 10);
$pdf->Cell(40, 7, 'Teslim Tarihi:', 0, 0);
$pdf->SetFont('dejavusans', '', 10);
$pdf->Cell(50, 7, $row['ttarih2'] . ' ' . $row['e_time'], 0, 1);

// Fiyat bilgileri
$pdf->Ln(7);
$pdf->SetFont('dejavusans', 'B', 10);
$pdf->Cell(40, 7, 'Fiyat:', 0, 0);
$pdf->SetFont('dejavusans', '', 10);
$pdf->Cell(50, 7, $row['fiyat'] . ' TL', 0, 1);

$pdf->SetFont('dejavusans', 'B', 10);
$pdf->Cell(40, 7, 'KDV Dahil Fiyat:', 0, 0);
$pdf->SetFont('dejavusans', '', 10);
$pdf->Cell(50, 7, $row['fiyat_sn'] . ' TL', 0, 1);

$pdf->SetFont('dejavusans', 'B', 10);
$pdf->Cell(40, 7, 'Kapora:', 0, 0);
$pdf->SetFont('dejavusans', '', 10);
$pdf->Cell(50, 7, $row['kapora'] . ' TL', 0, 1);

$pdf->SetFont('dejavusans', 'B', 10);
$pdf->Cell(40, 7, 'Kalan:', 0, 0);
$pdf->SetFont('dejavusans', '', 10);
$pdf->Cell(50, 7, $row['kalan'] . ' TL', 0, 1);

$pdf->SetFont('dejavusans', 'B', 10);
$pdf->Cell(40, 7, 'KDV Dahil Kalan:', 0, 0);
$pdf->SetFont('dejavusans', '', 10);
$pdf->Cell(50, 7, $row['kdvkalan'] . ' TL', 0, 1);

// İmza alanı
$pdf->Ln(20);
$pdf->Cell(95, 10, 'Karataşcam Adına', 0, 0, 'C');
$pdf->Cell(95, 10, 'Müşteri', 0, 1, 'C');
$pdf->Ln(15);
$pdf->Cell(95, 10, $row['satis'], 0, 0, 'C');
$pdf->Cell(95, 10, $row['isim'] . ' ' . $row['soyisim'], 0, 1, 'C');

// Firma bilgileri
$pdf->Ln(15);
$pdf->SetFont('dejavusans', 'B', 9);
$pdf->Cell(0, 7, 'Karataş Ayna Kristal Cam Mob. İnş. Nak. San. ve Tic. Ltd. Şti', 0, 1, 'C');
$pdf->SetFont('dejavusans', '', 9);
$pdf->Cell(0, 5, 'Çalım Sok. No:19 Siteler - Ankara / Türkiye', 0, 1, 'C');
$pdf->Cell(0, 5, 'T: +90 312 348 9162 | F: +90 312 348 7078', 0, 1, 'C');
$pdf->Cell(0, 5, 'info@karatascam.com.tr | www.karatascam.com.tr', 0, 1, 'C');

// Tamponlanmış tüm çıktıları temizle
ob_end_clean();

// PDF'i çıktıla
$pdf->Output('Siparis_' . $siparisNo . '.pdf', 'I');
?>