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

 Date: 02/06/2026 21:54:40
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
) ENGINE = InnoDB AUTO_INCREMENT = 197 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of activity_log
-- ----------------------------
INSERT INTO `activity_log` VALUES (160, 1, 'UPDATE_SETTINGS', 'Memperbarui pengaturan situs (1 perubahan)\n[Tagline] \"Freight Forwarding & Export - Import\" → \"Freight Forwarding & Export - Importt\"', '::1', '2026-06-01 18:24:17');
INSERT INTO `activity_log` VALUES (161, 1, 'UPDATE_SETTINGS', 'Memperbarui pengaturan situs (1 perubahan)\n[Tagline] \"Freight Forwarding & Export - Importt\" → \"Freight Forwarding & Export - Import\"', '::1', '2026-06-01 18:24:27');
INSERT INTO `activity_log` VALUES (162, 1, 'TOGGLE_CARGO_TYPE', 'Mengubah status jenis kargo\n[Nama] FCL (Full Container Load)\n[Status] \"Aktif\" → \"Nonaktif\"', '::1', '2026-06-02 21:11:22');
INSERT INTO `activity_log` VALUES (163, 1, 'TOGGLE_CARGO_TYPE', 'Mengubah status jenis kargo\n[Nama] FCL (Full Container Load)\n[Status] \"Nonaktif\" → \"Aktif\"', '::1', '2026-06-02 21:11:23');
INSERT INTO `activity_log` VALUES (164, 1, 'TOGGLE_CARGO_TYPE', 'Mengubah status jenis kargo\n[Nama] LCL (Less than Container Load)\n[Status] \"Aktif\" → \"Nonaktif\"', '::1', '2026-06-02 21:11:24');
INSERT INTO `activity_log` VALUES (165, 1, 'TOGGLE_CARGO_TYPE', 'Mengubah status jenis kargo\n[Nama] LCL (Less than Container Load)\n[Status] \"Nonaktif\" → \"Aktif\"', '::1', '2026-06-02 21:11:24');
INSERT INTO `activity_log` VALUES (166, 1, 'EDIT_CARGO_TYPE', 'Edit jenis kargo: FCL (Full Container Load (1 perubahan)\n[Nama] \"FCL (Full Container Load)\" → \"FCL (Full Container Load\"', '::1', '2026-06-02 21:12:09');
INSERT INTO `activity_log` VALUES (167, 1, 'EDIT_CARGO_TYPE', 'Edit jenis kargo: FCL (Full Container Load) (2 perubahan)\n[Nama] \"FCL (Full Container Load\" → \"FCL (Full Container Load)\"\n[Kode] \"FCL\" → \"FCLL\"', '::1', '2026-06-02 21:12:23');
INSERT INTO `activity_log` VALUES (168, 1, 'EDIT_CARGO_TYPE', 'Edit jenis kargo: FCL (Full Container Load) (1 perubahan)\n[Kode] \"FCLL\" → \"FCL\"', '::1', '2026-06-02 21:12:31');
INSERT INTO `activity_log` VALUES (169, 1, 'EDIT_CARGO_TYPE', 'Edit jenis kargo: FCL (Full Container Load)', '::1', '2026-06-02 21:12:40');
INSERT INTO `activity_log` VALUES (170, 1, 'EDIT_CARGO_TYPE', 'Edit jenis kargo: FCL (Full Container Load)', '::1', '2026-06-02 21:13:25');
INSERT INTO `activity_log` VALUES (171, 1, 'EDIT_CARGO_TYPE', 'Edit jenis kargo: FCL (Full Container Load)', '::1', '2026-06-02 21:13:31');
INSERT INTO `activity_log` VALUES (172, 1, 'EDIT_CARGO_TYPE', 'Edit jenis kargo: FCL (Full Container Load)', '::1', '2026-06-02 21:13:41');
INSERT INTO `activity_log` VALUES (173, 1, 'EDIT_CARGO_TYPE', 'Edit jenis kargo: FCL (Full Container Load)', '::1', '2026-06-02 21:14:03');
INSERT INTO `activity_log` VALUES (174, 1, 'EDIT_CARGO_TYPE', 'Edit jenis kargo: FCL (Full Container Load) (1 perubahan)\n[Status] \"Aktif\" → \"Nonaktif\"', '::1', '2026-06-02 21:14:09');
INSERT INTO `activity_log` VALUES (175, 1, 'EDIT_CARGO_TYPE', 'Edit jenis kargo: FCL (Full Container Load)', '::1', '2026-06-02 21:14:12');
INSERT INTO `activity_log` VALUES (176, 1, 'TOGGLE_CARGO_TYPE', 'Mengubah status jenis kargo\n[Nama] FCL (Full Container Load)\n[Status] \"Nonaktif\" → \"Aktif\"', '::1', '2026-06-02 21:14:13');
INSERT INTO `activity_log` VALUES (177, 1, 'EDIT_CARGO_TYPE', 'Edit jenis kargo: FCL (Full Container Load) (1 perubahan)\n[Status] \"Aktif\" → \"Nonaktif\"', '::1', '2026-06-02 21:14:22');
INSERT INTO `activity_log` VALUES (178, 1, 'EDIT_CARGO_TYPE', 'Edit jenis kargo: FCL (Full Container Load)', '::1', '2026-06-02 21:14:27');
INSERT INTO `activity_log` VALUES (179, 1, 'EDIT_CARGO_TYPE', 'Edit jenis kargo: FCL (Full Container Load)', '::1', '2026-06-02 21:14:29');
INSERT INTO `activity_log` VALUES (180, 1, 'TOGGLE_CARGO_TYPE', 'Mengubah status jenis kargo\n[Nama] FCL (Full Container Load)\n[Status] \"Nonaktif\" → \"Aktif\"', '::1', '2026-06-02 21:14:30');
INSERT INTO `activity_log` VALUES (181, 1, 'EDIT_CARGO_TYPE', 'Edit jenis kargo: FCL (Full Container Load)', '::1', '2026-06-02 21:15:29');
INSERT INTO `activity_log` VALUES (182, 1, 'EDIT_CARGO_TYPE', 'Edit jenis kargo: FCL (Full Container Load) (1 perubahan)\n[Urutan] \"1\" → \"2\"', '::1', '2026-06-02 21:39:22');
INSERT INTO `activity_log` VALUES (183, 1, 'EDIT_CARGO_TYPE', 'Edit jenis kargo: FCL (Full Container Load) (1 perubahan)\n[Urutan] \"2\" → \"1\"', '::1', '2026-06-02 21:39:27');
INSERT INTO `activity_log` VALUES (184, 1, 'EDIT_CARGO_TYPE', 'Edit jenis kargo: FCL (Full Container Load)', '::1', '2026-06-02 21:40:01');
INSERT INTO `activity_log` VALUES (185, 1, 'ADD_TESTIMONIAL', 'Menambahkan testimoni baru\n[Nama] a\n[Jabatan] -\n[Perusahaan] a\n[Rating] 5 bintang\n[Status] Aktif', '::1', '2026-06-02 21:43:06');
INSERT INTO `activity_log` VALUES (186, 1, 'ADD_TESTIMONIAL', 'Menambahkan testimoni baru\n[Nama] a\n[Jabatan] -\n[Perusahaan] a\n[Rating] 5 bintang\n[Status] Aktif', '::1', '2026-06-02 21:43:11');
INSERT INTO `activity_log` VALUES (187, 1, 'ADD_TESTIMONIAL', 'Menambahkan testimoni baru\n[Nama] a\n[Jabatan] -\n[Perusahaan] a\n[Rating] 5 bintang\n[Status] Aktif', '::1', '2026-06-02 21:43:13');
INSERT INTO `activity_log` VALUES (188, 1, 'DELETE_TESTIMONIAL', 'Menghapus testimoni\n[Nama] a\n[ID] 8', '::1', '2026-06-02 21:43:14');
INSERT INTO `activity_log` VALUES (189, 1, 'DELETE_TESTIMONIAL', 'Menghapus testimoni\n[Nama] a\n[ID] 7', '::1', '2026-06-02 21:43:16');
INSERT INTO `activity_log` VALUES (190, 1, 'DELETE_TESTIMONIAL', 'Menghapus testimoni\n[Nama] a\n[ID] 6', '::1', '2026-06-02 21:43:31');
INSERT INTO `activity_log` VALUES (191, 1, 'EDIT_TESTIMONIAL', 'Edit testimoni: Budi Santoso (1 perubahan)\n[Jabatan] \"Direktur\" → \"Direkturr\"', '::1', '2026-06-02 21:43:34');
INSERT INTO `activity_log` VALUES (192, 1, 'EDIT_TESTIMONIAL', 'Edit testimoni: Budi Santoso', '::1', '2026-06-02 21:43:36');
INSERT INTO `activity_log` VALUES (193, 1, 'EDIT_TESTIMONIAL', 'Edit testimoni: Budi Santoso', '::1', '2026-06-02 21:43:38');
INSERT INTO `activity_log` VALUES (194, 1, 'EDIT_TESTIMONIAL', 'Edit testimoni: Budi Santoso (1 perubahan)\n[Jabatan] \"Direkturr\" → \"Direktur\"', '::1', '2026-06-02 21:43:41');
INSERT INTO `activity_log` VALUES (195, 1, 'EDIT_TESTIMONIAL', 'Edit testimoni: Budi Santoso', '::1', '2026-06-02 21:43:42');
INSERT INTO `activity_log` VALUES (196, 1, 'EDIT_TESTIMONIAL', 'Edit testimoni: Budi Santoso', '::1', '2026-06-02 21:44:21');

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
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admins
-- ----------------------------
INSERT INTO `admins` VALUES (1, 'admin', 'admin@primabaharisejahtera.co.id', '$2y$10$5BhofHljl7hLgZ/gvtAJgOeY4lLLakOrI8/UOTI743sM33e0XrPce', 'Super Administrator', 'superadmin', NULL, '2026-06-02 21:10:27', '2026-05-06 19:56:39');
INSERT INTO `admins` VALUES (2, 'darren', 'darrenjaylenson1@gmail.com', '$2y$10$PCuoFHHkvTEQnwV5ZoCXROZTLbQ7F2nSq5/8aUY4thOWFjrqeuUc.', 'darren jaylenson', 'admin', NULL, '2026-05-26 21:11:08', '2026-05-26 21:08:25');

