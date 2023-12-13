TRUNCATE TABLE `mc_geoloc_content`;
DROP TABLE `mc_geoloc_content`;
TRUNCATE TABLE `mc_geoloc`;
DROP TABLE `mc_geoloc`;
TRUNCATE TABLE `mc_geoloc_address_content`;
DROP TABLE `mc_geoloc_address_content`;
TRUNCATE TABLE `mc_geoloc_address`;
DROP TABLE `mc_geoloc_address`;
TRUNCATE TABLE `mc_geoloc_config`;
DROP TABLE `mc_geoloc_config`;

DELETE FROM `mc_config_img` WHERE `module_img` = 'plugins' AND `attribute_img` = 'geoloc';

DELETE FROM `mc_admin_access` WHERE `id_module` IN (
    SELECT `id_module` FROM `mc_module` as m WHERE m.name = 'geoloc'
);