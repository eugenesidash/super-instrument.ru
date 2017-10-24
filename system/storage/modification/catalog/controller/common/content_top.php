<?php
class ControllerCommonContentTop extends Controller {
	public function index() {
		$this->load->model('design/layout');

		if (isset($this->request->get['route'])) {
			$route = (string)$this->request->get['route'];
		} else {
			$route = 'common/home';
		}

		$layout_id = 0;

		if ($route == 'product/category' && isset($this->request->get['path'])) {
			$this->load->model('catalog/category');

			$path = explode('_', (string)$this->request->get['path']);

			$layout_id = $this->model_catalog_category->getCategoryLayoutId(end($path));
		}

		if ($route == 'product/product' && isset($this->request->get['product_id'])) {
			$this->load->model('catalog/product');

			$layout_id = $this->model_catalog_product->getProductLayoutId($this->request->get['product_id']);
		}

		if ($route == 'information/information' && isset($this->request->get['information_id'])) {
			$this->load->model('catalog/information');

			$layout_id = $this->model_catalog_information->getInformationLayoutId($this->request->get['information_id']);
		}

		if (!$layout_id) {
			$layout_id = $this->model_design_layout->getLayout($route);
		}

		if (!$layout_id) {
			$layout_id = $this->config->get('config_layout_id');
		}

		$this->load->model('extension/module');


			//Для совместимости с 2.1
			$uni_modules = glob(DIR_APPLICATION . 'controller/extension/module/*.php', GLOB_BRACE);	
			
			$ext = array();
			foreach ($uni_modules as $uni_module) {
				$ext[] = basename($uni_module, '.php');
			}
			
		$data['modules'] = array();

		$modules = $this->model_design_layout->getLayoutModules($layout_id, 'content_top');

		foreach ($modules as $module) {
			$part = explode('.', $module['code']);

			if (isset($part[0]) && $this->config->get($part[0] . '_status')) {
				$data['modules'][] = in_array($part[0], $ext) ? $this->load->controller('extension/module/' . $part[0]) : $this->load->controller('module/' . $part[0]);
			}

			if (isset($part[1])) {
				$setting_info = $this->model_extension_module->getModule($part[1]);

				if ($setting_info && $setting_info['status']) {
					$data['modules'][] = in_array($part[0], $ext) ? $this->load->controller('extension/module/' . $part[0], $setting_info) : $this->load->controller('module/' . $part[0], $setting_info);
				}
			}
		}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/content_top.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/common/content_top.tpl', $data);
		} else {
			return $this->load->view('default/template/common/content_top.tpl', $data);
		}
	}
}