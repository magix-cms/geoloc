ALTER TABLE `mc_geoloc_content` ADD `seo_title_geoloc` VARCHAR(180) NULL AFTER `content_geoloc`, ADD `seo_desc_geoloc` TEXT NULL AFTER `seo_title_geoloc`;
ALTER TABLE `mc_geoloc_address_content` ADD `seo_title_address` VARCHAR(180) NULL AFTER `url_address`, ADD `seo_desc_address` TEXT NULL AFTER `seo_title_address`;
