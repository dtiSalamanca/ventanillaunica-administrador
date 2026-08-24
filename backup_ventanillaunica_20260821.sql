-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: ventanillaunica
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `ventanillaunica`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `ventanillaunica` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `ventanillaunica`;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('sistema-de-administracion-de-la-ventanilla-unica-cache-ad_user_5','a:14:{s:10:\"id_usuario\";i:5;s:8:\"username\";s:10:\"desarrollo\";s:15:\"existe_en_mysql\";b:1;s:6:\"nombre\";s:8:\"JEFATURA\";s:8:\"apaterno\";s:2:\"DE\";s:8:\"amaterno\";s:10:\"DESARROLLO\";s:3:\"app\";s:32:\"Ventanilla Unica - Administrador\";s:6:\"rol_id\";i:1;s:3:\"rol\";s:13:\"Administrador\";s:7:\"id_area\";i:31;s:11:\"area_nombre\";s:45:\"Dirección de Tecnologías de la Información\";s:11:\"area_activo\";b:1;s:6:\"activo\";b:1;s:21:\"ad_password_encrypted\";s:200:\"eyJpdiI6ImRBQ3ZrREFTVExmWlZzQUJ1OUhVZEE9PSIsInZhbHVlIjoiMlRBaTUya2l3cC9uV2tYaENoNE5rZz09IiwibWFjIjoiYTgyNGEwZjdkY2QwMjI0NWUxYTE0OTlhYWRiNjIwM2RjMThjNDU3OTdmYmViYjczM2IzOGFlYzgzZjJmMGU0NyIsInRhZyI6IiJ9\";}',1787351594),('sistema-de-administracion-de-la-ventanilla-unica-cache-josafatmendozaperez@gmail.com|127.0.0.1','i:1;',1787322855),('sistema-de-administracion-de-la-ventanilla-unica-cache-josafatmendozaperez@gmail.com|127.0.0.1:timer','i:1787322855;',1787322855);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cat_dependencias`
--

DROP TABLE IF EXISTS `cat_dependencias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_dependencias` (
  `id_dependencia` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre_dependencia` varchar(255) NOT NULL,
  `estatus_dependencia` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_dependencia`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cat_dependencias`
--

