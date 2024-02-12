CREATE TABLE IF NOT EXISTS `mc_geoloc` (
  `id_geoloc` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `date_register` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_geoloc`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `mc_geoloc_content` (
    `id_content` smallint(3) NOT NULL AUTO_INCREMENT,
    `id_geoloc` smallint(3) UNSIGNED NOT NULL,
    `id_lang` smallint(3) UNSIGNED NOT NULL,
    `name_geoloc` varchar(175) DEFAULT NULL,
    `content_geoloc` text,
    `seo_title_geoloc` varchar(180) DEFAULT NULL,
    `seo_desc_geoloc` text,
    `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `published_geoloc` smallint(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id_content`),
    KEY `id_geoloc` (`id_geoloc`),
    KEY `id_lang` (`id_lang`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `mc_geoloc_content`
  ADD CONSTRAINT `mc_geoloc_content_ibfk_2` FOREIGN KEY (`id_lang`) REFERENCES `mc_lang` (`id_lang`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `mc_geoloc_content_ibfk_1` FOREIGN KEY (`id_geoloc`) REFERENCES `mc_geoloc` (`id_geoloc`) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS `mc_geoloc_address` (
  `id_address` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `img_address` varchar(150) DEFAULT NULL,
  `order_address` smallint(5) unsigned NOT NULL DEFAULT '0',
  `date_register` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_address`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `mc_geoloc_address_content` (
    `id_content` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_address` smallint(5) UNSIGNED NOT NULL,
    `id_lang` smallint(3) UNSIGNED NOT NULL,
    `company_address` varchar(50) NOT NULL,
    `content_address` text,
    `address_address` varchar(175) NOT NULL,
    `postcode_address` varchar(12) NOT NULL,
    `country_address` varchar(30) NOT NULL,
    `city_address` varchar(40) NOT NULL,
    `phone_address` varchar(45) DEFAULT NULL,
    `mobile_address` varchar(45) DEFAULT NULL,
    `fax_address` varchar(45) DEFAULT NULL,
    `email_address` varchar(150) DEFAULT NULL,
    `vat_address` varchar(80) DEFAULT NULL,
    `lat_address` double NOT NULL,
    `lng_address` double NOT NULL,
    `facebook_address` varchar(150) DEFAULT NULL,
    `instagram_address` varchar(150) DEFAULT NULL,
    `linkedin_address` varchar(150) DEFAULT NULL,
    `website_address` varchar(150) DEFAULT NULL,
    `suppl_address` varchar(150) DEFAULT NULL,
    `url_address` varchar(150) DEFAULT NULL,
    `seo_title_address` varchar(180) DEFAULT NULL,
    `seo_desc_address` text,
    `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `published_address` smallint(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id_content`),
    KEY `id_lang` (`id_lang`),
    KEY `id_address` (`id_address`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

ALTER TABLE `mc_geoloc_address_content`
  ADD CONSTRAINT `mc_geoloc_address_content_ibfk_1` FOREIGN KEY (`id_address`) REFERENCES `mc_geoloc_address` (`id_address`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `mc_geoloc_address_content_ibfk_2` FOREIGN KEY (`id_lang`) REFERENCES `mc_lang` (`id_lang`) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS `mc_geoloc_config` (
  `id_geoloc_config` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `config_id` varchar(100) NOT NULL,
  `config_value` text,
  PRIMARY KEY (`id_geoloc_config`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `mc_geoloc_tag` (
    `id_tag` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_lang` smallint(5) UNSIGNED NOT NULL,
    `name_tag` varchar(50) NOT NULL,
    PRIMARY KEY (`id_tag`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `mc_geoloc_tag_rel` (
    `id_rel` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_address` int(10) UNSIGNED NOT NULL,
    `id_tag` int(10) UNSIGNED NOT NULL,
    PRIMARY KEY (`id_rel`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

INSERT INTO `mc_geoloc_config` (`id_geoloc_config`, `config_id`, `config_value`) VALUES
(NULL, 'markerColor', '#f3483c'),
(NULL, 'api_key', NULL);

INSERT INTO `mc_config_img` (`id_config_img`, `module_img`, `attribute_img`, `width_img`, `height_img`, `type_img`, `prefix_img`, `resize_img`) VALUES
(null, 'geoloc', 'geoloc', '360', '270', 'small', 's', 'adaptive'),
(null, 'geoloc', 'geoloc', '480', '360', 'medium', 'm', 'adaptive'),
(null, 'geoloc', 'geoloc', '960', '720', 'large', 'l', 'adaptive');