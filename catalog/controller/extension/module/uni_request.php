<?php  
class ControllerExtensionModuleUniRequest extends Controller {
	public function index() {
	
		$data = array();
		$data['lang'] = array_merge($data, $this->language->load('unishop/unishop'));
	
		$this->load->model('unishop/setting');
		$settings = $this->model_unishop_setting->getSetting();
		$language_id = $this->config->get('config_language_id');
		
		$data['product_id'] = isset($this->request->get['id']) && $this->request->get['id'] != '' ? $this->request->get['id'] : '';
		
		$data['name_text'] = $settings[$language_id]['callback_name_text'];
		$data['phone_text'] = $settings[$language_id]['callback_phone_text'];
		$data['mail_text'] = $settings[$language_id]['callback_mail_text'];
		$data['comment_text'] = $settings[$language_id]['callback_comment_text'];
		
		$data['reason'] = isset($this->request->get['reason']) && $this->request->get['reason'] != '' ? $this->request->get['reason'] : '';
		$data['phone_mask'] = isset($settings['callback_phone_mask']) ? $settings['callback_phone_mask'] : '';
		
		$data['show_reason1'] = isset($settings['show_reason1']) ? $settings['show_reason1'] : '';
		$data['text_reason1'] = $settings[$language_id]['text_reason1'];
		$data['show_reason2'] = isset($settings['show_reason2']) ? $settings['show_reason2'] : '';
		$data['text_reason2'] = $settings[$language_id]['text_reason2'];
		$data['show_reason3'] = isset($settings['show_reason3']) ? $settings['show_reason3'] : '';
		$data['text_reason3'] = $settings[$language_id]['text_reason3'];
		
		if (VERSION >= 2.2) {
			$this->response->setOutput($this->load->view('extension/module/uni_request', $data));
		} else {	
			$this->response->setOutput($this->load->view('unishop/template/extension/module/uni_request.tpl', $data));
		}
  	}
	
	public function mail() {
		$this->language->load('module/uni_request');
		$this->load->model('extension/extension');
		
		$type = isset($this->request->post['customer_reason']) ? htmlspecialchars(strip_tags($this->request->post['customer_reason'])) : htmlspecialchars(strip_tags($this->request->post['type']));
		
		$product_id = '';
		if (isset($this->request->post['product_id'])) {
			$product_id = (int)$this->request->post['product_id'];
			$this->load->model('catalog/product');
			$product_info = $this->model_catalog_product->getProduct($product_id);
		}
		
		$customer_name = isset($this->request->post['customer_name']) ? htmlspecialchars(strip_tags($this->request->post['customer_name'])) : '';
		$customer_phone = isset($this->request->post['customer_phone']) ? htmlspecialchars(strip_tags($this->request->post['customer_phone'])) : '';
		$customer_mail = isset($this->request->post['customer_mail']) ? htmlspecialchars(strip_tags($this->request->post['customer_mail'])) : '';
		$customer_comment = isset($this->request->post['customer_comment']) ? htmlspecialchars(strip_tags($this->request->post['customer_comment'])) : '';
		
		$json = array();
		
		$json['error'] = '';
		
		if (utf8_strlen($customer_name) < 3 || utf8_strlen($customer_name) > 25) {
			$json['error'] .= $this->language->get('text_error_name');
		}
		
		if (utf8_strlen($customer_phone) < 3 || utf8_strlen($customer_phone) > 25) {
			$json['error'] .= $this->language->get('text_error_phone');
		}

		$mail_error = false;
		
		if ($customer_mail && $this->config->get('config_mail_regexp')) {
			$mail_error = !preg_match($this->config->get('config_mail_regexp'), $this->request->post['customer_mail']) ? true : false;
		}
		
		if (isset($this->request->post['customer_mail']) && utf8_strlen($customer_mail) < 3 || utf8_strlen($customer_mail) > 35 || $mail_error) {
			$json['error'] .= $this->language->get('text_error_mail');
		}
		
		if(!$json['error']) {
			$text = '';
			$text .= $product_id ? $this->language->get('text_product').$product_info['name'].'<br />' : '';
			$text .= $this->language->get('text_name').$customer_name.'<br />';
			$text .= $this->language->get('text_phone').$customer_phone.'<br />';
			$text .= $this->language->get('text_mail').$customer_mail.'<br />';
			$text .= $this->language->get('text_comment').$customer_comment.'<br />';
		
			$subject = $type && $product_id ? sprintf($this->language->get('text_reason'), $type, $product_info['name']) : sprintf($this->language->get('text_reason2'), $type);
		
			$mail = new Mail(); 
			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
		
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
		
			$mail->setTo($this->config->get('config_email'));
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender($this->config->get('config_name'));
			$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
			$mail->setHtml(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
		
			$mail->send();
			
			$emails = (VERSION >= 2.2) ? explode(',', $this->config->get('config_mail_alert_email')) : explode(',', $this->config->get('config_mail_alert'));
			foreach ($emails as $email) {
				if ($email && preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $email)) {
					$mail->setTo($email);
					$mail->send();
				}
			}
				
			$json['success'] = $this->language->get('text_success');
		}
		
		$this->response->setOutput(json_encode($json));
	}
}
?>