<?php
class ControllerExtensionModuleReviews extends Controller {
    public function index($setting) {
		$setting = $setting['reviews'];
	
        $this->language->load('module/reviews');
        $this->load->model('catalog/product');
        $this->load->model('tool/image');
        $this->load->model('catalog/reviews');
        
        $data['module'] = 'reviews';

        $data['module_header'] = $setting['module_header'][$this->config->get('config_language_id')];

        $data['reviews'] = array();

        $limit = $setting['limit'] > 0 ? $setting['limit'] : 4;

        $text_limit = $setting['text_limit'] > 0 ? $setting['text_limit'] : 50;

        if (isset($setting['category_sensitive']) && !empty($this->request->get['path'])){
            $categories = explode('_', $this->request->get['path']);
            $category_id = (int)array_pop($categories);
        } else {
            $category_id = 0;
        }

        $results = $setting['order_type'] == 'last' ? $this->model_catalog_reviews->getLatestReviews($limit, $category_id) : $this->model_catalog_reviews->getRandomReviews($limit, $category_id);

        foreach ($results as $result) {
            $rating = $this->config->get('config_review_status') ? $result['rating'] : '';

            $product = $this->model_catalog_product->getProduct($result['product_id']);
             
			if ($product['image']) {
				if (VERSION >= 2.2) {
					$thumb = $this->model_tool_image->resize($product['image'], $this->config->get($this->config->get('config_theme') . '_image_product_width'), $this->config->get($this->config->get('config_theme') . '_image_product_height'));
				} else {
					$thumb = $this->model_tool_image->resize($product['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
				}
            } else {
				if (VERSION >= 2.2) {
					$thumb = $this->model_tool_image->resize('placeholder.png', $this->config->get($this->config->get('config_theme') . '_image_product_width'), $this->config->get($this->config->get('config_theme') . '_image_product_height'));
				} else {
					$thumb = $this->model_tool_image->resize('placeholder.png', $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
				}
			}
			
			$link_all_reviews = isset($setting['show_all_button_link']) ? $this->url->link('product/product', 'product_id=' . $product['product_id']).'#tab-review' : $this->url->link('product/reviews');
			
            $data['reviews'][] = array(
				'product_id'  => $product['product_id'],
                'prod_thumb'  => $thumb,
                'prod_name'   => $product['name'],
                'review_id'   => $result['review_id'],
                'rating'      => $rating,
				'description' => utf8_substr(strip_tags(html_entity_decode($result['text'], ENT_QUOTES, 'UTF-8')), 0, $text_limit) . '..',
                'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'href'        => $this->url->link('product/product', 'product_id=' . $product['product_id']),
                'author'      => $result['author'],
                'prod_href'   => $this->url->link('product/product', 'product_id=' . $product['product_id']),
				'link_all_reviews' => $link_all_reviews
            );
			
			
        }
		
        $data['text_all_reviews'] = $this->language->get('text_all_reviews');
		$data['show_all_button'] = isset($setting['show_all_button']) ? $setting['show_all_button'] : '';
		
		if (VERSION >= 2.2) {
			return $this->load->view('extension/module/reviews', $data);
		} else {
			return $this->load->view('unishop/template/extension/module/reviews.tpl', $data);
		}
    }
}