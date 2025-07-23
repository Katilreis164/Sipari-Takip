<?php
// Oturum kontrolü
session_start();
if(!isset($_SESSION['personel'])) {
    header("Location: login.php");
    exit;
}

// Veritabanı bağlantısı
require_once("baglanti.php");

// Tarih ayarları - Türkçe
setlocale(LC_TIME, 'tr_TR.UTF-8', 'tr_TR', 'tr', 'turkish');

// Siparişleri çek
$sql = "SELECT * FROM siparis ORDER BY starih DESC";
$result = mysqli_query($baglanti, $sql);
$siparisler = [];

// Durum sütunlarını tanımla
$durumlar = ['Üretim', 'Hazır', 'Tamamlandı', 'İptal', 'Tamamlanmadı!', 'DolmakÜzere!'];
foreach ($durumlar as $durum) {
    $siparisler[$durum] = [];
}

// Siparişleri durumlara göre grupla
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $durum = isset($row['durum']) ? $row['durum'] : 'Üretim';
        if (array_key_exists($durum, $siparisler)) {
            $siparisler[$durum][] = $row;
        } else {
            $siparisler['Üretim'][] = $row;
        }
    }
}

// Tarih formatını Türkçe'ye çeviren fonksiyon
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
    
    $gunler = array(
        'Monday' => 'Pazartesi',
        'Tuesday' => 'Salı',
        'Wednesday' => 'Çarşamba',
        'Thursday' => 'Perşembe',
        'Friday' => 'Cuma',
        'Saturday' => 'Cumartesi',
        'Sunday' => 'Pazar'
    );
    
    $date = new DateTime($tarih);
    $formatlanmisTarih = $date->format('d F Y');
    
    foreach ($aylar as $en => $tr) {
        $formatlanmisTarih = str_replace($en, $tr, $formatlanmisTarih);
    }
    
    return $formatlanmisTarih;
}

// Rastgele sipariş durumu seçen fonksiyon
function rastgeleDurum() {
    $durumlar = ['Üretim', 'Hazır', 'Tamamlandı'];
    return $durumlar[array_rand($durumlar)];
}

// Rastgele sipariş ürünü seçen fonksiyon
function rastgeleUrun() {
    $urunler = ['Cam Kapı', 'Ayna', 'Cam Balkon', 'Duşakabin', 'Cam Bölme', 'Küpeşte Cam', 'Vitrin Cam'];
    return $urunler[array_rand($urunler)];
}

// Rastgele saat oluşturan fonksiyon
function rastgeleSaat() {
    $saat = rand(8, 18);
    $dakika = rand(0, 59);
    return sprintf('%02d:%02d', $saat, $dakika);
}

// Bugünün tarihini al
$bugun = date('Y-m-d');

// Bugünün haftanın kaçıncı günü olduğunu bul (1: Pazartesi, 7: Pazar)
$bugunun_gunu = date('N'); 

// Haftanın başlangıç tarihini hesapla (Pazartesi)
$haftanin_baslangici = date('Y-m-d', strtotime("-" . ($bugunun_gunu - 1) . " days", strtotime($bugun)));

// Geçmiş 2 hafta ve gelecek 2 hafta için tarihler
$iki_hafta_once = date('Y-m-d', strtotime("-14 days", strtotime($haftanin_baslangici)));
$iki_hafta_sonra = date('Y-m-d', strtotime("+20 days", strtotime($haftanin_baslangici)));

// Tarih aralığını oluştur
$tarihler = array();
$bu_hafta_tarihleri = array();
$tum_tarihler = array();

// Mevcut haftanın (Bu Hafta) tarihlerini oluştur - 7 gün (Pazartesi-Pazar)
for ($i = 0; $i < 7; $i++) {
    $tarih = date('Y-m-d', strtotime("+" . $i . " days", strtotime($haftanin_baslangici)));
    $tarihler[] = $tarih;
    $bu_hafta_tarihleri[] = $tarih;
}

// Tüm tarihleri oluştur - 35 gün (geçmiş 2 hafta + bu hafta + gelecek 2 hafta)
$gecici_tarih = $iki_hafta_once;
while ($gecici_tarih <= $iki_hafta_sonra) {
    $tum_tarihler[] = $gecici_tarih;
    $gecici_tarih = date('Y-m-d', strtotime("+1 day", strtotime($gecici_tarih)));
}

// JavaScript için tarih dizilerini JSON formatına dönüştür
$js_bu_hafta_tarihleri = json_encode($bu_hafta_tarihleri);
$js_tum_tarihler = json_encode($tum_tarihler);

// Tüm siparişleri bir dizide topla
$tum_siparisler = array();

// Tüm siparişleri sıralı bir diziye topla
$tum_siparisler = [];
foreach ($siparisler as $durum => $siparis_listesi) {
    foreach ($siparis_listesi as $siparis) {
        $tum_siparisler[] = $siparis;
    }
}

// Tarihleri ve gün isimlerini oluştur
$gun_isimleri = array('Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi', 'Pazar');
$ay_isimleri = array('Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık');

