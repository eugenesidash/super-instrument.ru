<?php
class ControllerModuleBestSeller extends Controller {
	public function index($setting) {
		$this->load->language('module/bestseller');

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
			

		
			$data['heading_title'] = isset($settings['show_heading_in_admin']) && isset($setting['name']) ? $setting['name'] : $this->language->get('heading_title');
			$module_type_view = isset($settings['module_type_view']) ? $settings['module_type_view'] : array();
			$data['type_view'] = isset($setting['name']) && in_array($setting['name'], $module_type_view) ? 'grid' : 'carousel';
			

		$data['text_tax'] = $this->language->get('text_tax');

		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_wishlist'] = $this->language->get('button_wishlist');
		$data['button_compare'] = $this->language->get('button_compare');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$data['products'] = array();

		$results = $this->model_catalog_product->getBestSellerProducts($setting['limit']);

		if ($results) {
			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
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
					$rating = $result['rating'];
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
					'rating'      => $rating,
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
				);
			}

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/bestseller.tpl')) {
				return $this->load->view($this->config->get('config_template') . '/template/module/bestseller.tpl', $data);
			} else {
				return $this->load->view('default/template/module/bestseller.tpl', $data);
			}
		}
	}
}
