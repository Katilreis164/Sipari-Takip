<?php
// Oturum kontrolü
session_start();
if(!isset($_SESSION['personel'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KARATAŞCAM - Sipariş Takip</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        html, body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        .container {
            width: 100%;
            max-width: 100%;
            padding: 0;
            margin: 0;
            border-radius: 0;
        }
        .content-section {
            width: 100%;
            padding: 20px;
            box-sizing: border-box;
        }
        .full-width-section {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
        }
        .header-section {
            background-color: #fff;
            padding: 15px 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo-container {
            display: flex;
            align-items: center;
        }
        .logo-container img {
            height: 60px;
            margin-right: 15px;
        }
        .title-text {
            font-size: 28px;
            font-weight: bold;
            color: #333;
        }
        .title-text span {
            color: #4d4d4d;
            font-weight: bolder;
        }
        .user-info {
            text-align: right;
        }
        .user-info span {
            font-weight: bold;
        }
        .logout {
            color: #d9534f;
            text-decoration: none;
            margin-left: 10px;
        }
        .menu {
            display: flex;
            justify-content: center;
            margin: 15px 0;
            flex-wrap: wrap;
        }
        .menu-item {
            padding: 10px 15px;
            background-color: #4285f4;
            color: white;
            text-decoration: none;
            margin: 0 5px;
            border-radius: 4px;
        }
        .menu-item:hover {
            background-color: #357ae8;
        }
        .menu-item.active {
            background-color: #1a73e8;
            font-weight: bold;
        }
        /* Devre dışı buton stili */
        .menu-item.disabled {
            background-color: #b3b3b3;
            opacity: 0.7;
            cursor: not-allowed;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-section">
            <div class="header">
                <div class="logo-container">
                    <img src="image/logo-karatascam.png" alt="KARATAŞCAM Logo">
                    <h1 class="title-text">KARATAŞ<span>CAM</span></h1>
                </div>
                
                <div class="user-info">
                    <?php if(isset($_SESSION['personel'])): ?>
                    Hoş geldiniz, <span><?php echo $_SESSION['personel']; ?></span>
                    <a href="logout.php" class="logout">Çıkış Yap</a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="menu">
                <a href="index.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                    Ana Sayfa
                </a>
                <a href="giris.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'giris.php' || basename($_SERVER['PHP_SELF']) == 'ekle.php' ? 'active' : ''; ?>">
                    2025 Yeni Sipariş Girişi
                </a>
                <a href="javascript:void(0);" class="menu-item disabled" onclick="return false;">
                    Sipariş Takvim (Bakımda)
                </a>
                <a href="listele.php" class="menu-item">
                    2024 Sipariş Takip
                </a>
                <a href="kanban.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'kanban.php' ? 'active' : ''; ?>">
                    Kanban Görünümü
                </a>
            </div>
        </div>
        
        <div class="content-section">
            <!-- Sayfa içeriği buraya gelecek -->
        </div>
    </div>
</body>
</html> 