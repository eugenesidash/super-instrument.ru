<?php
class ControllerPaymentCod1 extends Controller {
	public function index() {
		$data['button_confirm'] = $this->language->get('button_confirm');

		$data['text_loading'] = $this->language->get('text_loading');

		$data['continue'] = $this->url->link('checkout/success');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/cod_1.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/payment/cod_1.tpl', $data);
		} else {
			return $this->load->view('default/template/payment/cod_1.tpl', $data);
		}
	}

	public function confirm() {
		if ($this->session->data['payment_method']['code'] == 'cod_1') {
			$this->load->model('checkout/order');

			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('cod_1_order_status_id'));
		}
	}
}