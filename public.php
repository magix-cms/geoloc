<?php
require_once('db.php');
require_once('marker.php');
/**
 * @category plugin
 * @package geoloc
 * @copyright MAGIX CMS Copyright (c) 2011 Gerits Aurelien, http://www.magix-dev.be, http://www.magix-cms.com
 * @license Dual licensed under the MIT or GPL Version 3 licenses.
 * @version 1.0
 * @create 20-12-2021
 * @author Aurélien Gérits <aurelien@magix-cms.com>
 * @name plugins_geoloc_public
 */
class plugins_geoloc_public extends plugins_geoloc_db {
	/**
	 * @var frontend_model_template $template
	 * @var frontend_model_data $data
	 */
    protected frontend_model_template $template;
    protected frontend_model_data $data;
    protected component_files_images $imagesComponent;

	/**
	 * @var bool $dotless
	 */
	public bool $dotless;

	/**
	 * @var string $lang
	 * @var string $marker
	 */
	public string
		$lang,
		$marker;
    public int $id,
        $tag;

	/**
	 * @var array $conf
	 */
	public array $conf;

	/**
	 *
	 */
	public function __construct() {
	    $this->template = new frontend_model_template();
		$this->data = new frontend_model_data($this);
		$this->lang = $this->template->lang;
        if(http_request::isGet('id')) $this->id = form_inputEscape::numeric($_GET['id']);
        if(http_request::isGet('tag')) $this->tag = form_inputEscape::numeric($_GET['tag']);
        if(http_request::isGet('marker')) $this->marker = form_inputEscape::simpleClean($_GET['marker']);
		$this->dotless = http_request::isGet('dotless');
		$config = $this->getItems('config');
		if(!empty($config)) {
			$configId = [];
			$configValue = [];
			foreach($config as $key){
				$configId[] = $key['config_id'];
				$configValue[] = $key['config_value'];
			}
			$config = array_combine($configId,$configValue);
		}
		$this->conf = $config;
	}

	/**
	 * Assign data to the defined variable or return the data
	 * @param string $type
	 * @param string|array|null $id
	 * @param string|null $context
	 * @param bool|string $assign
	 * @return mixed
	 */
	private function getItems(string $type, $id = null, ?string $context = null, $assign = false) {
		return $this->data->getItems($type, $id, $context, $assign);
	}

    /**
     * @return void
     */
    private function initImageComponent(): void {
        if(!isset($this->imagesComponent)) $this->imagesComponent = new component_files_images($this->template);
    }
	/**
	 * Load map data
	 * @return string
	 */
	private function setJsConfig(): string {
        $addresses= [];

        if(empty($addresses)) {
            if(isset($this->tag)) {
                $this->getItems('tag_name',$this->tag,'one','tag');
                $addresses = $this->getAddresses(true, true);
            }
            else {
                $addresses = $this->getAddresses(true);
            }
        }
		//$config = $this->conf;
		$config = ['api_key' => $this->conf['api_key']];
		if($addresses != null) {
			$map = [];
			foreach ($addresses as $addr){
				$mark = '{';
				foreach ($addr as $k => $v) {
					$mark .= '"'.str_replace('_address','',$k).'":'.json_encode($v).',';
				}
				$mark = substr($mark, 0, -1).'}';
				$map[] = $mark;
			}
			$config['markers'] = '['.implode(',',$map).']';
		}
		else {
			$config['markers'] = '[]';
		}

		$configString = [];
		foreach ($config as $k => $v) {
			if($k != 'markers')
				$v = json_encode($v);
			$configString[]= $k.':'.$v;
		}
		$detect = new Mobile_Detect;
		$OS = false;
		if( $detect->isiOS() ){
			$OS = 'IOS';
		}
		elseif( $detect->isAndroidOS() ){
			$OS = 'Android';
		}
		$configString[] = '"OS":"'.$OS.'"';
		$configString[] = '"lang":"'.$this->lang.'"';
		return '{'.implode(',',$configString).'}';
	}

	/**
	 * Load map data
	 * @return array
	 */
	private function setConfig(): array {
		$config = [];
		if(!empty($this->conf)) {
			foreach ($this->conf as $k => $v) {
				$config[$k] = $v;
			}
		}
		return $config;
	}

