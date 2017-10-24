<?php
class ControllerShippingByTotalPercent extends Controller {
	private $version = 'ver 1.22';
	private $error = array();

	public function index() {
		$this->load->language('shipping/by_total_percent');
		$this->load->model('setting/setting');
		
		$this->document->setTitle(strip_tags($this->language->get('heading_title')) . ' ' .  $this->version);
		$this->data['heading_title'] = strip_tags($this->language->get('heading_title')) . ' ' .  $this->version;

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->model_setting_setting->editSetting('by_total_percent', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('shipping/by_total_percent', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['text_none'] = $this->language->get('text_none');
		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');

		$this->data['entry_rate_weight'] = $this->language->get('entry_rate_weight');
		$this->data['entry_rate_total_summa'] = $this->language->get('entry_rate_total_summa');
		$this->data['entry_rate_percent'] = $this->language->get('entry_rate_percent');
		$this->data['entry_tax_class'] = $this->language->get('entry_tax_class');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

		$this->data['tab_general'] = $this->language->get('tab_general');

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
	   		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
	  		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
	   		'text'      => $this->language->get('text_shipping'),
			'href'      => $this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL'),
	  		'separator' => ' :: '
   		);

   		$this->data['breadcrumbs'][] = array(
	   		'text'      => strip_tags($this->language->get('heading_title')) . ' ' .  $this->version,
			'href'      => $this->url->link('shipping/by_total_percent', 'token=' . $this->session->data['token'], 'SSL'),
	  		'separator' => ' :: '
   		);

		$this->data['action'] = $this->url->link('shipping/by_total_percent', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['cancel'] = $this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL'); 

		$this->load->model('localisation/geo_zone');

		$geo_zones = $this->model_localisation_geo_zone->getGeoZones();

		foreach ($geo_zones as $geo_zone) {
			if (isset($this->request->post['by_total_percent_' . $geo_zone['geo_zone_id'] . '_rate_weight'])) {
				$this->data['by_total_percent_' . $geo_zone['geo_zone_id'] . '_rate_weight'] = $this->request->post['by_total_percent_' . $geo_zone['geo_zone_id'] . '_rate_weight'];
			} else {
				$this->data['by_total_percent_' . $geo_zone['geo_zone_id'] . '_rate_weight'] = $this->config->get('by_total_percent_' . $geo_zone['geo_zone_id'] . '_rate_weight');
			}
			
			if (isset($this->request->post['by_total_percent_' . $geo_zone['geo_zone_id'] . '_rate_total_summa'])) {
				$this->data['by_total_percent_' . $geo_zone['geo_zone_id'] . '_rate_total_summa'] = $this->request->post['by_total_percent_' . $geo_zone['geo_zone_id'] . '_rate_total_summa'];
			} else {
				$this->data['by_total_percent_' . $geo_zone['geo_zone_id'] . '_rate_total_summa'] = $this->config->get('by_total_percent_' . $geo_zone['geo_zone_id'] . '_rate_total_summa');
			}
			
			if (isset($this->request->post['by_total_percent_' . $geo_zone['geo_zone_id'] . '_rate_percent'])) {
				$this->data['by_total_percent_' . $geo_zone['geo_zone_id'] . '_rate_percent'] = $this->request->post['by_total_percent_' . $geo_zone['geo_zone_id'] . '_rate_percent'];
			} else {
				$this->data['by_total_percent_' . $geo_zone['geo_zone_id'] . '_rate_percent'] = $this->config->get('by_total_percent_' . $geo_zone['geo_zone_id'] . '_rate_percent');
			}

			if (isset($this->request->post['by_total_percent_' . $geo_zone['geo_zone_id'] . '_status'])) {
				$this->data['by_total_percent_' . $geo_zone['geo_zone_id'] . '_status'] = $this->request->post['by_total_percent_' . $geo_zone['geo_zone_id'] . '_status'];
			} else {
				$this->data['by_total_percent_' . $geo_zone['geo_zone_id'] . '_status'] = $this->config->get('by_total_percent_' . $geo_zone['geo_zone_id'] . '_status');
			}
		}

		$this->data['geo_zones'] = $geo_zones;

		if (isset($this->request->post['by_total_percent_tax_class_id'])) {
			$this->data['by_total_percent_tax_class_id'] = $this->request->post['by_total_percent_tax_class_id'];
		} else {
			$this->data['by_total_percent_tax_class_id'] = $this->config->get('by_total_percent_tax_class_id');
		}

		if (isset($this->request->post['by_total_percent_status'])) {
			$this->data['by_total_percent_status'] = $this->request->post['by_total_percent_status'];
		} else {
			$this->data['by_total_percent_status'] = $this->config->get('by_total_percent_status');
		}

		if (isset($this->request->post['by_total_percent_sort_order'])) {
			$this->data['by_total_percent_sort_order'] = $this->request->post['by_total_percent_sort_order'];
		} else {
			$this->data['by_total_percent_sort_order'] = $this->config->get('by_total_percent_sort_order');
		}

		$this->load->model('localisation/tax_class');

		$this->data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		$this->template = 'shipping/by_total_percent.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'shipping/by_total_percent')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
}
?>