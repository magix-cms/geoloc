<?php
require_once ('db.php');
require_once('marker.php');
/**
 * @category plugin
 * @package geoloc
 * @copyright MAGIX CMS Copyright (c) 2011 Gerits Aurelien, http://www.magix-dev.be, http://www.magix-cms.com
 * @license Dual licensed under the MIT or GPL Version 3 licenses.
 * @version 1.0
 * @create 20-12-2021
 * @author Aurélien Gérits <aurelien@magix-cms.com>
 * @name plugins_geoloc_admin
 */
class plugins_geoloc_admin extends plugins_geoloc_db {
	/**
	 * @var backend_model_template $template;
	 * @var backend_model_data $data;
	 * @var component_core_message $message;
	 * @var backend_controller_plugins $plugins;
	 * @var xml_sitemap $xml;
	 * @var backend_model_sitemap $sitemap;
	 * @var backend_model_language $modelLanguage;
	 * @var component_collections_language $collectionLanguage;
	 * @var component_files_upload $upload;
	 * @var component_files_images $imagesComponent;
	 * @var backend_controller_module $module;
	 */
	protected backend_model_template $template;
	protected backend_model_data $data;
	protected component_core_message $message;
	protected backend_controller_plugins $plugins;
	protected xml_sitemap $xml;
	protected backend_model_sitemap $sitemap;
	protected backend_model_language $modelLanguage;
	protected component_collections_language $collectionLanguage;
	protected component_files_upload $upload;
	protected component_files_images $imagesComponent;
	protected backend_controller_module $module;

	/**
	 * @var array $mods
	 */
	protected array $mods;

	/**
	 * @var int $edit
	 * @var int $id
	 */
	public int
		$edit,
		$id,
        $id_lang;

	/**
	 * @var string $plugin
	 * @var string $action
	 * @var string $tab
	 * @var string $img Image
	 */
	public string
		$plugin,
		$action,
		$tab,
		$img,
        $name_tag;

	/**
	 * @var array $cfg Configuration
	 * @var array $content Page title and content
	 * @var array $address Address information
	 */
	public array
		$cfg,
		$content,
		$address;

    /**
     * @param backend_model_template|null $t
     * @throws Exception
     */
    public function __construct(?backend_model_template $t = null)
    {
        $this->template = $t instanceof backend_model_template ? $t : new backend_model_template;
		$this->data = new backend_model_data($this);
		$this->message = new component_core_message($this->template);
		$this->plugins = new backend_controller_plugins();
		$this->xml = new xml_sitemap();
		$this->sitemap = new backend_model_sitemap($this->template);
		$this->modelLanguage = new backend_model_language($this->template);
		$this->collectionLanguage = new component_collections_language();
		$this->upload = new component_files_upload();
		$this->imagesComponent = new component_files_images($this->template);

		// --- Get
		if (http_request::isGet('edit')) $this->edit = form_inputEscape::numeric($_GET['edit']);
		if (http_request::isRequest('action')) $this->action = form_inputEscape::simpleClean($_REQUEST['action']);
		if (http_request::isGet('tabs')) $this->tab = form_inputEscape::simpleClean($_GET['tabs']);
		// --- Post
		// - Config
		if (http_request::isPost('cfg')) $this->cfg = form_inputEscape::arrayClean($_POST['cfg']);
		// - Content
		if (http_request::isPost('content')) {
			$array = $_POST['content'];
			foreach($array as $key => $arr) {
				foreach($arr as $k => $v) {
					$array[$key][$k] = ($k == 'content_geoloc') ? form_inputEscape::cleanQuote($v) : form_inputEscape::simpleClean($v);
				}
			}
			$this->content = $array;
		}
		// - Addresses
		if (http_request::isPost('address')) {
			$arrayAddress = $_POST['address']['content'];
			foreach($arrayAddress as $key => $arr) {
				foreach($arr as $k => $v) {
                    $arrayAddress[$key][$k] = ($k == 'content_address') ? form_inputEscape::cleanQuote($v) : form_inputEscape::simpleClean($v);
				}
			}
			$this->address = $arrayAddress;
			//$this->address = form_inputEscape::arrayClean($_POST['address']);
		}
        if (http_request::isPost('name_tag')) {
            $this->name_tag = form_inputEscape::simpleClean($_POST['name_tag']);
        }
        if (http_request::isPost('id_lang')) {
            $this->id_lang = form_inputEscape::simpleClean($_POST['id_lang']);
        }
		// --- Add or Edit
		if (http_request::isPost('id')) $this->id = form_inputEscape::numeric($_POST['id']);
		// --- Image Upload
		if(isset($_FILES['img']["name"])) $this->img = http_url::clean($_FILES['img']["name"]);
		if (http_request::isGet('plugin')) $this->plugin = form_inputEscape::simpleClean($_GET['plugin']);
	}

