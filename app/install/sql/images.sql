DROP TABLE IF EXISTS `%table_prefix%images`;
CREATE TABLE `%table_prefix%images` (
  `image_id` bigint(32) NOT NULL AUTO_INCREMENT,
  `image_name` varchar(255) NOT NULL,
  `image_extension` varchar(255) NOT NULL,
  `image_size` int(11) NOT NULL,
  `image_width` int(11) NOT NULL,
  `image_height` int(11) NOT NULL,
  `image_date` datetime NOT NULL,
  `image_date_gmt` datetime NOT NULL,
  `image_title` varchar(100) DEFAULT NULL,
  `image_description` mediumtext,
  `image_nsfw` tinyint(1) NOT NULL DEFAULT '0',
  `image_user_id` bigint(32) DEFAULT NULL,
  `image_album_id` bigint(32) DEFAULT NULL,
  `image_uploader_ip` varchar(255) NOT NULL,
  `image_storage_mode` enum('datefolder','direct','old','path') NOT NULL DEFAULT 'datefolder',
  `image_path` varchar(4096) DEFAULT NULL,
  `image_storage_id` bigint(32) DEFAULT NULL,
  `image_md5` varchar(32) NOT NULL,
  `image_source_md5` varchar(32) DEFAULT NULL,
  `image_original_filename` varchar(255) NOT NULL,
  `image_original_exifdata` longtext,
  `image_views` bigint(32) NOT NULL DEFAULT '0',
  `image_category_id` bigint(32) DEFAULT NULL,
  `image_chain` tinyint(128) NOT NULL,
  `image_thumb_size` int(11) NOT NULL,
  `image_medium_size` int(11) NOT NULL DEFAULT '0',
  `image_expiration_date_gmt` datetime DEFAULT NULL,
  `image_likes` bigint(32) NOT NULL DEFAULT '0',
  `image_is_animated` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`image_id`),
  KEY `image_name` (`image_name`(191)),
  KEY `image_extension` (`image_extension`(191)),
  KEY `image_size` (`image_size`),
  KEY `image_width` (`image_width`),
  KEY `image_height` (`image_height`),
  KEY `image_date_gmt` (`image_date_gmt`),
  KEY `image_nsfw` (`image_nsfw`),
  KEY `image_user_id` (`image_user_id`),
  KEY `image_album_id` (`image_album_id`),
  KEY `image_uploader_ip` (`image_uploader_ip`(191)),
  KEY `image_storage_mode` (`image_storage_mode`),
  KEY `image_path` (`image_path`(191)),
  KEY `image_storage_id` (`image_storage_id`),
  KEY `image_md5` (`image_md5`),
  KEY `image_source_md5` (`image_source_md5`),
  KEY `image_views` (`image_views`),
  KEY `image_category_id` (`image_category_id`),
  KEY `image_chain` (`image_chain`),
  KEY `image_expiration_date_gmt` (`image_expiration_date_gmt`),
  KEY `image_likes` (`image_likes`),
  KEY `image_is_animated` (`image_is_animated`),
  KEY `image_album_id_image_id` (`image_album_id`, `image_id`),
  FULLTEXT KEY `searchindex` (`image_name`,`image_title`,`image_description`,`image_original_filename`)
) ENGINE=%table_engine% DEFAULT CHARSET=utf8mb4;