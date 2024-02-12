<?php
/**
 * @category plugin
 * @package geoloc
 * @copyright MAGIX CMS Copyright (c) 2011 Gerits Aurelien, http://www.magix-dev.be, http://www.magix-cms.com
 * @license Dual licensed under the MIT or GPL Version 3 licenses.
 * @version 1.0
 * @create 20-12-2021
 * @author Aurélien Gérits <aurelien@magix-cms.com>
 * @name plugins_geoloc_db
 */
class plugins_geoloc_db {
	/**
	 * @var debug_logger $logger
	 */
	protected debug_logger $logger;

	/**
	 * @param array $config
	 * @param array $params
	 * @return array|bool
	 */
	public function fetchData(array $config, array $params = []) {
		if($config['context'] === 'all') {
			switch ($config['type']) {
				case 'pages':
					$query = 'SELECT h.*,c.*
							FROM mc_geoloc AS h
							JOIN mc_geoloc_content AS c USING(id_geoloc)
							JOIN mc_lang AS lang ON(c.id_lang = lang.id_lang)';
					break;
				case 'address':
					$query = 'SELECT a.*,c.*
							FROM mc_geoloc_address AS a
							JOIN mc_geoloc_address_content AS c USING(id_address)
							JOIN mc_lang AS lang ON(c.id_lang = lang.id_lang)
							WHERE c.id_lang = :default_lang';
					break;
				case 'addressContent':
					$query = 'SELECT a.*,c.*,lang.iso_lang, rel.tags_address
							FROM mc_geoloc_address AS a
							JOIN mc_geoloc_address_content AS c USING(id_address)
							JOIN mc_lang AS lang ON(c.id_lang = lang.id_lang)
							LEFT OUTER JOIN (
								SELECT tagrel.id_address, lang.id_lang, GROUP_CONCAT( tag.name_tag ORDER BY tagrel.id_rel SEPARATOR "," ) AS tags_address
								FROM mc_geoloc_tag AS tag
								JOIN mc_geoloc_tag_rel AS tagrel USING ( id_tag )
								JOIN mc_lang AS lang ON ( tag.id_lang = lang.id_lang )
								GROUP BY tagrel.id_address, lang.id_lang
								)rel ON ( rel.id_address = a.id_address AND rel.id_lang = c.id_lang)
							WHERE c.id_address = :id';
					break;
				case 'addresses':
					$query = "SELECT a.*,c.*,l.iso_lang,ntr.id_tag
							FROM mc_geoloc_address AS a
							JOIN mc_geoloc_address_content AS c USING(id_address)
							    LEFT JOIN mc_geoloc_tag_rel AS ntr ON(c.id_address = ntr.id_address)
							JOIN mc_lang AS l USING(id_lang) 
							WHERE iso_lang = :lang
							AND c.published_address = 1";
					break;
                case 'addresses_tag':
                    $query = "SELECT a.*,c.*,l.iso_lang,ntr.id_tag
							FROM mc_geoloc_address AS a
							JOIN mc_geoloc_address_content AS c USING(id_address)
							JOIN mc_geoloc_tag_rel AS ntr ON(c.id_address = ntr.id_address)
							JOIN mc_lang AS l USING(id_lang) 
							WHERE iso_lang = :lang
							AND c.published_address = 1 AND ntr.id_tag = :id";
                    break;
                case 'addresses_tags':
                    $query = "SELECT a.*,c.*,l.iso_lang,ntr.id_tag
							FROM mc_geoloc_address AS a
							JOIN mc_geoloc_address_content AS c USING(id_address)
							LEFT JOIN mc_geoloc_tag_rel AS ntr ON(c.id_address = ntr.id_address)
							JOIN mc_lang AS l USING(id_lang) 
							WHERE iso_lang = :lang
							AND c.published_address = 1 AND (ntr.id_tag IN (".implode(',',$params['ids']).") OR ntr.id_tag IS NULL)";
                    unset($params['ids']);
                    break;
				case 'config':
					$query = "SELECT * FROM mc_geoloc_config";
					break;
                case 'tags':
                    $query = 'SELECT tag.id_tag,tag.name_tag
					FROM mc_geoloc_tag AS tag
					JOIN mc_lang AS lang ON(tag.id_lang = lang.id_lang)
					WHERE tag.id_lang = :id_lang';
                    break;
                case 'buildTags':
                    $query = 'SELECT tag.id_tag,tag.name_tag
					FROM mc_geoloc_tag AS tag
					JOIN mc_lang AS lang ON(tag.id_lang = lang.id_lang)
					WHERE iso_lang = :lang ORDER BY tag.name_tag ASC';
                    break;
                case 'img':
                    $query = 'SELECT s.id_address, s.img_address
                        		FROM mc_geoloc_address AS s WHERE s.img_address IS NOT NULL';
                    break;
				default:
					return false;
			}

			try {
				return component_routing_db::layer()->fetchAll($query, $params);
			}
			catch (Exception $e) {
				if(!isset($this->logger)) $this->logger = new debug_logger(MP_LOG_DIR);
				$this->logger->log('statement','db',$e->getMessage(),$this->logger::LOG_MONTH);
			}
		}
		elseif($config['context'] === 'one') {
			switch ($config['type']) {
				case 'root':
					$query = 'SELECT * FROM mc_geoloc ORDER BY id_geoloc DESC LIMIT 0,1';
					break;
				case 'content':
					$query = 'SELECT * FROM mc_geoloc_content WHERE id_geoloc = :id AND id_lang = :id_lang';
					break;
				case 'page':
					$query = 'SELECT *
							FROM mc_geoloc as g
							JOIN mc_geoloc_content as gc USING(id_geoloc)
							JOIN mc_lang as l USING(id_lang)
							WHERE iso_lang = :lang
							LIMIT 0,1';
					break;
				case 'markerColor':
					$query = "SELECT config_value as markerColor FROM mc_geoloc_config WHERE config_id = 'markerColor'";
					break;
				case 'mapAddressContent':
					$query = 'SELECT mga.*,mgac.* 
                        FROM mc_geoloc_address AS mga
                        JOIN mc_geoloc_address_content AS mgac ON(mga.id_address = mgac.id_address)
                        JOIN mc_lang AS lang ON(mgac.id_lang = lang.id_lang)
                        WHERE mgac.id_address = :id AND lang.iso_lang = :lang';
					break;
                case 'addressContent':
                    $query = 'SELECT * FROM mc_geoloc_address_content WHERE id_address = :id AND id_lang = :id_lang';
                    break;
				case 'lastAddress':
					$query = 'SELECT * FROM mc_geoloc_address ORDER BY id_address DESC LIMIT 0,1';
					break;
                case 'tag':
                    $query = 'SELECT tag.*, (SELECT id_rel FROM mc_geoloc_tag_rel WHERE id_address = :id_address AND id_tag = tag.id_tag) AS rel_tag
					FROM mc_geoloc_tag AS tag
					WHERE tag.id_lang = :id_lang AND tag.name_tag LIKE :name_tag';
                    break;
                case 'tag_name':
                    $query = "SELECT id_tag as id, name_tag as name FROM mc_geoloc_tag WHERE id_tag = :id";
                    break;
                case 'tag_address':
                    $query = 'SELECT * FROM mc_geoloc_tag_rel WHERE id_address = :id_address';
                    break;
                case 'countTags':
                    $query = 'SELECT count(id_tag) AS tags FROM mc_geoloc_tag_rel WHERE id_tag = :id_tag';
                    break;
				default:
					return false;
			}

			try {
				return component_routing_db::layer()->fetch($query, $params);
			}
			catch (Exception $e) {
				if(!isset($this->logger)) $this->logger = new debug_logger(MP_LOG_DIR);
				$this->logger->log('statement','db',$e->getMessage(),$this->logger::LOG_MONTH);
			}
		}
		return false;
    }