    /**
     * @param array $data
     * @param bool $json
     * @return array
     */
    private function setItemGeoData(array $data, bool $json = false): array {
        $arr = [];
        $country = new component_collections_country();
        $iso = null;
        if($data['country_address'] != null){
            foreach($country->getCountries() as $key => $item){
                if($item == $data['country_address']){
                    $iso = $key;
                }
            }
        }

        if(!empty($data)) {
            $this->initImageComponent();
            $publicUrl = '/'.$data['iso_lang'].'/geoloc/'.$data['id_address'].'-'.$data['url_address'].'/';
            $string_format = new component_format_string();
            if(!$json) {
                $arr = [
                    'id' => $data['id_address'],
                    'id_lang' => $data['id_lang'],
                    'company' => $data['company_address'],
                    'resume'=>$string_format->clearHTMLTemplate($data['content_address']),
                    'content' => $data['content_address'],
                    'location' => [
                        'address' => $data['address_address'],
                        'postcode' => $data['postcode_address'],
                        'city' => $data['city_address'],
                        'country' => $data['country_address'],
                        'isocountry' => $iso
                    ],
                    'geo' => [
                        'lat' => $data['lat_address'],
                        'lng' => $data['lng_address']
                    ],
                    'contact' => [
                        'phone' => $data['phone_address'],
                        'email' => $data['email_address'],
                        'mobile' => $data['mobile_address'],
                        'fax' => $data['fax_address']
                    ],
                    'social' => [
                        'facebook' => $data['facebook_address'],
                        'instagram' => $data['instagram_address'],
                        'linkedin' => $data['linkedin_address'],
                        'website' => $data['website_address'],
                        'suppl' => $data['suppl_address']
                    ],
                    'img' => !empty($data['img_address']) ? $this->imagesComponent->setModuleImage('geoloc', 'geoloc', $data['img_address'], $data['id_address']) : NULL,
                    'published' => $data['published_address'],
                    'order' => $data['order_address'],
                    'public_url' => $publicUrl
                ];
            }else{
                $arr = [
                    'id' => $data['id_address'],
                    'id_lang' => $data['id_lang'],
                    'company' => $data['company_address'],
                    'resume'    =>$string_format->clearHTMLTemplate($data['content_address']),
                    'content' => $data['content_address'],
                    'address' => $data['address_address'],
                    'postcode' => $data['postcode_address'],
                    'city' => $data['city_address'],
                    'country' => $data['country_address'],
                    'isocountry' => $iso,
                    'lat' => $data['lat_address'],
                    'lng' => $data['lng_address'],
                    'phone' => $data['phone_address'],
                    'email' => $data['email_address'],
                    'mobile' => $data['mobile_address'],
                    'fax' => $data['fax_address'],
                    'img' => !empty($data['img_address']) ? $this->imagesComponent->setModuleImage('geoloc', 'geoloc', $data['img_address'], $data['id_address']) : NULL,
                    'published' => $data['published_address'],
                    'order' => $data['order_address'],
                    'link' => $publicUrl
                ];
            }
        }
        return $arr;
    }
    /**
     * @throws Exception
     */
    private function getBuildTagList()
    {
        $setBuildUrl = new http_url();
        $tags = $this->getItems('buildTags',array('lang' => $this->lang),'all',false);
        //$newData = array();
        foreach($tags as $key => $val){
            $tags[$key]['url_tag'] = $setBuildUrl->clean($val['name_tag']);
        }
        $this->template->assign('tags',$tags);
    }
	/**
	 * Execute le plugin dans la partie public
	 */
	public function run() {
		$this->template->configLoad();

		if(isset($this->marker)) {
			$img = '';

			if($this->marker === 'main') {
				$markerPath = component_core_system::basePath().'/plugins/geoloc/markers/'.$this->marker.($this->dotless?'-dotless':'').'.svg';

				if(!file_exists($markerPath)) {
					$config = parent::fetchData(array('context' => 'one','type' => 'config'));
					$marker = new plugins_geoloc_marker($config['markerColor'],$this->template);
					$marker->createMarker();
				} else {
					$img = file_get_contents($markerPath);
				}
			}
			else {
				$img = file_get_contents(component_core_system::basePath().'/plugins/geoloc/markers/grey'.($this->dotless?'-dotless':'').'.svg');
			}

			if($img !== '') {
				header('Content-type: image/svg+xml');
				print $img;
			}
		}
		else {
            if(isset($this->id)){
                $adresseContent = $this->getItems('mapAddressContent',['id' => $this->id,'lang' => $this->lang],'one',false);
                $geoData = $this->setItemGeoData($adresseContent);
                $this->template->assign('geoloc',$geoData);
                $this->template->breadcrumb->addItem($this->template->getConfigVars('geoloc'),'/'.$this->lang.'/geoloc/');
                $this->template->breadcrumb->addItem(
                    $geoData['company']
                );
                $this->template->display('geoloc/page.tpl');
            }else{
                $this->getItems('page',['lang' => $this->lang],'one',true);
                $this->template->breadcrumb->addItem($this->template->getConfigVars('geoloc'));
                if(isset($this->tag)){
                    $this->template->assign('addresses',$this->getAddresses(false,true));
                }else{
                    $this->template->assign('addresses',$this->getAddresses());
                }
                $this->template->assign('config',$this->setConfig());
                $this->template->assign('config_gmap',$this->setJsConfig());
                $this->getBuildTagList();
                $this->template->display('geoloc/index.tpl');
            }
		}
    }

	/**
	 * @return array
	 */
	public function outrun(): array {
		return [
			'page' => $this->getItems('page',['lang' => $this->lang],'one'),
			'config' => $this->setConfig(),
			'config_geoloc' => $this->setJsConfig()
		];
	}

	/**
	 * @return array
	 */
	public function getAddresses(bool $json = false,bool $tag = false): array {
        $address = [];
        if($tag){
            $addresses = $this->getItems('addresses_tag',['lang' => $this->lang, 'id' => $this->tag],'all',false)?: [];
        }else{
            $addresses = $this->getItems('addresses',['lang' => $this->lang],'all', false)?: [];
        }

        if($addresses != null) {
            foreach ($addresses as $items) {
                $address[] = $this->setItemGeoData($items,$json);
            }
        }
        return $address;
	}
}