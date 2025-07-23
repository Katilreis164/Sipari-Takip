CREATE DATABASE IF NOT EXISTS siparistakip DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE siparistakip;

-- Personel bilgileri tablosu
CREATE TABLE IF NOT EXISTS personel (
  id int(11) NOT NULL AUTO_INCREMENT,
  kullanici varchar(50) NOT NULL,
  sifre varchar(32) NOT NULL, -- Sistem güvenliği
  son_giris datetime DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sistem kullanıcıları
INSERT INTO personel (kullanici, sifre) VALUES ('admin', '81dc9bdb52d04dc20036dbd8313ed055');
INSERT INTO personel (kullanici, sifre) VALUES ('kullanıcı1', '81dc9bdb52d04dc20036dbd8313ed055');
INSERT INTO personel (kullanici, sifre) VALUES ('kullanıcı2', '81dc9bdb52d04dc20036dbd8313ed055');
INSERT INTO personel (kullanici, sifre) VALUES ('kullanıcı3', '81dc9bdb52d04dc20036dbd8313ed055');

-- Sipariş tablosu
CREATE TABLE IF NOT EXISTS siparis (
  id int(11) NOT NULL AUTO_INCREMENT,
  musteri_id int(11) NOT NULL, -- Müşteri ID'si
  yilsiparis varchar(20) NOT NULL,
  isim varchar(50) NOT NULL,
  soyisim varchar(50) NOT NULL,
  evtel varchar(20) DEFAULT NULL,
  ceptel varchar(20) DEFAULT NULL,
  email varchar(100) DEFAULT NULL,
  adres text,
  semt varchar(50) DEFAULT NULL,
  urun varchar(100) DEFAULT NULL,
  aciklama text,
  starih date DEFAULT NULL,
  s_time time DEFAULT NULL,
  ttarih2 date DEFAULT NULL,
  e_time time DEFAULT NULL,
  satis varchar(50) DEFAULT NULL,
  nt text,
  fiyat decimal(10,2) DEFAULT NULL,
  kalan decimal(10,2) DEFAULT NULL,
  fiyat_sn decimal(10,2) DEFAULT NULL,
  kdvkalan decimal(10,2) DEFAULT NULL,
  kapora decimal(10,2) DEFAULT NULL,
  durum varchar(50) DEFAULT NULL,
  olusturma_tarih TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Sipariş oluşturma tarihi
  PRIMARY KEY (id),
  UNIQUE KEY (yilsiparis) -- Sipariş numarasının benzersiz olmasını sağlar
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Müşteriler tablosu
CREATE TABLE IF NOT EXISTS musteriler (
  id int(11) NOT NULL AUTO_INCREMENT,
  isim varchar(50) NOT NULL,
  soyisim varchar(50) NOT NULL,
  evtel varchar(20) DEFAULT NULL,
  ceptel varchar(20) DEFAULT NULL,
  email varchar(100) DEFAULT NULL,
  adres text,
  semt varchar(50) DEFAULT NULL,
  olusturma_tarih TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY (ceptel) -- Müşterinin benzersiz olması için cep telefonu kullanılabilir
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Resimler tablosu
CREATE TABLE IF NOT EXISTS resimler (
  id int(11) NOT NULL AUTO_INCREMENT,
  siparisno varchar(20) NOT NULL,
  resim varchar(255) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Krokiler tablosu
CREATE TABLE IF NOT EXISTS krokiler (
  id int(11) NOT NULL AUTO_INCREMENT,
  siparisno varchar(20) NOT NULL,
  kroki varchar(255) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Satış personeli tablosu
CREATE TABLE IF NOT EXISTS kisiler (
  id int(11) NOT NULL AUTO_INCREMENT,
  adsoyad varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Örnek satış personeli
INSERT INTO kisiler (adsoyad) VALUES ('Ahmet Yılmaz');
INSERT INTO kisiler (adsoyad) VALUES ('Ayşe Demir');
INSERT INTO kisiler (adsoyad) VALUES ('Mehmet Kaya');