	/**
	 * @param string $type
	 * @param array $params
	 * @return bool|mixed
	 */
	public function insert(string $type, array $params = []) {
		switch ($type) {
            case 'newTagComb':
                $queries = [
                    ['request' => 'INSERT INTO mc_geoloc_tag (id_lang,name_tag) VALUE (:id_lang,:name_tag)', 'params' => ['id_lang' => $params['id_lang'],'name_tag' => $params['name_tag']]],
                    ['request' => 'SELECT @tag_id := LAST_INSERT_ID() as id_tag', 'params' => [], 'fetch' => true],
                    ['request' => 'SET @address_id = :id_address', 'params' => ['id_address' => $params['id_address']]],
                    ['request' => 'INSERT INTO mc_geoloc_tag_rel (id_address,id_tag) VALUE (@address_id,@tag_id)', 'params' => []]
                ];

                try {
                    $results = component_routing_db::layer()->transaction($queries);
                    return $results[1];
                }
                catch (Exception $e) {
                    if(!isset($this->logger)) $this->logger = new debug_logger(MP_LOG_DIR);
                    $this->logger->log('statement','db',$e->getMessage(),$this->logger::LOG_MONTH);
                    return false;
                }
			case 'root':
				$query = 'INSERT INTO mc_geoloc(date_register) VALUES (NOW())';
				break;
			case 'content':
				$query = 'INSERT INTO mc_geoloc_content(id_geoloc, id_lang, name_geoloc, content_geoloc, published_geoloc) 
						VALUES (:id, :id_lang, :name_geoloc, :content_geoloc, :published_geoloc)';
				break;
			case 'address':
                $queries = [
                    ['request' => 'INSERT INTO mc_geoloc_address(order_address, date_register) SELECT COUNT(id_address), NOW() FROM mc_geoloc_address', 'params' => []],
                    ['request' => 'SELECT @address_id := LAST_INSERT_ID() as id_address', 'params' => [], 'fetch' => true]
                ];

                try {
                    $results = component_routing_db::layer()->transaction($queries);
                    return $results[1];
                }
                catch (Exception $e) {
                    if(!isset($this->logger)) $this->logger = new debug_logger(MP_LOG_DIR);
                    $this->logger->log('statement','db',$e->getMessage(),$this->logger::LOG_MONTH);
                    return false;
                }
			case 'addressContent':
				$query = 'INSERT INTO mc_geoloc_address_content(id_address, id_lang, company_address, content_address, address_address, postcode_address, country_address, city_address, phone_address, mobile_address, fax_address, email_address, vat_address, lat_address, lng_address, facebook_address, instagram_address, linkedin_address, website_address, suppl_address, url_address, last_update, published_address)
						VALUES (:id_address, :id_lang, :company_address, :content_address, :address_address, :postcode_address, :country_address, :city_address, :phone_address, :mobile_address, :fax_address, :email_address, :vat_address, :lat_address, :lng_address, :facebook_address, :instagram_address, :linkedin_address, :website_address, :suppl_address, :url_address, NOW(), :published_address)';
                //print $query;
                //var_dump($params);
				break;
            case 'newTag':
                $query = 'INSERT INTO mc_geoloc_tag (id_lang,name_tag) VALUES (:id_lang,:name_tag)';
                break;
            case 'newTagRel':
                $query = 'INSERT INTO mc_geoloc_tag_rel (id_address,id_tag) VALUES (:id_address,:id_tag)';
                break;
			default:
				return false;
		}

		try {
			component_routing_db::layer()->insert($query,$params);
			return true;
		}
		catch (Exception $e) {
			if(!isset($this->logger)) $this->logger = new debug_logger(MP_LOG_DIR);
			$this->logger->log('statement','db',$e->getMessage(),$this->logger::LOG_MONTH);
			return false;
		}
    }