-- ----------------------------
-- Table structure for cargo_types
-- ----------------------------
DROP TABLE IF EXISTS `cargo_types`;
CREATE TABLE `cargo_types`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uq_cargo_code`(`code` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cargo_types
-- ----------------------------
INSERT INTO `cargo_types` VALUES (1, 'FCL (Full Container Load)', 'FCL', 'Pengiriman satu kontainer penuh milik satu pengirim', 1, 1, '2026-05-20 22:31:52');
INSERT INTO `cargo_types` VALUES (2, 'LCL (Less than Container Load)', 'LCL', 'Pengiriman barang patungan dalam satu kontainer', 1, 2, '2026-05-20 22:31:52');
INSERT INTO `cargo_types` VALUES (3, 'Break Bulk', 'BREAK_BULK', 'Barang curah yang tidak dikemas dalam kontainer', 1, 3, '2026-05-20 22:31:52');
INSERT INTO `cargo_types` VALUES (4, 'Project Cargo', 'PROJECT', 'Pengiriman kargo khusus untuk proyek berskala besar', 1, 4, '2026-05-20 22:31:52');
INSERT INTO `cargo_types` VALUES (5, 'Lainnya', 'OTHER', 'Jenis kargo lainnya', 1, 5, '2026-05-20 22:31:52');

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
  `cargo_type_id` int NULL DEFAULT NULL,
  `is_read` tinyint(1) NULL DEFAULT 0,
  `is_replied` tinyint(1) NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_msg_cargo`(`cargo_type_id` ASC) USING BTREE,
  CONSTRAINT `fk_msg_cargo` FOREIGN KEY (`cargo_type_id`) REFERENCES `cargo_types` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of messages
