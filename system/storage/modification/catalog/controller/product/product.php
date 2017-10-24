<?php
class ControllerProductProduct extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('product/product');

			$this->load->model('unishop/setting');
			$this->load->model('unishop/attr_and_options');
			$settings = $this->model_unishop_setting->getSetting();
			$lang_id = $this->config->get('config_language_id');
			
			$data = isset($data) ? $data : array();
			$data['lang'] = array_merge($data, $this->language->load('unishop/unishop'));

			$data['shop_name'] = $this->config->get('config_name');
			
			$data['text_select'] = $this->language->get('text_select');
			
			$data['show_quick_order'] = isset($settings['show_quick_order']) ? $settings['show_quick_order'] : '';	
			$data['show_quick_order_text'] = isset($settings['show_quick_order_text']) ? $settings['show_quick_order_text'] : '';			
			$data['quick_order_icon'] = isset($settings['show_quick_order']) ? html_entity_decode($settings[$lang_id]['quick_order_icon'], ENT_QUOTES, 'UTF-8') : '';	
			$data['quick_order_title'] = isset($settings['show_quick_order']) ? $settings[$lang_id]['quick_order_title'] : '';
			
			$data['show_quick_order_quantity'] = isset($settings['show_quick_order_quantity']) ? $settings['show_quick_order_quantity'] : '';
			
			$data['show_attr'] = (isset($settings['show_attr']) ? $settings['show_attr'] : '');
			$data['show_attr_name'] = (isset($settings['show_attr_name']) ? $settings['show_attr_name'] : '');
			
			$data['show_description'] = isset($settings['show_description']) ? $settings['show_description'] : '';
			$data['show_description_alt'] = isset($settings['show_description_alt']) ? $settings['show_description_alt'] : '';
			
			$data['show_rating'] = isset($settings['show_rating']) ? $settings['show_rating'] : '';
			$data['show_rating_count'] = isset($settings['show_rating_count']) ? $settings['show_rating_count'] : '';
			
			$data['show_stock_status'] = isset($settings['show_stock_status']) ? $settings['show_stock_status'] : '';
			
			$data['wishlist_btn_disabled'] = isset($settings['wishlist_btn_disabled']) ? $settings['wishlist_btn_disabled'] : '';
			$data['compare_btn_disabled'] = isset($settings['compare_btn_disabled']) ? $settings['compare_btn_disabled'] : '';
			
			$currency = (VERSION >= 2.2) ? $this->session->data['currency'] : '';
			

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$this->load->model('catalog/category');

		if (isset($this->request->get['path'])) {
			$path = '';

			$parts = explode('_', (string)$this->request->get['path']);

			$category_id = (int)array_pop($parts);

			foreach ($parts as $path_id) {
				if (!$path) {
					$path = $path_id;
				} else {
					$path .= '_' . $path_id;
				}

				$category_info = $this->model_catalog_category->getCategory($path_id);

				if ($category_info) {
					$data['breadcrumbs'][] = array(
						'text' => $category_info['name'],
						'href' => $this->url->link('product/category', 'path=' . $path)
					);
				}
			}

			// Set the last category breadcrumb
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$url = '';

				if (isset($this->request->get['sort'])) {
					$url .= '&sort=' . $this->request->get['sort'];
				}

				if (isset($this->request->get['order'])) {
					$url .= '&order=' . $this->request->get['order'];
				}

				if (isset($this->request->get['page'])) {
					$url .= '&page=' . $this->request->get['page'];
				}

				if (isset($this->request->get['limit'])) {
					$url .= '&limit=' . $this->request->get['limit'];
				}

				$data['breadcrumbs'][] = array(
					'text' => $category_info['name'],
					'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url)
				);
			}
		}

		$this->load->model('catalog/manufacturer');

		if (isset($this->request->get['manufacturer_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_brand'),
				'href' => $this->url->link('product/manufacturer')
			);

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($this->request->get['manufacturer_id']);

			if ($manufacturer_info) {
				$data['breadcrumbs'][] = array(
					'text' => $manufacturer_info['name'],
					'href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . $url)
				);
			}
		}

		if (isset($this->request->get['search']) || isset($this->request->get['tag'])) {
			$url = '';

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_search'),
				'href' => $this->url->link('product/search', $url)
			);
		}

		if (isset($this->request->get['product_id'])) {
			$product_id = (int)$this->request->get['product_id'];
		} else {
			$product_id = 0;
		}

		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($product_id);

		if ($product_info) {
			$url = '';

			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['manufacturer_id'])) {
				$url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
			}

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $product_info['name'],
				'href' => $this->url->link('product/product', $url . '&product_id=' . $this->request->get['product_id'])
			);

			if ($product_info['meta_title']) {
				$this->document->setTitle($product_info['meta_title']);
			} else {
				$this->document->setTitle($product_info['name']);
			}

			$this->document->setDescription($product_info['meta_description']);
			$this->document->setKeywords($product_info['meta_keyword']);
			$this->document->addLink($this->url->link('product/product', 'product_id=' . $this->request->get['product_id']), 'canonical');
			$this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
			$this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');
			$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment.js');
			$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/locale/'.$this->session->data['language'].'.js');
			$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
			$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

			if ($product_info['meta_h1']) {
				$data['heading_title'] = $product_info['meta_h1'];
			} else {
				$data['heading_title'] = $product_info['name'];;
			}

			$data['text_select'] = $this->language->get('text_select');
			$data['text_manufacturer'] = $this->language->get('text_manufacturer');
			$data['text_model'] = $this->language->get('text_model');
			$data['text_reward'] = $this->language->get('text_reward');
			$data['text_points'] = $this->language->get('text_points');
			$data['text_stock'] = $this->language->get('text_stock');
			$data['text_discount'] = $this->language->get('text_discount');
			$data['text_tax'] = $this->language->get('text_tax');
			$data['text_option'] = $this->language->get('text_option');
			$data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
			$data['text_write'] = $this->language->get('text_write');
			$data['text_login'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', 'SSL'), $this->url->link('account/register', '', 'SSL'));
			$data['text_note'] = $this->language->get('text_note');
			$data['text_tags'] = $this->language->get('text_tags');
			$data['text_related'] = $this->language->get('text_related');
			$data['text_payment_recurring'] = $this->language->get('text_payment_recurring');
			$data['text_loading'] = $this->language->get('text_loading');

			$data['entry_qty'] = $this->language->get('entry_qty');
			$data['entry_name'] = $this->language->get('entry_name');
			$data['entry_review'] = $this->language->get('entry_review');
			$data['entry_rating'] = $this->language->get('entry_rating');
			$data['entry_good'] = $this->language->get('entry_good');
			$data['entry_bad'] = $this->language->get('entry_bad');

			$data['button_cart'] = $this->language->get('button_cart');
			$data['button_wishlist'] = $this->language->get('button_wishlist');
			$data['button_compare'] = $this->language->get('button_compare');
			$data['button_upload'] = $this->language->get('button_upload');
			$data['button_continue'] = $this->language->get('button_continue');

			$this->load->model('catalog/review');

			$data['tab_description'] = $this->language->get('tab_description');
			$data['tab_attribute'] = $this->language->get('tab_attribute');
			$data['tab_review'] = sprintf($this->language->get('tab_review'), $product_info['reviews']);

			$data['product_id'] = (int)$this->request->get['product_id'];
			$data['manufacturer'] = $product_info['manufacturer'];
			$data['manufacturers'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id']);
			$data['model'] = $product_info['model'];
			$data['reward'] = $product_info['reward'];
			$data['points'] = $product_info['points'];
			## [m3956:tabs]
			if(isset($product_info['legacy_description']) and false !== strpos($product_info['legacy_description'],'[![DELIMITER]]')){
				$dx = explode('[![DELIMITER]]',$product_info['legacy_description']);
				foreach($dx as &$dxx)
					$dxx = html_entity_decode($dxx, ENT_QUOTES, 'UTF-8');
				#
				$data['description']	= $dx[0];
				$data['xspecs']			= $dx[1];
				$data['xobtainment']	= $dx[2];
			}
			else{
				$data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');
				$data['xspecs'] = $data['xobtainment'] = '';
			}
			## [/m3956:tabs]


			$data['lang'] = array_merge($data, $this->language->load('unishop/unishop'));
			
			$viewed_id = isset($this->request->cookie['viewed_id']) ? explode(',', $this->request->cookie['viewed_id']) : array();
			if (!in_array($product_info['product_id'], $viewed_id)) {
				$viewed_id = isset($this->request->cookie['viewed_id']) ? $this->request->cookie['viewed_id'] : '';
				$viewed_id .= $product_info['product_id'].',';
				setcookie('viewed_id', $viewed_id, time()+86400, '/');
			}
			
			$data['weight'] = $product_info['weight'] > 0 ? $this->weight->format($product_info['weight'], $product_info['weight_class_id'], $this->language->get('decimal_point'), $this->language->get('thousand_point')) : '';
			
			$currency = (VERSION >= 2.2) ? $this->session->data['currency'] : '';
			$data['currency_code'] = (VERSION >= 2.2) ? $this->session->data['currency'] : $this->currency->getCode();
			$data['p_value'] = $this->tax->calculate($product_info['special'] ? $product_info['special'] : $product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax'))*$this->currency->getValue($currency);
			
			$this->load->model('unishop/setting');
			$settings = $this->model_unishop_setting->getSetting();
			$lang_id = $this->config->get('config_language_id');
			
			$data['show_quick_order'] = isset($settings['show_quick_order']) ? $settings['show_quick_order'] : '';
			$data['show_quick_order_text_product'] = isset($settings['show_quick_order_text_product']) ? $settings['show_quick_order_text_product'] : '';
			$data['quick_order_icon'] = isset($settings['show_quick_order']) ? html_entity_decode($settings[$lang_id]['quick_order_icon'], ENT_QUOTES, 'UTF-8') : '';
			$data['quick_order_title'] = isset($settings['show_quick_order']) ? $settings[$lang_id]['quick_order_title'] : '';
			
			$data['show_quick_order_quantity'] = isset($settings['show_quick_order_quantity']) ? $settings['show_quick_order_quantity'] : '';
			
			$data['text_related'] = isset($settings[$lang_id]['title_related']) ? $settings[$lang_id]['title_related'] : $this->language->get('text_related');
			
			$data['quantity'] = $product_info['quantity'];
			
			$data['show_product_attr_group'] = $settings['show_product_attr_group'];
			$data['show_product_attr_item'] = $settings['show_product_attr_item'];
			
			$data['show_product_attr'] = isset($settings['show_product_attr']) ? $settings['show_product_attr'] : '';
			
			$data['cart_btn_disabled'] = $product_info['quantity'] <= 0 && isset($settings['cart_btn_disabled']) ? $settings['cart_btn_disabled'] : '';
			$data['cart_btn_icon_mobile'] = $product_info['quantity'] <= 0 && isset($settings['cart_btn_icon_disabled_mobile']) ? $settings['cart_btn_icon_disabled_mobile'] : '';
			$data['cart_btn_icon'] = $product_info['quantity'] > 0 ? $settings[$lang_id]['cart_btn_icon'] : $settings[$lang_id]['cart_btn_icon_disabled'];
			$data['cart_btn_text'] = $product_info['quantity'] > 0 ? $settings[$lang_id]['cart_btn_text'] : $settings[$lang_id]['cart_btn_text_disabled'];
			
			$data['cart_btn_class'] = $product_info['quantity'] <= 0 ? ' disabled' : '';
			$data['cart_btn_class'] .= $product_info['quantity'] <= 0 && isset($settings['cart_btn_disabled']) ? ' disabled2' : '';
			
			$data['wishlist_btn_disabled'] = isset($settings['wishlist_btn_disabled']) ? $settings['wishlist_btn_disabled'] : '';
			$data['compare_btn_disabled'] = isset($settings['compare_btn_disabled']) ? $settings['compare_btn_disabled'] : '';
			
			$data['sku'] = $product_info['sku'];
			$data['upc'] = $product_info['upc'];
			$data['ean'] = $product_info['ean'];
			$data['jan'] = $product_info['jan'];
			$data['isbn'] = $product_info['isbn'];
			$data['mpn'] = $product_info['mpn'];
			$data['location'] = $product_info['location'];
			
			$data['sku_text'] = $settings[$lang_id]['sku_text'];
			$data['upc_text'] = !isset($settings['upc_as_sticker']) ? $settings[$lang_id]['upc_text'] : '';
			$data['ean_text'] = !isset($settings['ean_as_sticker']) ? $settings[$lang_id]['ean_text'] : '';
			$data['jan_text'] = !isset($settings['jan_as_sticker']) ? $settings[$lang_id]['jan_text'] : '';
			$data['isbn_text'] = !isset($settings['isbn_as_sticker']) ? $settings[$lang_id]['isbn_text'] : '';
			$data['mpn_text'] = !isset($settings['mpn_as_sticker']) ? $settings[$lang_id]['mpn_text'] : '';
			$data['location_text'] = $settings[$lang_id]['location_text'];
			
			$data['show_stock_status'] = isset($settings['show_stock_status']) ? $settings['show_stock_status'] : '';
			$data['stock_status'] = $product_info['stock_status'];
			$data['stock_status_id'] = $product_info['stock_status_id'];
			
			$data['product_banners'] = (isset($settings[$lang_id]['product_banners']) ? $settings[$lang_id]['product_banners'] : array());
			
			$data['show_additional_tab'] = isset($settings['show_additional_tab']) ? $settings['show_additional_tab'] : '';
			$data['additional_tab_icon'] = isset($settings['show_additional_tab']) ? $settings[$lang_id]['additional_tab_icon'] : '';	
			$data['additional_tab_title'] = isset($settings['show_additional_tab']) ? $settings[$lang_id]['additional_tab_title'] : '';	
			$data['additional_tab_text'] = isset($settings['show_additional_tab']) ? html_entity_decode($settings[$lang_id]['additional_tab_text'], ENT_QUOTES, 'UTF-8') : '';
			
			$data['show_manufacturer'] = '';
			if(isset($settings['show_manufacturer'])) {
				
				$data['manufacturer_position'] = (isset($settings['manufacturer_position']) ? $settings['manufacturer_position'] : '');
				$data['manufacturer_title'] = $settings[$lang_id]['manufacturer_title'];
				
				$this->load->model('tool/image');
				$manufacturer_descr = $this->model_catalog_manufacturer->getManufacturer($product_info['manufacturer_id']);
				
				if($manufacturer_descr) {
					$data['show_manufacturer'] = $settings['show_manufacturer'];
				
					if ($manufacturer_descr['image']) {
						$manufacturer_image = $this->model_tool_image->resize($manufacturer_descr['image'], $settings['manufacturer_logo_w'], $settings['manufacturer_logo_h']);
					} else {
						$manufacturer_image = '';
					}
			
					$data['manufacturer_name'] = $manufacturer_descr['name'];
					$data['manufacturer_description'] = isset($manufacturer_descr['description']) ? utf8_substr(strip_tags(html_entity_decode($manufacturer_descr['description'], ENT_QUOTES, 'UTF-8')), 0, $settings['manufacturer_text_length']) : '';
					$data['manufacturer_image'] = $manufacturer_image;
					$data['manufacturer_href'] = $this->url->link('product/manufacturer/info&manufacturer_id=' . $product_info['manufacturer_id']);
				}
			}
			
			$data['show_rating'] = (isset($settings['show_rating']) ? $settings['show_rating'] : '');
			$data['show_rating_count'] = (isset($settings['show_rating_count']) ? $settings['show_rating_count'] : '');
			
			$data['stickers'] = array();
			
			$stickers_data = array(
				'product_id' 	=> $product_info['product_id'],
				'price'			=> $product_info['price'],
				'special'		=> $product_info['special'],
				'tax_class_id'  => $product_info['tax_class_id'],
				'date_available'=> $product_info['date_available'],
				'reward'		=> $product_info['reward'],
				'upc'			=> $product_info['upc'],
				'ean'			=> $product_info['ean'],
				'jan'			=> $product_info['jan'],
				'isbn'			=> $product_info['isbn'],
				'mpn'			=> $product_info['mpn'],
			);
			
			$data['stickers'] = $this->load->controller('unishop/stickers', $stickers_data);
			
			$data['text_description'] = $this->language->get('text_description');
			$data['text_weight'] = $this->language->get('text_weight');
			
			$data['show_plus_minus_review'] = (isset($settings['show_plus_minus_review']) ? $settings['show_plus_minus_review'] : '');
			$data['plus_minus_review_required'] = (isset($settings['plus_minus_review_required']) ? $settings['plus_minus_review_required'] : '');
			
			$data['auto_related'] = $this->load->controller('unishop/auto_related');
			
			if ($product_info['quantity'] <= 0) {
				$data['stock'] = $product_info['stock_status'];
			} elseif ($this->config->get('config_stock_display')) {
				$data['stock'] = $product_info['quantity'];
			} else {
				$data['stock'] = $this->language->get('text_instock');
			}

			$this->load->model('tool/image');

			if ($product_info['image']) {
				$data['popup'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height'));
			} else {
				$data['popup'] = '';
			}

			if ($product_info['image']) {
				$data['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'));
			} else {
				$data['thumb'] = '';
			}


			$data['small'] = $this->model_tool_image->resize($product_info['image'] ? $product_info['image'] : 'no_image.jpg', (VERSION >= 2.2) ? $this->config->get($this->config->get('config_theme') . '_image_additional_width') : $this->config->get('config_image_additional_width'), (VERSION >= 2.2) ? $this->config->get($this->config->get('config_theme') . '_image_additional_height') : $this->config->get('config_image_additional_height'));
			
			$data['images'] = array();

			$results = $this->model_catalog_product->getProductImages($this->request->get['product_id']);

			foreach ($results as $result) {
				$data['images'][] = array(
					'popup' => $this->model_tool_image->resize($result['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height')),

			'small' => $this->model_tool_image->resize($result['image'], (VERSION >= 2.2) ? $this->config->get($this->config->get('config_theme') . '_image_thumb_width') : $this->config->get('config_image_thumb_width'), (VERSION >= 2.2) ? $this->config->get($this->config->get('config_theme') . '_image_thumb_height') : $this->config->get('config_image_thumb_height')),
			'full' => $this->model_tool_image->resize($result['image'], (VERSION >= 2.2) ? $this->config->get($this->config->get('config_theme') . '_image_popup_width') : $this->config->get('config_image_popup_width'), (VERSION >= 2.2) ? $this->config->get($this->config->get('config_theme') . '_image_popup_height') : $this->config->get('config_image_popup_height')),
			
					'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get('config_image_additional_width'), $this->config->get('config_image_additional_height'))
				);
			}

			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
				$data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$data['price'] = false;
			}

			if ((float)$product_info['special']) {
				$data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$data['special'] = false;
			}

			if ($this->config->get('config_tax')) {
				$data['tax'] = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price']);
			} else {
				$data['tax'] = false;
			}

			$discounts = $this->model_catalog_product->getProductDiscounts($this->request->get['product_id']);

			$data['discounts'] = array();

			foreach ($discounts as $discount) {
				$data['discounts'][] = array(
					'quantity' => $discount['quantity'],
					'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')))
				);
			}

			$data['options'] = array();

			foreach ($this->model_catalog_product->getProductOptions($this->request->get['product_id']) as $option) {
				$product_option_value_data = array();

				foreach ($option['product_option_value'] as $option_value) {
					if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
						if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
							$price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false));
						} else {
							$price = false;
						}

						$product_option_value_data[] = array(
							'product_option_value_id' => $option_value['product_option_value_id'],
							'option_value_id'         => $option_value['option_value_id'],
							'name'                    => $option_value['name'],
							'image'                   => $option_value['image'] ? $this->model_tool_image->resize($option_value['image'], 50, 50) : '',

			'small' => $this->model_tool_image->resize($option_value['image'], (VERSION >= 2.2) ? $this->config->get($this->config->get('config_theme') . '_image_thumb_width') : $this->config->get('config_image_thumb_width'), (VERSION >= 2.2) ? $this->config->get($this->config->get('config_theme') . '_image_thumb_height') : $this->config->get('config_image_thumb_height')),
			'full' => $this->model_tool_image->resize($option_value['image'], (VERSION >= 2.2) ? $this->config->get($this->config->get('config_theme') . '_image_popup_width') : $this->config->get('config_image_popup_width'), (VERSION >= 2.2) ? $this->config->get($this->config->get('config_theme') . '_image_popup_height') : $this->config->get('config_image_popup_height')),
			
							'price'                   => $price,
							'price_prefix'            => $option_value['price_prefix']
						);
					}
				}

				$data['options'][] = array(
					'product_option_id'    => $option['product_option_id'],
					'product_option_value' => $product_option_value_data,
					'option_id'            => $option['option_id'],
					'name'                 => $option['name'],
					'type'                 => $option['type'],
					'value'                => $option['value'],
					'required'             => $option['required']
				);
			}

			if ($product_info['minimum']) {
				$data['minimum'] = $product_info['minimum'];
			} else {
				$data['minimum'] = 1;
			}

			$data['review_status'] = $this->config->get('config_review_status');

			if ($this->config->get('config_review_guest') || $this->customer->isLogged()) {
				$data['review_guest'] = true;
			} else {
				$data['review_guest'] = false;
			}

			if ($this->customer->isLogged()) {
				$data['customer_name'] = $this->customer->getFirstName() . '&nbsp;' . $this->customer->getLastName();
			} else {
				$data['customer_name'] = '';
			}

			$data['reviews'] = sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']);
			$data['rating'] = (int)$product_info['rating'];

			// Captcha
			if ($this->config->get($this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
				$data['captcha'] = $this->load->controller('captcha/' . $this->config->get('config_captcha'));
			} else {
				$data['captcha'] = '';
			}

			$data['attribute_groups'] = $this->model_catalog_product->getProductAttributes($this->request->get['product_id']);

			$data['products'] = array();

			$results = $this->model_catalog_product->getProductRelated($this->request->get['product_id']);

			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_related_width'), $this->config->get('config_image_related_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('config_image_related_width'), $this->config->get('config_image_related_height'));
				}

				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$price = false;
				}

				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}


			$result = isset($product_info) && isset($setting) ? $product_info : $result;
			
			if (VERSION >= 2.2) {
				$img_width = isset($this->request->get['product_id']) ? $this->config->get($this->config->get('config_theme') . '_image_related_width') : $this->config->get($this->config->get('config_theme') . '_image_product_width');
				$img_height = isset($this->request->get['product_id']) ? $this->config->get($this->config->get('config_theme') . '_image_related_height') : $this->config->get($this->config->get('config_theme') . '_image_product_height');
			} else {
				$img_width = isset($this->request->get['product_id']) ? $this->config->get('config_image_related_width') : $this->config->get('config_image_product_width');
				$img_height = isset($this->request->get['product_id']) ? $this->config->get('config_image_related_height') : $this->config->get('config_image_product_height');
			}
			
			$img_width = isset($setting['width']) ? $setting['width'] : $img_width;
			$img_height = isset($setting['height']) ? $setting['height'] : $img_height;
			
			$attribute_groups = (isset($settings['show_attr']) ? $this->model_catalog_product->getProductAttributes($result['product_id'], $settings['show_attr_group'], $settings['show_attr_item']) : array());
			
			$options = array();
			if (isset($settings['show_options']) && $settings['show_options_item'] > 0) {				
				foreach ($this->model_catalog_product->getProductOptions($result['product_id'], $settings['show_options_item']) as $option) {
					$product_option_value_data = array();

					foreach ($option['product_option_value'] as $option_value) {
						if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
							if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
								$option_price = $this->currency->format($this->tax->calculate($option_value['price'], $result['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $currency);
							} else {
								$option_price = false;
							}
							
							$product_option_value_data[] = array(
								'product_option_value_id' => $option_value['product_option_value_id'],
								'option_value_id'         => $option_value['option_value_id'],
								'name'                    => $option_value['name'],
								'image'                   => $option_value['image'] ? $this->model_tool_image->resize($option_value['image'], 50, 50) : '',
								'small' 				  => $this->model_tool_image->resize($option_value['image'], $img_width, $img_height),
								'price'                   => $option_price,
								'price_prefix'            => $option_value['price_prefix']
							);
						}
					}

					$options[] = array(
						'product_option_id'    => $option['product_option_id'],
						'product_option_value' => $product_option_value_data,
						'option_id'            => $option['option_id'],
						'name'                 => $option['name'],
						'type'                 => $option['type'],
						'value'                => $option['value'],
						'required'             => $option['required']
					);
				}
			}
			
			$weight = ($result['weight'] > 0) ? $this->weight->format($result['weight'], $result['weight_class_id'], $this->language->get('decimal_point'), $this->language->get('thousand_point')) : '';
			
			$additional_image = '';
			
			if(isset($settings['show_additional_image'])) {
				$results_img = $this->model_catalog_product->getProductImages($result['product_id']);
				foreach ($results_img as $key => $result_img) {
					if ($key == 0) {
						$additional_image = $this->model_tool_image->resize($result_img['image'], $img_width, $img_height);
					}
				}
			}
			
			$stickers = array();
			
			$stickers_data = array(
				'product_id' 	=> $result['product_id'],
				'price'			=> $result['price'],
				'special'		=> $result['special'],
				'tax_class_id'  => $result['tax_class_id'],
				'date_available'=> $result['date_available'],
				'reward'		=> $result['reward'],
				'upc'			=> $result['upc'],
				'ean'			=> $result['ean'],
				'jan'			=> $result['jan'],
				'isbn'			=> $result['isbn'],
				'mpn'			=> $result['mpn'],
			);
			
			$stickers = $this->load->controller('unishop/stickers', $stickers_data);
						
			$cart_btn_class = $result['quantity'] <= 0 ? ' disabled' : '';
			$cart_btn_class .= $result['quantity'] <= 0 && isset($settings['cart_btn_disabled']) ? ' disabled2' : '';
			
				$data['products'][] = array(

			'additional_image'			=> $additional_image,
			'weight' 					=> $weight,
			'num_reviews' 				=> $result['reviews'] ? $result['reviews'] : '',
			'quantity' 					=> $result['quantity'],
			'minimum' 					=> $result['minimum'],
			'stock_status' 				=> isset($settings['show_stock_status']) ? $result['stock_status'] : '',
			'stock_status_id' 			=> isset($settings['show_stock_status']) ? $result['stock_status_id'] : '',
			'stickers' 					=> $stickers,
			'price_value' 				=> $this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax'))*$this->currency->getValue($currency),
			'special_value' 			=> $this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax'))*$this->currency->getValue($currency),
			'attribute_groups' 			=> $attribute_groups,
			'options'					=> $options,
			'weight_value'				=> $result['weight'],
			'weight_unit' 				=> $this->weight->getUnit($result['weight_class_id']),
			'cart_btn_disabled' 		=> $result['quantity'] <= 0 && isset($settings['cart_btn_disabled']) ? $settings['cart_btn_disabled'] : '',
			'cart_btn_icon_mobile' 		=> $result['quantity'] <= 0 && isset($settings['cart_btn_icon_disabled_mobile']) ? $settings['cart_btn_icon_disabled_mobile'] : '',
			'cart_btn_icon' 			=> $result['quantity'] > 0 ? $settings[$lang_id]['cart_btn_icon'] : $settings[$lang_id]['cart_btn_icon_disabled'],
			'cart_btn_text' 			=> $result['quantity'] > 0 ? $settings[$lang_id]['cart_btn_text'] : $settings[$lang_id]['cart_btn_text_disabled'],
			'cart_btn_class' 			=> $cart_btn_class,
			
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('config_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'      => $rating,
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
				);
			}

			$data['tags'] = array();

			if ($product_info['tag']) {
				$tags = explode(',', $product_info['tag']);

				foreach ($tags as $tag) {
					$data['tags'][] = array(
						'tag'  => trim($tag),
						'href' => $this->url->link('product/search', 'tag=' . trim($tag))
					);
				}
			}

			$data['recurrings'] = $this->model_catalog_product->getProfiles($this->request->get['product_id']);

			$this->model_catalog_product->updateViewed($this->request->get['product_id']);

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/product.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/product/product.tpl', $data));
			} else {
				$this->response->setOutput($this->load->view('default/template/product/product.tpl', $data));
			}
		} else {
			$url = '';

			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['manufacturer_id'])) {
				$url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
			}

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('product/product', $url . '&product_id=' . $product_id)
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['heading_title'] = $this->language->get('text_error');

			$data['text_error'] = $this->language->get('text_error');

			$data['button_continue'] = $this->language->get('button_continue');

			$data['continue'] = $this->url->link('common/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/error/not_found.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/error/not_found.tpl', $data));
			} else {
				$this->response->setOutput($this->load->view('default/template/error/not_found.tpl', $data));
			}
		}
	}

	public function review() {

			$this->language->load('unishop/unishop');
			$data['text_admin_reply'] = $this->language->get('text_admin_reply');
			$data['text_plus'] = $this->language->get('text_plus');
			$data['text_minus'] = $this->language->get('text_minus');
			$data['text_comment'] = $this->language->get('text_comment');
			
			$this->load->model('unishop/setting');
			$settings = $this->model_unishop_setting->getSetting();
			
			$data['show_plus_minus_review'] = isset($settings['show_plus_minus_review']) ? $settings['show_plus_minus_review'] : '';
			
		$this->load->language('product/product');

		$this->load->model('catalog/review');

		$data['text_no_reviews'] = $this->language->get('text_no_reviews');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['reviews'] = array();

		$review_total = $this->model_catalog_review->getTotalReviewsByProductId($this->request->get['product_id']);

		$results = $this->model_catalog_review->getReviewsByProductId($this->request->get['product_id'], ($page - 1) * 5, 5);

		foreach ($results as $result) {
			$data['reviews'][] = array(
				'author'     => $result['author'],
				'text'       => nl2br($result['text']),

			'plus'      	=> nl2br($result['plus']),
			'minus'       	=> nl2br($result['minus']),
			'admin_reply'	=> nl2br($result['admin_reply']),
				'rating'     => (int)$result['rating'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$pagination = new Pagination();
		$pagination->total = $review_total;
		$pagination->page = $page;
		$pagination->limit = 5;
		$pagination->url = $this->url->link('product/product/review', 'product_id=' . $this->request->get['product_id'] . '&page={page}');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($review_total) ? (($page - 1) * 5) + 1 : 0, ((($page - 1) * 5) > ($review_total - 5)) ? $review_total : ((($page - 1) * 5) + 5), $review_total, ceil($review_total / 5));

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/review.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/product/review.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/product/review.tpl', $data));
		}
	}

	public function write() {
		$this->load->language('product/product');

		$json = array();

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 25)) {
				$json['error'] = $this->language->get('error_name');
			}


			$this->language->load('unishop/unishop');
			
			$this->load->model('unishop/setting');
			$settings = $this->model_unishop_setting->getSetting();
			
			$data['plus_minus_review_required'] = '';
			if(isset($settings['show_plus_minus_review']) && isset($settings['plus_minus_review_required'])) {
				if ((utf8_strlen($this->request->post['plus']) < 3) || (utf8_strlen($this->request->post['plus']) > 1000)) {
					$json['error'] = $this->language->get('error_plus');
				}
				if ((utf8_strlen($this->request->post['minus']) < 3) || (utf8_strlen($this->request->post['minus']) > 1000)) {
					$json['error'] = $this->language->get('error_minus');
				}
			}
			
			if ((utf8_strlen($this->request->post['text']) < 25) || (utf8_strlen($this->request->post['text']) > 1000)) {
				$json['error'] = $this->language->get('error_text');
			}

			if (empty($this->request->post['rating']) || $this->request->post['rating'] < 0 || $this->request->post['rating'] > 5) {
				$json['error'] = $this->language->get('error_rating');
			}

			// Captcha
			if ($this->config->get($this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
				$captcha = $this->load->controller('captcha/' . $this->config->get('config_captcha') . '/validate');

				if ($captcha) {
					$json['error'] = $captcha;
				}
			}

			if (!isset($json['error'])) {
				$this->load->model('catalog/review');

				$this->model_catalog_review->addReview($this->request->get['product_id'], $this->request->post);

				$json['success'] = $this->language->get('text_success');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function getRecurringDescription() {
		$this->language->load('product/product');
		$this->load->model('catalog/product');

		if (isset($this->request->post['product_id'])) {
			$product_id = $this->request->post['product_id'];
		} else {
			$product_id = 0;
		}

		if (isset($this->request->post['recurring_id'])) {
			$recurring_id = $this->request->post['recurring_id'];
		} else {
			$recurring_id = 0;
		}

		if (isset($this->request->post['quantity'])) {
			$quantity = $this->request->post['quantity'];
		} else {
			$quantity = 1;
		}

		$product_info = $this->model_catalog_product->getProduct($product_id);
		$recurring_info = $this->model_catalog_product->getProfile($product_id, $recurring_id);

		$json = array();

		if ($product_info && $recurring_info) {
			if (!$json) {
				$frequencies = array(
					'day'        => $this->language->get('text_day'),
					'week'       => $this->language->get('text_week'),
					'semi_month' => $this->language->get('text_semi_month'),
					'month'      => $this->language->get('text_month'),
					'year'       => $this->language->get('text_year'),
				);

				if ($recurring_info['trial_status'] == 1) {
					$price = $this->currency->format($this->tax->calculate($recurring_info['trial_price'] * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')));
					$trial_text = sprintf($this->language->get('text_trial_description'), $price, $recurring_info['trial_cycle'], $frequencies[$recurring_info['trial_frequency']], $recurring_info['trial_duration']) . ' ';
				} else {
					$trial_text = '';
				}

				$price = $this->currency->format($this->tax->calculate($recurring_info['price'] * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')));

				if ($recurring_info['duration']) {
					$text = $trial_text . sprintf($this->language->get('text_payment_description'), $price, $recurring_info['cycle'], $frequencies[$recurring_info['frequency']], $recurring_info['duration']);
				} else {
					$text = $trial_text . sprintf($this->language->get('text_payment_cancel'), $price, $recurring_info['cycle'], $frequencies[$recurring_info['frequency']], $recurring_info['duration']);
				}

				$json['success'] = $text;
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
