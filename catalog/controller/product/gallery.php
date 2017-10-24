<?php  
class ControllerProductGallery extends Controller {
	public function index() {
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
	
		$this->load->model('catalog/gallery');
		$this->load->model('tool/image');
		
		$this->language->load('product/gallery');
		
		$this->document->setTitle($this->language->get('heading_title'));
		$data['heading_title'] = $this->language->get('heading_title');
		
		$this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');
		$this->document->addStyle('catalog/view/theme/unishop/stylesheet/gallery.css');
		
		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('product/gallery'),
   		);
	
		$gallerys = $this->model_catalog_gallery->getGallerys();
		
		if($gallerys) {
			foreach ($gallerys as $gallery) {
				$images = array();
		
				$results = $this->model_catalog_gallery->getGallery($gallery['gallery_id']);
		
				foreach ($results as $result) {
					if (file_exists(DIR_IMAGE . $result['image'])) {
						$images[] = array(
							'title' => $result['title'],
							'link'  => $result['link'],
							'image' => $this->model_tool_image->resize($result['image'], 320, 240),
							'popup' => $this->model_tool_image->resize($result['image'], 800, 600)
						);
					}
				}
		
				$data['gallerys'][] = array(
					'name' 		=> $gallery['name'],
					'images'    => $images,
				);
			}
				
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');
			
			if (VERSION >= 2.2) {
				$this->response->setOutput($this->load->view('product/uni_gallery', $data));
			} else {
				$this->response->setOutput($this->load->view('unishop/template/product/uni_gallery.tpl', $data));
			}
		} else {
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
			
			if (VERSION >= 2.2) {
				$this->response->setOutput($this->load->view('error/not_found', $data));
			} else {
				$this->response->setOutput($this->load->view('unishop/template/error/not_found.tpl', $data));
			}
		}
	}
}
?>