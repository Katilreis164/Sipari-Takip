<?php
// Oturum kontrolü include.php içinde yapılıyor
include('include.php');
include('templates.php');
require_once("baglanti.php"); // Veritabanı bağlantısı

// Sipariş istatistikleri
try {
    $toplam_siparis = 0;
    $tamamlanan_siparis = 0;
    $bekleyen_siparis = 0;
    $iptal_siparis = 0;
    
    if ($baglanti) {
        // Toplam sipariş sayısı
        $sql_toplam = "SELECT COUNT(*) as toplam FROM siparis";
        $result_toplam = mysqli_query($baglanti, $sql_toplam);
        $row_toplam = mysqli_fetch_assoc($result_toplam);
        $toplam_siparis = $row_toplam['toplam'];
        
        // Tamamlanan sipariş sayısı
        $sql_tamamlanan = "SELECT COUNT(*) as toplam FROM siparis WHERE durum = 'Tamamlandı'";
        $result_tamamlanan = mysqli_query($baglanti, $sql_tamamlanan);
        $row_tamamlanan = mysqli_fetch_assoc($result_tamamlanan);
        $tamamlanan_siparis = $row_tamamlanan['toplam'];
        
        // Üretimdeki sipariş sayısı
        $sql_bekleyen = "SELECT COUNT(*) as toplam FROM siparis WHERE durum = 'Üretim'";
        $result_bekleyen = mysqli_query($baglanti, $sql_bekleyen);
        $row_bekleyen = mysqli_fetch_assoc($result_bekleyen);
        $bekleyen_siparis = $row_bekleyen['toplam'];
        
        // İptal edilen sipariş sayısı
        $sql_iptal = "SELECT COUNT(*) as toplam FROM siparis WHERE durum = 'İptal'";
        $result_iptal = mysqli_query($baglanti, $sql_iptal);
        $row_iptal = mysqli_fetch_assoc($result_iptal);
        $iptal_siparis = $row_iptal['toplam'];
    }
} catch (Exception $e) {
    // Hata durumunda sessizce devam et
}
?>

<!-- Butonlar -->
<div class="quick-actions">
    <div class="action-buttons">
        <a href="listele.php" class="action-button">
            <i class="fas fa-list"></i>
            <span>Sipariş Listesi</span>
        </a>
            </div>
        </div>
        
<!-- Sipariş Durumu Kartları -->
<div class="status-cards">
    <div class="status-card">
        <div class="status-icon">
            <i class="fas fa-clipboard-list"></i>
        </div>
        <div class="status-details">
            <div class="status-count"><?php echo $toplam_siparis; ?></div>
            <div class="status-label">Toplam Sipariş</div>
        </div>
    </div>
    
    <div class="status-card">
        <div class="status-icon">
            <i class="fas fa-industry"></i>
        </div>
        <div class="status-details">
            <div class="status-count"><?php echo $bekleyen_siparis; ?></div>
            <div class="status-label">Üretimdeki Sipariş</div>
        </div>
    </div>
    
    <div class="status-card">
        <div class="status-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="status-details">
            <div class="status-count"><?php echo $tamamlanan_siparis; ?></div>
            <div class="status-label">Tamamlanan Sipariş</div>
        </div>
    </div>
    
    <div class="status-card">
        <div class="status-icon">
            <i class="fas fa-ban"></i>
        </div>
        <div class="status-details">
            <div class="status-count"><?php echo $iptal_siparis; ?></div>
            <div class="status-label">İptal Edilen Sipariş</div>
        </div>
    </div>
</div>

