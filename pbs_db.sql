/*
 Navicat Premium Data Transfer

 Source Server         : database
 Source Server Type    : MySQL
 Source Server Version : 100432 (10.4.32-MariaDB)
 Source Host           : localhost:3306
 Source Schema         : pbs_db

 Target Server Type    : MySQL
 Target Server Version : 100432 (10.4.32-MariaDB)
 File Encoding         : 65001

 Date: 09/05/2026 23:55:48
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for activity_log
-- ----------------------------
DROP TABLE IF EXISTS `activity_log`;
CREATE TABLE `activity_log`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `admin_id` int NULL DEFAULT NULL,
  `action` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `admin_id`(`admin_id` ASC) USING BTREE,
  CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of activity_log
-- ----------------------------
INSERT INTO `activity_log` VALUES (1, 1, 'DELETE_SERVICE', 'ID: 6', '::1', '2026-05-09 23:54:07');
INSERT INTO `activity_log` VALUES (2, 1, 'DELETE_SERVICE', 'ID: 5', '::1', '2026-05-09 23:54:25');

-- ----------------------------
-- Table structure for admins
-- ----------------------------
DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('superadmin','admin','editor') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'admin',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `last_login` datetime NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `username`(`username` ASC) USING BTREE,
  UNIQUE INDEX `email`(`email` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admins
-- ----------------------------
INSERT INTO `admins` VALUES (1, 'admin', 'admin@primabaharisejahtera.co.id', '$2y$10$5BhofHljl7hLgZ/gvtAJgOeY4lLLakOrI8/UOTI743sM33e0XrPce', 'Super Administrator', 'superadmin', NULL, '2026-05-09 23:52:30', '2026-05-06 19:56:39');

-- ----------------------------
-- Table structure for gallery
-- ----------------------------
DROP TABLE IF EXISTS `gallery`;
CREATE TABLE `gallery`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `caption` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `category` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'Umum',
  `is_active` tinyint(1) NULL DEFAULT 1,
  `sort_order` int NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of gallery
-- ----------------------------

-- ----------------------------
-- Table structure for messages
-- ----------------------------
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `phone` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `subject` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cargo_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'Lainnya',
  `is_read` tinyint(1) NULL DEFAULT 0,
  `is_replied` tinyint(1) NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of messages
-- ----------------------------

-- ----------------------------
-- Table structure for news
-- ----------------------------
DROP TABLE IF EXISTS `news`;
CREATE TABLE `news`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `excerpt` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `author` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'Admin PBS',
  `category` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'Berita',
  `tags` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `is_published` tinyint(1) NULL DEFAULT 1,
  `published_at` datetime NULL DEFAULT current_timestamp,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `slug`(`slug` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of news
-- ----------------------------
INSERT INTO `news` VALUES (1, 'PBS Buka Rute Baru Batam–Surabaya–Makassar', 'rute-baru-batam-surabaya-makassar', NULL, 'PT. Prima Bahari Sejahtera dengan bangga mengumumkan pembukaan rute baru Batam–Surabaya–Makassar mulai bulan ini.', '<p>PT. Prima Bahari Sejahtera dengan bangga mengumumkan pembukaan rute pengiriman laut terbaru yang menghubungkan Batam–Surabaya–Makassar. Rute baru ini hadir sebagai respons atas meningkatnya permintaan pengiriman barang dari Batam ke kawasan Indonesia Timur.</p><p>Dengan rute baru ini, pelanggan dapat menikmati layanan pengiriman yang lebih cepat dan efisien. Jadwal keberangkatan tersedia setiap minggu, sehingga memberikan fleksibilitas tinggi bagi para pelaku usaha.</p>', 'Admin PBS', 'Berita', NULL, 1, '2026-05-01 19:56:39', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `news` VALUES (2, 'Tips Mengemas Barang untuk Pengiriman Laut', 'tips-mengemas-barang-pengiriman-laut', NULL, 'Pengemasan yang tepat sangat penting untuk memastikan barang Anda tiba dalam kondisi sempurna. Simak tips dari tim PBS.', '<p>Pengiriman via kapal laut melibatkan perjalanan panjang di atas lautan. Oleh karena itu, pengemasan yang tepat sangat krusial untuk memastikan keselamatan barang Anda.</p><h3>1. Gunakan Kardus Berkualitas</h3><p>Pilih kardus dengan ketebalan minimal 5 ply untuk barang yang rentan. Pastikan kardus dalam kondisi baru dan tidak lembab.</p><h3>2. Bubble Wrap dan Styrofoam</h3><p>Gunakan lapisan pengaman ekstra untuk barang-barang pecah belah atau elektronik.</p>', 'Admin PBS', 'Berita', NULL, 1, '2026-04-24 19:56:39', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `news` VALUES (3, 'Layanan Lost Cargo: Solusi Hemat untuk UMKM', 'layanan-lost-cargo-solusi-umkm', NULL, 'Layanan Lost Cargo atau LCL adalah pilihan tepat bagi pelaku UMKM yang ingin mengirimkan produk ke seluruh Indonesia.', '<p>Bagi para pelaku Usaha Mikro, Kecil, dan Menengah (UMKM), biaya logistik seringkali menjadi tantangan utama. Layanan Lost Cargo dari PBS hadir sebagai solusi yang hemat namun tetap andal.</p><p>Dengan sistem konsolidasi muatan, biaya pengiriman Anda dihitung berdasarkan berat dan volume aktual barang, bukan untuk satu kontainer penuh.</p>', 'Admin PBS', 'Berita', NULL, 1, '2026-04-16 19:56:39', '2026-05-06 19:56:39', '2026-05-06 19:56:39');

-- ----------------------------
-- Table structure for routes
-- ----------------------------
DROP TABLE IF EXISTS `routes`;
CREATE TABLE `routes`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `origin` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `destination` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `frequency` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'Mingguan',
  `duration` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `is_active` tinyint(1) NULL DEFAULT 1,
  `sort_order` int NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of routes
-- ----------------------------
INSERT INTO `routes` VALUES (1, 'Batam', 'Jakarta', 'Setiap Senin & Kamis', '3-4 Hari', 1, 0);
INSERT INTO `routes` VALUES (2, 'Batam', 'Surabaya', 'Setiap Selasa', '5-6 Hari', 1, 0);
INSERT INTO `routes` VALUES (3, 'Batam', 'Makassar', 'Setiap Rabu', '7-8 Hari', 1, 0);
INSERT INTO `routes` VALUES (4, 'Batam', 'Medan', 'Setiap Kamis', '2-3 Hari', 1, 0);
INSERT INTO `routes` VALUES (5, 'Batam', 'Semarang', 'Setiap Jumat', '4-5 Hari', 1, 0);
INSERT INTO `routes` VALUES (6, 'Jakarta', 'Batam', 'Setiap Senin & Kamis', '3-4 Hari', 1, 0);

-- ----------------------------
-- Table structure for services
-- ----------------------------
DROP TABLE IF EXISTS `services`;
CREATE TABLE `services`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'bi-box-seam',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `short_desc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `full_desc` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `features` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'JSON array of feature strings',
  `is_featured` tinyint(1) NULL DEFAULT 0,
  `is_active` tinyint(1) NULL DEFAULT 1,
  `sort_order` int NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `slug`(`slug` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of services
-- ----------------------------
INSERT INTO `services` VALUES (1, 'Lost Cargo / LCL', 'lost-cargo-lcl', 'bi-box-seam', NULL, 'Layanan pengiriman muatan satuan (Less than Container Load) yang ekonomis untuk berbagai jenis barang.', 'Layanan Lost Cargo atau Less than Container Load (LCL) adalah solusi ideal bagi Anda yang ingin mengirimkan barang dalam jumlah yang belum mencukupi satu kontainer penuh. Muatan Anda akan digabungkan dengan muatan pengirim lain sehingga biaya menjadi lebih efisien. Kami menjamin keamanan dan keselamatan barang Anda selama proses pengiriman.', '[\"Biaya lebih ekonomis untuk muatan kecil\",\"Pengiriman ke seluruh wilayah Indonesia\",\"Pengemasan profesional & aman\",\"Tracking real-time\",\"Asuransi pengiriman tersedia\",\"Pickup & delivery tersedia\"]', 1, 1, 1, '2026-05-06 19:56:39', '2026-05-09 23:52:41');
INSERT INTO `services` VALUES (2, 'Full Container Load / FCL', 'full-container-load-fcl', 'bi-grid-3x3-gap', NULL, 'Sewa kontainer penuh untuk muatan besar. Tersedia ukuran 20 feet dan 40 feet sesuai kebutuhan.', 'Full Container Load (FCL) adalah layanan penyewaan kontainer secara utuh untuk memenuhi kebutuhan pengiriman barang dalam skala besar. Dengan FCL, muatan Anda tidak akan bercampur dengan muatan orang lain, memberikan keamanan dan privasi penuh bagi barang Anda. Kami menyediakan kontainer 20 feet dan 40 feet.', '[\"Kontainer 20ft & 40ft tersedia\",\"Keamanan muatan lebih terjamin\",\"Cocok untuk muatan besar\",\"Fleksibel jadwal keberangkatan\",\"Layanan stuffing & unstuffing\",\"Dokumen B/L & manifes lengkap\"]', 1, 1, 2, '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `services` VALUES (3, 'Freight Forwarding', 'freight-forwarding', 'bi-truck', NULL, 'Layanan pengurusan seluruh proses pengiriman, dari pengemasan, dokumentasi, hingga pengiriman ke tujuan.', 'Freight Forwarding adalah layanan manajemen logistik terpadu yang menangani seluruh aspek pengiriman barang Anda. Tim profesional kami akan mengurus semua proses, mulai dari pengambilan barang, pengemasan, dokumentasi ekspor-impor, pemrosesan bea cukai, hingga pengiriman akhir ke tujuan Anda.', '[\"Pengurusan dokumen ekspor-impor\",\"Clearance bea cukai\",\"Pengemasan & labeling\",\"Asuransi kargo\",\"Koordinasi multi-moda transportasi\",\"Laporan pengiriman real-time\"]', 1, 1, 3, '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `services` VALUES (4, 'Ekspor - Impor', 'ekspor-impor', 'bi-arrow-left-right', NULL, 'Konsultasi dan pengurusan dokumen ekspor-impor, termasuk bea cukai, NPIK, dan perizinan lainnya.', 'Layanan ekspor-impor kami mencakup pengurusan semua dokumen dan perizinan yang diperlukan untuk kegiatan perdagangan internasional. Kami memiliki tim ahli yang berpengalaman dalam regulasi bea cukai Indonesia dan internasional, memastikan barang Anda melewati proses clearance dengan cepat dan tepat.', '[\"Pengurusan dokumen L/C\",\"Customs clearance\",\"Pengurusan NPIK & API\",\"Konsultasi regulasi perdagangan\",\"PIB & PEB processing\",\"PPJK berpengalaman\"]', 1, 1, 4, '2026-05-06 19:56:39', '2026-05-06 19:56:39');

-- ----------------------------
-- Table structure for settings
-- ----------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `setting_key`(`setting_key` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 33 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of settings
-- ----------------------------
INSERT INTO `settings` VALUES (1, 'site_name', 'PT. Prima Bahari Sejahtera', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (2, 'site_tagline', 'Freight Forwarding & Export - Import', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (3, 'site_description', 'PT. Prima Bahari Sejahtera bergerak di bidang jasa pengiriman via kapal laut. Menerima pengiriman barang baik Lost Cargo maupun Full Container ke seluruh Indonesia.', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (4, 'meta_keywords', 'freight forwarding, ekspor impor, pengiriman kapal laut, lost cargo, full container, Prima Bahari Sejahtera, PBS', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (5, 'logo', '', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (6, 'favicon', 'images/favicon.png', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (7, 'og_image', '', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (8, 'contact_phone', '+62 778 123456', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (9, 'contact_whatsapp', '+62 812 3456 7890', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (10, 'contact_email', 'info@primabaharisejahtera.co.id', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (11, 'contact_address', 'Jl. Pelabuhan Batu Ampar No. 12, Batam, Kepulauan Riau 29461', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (12, 'social_instagram', '', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (13, 'social_facebook', '', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (14, 'social_linkedin', '', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (15, 'hero_video', '', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (16, 'hero_title', 'Solusi Pengiriman Laut Terpercaya', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (17, 'hero_subtitle', 'PT. Prima Bahari Sejahtera melayani pengiriman Lost Cargo & Full Container ke seluruh pelosok Indonesia dengan armada kapal modern.', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (18, 'hero_btn1_text', 'Lihat Layanan Kami', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (19, 'hero_btn1_url', '/services.php', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (20, 'hero_btn2_text', 'Hubungi Kami', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (21, 'hero_btn2_url', '/contact.php', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (22, 'stat_years', '10+', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (23, 'stat_routes', '50+', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (24, 'stat_containers', '5.000+', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (25, 'stat_clients', '200+', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (26, 'about_title', 'Pengiriman Andal, Koneksi Nusantara', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (27, 'about_description', 'Dengan pengalaman lebih dari 10 tahun, kami hadir sebagai mitra logistik terpercaya yang menghubungkan ribuan pulau di Indonesia. Kami menawarkan solusi pengiriman laut yang efisien, aman, dan tepat waktu—baik untuk muatan satuan (lost cargo) maupun kontainer penuh (full container).', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (28, 'about_image', '', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (29, 'footer_copyright', '© 2025 PT. Prima Bahari Sejahtera. All Rights Reserved.', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (30, 'google_maps_embed', '', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (31, 'cta_title', 'Siap Kirimkan Muatan Anda?', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (32, 'cta_description', 'Dapatkan penawaran terbaik untuk kebutuhan pengiriman laut Anda. Tim kami siap membantu 24/7.', '2026-05-06 19:56:39', '2026-05-06 19:56:39');

-- ----------------------------
-- Table structure for team
-- ----------------------------
DROP TABLE IF EXISTS `team`;
CREATE TABLE `team`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `bio` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `linkedin` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `is_active` tinyint(1) NULL DEFAULT 1,
  `sort_order` int NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of team
-- ----------------------------

-- ----------------------------
-- Table structure for testimonials
-- ----------------------------
DROP TABLE IF EXISTS `testimonials`;
CREATE TABLE `testimonials`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `company` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `position` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` tinyint NULL DEFAULT 5,
  `is_active` tinyint(1) NULL DEFAULT 1,
  `sort_order` int NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of testimonials
-- ----------------------------
INSERT INTO `testimonials` VALUES (1, 'Budi Santoso', 'CV. Maju Bersama', 'Direktur', NULL, 'Sudah 3 tahun bermitra dengan PBS, pelayanan sangat profesional dan tepat waktu. Barang selalu tiba dalam kondisi sempurna. Sangat direkomendasikan!', 5, 1, 1, '2026-05-06 19:56:40');
INSERT INTO `testimonials` VALUES (2, 'Rina Dewi', 'PT. Nusantara Jaya', 'Manager Logistik', NULL, 'PBS membantu kami mengelola pengiriman FCL ke seluruh Indonesia. Dokumentasi lengkap, harga kompetitif, dan tim yang responsif. Puas banget!', 5, 1, 2, '2026-05-06 19:56:40');
INSERT INTO `testimonials` VALUES (3, 'Ahmad Fauzi', 'Toko Elektronik Makmur', 'Pemilik', NULL, 'Layanan Lost Cargo PBS sangat membantu bisnis kecil kami. Bisa kirim barang satuan dengan harga terjangkau. Tracking-nya juga mudah dipahami.', 5, 1, 3, '2026-05-06 19:56:40');
INSERT INTO `testimonials` VALUES (4, 'Siti Rahma', 'PT. Batam Textile', 'Supply Chain Manager', NULL, 'Pengurusan dokumen ekspor kami jauh lebih mudah sejak menggunakan PBS. Tim mereka sangat berpengalaman dan selalu siap membantu.', 4, 1, 4, '2026-05-06 19:56:40');
INSERT INTO `testimonials` VALUES (5, 'Dede Kurniawan', 'UD. Sembako Jaya', 'Pemilik', NULL, 'Harga bersaing, pelayanan ramah, dan yang paling penting barang selalu aman sampai tujuan. PBS adalah pilihan terbaik untuk pengiriman laut!', 5, 1, 5, '2026-05-06 19:56:40');

SET FOREIGN_KEY_CHECKS = 1;
