<?php
include('include.php');
include('baglanti.php');

// Sayfanın başında menü görünürlüğünü sağlama
echo '<style>
    .header-section .menu {
        display: flex !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
</style>';

        // Siparişleri çek
$sql = "SELECT * FROM siparis ORDER BY starih DESC LIMIT 100";
        $result = $baglanti->query($sql);
?>

<div class="search-container">
    <h2 class="search-title">Sipariş Listesi</h2>
    
    <div class="control-buttons" style="margin-bottom: 15px; display: flex; gap: 10px;">
        <a href="kanban.php" class="btn btn-primary"><i class="fas fa-columns"></i> Kanban Görünümü</a>
        <a href="index.php" class="btn btn-info"><i class="fas fa-home"></i> Ana Sayfa</a>
        <a href="giris.php" class="btn btn-success"><i class="fas fa-plus"></i> Yeni Sipariş</a>
    </div>
    
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Son 100 sipariş listelenmektedir. Daha fazla sipariş için arama yapabilirsiniz.
    </div>
    
    <div class="table-responsive" style="overflow-x: hidden;">
        <table style="min-width: 100%;">
            <thead>
                <tr>
                        <th>Sipariş No</th>
                    <th>Müşteri</th>
                    <th>Firma</th>
                    <th>Sipariş Tarihi</th>
                    <th>Teslim Tarihi</th>
                        <th>Durum</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        // Değişkenleri güvenli bir şekilde al
                        $siparis_no = isset($row["yilsiparis"]) ? $row["yilsiparis"] : "";
                        $musteri = (isset($row["isim"]) ? $row["isim"] : "") . " " . (isset($row["soyisim"]) ? $row["soyisim"] : "");
                        $firma = "-"; // Varsayılan değer
                        
                        // Firma alanını kontrol et (sirket, firma veya kurum olabilir)
                        if (isset($row["sirket"]) && !empty($row["sirket"])) {
                            $firma = $row["sirket"];
                        } elseif (isset($row["firma"]) && !empty($row["firma"])) {
                            $firma = $row["firma"];
                        } elseif (isset($row["kurum"]) && !empty($row["kurum"])) {
                            $firma = $row["kurum"];
                        }
                        
                        $siparis_tarihi = isset($row["starih"]) ? date('d.m.Y', strtotime($row["starih"])) : "";
                        $teslim_tarihi = isset($row["ttarih2"]) ? date('d.m.Y', strtotime($row["ttarih2"])) : "";
                        $durum = isset($row["durum"]) ? $row["durum"] : "";
                        $id = isset($row["id"]) ? $row["id"] : "";
                        
                        // Durum sütununu renklendir
                        $durum_class = "";
                        if($durum == "Üretim") {
                            $durum_class = "bg-warning";
                        } else if($durum == "Hazır") {
                            $durum_class = "bg-success";
                        } else if($durum == "Tamamlandı") {
                            $durum_class = "bg-info";
                        }
                        
                        echo "<tr>";
                        echo "<td>".$siparis_no."</td>";
                        echo "<td>".$musteri."</td>";
                        echo "<td>".$firma."</td>";
                        echo "<td>".$siparis_tarihi."</td>";
                        echo "<td>".$teslim_tarihi."</td>";
                        echo "<td class='".$durum_class."'>".$durum."</td>";
                        
                        echo "<td>";
                        if (!empty($id)) {
                            echo "<a href='ayrinti.php?yilsiparis=".$siparis_no."' class='btn btn-sm' title='Ayrıntılar'><i class='fas fa-eye'></i></a> ";
                            echo "<a href='guncelle.php?siparis_no3=".$siparis_no."' class='btn btn-sm' title='Düzenle'><i class='fas fa-edit'></i></a> ";
                            echo "<a href='siparispdf.php?yilsiparis=".$siparis_no."' target='_blank' class='btn btn-sm' title='PDF Oluştur'><i class='fas fa-file-pdf'></i></a>";
                        }
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>Henüz sipariş bulunmamaktadır.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('footer.php'); ?>