<div class="search-container">
    <h2 class="search-title">Sipariş Arama ve Yönetim</h2>
        
    <div class="form-grid">
        <div class="form-group">
        <form action="aramasonuc.php" method="POST">
                <label for="siparis_no"><i class="fas fa-search"></i> Sipariş Numarasına göre ara:</label>
                <div class="input-button-group">
                    <input type="text" id="siparis_no" name="siparis_no" placeholder="Sipariş numarası girin...">
                    <button type="submit" class="btn"><i class="fas fa-search"></i> Ara</button>
                </div>
        </form>
        </div>
        
        <div class="form-group">
        <form action="aramasonuc.php" method="POST">
                <label for="satis_kisi"><i class="fas fa-user"></i> Sipariş Alan Kişiye göre ara:</label>
                <div class="input-button-group">
            <select id="satis_kisi" name="satis_kisi">
                <option value="Seçiniz">Seçiniz</option>
                <option value="Sercan Temel">Sercan Temel</option>
                <option value="Elif Akyol">Elif Akyol</option>
            </select>
                    <button type="submit" class="btn"><i class="fas fa-search"></i> Ara</button>
                </div>
        </form>
        </div>
        
        <div class="form-group">
        <form action="aramasonuc.php" method="POST">
                <label for="musteri_isim"><i class="fas fa-address-card"></i> Müşteri İsim VEYA Soyadına göre ara:</label>
                <div class="input-button-group">
                    <input type="text" id="musteri_isim" name="musteri_isim" placeholder="Müşteri adı veya soyadı girin...">
                    <button type="submit" class="btn"><i class="fas fa-search"></i> Ara</button>
                </div>
        </form>
        </div>
        
        <div class="form-group">
        <form action="resimekle.php" method="POST">
                <label for="resim_ekle"><i class="fas fa-image"></i> Siparişe Resim Ekle:</label>
                <div class="input-button-group">
                    <input type="text" id="resim_ekle" name="resim_ekle" placeholder="Sipariş numarası girin...">
                    <button type="submit" class="btn"><i class="fas fa-upload"></i> Göster</button>
                </div>
        </form>
        </div>
    </div>
</div>

<style>
    /* Hızlı İşlemler Stili */
    .quick-actions {
        background: transparent; /* Arka planı şeffaf yap */
        border-radius: 0;
        padding: 15px 0;
        margin-bottom: 20px;
        box-shadow: none; /* Gölgeyi kaldır */
        color: #333;
        position: relative;
        overflow: hidden;
    }
    
    .section-header {
        display: none; /* Başlık kısmını gizle */
    }
    
    .action-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        justify-content: center;
        position: relative;
        z-index: 1;
    }
    
    .action-button {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 15px;
        background-color: #222;
        border-radius: 8px;
        text-decoration: none;
        color: white;
        transition: all 0.3s ease;
        min-width: 120px;
        text-align: center;
        border: 1px solid #444;
        position: relative;
        overflow: hidden;
    }
    
    .action-button::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: rgba(255,255,255,0.1);
        transition: all 0.5s ease;
        z-index: 0;
    }
    
    .action-button:hover::before {
        left: 100%;
    }
    
    .action-button:hover {
        background-color: #444;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }
    
    .action-button i {
        font-size: 24px;
        margin-bottom: 8px;
        color: #3498db;
        position: relative;
        z-index: 1;
    }
    
    .action-button span {
        font-size: 14px;
        font-weight: 500;
        position: relative;
        z-index: 1;
    }
    
    /* Sipariş Durumu Kartları */
    .status-cards {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 20px;
        justify-content: space-between;
    }
    
    .status-card {
        flex: 1;
        min-width: 200px;
        background-color: #fff;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
    }
    
    .status-icon {
        width: 50px;
        height: 50px;
        background-color: #333;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
    }
    
    .status-icon i {
        font-size: 20px;
        color: white;
    }
    
    .status-details {
        flex-grow: 1;
    }
    
    .status-count {
        font-size: 24px;
        font-weight: bold;
        color: #333;
    }
    
    .status-label {
        font-size: 14px;
        color: #777;
    }
    
    .status-card:nth-child(1) .status-icon {
        background-color: #3498db;
    }
    
    .status-card:nth-child(2) .status-icon {
        background-color: #f39c12;
    }
    
    .status-card:nth-child(3) .status-icon {
        background-color: #2ecc71;
    }
    
    .status-card:nth-child(4) .status-icon {
        background-color: #e74c3c;
    }
    
    @media (max-width: 768px) {
        .action-buttons, .status-cards {
            flex-direction: column;
        }
        
        .action-button, .status-card {
            width: 100%;
        }
    }
</style>

<?php include('footer.php'); ?>