	/**
	 * Method to override the name of the plugin in the admin menu
	 * @return string
	 */
	public function getExtensionName(): string {
		return $this->template->getConfigVars('geoloc_plugin');
	}

	/**
	 * Assign data to the defined variable or return the data
	 * @param string $type
	 * @param string|array|null $id
	 * @param string|null$context
	 * @param bool|string $assign
	 * @return mixed
	 */
	private function getItems(string $type, $id = null, ?string $context = null, $assign = true) {
		return $this->data->getItems($type, $id, $context, $assign);
	}
    /**
     * @return void
     */
    private function initImageComponent(): void {
        if(!isset($this->imagesComponent)) $this->imagesComponent = new component_files_images($this->template);
    }
	/**
	 * @param array $data
	 * @return array
	 */
	private function setItemContentData(array $data): array {
		$arr = [];
		if(!empty($data)) {
			foreach ($data as $page) {
				if (!array_key_exists($page['id_geoloc'], $arr)) {
					$arr[$page['id_geoloc']] = [
						'id_geoloc' => $page['id_geoloc']
					];
				}
				$arr[$page['id_geoloc']]['content'][$page['id_lang']] = [
					'id_lang' => $page['id_lang'],
					'name_geoloc' => $page['name_geoloc'],
					'content_geoloc' => $page['content_geoloc'],
                    'seo_title_geoloc' => $page['seo_title_geoloc'],
                    'seo_desc_geoloc' => $page['seo_desc_geoloc'],
					'published_geoloc' => $page['published_geoloc']
				];
			}
		}
		return $arr;
	}

	/**
	 * @param array $data
	 * @return array
	 */
	private function setItemAddressData(array $data): array {
		$arr = [];
		if(!empty($data)) {
			foreach ($data as $page) {
				if (!array_key_exists($page['id_address'], $arr)) {
					$arr[$page['id_address']] = [
						'id_address' => $page['id_address'],
						'img_address' => $page['img_address']
					];
				}
                $tagData = parent::fetchData(
                    array('context'=>'all','type'=>'tags'),
                    array('id_lang'=>$page['id_lang'])
                );
                if($tagData != null){
                    $newArrayTags = array();
                    foreach($tagData as $item){
                        $newArrayTags[]=$item['name_tag'];
                    }
                    $tags = implode(',',$newArrayTags);
                }else{
                    $tags = '';
                }
                $publicUrl = '/'.$page['iso_lang'].'/geoloc/'.$page['id_address'].'-'.$page['url_address'].'/';

				$arr[$page['id_address']]['content'][$page['id_lang']] = [
					'id_lang' => $page['id_lang'],
					'company_address' => $page['company_address'],
					'content_address' => $page['content_address'],
					'address_address' => $page['address_address'],
					'postcode_address' => $page['postcode_address'],
					'country_address' => $page['country_address'],
					'city_address' => $page['city_address'],
					'phone_address' => $page['phone_address'],
					'mobile_address' => $page['mobile_address'],
					'fax_address' => $page['fax_address'],
					'email_address' => $page['email_address'],
					'vat_address' => $page['vat_address'],
					'lat_address' => $page['lat_address'],
					'lng_address' => $page['lng_address'],
                    'facebook_address'   => $page['facebook_address'],
                    'instagram_address'  => $page['instagram_address'],
                    'linkedin_address'   => $page['linkedin_address'],
                    'website_address'    => $page['website_address'],
                    'suppl_address'      => $page['suppl_address'],
					'url_address' => $page['url_address'],
                    'seo_title_address' => $page['seo_title_address'],
                    'seo_desc_address' => $page['seo_desc_address'],
					'public_url' => $publicUrl,
					'img_address' => $page['img_address'],
					'published_address' => $page['published_address'],
                    'tags_address'      => $page['tags_address'],
                    'tags'              => $tags
				];
			}
		}
		return $arr;
	}

	/**
	 * set Data from database
	 * @param string $type
	 * @return array
	 */
	private function getBuildItems(string $type): array {
		switch($type){
			case 'content':
				$collection = $this->getItems('pages',null,'all',false);
				return $this->setItemContentData($collection);
			case 'address':
				$collection = $this->getItems('addressContent',$this->edit,'all',false);
				return $this->setItemAddressData($collection);
		}
		return [];
	}

