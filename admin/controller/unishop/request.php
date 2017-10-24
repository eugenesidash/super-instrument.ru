<?php
class ControllerUniShopRequest extends Controller {
	private $error = false;

	public function index() {
		$this->load->language('unishop/request');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('unishop/request');

		$this->getList();
		$this->check();
	}

	public function edit() {
		$this->load->language('unishop/request');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('unishop/request');

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateDelete()) {
			$this->model_unishop_request->editRequest($this->request->get['request_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_type=' . urlencode(html_entity_decode($this->request->get['filter_type'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status']) && $this->request->get['filter_status'] != '') {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('unishop/request', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('unishop/request');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('unishop/request');

		if (isset($this->request->get['request_id']) && $this->validateDelete()) {
			//foreach ($this->request->post['selected'] as $request_id) {
				//$this->model_blog_request->deleterequest($request_id);
			//}
			
			$this->model_unishop_request->deleteRequest($this->request->get['request_id']);

			$this->session->data['success'] = $this->language->get('text_success_delete');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_author'])) {
				$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('unishop/request', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function getList() {
		$this->load->language('unishop/request');
	
		if (isset($this->request->get['filter_type'])) {
			$filter_type = $this->request->get['filter_type'];
		} else {
			$filter_type = '';
		}

		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}
		
		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = '';
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = '';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'date_added';
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_type=' . urlencode(html_entity_decode($this->request->get['filter_type'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('unishop/request', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$data['add'] = $this->url->link('unishop/request/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('unishop/request/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['requests'] = array();

		$filter_data = array(
			'filter_type'    => $filter_type,
			'filter_name'     => $filter_name,
			'filter_status'     => $filter_status,
			'filter_date_added' => $filter_date_added,
			'sort'              => $sort,
			'order'             => $order,
			'start'             => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'             => $this->config->get('config_limit_admin')
		);

		$request_total = $this->model_unishop_request->getTotalRequests($filter_data);

		$results = $this->model_unishop_request->getRequests($filter_data);
		
		$data['types'] = $this->model_unishop_request->getStatuses();

		foreach ($results as $result) {
			
			if($result['status'] == 1) {
				$status = $this->language->get('text_status_1');	
			} else if($result['status'] == 2) {
				$status = $this->language->get('text_status_2');
			} else if($result['status'] == 3) {
				$status = $this->language->get('text_status_3');
			}
			
			$product_name = '';
			$product_href = '';
			
			if($result['product_id']) {
				$this->load->model('catalog/product');
				$product_info = $this->model_catalog_product->getProduct($result['product_id']);
				$product_name = $product_info['name'];
				$product_href = HTTPS_CATALOG.'catalog/product&product_id=' . $result['product_id'];
			}
		
			$data['requests'][] = array(
				'request_id'  => $result['request_id'],
				'type'       => $result['type'],
				'name'     => $result['name'],
				'phone'     => $result['phone'],
				'mail'     => $result['mail'],
				'product_name'     => $product_name,
				'product_href'     => $product_href,
				'comment'     =>  nl2br($result['comment']),
				'admin_comment'     => $result['admin_comment'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'date_modified' => date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
				'status'     => $status,
				'edit'       => $this->url->link('unishop/request/edit', 'token=' . $this->session->data['token'] . '&request_id=' . $result['request_id'] . $url, 'SSL'),
				'delete'       => $this->url->link('unishop/request/delete', 'token=' . $this->session->data['token'] . '&request_id=' . $result['request_id'] . $url, 'SSL')
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_status_1'] = $this->language->get('text_status_1');
		$data['text_status_2'] = $this->language->get('text_status_2');
		$data['text_status_3'] = $this->language->get('text_status_3');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['column_type'] = $this->language->get('column_type');
		$data['column_name'] = $this->language->get('column_name');
		$data['column_phone'] = $this->language->get('column_phone');
		$data['column_mail'] = $this->language->get('column_mail');
		$data['column_comment'] = $this->language->get('column_comment');
		$data['column_admin_comment'] = $this->language->get('column_admin_comment');
		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_date_modified'] = $this->language->get('column_date_modified');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_action'] = $this->language->get('column_action');

		$data['entry_type'] = $this->language->get('entry_type');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_rating'] = $this->language->get('entry_rating');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_date_added'] = $this->language->get('entry_date_added');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_filter'] = $this->language->get('button_filter');

		$data['token'] = $this->session->data['token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_type'] = $this->url->link('unishop/request', 'token=' . $this->session->data['token'] . '&sort=type' . $url, 'SSL');
		$data['sort_name'] = $this->url->link('unishop/request', 'token=' . $this->session->data['token'] . '&sort=name' . $url, 'SSL');
		$data['sort_phone'] = $this->url->link('unishop/request', 'token=' . $this->session->data['token'] . '&sort=phone' . $url, 'SSL');
		$data['sort_mail'] = $this->url->link('unishop/request', 'token=' . $this->session->data['token'] . '&sort=mail' . $url, 'SSL');
		$data['sort_date_added'] = $this->url->link('unishop/request', 'token=' . $this->session->data['token'] . '&sort=date_added' . $url, 'SSL');
		$data['sort_date_modified'] = $this->url->link('unishop/request', 'token=' . $this->session->data['token'] . '&sort=date_modified' . $url, 'SSL');
		$data['sort_status'] = $this->url->link('unishop/request', 'token=' . $this->session->data['token'] . '&sort=status' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['filter_type'])) {
			$url .= '&filter_type=' . urlencode(html_entity_decode($this->request->get['filter_type'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $request_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('unishop/request', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($request_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($request_total - $this->config->get('config_limit_admin'))) ? $request_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $request_total, ceil($request_total / $this->config->get('config_limit_admin')));

		$data['filter_type'] = $filter_type;
		$data['filter_name'] = $filter_name;
		$data['filter_status'] = $filter_status;
		$data['filter_date_added'] = $filter_date_added;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('unishop/request_list.tpl', $data));
		
		$this->model_unishop_request->Install();
	}

	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['request_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		
		$data['text_status_1'] = $this->language->get('text_status_1');
		$data['text_status_2'] = $this->language->get('text_status_2');
		$data['text_status_3'] = $this->language->get('text_status_3');

		$data['entry_type'] = $this->language->get('entry_type');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_phone'] = $this->language->get('entry_phone');
		$data['entry_mail'] = $this->language->get('entry_mail');
		$data['entry_product'] = $this->language->get('entry_product');
		$data['entry_comment'] = $this->language->get('entry_comment');
		$data['entry_admin_comment'] = $this->language->get('entry_admin_comment');
		$data['entry_status'] = $this->language->get('entry_status');

		$data['help_article'] = $this->language->get('help_article');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['article'])) {
			$data['error_article'] = $this->error['article'];
		} else {
			$data['error_article'] = '';
		}

		if (isset($this->error['author'])) {
			$data['error_author'] = $this->error['author'];
		} else {
			$data['error_author'] = '';
		}

		if (isset($this->error['text'])) {
			$data['error_text'] = $this->error['text'];
		} else {
			$data['error_text'] = '';
		}

		if (isset($this->error['rating'])) {
			$data['error_rating'] = $this->error['rating'];
		} else {
			$data['error_rating'] = '';
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_author'])) {
			$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('unishop/request', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		//if (!isset($this->request->get['request_id'])) {
			//$data['action'] = $this->url->link('unishop/request/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		//} else {
			$data['action'] = $this->url->link('unishop/request/edit', 'token=' . $this->session->data['token'] . '&request_id=' . $this->request->get['request_id'] . $url, 'SSL');
		//}

		$data['cancel'] = $this->url->link('unishop/request', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['request_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$request_info = $this->model_unishop_request->getRequest($this->request->get['request_id']);
		}
		
		//print_r($request_info);

		$data['token'] = $this->session->data['token'];

		if (isset($this->request->post['type'])) {
			$data['type'] = $this->request->post['type'];
		} elseif (!empty($request_info)) {
			$data['type'] = $request_info['type'];
		} else {
			$data['type'] = '';
		}

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($request_info)) {
			$data['name'] = $request_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($this->request->post['phone'])) {
			$data['phone'] = $this->request->post['phone'];
		} elseif (!empty($request_info)) {
			$data['phone'] = $request_info['phone'];
		} else {
			$data['phone'] = '';
		}
		
		if (isset($this->request->post['mail'])) {
			$data['mail'] = $this->request->post['mail'];
		} elseif (!empty($request_info)) {
			$data['mail'] = $request_info['mail'];
		} else {
			$data['mail'] = '';
		}
		
		$data['product_id'] = '';
		if(isset($request_info)) {
			$data['product_id'] = $request_info['product_id'];
		}
		
		$data['product_name'] = '';
		$data['product_href'] = '';
		if($data['product_id']) {
			$this->load->model('catalog/product');
			$product_info = $this->model_catalog_product->getProduct($request_info['product_id']);
			$data['product_name'] = $product_info['name'];
			$data['product_href'] = HTTPS_CATALOG.'catalog/product&product_id=' . $request_info['product_id'];
		}

		if (isset($this->request->post['comment'])) {
			$data['comment'] = html_entity_decode($this->request->post['comment']);
		} elseif (!empty($request_info)) {
			$data['comment'] = html_entity_decode($request_info['comment']);
		} else {
			$data['comment'] = '';
		}
		
		if (isset($this->request->post['admin_comment'])) {
			$data['admin_comment'] = $this->request->post['admin_comment'];
		} elseif (!empty($request_info)) {
			$data['admin_comment'] = $request_info['admin_comment'];
		} else {
			$data['admin_comment'] = '';
		}
		
		if (isset($this->request->post['date_added'])) {
			$data['date_added'] = $this->request->post['date_added'];
		} elseif (!empty($request_info)) {
			$data['date_added'] = $request_info['date_added'];
		} else {
			$data['date_added'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($request_info)) {
			$data['status'] = $request_info['status'];
		} else {
			$data['status'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('unishop/request_form.tpl', $data));
	}
	
	public function check() {
		$this->load->model('unishop/request');
		$this->load->model('catalog/product');
		
		$filter_data = array();
		
		$comment = $this->language->get('text_comment');
		$status = 3;
		
		$results = $this->model_unishop_request->getRequests($filter_data);
		
		if($results) {
			foreach($results as $result) {
				if($result['product_id'] > 0 && $result['type'] == $this->language->get('text_notify') && $result['mail'] !='' && $result['status'] != $status) {
					$products = $this->model_catalog_product->getProduct($result['product_id']);
					if($products['quantity'] > 0) {
						$this->model_unishop_request->setComment($comment, $result['request_id']);
						$this->model_unishop_request->setStatus($status, $result['request_id']);
						$this->send($result['mail'], $products['name'], $products['product_id']);
					}
				}
			}
		}
	}
	
	protected function send($customer_mail, $product_id, $product_name) {
		$this->load->language('unishop/request');

		$subject = sprintf($this->language->get('text_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));

		$message = sprintf($this->language->get('text_message'), html_entity_decode($product_name, ENT_QUOTES, 'UTF-8'), $product_id)."\n";
		//$message .= html_entity_decode($data['text'], ENT_QUOTES, 'UTF-8') . "\n\n";

		$mail = new Mail();
		$mail->protocol = $this->config->get('config_mail_protocol');
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
		$mail->smtp_username = $this->config->get('config_mail_smtp_username');
		$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
		$mail->smtp_port = $this->config->get('config_mail_smtp_port');
		$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

		$mail->setTo($customer_mail);
		//$mail->setTo($this->config->get('config_email'));
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
		$mail->setSubject($subject);
		$mail->setHtml(html_entity_decode($message));
		$mail->send();
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'unishop/request')) {
			//$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['article_id']) {
			//$this->error['article'] = $this->language->get('error_article');
		}

		if ((utf8_strlen($this->request->post['author']) < 3) || (utf8_strlen($this->request->post['author']) > 64)) {
			//$this->error['author'] = $this->language->get('error_author');
		}

		if (utf8_strlen($this->request->post['text']) < 1) {
			//$this->error['text'] = $this->language->get('error_text');
		}

		if (!isset($this->request->post['rating']) || $this->request->post['rating'] < 0 || $this->request->post['rating'] > 5) {
			//$this->error['rating'] = $this->language->get('error_rating');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'unishop/request')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

}