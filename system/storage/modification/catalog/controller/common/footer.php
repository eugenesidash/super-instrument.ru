<?php
class ControllerCommonFooter extends Controller {
	public function index() {
		$this->load->language('common/footer');

		$data['scripts'] = $this->document->getScripts('footer');

		$data['text_information'] = $this->language->get('text_information');
		$data['text_service'] = $this->language->get('text_service');
		$data['text_extra'] = $this->language->get('text_extra');
		$data['text_contact'] = $this->language->get('text_contact');
		$data['text_return'] = $this->language->get('text_return');
		$data['text_sitemap'] = $this->language->get('text_sitemap');
		$data['text_manufacturer'] = $this->language->get('text_manufacturer');
		$data['text_voucher'] = $this->language->get('text_voucher');
		$data['text_affiliate'] = $this->language->get('text_affiliate');
		$data['text_special'] = $this->language->get('text_special');
		$data['text_account'] = $this->language->get('text_account');
		$data['text_order'] = $this->language->get('text_order');
		$data['text_wishlist'] = $this->language->get('text_wishlist');
		$data['text_newsletter'] = $this->language->get('text_newsletter');

			$this->load->model('unishop/setting');
			$settings = $this->model_unishop_setting->getSetting();
			$lang_id = $this->config->get('config_language_id');
			$data['lang'] = array_merge($data, $this->language->load('unishop/unishop'));
			
			$this->document->addScript('catalog/view/javascript/jquery/owl-carousel/owl.carousel.min.js');
			$pagespeed = isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'Page Speed') !== false) ? true : false;
			
			$data['phone'] = $this->config->get('config_telephone');
			$data['address'] = $this->config->get('config_address');
			
			$data['show_fly_menu'] = isset($settings['show_fly_menu']) ? $settings['show_fly_menu'] : '';
			$data['fly_menu_product'] = isset($settings['fly_menu_product']) ? $settings['fly_menu_product'] : '';
			$data['show_fly_cart'] = isset($settings['show_fly_cart']) ? $settings['show_fly_cart'] : '';
			$data['show_fly_callback'] = isset($settings['show_fly_callback']) ? $settings['show_fly_callback'] : '';
			$data['fly_callback_text'] = isset($settings['show_fly_callback']) ? $settings[$lang_id]['fly_callback_text'] : '';
			
			$data['column_icon1'] = $settings[$lang_id]['footer_column_icon1'];
			$data['column_icon2'] = $settings[$lang_id]['footer_column_icon2'];
			$data['column_icon3'] = $settings[$lang_id]['footer_column_icon3'];
			$data['column_icon4'] = $settings[$lang_id]['footer_column_icon4'];
			
			$data['column_heading1'] = $settings[$lang_id]['footer_column_heading1'];
			$data['column_heading2'] = $settings[$lang_id]['footer_column_heading2'];
			$data['column_heading3'] = $settings[$lang_id]['footer_column_heading3'];
			$data['column_heading4'] = $settings[$lang_id]['footer_column_heading4'];
			
			$data['footerlinks'] = (isset($settings[$lang_id]['footerlinks']) ? $settings[$lang_id]['footerlinks'] : array());
			
			$data['footer_text'] = html_entity_decode($settings[$lang_id]['footer_text'], ENT_QUOTES, 'UTF-8');
			$data['footer_map'] = !$pagespeed ? html_entity_decode($settings['footer_map'], ENT_QUOTES, 'UTF-8') : '';
			
			$data['socials'] = (isset($settings['socials']) ? $settings['socials'] : array());
			
			$data['payment_icons'] = (isset($settings['payment_icons']) ? $settings['payment_icons'] : array());
			
			$data['change_opt_img'] = isset($settings['change_opt_img']) ? $settings['change_opt_img'] : '';
			$data['change_opt_img_p'] = isset($settings['change_opt_img_p']) ? $settings['change_opt_img_p'] : '';
			
			$data['show_search'] = isset($settings['show_search']) ? $settings['show_search'] : '';
			$data['show_search_image'] = isset($settings['show_search_image']) ? $settings['show_search_image'] : '';
			$data['show_search_description'] = isset($settings['show_search_description']) ? $settings['show_search_description'] : '';
			$data['show_search_rating'] = isset($settings['show_search_rating']) ? $settings['show_search_rating'] : '';
			$data['show_search_price'] = isset($settings['show_search_price']) ? $settings['show_search_price'] : '';
			$data['search_limit'] = isset($settings['search_limit']) ? $settings['search_limit'] : '';
			
			$this->load->model('extension/extension');
			$data['analytics'] = array();
			$analytics = !$pagespeed ? $this->model_extension_extension->getExtensions('analytics') : array();
			foreach ($analytics as $analytic) {
				if ($this->config->get($analytic['code'] . '_status')) {
					if(VERSION >= 2.3) {
						$data['analytics'][] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get($analytic['code'] . '_status'));
					} else {
						$data['analytics'][] = $this->load->controller('analytics/' . $analytic['code']);
					}
				}
			}
			

		$this->load->model('catalog/information');

		$data['informations'] = array();

		foreach ($this->model_catalog_information->getInformations() as $result) {
			if ($result['bottom']) {
				$data['informations'][] = array(
					'title' => $result['title'],
					'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
				);
			}
		}

		$data['contact'] = $this->url->link('information/contact');
		$data['return'] = $this->url->link('account/return/add', '', 'SSL');
		$data['sitemap'] = $this->url->link('information/sitemap');
		$data['manufacturer'] = $this->url->link('product/manufacturer');
		$data['voucher'] = $this->url->link('account/voucher', '', 'SSL');
		$data['affiliate'] = $this->url->link('affiliate/account', '', 'SSL');
		$data['special'] = $this->url->link('product/special');
		$data['account'] = $this->url->link('account/account', '', 'SSL');
		$data['order'] = $this->url->link('account/order', '', 'SSL');
		$data['wishlist'] = $this->url->link('account/wishlist', '', 'SSL');
		$data['newsletter'] = $this->url->link('account/newsletter', '', 'SSL');

		$data['powered'] = sprintf($this->language->get('text_powered'), $this->config->get('config_name'), date('Y', time()));

		// Whos Online
		if ($this->config->get('config_customer_online')) {
			$this->load->model('tool/online');

			if (isset($this->request->server['REMOTE_ADDR'])) {
				$ip = $this->request->server['REMOTE_ADDR'];
			} else {
				$ip = '';
			}

			if (isset($this->request->server['HTTP_HOST']) && isset($this->request->server['REQUEST_URI'])) {
				$url = 'http://' . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
			} else {
				$url = '';
			}

			if (isset($this->request->server['HTTP_REFERER'])) {
				$referer = $this->request->server['HTTP_REFERER'];
			} else {
				$referer = '';
			}

			$this->model_tool_online->addOnline($ip, $this->customer->getId(), $url, $referer);
		}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/footer.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/common/footer.tpl', $data);
		} else {
			return $this->load->view('default/template/common/footer.tpl', $data);
		}
	}
}
