<?php
class ControllerProductSpecial extends Controller {
	public function index() {


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
			
			$data['menu_schema'] = (isset($settings['menu_schema']) ? $settings['menu_schema'] : array());
			$data['subcategory_column'] = isset($settings['subcategory_column']) ? implode(' ', $settings['subcategory_column']) : '';
			
			$data['show_grid_button'] = isset($settings['show_grid_button']) ? $settings['show_grid_button'] : '';
			$data['show_list_button'] = isset($settings['show_list_button']) ? $settings['show_list_button'] : '';
			$data['show_compact_button'] = isset($settings['show_compact_button']) ? $settings['show_compact_button'] : '';
			
		$this->load->language('product/special');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'p.sort_order';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['limit'])) {
			$limit = (int)$this->request->get['limit'];
		} else {
			$limit = $this->config->get('config_product_limit');
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
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

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('product/special', $url)
		);

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_empty'] = $this->language->get('text_empty');
		$data['text_quantity'] = $this->language->get('text_quantity');
		$data['text_manufacturer'] = $this->language->get('text_manufacturer');
		$data['text_model'] = $this->language->get('text_model');
		$data['text_price'] = $this->language->get('text_price');
		$data['text_tax'] = $this->language->get('text_tax');
		$data['text_points'] = $this->language->get('text_points');
		$data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));
		$data['text_sort'] = $this->language->get('text_sort');
		$data['text_limit'] = $this->language->get('text_limit');

		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_wishlist'] = $this->language->get('button_wishlist');
		$data['button_compare'] = $this->language->get('button_compare');
		$data['button_list'] = $this->language->get('button_list');
		$data['button_grid'] = $this->language->get('button_grid');
		$data['button_continue'] = $this->language->get('button_continue');

		$data['compare'] = $this->url->link('product/compare');

		$data['products'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $limit,
			'limit' => $limit
		);

		$product_total = $this->model_catalog_product->getTotalProductSpecials();

		$results = $this->model_catalog_product->getProductSpecials($filter_data);

		foreach ($results as $result) {
			if ($result['image']) {
				$image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
			} else {
				$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
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
				'rating'      => $result['rating'],
				'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'] . $url)
			);
		}

		$url = '';

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}

		$data['sorts'] = array();

		$data['sorts'][] = array(
			'text'  => $this->language->get('text_default'),
			'value' => 'p.sort_order-ASC',
			'href'  => $this->url->link('product/special', 'sort=p.sort_order&order=ASC' . $url)
		);

		$data['sorts'][] = array(
			'text'  => $this->language->get('text_name_asc'),
			'value' => 'pd.name-ASC',
			'href'  => $this->url->link('product/special', 'sort=pd.name&order=ASC' . $url)
		);

		$data['sorts'][] = array(
			'text'  => $this->language->get('text_name_desc'),
			'value' => 'pd.name-DESC',
			'href'  => $this->url->link('product/special', 'sort=pd.name&order=DESC' . $url)
		);

		$data['sorts'][] = array(
			'text'  => $this->language->get('text_price_asc'),
			'value' => 'ps.price-ASC',
			'href'  => $this->url->link('product/special', 'sort=ps.price&order=ASC' . $url)
		);

		$data['sorts'][] = array(
			'text'  => $this->language->get('text_price_desc'),
			'value' => 'ps.price-DESC',
			'href'  => $this->url->link('product/special', 'sort=ps.price&order=DESC' . $url)
		);

		if ($this->config->get('config_review_status')) {
			$data['sorts'][] = array(
				'text'  => $this->language->get('text_rating_desc'),
				'value' => 'rating-DESC',
				'href'  => $this->url->link('product/special', 'sort=rating&order=DESC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_rating_asc'),
				'value' => 'rating-ASC',
				'href'  => $this->url->link('product/special', 'sort=rating&order=ASC' . $url)
			);
		}

		$data['sorts'][] = array(
				'text'  => $this->language->get('text_model_asc'),
				'value' => 'p.model-ASC',
				'href'  => $this->url->link('product/special', 'sort=p.model&order=ASC' . $url)
		);

		$data['sorts'][] = array(
			'text'  => $this->language->get('text_model_desc'),
			'value' => 'p.model-DESC',
			'href'  => $this->url->link('product/special', 'sort=p.model&order=DESC' . $url)
		);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$data['limits'] = array();

		$limits = array_unique(array($this->config->get('config_product_limit'), 25, 50, 75, 100));

		sort($limits);

		foreach($limits as $value) {
			$data['limits'][] = array(
				'text'  => $value,
				'value' => $value,
				'href'  => $this->url->link('product/special', $url . '&limit=' . $value)
			);
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}

		$pagination = new Pagination();
		$pagination->total = $product_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('product/special', $url . '&page={page}');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($product_total - $limit)) ? $product_total : ((($page - 1) * $limit) + $limit), $product_total, ceil($product_total / $limit));

		// http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html
		if ($page == 1) {
		    $this->document->addLink($this->url->link('product/special', '', 'SSL'), 'canonical');
		} elseif ($page == 2) {
		    $this->document->addLink($this->url->link('product/special', '', 'SSL'), 'prev');
		} else {
		    $this->document->addLink($this->url->link('product/special', 'page='. ($page - 1), 'SSL'), 'prev');
		}

		if ($limit && ceil($product_total / $limit) > $page) {
		    $this->document->addLink($this->url->link('product/special', 'page='. ($page + 1), 'SSL'), 'next');
		}

		$data['sort'] = $sort;
		$data['order'] = $order;
		$data['limit'] = $limit;

		$data['continue'] = $this->url->link('common/home');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/special.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/product/special.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/product/special.tpl', $data));
		}
	}
}
