<?php
class ControllerCommonSearch extends Controller {
	public function index() {
		$this->load->language('common/search');

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
			

			$data = array();
			$data['lang'] = array_merge($data, $this->language->load('unishop/unishop'));
			
			$this->load->model('unishop/setting');
			$settings = $this->model_unishop_setting->getSetting();
			$lang_id = $this->config->get('config_language_id');
			
			$phrase_array = explode(",", isset($settings[$lang_id]['search_phrase']) ? $settings[$lang_id]['search_phrase'] : '');
			$data['search_phrase'] = $phrase_array ? $phrase_array[array_rand($phrase_array)] : '';
			
			$this->load->model('catalog/product');
			$data['categories'] = array();
			$categories = $this->model_catalog_category->getCategories(0);
			foreach ($categories as $category) {
				$data['categories'][] = array(
					'category_id' => $category['category_id'],
					'name'        => $category['name'],
				);
			}
			

		$data['text_search'] = $this->language->get('text_search');

		if (isset($this->request->get['search'])) {
			$data['search'] = $this->request->get['search'];
		} else {
			$data['search'] = '';
		}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/search.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/common/search.tpl', $data);
		} else {
			return $this->load->view('default/template/common/search.tpl', $data);
		}
	}
}