# Sipariş Takip Sistemi 2025

Modern ve kullanımı kolay sipariş takip uygulaması. Bu sistem, siparişlerin oluşturulması, takibi ve düzenlenmesi için geliştirilmiştir.

## Sistem Gereksinimleri

- PHP 7.4 veya üzeri
- MySQL 5.7 veya üzeri
- Apache / Nginx web sunucusu

## Kurulum

1. Dosyaları web sunucunuzun ilgili dizinine kopyalayın.
2. Veritabanını oluşturun: 
   ```
   CREATE DATABASE siparis2025 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
3. Aşağıdaki SQL komutlarını çalıştırarak gerekli tabloları oluşturun:

```sql
CREATE TABLE kullanicilar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kullanici_adi VARCHAR(50) NOT NULL UNIQUE,
    sifre VARCHAR(255) NOT NULL,
    ad_soyad VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    son_giris DATETIME,
    durum TINYINT(1) DEFAULT 1,
    kayit_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE siparisler (
    id INT AUTO_INCREMENT PRIMARY KEY,
    siparis_no VARCHAR(20) NOT NULL UNIQUE,
    musteri_adi VARCHAR(100) NOT NULL,
    musteri_tel VARCHAR(20),
    musteri_email VARCHAR(100),
    adres TEXT,
    tarih DATE NOT NULL,
    tutar DECIMAL(10,2) NOT NULL,
    odeme_durum VARCHAR(20) NOT NULL,
    durum VARCHAR(20) NOT NULL,
    durum_aciklama TEXT,
    aciklama TEXT,
    urun_bilgisi TEXT NOT NULL,
    urun_resim VARCHAR(255),
    adres_kroki VARCHAR(255),
    ekleyen_id INT,
    guncelleme_tarihi DATETIME,
    kayit_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ekleyen_id) REFERENCES kullanicilar(id)
);
```

4. İlk kullanıcıyı oluşturun:

```sql
INSERT INTO kullanicilar (kullanici_adi, sifre, ad_soyad, email) 
VALUES ('admin', '$2y$10$XsN2KZTtvWU6Llp8j2cYme/gNdErPPkM3PyYmdgFk8Qo36BVDHGCa', 'Sistem Yöneticisi', 'admin@example.com');
```

(Not: Bu, şifresi "123456" olan bir admin kullanıcısı oluşturur. Güvenlik açısından gerçek ortamda farklı bir şifre kullanmalısınız.)

5. `inc/baglanti.php` dosyasındaki veritabanı bağlantı bilgilerini kendi sunucunuza göre düzenleyin.

## Dosya Yapısı

```
siparistakip/2025/
│
├── inc/                  # Dahil edilen dosyalar
│   ├── baglanti.php      # Veritabanı bağlantısı
│   ├── functions.php     # Yardımcı fonksiyonlar
│   ├── header.php        # Sayfa üst kısmı
│   └── footer.php        # Sayfa alt kısmı
│
├── assets/               # CSS, JS ve görsel dosyaları
│   └── style.css         # Ana stil dosyası
│
├── uploads/              # Yüklenen dosyalar
│   ├── resimler/         # Ürün görselleri
│   └── kroki/            # Adres krokileri
│
├── login.php             # Giriş sayfası
├── index.php             # Ana sayfa
├── index.html            # Yönlendirme sayfası
├── logout.php            # Çıkış işlemi
├── yeni_siparis.php      # Yeni sipariş ekleme
├── ayrinti.php           # Sipariş detayları
├── arama_sonuc.php       # Arama sonuçları
└── README.md             # Bu dosya
```

## Özellikler

- Kullanıcı girişi ve oturum yönetimi
- Yeni sipariş oluşturma
- Sipariş arama ve filtreleme
- Sipariş detaylarını görüntüleme
- Sipariş durumunu güncelleme
- Siparişlere dosya ekleyebilme (resim, kroki vb.)

## Güvenlik

- Parola şifreleme (PHP password_hash / password_verify)
- SQL enjeksiyon koruması (Prepared Statements)
- XSS koruması (htmlspecialchars)
- Oturum güvenliği kontrolü

## Yardım ve Destek

Herhangi bir sorun yaşarsanız veya öneriniz varsa lütfen iletişime geçin. 
