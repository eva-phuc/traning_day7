-- MySQL dump 10.13  Distrib 5.1.69, for redhat-linux-gnu (x86_64)
--
-- Host: localhost    Database: haq_drive
-- ------------------------------------------------------
-- Server version	5.1.69

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `bookmarks`
--

DROP TABLE IF EXISTS `bookmarks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bookmarks` (
  `bookmark_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '管理用ID',
  `user_id` int(11) NOT NULL COMMENT 'ユーザーID',
  `item_id` int(11) NOT NULL COMMENT '商品ID',
  `option_id` varchar(128) DEFAULT NULL COMMENT 'オプションID',
  `alert` enum('on','off') DEFAULT 'off' COMMENT 'アラート設定',
  `alert_set_price` int(11) DEFAULT NULL COMMENT 'アラート設定用価格',
  `alert_done_price` int(11) DEFAULT NULL COMMENT 'アラート済価格',
  `price` int(11) DEFAULT NULL COMMENT '登録時価格',
  `stock` int(11) DEFAULT NULL COMMENT '登録時在庫',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成日時',
  `updated_at` datetime DEFAULT NULL COMMENT '更新日時',
  `deleted_at` datetime DEFAULT NULL COMMENT '削除日時',
  PRIMARY KEY (`bookmark_id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookmarks`
--

LOCK TABLES `bookmarks` WRITE;
/*!40000 ALTER TABLE `bookmarks` DISABLE KEYS */;
INSERT INTO `bookmarks` VALUES (1,66255,1,NULL,'on',1,8200,8300,10,'2014-08-12 05:44:22','2014-08-12 14:44:22',NULL),(2,66255,2,'12703660','on',1,6048,6148,2,'2014-08-12 05:45:10','2014-08-12 14:45:10',NULL),(3,66120,2,'12703657','on',1,6048,6148,2,'2014-08-12 05:46:42','2014-08-12 14:46:42',NULL),(4,66255,3,'10bl0253-wh-S','on',1,1569,1669,99,'2014-08-12 05:47:00','2014-08-12 14:47:00',NULL),(5,2,4,'13824473','on',1,14040,14140,0,'2014-08-12 05:47:00','2014-08-12 14:47:00',NULL),(6,66120,5,NULL,'on',1,16800,16900,1,'2014-08-12 05:47:37','2014-08-12 14:47:37',NULL),(7,66255,4,'13824474','on',1,14040,14140,5,'2014-08-12 05:48:34','2014-08-12 14:48:34',NULL),(8,64869,6,NULL,'on',1,16740000,16740100,99,'2014-08-12 06:28:42','2014-11-07 17:19:54',NULL),(9,66120,7,NULL,'on',1,NULL,12500,1,'2014-08-12 06:47:43','2014-08-12 15:47:43',NULL),(10,66255,7,NULL,'on',1,NULL,12500,1,'2014-08-12 08:15:20','2014-08-12 17:15:20',NULL),(11,66255,8,NULL,'on',1,10000,12500,3,'2014-08-13 05:32:15','2014-08-13 14:32:15',NULL),(12,66255,9,NULL,'on',1,NULL,5990,5,'2014-08-13 05:34:12','2014-08-13 14:34:12',NULL),(13,66255,2,'12703657','on',1,NULL,6048,1,'2014-08-13 05:34:45','2014-08-13 14:34:45',NULL),(14,66255,10,NULL,'on',1,NULL,1234,99,'2014-08-13 05:35:13','2014-08-13 14:35:13',NULL),(15,66255,11,NULL,'on',1,NULL,1440,99,'2014-08-13 05:36:01','2014-08-13 14:36:01',NULL),(16,66255,12,NULL,'on',1,NULL,1280,99,'2014-08-13 05:36:20','2014-08-13 14:36:20',NULL),(17,66230,12,NULL,'on',1,NULL,1280,99,'2014-08-28 07:20:07','2014-08-28 16:20:07',NULL),(18,66230,11,NULL,'on',1,NULL,1440,99,'2014-09-06 08:14:17','2014-09-06 17:14:17',NULL),(19,66230,13,NULL,'on',1,NULL,4280,3,'2014-09-06 09:14:03','2014-09-08 21:45:04','2014-09-08 21:45:04'),(20,66230,6,NULL,'on',1,NULL,16740000,99,'2014-09-06 09:24:02','2014-09-06 18:24:02',NULL),(21,66230,5,NULL,'on',1,NULL,16800,1,'2014-09-06 09:26:50','2014-09-06 18:26:50',NULL),(22,66230,2,'12703657','on',1,NULL,6048,0,'2014-09-06 09:28:30','2014-09-08 21:45:00',NULL),(23,66434,13,NULL,'off',1,NULL,4280,2,'2014-09-08 07:18:01','2014-09-08 16:18:01',NULL),(24,66434,14,NULL,'on',1,NULL,6980,2,'2014-09-08 07:20:25','2014-09-08 16:20:25',NULL),(25,66434,1,NULL,'on',1,NULL,8200,9,'2014-09-08 07:26:26','2014-09-08 16:26:26',NULL),(26,66434,5,NULL,'on',1,NULL,16800,1,'2014-09-08 07:39:47','2014-09-08 16:39:47',NULL),(27,66230,15,NULL,'off',1,NULL,143000,4,'2014-09-08 12:50:59','2014-09-08 21:50:59',NULL),(28,66230,16,NULL,'on',1,NULL,2550,99,'2014-09-09 15:35:34','2014-09-10 00:35:34',NULL),(29,66120,17,NULL,'on',1,NULL,94000,9,'2014-09-10 06:17:47','2014-09-10 15:17:47',NULL),(30,66120,18,NULL,'on',1,NULL,12300,2,'2014-09-10 06:22:59','2014-09-10 15:22:59',NULL),(31,66529,17,NULL,'on',1,NULL,94000,9,'2014-09-10 16:32:53','2014-09-11 01:32:53',NULL),(32,66120,14,NULL,'on',1,NULL,6980,2,'2014-09-11 06:31:09','2014-09-11 15:31:09',NULL),(33,66529,19,NULL,'off',1,NULL,3800,8,'2014-09-11 06:40:13','2014-09-11 15:40:13',NULL),(34,66529,5,NULL,'on',1,NULL,16800,1,'2014-09-12 04:00:57','2014-09-12 13:00:57',NULL),(35,66529,18,NULL,'off',1,NULL,12300,2,'2014-09-12 05:41:21','2014-09-12 14:41:21',NULL),(36,66541,16,NULL,'on',1,NULL,2550,99,'2014-09-12 06:47:01','2014-09-12 15:47:01',NULL),(37,66547,12,NULL,'off',1,NULL,1280,99,'2014-09-12 07:39:50','2014-09-12 16:39:50',NULL),(38,66120,19,NULL,'on',1,NULL,3800,8,'2014-09-16 11:12:09','2014-09-16 20:12:09',NULL),(39,66120,20,NULL,'on',1,6000,6500,3,'2014-09-16 11:32:06','2014-09-16 20:32:06',NULL),(40,66624,5,NULL,'on',1,NULL,16800,1,'2014-09-16 14:00:21','2014-09-16 23:00:21',NULL),(41,66624,20,NULL,'on',1,NULL,6500,2,'2014-09-17 12:26:08','2014-09-17 21:33:06',NULL),(42,66624,21,'14385054','off',1,NULL,17064,3,'2014-09-17 12:34:56','2014-09-17 21:34:56',NULL),(43,66255,14,NULL,'off',1,NULL,6980,3,'2014-09-17 13:41:42','2014-09-17 22:41:42',NULL),(44,66644,21,'14385044','on',1,NULL,17064,5,'2014-09-17 13:55:14','2014-09-17 22:55:27',NULL),(45,66120,21,'14385044','on',1,NULL,17064,3,'2014-10-21 07:00:18','2014-10-21 16:00:18',NULL),(46,66120,12,NULL,'on',1,NULL,1280,99,'2014-10-21 07:02:35','2014-10-21 16:02:35',NULL),(47,66120,22,NULL,'on',1,NULL,10500,4,'2014-10-21 07:35:09','2014-10-21 16:35:09',NULL),(48,66120,23,NULL,'on',1,NULL,182800,1,'2014-10-24 02:19:29','2014-10-24 11:19:29',NULL),(49,69092,23,NULL,'on',1,NULL,182800,1,'2014-10-27 11:18:59','2014-10-27 20:18:59',NULL),(50,69092,22,NULL,'on',1,NULL,10500,4,'2014-10-27 11:27:58','2014-10-27 20:27:58',NULL),(51,69092,17,NULL,'on',1,NULL,94000,9,'2014-10-28 02:42:19','2014-10-28 11:42:19',NULL);
/*!40000 ALTER TABLE `bookmarks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `denyinfo`
--

DROP TABLE IF EXISTS `denyinfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `denyinfo` (
  `user_id` int(11) NOT NULL,
  `mail_alert` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `denyinfo`
--

LOCK TABLES `denyinfo` WRITE;
/*!40000 ALTER TABLE `denyinfo` DISABLE KEYS */;
INSERT INTO `denyinfo` VALUES (0,1,NULL,'2014-05-30 12:15:55','0000-00-00 00:00:00'),(65837,0,NULL,'2014-05-30 12:45:34','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `denyinfo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item_options`
--

DROP TABLE IF EXISTS `item_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item_options` (
  `item_id` int(11) NOT NULL COMMENT '商品ID',
  `option_id` varchar(128) NOT NULL COMMENT 'オプションID',
  `option_values` text COMMENT 'オプション内容',
  `stock` int(11) DEFAULT NULL COMMENT 'オプションの在庫',
  `img_url` varchar(256) DEFAULT NULL COMMENT '商品画像URL',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成日時',
  `updated_at` datetime DEFAULT NULL COMMENT '更新日時',
  `deleted_at` datetime DEFAULT NULL COMMENT '削除日時',
  PRIMARY KEY (`item_id`,`option_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_options`
--

LOCK TABLES `item_options` WRITE;
/*!40000 ALTER TABLE `item_options` DISABLE KEYS */;
INSERT INTO `item_options` VALUES (2,'12703657','YToyOntpOjA7YToyOntzOjQ6Im5hbWUiO3M6Mzoi6ImyIjtzOjU6InZhbHVlIjtzOjEyOiLjg5vjg6/jgqTjg4giO31pOjE7YToyOntzOjQ6Im5hbWUiO3M6OToi44K144Kk44K6IjtzOjU6InZhbHVlIjtzOjE6IlMiO319',0,'http://img5.zozo.jp/goodsimages/576/4569576/4569576_1_D_35.jpg','2014-08-12 05:46:42','2014-09-09 04:00:04',NULL),(2,'12703660','YToyOntpOjA7YToyOntzOjQ6Im5hbWUiO3M6Mzoi6ImyIjtzOjU6InZhbHVlIjtzOjEyOiLjgrPjg7zjg6njg6siO31pOjE7YToyOntzOjQ6Im5hbWUiO3M6OToi44K144Kk44K6IjtzOjU6InZhbHVlIjtzOjE6IlMiO319',0,'http://img5.zozo.jp/goodsimages/576/4569576/4569576_367_D_35.jpg','2014-08-12 05:45:10','2014-09-09 04:00:04',NULL),(3,'10bl0253-wh-S','YToyOntpOjA7YToyOntzOjQ6Im5hbWUiO3M6OToi44Kr44Op44O8IjtzOjU6InZhbHVlIjtzOjEyOiLjg5vjg6/jgqTjg4giO31pOjE7YToyOntzOjQ6Im5hbWUiO3M6OToi44K144Kk44K6IjtzOjU6InZhbHVlIjtzOjE6IlMiO319',99,NULL,'2014-08-12 05:47:00','2014-11-12 03:00:02',NULL),(4,'13824473','YToyOntpOjA7YToyOntzOjQ6Im5hbWUiO3M6Mzoi6ImyIjtzOjU6InZhbHVlIjtzOjE1OiLjgqLjgqTjg5zjg6rjg7wiO31pOjE7YToyOntzOjQ6Im5hbWUiO3M6OToi44K144Kk44K6IjtzOjU6InZhbHVlIjtzOjQ6IkZSRUUiO319',0,'http://img5.zozo.jp/goodsimages/492/5057492/5057492_38_D_35.jpg','2014-08-12 05:47:00','2014-10-15 04:00:13',NULL),(4,'13824474','YToyOntpOjA7YToyOntzOjQ6Im5hbWUiO3M6Mzoi6ImyIjtzOjU6InZhbHVlIjtzOjEyOiLjg5bjg6njg4Pjgq8iO31pOjE7YToyOntzOjQ6Im5hbWUiO3M6OToi44K144Kk44K6IjtzOjU6InZhbHVlIjtzOjQ6IkZSRUUiO319',0,'http://img5.zozo.jp/goodsimages/492/5057492/5057492_8_D_35.jpg','2014-08-12 05:48:34','2014-10-15 04:00:13',NULL),(21,'14385044','YToyOntpOjA7YToyOntzOjQ6Im5hbWUiO3M6Mzoi6ImyIjtzOjU6InZhbHVlIjtzOjEyOiLjg5njg7zjgrjjg6UiO31pOjE7YToyOntzOjQ6Im5hbWUiO3M6OToi44K144Kk44K6IjtzOjU6InZhbHVlIjtzOjg6Ik9ORSBTSVpFIjt9fQ==',3,'http://img5.zozo.jp/goodsimages/266/5339266/5339266_14_D_35.jpg','2014-09-17 13:55:14','2014-11-12 04:00:23',NULL),(21,'14385054','YToyOntpOjA7YToyOntzOjQ6Im5hbWUiO3M6Mzoi6ImyIjtzOjU6InZhbHVlIjtzOjEyOiLjg43jgqTjg5Pjg7wiO31pOjE7YToyOntzOjQ6Im5hbWUiO3M6OToi44K144Kk44K6IjtzOjU6InZhbHVlIjtzOjg6Ik9ORSBTSVpFIjt9fQ==',5,'http://img5.zozo.jp/goodsimages/266/5339266/5339266_16_D_35.jpg','2014-09-17 12:34:56','2014-11-12 04:00:23',NULL);
/*!40000 ALTER TABLE `item_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '管理用ID',
  `shop_id` int(11) NOT NULL COMMENT 'ショップID',
  `url` varchar(256) DEFAULT NULL COMMENT '商品詳細URL',
  `item_name` varchar(512) DEFAULT NULL COMMENT '商品名',
  `price` int(11) DEFAULT NULL COMMENT '現在価格',
  `old_price` int(11) DEFAULT NULL COMMENT '更新前価格',
  `default_price` int(11) DEFAULT NULL COMMENT '通常価格',
  `bookmark_high_price` int(11) DEFAULT NULL COMMENT '登録最高価格',
  `sale_price` int(11) DEFAULT NULL COMMENT 'セール価格',
  `sale` enum('yes','no') NOT NULL DEFAULT 'no' COMMENT 'セール状態',
  `stock` int(11) DEFAULT NULL COMMENT '在庫',
  `img_url` varchar(256) DEFAULT NULL COMMENT '商品画像URL',
  `brand` varchar(128) DEFAULT NULL COMMENT 'ブランド',
  `category` varchar(256) DEFAULT NULL COMMENT 'カテゴリー',
  `item_code` varchar(64) DEFAULT NULL COMMENT '商品識別コード',
  `shop_code` varchar(64) DEFAULT NULL COMMENT 'ショップ識別コード',
  `bookmark_count` int(11) NOT NULL DEFAULT '0' COMMENT '登録数',
  `update_fail_count` int(11) NOT NULL DEFAULT '0' COMMENT '更新失敗回数',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成日時',
  `updated_at` datetime DEFAULT NULL COMMENT '更新日時',
  `deleted_at` datetime DEFAULT NULL COMMENT '削除日時',
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items`
--

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
INSERT INTO `items` VALUES (1,3,'http://www.buyma.com/item/12923701/','送料無料・国内発送ASOS  Midi Bandeau Sundress In Check Print',8200,8200,8200,8300,NULL,'no',7,'http://static.buyma.com/imgdata/item/140424/0012923701/428_1.jpg','\n									ASOS(エイソス)\n								','レディースファッション > ワンピース > ワンピース','12923701',NULL,2,0,'2014-08-12 05:44:22','2014-11-12 05:00:02',NULL),(2,2,'http://zozo.jp/shop/rosebudcouples/goods/3569576/','(SEEDS OF CALIFORNIA FOR RB)6/40S C.SLUB FRILL SLEEVE TOP',6048,6048,6048,6148,NULL,'no',0,'http://img5.zozo.jp/goodsimages/576/4569576/4569576_367_D_500.jpg','ROSE BUD COUPLES WOMENS','トップス > Tシャツ・カットソー','3569576','rosebudcouples',4,98,'2014-08-12 05:45:10','2014-11-12 04:00:10',NULL),(3,1,'http://store.shopping.yahoo.co.jp/karei-fuku/10bl0253.html','クロップドパンツ カラーパンツ スキニー ボトムス カラーパン レギンス 花柄 レース 8分丈 フィット スリム 送料無料',1569,1569,1569,1669,NULL,'no',99,'http://item.shopping.c.yimg.jp/i/l/karei-fuku_10bl0253',NULL,NULL,'10bl0253','karei-fuku',1,0,'2014-08-12 05:47:00','2014-11-12 03:00:01',NULL),(4,2,'http://zozo.jp/shop/urbanresearch/goods/4057492/','ROSSO ビックチェックコットンシャツ',14040,14040,14040,14140,NULL,'no',0,'http://img5.zozo.jp/goodsimages/492/5057492/5057492_34_D_500.jpg','ROSSO','トップス > シャツ・ブラウス','4057492','urbanresearch',2,56,'2014-08-12 05:47:00','2014-11-12 04:00:19',NULL),(5,3,'http://www.buyma.com/item/11708721/','Asics Gel Lyte Ⅲ 【日本未発売】 アシックスゲルライト',16800,16800,16800,16900,NULL,'no',0,'http://static.buyma.com/imgdata/item/131202/0011708721/428_1.jpg','\n									asics(アシックス)\n								','メンズファッション > 靴・ブーツ・サンダル > スニーカー','11708721',NULL,5,0,'2014-08-12 05:47:37','2014-11-12 05:00:03',NULL),(6,1,'http://store.shopping.yahoo.co.jp/yoshida/br01-tourbillon.html','ベル＆ロス 時計 Bell&amp;Ross AVIATION　BR 01 46 MM　BR 01 TOURBILLON ＜世界限定60本＞BR 01 TOURBILLON',16740000,16740000,16740000,16740100,NULL,'no',99,'http://item.shopping.c.yimg.jp/i/l/yoshida_br01-tourbillon',NULL,NULL,'br01-tourbillon','yoshida',2,0,'2014-08-12 06:28:42','2014-11-12 03:00:03',NULL),(7,3,'http://www.buyma.com/item/12870074/','新作! ★UGG Australia★ &quot;Bennison II&quot; サンダル',12500,12500,12500,12600,NULL,'no',1,'http://static.buyma.com/imgdata/item/140418/0012870074/428_1.jpg','\n									UGG Australia(アグ オーストラリア)\n								','メンズファッション > 靴・ブーツ・サンダル > サンダル','12870074',NULL,2,113,'2014-08-12 06:47:43','2014-11-12 05:00:10',NULL),(8,3,'http://www.buyma.com/item/13581443/','新作!セレブ愛用ロマンチックadidasOriginals×THE FARM COMPANY',12800,12800,29800,12500,12800,'yes',2,'http://static.buyma.com/imgdata/item/140708/0013581443/428_1.jpg','\n									adidas(アディダス)\n								','レディースファッション > 靴・シューズ > スニーカー','13581443',NULL,1,13,'2014-08-13 05:32:15','2014-11-12 05:00:18',NULL),(9,3,'http://www.buyma.com/item/13740607/','シックなオールインワン2色',5990,5990,5990,5990,NULL,'no',2,'http://static.buyma.com/imgdata/item/140725/0013740607/428_1.jpg',NULL,'レディースファッション > ワンピース > オールインワン','13740607',NULL,1,111,'2014-08-13 05:34:12','2014-11-12 05:00:24',NULL),(10,1,'http://store.shopping.yahoo.co.jp/zcz/fc01032-0.html','Tシャツ レディース ロングTシャツ Tシャツ レディース',1234,1234,1234,1234,NULL,'no',99,'http://item.shopping.c.yimg.jp/i/l/zcz_fc01032-0',NULL,NULL,'fc01032-0','zcz',1,0,'2014-08-13 05:35:13','2014-11-12 03:00:04',NULL),(11,1,'http://store.shopping.yahoo.co.jp/zcz/fc01034-0.html','カットソー Tシャツレディース  ロングTシャツTシャツレディース  長袖SEXYプライマーシャツ　',1440,1440,1440,1440,NULL,'no',99,'http://item.shopping.c.yimg.jp/i/l/zcz_fc01034-0',NULL,NULL,'fc01034-0','zcz',2,0,'2014-08-13 05:36:01','2014-11-12 03:00:05',NULL),(12,1,'http://store.shopping.yahoo.co.jp/zcz/c40706002.html','コットン　ラウンドネック　Tシャツ　数字小さいポケット　レディース　可愛い　Tシャツ',1280,1280,1280,1280,NULL,'no',99,'http://item.shopping.c.yimg.jp/i/l/zcz_c40706002',NULL,NULL,'c40706002','zcz',4,0,'2014-08-13 05:36:20','2014-11-12 03:00:06',NULL),(13,3,'http://www.buyma.com/item/13169858/','ウエストゴム コットン ５分袖 サロペットパンツ ダークブルー',4280,4280,4280,4280,NULL,'no',2,'http://static.buyma.com/imgdata/item/140521/0013169858/428_1.jpg',NULL,'レディースファッション > ワンピース > オールインワン','13169858',NULL,2,96,'2014-09-06 09:14:03','2014-11-12 05:00:30',NULL),(14,3,'http://www.buyma.com/item/11463530/','新作　オシャレ♪2カラーパフスリーブオールインワン　送料込',6980,6980,6980,6980,6980,'no',1,'http://static.buyma.com/imgdata/item/131107/0011463530/428_1.jpg',NULL,'レディースファッション > ワンピース > オールインワン','11463530',NULL,3,0,'2014-09-08 07:20:24','2014-11-12 05:00:32',NULL),(15,3,'http://www.buyma.com/item/14002382/','15AW MON082 MONCLER &quot;HERMINE&quot; PADDED COAT',145000,145000,236520,143000,145000,'yes',2,'http://static.buyma.com/imgdata/item/140824/0014002382/428_1.jpg','\n									MONCLER(モンクレール)\n								','レディースファッション > アウター > ダウンジャケット','14002382',NULL,1,0,'2014-09-08 12:50:59','2014-11-12 05:00:33',NULL),(16,1,'http://store.shopping.yahoo.co.jp/zcz/fc40322005.html','ショートパンツ付き 水着 レディース　ビキニ　タンキニ　シンプル　安い 水着通販 安い ビキニ 体型カバー',2550,2550,2550,2550,NULL,'no',99,'http://item.shopping.c.yimg.jp/i/l/zcz_fc40322005',NULL,NULL,'fc40322005','zcz',2,71,'2014-09-09 15:35:34','2014-11-12 03:00:10',NULL),(17,3,'http://www.buyma.com/item/13510065/','【秋冬★新作!】ルブタンSIMPLENODO PATENTリボントップ10㎝',94500,94000,94500,94000,NULL,'no',9,'http://static.buyma.com/imgdata/item/140629/0013510065/428_1.jpg','\n									Christian Louboutin(クリスチャンルブタン)\n								','レディースファッション > 靴・シューズ > パンプス・ミュール','13510065',NULL,3,0,'2014-09-10 06:17:47','2014-11-12 05:00:35',NULL),(18,3,'http://www.buyma.com/item/13522743/','【関税込♪】UGG Australia☆Ithan Slide サンダル',12300,12300,12300,12300,NULL,'no',2,'http://static.buyma.com/imgdata/item/140701/0013522743/428_1.jpg','\n									UGG Australia(アグ オーストラリア)\n								','メンズファッション > 靴・ブーツ・サンダル > サンダル','13522743',NULL,2,0,'2014-09-10 06:22:59','2014-11-12 05:00:37',NULL),(19,3,'http://www.buyma.com/item/12909457/','【Sonia Kashuk】MOISTURE LUXE TINTED LIP BALM',3800,3800,3800,3800,NULL,'no',7,'http://static.buyma.com/imgdata/item/140423/0012909457/428_1.jpg','\n									sonia kashuk(ソニアカシャック)\n								','ビューティー > メイクアップ > リップグロス・口紅類','12909457',NULL,2,0,'2014-09-11 06:40:13','2014-11-12 05:00:39',NULL),(20,3,'http://www.buyma.com/item/13972064/','国内発送★asos★カラバリ♪ペプラムペンシルドレス',6000,6000,13000,6500,6000,'yes',3,'http://static.buyma.com/imgdata/item/140821/0013972064/428_1.jpg','\n									ASOS(エイソス)\n								','レディースファッション > ワンピース > ワンピース','13972064',NULL,2,0,'2014-09-16 11:32:06','2014-11-12 05:00:40',NULL),(21,2,'http://zozo.jp/shop/rosebud/goods/4339266/','(BONFANTI)738387 MEDIUM WOOL TOTE BAG',17064,17064,17064,17064,NULL,'no',99,'http://img5.zozo.jp/goodsimages/266/5339266/5339266_16_D_500.jpg','ROSE BUD','バッグ > トートバッグ','4339266','rosebud',3,0,'2014-09-17 12:34:56','2014-11-12 04:00:22',NULL),(22,3,'http://www.buyma.com/item/14429849/','国内発送★ASOS　プリーツスカート ミディスケータードレス',10500,10500,10500,10500,NULL,'no',4,'http://static.buyma.com/imgdata/item/141010/0014429849/428_1.jpg',NULL,'レディースファッション > ワンピース > ワンピース','14429849',NULL,2,0,'2014-10-21 07:35:09','2014-11-12 05:00:42',NULL),(23,3,'http://www.buyma.com/item/14493879/','追跡付/関税負担《新作限定♪エレカジ&#9825;》スニーカー',182800,182800,182800,182800,NULL,'no',1,'http://static.buyma.com/imgdata/item/141016/0014493879/428_1.jpg',NULL,'レディースファッション > 靴・シューズ > スニーカー','14493879',NULL,2,0,'2014-10-24 02:19:29','2014-11-12 05:00:44',NULL);
/*!40000 ALTER TABLE `items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_info`
--

DROP TABLE IF EXISTS `notification_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notification_info` (
  `user_id` int(11) NOT NULL,
  `is_mail_alert_deny` tinyint(1) NOT NULL DEFAULT '0',
  `os_type` enum('ios','android') DEFAULT NULL,
  `device_token` varchar(128) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_info`
--

LOCK TABLES `notification_info` WRITE;
/*!40000 ALTER TABLE `notification_info` DISABLE KEYS */;
INSERT INTO `notification_info` VALUES (0,0,'ios','8fa88aaf2547f074d893880f7bd833d7b230b4c9fe949938f593c3718f1d2ab0',NULL,'2014-09-16 13:25:35','0000-00-00 00:00:00'),(2,0,'ios','1dbea348740bc784002c2c602c0059294b343502dacb34b8c6d4375f25e4917c',NULL,'2014-08-12 07:22:22','0000-00-00 00:00:00'),(64685,1,'ios','8fa88aaf2547f074d893880f7bd833d7b230b4c9fe949938f593c3718f1d2ab0',NULL,'2014-07-08 13:02:59','0000-00-00 00:00:00'),(64720,1,NULL,NULL,NULL,'2014-07-03 07:55:14','0000-00-00 00:00:00'),(64869,0,'ios','f9f9791df80ebb0b5ea396dcec2600d2626413eb8767a6097255b51d6986e94d',NULL,'2014-08-12 06:24:42','0000-00-00 00:00:00'),(65236,1,'ios','5175eb587d25009ea446a9917074a91dfa0de97fc0b42ea80c9d04e209e78683',NULL,'2014-07-09 13:08:11','0000-00-00 00:00:00'),(65837,1,NULL,NULL,NULL,'2014-07-03 11:44:40','0000-00-00 00:00:00'),(66115,0,NULL,NULL,NULL,'2014-07-03 11:28:26','0000-00-00 00:00:00'),(66117,0,NULL,NULL,NULL,'2014-07-08 05:19:52','0000-00-00 00:00:00'),(66120,1,'ios','8fa88aaf2547f074d893880f7bd833d7b230b4c9fe949938f593c3718f1d2ab0',NULL,'2014-09-22 02:15:10','0000-00-00 00:00:00'),(66121,1,'ios','a926e545a9cc902ef81eead0e9790bf5b81d3a05d634f85b08afd32cb4e2fce7',NULL,'2014-07-08 10:01:56','0000-00-00 00:00:00'),(66123,0,'ios','edf5f7bb88ef13e8a56a3f659229e4e79d10ee16b8e539edfd121c0f3485e463',NULL,'2014-07-10 03:34:19','0000-00-00 00:00:00'),(66124,0,'ios','a926e545a9cc902ef81eead0e9790bf5b81d3a05d634f85b08afd32cb4e2fce7',NULL,'2014-07-10 01:42:26','0000-00-00 00:00:00'),(66230,0,NULL,NULL,NULL,'2014-09-09 02:43:32','0000-00-00 00:00:00'),(66255,1,'ios','ecc3a85b7d81d82cb771c5f84131c5b61132aff1e3cfc06054342419581307f8',NULL,'2014-09-17 13:34:18','0000-00-00 00:00:00'),(66477,0,NULL,NULL,NULL,'2014-09-08 04:52:10','0000-00-00 00:00:00'),(66515,1,NULL,NULL,NULL,'2014-09-09 08:49:25','0000-00-00 00:00:00'),(66624,1,NULL,NULL,NULL,'2014-09-17 12:39:50','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `notification_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shops`
--

DROP TABLE IF EXISTS `shops`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shops` (
  `shop_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '管理用ID',
  `name` varchar(128) DEFAULT NULL COMMENT 'オーダーID',
  `url` varchar(128) DEFAULT NULL COMMENT '商品ページURL',
  `host` varchar(64) DEFAULT NULL COMMENT '商品ページホスト',
  `top_url` varchar(128) DEFAULT NULL COMMENT 'サイトTOPページURL',
  `code` varchar(64) DEFAULT NULL COMMENT '略名',
  `kana_name` varchar(128) DEFAULT NULL COMMENT 'カタカナ表記',
  `english_name` varchar(128) DEFAULT NULL COMMENT '英語表記',
  `sort_name` varchar(128) DEFAULT NULL COMMENT 'ソート用表記',
  `initial` varchar(4) DEFAULT NULL COMMENT 'Idex用イニシャル',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成日時',
  `updated_at` datetime DEFAULT NULL COMMENT '更新日時',
  `deleted_at` datetime DEFAULT NULL COMMENT '削除日時',
  PRIMARY KEY (`shop_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shops`
--

LOCK TABLES `shops` WRITE;
/*!40000 ALTER TABLE `shops` DISABLE KEYS */;
INSERT INTO `shops` VALUES (1,'Yahoo!ショッピング','http://store.shopping.yahoo.co.jp/','store.shopping.yahoo.co.jp','shopping.yahoo.co.jp','yshop','ヤフーショッピング','Yahoo!Shopping','Yahooショッピング','Y','2014-06-09 00:55:51',NULL,NULL),(2,'ZOZOTOWN','http://zozo.jp/','zozo.jp','zozo.jp','zozo','ゾゾタウン','ZOZOTOWN','ZOZOTOWN','Z','2014-06-09 00:55:51',NULL,NULL),(3,'BUYMA','http://www.buyma.com/','www.buyma.com','buyma.com','buyma','バイマ','BUYMA','BUYMA','B','2014-06-09 00:55:51',NULL,NULL);
/*!40000 ALTER TABLE `shops` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-11-12 13:35:27
