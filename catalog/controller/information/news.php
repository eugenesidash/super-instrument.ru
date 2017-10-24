<?php
class ControllerInformationNews extends Controller {

	public function index() {
		$data = array();
		
		$this->load->model('unishop/setting');
		$settings = $this->model_unishop_setting->getSetting();
		$language_id = $this->config->get('config_language_id');
		
		$data['route'] = ''; 
		if(isset($this->request->get['route'])) {
			$data['route'] = $this->request->get['route']; 
		}
		
		$data['menu_schema'] = array();
		if (isset($settings['menu_schema'])) {
			$data['menu_schema'] = $settings['menu_schema'];
		}
		
		$this->language->load('information/news');

		$this->load->model('catalog/news');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'href'		=> $this->url->link('common/home'),
			'text'		=> $this->language->get('text_home')
		);

		if (isset($this->request->get['news_id'])) {
			$news_id = $this->request->get['news_id'];
		} else {
			$news_id = 0;
		}

		$news_info = $this->model_catalog_news->getNewsStory($news_id);

		if ($news_info) {

			$data['breadcrumbs'][] = array(
				'text'		=> $this->language->get('heading_title'),
				'href'		=> $this->url->link('information/news'),
			);

			$data['breadcrumbs'][] = array(
				'text'		=> $news_info['title'],
				'href'		=> $this->url->link('information/news', 'news_id=' . $this->request->get['news_id']),
			);

			$this->document->setTitle($news_info['title']);
			$this->document->setDescription($news_info['meta_description']);
			$this->document->addLink($this->url->link('information/news', 'news_id=' . $this->request->get['news_id']), 'canonical');
			
			$this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
			$this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');

     		$data['news_info'] = $news_info;

     		$data['heading_title'] = $news_info['title'];

			$data['description'] = html_entity_decode($news_info['description']);

			$data['viewed'] = sprintf($this->language->get('text_viewed'), $news_info['viewed']);

			$data['min_height'] = $this->config->get('news_thumb_height');

			$this->load->model('tool/image');
			
			$settings = $this->config->get('news');
			
			$data['addthis'] = isset($settings['addthis']) ? $settings['addthis'] : '';

			$data['image'] = isset($settings['image']) ? $settings['image'] : '';

			$data['thumb'] = $this->model_tool_image->resize($news_info['image'], $settings['thumb_width'], $settings['thumb_height']);
			$data['popup'] = $this->model_tool_image->resize($news_info['image'], $settings['popup_width'], $settings['popup_height']);

     		$data['button_news'] = $this->language->get('button_news');
			$data['button_continue'] = $this->language->get('button_continue');

			$data['news'] = $this->url->link('information/news');
			$data['continue'] = $this->url->link('common/home');

			$this->model_catalog_news->updateViewed($this->request->get['news_id']);
			
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');
			
			if (VERSION >= 2.2) {
				$this->response->setOutput($this->load->view('information/news', $data));
			} else {
				$this->response->setOutput($this->load->view('unishop/template/information/news.tpl', $data));
			}

	  	} else {
			$url = '';
			
			if (isset($this->request->get['page'])) {
				$page = $this->request->get['page'];
				$url .= '&page=' . $this->request->get['page'];
			} else { 
				$page = 1;
			}
				
			//$limit = $this->config->get('config_catalog_limit');
			
			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else { 
				$limit = $this->config->get('config_product_limit');
			}
		
			$filter_data = array(
				'page' => $page,
				'limit' => $limit,
				'start' => $limit * ($page - 1),
			);
		
			$total = $this->model_catalog_news->getTotalNews();
		
	  		$news_data = $this->model_catalog_news->getNews($filter_data);
			
			$new_news_data = $news_data;
			
	  		if ($new_news_data) {
				$this->document->setTitle($this->language->get('heading_title'));

				$data['breadcrumbs'][] = array(
					'text'		=> $this->language->get('heading_title'),
					'href'		=> $this->url->link('information/news')
				);

				$data['heading_title'] = $this->language->get('heading_title');

				$data['text_more'] = $this->language->get('text_more');
				$data['text_posted'] = $this->language->get('text_posted');
				
				$settings = $this->config->get('news');
				
				$this->load->model('tool/image');

				foreach ($new_news_data as $result) {
					if ($result['image']) {
						$image = $this->model_tool_image->resize($result['image'], $settings['thumb_width'], $settings['thumb_height']);
					} else {
						$image = '';
					}
				
					$data['news_data'][] = array(
						'id'  				=> $result['news_id'],
						'image'  			=> $image,
						'title'				=> $result['title'],
						'description'		=> utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $settings['chars']) . '..',
						'href'				=> $this->url->link('information/news', 'news_id=' . $result['news_id']),
						'posted'			=> date($this->language->get('date_format_short'), strtotime($result['date_added']))
					);
				}

				$data['button_continue'] = $this->language->get('button_continue');
				$data['continue'] = $this->url->link('common/home');
				$data['template'] = $this->config->get('config_template');
				
				$pagination = new Pagination();
				$pagination->total = $total;
				$pagination->page = $page;
				$pagination->limit = $limit;
				$pagination->text = $this->language->get('text_pagination');
				$pagination->url = $this->url->link('information/news', $url . '&page={page}', 'SSL');
				$data['pagination'] = $pagination->render();

				$data['column_left'] = $this->load->controller('common/column_left');
				$data['column_right'] = $this->load->controller('common/column_right');
				$data['content_top'] = $this->load->controller('common/content_top');
				$data['content_bottom'] = $this->load->controller('common/content_bottom');
				$data['footer'] = $this->load->controller('common/footer');
				$data['header'] = $this->load->controller('common/header');
				
				if (VERSION >= 2.2) {
					$this->response->setOutput($this->load->view('information/news', $data));
				} else {
					$this->response->setOutput($this->load->view('unishop/template/information/news.tpl', $data));
				}

			} else {
		  		$this->document->setTitle($this->language->get('text_error'));

	     		$data['breadcrumbs'] = array();

				$data['breadcrumbs'][] = array(
					'href'		=> $this->url->link('common/home'),
					'text'		=> $this->language->get('text_home')
				);
				
				$data['heading_title'] = $this->language->get('text_error');
				$data['text_error'] = $this->language->get('text_error');
				$data['button_continue'] = $this->language->get('button_continue');
				$data['continue'] = $this->url->link('common/home');
				$data['template'] = $this->config->get('config_template');
				$data['column_left'] = $this->load->controller('common/column_left');
				$data['column_right'] = $this->load->controller('common/column_right');
				$data['content_top'] = $this->load->controller('common/content_top');
				$data['content_bottom'] = $this->load->controller('common/content_bottom');
				$data['footer'] = $this->load->controller('common/footer');
				$data['header'] = $this->load->controller('common/header');
				
				if (VERSION >= 2.2) {
					$this->response->setOutput($this->load->view('error/not_found', $data));
				} else {
					if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/error/not_found.tpl')) {
						$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/error/not_found.tpl', $data));
					} else {
						$this->response->setOutput($this->load->view('default/template/error/not_found.tpl', $data));
					}
				}
		  	}
		}
	}
}
?>