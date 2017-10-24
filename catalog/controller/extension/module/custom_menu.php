<?php  
class ControllerExtensionModuleCustomMenu extends Controller {
	public function index($setting) {
	
		$this->load->model('module/custom_menu');
		
		$this->document->addStyle('catalog/view/theme/unishop/stylesheet/custom_menu.css');
		
		$data['custom_menu'] = $this->model_module_custom_menu->getCustomMenu();
		
		$language_id = $this->config->get('config_language_id');
		$data['head'] = $setting['custom_menu_module'][$language_id]['head'];
		$data['style'] = $setting['custom_menu_module']['style'];
		if(isset($setting['custom_menu_module']['in_module'])) {
			$data['in_module'] = $setting['custom_menu_module']['in_module'];
		} else {
			$data['in_module'] = array();
		}
		
		if (VERSION >= 2.2) {
			return $this->load->view('extension/module/custom_menu', $data);
		} else {
			return $this->load->view('unishop/template/extension/module/custom_menu.tpl', $data);
		}
  	}

}
?>