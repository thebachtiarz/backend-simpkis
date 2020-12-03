-- create database simpkis
CREATE DATABASE `simpkis` /*!40100 DEFAULT CHARACTER SET utf8 */;

-- simpkis.users definition
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- simpkis.personal_access_tokens definition
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- simpkis.user_biodatas definition
CREATE TABLE `user_biodatas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- simpkis.user_statuses definition
CREATE TABLE `user_statuses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- simpkis.kelas_groups definition
CREATE TABLE `kelas_groups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tingkat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- simpkis.kelas definition
CREATE TABLE `kelas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_group` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kelas_id_group_foreign` (`id_group`),
  CONSTRAINT `kelas_id_group_foreign` FOREIGN KEY (`id_group`) REFERENCES `kelas_groups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- simpkis.siswas definition
CREATE TABLE `siswas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nisn` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_kelas` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `siswas_nisn_unique` (`nisn`),
  KEY `siswas_id_kelas_foreign` (`id_kelas`),
  CONSTRAINT `siswas_id_kelas_foreign` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=631 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- simpkis.ketua_kelas definition
CREATE TABLE `ketua_kelas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_siswa` bigint(20) unsigned NOT NULL,
  `id_kelas` bigint(20) unsigned NOT NULL,
  `id_user` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ketua_kelas_id_siswa_unique` (`id_siswa`),
  UNIQUE KEY `ketua_kelas_id_kelas_unique` (`id_kelas`),
  KEY `ketua_kelas_id_user_foreign` (`id_user`),
  CONSTRAINT `ketua_kelas_id_kelas_foreign` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ketua_kelas_id_siswa_foreign` FOREIGN KEY (`id_siswa`) REFERENCES `siswas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ketua_kelas_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- simpkis.semesters definition
CREATE TABLE `semesters` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `semester` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `semesters_semester_unique` (`semester`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- simpkis.kegiatans definition
CREATE TABLE `kegiatans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nilai` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `nilai_avg` int(11) NOT NULL DEFAULT '0',
  `hari` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `waktu_mulai` time NOT NULL,
  `waktu_selesai` time NOT NULL,
  `akses` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- simpkis.presensi_groups definition
CREATE TABLE `presensi_groups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_kegiatan` bigint(20) unsigned NOT NULL,
  `id_user` bigint(20) unsigned NOT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `approve` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `presensi_groups_id_kegiatan_foreign` (`id_kegiatan`),
  KEY `presensi_groups_id_user_foreign` (`id_user`),
  CONSTRAINT `presensi_groups_id_kegiatan_foreign` FOREIGN KEY (`id_kegiatan`) REFERENCES `kegiatans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `presensi_groups_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2101 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- simpkis.presensis definition
CREATE TABLE `presensis` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_presensi` bigint(20) unsigned NOT NULL,
  `id_semester` bigint(20) unsigned NOT NULL,
  `id_siswa` bigint(20) unsigned NOT NULL,
  `nilai` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `presensis_id_presensi_foreign` (`id_presensi`),
  KEY `presensis_id_semester_foreign` (`id_semester`),
  KEY `presensis_id_siswa_foreign` (`id_siswa`),
  CONSTRAINT `presensis_id_presensi_foreign` FOREIGN KEY (`id_presensi`) REFERENCES `presensi_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `presensis_id_semester_foreign` FOREIGN KEY (`id_semester`) REFERENCES `semesters` (`id`) ON DELETE CASCADE,
  CONSTRAINT `presensis_id_siswa_foreign` FOREIGN KEY (`id_siswa`) REFERENCES `siswas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=63001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- simpkis.nilai_tambahans definition
CREATE TABLE `nilai_tambahans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_semester` bigint(20) unsigned NOT NULL,
  `id_siswa` bigint(20) unsigned NOT NULL,
  `id_kegiatan` bigint(20) unsigned NOT NULL,
  `nilai` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `nilai_tambahans_id_semester_foreign` (`id_semester`),
  KEY `nilai_tambahans_id_siswa_foreign` (`id_siswa`),
  KEY `nilai_tambahans_id_kegiatan_foreign` (`id_kegiatan`),
  CONSTRAINT `nilai_tambahans_id_kegiatan_foreign` FOREIGN KEY (`id_kegiatan`) REFERENCES `kegiatans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `nilai_tambahans_id_semester_foreign` FOREIGN KEY (`id_semester`) REFERENCES `semesters` (`id`) ON DELETE CASCADE,
  CONSTRAINT `nilai_tambahans_id_siswa_foreign` FOREIGN KEY (`id_siswa`) REFERENCES `siswas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- simpkis.nilai_akhir_groups definition
CREATE TABLE `nilai_akhir_groups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_semester` bigint(20) unsigned NOT NULL,
  `id_kelas` bigint(20) unsigned NOT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `nilai_akhir_groups_id_semester_foreign` (`id_semester`),
  KEY `nilai_akhir_groups_id_kelas_foreign` (`id_kelas`),
  CONSTRAINT `nilai_akhir_groups_id_kelas_foreign` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `nilai_akhir_groups_id_semester_foreign` FOREIGN KEY (`id_semester`) REFERENCES `semesters` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- simpkis.nilai_akhirs definition
CREATE TABLE `nilai_akhirs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_nilai` bigint(20) unsigned NOT NULL,
  `id_semester` bigint(20) unsigned NOT NULL,
  `id_siswa` bigint(20) unsigned NOT NULL,
  `nilai_akhir` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `nilai_akhirs_id_nilai_foreign` (`id_nilai`),
  KEY `nilai_akhirs_id_semester_foreign` (`id_semester`),
  KEY `nilai_akhirs_id_siswa_foreign` (`id_siswa`),
  CONSTRAINT `nilai_akhirs_id_nilai_foreign` FOREIGN KEY (`id_nilai`) REFERENCES `nilai_akhir_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `nilai_akhirs_id_semester_foreign` FOREIGN KEY (`id_semester`) REFERENCES `semesters` (`id`) ON DELETE CASCADE,
  CONSTRAINT `nilai_akhirs_id_siswa_foreign` FOREIGN KEY (`id_siswa`) REFERENCES `siswas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=631 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