	/**
	 * @param array $config
	 * @return void
	 * @throws Exception
	 */
	public function setSitemap(array $config) {
		$dateFormat = new date_dateformat();
		//print 'lang sitemap plugins: '.$config['id_lang'];
		$url = '/' . $config['iso_lang']. '/'.$config['name'].'/';
		$this->xml->writeNode([
			'type' => 'child',
			'loc' => $this->sitemap->url(['domain' => $config['domain'], 'url' => $url]),
			'image' => false,
			'lastmod' => $dateFormat->dateDefine(),
			'changefreq' => 'always',
			'priority' => '0.7'
		]);
	}

	/**
	 * @access private
	 * Charge les données de configuration pour l'édition
	 */
	private function setConfigData() {
		$config = parent::fetchData(['context' => 'all','type' => 'config']);
		$configId = [];
		$configValue = [];
		foreach ($config as $key) {
			$configId[] = $key['config_id'];
			$configValue[] = $key['config_value'];
		}
		$setConfig = array_combine($configId, $configValue);
		$this->template->assign('getConfigData', $setConfig);
	}

	/**
	 * Insert data
	 * @param array $config
	 */
	private function add(array $config) {
		switch ($config['type']) {
			case 'address':
				return parent::insert($config['type']);
			case 'addressContent':
			case 'content':
            case 'newTagRel':
            case 'newTagComb':
				parent::insert($config['type'], $config['data']);
				break;
		}
	}

	/**
	 * Update data
	 * @param array $config
	 */
	private function upd(array $config) {
		switch ($config['type']) {
			case 'address':
			case 'addressContent':
			case 'img':
			case 'content':
				parent::update($config['type'],$config['data']);
				break;
			case 'config':
				parent::update($config['type'],$config['data']);
				$this->message->json_post_response(true,'update');
				break;
		}
	}

	/**
	 * Delete a record
	 * @param array $config
	 */
	private function del(array $config) {
		switch ($config['type']) {
			case 'address':
            case 'tagRel':
            case 'tags':
				parent::delete($config['type'],$config['data']);
				$this->message->json_post_response(true,'delete',array('id' => $this->id));
				break;
		}
	}

	/**
	 *
	 */
	private function loadModules() {
		$this->module = isset($this->module) ?: new backend_controller_module();
		if(empty($this->mods)) $this->mods = $this->module->load_module('geoloc');
	}

