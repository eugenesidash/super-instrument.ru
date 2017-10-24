<?php
class ControllerCommonHeader extends Controller {
	public function index() {
		// Analytics
		$this->load->model('extension/extension');

		$data['analytics'] = array();

		$analytics = $this->model_extension_extension->getExtensions('analytics');

		foreach ($analytics as $analytic) {
			if ($this->config->get($analytic['code'] . '_status')) {
				$data['analytics'][] = $this->load->controller('analytics/' . $analytic['code']);
			}
		}

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
		}

		$data['title'] = $this->document->getTitle();

		$data['base'] = $server;
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts();

    // OCFilter start
    $data['noindex'] = $this->document->isNoindex();
    // OCFilter end
      
		$data['lang'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');

		$data['name'] = $this->config->get('config_name');

		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}

		$this->load->language('common/header');
		$data['og_url'] = (isset($this->request->server['HTTPS']) ? HTTPS_SERVER : HTTP_SERVER) . substr($this->request->server['REQUEST_URI'], 1, (strlen($this->request->server['REQUEST_URI'])-1));
		$data['og_image'] = $this->document->getOgImage();

		$data['text_home'] = $this->language->get('text_home');

		// Wishlist
		if ($this->customer->isLogged()) {
			$this->load->model('account/wishlist');

			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());
		} else {
			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
		}

		$data['text_shopping_cart'] = $this->language->get('text_shopping_cart');
		$data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', 'SSL'), $this->customer->getFirstName(), $this->url->link('account/logout', '', 'SSL'));

		$data['text_account'] = $this->language->get('text_account');
		$data['text_register'] = $this->language->get('text_register');
		$data['text_login'] = $this->language->get('text_login');
		$data['text_order'] = $this->language->get('text_order');
		$data['text_transaction'] = $this->language->get('text_transaction');
		$data['text_download'] = $this->language->get('text_download');
		$data['text_logout'] = $this->language->get('text_logout');
		$data['text_checkout'] = $this->language->get('text_checkout');
		$data['text_page'] = $this->language->get('text_page');
		$data['text_category'] = $this->language->get('text_category');
		$data['text_all'] = $this->language->get('text_all');

		$data['home'] = $this->url->link('common/home');
		$data['wishlist'] = $this->url->link('account/wishlist', '', 'SSL');
		$data['logged'] = $this->customer->isLogged();
		$data['account'] = $this->url->link('account/account', '', 'SSL');
		$data['register'] = $this->url->link('account/register', '', 'SSL');
		$data['login'] = $this->url->link('account/login', '', 'SSL');
		$data['order'] = $this->url->link('account/order', '', 'SSL');
		$data['transaction'] = $this->url->link('account/transaction', '', 'SSL');
		$data['download'] = $this->url->link('account/download', '', 'SSL');
		$data['logout'] = $this->url->link('account/logout', '', 'SSL');
		$data['shopping_cart'] = $this->url->link('checkout/cart');
		$data['checkout'] = $this->url->link('checkout/checkout', '', 'SSL');
		$data['contact'] = $this->url->link('information/contact');
		$data['telephone'] = $this->config->get('config_telephone');

			$data['store_id'] = $this->config->get('config_store_id');
			
			$data['lang_1'] = array_merge($data, $this->language->load('unishop/unishop'));
			
			$data['contact'] = $this->url->link('information/contact');
			$data['route'] = isset($this->request->get['route']) ? $this->request->get['route'] : '';
			
			if($this->customer->isLogged()) {
				$this->load->model('account/customer');
				$customer = $this->model_account_customer->getCustomer($this->customer->getId());
				$data['customer_name'] = $customer['firstname'];
			}
			
			$this->load->model('unishop/setting');
			$settings = $this->model_unishop_setting->getSetting();
			$lang_id = $this->config->get('config_language_id');
			
			$data['show_meta_robots'] = isset($settings['show_meta_robots']) ? $settings['show_meta_robots'] : '';
			$data['default_view'] = isset($settings['default_view']) ? $settings['default_view'] : 'grid';
			
			$data['custom_style'] = isset($settings['custom_style']) ? $settings['custom_style'] : '';
			$data['background_image'] = isset($settings['background_image']) && $settings['background_image'] != '' ? $settings['background_image'] : '';
			$data['user_css'] = isset($settings['user_css']) ? html_entity_decode($settings['user_css'], ENT_QUOTES, 'UTF-8') : '';
			$data['user_js'] = isset($settings['user_js']) ? html_entity_decode($settings['user_js'], ENT_QUOTES, 'UTF-8') : '';

			$data['show_login'] = (isset($settings['show_login']) ? $settings['show_login'] : '');
			$data['show_register'] = (isset($settings['show_register']) ? $settings['show_register'] : '');
			$data['menu_type'] = (isset($settings['menu_type']) ? $settings['menu_type'] : '');
			$data['menu_schema'] = (isset($settings['menu_schema']) ? $settings['menu_schema'] : array());
			$data['headerlinks'] = (isset($settings[$lang_id]['headerlinks']) ? $settings[$lang_id]['headerlinks'] : array());
			$data['headerlinks2'] = isset($settings[$lang_id]['headerlinks2']) ? $settings[$lang_id]['headerlinks2'] : array();
			$data['callback'] = isset($settings['show_callback']) ? $settings['show_callback'] : '';
			$data['phone1'] = $this->config->get('config_telephone');
			$data['phones'] = isset($settings['phones']) ? $settings['phones'] : array();
			
			$text_in_add_contacts = isset($settings[$lang_id]['text_in_add_contacts']) ? html_entity_decode($settings[$lang_id]['text_in_add_contacts'], ENT_QUOTES, 'UTF-8') : '';
			$data['text_in_add_contacts'] = str_replace('<p><br></p>', '', $text_in_add_contacts) != '' ? $text_in_add_contacts : '';

			$data['text_in_add_contacts_position'] = (isset($settings['text_in_add_contacts_position']) ? $settings['text_in_add_contacts_position'] : '');
			
			$data['fax'] = $this->config->get('config_fax');
			$data['mail'] = $this->config->get('config_email');
			$data['delivery_hours'] = isset($settings[$lang_id]['delivery_hours']) ? $settings[$lang_id]['delivery_hours'] : '';
			$data['text_menu'] = isset($settings[$lang_id]['text_menu']) ? $settings[$lang_id]['text_menu'] : '';
			
			$data['menu_links_disabled'] = isset($settings['menu_links_disabled']) ? $settings['menu_links_disabled'] : '';
			$cat_id = isset($this->request->get['path']) ? explode('_', (string)$this->request->get['path']) : array();
			$data['category_id'] = (int)array_pop($cat_id);
			

		$status = true;

		if (isset($this->request->server['HTTP_USER_AGENT'])) {
			$robots = explode("\n", str_replace(array("\r\n", "\r"), "\n", trim($this->config->get('config_robots'))));

			foreach ($robots as $robot) {
				if ($robot && strpos($this->request->server['HTTP_USER_AGENT'], trim($robot)) !== false) {
					$status = false;

					break;
				}
			}
		}

		// Menu
		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$data['categories'] = array();

		$categories = $this->model_catalog_category->getCategories(0);

		foreach ($categories as $category) {
			if ($category['top']) {
				// Level 2
				$children_data = array();

				$children = $this->model_catalog_category->getCategories($category['category_id']);

				
			foreach ($children as $child) {
				$filter_data = array(
					'filter_category_id'  => $child['category_id'],
					'filter_sub_category' => true
				);
			
				$children2 = $this->model_catalog_category->getCategories($child['category_id']);
				$children_data2 = array();
			
				foreach ($children2 as $child2) {
					if($child2['top']) {
						$children_data2[] = array(
							'name'  		=> $child2['name'],
							'category_id'	=> $child2['category_id'],
							'href'  		=> $this->url->link('product/category', 'path='.$category['category_id'].'_'.$child['category_id'].'_'.$child2['category_id']),
						);
					}
				}
			
				if($child['top']) {
					$children_data[] = array(
						'name'       	=> $child['name'],
						'category_id'	=> $child['category_id'],
						'children'   	=> $children_data2,
						'href'        	=> $this->url->link('product/category', 'path=' . $category['category_id'].'_'.$child['category_id']),
					);
				}
			
				}

				// Level 1
				$data['categories'][] = array(
					'name'     => $category['name'],
	
			'category_id' => $category['category_id'],
			
					'children' => $children_data,
					'column'   => $category['column'] ? $category['column'] : 1,
					'href'     => $this->url->link('product/category', 'path=' . $category['category_id'])
				);
			}
		}

		$data['geoip'] = $this->load->controller('module/geoip');
		$data['language'] = $this->load->controller('common/language');
		$data['currency'] = $this->load->controller('common/currency');
		$data['search'] = $this->load->controller('common/search');
		$data['cart'] = $this->load->controller('common/cart');

		// For page specific css
		if (isset($this->request->get['route'])) {
			if (isset($this->request->get['product_id'])) {
				$class = '-' . $this->request->get['product_id'];
			} elseif (isset($this->request->get['path'])) {
				$class = '-' . $this->request->get['path'];
			} elseif (isset($this->request->get['manufacturer_id'])) {
				$class = '-' . $this->request->get['manufacturer_id'];
			} else {
				$class = '';
			}

			$data['class'] = str_replace('/', '-', $this->request->get['route']) . $class;
		} else {
			$data['class'] = 'common-home';
		}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/header.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/common/header.tpl', $data);
		} else {
			return $this->load->view('default/template/common/header.tpl', $data);
		}
	}
}
