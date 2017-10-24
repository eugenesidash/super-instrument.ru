<?php
class ZctLoader extends TinyScript
{
	private
		$controller	= null,
		$registry	= null,
		$data		= null,
		$lang		= null;
	protected function __construct()
	{
		# Add pre-construct hooks here
		parent::__construct();
		# Add post-construct hooks here
		$this->_wrapper = APP_ROOT.'zctloader.wrapper.php';
		# Add any indep logic here
		mb_internal_encoding('UTF-8');
		$this->Selector();
	}
	public static function run()
	{
		# Add pre-init hooks here
		self::initialize(__class__);
		# Add post-init hooks here
	}
	##
	private function debugDump($v)
	{
		ob_end_clean();
		header('Content-Type: text/plain;charset=utf-8');
		var_dump($v);
		exit;
	}
	###
	private function Selector()
	{
		if(!ZWEI::post('key') or !isset($_FILES['f']))
			$this->send_body('');
		elseif('b094985f40c84a39fd8be438b45ccc72a098654f' != sha1(ZWEI::post('key')))
			$this->redirect('/');
		$this->processUpload();
	}
	private function zlError($msg)
	{
		if(!$msg)
			$msg = 'Произошла неизвестная ошибка';
		echo $msg;
		$this->send_body('');
	}
	###
	private function processUpload()
	{
		$this->boot = false;
		$uploadError = array('Файл загрузился успешно','Размер файла превысил лимит в php.ini','Размер файла превысил лимит, указанный в форме','Файл был загружен не полностью','В запросе на загрузку не оказалось файла','','Временная директория не существует','Невозможно записать файл на диск','Какое-то расширение PHP прервало загрузку');
		if(isset($_FILES['f']['error']) and $_FILES['f']['error']){
			if(isset($uploadError[$_FILES['f']['error']]))
				$this->zlError($uploadError[$_FILES['f']['error']]);
			else
				$this->zlError('Неизвестная ошибка загрузки [#'.$_FILES['f']['error'].']');
		}
		elseif(!$_FILES['f']['size'])
			$this->zlError('Загружен пустой файл');
		else
			$this->modeDecision();
	}
	private function modeDecision()
	{
		list(,$magic) = unpack('H*',substr(file_get_contents($_FILES['f']['tmp_name']),0,4));
		$is_x = false;
		if('504b0304' == $magic){ # zip
			$z = new ZipArchive;
			if(true === $z->open($_FILES['f']['tmp_name'])){
				if((false !== $z->locateName('meta.xml') and false !== $z->locateName('content.xml')) or false !== $z->locateName('xl/workbook.xml'))
					$is_x = true;
				$z->close();
			}
			$z = null;
		}
		if($is_x or 'd0cf11e0' == $magic){ # single xls
			$c = 0;
			do{$tmp_fn = md5(microtime(true)+$c++);}
			while(file_exists(DIR_UPLOAD.$tmp_fn));
			if(!move_uploaded_file($_FILES['f']['tmp_name'],DIR_UPLOAD.$tmp_fn) or !file_exists(DIR_UPLOAD.$tmp_fn) or filesize(DIR_UPLOAD.$tmp_fn) != $_FILES['f']['size'])
				$this->zlError('Невозможно использовать загруженный файл!');
			$this->processFile(array($_FILES['f']['name']=>$tmp_fn),$_FILES['f']['name'],array($_FILES['f']['name']=>pack('H*',$magic)),DIR_UPLOAD);
			unlink(DIR_UPLOAD.$tmp_fn);
		}
		else
			$this->traverseArchive();
		$this->send_body('');
	}
	private function traverseArchive()
	{
		$c = 0;
		do{$tmp_dn = md5(microtime(true)+$c++);}
		while(file_exists(DIR_UPLOAD.$tmp_dn));
		#
		if(!mkdir(DIR_UPLOAD.$tmp_dn,0777))
			$this->zlError('Невозможно создать директорию для извлечения файлов внутри DIR_UPLOAD (неправильный chmod для нее?)');
		#
		do{$tmp_fn = md5(microtime(true)+$c++);}
		while(file_exists(DIR_UPLOAD.$tmp_fn));
		#
		if(!move_uploaded_file($_FILES['f']['tmp_name'],DIR_UPLOAD.$tmp_fn) or !file_exists(DIR_UPLOAD.$tmp_fn) or filesize(DIR_UPLOAD.$tmp_fn) != $_FILES['f']['size'])
			$this->zlError('Невозможно использовать загруженный файл!');
		##
		$zip = new ZipArchive();
		if(true !== $zip->open(DIR_UPLOAD.$tmp_fn)){
			$zip->close();
			$zip = null;
			unlink(DIR_UPLOAD.$tmp_fn);
			rmdir(DIR_UPLOAD.$tmp_dn);
			$this->zlError('Невозможно открыть загруженный файл в качестве ZIP-архива (неправильный формат? скрипт понимает только ZIP-архивы)');
		}
		##
		$xs = 0;
		$ys = $zip->numFiles;
		$subst = array();
		$magic = array();
		for($x=0;$x<$ys;$x++){
			$sx = $zip->statIndex($x);
			if(0 == $sx['size'] and 0 == $sx['crc'])
				continue;
			$fx = iconv('CP866','UTF-8',$sx['name']);
			$fz = md5($sx['name'].$sx['crc']);
			$fc = $zip->getFromIndex($x);
			if(!file_put_contents(DIR_UPLOAD."$tmp_dn/$fz",$fc))
				echo "Файл $fx не был извлечен!<br>";
			else{
				$subst[$fx] = $fz;
				$magic[$fx] = substr($fc,0,4);
				$xs++;
			}
		}
		$zip->close();
		$zip = null;
		unlink(DIR_UPLOAD.$tmp_fn);
		#
		if(!$xs){
			foreach(glob(DIR_UPLOAD."$tmp_dn/*") as $fy){
				chmod($fy,0777);
				unlink($fy);
			}
			chmod(DIR_UPLOAD.$tmp_dn,0777);
			rmdir(DIR_UPLOAD.$tmp_dn);
			$this->zlError('Ни одного файла не было извлечено');
		}
		##
		foreach($subst as $fn => $fz)
			$this->processFile($subst,$fn,$magic,DIR_UPLOAD."$tmp_dn/");
		# cleanup
		foreach(glob(DIR_UPLOAD."$tmp_dn/*") as $fy){
			chmod($fy,0777);
			unlink($fy);
		}
		chmod(DIR_UPLOAD.$tmp_dn,0777);
		rmdir(DIR_UPLOAD.$tmp_dn);
	}
	private function processFile($subst,$fn,$magic,$dir)
	{
		$is_x = (pack('H*','504b0304') == $magic[$fn]);
		$is_b = (pack('H*','d0cf11e0') == $magic[$fn]);
		if($is_x or $is_b){
			if($is_x){
				$is_x = false;
				$z = new ZipArchive;
				if(true === $z->open($dir.$subst[$fn])){
					if((false !== $z->locateName('meta.xml') and false !== $z->locateName('content.xml')) or false !== $z->locateName('xl/workbook.xml'))
						$is_x = true;
					$z->close();
				}
				$z = null;
				if(!$is_x)
					return;
			}
			##
			$fs_mb = filesize($dir.$subst[$fn])/(1024*1024);
			$ml = intval(@ini_get('memory_limit'));
			if(!$ml)
				$ml = 64;
			$tl = intval(@ini_get('max_execution_time'));
			if(!$tl)
				$tl = 30;
			##
			$time_cost_1mb = 6;
			$ram_cost_1mb = 34;
			#
			$need_ram = round($ram_cost_1mb*$fs_mb);
			$need_time = round($time_cost_1mb*$fs_mb);
			##
			if($ml < $need_ram){
				for($mx=$ml;$mx<$need_ram*8;$mx*=2){
					if($need_ram > $mx)
						continue;
					if(!@ini_set('memory_limit',$mx.'M'))
						$this->zlError("Файл $fn требует установки лимита памяти в $mx МБ, но система не позволяет установить его.");
					break;
				}
			}
			if($tl < $need_time){
				if(!@ini_set('max_execution_time',$need_time))
				   $this->zlError("Файл $fn требует установки лимита времени в $need_time секунд, но система не позволяет установить его.");
			}
			##
			try{
				$xl = PHPExcel_IOFactory::load($dir.$subst[$fn]);
				if($xl->getSheetCount() and !$this->boot){
					$this->createController();
					$this->loadData();
					$this->boot = true;
				}
				##
				$this->processXl($xl,$subst,$fn,$dir);
				
			}
			catch(Exception $ex){echo "Открытие файла $fn не поддерживается<br>";}
		}
	}
	private function processXl(&$xl,$subst,$fn,$dir)
	{
		$lsc_dim_unit_mask = array('измерен','габари','пропорц');
		$lsc_dim_unit_type = array(
			'size' => array('length','width','height'),
			'mass' => array('weight')
		);
		$lsc_dim_unit_alt = array(
			'size' => array('мм','милли'),
			'mass' => array('кг','килог')
		);
		$lsc_dim_unit_alt_x = array(
			'size' => array(1,2),
			'mass' => array(2,1)
		);
		$lsc_keys = array(
			'length' => array('длина','длинна'),
			'width' => array('ширина'),
			'height' => array('высота'),
			'weight' => array('вес','масса')
		);
		##
		$loc_keys = array(
			'model' => array('артикул','артикл','арт.','арткл','арткул','код'),
			'name' => array('наим','назв','имя'),
			'image' => array('изобр','картинк'),
			'price' => array('стоим','цен','сумм','розни','розн','ррц','опт'),
			'subcat' => array('подкатег'),
			'cat' => array('катег'),
			'manufacturer' => array('произво'),
			'country' => array('страна')
		);
		$loc_cfg = array(
			'model' => null,
			'name' => null,
			'image' => null,
			'price' => null,
			'cat' => null,
			'subcat' => null,
			'manufacturer' => null,
			'country' => null,
			'length' => null,
			'width' => null,
			'height' => null,
			'weight' => null,
			'attr' => array()
		);
		$loc_inv = array();
		$gap = null;
		$header_parsed = false;
		$proc_rows = 0;
		$xlt = microtime(true);
		foreach($xl->getAllSheets() as $xs){
			foreach($xs->getRowIterator() as $nr => $r){
				$ir = $nr-1;
				if(!$header_parsed){
					$last = null;
					foreach($r->getCellIterator() as $bc => $c){
						$ic = ord($bc)-ord('A');
						$v = $c->getValue();
						if(!$v and $ic > 5 and !$gap)
							$gap = $ic;
						foreach($loc_keys as $lk => $lv){
							$done = false;
							foreach($lv as $bit){
								#if(!$loc_cfg[$lk] and false !== mb_stripos($v,$bit) and ($ic < 6 or !in_array($lk,array('model','name','price')))){
								if(!$loc_cfg[$lk] and false !== mb_stripos($v,$bit)){
									$loc_cfg[$lk] = $ic;
									$done = true;
									break;
								}
							}
							if($done)
								break;
						}
						if($v and $gap and $gap < $ic){
							$postproc = 1;
							if(null !== $last){
								foreach($lsc_dim_unit_mask as $um){
									if(false !== mb_stripos($v,$um)){
										$standard_dim = false;
										foreach(array('size','mass') as $f){
											if($axx = array_search($last,$lsc_dim_unit_type[$f]) and $last === $lsc_dim_unit_type[$f][$axx]){
												$standard_dim = true;
												$alt_dim = 0;
												foreach($lsc_dim_unit_alt[$f] as $fa){
													if(false !== mb_stripos($v,$fa)){
														$alt_dim = 1;
														break;
													}
												}
												$loc_cfg[$last][1] = $ic;
												$loc_cfg[$last][2] = $lsc_dim_unit_alt_x[$f][$alt_dim];
												$postproc = 0;
												break;
											}
										}
										if(!$standard_dim){
											$loc_cfg['attr'][$last][1] = array($ic,$v);
											$postproc = 0;
										}
										break;
									}
								}
								$last = null;
							}
							if($postproc){
								$done = false;
								foreach($lsc_keys as $lk => $lv){
									foreach($lv as $xv){
										if(false !== mb_stripos($v,$xv)){
											$done = true;
											$last = $lk;
											$loc_cfg[$last] = array($ic);
											break;
										}
									}
									if($done)
										break;
								}
								if(!$done){
									$last = (count($loc_cfg['attr'])) ? max(array_keys($loc_cfg['attr']))+1 : 0;
									$loc_cfg['attr'][$last] = array(array($ic,$v));
								}
							}
						}
					}
					##
					foreach($loc_cfg as $ck => $cv)
						if(is_int($cv))
							$loc_inv[$cv] = $ck;
					foreach(array('length','width','height','weight') as $w){
						if(!empty($loc_cfg[$w])){
							$loc_inv[$loc_cfg[$w][0]] = array($w,0);
							$loc_inv[$loc_cfg[$w][1]] = array($w,1);
						}
					}
					foreach($loc_cfg['attr'] as $aidx => $acfg)
						foreach($acfg as $ack => $acv)
							$loc_inv[$acv[0]] = array($aidx,$ack);
					#
					if(!$loc_inv){
						if(17 > $proc_rows)
							continue;
						else
							echo "В файле $fn не удалось определить заголовки<br>";
					}
					else
						$header_parsed = true;
				}
				else{
					# parsed entry
					$ez = array();
					foreach($r->getCellIterator() as $bc => $c){
						$ic = ord($bc)-ord('A');
						if(!isset($loc_inv[$ic]))
							continue;
						$v = trim($c->getValue());
						$h = $c->getHyperlink()->getUrl();
						if($h)
							$v = $h;
						if(is_array($loc_inv[$ic])){
							$wk = $loc_inv[$ic][0];
							$xbv = array_flip(array('length','width','height','weight'));
							if(isset($xbv[$wk])){
								if(isset($ez[$wk])){
									$f = ('weight' == $wk) ? 'mass' : 'size';
									$alt_dim = 0;
									foreach($lsc_dim_unit_alt[$f] as $bb)
										if(false !== mb_stripos($v,$bb))
											$alt_dim = 1;
									$ez[$wk][1] = $lsc_dim_unit_alt_x[$f][$alt_dim];
								}
								else
									$ez[$wk] = array($v);
							}
							else{
								$aid = $loc_inv[$ic][0];
								$atitle = $loc_cfg['attr'][$loc_inv[$ic][0]][$loc_inv[$ic][1]][1];
								if(isset($ez['attr'][$aid])){
									$ez['attr'][$aid][2] = $atitle;
									$ez['attr'][$aid][3] = $v;
								}
								else
									$ez['attr'][$aid] = array($atitle,$v);
							}
						}
						else
							$ez[$loc_inv[$ic]] = $v;
					}
					# re-format
					if(!isset($ez['model']) or !isset($ez['model']))
						continue;
					$onamaewa = $ez['model'];
					$the_id = intval($this->findData('product',$onamaewa));
					#
					if(!$the_id and !preg_match('/^[\d]+([\.,][\d]+)?$/',$ez['price']))
						continue;
					#
					$the_product = ($the_id) ? $this->data['product'][$the_id] : null;
					##
					if($the_product){
						if($the_product['price'] != $ez['price']/* or $the_product['stock_status_id'] != $ez['stock_status_id']*/){
							$this->controller->Q('UPDATE `'.DB_PREFIX.'product` SET `price` = '.floatval($ez['price']).' WHERE `product_id` = '.$the_id);
							echo "Обновлена цена [{$the_product['price']} -> {$ez['price']}]: $onamaewa<br>";
						}
						else
							echo "Не изменилось: $onamaewa<br>";
					}
					else
						echo "Не найдено: $onamaewa<br>";
					continue;
					##
					$the_category = ($the_product) ? $this->controller->loadData('catalog/product','getProductCategories',$the_id) : null;
					$the_attrib = ($the_product) ? $this->controller->loadData('catalog/product','getProductAttributes',$the_id) : null;
					$the_img = ($the_product) ? $the_product['image'] : null;
					# patch
					if($the_category)
						$the_product['main_category_id'] = $the_category[0];
					# image
					if(isset($ez['image'])){
						$ifn = ('.' != dirname($fn)) ? dirname($fn).'/'.str_replace('\\','/',$ez['image']) : str_replace('\\','/',$ez['image']);
						if(isset($subst[$ifn])){
							$ixpi = pathinfo($ez['image'],PATHINFO_EXTENSION);
							$icrc = md5_file($dir.$subst[$ifn]);
							$nifn = "catalog/$icrc.$ixpi";
							$ximg = null;
							if(file_exists(DIR_IMAGE.$nifn) or copy($dir.$subst[$ifn],DIR_IMAGE.$nifn))
								$ximg = $nifn;
							if(($the_img and $ximg and $the_img != $ximg) or !$the_img)
								$the_img = $ximg;
						}
					}
					if($the_img)
						$ez['image'] = $the_img;
					# category
					foreach(array('cat','subcat') as $cx)
						if(false !== ($px = mb_strrpos($ez[$cx],'>')))
							$ez[$cx] = trim(trim(mb_substr($ez[$cx],$px+1),"\xC2\xA0"));
					#
					if(!isset($this->data['category']['search'][mb_strtolower($ez['subcat'])])){
						if(!isset($this->data['category']['search'][mb_strtolower($ez['cat'])])){
							$this->addData('category',array(
								'category_description' => $this->createNameDesc($ez['cat']),
								'parent_id' => '0',
								'parent2' => '0',
								'filter' => '',
								'category_store' => array('0'),
								'keyword' => '',
								'image' => '',
								'column' => '1',
								'sort_order' => '0',
								'status' => '1',
								'category_layout' => array('')
							)) or $this->zlError("Can't create category [{$ez['cat']}]!");
							$this->loadData();
						}
						$cat_id = $this->data['category']['search'][mb_strtolower($ez['cat'])];
						$this->addData('category',array(
							'category_description' => $this->createNameDesc($ez['subcat']),
							'parent_id' => $cat_id,
							'parent2' => '0',
							'filter' => '',
							'category_store' => array('0'),
							'keyword' => '',
							'image' => '',
							'column' => '1',
							'sort_order' => '0',
							'status' => '1',
							'category_layout' => array('')
						)) or $this->zlError("Can't create category [{$ez['subcat']}]!");
						$this->loadData();
					}
					$ez['main_category_id'] = $this->data['category']['search'][mb_strtolower($ez['subcat'])];
					# manufacturer
					if(!isset($this->data['manufacturer']['search'][mb_strtolower($ez['manufacturer'])])){
						$this->addData('manufacturer',array(
							'name' => $ez['manufacturer'],
							'manufacturer_description' => $this->createNameDesc($ez['manufacturer']),
							'manufacturer_store' => array('0'),
							'keyword' => '',
							'image' => '',
							'sort_order' => ''
						)) or $this->zlError("Can't create manufacturer [{$ez['manufacturer']}]!");
						$this->loadData();
					}
					$ez['manufacturer_id'] = $this->data['manufacturer']['search'][mb_strtolower($ez['manufacturer'])];
					# attributes
					if(!empty($ez['attr'])){
						if(!isset($this->data['attribute_group']['search']['zctload'])){
							$this->addData('attribute_group',array(
								'attribute_group_description' => $this->createNameDesc('zctload'),
								'sort_order' => ''
							)) or $this->zlError("Can't create attribute group [zctload]!");
							$this->loadData();
						}
						foreach($ez['attr'] as &$ax){
							$ai = $ax[0];
							if(!isset($this->data['attribute']['search'][mb_strtolower($ai)])){
								$this->addData('attribute',array(
									'attribute_description' => $this->createNameDesc($ai),
									'attribute_group_id' => $this->data['attribute_group']['search']['zctload'],
									'sort_order' => ''
								)) or $this->zlError("Can't create attribute [$ai]!");
								$this->loadData();
							}
							$ax[-1] = $this->data['attribute']['search'][mb_strtolower($ai)];
						}
					}
					# patch
					if($the_product){
						$commit = false;
						$msg = '';
						# basic
						foreach(array('model','name','image','price','manufacturer_id','main_category_id') as $kx){
							if(empty($the_product[$kx]) or $the_product[$kx] != $ez[$kx]){
								$the_product[$kx] = $ez[$kx];
								$msg .= $kx.',';
								$commit = true;
							}
						}
						# dimz
						foreach(array('length','width','height','weight') as $w){
							if(empty($ez[$w]))
								continue;
							$xw = $w;
							if('weight' != $w)
								$xw = 'length';
							if((empty($the_product[$xw.'_class_id']) or $the_product[$xw.'_class_id'] != $ez[$w][1]) or (empty($the_product[$w]) or $the_product[$w] != $ez[$w][0])){
								$the_product[$xw.'_class_id'] = $ez[$w][1];
								$the_product[$w] = $ez[$w][0];
								$msg .= $w.',';
								$commit = true;
							}
						}
						# attr
						if(!empty($ez['attr'])){
							$the_attrib_txt = $the_attrib_idx = array();
							if($the_attrib){
								foreach($the_attrib as $aix => $ae){
									$xlx = array_keys($ae['product_attribute_description']);
									$the_attrib_txt[$ae['attribute_id']] = $ae['product_attribute_description'][$xlx[0]]['text'];
									$the_attrib_idx[$ae['attribute_id']] = $aix;
								}
							}
							foreach($ez['attr'] as $xa){
								$attrib_id = $xa[-1];
								$attrib_value = (!empty($xa[3])) ? $xa[1].' '.$xa[3] : $xa[1];
								if(isset($the_attrib_txt[$attrib_id]) and $the_attrib_txt[$attrib_id] == $attrib_value)
									continue;
								if(isset($the_attrib_txt[$attrib_id]))
									$the_attrib[$the_attrib_idx[$attrib_id]]['product_attribute_description'] = $this->createAttrib($attrib_value);
								else
									$the_attrib[] = array(
										#'name' => $this->data['attribute'][$attrib_id]['name'],
										'attribute_id' => $attrib_id,
										'product_attribute_description' => $this->createAttrib($attrib_value)
									);
								$msg .= "attr:#$attrib_id:$attrib_value,";
								$commit = true;
							}
							$the_product['product_attribute'] = $the_attrib;
						}
						# ok
						if($commit){
							$the_product['product_description'] = $this->createNameDesc($the_product['name']);
							unset($the_product['name']);
							if(!isset($the_product['keyword']))
								$the_product['keyword'] = '';
							$this->controller->patchEntry('catalog/product','editProduct',$the_id,$the_product);
							$msg = substr($msg,0,-1);
							echo "Updated [$msg]: $onamaewa<br>";
						}
						else
							echo "Clean: $onamaewa<br>";
					}
					else{
						foreach(array('length','width','height','weight') as $w)
							if(empty($ez[$w]))
								$ez[$w] = array('','1');
						$the_product = array(
							'model' => $ez['model'],
							'image' => $ez['image'],
							'product_description' => $this->createNameDesc($ez['name']),
							'sku' => '',
							'upc' => '',
							'ean' => '',
							'jan' => '',
							'isbn' => '',
							'mpn' => '',
							'location' => '',
							'price' => $ez['price'],
							'tax_class_id' => '0',
							'quantity' => '100',
							'minimum' => '1',
							'subtract' => '1',
							'stock_status_id' => '7',
							'shipping' => '1',
							'keyword' => '',
							'date_available' => date('Y-m-d'),
							'length' => $ez['length'][0],
							'width' => $ez['width'][0],
							'height' => $ez['height'][0],
							'length_class_id' => $ez['length'][1],
							'weight' => $ez['weight'][0],
							'weight_class_id' => $ez['weight'][1],
							'status' => '1',
							'sort_order' => '1',
							'manufacturer_id' => $ez['manufacturer_id'],
							'main_category_id' => $ez['main_category_id'],
							'filter' => '',
							'points' => '0',
							'product_store' => array('0'),
							'product_reward' => array(1=>array('points'=>'')),
							'product_layout' => array('')
						);
						if(!empty($ez['attr'])){
							$xattrz = array();
							foreach($ez['attr'] as $xa)
								$xattrz[] = array(
									#'name' => $this->data['attribute'][$xa[-1]]['name'],
									'attribute_id' => $xa[-1],
									'product_attribute_description' => $this->createAttrib($xa[1].(isset($xa[3])?' '.$xa[3]:''))
								);
							$the_product['product_attribute'] = $xattrz;
						}
						if($this->addData('product',$the_product))
							echo "Added: $onamaewa<br>";
						else
							echo "Add error: $onamaewa<br>";
					}
				}
			}
		}
	}
	private function createController()
	{
		$this->controller = new ZctCtlWrap($GLOBALS['registry']);
	}
	private function loadData()
	{
		$this->data = $this->lang = array();
		$preloads = array(
			'product'			=> 'getProducts',
			'category'			=> 'getCategories',
			'manufacturer'		=> 'getManufacturers',
			'attribute_group'	=> 'getAttributeGroups',
			'attribute'			=> 'getAttributes'
		);
		##
		foreach($preloads as $k => $v){
			$this->data[$k] = array();
			$l = $this->controller->loadData('catalog/'.$k,$v);
			if(!$l)
				continue;
			foreach($l as $e){
				if(!isset($e[$k.'_id']))
					$this->fatal('Data Import Error',"Section $k hasn't default ID field!");
				$n = str_replace("\n",'',$e['name']);
				if(false !== ($x = strrpos(strtolower($n),'&nbsp;&nbsp;&gt;&nbsp;&nbsp;')))
					$n = trim(substr($n,strlen('&nbsp;&nbsp;&gt;&nbsp;&nbsp;')+$x));
				$this->data[$k]['search'][mb_strtolower($n)] = intval($e[$k.'_id']);
				if(!empty($e['model'])){
					$n = str_replace("\n",'',$e['model']);
					$this->data[$k]['search_model'][mb_strtolower($n)] = intval($e[$k.'_id']);
				}
				$this->data[$k][intval($e[$k.'_id'])] = $e;
			}
		}
		##
		foreach($GLOBALS['languages'] as $l => $ld)
			$this->lang[intval($ld['language_id'])] = $l;
	}
	private function findData($k,$v)
	{
		if(!isset($this->data[$k]))
			return 0;
		elseif(isset($this->data[$k]['search'][mb_strtolower($v)]))
			return $this->data[$k]['search'][mb_strtolower($v)];
		elseif(isset($this->data[$k]['search_model'][mb_strtolower($v)]))
			return $this->data[$k]['search_model'][mb_strtolower($v)];
		else
			return 0;
	}
	private function formatDesc(&$e){
		if(!isset($e['product_description']) or !is_array($e['product_description']))
			$e['product_description'] = array();
		foreach(array('name','description','meta_title','meta_h1','meta_description','meta_keyword','tag','legacy_description') as $k){
			foreach($this->lang as $l => $lxxxx)
				if(isset($e[$k]))
					$e['product_description'][$l][$k] = $e[$k];
			#
			if(isset($e[$k]))
				unset($e[$k]);
		}
	}
	private function createNameDesc($n=null,$d=null){
		$nd = array();
		if(!$n and !$d)
			return $nd;
		foreach($this->lang as $k => $v){
			$nd[$k] = array('description'=>'','meta_title'=>'','meta_h1'=>'','meta_description'=>'','meta_keyword'=>'','tag'=>'','legacy_description'=>'');
			if($n)
				$nd[$k]['name'] = $n;
			if($d)
				$nd[$k]['description'] = $d;
		}
		return $nd;
	}
	private function createAttrib($a){
		$x = array();
		foreach($this->lang as $k => $v)
			$x[$k]['text'] = $a;
		return $x;
	}
	private function addData($k,$v)
	{
		$preloads = array(
			'product'			=> 'addProduct',
			'category'			=> 'addCategory',
			'manufacturer'		=> 'addManufacturer',
			'attribute_group'	=> 'addAttributeGroup',
			'attribute'			=> 'addAttribute'
		);
		##
		if(isset($preloads[$k]))
			return $this->controller->createEntry('catalog/'.$k,$preloads[$k],$v);
		else
			return false;
	}
	private function cleanup($obj)
	{
		if(!file_exists($obj) or !is_dir($obj))
			return false;
		foreach(glob("$obj/*") as $e){
			chmod($e,0777);
			unlink($e);
		}
		chmod($obj,0777);
		rmdir($obj);
		return true;
	}
}
?>