-- ----------------------------
INSERT INTO `messages` VALUES (6, 'abc', 'abc@gmail.com', '31231', 'asda', 'asdasd', 'FCL (Full Container Load)', 1, 1, 1, '2026-05-27 19:42:17');

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
INSERT INTO `news` VALUES (1, 'PBS Buka Rute Baru Batam–Surabaya–Makassar', 'rute-baru-batam-surabaya-makassar', 'uploads/news/pbs_6a1b253a812302.61625706.png', 'PT. Prima Bahari Sejahtera dengan bangga mengumumkan pembukaan rute baru Batam–Surabaya–Makassar mulai bulan ini.', 'PT. Prima Bahari Sejahtera dengan bangga mengumumkan pembukaan rute pengiriman laut terbaru yang menghubungkan Batam–Surabaya–Makassar. Rute baru ini hadir sebagai respons atas meningkatnya permintaan pengiriman barang dari Batam ke kawasan Indonesia Timur.Dengan rute baru ini, pelanggan dapat menikmati layanan pengiriman yang lebih cepat dan efisien. Jadwal keberangkatan tersedia setiap minggu, sehingga memberikan fleksibilitas tinggi bagi para pelaku usaha.', 'Admin', 'Berita', NULL, 1, '2026-05-01 19:56:00', '2026-05-06 19:56:39', '2026-06-01 18:09:43');
INSERT INTO `news` VALUES (2, 'Tips Mengemas Barang untuk Pengiriman Laut', 'tips-mengemas-barang-pengiriman-laut', 'uploads/news/pbs_6a1b268ebb4cd6.31476715.png', 'Pengemasan yang tepat sangat penting untuk memastikan barang Anda tiba dalam kondisi sempurna. Simak tips dari tim PBS.', 'Pengiriman via kapal laut melibatkan perjalanan panjang di atas lautan. Oleh karena itu, pengemasan yang tepat sangat krusial untuk memastikan keselamatan barang Anda. 1. Gunakan Kardus Berkualitas Pilih kardus dengan ketebalan minimal 5 ply untuk barang yang rentan. Pastikan kardus dalam kondisi baru dan tidak lembab. 2. Bubble Wrap dan Styrofoam Gunakan lapisan pengaman ekstra untuk barang-barang pecah belah atau elektronik.', 'Admin PBS', 'Berita', NULL, 1, '2026-04-24 19:56:00', '2026-05-06 19:56:39', '2026-05-31 01:04:13');

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
  `notes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of routes
-- ----------------------------
INSERT INTO `routes` VALUES (1, 'Batam', 'Jakarta', 'Setiap Senin', '3-4 Hari', 1, 0, 'Cut-off barang H-1 sebelum keberangkatan. Melayani LCL &amp; FCL.');
INSERT INTO `routes` VALUES (2, 'Batam', 'Surabaya', 'Setiap Selasa', '5-6 Hari', 1, 0, 'Batas masuk gudang hari Senin jam 15.00 WIB. Estimasi Port-to-Port.');
INSERT INTO `routes` VALUES (3, 'Batam', 'Makassar', 'Setiap Rabu', '7-8 Hari', 1, 0, 'Khusus rute ini, wajib konfirmasi muatan H-3. Pengurusan dokumen FTZ dibantu.');
INSERT INTO `routes` VALUES (4, 'Batam', 'Medan', 'Setiap Kamis', '2-3 Hari', 1, 0, 'Rute cepat via Jalur Selat Malaka. Cocok untuk komoditas sensitif waktu.');

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
INSERT INTO `services` VALUES (1, 'Lost Cargo / LCL', 'lost-cargo-lcl', 'bi-box-seam', 'uploads/services/pbs_6a1b0fc5170cd4.74661142.png', 'Layanan pengiriman muatan satuan (Less than Container Load) yang ekonomis untuk berbagai jenis barang.', '', '[\"Biaya lebih ekonomis untuk muatan kecil\",\"Pengiriman ke seluruh wilayah Indonesia\",\"Pengemasan profesional & aman\",\"Tracking real-time\",\"Asuransi pengiriman tersedia\",\"Pickup & delivery tersedia\"]', 1, 1, 1, '2026-05-06 19:56:39', '2026-06-01 18:37:30');
INSERT INTO `services` VALUES (2, 'Full Container Load / FCL', 'full-container-load-fcl', 'bi-grid-3x3-gap', 'uploads/services/pbs_6a1b1e09564756.78925108.png', 'Sewa kontainer penuh untuk muatan besar. Tersedia ukuran 20 feet dan 40 feet sesuai kebutuhan.', 'Full Container Load (FCL) adalah layanan penyewaan kontainer secara utuh untuk memenuhi kebutuhan pengiriman barang dalam skala besar. Dengan FCL, muatan Anda tidak akan bercampur dengan muatan orang lain, memberikan keamanan dan privasi penuh bagi barang Anda. Kami menyediakan kontainer 20 feet dan 40 feet.', '[\"Kontainer 20ft & 40ft tersedia\",\"Keamanan muatan lebih terjamin\",\"Cocok untuk muatan besar\",\"Fleksibel jadwal keberangkatan\",\"Layanan stuffing & unstuffing\",\"Dokumen B\\/L & manifes lengkap\"]', 1, 1, 2, '2026-05-06 19:56:39', '2026-05-31 00:27:37');
INSERT INTO `services` VALUES (3, 'Freight Forwarding', 'freight-forwarding', 'bi-truck', 'uploads/services/pbs_6a1b1e479a4a94.00318351.png', 'Layanan pengurusan seluruh proses pengiriman, dari pengemasan, dokumentasi, hingga pengiriman ke tujuan.', 'Freight Forwarding adalah layanan manajemen logistik terpadu yang menangani seluruh aspek pengiriman barang Anda. Tim profesional kami akan mengurus semua proses, mulai dari pengambilan barang, pengemasan, dokumentasi ekspor-impor, pemrosesan bea cukai, hingga pengiriman akhir ke tujuan Anda.', '[\"Pengurusan dokumen ekspor-impor\",\"Clearance bea cukai\",\"Pengemasan & labeling\",\"Asuransi kargo\",\"Koordinasi multi-moda transportasi\",\"Laporan pengiriman real-time\"]', 1, 1, 3, '2026-05-06 19:56:39', '2026-05-31 00:28:39');

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
) ENGINE = InnoDB AUTO_INCREMENT = 4077 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of settings
-- ----------------------------
INSERT INTO `settings` VALUES (1, 'site_name', 'PT. Prima Bahari Sejahtera', '2026-05-06 19:56:39', '2026-06-01 18:08:19');
INSERT INTO `settings` VALUES (2, 'site_tagline', 'Freight Forwarding & Export - Import', '2026-05-06 19:56:39', '2026-06-01 18:24:27');
INSERT INTO `settings` VALUES (3, 'site_description', 'PT. Prima Bahari Sejahtera bergerak di bidang jasa pengiriman via kapal laut. Menerima pengiriman barang baik Lost Cargo maupun Full Container ke seluruh Indonesia.', '2026-05-06 19:56:39', '2026-05-20 22:05:52');
INSERT INTO `settings` VALUES (4, 'meta_keywords', 'freight forwarding, ekspor impor, pengiriman kapal laut, lost cargo, full container, Prima Bahari, PBS', '2026-05-06 19:56:39', '2026-05-20 22:05:20');
INSERT INTO `settings` VALUES (5, 'logo', '', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (6, 'favicon', 'images/favicon.png', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (7, 'og_image', '', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (8, 'contact_phone', '+62 778 123456', '2026-05-06 19:56:39', '2026-05-20 22:14:42');
INSERT INTO `settings` VALUES (9, 'contact_whatsapp', '+62 812 3456 7890', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (10, 'contact_email', 'info@primabaharisejahtera.co.id', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (11, 'contact_address', 'Jl. Pelabuhan Batu Ampar No. 12, Batam, Kepulauan Riau 29461', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (12, 'social_instagram', 'http://instagram.com/', '2026-05-06 19:56:39', '2026-05-20 22:16:46');
INSERT INTO `settings` VALUES (13, 'social_facebook', 'https://www.facebook.com/', '2026-05-06 19:56:39', '2026-05-20 22:16:15');
INSERT INTO `settings` VALUES (14, 'social_linkedin', 'https://id.linkedin.com/', '2026-05-06 19:56:39', '2026-05-20 22:16:15');
INSERT INTO `settings` VALUES (15, 'hero_video', 'uploads/hero/pbs_6a1b214da90045.50429923.mp4', '2026-05-06 19:56:39', '2026-05-31 00:41:33');
INSERT INTO `settings` VALUES (16, 'hero_title', 'Solusi Pengiriman Laut Terpercaya', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (17, 'hero_subtitle', 'PT. Prima Bahari Sejahtera melayani pengiriman Lost Cargo & Full Container ke seluruh pelosok Indonesia dengan armada kapal modern.', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (18, 'hero_btn1_text', 'Lihat Layanan Kami', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (19, 'hero_btn1_url', '/services.php', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (20, 'hero_btn2_text', 'Hubungi Kami', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (21, 'hero_btn2_url', '/contact.php', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (22, 'stat_years', '10+', '2026-05-06 19:56:39', '2026-05-20 22:17:40');
INSERT INTO `settings` VALUES (23, 'stat_routes', '50+', '2026-05-06 19:56:39', '2026-05-20 22:17:40');
INSERT INTO `settings` VALUES (24, 'stat_containers', '5.000+', '2026-05-06 19:56:39', '2026-05-20 22:17:51');
INSERT INTO `settings` VALUES (25, 'stat_clients', '200+', '2026-05-06 19:56:39', '2026-05-20 22:17:51');
INSERT INTO `settings` VALUES (26, 'about_title', 'Pengiriman Andal, Koneksi Nusantara', '2026-05-06 19:56:39', '2026-05-20 22:13:09');
INSERT INTO `settings` VALUES (27, 'about_description', 'Dengan pengalaman lebih dari 10 tahun, kami hadir sebagai mitra logistik terpercaya yang menghubungkan ribuan pulau di Indonesia. Kami menawarkan solusi pengiriman laut yang efisien, aman, dan tepat waktu—baik untuk muatan satuan (lost cargo) maupun kontainer penuh (full container).', '2026-05-06 19:56:39', '2026-05-20 22:13:23');
INSERT INTO `settings` VALUES (28, 'about_image', 'uploads/about_image/pbs_6a1b229202df09.29514595.png', '2026-05-06 19:56:39', '2026-05-31 00:46:58');
INSERT INTO `settings` VALUES (29, 'footer_copyright', '© 2025 PT. Prima Bahari Sejahtera. All Rights Reserved.', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (30, 'google_maps_embed', '', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (31, 'cta_title', 'Siap Kirimkan Muatan Anda?', '2026-05-06 19:56:39', '2026-05-20 22:14:29');
INSERT INTO `settings` VALUES (32, 'cta_description', 'Dapatkan penawaran terbaik untuk kebutuhan pengiriman laut Anda. Tim kami siap membantu 24/7.', '2026-05-06 19:56:39', '2026-05-06 19:56:39');
INSERT INTO `settings` VALUES (49, 'vision', 'ini adalah visi kami', '2026-05-20 21:59:22', '2026-05-27 19:31:13');
INSERT INTO `settings` VALUES (50, 'mission', 'ini adalah misi kami', '2026-05-20 21:59:22', '2026-05-27 19:31:13');
INSERT INTO `settings` VALUES (2189, 'smtp_gmail', 'leonardojaylenson28@gmail.com', '2026-05-25 22:59:47', '2026-05-25 23:07:27');
INSERT INTO `settings` VALUES (2190, 'smtp_app_password', 'siel dmuk jknq lzex', '2026-05-25 22:59:47', '2026-05-25 23:07:27');
INSERT INTO `settings` VALUES (2191, 'smtp_from_name', 'PT. Prima Bahari Sejahtera', '2026-05-25 22:59:47', '2026-05-25 22:59:47');
INSERT INTO `settings` VALUES (2192, 'smtp_reply_to', '', '2026-05-25 22:59:47', '2026-05-25 22:59:47');
INSERT INTO `settings` VALUES (2193, 'smtp_default_subject', 'Re: Pesan dari website PT. Prima Bahari Sejahtera', '2026-05-25 22:59:47', '2026-05-25 22:59:47');

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
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of testimonials
-- ----------------------------
INSERT INTO `testimonials` VALUES (1, 'Budi Santoso', 'CV. Maju Bersama', 'Direktur', '', 'Sudah 3 tahun bermitra dengan PBS, pelayanan sangat profesional dan tepat waktu. Barang selalu tiba dalam kondisi sempurna. Sangat direkomendasikan!!', 5, 1, 1, '2026-05-06 19:56:40');
INSERT INTO `testimonials` VALUES (2, 'Rina Dewi', 'PT. Nusantara Jaya', 'Manager Logistik', NULL, 'PBS membantu kami mengelola pengiriman FCL ke seluruh Indonesia. Dokumentasi lengkap, harga kompetitif, dan tim yang responsif. Puas banget!', 5, 1, 2, '2026-05-06 19:56:40');
INSERT INTO `testimonials` VALUES (3, 'Ahmad Fauzi', 'Toko Elektronik Makmur', 'Pemilik', NULL, 'Layanan Lost Cargo PBS sangat membantu bisnis kecil kami. Bisa kirim barang satuan dengan harga terjangkau. Tracking-nya juga mudah dipahami.', 5, 1, 3, '2026-05-06 19:56:40');
INSERT INTO `testimonials` VALUES (4, 'Siti Rahma', 'PT. Batam Textile', 'Supply Chain Manager', NULL, 'Pengurusan dokumen ekspor kami jauh lebih mudah sejak menggunakan PBS. Tim mereka sangat berpengalaman dan selalu siap membantu.', 4, 1, 4, '2026-05-06 19:56:40');

SET FOREIGN_KEY_CHECKS = 1;