	private function getModuleTabs() {
		$newsItems = [];
		foreach ($this->mods as $name => $mod) {
			$item['name'] = $name;
			if (method_exists($mod, 'getExtensionName')) {
				$this->template->addConfigFile(
					array(component_core_system::basePath() . 'plugins' . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'i18n' . DIRECTORY_SEPARATOR),
					array($name . '_admin_')
				);
				//$this->template->configLoad();
				$item['title'] = $mod->getExtensionName();
			} else {
				$item['title'] = $name;
			}
			$newsItems[] = $item;
		}
		$this->template->assign('setTabsPlugins', $newsItems);
	}
    /**
     * Adds the plugin in resizing images
     * @return array
     */
    public function getItemsImages(): array
    {
        $data = $this->getItems('img',NULL,'all',false);
        $newArr = [];
        if(!empty($data)) {
            foreach($data as $key => $value){
                $newArr[$key]['id'] = $value['id_address'];
                $newArr[$key]['img'] = $value['img_address'];
            }
        }
        return $newArr;
    }
	/**
	 *
	 */
	public function run(){
		$this->loadModules();

		if(isset($this->plugin)) {
			$this->modelLanguage->getLanguage();
			$defaultLanguage = $this->collectionLanguage->fetchData(['context' => 'one', 'type' => 'default']);
			$this->getItems('address',['default_lang' => $defaultLanguage['id_lang']],'all');
			$this->getModuleTabs();
			// Initialise l'API menu des plugins core
			$this->modelLanguage->getLanguage();
			// Execute un plugin core
			$class = 'plugins_' . $this->plugin . '_core';
			if(file_exists(component_core_system::basePath().'plugins'.DIRECTORY_SEPARATOR.$this->plugin.DIRECTORY_SEPARATOR.'core.php') && class_exists($class) && method_exists($class, 'run')) {
				$executeClass =  new $class;
				if($executeClass instanceof $class){
					$executeClass->run();
				}
			}
		}
		else {
			if(isset($this->tab)) {
				if ($this->tab === 'content') {
					if (isset($this->action)) {
						switch ($this->action) {
							case 'edit':
								if(!empty($this->content)) {
									$root = parent::fetchData(['context' => 'one', 'type' => 'root']);
									if (!$root) {
										parent::insert('root');
										$root = parent::fetchData(['context' => 'one', 'type' => 'root']);
									}
									$id = $root['id_geoloc'];

									foreach ($this->content as $lang => $content) {
										if(empty($content['id'])) $content['id'] = $id;
										$rootLang = $this->getItems('content',array('id' => $id,'id_lang' => $lang),'one',false);

										$content['id_lang'] = $lang;
										$content['published_geoloc'] = (!isset($content['published_geoloc']) ? 0 : 1);

										$config = [
											'type' => 'content',
											'data' => $content
										];

										($rootLang) ? $this->upd($config) : $this->add($config);
									}
									$this->message->json_post_response(true,'update');
								}
								break;
						}
					}
				}
				elseif ($this->tab === 'config') {
					if (isset($this->action)) {
						switch ($this->action) {
							case 'edit':
								if (!empty($this->cfg)) {
									if(!empty($this->cfg['markerColor'])) {
										$marker = new plugins_geoloc_marker($this->cfg['markerColor'], $this->template);
										$marker->createMarker();
									}

									$this->upd([
										'type' => 'config',
										'data' => $this->cfg
									]);
								}
								break;
						}
					}
				}
				elseif ($this->tab === 'address') {

                    /*if(http_request::isMethod('POST')) {
                        var_dump($this->tab);
                        var_dump($this->action);
                        var_dump($this->address);
                    }*/
					switch ($this->action) {
						case 'add':
						case 'edit':
							if(!empty($this->address)) {
								$notify = 'update';
								$img = null;

								if(!empty($this->id)) {
									$img = parent::fetchData(array('context' => 'one', 'type' => 'img'),[$this->id]);
									$img = $img['img_address'];
								}

								if (!isset($this->id)) {
                                    $lastAddress = $this->add(['type' => 'address']);
									//$lastAddress = $this->getItems('lastAddress', null,'one',false);
                                    if($lastAddress !== false) {
                                        $this->id = $lastAddress[0]['id_address'];
                                        $notify = 'add_redirect';
                                    }
								}
                                //print 'test';
								if(!empty($this->img) && !empty($this->id)) {
									$resultUpload = $this->upload->setImageUpload(
										'img', [
										'name' => filter_rsa::randMicroUI(),
										'edit' => $img,
										'prefix' => ['s_','m_','l_'],
                                        'module_img'      => 'geoloc',
                                        'attribute_img'   => 'geoloc',
										'original_remove' => false
									],[
										'upload_root_dir' => 'upload/geoloc', //string
										'upload_dir' => $this->id //string ou array
									], false);
									if(!empty($resultUpload)) {
										$this->upd([
											'type' => 'img',
											'data' => [
												'id' => $this->id,
												'img' => $resultUpload['file']
											]
										]);
									}
								}

                                //var_dump($this->address);

                                if(!empty($this->id)) {
                                    //print_r($this->address);
                                    foreach ($this->address as $lang => $address) {
                                        $address['id_lang'] = $lang;
                                        //$address['blank_address'] = (!isset($address['blank_address']) ? 0 : 1);
                                        $address['published_address'] = (!isset($address['published_address']) ? 0 : 1);
                                        $address['url_address'] = http_url::clean($address['company_address'],[
                                            'dot' => false,
                                            'ampersand' => 'strict',
                                            'cspec' => '', 'rspec' => ''
                                        ]);

                                        $addrLang = $this->getItems('addressContent',['id' => $this->id,'id_lang' => $lang],'one',false);

                                        if($addrLang) {
                                            $address['id'] = $addrLang['id_content'];
                                        }
                                        else {
                                            $address['id_address'] = $this->id;
                                        }
                                        if(!empty($address['tag_address'])) {
                                            $tagAddress = explode(',', $address['tag_address']);
                                            if ($tagAddress != null) {
                                                foreach ($tagAddress as $key => $value) {
                                                    $setTags = $this->getItems('tag',['id_address' => $this->id, 'id_lang' => $lang, 'name_tag' => $value],'one',false);
                                                    if ($setTags['id_tag'] != null) {
                                                        if ($setTags['rel_tag'] == null) {
                                                            $this->add([
                                                                'type' => 'newTagRel',
                                                                'data' => [
                                                                    'id_address'=> $this->id,
                                                                    'id_tag' => $setTags['id_tag']
                                                                ]
                                                            ]);
                                                        }
                                                    } else {
                                                        /*print_r([
                                                            'id_address' => $this->id,
                                                            'id_lang' => $lang,
                                                            'name_tag'=> $value
                                                        ]);*/
                                                        $this->add([
                                                            'type' => 'newTagComb',
                                                            'data' => [
                                                                'id_address' => $this->id,
                                                                'id_lang' => $lang,
                                                                'name_tag'=> $value
                                                            ]
                                                        ]);
                                                    }
                                                }
                                            }
                                        }

                                        if(isset($address['tag_address'])){
                                            unset($address['tag_address']);
                                        }

                                        //print_r($address);
                                        $config = [
                                            'type' => 'addressContent',
                                            'data' => $address
                                        ];
                                        $extendData = [];
                                        if(isset($this->id)) {
                                            $setEditData =  $this->getItems('addressContent',['id' => $this->id],'all',false);
                                            //print_r($setEditData);
                                            $setEditData = $this->setItemAddressData($setEditData);
                                            $extendData[$lang] = $setEditData[$this->id]['content'][$lang]['public_url'];
                                        }
                                        $addrLang ? $this->upd($config) : $this->add($config);
                                    }
                                }
                                if(isset($this->id)){
                                    $this->message->json_post_response(true,$notify,array('result'=>$this->id,'extend'=>$extendData));
                                }else{
                                    $this->message->json_post_response(true,$notify);
                                }
							}
							else {
								$this->modelLanguage->getLanguage();
								$country = new component_collections_country();
								$this->template->assign('countries',$country->getCountries());
								$this->setConfigData();

								if(isset($this->edit)) {
									$setEditData = $this->getBuildItems('address');
									$this->template->assign('address', $setEditData[$this->edit]);
								}

								$this->template->assign('edit', $this->action === 'edit');
								$this->template->display('edit.tpl');
							}
							break;
						case 'delete':
                            if (isset($this->name_tag)) {

                                $setTags = $this->getItems('tag',['id_address' => $this->id,'id_lang' => $this->id_lang, 'name_tag' => $this->name_tag],'one',false);

                                if ($setTags['id_tag'] != null && $setTags['rel_tag'] != null) {

                                    //parent::delete(array('type' => 'tagRel'), array('id_rel' => $setTags['rel_tag']));
                                    $this->del(
                                        [
                                            'type' => 'tagRel',
                                            'data' => [
                                                'id_rel' => $setTags['rel_tag']
                                            ]
                                        ]
                                    );
                                    // On compte le nombre de tags restant
                                    if($setTags['id_tag'] > 5) {

                                        $countTags = $this->getItems('countTags',['id_tag' => $setTags['id_tag']],'one',false);
                                        //Si le nombre est égal 0 on supprime le tag définitivement.
                                        if($countTags['tags'] == '0'){
                                            //parent::delete(array('type' => 'tags'), array('id_tag' => $setTags['id_tag']));
                                            $this->del(
                                                [
                                                    'type' => 'tags',
                                                    'data' => [
                                                        'id_tag' => $setTags['id_tag']
                                                    ]
                                                ]
                                            );
                                        }
                                    }
                                }
                            }else {
                                if (isset($this->id) && !empty($this->id)) {
                                    $this->del(
                                        array(
                                            'type' => 'address',
                                            'data' => array(
                                                'id' => $this->id
                                            )
                                        )
                                    );
                                }
                            }
							break;
						/*case 'order':
							if (isset($this->address)) {
								$this->update_order();
							}
							break;*/
					}
				}
			}
			else {
				$this->modelLanguage->getLanguage();
				$defaultLanguage = $this->collectionLanguage->fetchData(['context'=>'one','type'=>'default']);
				$this->setConfigData();

				$last = parent::fetchData(['context' => 'one', 'type' => 'root']);
				$pages = $this->getBuildItems('content');
				$this->template->assign('pages', (isset($last['id_geoloc']) ? $pages[$last['id_geoloc']] : []));

				$this->getItems('address',['default_lang' => $defaultLanguage['id_lang']],'all');
				$assign = [
                    'id_address',
                    'company_address' => ['title' => 'name'],
                    'address_address' => ['title' => 'name'],
                    'postcode_address' => ['title' => 'name'],
                    'country_address' => ['title' => 'name'],
                    'content_address' => ['class' => 'fixed-td-lg', 'type' => 'bin', 'input' => null],
                    'date_register'
                ];
				$this->data->getScheme(['mc_geoloc_address', 'mc_geoloc_address_content'], ['id_address', 'company_address', 'address_address','postcode_address','country_address','content_address', 'date_register'], $assign);

				$this->getModuleTabs();
				$this->template->display('index.tpl');
			}
		}
	}
}