LOCK TABLES `cat_dependencias` WRITE;
/*!40000 ALTER TABLE `cat_dependencias` DISABLE KEYS */;
INSERT INTO `cat_dependencias` VALUES (10,'Desarrollo Urbano y Ordenamiento Territorial',1,'2026-07-14 02:34:34','2026-07-14 02:34:34'),(11,'Tesorería Municipal',1,'2026-07-14 02:34:34','2026-07-14 02:34:34'),(12,'Registro Civil',1,'2026-07-14 02:34:34','2026-07-14 02:34:34'),(13,'Catastro Municipal',1,'2026-07-14 02:34:34','2026-07-14 02:34:34'),(14,'Obras Públicas',1,'2026-07-14 02:34:34','2026-07-14 02:34:34'),(15,'Servicios Públicos Municipales',1,'2026-07-14 02:34:34','2026-07-14 02:34:34');
/*!40000 ALTER TABLE `cat_dependencias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cat_documentos_personales`
--

DROP TABLE IF EXISTS `cat_documentos_personales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_documentos_personales` (
  `id_documento` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre_documento` varchar(255) NOT NULL,
  `descripcion_documento` text DEFAULT NULL,
  `vigencia_meses` int(11) NOT NULL,
  `estatus_documento` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_documento`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cat_documentos_personales`
--

LOCK TABLES `cat_documentos_personales` WRITE;
/*!40000 ALTER TABLE `cat_documentos_personales` DISABLE KEYS */;
INSERT INTO `cat_documentos_personales` VALUES (1,'CURP','Clave Única de Registro de Población. Documento de identidad oficial que asigna un código alfanumérico único a cada ciudadano mexicano.',55,1,'2026-07-01 14:49:35','2026-07-13 20:54:02'),(2,'Acta de Nacimiento','Documento oficial emitido por el Registro Civil que certifica el nacimiento de una persona.',0,1,'2026-07-01 17:51:05','2026-07-06 17:25:14'),(3,'Comprobante de Domicilio','Documento que acredita la residencia del ciudadano. Debe tener una antigüedad no mayor a 3 meses.',3,1,'2026-07-01 17:51:11','2026-07-06 17:25:14'),(4,'INE','Identificación oficial vigente que acredita la identidad del ciudadano.',24,1,'2026-07-02 20:17:45','2026-07-03 19:22:56'),(5,'prueba','prueba',1,1,'2026-08-12 14:57:02','2026-08-12 14:57:02');
/*!40000 ALTER TABLE `cat_documentos_personales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cat_documentos_predios`
--

DROP TABLE IF EXISTS `cat_documentos_predios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_documentos_predios` (
  `id_documento_predio` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre_documento` varchar(255) NOT NULL,
  `vigencia_meses` int(11) NOT NULL,
  `estatus_documento` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_documento_predio`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cat_documentos_predios`
--

LOCK TABLES `cat_documentos_predios` WRITE;
/*!40000 ALTER TABLE `cat_documentos_predios` DISABLE KEYS */;
INSERT INTO `cat_documentos_predios` VALUES (1,'Escritura del predio',12,1,'2026-07-03 21:05:08','2026-07-03 21:05:08'),(2,'Boleta predial',6,1,'2026-07-03 21:05:08','2026-07-03 21:05:08'),(3,'Plano de ubicación',24,1,'2026-07-03 21:05:08','2026-07-03 21:05:08'),(4,'Comprobante de pago de impuestos',3,0,'2026-07-03 21:05:08','2026-07-03 21:05:08'),(5,'aaac',23,1,'2026-07-03 21:05:42','2026-07-03 21:05:55'),(6,'Solicitud por escrito dirigida al titular del área',1,1,'2026-07-14 21:07:15','2026-07-14 21:07:15');
/*!40000 ALTER TABLE `cat_documentos_predios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cat_requisitos`
--

DROP TABLE IF EXISTS `cat_requisitos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_requisitos` (
  `id_requisito` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre_requisito` varchar(255) NOT NULL,
  `descripcion_requisito` text DEFAULT NULL,
  `estatus_requisito` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_requisito`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cat_requisitos`
--

LOCK TABLES `cat_requisitos` WRITE;
/*!40000 ALTER TABLE `cat_requisitos` DISABLE KEYS */;
INSERT INTO `cat_requisitos` VALUES (18,'Identificación oficial vigente (INE/IFE)',NULL,1,'2026-07-14 02:34:34','2026-07-14 02:34:34'),(19,'Clave Única de Registro de Población (CURP)',NULL,1,'2026-07-14 02:34:34','2026-07-14 02:34:34'),(20,'Comprobante de domicilio (no mayor a 3 meses)',NULL,1,'2026-07-14 02:34:34','2026-07-14 02:34:34'),(21,'Escrituras del predio debidamente inscritasssss2',NULL,1,'2026-07-14 02:34:34','2026-07-20 17:39:48'),(22,'Plano arquitectónico autorizado',NULL,1,'2026-07-14 02:34:34','2026-07-14 02:34:34'),(23,'Recibo de pago del impuesto predial del año en curso',NULL,1,'2026-07-14 02:34:34','2026-07-14 02:34:34'),(24,'Solicitud por escrito dirigida al titular del área',NULL,1,'2026-07-14 02:34:34','2026-07-14 02:34:34'),(25,'Fotografías del inmueble (frente y lateral)',NULL,0,'2026-07-14 02:34:34','2026-07-20 16:28:04'),(26,'Acta de nacimiento',NULL,1,'2026-07-14 02:34:34','2026-07-21 17:16:17'),(27,'Comprobante de pago de derechos',NULL,0,'2026-07-14 02:34:34','2026-07-20 16:30:54'),(28,'Registro Federal de Contribuyentes (RFC)',NULL,1,'2026-07-14 02:34:34','2026-07-14 02:34:34'),(29,'Poder legal o carta poder (en caso de representación)',NULL,1,'2026-07-14 02:34:34','2026-07-14 02:34:34');
/*!40000 ALTER TABLE `cat_requisitos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cat_tramites`
--

DROP TABLE IF EXISTS `cat_tramites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_tramites` (
  `id_tramite` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre_tramite` varchar(255) NOT NULL,
  `descripcion_tramite` text DEFAULT NULL,
  `estatus_tramite` tinyint(1) NOT NULL DEFAULT 1,
  `fk_dependencia` int(10) unsigned NOT NULL,
  `precio_tramite` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tramite_cri` int(11) NOT NULL DEFAULT 0,
  `cobra_por_m2` tinyint(1) NOT NULL DEFAULT 0,
  `vigencia_dias` int(10) unsigned NOT NULL DEFAULT 0,
  `cuenta_predial` int(11) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_tramite`)
) ENGINE=InnoDB AUTO_INCREMENT=903 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cat_tramites`
--

LOCK TABLES `cat_tramites` WRITE;
/*!40000 ALTER TABLE `cat_tramites` DISABLE KEYS */;
INSERT INTO `cat_tramites` VALUES (15,'Licencia de Construcción',NULL,1,10,1850.00,0,0,0,1,'2026-07-14 02:34:34','2026-07-14 02:34:34'),(16,'Licencia de Uso de Suelo',NULL,1,10,1200.00,0,0,0,1,'2026-07-14 02:34:34','2026-07-14 02:34:34'),(17,'Manifestación de Construcción',NULL,1,10,950.00,0,0,0,1,'2026-07-14 02:34:34','2026-07-14 02:34:34'),(18,'Pago de Impuesto Predial',NULL,1,11,0.00,0,0,0,1,'2026-07-14 02:34:34','2026-07-14 02:34:34'),(19,'Constancia de No Adeudo de Predial',NULL,1,11,150.00,0,0,0,1,'2026-07-14 02:34:34','2026-07-14 02:34:34'),(20,'Acta de Nacimiento (copia certificada)','Acta de Nacimiento (copia certificada)',1,12,0.00,1,0,3,0,'2026-07-14 02:34:34','2026-08-19 16:38:41'),(21,'Acta de Matrimonio',NULL,1,12,250.00,0,0,0,0,'2026-07-14 02:34:34','2026-07-14 02:34:34'),(22,'Avalúo Catastral',NULL,1,13,700.00,0,0,0,1,'2026-07-14 02:34:34','2026-07-14 02:34:34'),(23,'Constancia Catastral',NULL,1,13,350.00,0,0,0,1,'2026-07-14 02:34:34','2026-07-14 02:34:34'),(24,'Permiso de Obra Menor',NULL,1,14,650.00,0,0,0,1,'2026-07-14 02:34:34','2026-07-14 02:34:34'),(25,'Solicitud de Baja de Servicios (agua/alumbrado)',NULL,1,15,80.00,0,0,0,0,'2026-07-14 02:34:34','2026-07-14 02:34:34'),(26,'avaluo catastral express','avaluo que se realiza en 5 dias',1,13,190.00,0,0,0,1,'2026-07-24 15:21:39','2026-07-24 15:21:39'),(27,'prueba de m²','prueba de m²',1,10,0.00,3,1,0,1,'2026-08-10 20:27:31','2026-08-10 20:27:31'),(901,'PRUEBA API - Tramite Uno','Tramite de prueba para la API de ordenes de pago',1,1,150.00,111,0,0,1,'2026-08-17 15:54:44','2026-08-17 15:54:44'),(902,'PRUEBA API - Tramite Dos','Tramite de prueba para la API de ordenes de pago',1,1,250.00,222,0,0,1,'2026-08-17 15:54:44','2026-08-17 15:54:44');
/*!40000 ALTER TABLE `cat_tramites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_06_15_203845_create_dependencias_table',1),(5,'2026_06_15_203857_create_tramites_table',1),(6,'2026_06_15_203913_create_requisitos_table',1),(7,'2026_06_15_203924_create_solicituds_table',1),(8,'2026_06_24_100000_rename_requisitos_table',1),(9,'2026_06_24_100001_create_requisitos_tramites_table',1),(10,'2026_06_29_131814_rename_columns_and_table_tramites_requisitos',1),(11,'2026_06_29_134641_create_cat_documentos_personales_table',1),(12,'2026_06_29_134641_create_tbl_documentos_personales_table',1),(13,'2026_06_30_094216_modify_tbl_solicitudes_columns',1),(14,'2026_06_30_094439_modify_cat_dependencias_columns',1),(15,'2026_06_30_101236_drop_unused_tables',1),(16,'2026_06_30_102331_create_tbl_documentos_solicitud_table',1),(17,'2026_06_30_102331_create_tbl_documentos_tramites_table',1),(18,'2026_06_30_102331_create_tbl_resoluciones_solicitudes_table',1),(19,'2026_06_30_102331_create_tbl_turnados_solicitudes_table',1),(20,'2026_06_30_102331_create_tbl_usuarios_ad_table',1),(21,'2026_07_02_085742_add_ruta_archivo_to_tbl_documentos_personales_table',1),(22,'2026_07_03_082750_add_precio_tramite_to_cat_tramites_table',1),(23,'2026_07_03_095905_add_descripcion_tramite_to_cat_tramites_table',1),(24,'2026_07_03_131448_add_descripcion_requisito_to_cat_requisitos_table',1),(25,'2026_07_03_132028_add_descripcion_documento_to_cat_documentos_personales_table',1),(26,'2026_07_03_143336_create_tbl_predios_table',1),(27,'2026_07_03_143337_create_tbl_documentos_predios_table',1),(28,'2026_07_03_144128_create_cat_documentos_predios_table',1),(29,'2026_07_03_144236_modify_tbl_documentos_predios_columns',1),(30,'2026_07_03_150205_add_vigencia_meses_to_cat_documentos_predios_table',1),(31,'2026_07_06_121138_rename_fk_user_to_fk_usuario_in_tbl_predios_table',1),(32,'2026_07_15_151223_add_fk_predio_to_tbl_solicitudes_table',2),(34,'2026_07_20_090513_create_tbl_tramites_prerequisitos_table',3),(35,'2026_07_20_152033_alter_documento_resolucion_nullable',4),(45,'2026_08_03_131123_add_tramite_cri_to_cat_tramites_table',6),(46,'2026_08_06_090351_create_personal_access_tokens_table',6),(47,'2026_08_06_091845_create_ordenes_pagos_table',6),(48,'2026_08_06_151012_add_fkpredio',6),(49,'2026_08_10_141806_add_cobra_por_m2_to_cat_tramites_table',7),(50,'2026_08_11_115648_alter_tbl_requisitos_tramites_fk_requisito_fk_predio',8),(51,'2026_08_11_153055_add_motivo_rechazo_to_tbl_documentos_personales_table',9),(52,'2026_08_11_153055_add_motivo_rechazo_to_tbl_documentos_predios_table',9),(53,'2026_08_11_160011_add_motivo_rechazo_to_tbl_predios_table',10),(54,'2026_07_31_125104_add_numero_folio_to_tbl_resoluciones_solicitudes_table',11),(55,'2026_08_12_150041_add_bloqueado_to_users_table',11),(56,'2026_08_13_120000_add_consultado_to_tbl_predios_table',12),(57,'2026_08_13_154320_add_fk_solicitud_to_ordenes_pagos_table',13),(58,'2026_08_18_140401_add_vigencia_dias_to_cat_tramites_table',14),(59,'2026_08_20_084014_add_fecha_aprobacion_to_documentos_tables',15);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ordenes_pagos`
--

DROP TABLE IF EXISTS `ordenes_pagos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ordenes_pagos` (
  `id_orden_pago` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre_tramite` varchar(255) NOT NULL,
  `precio_tramite` decimal(10,2) NOT NULL DEFAULT 0.00,
  `numero_cri` int(11) NOT NULL,
  `orden_estatus` int(11) NOT NULL DEFAULT 1 COMMENT '1: Pendiente, 2: Pagada, 3: Cancelada',
  `folio_pago` varchar(255) DEFAULT NULL,
  `fk_tramite` bigint(20) DEFAULT NULL,
  `fk_solicitud` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_orden_pago`)
) ENGINE=InnoDB AUTO_INCREMENT=903 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ordenes_pagos`
--

LOCK TABLES `ordenes_pagos` WRITE;
/*!40000 ALTER TABLE `ordenes_pagos` DISABLE KEYS */;
INSERT INTO `ordenes_pagos` VALUES (1,'prueba de m²',1500.00,3,1,'VU-2026-0001',27,13,'2026-08-10 21:04:27','2026-08-18 16:32:32'),(2,'prueba de m²',100.00,3,1,NULL,27,15,'2026-08-11 16:42:11','2026-08-11 16:42:11'),(3,'Acta de Nacimiento (copia certificada)',50.00,1,1,'VU-2026-0002',20,7,'2026-08-13 21:52:13','2026-08-18 16:32:32'),(4,'avaluo catastral express',100.00,0,1,NULL,26,16,'2026-08-14 16:20:13','2026-08-14 16:20:13'),(901,'PRUEBA API - Tramite Uno',150.00,111,1,NULL,901,901,'2026-08-17 15:54:44','2026-08-17 15:54:44'),(902,'PRUEBA API - Tramite Dos',250.00,222,1,'',902,902,'2026-08-17 15:54:44','2026-08-17 15:54:44');
/*!40000 ALTER TABLE `ordenes_pagos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
INSERT INTO `password_reset_tokens` VALUES ('josafatraulmendoza@gmail.com','$2y$12$EHSmpoSOPU0ObHIE0Vc6suzDhELXPIT9uOzuj7fVDluVUYq3tf7nm','2026-08-12 20:17:58');
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('dRzFHEc7svmNYUCwqyfMXflbaMG68qge7g95icpO',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:154.0) Gecko/20100101 Firefox/154.0','eyJfdG9rZW4iOiJOYXRUcmdPVHp1eDdSenZaQVRydWNqUjQ3TWcxaUZwQXJWelJhYTFFIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19',1787236245),('e13PSXVk173veAtI8NjhPiaHI7Ixej4ezlTjYRaH',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:154.0) Gecko/20100101 Firefox/154.0','eyJfdG9rZW4iOiJmOGdiTThZUlpSd1dDNUF1U2FLQU9BSG9iYkdFV2FaTGZ2Vkt2V1dkIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDAiLCJyb3V0ZSI6bnVsbH19',1787322800),('Eaj8FWgyKdVM1Hnajz3mWzFJ6pazTHznEb6qJzhg',5,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:154.0) Gecko/20100101 Firefox/154.0','eyJfdG9rZW4iOiJaOXFnMFlyZU44Q3RueTJ2OFFXOFdaYlB2UHIwR0dLOG9UZmpFblluIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9hcHJvYmFjaW9uZXNcL2RvY3VtZW50b3MtcGVyc29uYWxlcyIsInJvdXRlIjoiaW5kZXhBcHJvYmFjaW9uZXNEb2N1bWVudG9zUGVyc29uYWxlcyJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX2FkXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjUsImF1dGgiOnsicGFzc3dvcmRfY29uZmlybWVkX2F0IjoxNzg3MjM2MzE2fX0=',1787241336),('LgMJvogTF8NtwTqfwFrO2WOMbAMStDZIz1tIORAd',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:154.0) Gecko/20100101 Firefox/154.0','eyJfdG9rZW4iOiJiMFBvZEE0c0ZFS3FQNjRZVzNaOVNmNnlXTk02dUhyR3VKaDNjSGphIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDEiLCJyb3V0ZSI6bnVsbH19',1787323100),('OwGsXpSKEZeLtSiA6lm5xwElPT9Iov84DUQnsuG0',25,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:154.0) Gecko/20100101 Firefox/154.0','eyJfdG9rZW4iOiJWY284Rml4bzhlSHBPZU5XZVI3TU5HYUFXa2hqU2lvcjFYWlhIelZvIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAxXC9wZXJmaWxlc1wvcHJlZGlvc1wvZXN0YXR1cyIsInJvdXRlIjoiZXN0YXR1c1ByZWRpb3MifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MjUsImF1dGgiOnsicGFzc3dvcmRfY29uZmlybWVkX2F0IjoxNzg3MjM3MDQ3fX0=',1787241332),('Qn6KWYw0HXXlBCql5tKwARn0Am28dCU6EbVOBMD5',5,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:154.0) Gecko/20100101 Firefox/154.0','eyJfdG9rZW4iOiJ3cm1hRVNBVHJkYWpIaWc3SmFTa2dqamx4T1NkNzZPb1h4WVk4UW9JIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9ob21lIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwibG9naW5fYWRfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6NSwiYXV0aCI6eyJwYXNzd29yZF9jb25maXJtZWRfYXQiOjE3ODczMjI3OTR9fQ==',1787322799);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_documentos_personales`
--

DROP TABLE IF EXISTS `tbl_documentos_personales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_documentos_personales` (
  `id_documento` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fk_usuario` bigint(20) unsigned NOT NULL,
  `fk_documento_personal` bigint(20) unsigned NOT NULL,
  `fecha_registro` date NOT NULL,
  `fecha_aprobacion` date DEFAULT NULL,
  `estatus_documento` int(11) NOT NULL DEFAULT 1,
  `motivo_rechazo` text DEFAULT NULL,
  `ruta_archivo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_documento`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_documentos_personales`
--

LOCK TABLES `tbl_documentos_personales` WRITE;
/*!40000 ALTER TABLE `tbl_documentos_personales` DISABLE KEYS */;
INSERT INTO `tbl_documentos_personales` VALUES (33,8,1,'2026-07-06',NULL,2,NULL,'documentos_personales/8/1/cBWjjqcscywJcmMbsZeMew4U7BW8PFTDY5HEECmO.pdf','2026-07-06 15:51:22','2026-07-13 20:54:22'),(34,8,3,'2026-07-06',NULL,1,NULL,'documentos_personales/8/3/EyloKOwkOPrqIpye4Q5JAkfGutVhfQzvipIb5ZIJ.pdf','2026-07-06 15:51:57','2026-07-06 15:51:57'),(35,8,2,'2026-07-06',NULL,1,NULL,'documentos_personales/8/2/Xs61OHa3d3u5zmo0HbsNDioZJPUEsn2PlieTYvFy.pdf','2026-07-06 15:52:37','2026-07-06 15:52:37'),(36,8,4,'2026-07-06',NULL,0,NULL,'documentos_personales/8/4/xpV0EGX2WlvcWNz4s1TX9YtyttqJTBgXRBR6Odu3.pdf','2026-07-06 15:53:49','2026-07-13 20:58:44'),(37,25,4,'2026-07-14',NULL,2,NULL,'documentos_personales/25/4/5RD96eNKD2VQcLsfmsErT85dQv1T3Oj7O6nSmrEQ.pdf','2026-07-14 19:29:56','2026-07-14 19:41:08'),(38,25,1,'2026-07-14',NULL,2,NULL,'documentos_personales/25/1/rq7nMTduhYh6KKYkaDtqTlDDEyhcbpj1E951LftC.pdf','2026-07-14 19:30:25','2026-07-14 19:41:16'),(39,25,3,'2026-07-14',NULL,2,NULL,'documentos_personales/25/3/yMRKcAdssBOU82UTWJ6w6q7cBszbLGnBSfkrcmwp.pdf','2026-07-14 19:30:30','2026-07-24 15:31:18'),(40,25,2,'2026-07-24',NULL,2,NULL,'documentos_personales/25/2/4MttPe4ynV14CWCRbJpZatVIVrUrwEL5Y78JOZtb.pdf','2026-07-14 19:30:35','2026-07-24 15:32:34'),(41,25,5,'2026-08-20','2026-08-20',2,'hola','documentos_personales/25/5/CKfHkQE5D1j9sUdX1qGXZGMcVQC79rVQu9JJ9MXz.pdf','2026-08-12 14:57:13','2026-08-20 15:52:11');
/*!40000 ALTER TABLE `tbl_documentos_personales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_documentos_predios`
--

DROP TABLE IF EXISTS `tbl_documentos_predios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_documentos_predios` (
  `id_documento_predio` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fk_cat_documento_predio` bigint(20) unsigned NOT NULL,
  `ruta_documento` varchar(255) NOT NULL,
  `fk_predio` bigint(20) unsigned NOT NULL,
  `estatus_documento` int(11) NOT NULL DEFAULT 1,
  `fecha_aprobacion` date DEFAULT NULL,
  `motivo_rechazo` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_documento_predio`),
  KEY `tbl_documentos_predios_fk_predio_foreign` (`fk_predio`),
  KEY `tbl_documentos_predios_fk_cat_documento_predio_foreign` (`fk_cat_documento_predio`),
  CONSTRAINT `tbl_documentos_predios_fk_cat_documento_predio_foreign` FOREIGN KEY (`fk_cat_documento_predio`) REFERENCES `cat_documentos_predios` (`id_documento_predio`) ON DELETE CASCADE,
  CONSTRAINT `tbl_documentos_predios_fk_predio_foreign` FOREIGN KEY (`fk_predio`) REFERENCES `tbl_predios` (`id_predio`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_documentos_predios`
--

LOCK TABLES `tbl_documentos_predios` WRITE;
/*!40000 ALTER TABLE `tbl_documentos_predios` DISABLE KEYS */;
INSERT INTO `tbl_documentos_predios` VALUES (1,5,'documentos_predios/8/1/5/F8F31HQk6ROyIrd7K6HlwYo2QX42mGQKQZmox73N.pdf',1,2,NULL,NULL,'2026-07-06 15:54:25','2026-07-06 19:14:37'),(2,2,'documentos_predios/8/1/2/MLjFaPVGIrHjuEx8vury7JruOm7s2AVXeL2kN5r4.pdf',1,2,NULL,NULL,'2026-07-06 15:54:40','2026-07-06 19:16:41'),(3,1,'documentos_predios/8/1/1/rC0DK6O6IMhrJhDkwkMOsON9p6V3pAKQKAiuT7sM.pdf',1,2,NULL,NULL,'2026-07-06 17:43:26','2026-07-06 19:16:45'),(4,3,'documentos_predios/8/1/3/SF8oOlzZYWKGRb1DvXCLaUt2jA3mVTByQPS1LnVQ.pdf',1,2,NULL,NULL,'2026-07-06 17:44:16','2026-07-06 19:16:47'),(5,3,'documentos_predios/25/3/3/HHugdWrLWXcpJnNKV8O8ruex8RlQRR13VEiCYMcK.pdf',3,2,NULL,NULL,'2026-07-14 20:10:08','2026-07-14 21:05:05'),(6,2,'documentos_predios/25/3/2/TX2O9mktHnTLosAIhIsh9bLaAcjqb0mhpnJcjke3.pdf',3,2,NULL,NULL,'2026-07-14 20:10:15','2026-07-14 20:10:38'),(7,1,'documentos_predios/25/3/1/t0S2CzsN7uMzp33hXmARbEW3z8BN9qpvTeQWK11K.pdf',3,2,NULL,NULL,'2026-07-14 20:10:21','2026-07-14 20:10:36'),(8,6,'documentos_predios/25/3/6/T81IuZ14TrfOtm20N3PIho8NIw5AojXpBo3UVeR6.pdf',3,2,'2026-08-20',NULL,'2026-07-14 21:07:48','2026-08-20 15:44:17'),(9,5,'documentos_predios/25/3/5/1iN7w7SgbQbbJij89IvRy0JbU0esSX5q42UFxyzi.pdf',3,2,NULL,NULL,'2026-08-07 20:26:33','2026-08-07 20:27:20'),(10,1,'documentos_predios/25/4/1/6HvIq0Ox7Ibeb4SdXcDwTE4xO0lwzYul9WKtiqwb.pdf',4,1,NULL,NULL,'2026-08-11 14:33:46','2026-08-11 14:33:46'),(11,2,'documentos_predios/25/11/2/fGwAqdBUk5cB6PCeFyZJzautCGKAGG3jmOz3tKEQ.pdf',11,2,NULL,NULL,'2026-08-13 20:32:47','2026-08-13 20:33:25'),(12,1,'documentos_predios/25/11/1/E73yej5SUZ9C3XOMLzqZcZ97NxkDtFOOwaq1W0hz.pdf',11,2,NULL,NULL,'2026-08-13 20:32:50','2026-08-13 20:33:20'),(13,3,'documentos_predios/25/11/3/mWow8oBox2O2WzuFCHuIBnX1V0xIV7PHLF0kEv1T.pdf',11,2,NULL,NULL,'2026-08-13 20:32:55','2026-08-13 20:33:14'),(14,5,'documentos_predios/25/11/5/horpC3BrcH5ioz5hCL2USFSVdMO5G5EdxuUFwBly.pdf',11,2,NULL,NULL,'2026-08-13 20:33:00','2026-08-13 20:33:11'),(15,6,'documentos_predios/25/11/6/Y6ZxLnow70vOifD8wfZqHfNRm2l6hufPE40ZxVH5.pdf',11,2,NULL,NULL,'2026-08-13 20:34:00','2026-08-13 20:34:24');
/*!40000 ALTER TABLE `tbl_documentos_predios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_documentos_solicitud`
--

DROP TABLE IF EXISTS `tbl_documentos_solicitud`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_documentos_solicitud` (
  `id_documento` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fk_solicitud` bigint(20) unsigned NOT NULL,
  `documento_solicitud` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_documento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_documentos_solicitud`
--

LOCK TABLES `tbl_documentos_solicitud` WRITE;
/*!40000 ALTER TABLE `tbl_documentos_solicitud` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_documentos_solicitud` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_documentos_tramites`
--

DROP TABLE IF EXISTS `tbl_documentos_tramites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_documentos_tramites` (
  `id_documento` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fk_requisito` bigint(20) unsigned NOT NULL,
  `fk_documento_personal` bigint(20) unsigned DEFAULT NULL,
  `fk_documento_solicitud` bigint(20) unsigned DEFAULT NULL,
  `fk_solicitud` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_documento`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_documentos_tramites`
--

LOCK TABLES `tbl_documentos_tramites` WRITE;
/*!40000 ALTER TABLE `tbl_documentos_tramites` DISABLE KEYS */;
INSERT INTO `tbl_documentos_tramites` VALUES (1,18,37,NULL,1,'2026-07-15 21:25:47','2026-07-15 21:25:47'),(2,24,NULL,NULL,1,'2026-07-15 21:25:47','2026-07-15 21:25:47'),(3,18,37,NULL,2,'2026-07-15 21:43:18','2026-07-15 21:43:18'),(4,24,NULL,NULL,2,'2026-07-15 21:43:18','2026-07-15 21:43:18'),(5,18,37,NULL,3,'2026-07-15 22:49:38','2026-07-15 22:49:38'),(6,24,NULL,NULL,3,'2026-07-15 22:49:38','2026-07-15 22:49:38'),(7,18,37,NULL,4,'2026-07-17 23:08:26','2026-07-17 23:08:26'),(8,24,NULL,NULL,4,'2026-07-17 23:08:26','2026-07-17 23:08:26'),(9,18,37,NULL,5,'2026-07-20 22:31:02','2026-07-20 22:31:02'),(10,19,38,NULL,5,'2026-07-20 22:31:02','2026-07-20 22:31:02'),(11,18,37,NULL,6,'2026-07-21 03:23:03','2026-07-21 03:23:03'),(12,19,38,NULL,6,'2026-07-21 03:23:03','2026-07-21 03:23:03'),(13,18,37,NULL,7,'2026-07-21 03:23:09','2026-07-21 03:23:09'),(14,19,38,NULL,7,'2026-07-21 03:23:09','2026-07-21 03:23:09'),(15,18,37,NULL,8,'2026-07-21 03:23:23','2026-07-21 03:23:23'),(16,19,38,NULL,8,'2026-07-21 03:23:23','2026-07-21 03:23:23'),(17,18,37,NULL,9,'2026-07-21 21:36:36','2026-07-21 21:36:36'),(18,19,38,NULL,9,'2026-07-21 21:36:36','2026-07-21 21:36:36'),(19,18,37,NULL,10,'2026-07-24 21:15:47','2026-07-24 21:15:47'),(20,19,38,NULL,10,'2026-07-24 21:15:47','2026-07-24 21:15:47'),(21,18,37,NULL,11,'2026-07-31 03:42:54','2026-07-31 03:42:54'),(22,19,38,NULL,11,'2026-07-31 03:42:54','2026-07-31 03:42:54'),(23,2,40,NULL,12,'2026-08-08 02:27:41','2026-08-08 02:27:41'),(24,2,NULL,NULL,12,'2026-08-08 02:27:41','2026-08-08 02:27:41'),(25,1,38,NULL,12,'2026-08-08 02:27:41','2026-08-08 02:27:41'),(26,3,39,NULL,12,'2026-08-08 02:27:41','2026-08-08 02:27:41'),(27,1,NULL,NULL,12,'2026-08-08 02:27:41','2026-08-08 02:27:41'),(28,4,37,NULL,12,'2026-08-08 02:27:41','2026-08-08 02:27:41'),(29,3,NULL,NULL,12,'2026-08-08 02:27:41','2026-08-08 02:27:41'),(30,6,NULL,NULL,12,'2026-08-08 02:27:41','2026-08-08 02:27:41'),(31,5,NULL,NULL,12,'2026-08-08 02:27:41','2026-08-08 02:27:41'),(32,1,NULL,NULL,13,'2026-08-11 02:31:24','2026-08-11 02:31:24'),(33,3,NULL,NULL,13,'2026-08-11 02:31:24','2026-08-11 02:31:24'),(34,6,NULL,NULL,13,'2026-08-11 02:31:24','2026-08-11 02:31:24'),(35,1,38,NULL,14,'2026-08-11 20:35:57','2026-08-11 20:35:57'),(36,3,39,NULL,14,'2026-08-11 20:35:57','2026-08-11 20:35:57'),(37,1,NULL,NULL,15,'2026-08-11 20:43:42','2026-08-11 20:43:42'),(38,3,NULL,NULL,15,'2026-08-11 20:43:42','2026-08-11 20:43:42'),(39,6,NULL,NULL,15,'2026-08-11 20:43:42','2026-08-11 20:43:42'),(40,1,38,NULL,16,'2026-08-14 16:17:11','2026-08-14 16:17:11'),(41,3,39,NULL,16,'2026-08-14 16:17:11','2026-08-14 16:17:11'),(42,4,37,NULL,903,'2026-08-19 16:30:04','2026-08-19 16:30:04');
/*!40000 ALTER TABLE `tbl_documentos_tramites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_predios`
--

DROP TABLE IF EXISTS `tbl_predios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_predios` (
  `id_predio` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `clave_predio` varchar(255) NOT NULL,
  `estatus_predio` int(11) NOT NULL DEFAULT 1,
  `consultado` tinyint(4) NOT NULL DEFAULT 0,
  `motivo_rechazo` text DEFAULT NULL,
  `fk_usuario` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_predio`),
  KEY `tbl_predios_fk_usuario_foreign` (`fk_usuario`),
  CONSTRAINT `tbl_predios_fk_usuario_foreign` FOREIGN KEY (`fk_usuario`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_predios`
--

LOCK TABLES `tbl_predios` WRITE;
/*!40000 ALTER TABLE `tbl_predios` DISABLE KEYS */;
INSERT INTO `tbl_predios` VALUES (1,'2000MAH2USMAZE',2,0,NULL,8,'2026-07-03 21:21:24','2026-07-06 19:39:45'),(2,'SSSSS2222ss',2,0,NULL,8,'2026-07-06 19:35:06','2026-07-06 19:39:50'),(3,'12334567890',2,0,NULL,25,'2026-07-14 19:59:50','2026-07-14 20:08:00'),(4,'12743456789',2,0,NULL,25,'2026-07-24 15:07:19','2026-08-03 21:40:45'),(6,'25A000142060',0,0,'no saludos',25,'2026-08-12 14:43:44','2026-08-12 14:43:53'),(7,'123456789011',2,0,NULL,25,'2026-08-12 16:19:09','2026-08-12 16:23:31'),(8,'2345678987654',2,0,NULL,25,'2026-08-12 16:27:47','2026-08-12 16:32:42'),(9,'1234567890',0,1,'El predio registrado no existe en el sistema de predial.',25,'2026-08-13 16:00:20','2026-08-13 20:01:11'),(10,'25D000057001',2,2,NULL,25,'2026-08-13 19:36:40','2026-08-13 20:01:26'),(11,'25G001331001',2,2,NULL,25,'2026-08-13 20:16:12','2026-08-13 20:16:49'),(12,'25R000566003',2,2,NULL,25,'2026-08-13 20:38:29','2026-08-13 20:38:36');
/*!40000 ALTER TABLE `tbl_predios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_requisitos_tramites`
--

DROP TABLE IF EXISTS `tbl_requisitos_tramites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_requisitos_tramites` (
  `id_requisito` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fk_requisito` varchar(255) DEFAULT NULL,
  `fk_predio` varchar(255) DEFAULT NULL,
  `fk_tramite` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_requisito`)
) ENGINE=InnoDB AUTO_INCREMENT=127 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_requisitos_tramites`
--

LOCK TABLES `tbl_requisitos_tramites` WRITE;
/*!40000 ALTER TABLE `tbl_requisitos_tramites` DISABLE KEYS */;
INSERT INTO `tbl_requisitos_tramites` VALUES (109,'2',NULL,16,'2026-08-07 19:44:04','2026-08-07 19:44:04'),(110,'3',NULL,16,'2026-08-07 19:44:04','2026-08-07 19:44:04'),(111,'1',NULL,16,'2026-08-07 19:44:04','2026-08-07 19:44:04'),(112,'4',NULL,16,'2026-08-07 19:44:04','2026-08-07 19:44:04'),(113,NULL,'5',16,'2026-08-07 19:44:04','2026-08-07 19:44:04'),(114,NULL,'2',16,'2026-08-07 19:44:04','2026-08-07 19:44:04'),(115,NULL,'1',16,'2026-08-07 19:44:04','2026-08-07 19:44:04'),(116,NULL,'3',16,'2026-08-07 19:44:04','2026-08-07 19:44:04'),(117,NULL,'6',16,'2026-08-07 19:44:04','2026-08-07 19:44:04'),(118,'3',NULL,20,'2026-08-07 21:21:29','2026-08-07 21:21:29'),(120,NULL,'1',27,'2026-08-10 20:30:38','2026-08-10 20:30:38'),(121,NULL,'3',27,'2026-08-10 20:30:38','2026-08-10 20:30:38'),(122,NULL,'6',27,'2026-08-10 20:30:38','2026-08-10 20:30:38'),(123,'3',NULL,26,'2026-08-11 14:34:58','2026-08-11 14:34:58'),(124,'1',NULL,26,'2026-08-11 14:34:58','2026-08-11 14:34:58'),(125,'4',NULL,21,'2026-08-19 16:29:08','2026-08-19 16:29:08'),(126,NULL,'6',26,'2026-08-20 15:40:59','2026-08-20 15:40:59');
/*!40000 ALTER TABLE `tbl_requisitos_tramites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_resoluciones_solicitudes`
--

DROP TABLE IF EXISTS `tbl_resoluciones_solicitudes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_resoluciones_solicitudes` (
  `id_resolucion` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fk_turnado` bigint(20) unsigned NOT NULL,
  `resolucion_solicitud` text NOT NULL,
  `documento_resolucion` varchar(255) DEFAULT NULL,
  `numero_folio` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_resolucion`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_resoluciones_solicitudes`
--

LOCK TABLES `tbl_resoluciones_solicitudes` WRITE;
/*!40000 ALTER TABLE `tbl_resoluciones_solicitudes` DISABLE KEYS */;
INSERT INTO `tbl_resoluciones_solicitudes` VALUES (1,2,'saludos jsjsjs','doc_resolutivos/Res_1_2026-07-20.pdf',NULL,'2026-07-20 21:21:30','2026-07-20 21:21:30'),(2,3,'Rechazado: sera para la proxima saludos',NULL,NULL,'2026-07-20 21:24:11','2026-07-20 21:24:11'),(7,10,'hola','doc_resolutivos/Res_7_2026-08-10.pdf',NULL,'2026-08-10 21:04:27','2026-08-10 21:04:27'),(8,11,'renombrar','doc_resolutivos/TeM-15-2026-08-11.pdf',NULL,'2026-08-11 16:42:11','2026-08-11 16:42:13'),(9,9,'hola','doc_resolutivos/TeM-12-2026-08-12.pdf',NULL,'2026-08-12 15:19:02','2026-08-12 15:19:02'),(10,4,'pruebas','doc_resolutivos/TeM-07-2026-08-13.pdf',NULL,'2026-08-13 21:52:14','2026-08-13 21:52:14'),(11,13,'hola','doc_resolutivos/TeM-16-2026-08-14.pdf',NULL,'2026-08-14 16:20:13','2026-08-14 16:20:14');
/*!40000 ALTER TABLE `tbl_resoluciones_solicitudes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_solicitudes`
--

DROP TABLE IF EXISTS `tbl_solicitudes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_solicitudes` (
  `id_solicitud` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fk_usuario` int(10) unsigned NOT NULL,
  `fk_tramite` bigint(20) unsigned NOT NULL,
  `fk_predio` bigint(20) unsigned DEFAULT NULL,
  `fecha_solicitud` datetime DEFAULT NULL,
  `fecha_resolucion` datetime DEFAULT NULL,
  `observacion_solicitud` text DEFAULT NULL,
  `validez_solicitud` date DEFAULT NULL,
  `estatus_solicitud` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_solicitud`)
) ENGINE=InnoDB AUTO_INCREMENT=904 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_solicitudes`
--

LOCK TABLES `tbl_solicitudes` WRITE;
/*!40000 ALTER TABLE `tbl_solicitudes` DISABLE KEYS */;
INSERT INTO `tbl_solicitudes` VALUES (3,25,19,3,'2026-07-15 10:49:38','2026-07-17 11:00:26','pruebas saludos',NULL,2,'2026-07-15 16:49:38','2026-07-17 17:00:26'),(4,25,19,3,'2026-07-17 11:08:26','2026-07-20 15:21:30',NULL,NULL,3,'2026-07-17 17:08:26','2026-07-20 21:21:30'),(5,25,20,NULL,'2026-07-20 10:31:02','2026-07-20 15:14:18',NULL,NULL,6,'2026-07-20 16:31:02','2026-08-19 17:48:13'),(6,25,20,NULL,'2026-07-20 15:23:03','2026-07-20 15:24:11','sera para la proxima saludos',NULL,2,'2026-07-20 21:23:03','2026-07-20 21:24:11'),(7,25,20,NULL,'2026-07-20 15:23:09','2026-08-13 15:52:14',NULL,NULL,6,'2026-07-20 21:23:09','2026-08-19 17:48:13'),(12,25,16,3,'2026-08-07 14:27:41','2026-08-12 09:19:02',NULL,NULL,3,'2026-08-07 20:27:41','2026-08-12 15:19:02'),(13,25,27,3,'2026-08-10 14:31:24','2026-08-10 15:04:27',NULL,NULL,5,'2026-08-10 20:31:24','2026-08-19 17:48:13'),(14,25,26,4,'2026-08-11 08:35:57','2026-08-14 10:19:15',NULL,NULL,1,'2026-08-11 14:35:57','2026-08-14 16:19:15'),(15,25,27,3,'2026-08-11 08:43:42','2026-08-11 10:42:11',NULL,NULL,3,'2026-08-11 14:43:42','2026-08-11 16:42:11'),(16,25,26,8,'2026-08-14 10:17:11','2026-08-14 10:20:13',NULL,NULL,3,'2026-08-14 16:17:11','2026-08-14 16:20:13'),(901,901,901,NULL,'2026-08-17 09:54:44',NULL,NULL,NULL,3,'2026-08-17 15:54:44','2026-08-17 15:54:44'),(902,901,902,NULL,'2026-08-17 09:54:44',NULL,NULL,NULL,3,'2026-08-17 15:54:44','2026-08-17 15:54:44'),(903,25,21,NULL,'2026-08-19 10:30:04',NULL,NULL,NULL,0,'2026-08-19 16:30:04','2026-08-19 16:30:04');
/*!40000 ALTER TABLE `tbl_solicitudes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_tramites_prerequisitos`
--

DROP TABLE IF EXISTS `tbl_tramites_prerequisitos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_tramites_prerequisitos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fk_tramite` bigint(20) unsigned NOT NULL,
  `fk_tramite_requerido` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_tramite_prerequisito` (`fk_tramite`,`fk_tramite_requerido`),
  KEY `tbl_tramites_prerequisitos_fk_tramite_requerido_foreign` (`fk_tramite_requerido`),
  CONSTRAINT `tbl_tramites_prerequisitos_fk_tramite_foreign` FOREIGN KEY (`fk_tramite`) REFERENCES `cat_tramites` (`id_tramite`) ON DELETE CASCADE,
  CONSTRAINT `tbl_tramites_prerequisitos_fk_tramite_requerido_foreign` FOREIGN KEY (`fk_tramite_requerido`) REFERENCES `cat_tramites` (`id_tramite`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_tramites_prerequisitos`
--

LOCK TABLES `tbl_tramites_prerequisitos` WRITE;
/*!40000 ALTER TABLE `tbl_tramites_prerequisitos` DISABLE KEYS */;
INSERT INTO `tbl_tramites_prerequisitos` VALUES (4,18,20,'2026-07-21 16:24:29','2026-07-21 16:24:29'),(5,18,23,'2026-07-24 14:59:11','2026-07-24 14:59:11');
/*!40000 ALTER TABLE `tbl_tramites_prerequisitos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_turnados_solicitudes`
--

DROP TABLE IF EXISTS `tbl_turnados_solicitudes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_turnados_solicitudes` (
  `id_turnado` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fk_usuario_ad` bigint(20) unsigned NOT NULL,
  `fk_solicitud` bigint(20) unsigned NOT NULL,
  `estatus_turnado` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_turnado`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_turnados_solicitudes`
--

LOCK TABLES `tbl_turnados_solicitudes` WRITE;
/*!40000 ALTER TABLE `tbl_turnados_solicitudes` DISABLE KEYS */;
INSERT INTO `tbl_turnados_solicitudes` VALUES (1,1,5,1,'2026-07-20 16:31:23','2026-07-20 16:31:23'),(2,1,4,1,'2026-07-20 21:16:11','2026-07-20 21:16:11'),(3,1,6,1,'2026-07-20 21:23:41','2026-07-20 21:23:41'),(4,1,7,1,'2026-07-20 21:23:49','2026-07-20 21:23:49'),(9,1,12,1,'2026-08-07 20:44:00','2026-08-07 20:44:00'),(10,1,13,1,'2026-08-10 20:32:05','2026-08-10 20:32:05'),(11,1,15,1,'2026-08-11 14:43:58','2026-08-11 14:43:58'),(12,1,14,1,'2026-08-14 16:19:15','2026-08-14 16:19:15'),(13,1,16,1,'2026-08-14 16:19:39','2026-08-14 16:19:39');
/*!40000 ALTER TABLE `tbl_turnados_solicitudes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_usuarios_ad`
--

DROP TABLE IF EXISTS `tbl_usuarios_ad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_usuarios_ad` (
  `id_usuario` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre_usuario` varchar(255) NOT NULL,
  `fk_dependencia` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_usuarios_ad`
--

LOCK TABLES `tbl_usuarios_ad` WRITE;
/*!40000 ALTER TABLE `tbl_usuarios_ad` DISABLE KEYS */;
INSERT INTO `tbl_usuarios_ad` VALUES (1,'notificacion',11,'2026-07-17 20:01:23','2026-07-17 20:01:23');
/*!40000 ALTER TABLE `tbl_usuarios_ad` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `bloqueado` tinyint(1) NOT NULL DEFAULT 0,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=902 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (8,'Cristhian Jair Rangel Parra','cthjxir@gmail.com','2026-07-01 02:39:49',0,'$2y$12$29vbNHGWNDHsQNjb4dBMouPO.597LZHAE5C4jKfQ9jNVvtyM/JpjK',NULL,'2026-06-30 20:39:17','2026-07-03 16:53:00'),(9,'Prueba Pendiente 1','prueba.pendiente.1@example.test',NULL,1,'$2y$12$29vbNHGWNDHsQNjb4dBMouPO.597LZHAE5C4jKfQ9jNVvtyM/JpjK',NULL,'2026-07-01 15:06:53','2026-08-12 21:07:56'),(10,'Prueba Pendiente 2','prueba.pendiente.2@example.test',NULL,0,'$2y$12$29vbNHGWNDHsQNjb4dBMouPO.597LZHAE5C4jKfQ9jNVvtyM/JpjK',NULL,'2026-07-01 15:06:54','2026-07-01 15:06:54'),(11,'Prueba Pendiente 3','prueba.pendiente.3@example.test',NULL,0,'$2y$12$29vbNHGWNDHsQNjb4dBMouPO.597LZHAE5C4jKfQ9jNVvtyM/JpjK',NULL,'2026-07-01 15:06:54','2026-07-01 15:06:54'),(12,'Prueba Pendiente 4','prueba.pendiente.4@example.test',NULL,0,'$2y$12$29vbNHGWNDHsQNjb4dBMouPO.597LZHAE5C4jKfQ9jNVvtyM/JpjK',NULL,'2026-07-01 15:06:54','2026-07-01 15:06:54'),(13,'Prueba Pendiente 5','prueba.pendiente.5@example.test',NULL,0,'$2y$12$29vbNHGWNDHsQNjb4dBMouPO.597LZHAE5C4jKfQ9jNVvtyM/JpjK',NULL,'2026-07-01 15:06:54','2026-07-01 15:06:54'),(14,'Prueba Pendiente 6','prueba.pendiente.6@example.test',NULL,0,'$2y$12$29vbNHGWNDHsQNjb4dBMouPO.597LZHAE5C4jKfQ9jNVvtyM/JpjK',NULL,'2026-07-01 15:06:55','2026-07-01 15:06:55'),(15,'Prueba Pendiente 7','prueba.pendiente.7@example.test',NULL,0,'$2y$12$29vbNHGWNDHsQNjb4dBMouPO.597LZHAE5C4jKfQ9jNVvtyM/JpjK',NULL,'2026-07-01 15:06:55','2026-07-01 15:06:55'),(16,'Prueba Pendiente 8','prueba.pendiente.8@example.test',NULL,0,'$2y$12$29vbNHGWNDHsQNjb4dBMouPO.597LZHAE5C4jKfQ9jNVvtyM/JpjK',NULL,'2026-07-01 15:06:55','2026-07-01 15:06:55'),(17,'Prueba Revisado 1','prueba.revisado.1@example.test',NULL,0,'$2y$12$29vbNHGWNDHsQNjb4dBMouPO.597LZHAE5C4jKfQ9jNVvtyM/JpjK',NULL,'2026-07-01 15:06:55','2026-07-01 15:06:55'),(18,'Prueba Revisado 2','prueba.revisado.2@example.test',NULL,0,'$2y$12$29vbNHGWNDHsQNjb4dBMouPO.597LZHAE5C4jKfQ9jNVvtyM/JpjK',NULL,'2026-07-01 15:06:55','2026-07-01 15:06:55'),(19,'Prueba Revisado 3','prueba.revisado.3@example.test',NULL,0,'$2y$12$29vbNHGWNDHsQNjb4dBMouPO.597LZHAE5C4jKfQ9jNVvtyM/JpjK',NULL,'2026-07-01 15:06:56','2026-07-01 15:06:56'),(20,'Prueba Revisado 4','prueba.revisado.4@example.test',NULL,0,'$2y$12$29vbNHGWNDHsQNjb4dBMouPO.597LZHAE5C4jKfQ9jNVvtyM/JpjK',NULL,'2026-07-01 15:06:56','2026-07-01 15:06:56'),(21,'Prueba Revisado 5','prueba.revisado.5@example.test',NULL,0,'$2y$12$29vbNHGWNDHsQNjb4dBMouPO.597LZHAE5C4jKfQ9jNVvtyM/JpjK',NULL,'2026-07-01 15:06:56','2026-07-01 15:06:56'),(22,'Prueba Revisado 6','prueba.revisado.6@example.test',NULL,0,'$2y$12$29vbNHGWNDHsQNjb4dBMouPO.597LZHAE5C4jKfQ9jNVvtyM/JpjK',NULL,'2026-07-01 15:06:56','2026-07-01 15:06:56'),(23,'Prueba Revisado 7','prueba.revisado.7@example.test',NULL,0,'$2y$12$29vbNHGWNDHsQNjb4dBMouPO.597LZHAE5C4jKfQ9jNVvtyM/JpjK',NULL,'2026-07-01 15:06:57','2026-07-01 15:06:57'),(24,'Prueba Revisado 8','prueba.revisado.8@example.test',NULL,0,'$2y$12$29vbNHGWNDHsQNjb4dBMouPO.597LZHAE5C4jKfQ9jNVvtyM/JpjK',NULL,'2026-07-01 15:06:57','2026-07-01 15:06:57'),(25,'R_Josafat','josafatmendozaperez@gmail.com','2026-07-15 01:28:15',0,'$2y$12$xy/slNH33C4qQzL45BvhR.1cAC0rAmdCCtJ935pfOb2E.04hFSFJ.',NULL,'2026-07-14 19:27:23','2026-08-12 21:20:05'),(41,'Josafat','josafatraulmendoza@gmail.com',NULL,0,'$2y$12$XHcqwclaMWucy5ebtmbeKuWkawhLPwtA4/NcOs58ByBfLyBlnE.Jm',NULL,'2026-08-11 21:16:48','2026-08-12 21:05:11'),(901,'Ciudadano Prueba API','api.prueba@test.local','2026-08-17 15:54:44',0,'$2y$12$XvZyTm4MK2h.oio7WAoaK.WFgVLzVLp93SIzeIcqUePPteMCPBrJC',NULL,'2026-08-17 15:54:44','2026-08-17 15:54:44');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-21  8:44:28