?>
<!DOCTYPE html>
<html>
<head>
    <title>KARATAŞCAM - Kanban Sipariş Takip</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/tr.js"></script>
    <style>
        :root {
            --primary-color: #5e35b1;
            --secondary-color: #7e57c2;
            --accent-color: #7986cb;
            --background-color: #f5f7fa;
            --card-color: #ffffff;
            --text-primary: #333333;
            --text-secondary: #666666;
            --border-color: #e0e0e0;
            --header-bg: #4a148c;
            --shadow-sm: 0 2px 5px rgba(0,0,0,0.1);
            --shadow-md: 0 4px 10px rgba(0,0,0,0.12);
            --shadow-lg: 0 8px 16px rgba(0,0,0,0.16);
            --status-new: #5e35b1;
            --status-progress: #fb8c00;
            --status-ready: #43a047;
            --status-done: #1976d2;
            --status-canceled: #e53935;
            --status-hold: #f9a825;
        }
        
        body { 
            font-family: 'Segoe UI', Roboto, Arial, sans-serif; 
            margin: 0; 
            padding: 0; 
            background-color: var(--background-color);
            color: var(--text-primary);
        }
        
        .container { 
            width: 100%; 
            margin: 0 auto; 
            background-color: var(--background-color); 
            padding: 0; 
            box-sizing: border-box;
        }
        
        .app-header {
            background-color: var(--header-bg);
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow-md);
            border-bottom: 3px solid #7e57c2;
        }
        
        .header-controls {
            display: flex;
            align-items: center;
        }
        
        .title-text {
            font-size: 24px;
            font-weight: bold;
            color: white;
            display: flex;
            align-items: center;
        }
        
        .title-text i {
            margin-right: 12px;
            font-size: 28px;
        }
        
        .title-text span {
            color: #b39ddb;
            font-weight: 400;
        }
        
        .user-info { 
            display: flex;
            align-items: center;
            font-size: 14px;
        }
        
        .user-avatar {
            width: 32px;
            height: 32px;
            background-color: #7e57c2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-weight: bold;
            color: white;
            box-shadow: var(--shadow-sm);
        }
        
        .user-info span { 
            font-weight: 500; 
        }
        
        .logout { 
            color: #ffcdd2; 
            text-decoration: none; 
            margin-left: 15px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
        }
        
        .logout i {
            margin-right: 5px;
        }
        
        .logout:hover { 
            color: white;
        }
        
        .main-content {
            padding: 20px;
            max-width: 1600px;
            margin: 0 auto;
        }
        
        .menu { 
            background-color: #f0eafb;
            padding: 10px 20px;
            border-radius: 8px;
            display: flex; 
            justify-content: flex-start;
            margin: 0 0 20px 0; 
            flex-wrap: wrap;
            box-shadow: var(--shadow-sm);
        }
        
        .menu-item { 
            padding: 8px 16px; 
            background-color: transparent; 
            color: var(--primary-color); 
            text-decoration: none; 
            margin: 5px 10px; 
            border-radius: 30px;
            transition: all 0.3s;
            font-weight: 500;
            display: flex;
            align-items: center;
        }
        
        .menu-item i {
            margin-right: 8px;
        }
        
        .menu-item:hover { 
            background-color: rgba(126, 87, 194, 0.1);
        }
        
        .active {
            background-color: var(--primary-color);
            color: white;
        }
        
        .active:hover {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .tab-container {
            margin-bottom: 20px;
        }
        
        .tabs {
            display: flex;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            margin-bottom: 20px;
        }
        
        .tab {
            padding: 12px 20px;
            cursor: pointer;
            font-weight: 500;
            color: var(--text-secondary);
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
            display: flex;
            align-items: center;
        }
        
        .tab i {
            margin-right: 8px;
        }
        
        .tab:hover {
            background-color: #f5f5f5;
            color: var(--primary-color);
        }
        
        .tab.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
            background-color: transparent;
        }
        
        .filters-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .filter-group {
            display: flex;
            align-items: center;
            background-color: white;
            border-radius: 8px;
            padding: 5px;
            box-shadow: var(--shadow-sm);
            margin-right: 10px;
            margin-bottom: 10px;
        }
        
        .filter-label {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            font-size: 14px;
            color: var(--text-secondary);
        }
        
        .filter-label i {
            margin-right: 6px;
        }
        
        .filter-btn {
            padding: 8px 15px;
            background-color: transparent;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            color: var(--text-secondary);
            transition: all 0.2s;
            font-size: 14px;
        }
        
        .filter-btn:hover, .filter-btn.active {
            background-color: rgba(94, 53, 177, 0.1);
            color: var(--primary-color);
        }
        
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .page-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
            display: flex;
            align-items: center;
        }
        
        .page-title i {
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        .action-group {
            display: flex;
        }
        
        .action-btn {
            background-color: var(--primary-color);
            color: white;
            padding: 10px 16px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            font-weight: 500;
            transition: all 0.3s;
            margin-left: 10px;
            box-shadow: var(--shadow-sm);
        }
        
        .action-btn.secondary {
            background-color: white;
            color: var(--primary-color);
            border: 1px solid var(--border-color);
        }
        
        .action-btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        
        .action-btn.secondary:hover {
            background-color: #f5f5f5;
            color: var(--secondary-color);
        }
        
        .action-btn i {
            margin-right: 8px;
        }

        /* Kanban Tablo Stili */
        .project-container {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            background-color: white;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        
        .project-container:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .project-table {
            border-collapse: separate;
            border-spacing: 0;
            width: auto;
            min-width: 100%;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .project-table th, .project-table td {
            border: 1px solid var(--border-color);
            padding: 0;
            text-align: center;
            position: relative;
        }
        
        .project-table th {
            background-color: #f7f5ff;
            height: 68px;
            font-weight: 500;
            position: sticky;
            top: 0;
            z-index: 10;
            color: var(--primary-color);
            border-bottom: 2px solid #e0e0ff;
            transition: all 0.3s ease;
            min-width: 100px;
        }
        
        .project-table th:hover {
            background-color: #f0ebfd;
        }
        
        .project-table th:first-child {
            text-align: left;
            padding-left: 15px;
            background-color: #ede7f6;
            width: 270px;
            min-width: 270px;
        }
        
        .project-table td:first-child {
            text-align: left;
            padding: 0;
            background-color: #f9f7ff;
            width: 270px;
            min-width: 270px;
        }
        
        .project-table td {
            height: 70px;
            min-width: 100px;
            transition: all 0.3s ease;
        }
        
        .project-table tr:nth-child(odd) td {
            background-color: #fcfbff;
        }
        
        .project-table tr:nth-child(odd) td:first-child {
            background-color: #f5f0ff;
        }
        
        .project-table tr:nth-child(even) td:first-child {
            background-color: #f9f7ff;
        }
        
        .project-table tr:hover td {
            background-color: #f0ebfd !important;
        }
        
        .date-header {
            display: flex;
            flex-direction: column;
            text-align: center;
            min-width: 100px;
            padding: 5px;
            height: 100%;
            justify-content: center;
        }
        
        .day-name {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 5px;
            display: block;
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
        }
        
        .date-value {
            font-size: 16px;
            position: relative;
            padding: 2px 0;
        }
        
        .month-name {
            display: block;
            font-size: 12px;
            color: #666;
            margin-top: 4px;
        }
        
        /* Bugün için vurgulama */
        .today-column {
            background-color: rgba(255, 220, 40, 0.15);
        }
        
        .today-column .date-header {
            background-color: #ffc107;
            color: #000;
            padding: 5px;
            border-radius: 5px;
        }
        
        /* Haftalık görünüm için stil */
        .weekly-view .date-col:not(.show-in-weekly) {
            display: none;
        }
        
        /* Tüm tarihler görünümü için stil */
        .all-dates-view .date-col {
            display: table-cell !important;
        }
        
        /* Tarih hücreleri için düzeltmeler */
        .date-col {
            min-width: 130px !important;
            width: 130px !important;
        }
        
        /* Bu hafta vurgusu */
        .bu-hafta {
            border-top: 3px solid var(--primary-color);
            box-shadow: inset 0 4px 8px -4px rgba(126, 87, 194, 0.1);
        }
        
        /* Bugün vurgusu */
        .today-header {
            background-color: #ede7f6 !important;
            box-shadow: inset 0 4px 8px -4px rgba(126, 87, 194, 0.2);
        }
        
        .today-header .date-value {
            color: var(--primary-color);
            font-weight: 700;
        }
        
        .today-header .day-name {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .today-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: var(--primary-color);
        }
        
        .today-column {
            background-color: rgba(126, 87, 194, 0.05) !important;
        }
        
        /* Durum sembolleri iyileştirme */
        .status-symbol {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            transition: all 0.2s ease;
        }
        
        .status-symbol i {
            font-size: 18px;
            opacity: 0.9;
            background-color: rgba(255,255,255,0.8);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .status-symbol.uretim i {
            color: #fb8c00;
        }
        
        .status-symbol.hazir i {
            color: #43a047;
        }
        
        .status-symbol.tamamlandi i {
            color: #1976d2;
        }
        
        .status-symbol.iptal i {
            color: #e53935;
        }
        
        .status-symbol:hover i {
            transform: scale(1.2);
            box-shadow: 0 5px 10px rgba(0,0,0,0.15);
        }
        
        .customer-info {
            cursor: pointer;
            position: relative;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 10px 15px;
            box-sizing: border-box;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            border-radius: 0 4px 4px 0;
        }
        
        .customer-info:hover {
            transform: translateX(4px);
            border-left-color: var(--primary-color);
            background-color: #ede7f6;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .customer-name {
            font-weight: 600;
            font-size: 15px;
            color: var(--text-primary);
            margin-bottom: 3px;
            display: flex;
            align-items: center;
        }
        
        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .status-dot.uretim {
            background-color: #fb8c00;
        }
        
        .status-dot.hazir {
            background-color: #43a047;
        }
        
        .status-dot.tamamlandi {
            background-color: #1976d2;
        }
        
        .status-dot.iptal {
            background-color: #e53935;
        }
        
        .status-dot.tamamlanmadi {
            background-color: #ff9800;
        }
        
        .status-dot.dolmakuzere {
            background-color: #9c27b0;
        }
        
        /* Takvim görünümleri için iyileştirmeler */
        .weekly-view {
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: auto;
            padding-bottom: 10px;
            scrollbar-width: thin;
            scrollbar-color: var(--primary-color) #f0f0f0;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
        }
        
        .all-dates-view {
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: auto;
            padding-bottom: 10px;
            scrollbar-width: thin;
            scrollbar-color: var(--primary-color) #f0f0f0;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
        }
        
        /* Webkit tabanlı tarayıcılar için kaydırma çubuğu stilleri */
        .weekly-view::-webkit-scrollbar,
        .all-dates-view::-webkit-scrollbar {
            height: 8px;
        }
        
        .weekly-view::-webkit-scrollbar-track,
        .all-dates-view::-webkit-scrollbar-track {
            background: #f0f0f0;
            border-radius: 4px;
        }
        
        .weekly-view::-webkit-scrollbar-thumb,
        .all-dates-view::-webkit-scrollbar-thumb {
            background-color: var(--primary-color);
            border-radius: 4px;
        }
        
        /* Hafta sonu stilleri */
        th.date-col[data-day="6"], th.date-col[data-day="7"] {
            background-color: #f5f0ff;
        }
        
        td[data-day="6"], td[data-day="7"] {
            background-color: #fcf8ff !important;
        }
        
        /* Bugün için vurgu */
        .today-header {
            background-color: #ede7f6 !important;
            box-shadow: inset 0 4px 8px -4px rgba(126, 87, 194, 0.2);
        }
        
        .today-header .date-value {
            color: var(--primary-color);
            font-weight: 700;
        }
        
        .today-header .day-name {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .today-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: var(--primary-color);
        }
        
        .today-column {
            background-color: rgba(126, 87, 194, 0.05) !important;
        }
        
        /* Ay-gün görünümü */
        .date-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 10px 0;
            height: 100%;
            box-sizing: border-box;
        }
        
        .day-name {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #555;
            margin-bottom: 5px;
            white-space: nowrap;
            width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .date-value {
            position: relative;
            display: block;
            padding: 5px 8px;
            border-radius: 4px;
            background-color: rgba(255, 255, 255, 0.7);
            font-size: 16px;
            font-weight: 600;
            min-width: 36px;
            text-align: center;
        }
        
        .month-name {
            display: block;
            font-size: 11px;
            color: var(--text-secondary);
            margin-top: 5px;
            width: 100%;
            text-align: center;
        }
        
        /* Takvim animasyonları */
        @keyframes dateHover {
            0% { transform: translateY(0); }
            50% { transform: translateY(-3px); }
            100% { transform: translateY(0); }
        }
        
        .date-header:hover .date-value {
            animation: dateHover 0.5s ease;
        }
        
        /* Liste görünümü için CSS */
        .project-table.list-view {
            display: table;
            width: 100%;
        }
        
        .project-table.list-view th,
        .project-table.list-view td {
            display: table-cell;
            width: auto;
        }
        
        /* İyileştirilmiş sabit sütun stilleri */
        .sticky-col {
            position: relative !important;
            left: auto !important;
            z-index: 1 !important;
            background-color: inherit !important;
            box-shadow: none !important;
        }
        
        /* İlk sütun için padding ayarını kaldır */
        .project-container {
            padding-left: 0 !important;
            position: relative !important;
            overflow-x: auto !important;
        }
        
        /* Temizleme - Kullanılmayan sınıfları kaldır */
        .customer-cell-clone, .fixed-sidebar {
            display: none !important;
        }
        
        /* Animasyonlar */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(15px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        @keyframes pulse {
            0% { transform: scale(1); box-shadow: var(--shadow-sm); }
            50% { transform: scale(1.05); box-shadow: var(--shadow-md); }
            100% { transform: scale(1); box-shadow: var(--shadow-sm); }
        }
        
        @keyframes slideIn {
            from { transform: translateX(-20px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        .pulse {
            animation: pulse 2s infinite;
        }
        
        .animate-slideIn {
            animation: slideIn 0.5s forwards;
        }
        
        .shimmer {
            position: relative;
            overflow: hidden;
        }
        
        .shimmer::after {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            transform: translateX(-100%);
            background-image: linear-gradient(
                90deg,
                rgba(255, 255, 255, 0) 0,
                rgba(255, 255, 255, 0.2) 20%,
                rgba(255, 255, 255, 0.5) 60%,
                rgba(255, 255, 255, 0)
            );
            animation: shimmer 2s infinite;
            content: '';
        }
        
        @keyframes shimmer {
            100% { transform: translateX(100%); }
        }
        
        /* Responsive */
        @media screen and (max-width: 992px) {
            .project-table th:first-child,
            .project-table td:first-child {
                width: 220px;
            }
            
            .filter-group {
                margin-bottom: 10px;
            }
        }
        
        @media screen and (max-width: 768px) {
            .menu, .action-bar, .filters-bar {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filter-group, .action-group {
                margin-top: 10px;
            }
            
            .action-btn {
                margin-left: 0;
                margin-top: 10px;
                text-align: center;
            }
        }

        /* Tablo görünümleri */
        .weekly-view, .monthly-view, .all-dates-view {
            width: 100%;
            overflow-x: auto;
            padding-bottom: 10px;
            scrollbar-width: thin;
            scrollbar-color: var(--primary-color) #f0f0f0;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
        }

        /* Tablo hücre genişlikleri */
        .date-col {
            min-width: 130px !important;
            width: 130px !important;
        }

        /* Tablo başlığı ilk sütun */
        .project-table th:first-child {
            width: 250px;
            min-width: 250px;
        }

        /* Tablo veri hücresi ilk sütun */
        .project-table td:first-child {
            width: 250px;
            min-width: 250px;
        }

        /* Ay görünümü için stil */
        .monthly-view .date-col {
            display: table-cell !important;
        }

        /* Tüm tarihler görünümü için stil */
        .all-dates-view .date-col {
            display: table-cell !important;
        }

        /* Haftalık görünüm için stil - yalnızca bu haftanın günlerini göster */
        .weekly-view .date-col {
            display: none;
        }

        .weekly-view .bu-hafta {
            display: table-cell !important;
        }

        .weekly-view td[data-date] {
            display: none;
        }

        .weekly-view td.bu-hafta-column {
            display: table-cell !important;
        }

        /* Bu Ay görünümü için CSS */
        .monthly-view {
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: auto;
            padding-bottom: 10px;
            scrollbar-width: thin;
            scrollbar-color: var(--primary-color) #f0f0f0;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
        }
        
        /* Aylık görünümde tüm tarihler göster */
        .monthly-view .project-table th.date-col,
        .monthly-view .project-table td[data-date] {
            display: table-cell !important;
        }
        
        /* Tüm tarihler görünümünde tüm hücreler göster */
        .all-dates-view .project-table th.date-col,
        .all-dates-view .project-table td[data-date] {
            display: table-cell !important;
        }

        /* Haftalık görünümde hücre düzenlemeleri */
        .weekly-view .project-table th.date-col {
            display: none;
        }
        
        .weekly-view .project-table th.bu-hafta {
            display: table-cell !important;
        }
        
        .weekly-view .project-table td[data-date] {
            display: none;
        }
        
        .weekly-view .project-table td.bu-hafta-column {
            display: table-cell !important;
        }

        /* Tarih başlıkları daha net görünüm için */
        th.date-col {
            padding: 0 !important;
        }
        
        .date-header {
            padding: 6px 2px !important;
        }

        /* İlk sütunun arkaplan renklerini koruma */
        .project-table th.fixed-column {
            background-color: #ede7f6 !important;
        }
        
        .project-table td.fixed-column {
            background-color: #f9f7ff !important;
        }
        
        .project-table tr:nth-child(odd) td.fixed-column {
            background-color: #f5f0ff !important;
        }
        
        /* İlk sütuna yer açma */
        .first-col-space {
            min-width: 250px;
            width: 250px;
            visibility: hidden;
        }

        /* Sabit kenar çubuğu stillerini kaldır - artık kullanılmıyor */
        .fixed-sidebar, 
        .project-table-container,
        .sidebar-header,
        .fixed-sidebar .customer-row {
            display: none !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="app-header">
            <div class="title-text">
                <i class="fas fa-tasks"></i>
                KARATAŞ<span>CAM</span>
            </div>
            <div class="header-controls">
                <div class="user-info">
                    <div class="user-avatar">
                        <?php echo substr($_SESSION['personel'], 0, 1); ?>
                    </div>
                    <span><?php echo $_SESSION['personel']; ?></span>
                    <a href="logout.php" class="logout">
                        <i class="fas fa-sign-out-alt"></i> Çıkış
                    </a>
                </div>
            </div>
        </div>
        
        <div class="main-content">
            <div class="menu">
                <a href="index.php" class="menu-item">
                    <i class="fas fa-home"></i> Ana Sayfa
                </a>
                <a href="giris.php" class="menu-item">
                    <i class="fas fa-plus-circle"></i> Yeni Sipariş
                </a>
                <a href="listele.php" class="menu-item">
                    <i class="fas fa-calendar-alt"></i> Sipariş Takvim
                </a>
                <a href="listele.php" class="menu-item">
                    <i class="fas fa-clipboard-list"></i> Sipariş Takip
                </a>
                <a href="kanban.php" class="menu-item active">
                    <i class="fas fa-columns"></i> Kanban Görünümü
                </a>
            </div>
            
            <div class="tab-container">
                <div class="tabs">
                    <div class="tab active">
                        <i class="fas fa-columns"></i> Kanban Görünümü
                    </div>
                    <div class="tab">
                        <i class="fas fa-list"></i> Liste Görünümü
                    </div>
                    <div class="tab">
                        <i class="fas fa-calendar-week"></i> Haftalık Görünüm
                    </div>
                </div>
            </div>
            
            <div class="filters-bar">
                <div class="filter-group">
                    <div class="filter-label">
                        <i class="fas fa-filter"></i> Duruma Göre:
                    </div>
                    <button class="filter-btn active" data-filter="all">Tümü</button>
                    <?php foreach ($durumlar as $durum): ?>
                        <button class="filter-btn" data-filter="<?php echo strtolower(str_replace(['ı', 'İ', 'ü', 'Ü', 'ö', 'Ö', 'ç', 'Ç', 'ş', 'Ş', 'ğ', 'Ğ', ' ', '!'], ['i', 'i', 'u', 'u', 'o', 'o', 'c', 'c', 's', 's', 'g', 'g', '', ''], $durum)); ?>"><?php echo $durum; ?></button>
                    <?php endforeach; ?>
                </div>
                
                <div class="filter-group">
                    <div class="filter-label">
                        <i class="fas fa-calendar"></i> Tarihe Göre:
                    </div>
                    <button class="filter-btn active" data-filter="bu-hafta">Bu Hafta</button>
                    <button class="filter-btn" data-filter="bu-ay">Bu Ay</button>
                    <button class="filter-btn" data-filter="tum-tarihler">Tüm Tarihler</button>
                </div>
            </div>
            
            <div class="action-bar">
                <h2 class="page-title">
                    <i class="fas fa-clipboard-check"></i> Sipariş Durumu İzleme
                </h2>
                
                <div class="action-group">
                    <a href="#" class="action-btn secondary">
                        <i class="fas fa-print"></i> Rapor Al
                    </a>
                    <a href="giris.php" class="action-btn pulse">
                        <i class="fas fa-plus-circle"></i> Yeni Sipariş Ekle
                    </a>
                </div>
            </div>
            
            <?php
            // Siparişlerin tarih haritasını oluştur (tarih -> siparişler dizisi)
            $tarih_siparis_haritasi = [];
            foreach ($tum_tarihler as $tarih) {
                $tarih_siparis_haritasi[$tarih] = [];
            }
            
            // Her siparişi tarihlere göre haritala
            foreach ($tum_siparisler as $siparis) {
                $siparis_tarih = isset($siparis['starih']) ? $siparis['starih'] : '';
                $teslim_tarih = isset($siparis['ttarih2']) ? $siparis['ttarih2'] : '';
                
                if (!empty($siparis_tarih)) {
                    $siparis_date = new DateTime($siparis_tarih);
                    $siparis_date_str = $siparis_date->format('Y-m-d');
                    
                    // Eğer sipariş tarihi bizim aralığımızda ise, haritaya ekle
                    if (in_array($siparis_date_str, $tum_tarihler)) {
                        $tarih_siparis_haritasi[$siparis_date_str][] = $siparis;
                    }
                }
                
                // Teslim tarihi de ayrı bir etkinlik olarak ekle
                if (!empty($teslim_tarih)) {
                    $teslim_date = new DateTime($teslim_tarih);
                    $teslim_date_str = $teslim_date->format('Y-m-d');
                    
                    if (in_array($teslim_date_str, $tum_tarihler)) {
                        // Teslim tarihi bilgisini ekle
                        $siparis['etkinlik_turu'] = 'teslim';
                        $tarih_siparis_haritasi[$teslim_date_str][] = $siparis;
                    }
                }
            }
            ?>
            
            <div class="project-container">
                <table class="project-table">
                    <thead>
                        <tr>
                            <th class="sticky-col project-col">Sipariş</th>
                            <?php foreach ($tum_tarihler as $index => $tarih): ?>
                            <?php 
                                // Tarihi parse et
                                $date = new DateTime($tarih);
                                $gun_no = date('N', strtotime($tarih)) - 1; // 0: Pazartesi, 6: Pazar
                                $gun_ismi = $gun_isimleri[$gun_no];
                                $tarih_formati = $date->format('j'); // Gün (1-31)
                                $ay_ismi = $ay_isimleri[date('n', strtotime($tarih)) - 1];
                                
                                // Sınıfları belirle
                                $class_names = [];
                                if ($tarih == $bugun) $class_names[] = 'today-header';
                                if (in_array($tarih, $bu_hafta_tarihleri)) $class_names[] = 'bu-hafta';
                                $class_str = !empty($class_names) ? implode(' ', $class_names) : '';
                                
                                // Bu Hafta görünümü için varsayılan olarak tüm tarihler gizli olmasın
                                // Bunun yerine JavaScript tarafında gösterilecek/gizlenecek
                                $style = '';
                            ?>
                            <th class="date-col <?php echo $class_str; ?>" data-date="<?php echo $tarih; ?>" data-day="<?php echo date('N', strtotime($tarih)); ?>" style="<?php echo $style; ?>">
                                <div class="date-header">
                                    <span class="day-name"><?php echo $gun_ismi; ?></span>
                                    <div class="date-value">
                                        <?php echo $tarih_formati; ?>
                                        <span class="month-name"><?php echo $ay_ismi; ?></span>
                                    </div>
                                </div>
                            </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($siparisler as $durum => $durum_siparisleri): ?>
                            <?php foreach ($durum_siparisleri as $siparis_index => $siparis): 
                                // Durum sınıfını belirle
                                $durum_class = strtolower(str_replace(['ı', 'İ', 'ü', 'Ü', 'ö', 'Ö', 'ç', 'Ç', 'ş', 'Ş', 'ğ', 'Ğ', ' ', '!'], 
                                                    ['i', 'i', 'u', 'u', 'o', 'o', 'c', 'c', 's', 's', 'g', 'g', '', ''], 
                                                    $durum));
                                
                                // Sipariş verileri
                                $siparis_no = isset($siparis['yilsiparis']) ? $siparis['yilsiparis'] : 'N/A';
                                $musteri_ismi = isset($siparis['isim']) ? $siparis['isim'] . ' ' . (isset($siparis['soyisim']) ? $siparis['soyisim'] : '') : 'İsimsiz';
                                $urun_adi = isset($siparis['urun']) ? $siparis['urun'] : '';
                            ?>
                            <tr data-id="<?php echo $siparis_no; ?>" data-status="<?php echo $durum_class; ?>">
                                <td class="sticky-col project-col">
                                    <div class="customer-info">
                                        <div class="customer-name">
                                            <span class="status-dot <?php echo $durum_class; ?>"></span>
                                            <?php echo $musteri_ismi; ?>
                                        </div>
                                        <div class="order-id">Sipariş No: <?php echo $siparis_no; ?></div>
                                        <div class="order-product"><?php echo $urun_adi; ?></div>
                                    </div>
                                </td>
                                
                                <?php foreach ($tum_tarihler as $tarih_index => $tarih): 
                                    // Sınıfları belirle
                                    $td_class_names = [];
                                    if ($tarih == $bugun) $td_class_names[] = 'today-column';
                                    if (in_array($tarih, $bu_hafta_tarihleri)) $td_class_names[] = 'bu-hafta-column';
                                    $td_class_str = !empty($td_class_names) ? implode(' ', $td_class_names) : '';
                                    
                                    // Tüm tarihleri göstermek için style özelliğini boş bırakıyoruz
                                    // JavaScript'te filtreleme yapacağız
                                    $td_style = '';
                                    
                                    // Durum sembolü gösterim kontrolü
                                    $unique_value = ($siparis_index * count($tum_tarihler)) + $tarih_index;
                                    $show_status = ($unique_value % 4 == 0); // Her 4 hücreden 1'inde göster
                                    
                                    // İkon seçimi
                                    $icon = 'circle';
                                    if ($durum_class == 'uretim') $icon = 'industry';
                                    elseif ($durum_class == 'hazir') $icon = 'box-open';
                                    elseif ($durum_class == 'tamamlandi') $icon = 'clipboard-check';
                                    elseif ($durum_class == 'iptal') $icon = 'ban';
                                    elseif ($durum_class == 'tamamlanmadi') $icon = 'exclamation-triangle';
                                    elseif ($durum_class == 'dolmakuzere') $icon = 'shipping-fast';
                                ?>
                                <td class="<?php echo $td_class_str; ?>" data-date="<?php echo $tarih; ?>" data-day="<?php echo date('N', strtotime($tarih)); ?>" style="<?php echo $td_style; ?>">
                                    <?php if ($show_status): ?>
                                    <div class="status-symbol <?php echo $durum_class; ?>">
                                        <i class="fas fa-<?php echo $icon; ?>" title="<?php echo $durum; ?>"></i>
                                    </div>
                                    <?php endif; ?>
                                </td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="company-info">
                <div style="font-weight: 500; margin-bottom: 5px;">Karataş Ayna Kristal Cam Mob. İnş. Nak. San. ve Tic. Ltd. Şti.</div>
                Çalım Sok. No:19 Siteler - Ankara / Türkiye<br>
                T: +90 312 348 9162 | F: +90 312 348 7078<br>
                info@karatascam.com.tr | www.karatascam.com.tr
                
                <div class="logos">
                    <img src="image/yetkinlik.png" alt="Yetkinlik Logoları">
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        console.log("Kanban sayfası yüklendi");
        
        // PHP tarafından gelen tarih dizilerini JavaScript'e aktarıyoruz
        var buHaftaTarihleri = <?php echo $js_bu_hafta_tarihleri; ?>;
        var tumTarihler = <?php echo $js_tum_tarihler; ?>;
        
        console.log("Bu hafta tarihleri:", buHaftaTarihleri);
        console.log("Tüm tarihler sayısı:", tumTarihler.length);
        
        // Sayfa yüklendiğinde varsayılan olarak "Bu Hafta" görünümünü göster
        showWeeklyView();
        
        // Durum filtre düğmelerine tıklama olaylarını ekle
        $('.filter-group:eq(0) .filter-btn').click(function() {
            $('.filter-group:eq(0) .filter-btn').removeClass('active');
            $(this).addClass('active');
            
            var status = $(this).data('filter');
            filterByStatus(status);
        });
        
        // Tarih filtre düğmelerine tıklama olaylarını ekle
        $('.filter-group:eq(1) .filter-btn').click(function() {
            $('.filter-group:eq(1) .filter-btn').removeClass('active');
            $(this).addClass('active');
            
            var dateFilter = $(this).data('filter');
            
            if(dateFilter === "bu-hafta") {
                showWeeklyView();
            } else if(dateFilter === "tum-tarihler") {
                showAllDatesView();
            } else if(dateFilter === "bu-ay") {
                showMonthlyView();
            }
        });
        
        // Tab düğmelerine tıklama olaylarını ekle
        $('.tab').click(function() {
            $('.tab').removeClass('active');
            $(this).addClass('active');
            
            var tabIndex = $(this).index();
            if(tabIndex === 0) {
                // Kanban görünümü - varsayılan
                resetAllViews();
                $('.filter-group:eq(1) .filter-btn[data-filter="bu-hafta"]').click();
            } else if(tabIndex === 1) {
                // Liste görünümü
                showListView();
            } else if(tabIndex === 2) {
                // Haftalık görünüm
                showWeeklyView();
            }
        });
        
        // Tüm görünümleri sıfırla
        function resetAllViews() {
            $('.project-table').removeClass('list-view');
            $('.project-container').removeClass('weekly-view monthly-view all-dates-view');
            $('.project-table th, .project-table td').show();
        }
        
        // Durum filtreleme fonksiyonu
        function filterByStatus(status) {
            if(status === "all") {
                // Tüm siparişleri göster
                $('.project-table tbody tr').show();
            } else {
                // Sadece belirli durumda olanları göster
                $('.project-table tbody tr').hide();
                
                // Her satırda durum kontrolü yap
                $('.project-table tbody tr').each(function() {
                    var rowStatus = $(this).attr('data-status');
                    if(rowStatus && rowStatus.toLowerCase().indexOf(status.toLowerCase()) > -1) {
                        $(this).show();
                    }
                });
            }
        }
        
        // Bu Hafta görünümünü göster
        function showWeeklyView() {
            // Önce görünümleri sıfırla
            resetAllViews();
            
            // Haftalık görünümü etkinleştir
            $('.project-container').addClass('weekly-view');
            
            // Bugünün sütununu vurgula
            highlightToday();
            
            // Scroll pozisyonunu bugünün olduğu yere getir
            setTimeout(function() {
                scrollToCurrent();
            }, 100);
        }
        
        // Bu Ay görünümünü göster
        function showMonthlyView() {
            // Önce görünümleri sıfırla
            resetAllViews();
            
            // Aylık görünümü etkinleştir
            $('.project-container').addClass('monthly-view');
            
            // Bugünün sütununu vurgula
            highlightToday();
            
            // Scroll pozisyonunu bugünün olduğu yere getir
            setTimeout(function() {
                scrollToCurrent();
            }, 100);
        }
        
        // Tüm Tarihler görünümünü göster
        function showAllDatesView() {
            // Önce görünümleri sıfırla
            resetAllViews();
            
            // Tüm tarihler görünümünü etkinleştir
            $('.project-container').addClass('all-dates-view');
            
            // Bugünün sütununu vurgula
            highlightToday();
            
            // Scroll pozisyonunu bugünün olduğu yere getir
            setTimeout(function() {
                scrollToCurrent();
            }, 100);
        }
        
        // Liste görünümünü göster
        function showListView() {
            // Önce görünümleri sıfırla
            resetAllViews();
            
            // Liste görünümünü etkinleştir
            $('.project-table').addClass('list-view');
            
            // Tüm sütunları göster
            $('.project-table th, .project-table td').show();
        }
        
        // Bugünün sütununu vurgula
        function highlightToday() {
            var today = new Date().toISOString().split('T')[0]; // YYYY-MM-DD formatı
            $('.project-table th[data-date="' + today + '"]').addClass('today-header');
            $('.project-table td[data-date="' + today + '"]').addClass('today-column');
        }
        
        // Bugünün olduğu yere scroll yap
        function scrollToCurrent() {
            var today = new Date().toISOString().split('T')[0]; // YYYY-MM-DD formatı
            var todayCol = $('.project-table th[data-date="' + today + '"]');
            
            if(todayCol.length > 0) {
                var container = $('.project-container');
                var scrollTo = todayCol.offset().left - container.offset().left + container.scrollLeft() - (container.width() / 2) + (todayCol.width() / 2);
                
                container.animate({
                    scrollLeft: Math.max(0, scrollTo)
                }, 300);
            }
        }
        
        // Sayfa yüklendiğinde bugünün sütununu vurgula
        highlightToday();
    });
    </script>
</body>
</html>