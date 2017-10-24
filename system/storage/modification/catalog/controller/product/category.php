<?php
class ControllerProductCategory extends Controller {
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
			
		$this->load->language('product/category');

		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		if (isset($this->request->get['filter'])) {
			$filter = $this->request->get['filter'];
		} else {
			$filter = '';
		}

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


		// OCFilter start
    if (isset($this->request->get['filter_ocfilter'])) {
      $filter_ocfilter = $this->request->get['filter_ocfilter'];
    } else {
      $filter_ocfilter = '';
    }
		// OCFilter end
      
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		if (isset($this->request->get['path'])) {
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

			$path = '';

			$parts = explode('_', (string)$this->request->get['path']);

			$category_id = (int)array_pop($parts);

			foreach ($parts as $path_id) {
				if (!$path) {
					$path = (int)$path_id;
				} else {
					$path .= '_' . (int)$path_id;
				}

				$category_info = $this->model_catalog_category->getCategory($path_id);

				if ($category_info) {
					$data['breadcrumbs'][] = array(
						'text' => $category_info['name'],
						'href' => $this->url->link('product/category', 'path=' . $path . $url)
					);
				}
			}
		} else {
			$category_id = 0;
		}

		$category_info = $this->model_catalog_category->getCategory($category_id);

		if ($category_info) {

			if ($category_info['meta_title']) {
				$this->document->setTitle($category_info['meta_title']);
			} else {
				$this->document->setTitle($category_info['name']);
			}

			$this->document->setDescription($category_info['meta_description']);
			$this->document->setKeywords($category_info['meta_keyword']);

			if ($category_info['meta_h1']) {
				$data['heading_title'] = $category_info['meta_h1'];
			} else {
				$data['heading_title'] = $category_info['name'];
			}

			$data['text_refine'] = $this->language->get('text_refine');
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
			$data['button_continue'] = $this->language->get('button_continue');
			$data['button_list'] = $this->language->get('button_list');
			$data['button_grid'] = $this->language->get('button_grid');

			// Set the last category breadcrumb
			$data['breadcrumbs'][] = array(
				'text' => $category_info['name'],
				'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'])
			);

			if ($category_info['image']) {
				$data['thumb'] = $this->model_tool_image->resize($category_info['image'], $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height'));
			} else {
				$data['thumb'] = '';
			}

			$data['description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8');
			$data['compare'] = $this->url->link('product/compare');

			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['categories'] = array();

			$results = $this->model_catalog_category->getCategories($category_id);

			foreach ($results as $result) {
				$filter_data = array(
					'filter_category_id'  => $result['category_id'],
					'filter_sub_category' => true
				);

				$data['categories'][] = array(

			'thumb' => $this->model_tool_image->resize(($result['image'] == '' ? 'no_image.jpg' : $result['image']), $this->config->get((VERSION >= 2.2) ? $this->config->get('config_theme') . '_image_category_width' : 'config_image_category_width'), $this->config->get((VERSION >= 2.2) ? $this->config->get('config_theme') . '_image_category_height' : 'config_image_category_height')),
			
					'name' => $result['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
					'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '_' . $result['category_id'] . $url)
				);
			}

			$data['products'] = array();

			$filter_data = array(
				'filter_category_id' => $category_id,
				'filter_filter'      => $filter,
				'sort'               => $sort,
				'order'              => $order,
				'start'              => ($page - 1) * $limit,
				'limit'              => $limit
			);


  		// OCFilter start
  		$filter_data['filter_ocfilter'] = $filter_ocfilter;
  		// OCFilter end
      
			$product_total = $this->model_catalog_product->getTotalProducts($filter_data);

			$results = $this->model_catalog_product->getProducts($filter_data);

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
					'sku'         => (empty($result['sku'])) ? '' : $this->language->get('text_sku') .' '. $result['sku'],
					'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('config_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'      => $result['rating'],
					'href'        => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $result['product_id'] . $url)
				);
			}

			$url = '';


      // OCFilter start
			if (isset($this->request->get['filter_ocfilter'])) {
				$url .= '&filter_ocfilter=' . $this->request->get['filter_ocfilter'];
			}
      // OCFilter end
      
			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['sorts'] = array();

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_default'),
				'value' => 'p.sort_order-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.sort_order&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_name_asc'),
				'value' => 'pd.name-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=pd.name&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_name_desc'),
				'value' => 'pd.name-DESC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=pd.name&order=DESC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_price_asc'),
				'value' => 'p.price-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.price&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_price_desc'),
				'value' => 'p.price-DESC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.price&order=DESC' . $url)
			);

			if ($this->config->get('config_review_status')) {
				$data['sorts'][] = array(
					'text'  => $this->language->get('text_rating_desc'),
					'value' => 'rating-DESC',
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=rating&order=DESC' . $url)
				);

				$data['sorts'][] = array(
					'text'  => $this->language->get('text_rating_asc'),
					'value' => 'rating-ASC',
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=rating&order=ASC' . $url)
				);
			}

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_model_asc'),
				'value' => 'p.model-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.model&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_model_desc'),
				'value' => 'p.model-DESC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.model&order=DESC' . $url)
			);

			$url = '';


      // OCFilter start
			if (isset($this->request->get['filter_ocfilter'])) {
				$url .= '&filter_ocfilter=' . $this->request->get['filter_ocfilter'];
			}
      // OCFilter end
      
			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

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
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&limit=' . $value)
				);
			}

			$url = '';


      // OCFilter start
			if (isset($this->request->get['filter_ocfilter'])) {
				$url .= '&filter_ocfilter=' . $this->request->get['filter_ocfilter'];
			}
      // OCFilter end
      
			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

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
			$pagination->url = $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&page={page}');

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($product_total - $limit)) ? $product_total : ((($page - 1) * $limit) + $limit), $product_total, ceil($product_total / $limit));

			// http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html
			if ($page == 1) {
			    $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'], 'SSL'), 'canonical');
			} elseif ($page == 2) {
			    $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'], 'SSL'), 'prev');
			} else {
			    $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'] . '&page='. ($page - 1), 'SSL'), 'prev');
			}

			if ($limit && ceil($product_total / $limit) > $page) {
			    $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'] . '&page='. ($page + 1), 'SSL'), 'next');
			}

			$data['sort'] = $sort;
			$data['order'] = $order;
			$data['limit'] = $limit;

      // OCFilter Start
      $ocfilter_page_info = $this->load->controller('module/ocfilter/getPageInfo');

      if ($ocfilter_page_info) {
        $this->document->setTitle($ocfilter_page_info['meta_title']);

        if ($ocfilter_page_info['meta_description']) {
			    $this->document->setDescription($ocfilter_page_info['meta_description']);
        }

        if ($ocfilter_page_info['meta_keyword']) {
			    $this->document->setKeywords($ocfilter_page_info['meta_keyword']);
        }

			  $data['heading_title'] = $ocfilter_page_info['title'];

        if ($ocfilter_page_info['description'] && !isset($this->request->get['page']) && !isset($this->request->get['sort']) && !isset($this->request->get['order']) && !isset($this->request->get['search']) && !isset($this->request->get['limit'])) {
        	$data['description'] = html_entity_decode($ocfilter_page_info['description'], ENT_QUOTES, 'UTF-8');
        }
      } else {
        $meta_title = $this->document->getTitle();
        $meta_description = $this->document->getDescription();
        $meta_keyword = $this->document->getKeywords();

        $filter_title = $this->load->controller('module/ocfilter/getSelectedsFilterTitle');

        if ($filter_title) {
          if (false !== strpos($meta_title, '{filter}')) {
            $meta_title = trim(str_replace('{filter}', $filter_title, $meta_title));
          } else {
            $meta_title .= ' ' . $filter_title;
          }

          $this->document->setTitle($meta_title);

          if ($meta_description) {
            if (false !== strpos($meta_description, '{filter}')) {
              $meta_description = trim(str_replace('{filter}', $filter_title, $meta_description));
            } else {
              $meta_description .= ' ' . $filter_title;
            }

  			    $this->document->setDescription($meta_description);
          }

          if ($meta_keyword) {
            if (false !== strpos($meta_keyword, '{filter}')) {
              $meta_keyword = trim(str_replace('{filter}', $filter_title, $meta_keyword));
            } else {
              $meta_keyword .= ' ' . $filter_title;
            }

           	$this->document->setKeywords($meta_keyword);
          }

          $heading_title = $data['heading_title'];

          if (false !== strpos($heading_title, '{filter}')) {
            $heading_title = trim(str_replace('{filter}', $filter_title, $heading_title));
          } else {
            $heading_title .= ' ' . $filter_title;
          }

          $data['heading_title'] = $heading_title;

          $data['description'] = '';
        } else {
          $this->document->setTitle(trim(str_replace('{filter}', '', $meta_title)));
          $this->document->setDescription(trim(str_replace('{filter}', '', $meta_description)));
          $this->document->setKeywords(trim(str_replace('{filter}', '', $meta_keyword)));

          $data['heading_title'] = trim(str_replace('{filter}', '', $data['heading_title']));
        }
      }
      // OCFilter End
      

			$data['continue'] = $this->url->link('common/home');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/category.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/product/category.tpl', $data));
			} else {
				$this->response->setOutput($this->load->view('default/template/product/category.tpl', $data));
			}
		} else {
			$url = '';

			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
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
				'href' => $this->url->link('product/category', $url)
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
}