	/**
	 * @param string $type
	 * @param array $params
	 * @return bool
	 */
	public function update(string $type, array $params = []): bool {
		switch ($type) {
			case 'content':
				$query = 'UPDATE mc_geoloc_content 
						SET 
							name_geoloc = :name_geoloc,
							content_geoloc = :content_geoloc,
							seo_title_geoloc = :seo_title_geoloc,
							seo_desc_geoloc = :seo_desc_geoloc,
							published_geoloc = :published_geoloc
						WHERE id_geoloc = :id 
						AND id_lang = :id_lang';
				break;
			case 'addressContent':
				$query = 'UPDATE mc_geoloc_address_content
						SET 
							company_address = :company_address,
							content_address = :content_address,
							address_address = :address_address,
							postcode_address = :postcode_address,
							country_address = :country_address,
							city_address = :city_address,
							phone_address = :phone_address, 
							mobile_address = :mobile_address, 
							fax_address = :fax_address, 
							email_address = :email_address, 
							vat_address = :vat_address, 
							lat_address = :lat_address, 
							lng_address = :lng_address, 
							facebook_address = :facebook_address,
							instagram_address = :instagram_address,
							linkedin_address = :linkedin_address,
							website_address = :website_address,
							suppl_address = :suppl_address,
							url_address = :url_address,
							seo_title_address = :seo_title_address,
							seo_desc_address = :seo_desc_address,
							last_update = NOW(), 
							published_address = :published_address
						WHERE id_content = :id 
						AND id_lang = :id_lang';
				break;
			case 'config':
				$query = "UPDATE `mc_geoloc_config`
						SET config_value = CASE config_id
							WHEN 'api_key' THEN :api_key
							WHEN 'markerColor' THEN :markerColor
						END
						WHERE config_id IN ('api_key','markerColor')";
				break;
			case 'img':
				$query = 'UPDATE mc_geoloc_address
						SET 
							img_address = :img
						WHERE id_address = :id';
				break;
            case 'tagRel':
                $query = 'UPDATE mc_geoloc_tag_rel SET id_tag = :company_address WHERE id_address = :id_address';
                break;
			default:
				return false;
		}

		try {
			component_routing_db::layer()->update($query,$params);
			return true;
		}
		catch (Exception $e) {
			if(!isset($this->logger)) $this->logger = new debug_logger(MP_LOG_DIR);
			$this->logger->log('statement','db',$e->getMessage(),$this->logger::LOG_MONTH);
			return false;
		}
    }

	/**
	 * @param string $type
	 * @param array $params
	 * @return bool
	 */
	protected function delete(string $type, array $params = []): bool {
		switch ($type) {
			case 'address':
				$query = 'DELETE FROM mc_geoloc_address
						WHERE id_address = :id';
				break;
            case 'tagRel':
                $query = 'DELETE FROM mc_geoloc_tag_rel WHERE id_rel = :id_rel';
                break;
            case 'tags':
                $query = 'DELETE FROM mc_geoloc_tag WHERE id_tag = :id_tag';
                break;
			default:
				return false;
		}

		try {
			component_routing_db::layer()->delete($query,$params);
			return true;
		}
		catch (Exception $e) {
			if(!isset($this->logger)) $this->logger = new debug_logger(MP_LOG_DIR);
			$this->logger->log('statement','db',$e->getMessage(),$this->logger::LOG_MONTH);
			return false;
		}
	}
}