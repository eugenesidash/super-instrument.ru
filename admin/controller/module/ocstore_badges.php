<?php

class ControllerModuleOcstoreBadges extends Controller
{
	private $error = array();
	
	public function install() {
		$this->load->model('ocstore/badges');

		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'shipping/ocstore_badges');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'shipping/ocstore_badges');

		$this->model_ocstore_badges->install();
	}

	public function uninstall() {
		$this->load->model('ocstore/badges');
		$this->model_ocstore_badges->uninstall();
	}

	public function index() {
		$this->load->language('common/menu');
		$text_module = $this->language->get('text_module');

		$this->load->model('extension/modification');
		$this->load->language('module/ocstore_badges');
		$this->load->model('ocstore/badges');

		$flash = $this->model_extension_modification->getModificationByCode("ocstore_flash_data");
		if ($flash === array())
			die($this->language->get('exception_notexists_flash'));

		$this->document->setTitle(strip_tags($this->language->get('heading_title')));

		$data['heading_title'] = $this->language->get('heading_title');
		$data['ocstore_header'] = $this->language->get('ocstore_header');

		foreach ($this->language->all() as $key => $value)
			$data[$key] = $value;
		$data['badges'] = $this->model_ocstore_badges->badges();

		$data['action'] = $this->url->link('module/ocstore_badges', 'token=' . $this->session->data['token'], 'SSL');
		$data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
		$data['link_recache'] = $this->url->link('module/ocstore_badges/recache', 'token=' . $this->session->data['token'], 'SSL');
		$data['link_empty'] = $this->url->link('module/ocstore_badges/clean', 'token=' . $this->session->data['token'], 'SSL');
		$data['success'] = $this->session->flash->get('success');
		$data['errors'] = $this->session->flash->get('errors');
		$data['error'] = $this->session->flash->get('error');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['token'] = $this->session->data['token'];

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $text_module,
			'href' => $this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('module/ocstore_badges', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['link_edit'] = $this->url->link('module/ocstore_badges/edit', 'token=' . $this->session->data['token'], 'SSL');
		$data['link_remove'] = $this->url->link('module/ocstore_badges/remove', 'token=' . $this->session->data['token'], 'SSL');
		$data['link_add'] = $this->url->link('module/ocstore_badges/edit', 'token=' . $this->session->data['token'], 'SSL');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('ocstore/modules/badges/list.tpl', $data));
	}

	public function edit() {
		$this->load->language('common/menu');
		$text_module = $this->language->get('text_module');
		
		$this->load->model('extension/modification');
		$this->load->language('module/ocstore_badges');
		$this->load->model('ocstore/badges');
		$this->load->model('catalog/category');

		$this->document->setTitle(strip_tags($this->language->get('heading_title')));

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateForm()) {
			if (!$this->user->hasPermission('modify', 'module/ocstore_badges')) {
				$this->session->flash->set('error', $this->language->get('error_persimission'));
				$this->response->redirect($this->url->link('module/ocstore_badges', 'token=' . $this->session->data['token'], 'SSL'));
			}

			$infoImg = getimagesize(DIR_IMAGE.$this->request->post['image']);
			$this->request->post['data']['image_width'] = $infoImg[0];
			$this->request->post['data']['image_height'] = $infoImg[1];

			$this->request->post['data'] = json_encode($this->request->post['data']);

			if (isset($this->request->get['id'])) {
				$this->model_ocstore_badges->update($this->request->get['id'], $this->request->post);
				$this->session->flash->set('success', $this->language->get('success_update'));
			} else {
				$this->model_ocstore_badges->save($this->request->post);
				$this->session->flash->set('success', $this->language->get('success_save'));
			}

			
			$this->putJsContents();

			$this->response->redirect($this->url->link('module/ocstore_badges', 'token=' . $this->session->data['token'] , 'SSL'));
		}

		$data = array();

		try {
			if (isset($this->request->get['id'])) {
				$data = $this->model_ocstore_badges->edit($this->request->get['id']);
				$data['data'] = json_decode($data['data'], true);
			}
			else
				$data = array_merge($data, $this->model_ocstore_badges->add());
		} catch (Exception $e) {
			$this->session->flash->set('error', $e);
			$this->response->redirect($this->url->link('shipping/ocstore_badges/points', 'token=' . $this->session->data['token'] . (isset($this->request->get['filter']) ? '&' . http_build_query($this->request->get['filter']) : ""), 'SSL'));
		}

		foreach ($this->language->all() as $key => $value)
			$data[$key] = $value;

		if ($this->session->flash->get('olddata') !== false) {
			foreach ($this->session->flash->get('olddata') as $key => $value)
				$data[$key] = $value;
		}


		$data['heading_title'] = $this->language->get('heading_title');
		$data['ocstore_header'] = $this->language->get('ocstore_header');

		$data['action'] = $this->url->link('module/ocstore_badges/edit', 'token=' . $this->session->data['token'] . (isset($this->request->get['id']) ? "&id=" . $this->request->get['id'] : ""), 'SSL');
		$data['cancel'] = $this->url->link('module/ocstore_badges', 'token=' . $this->session->data['token'], 'SSL');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['token'] = $this->session->data['token'];

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $text_module,
			'href' => $this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('module/ocstore_badges', 'token=' . $this->session->data['token'], 'SSL')
		);

		// add scripts and styles
        $this->document->addScript('view/template/ocstore/js/select2.full.min.js');
        $this->document->addStyle('view/template/ocstore/css/select2.min.css');
		
		$data['allcategories'] = $this->model_catalog_category->getCategories();
		$data['alltags'] = $this->model_ocstore_badges->getAllTags();
		
		$data['errors'] = $this->session->flash->get('errors');
		$data['error'] = $this->session->flash->get('error');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('ocstore/modules/badges/edit.tpl', $data));
	}

	private function validateForm() {
		try {
			$errors = array();

			if (!isset($this->request->post['name']) || mb_strlen(trim($this->request->post['name'])) == 0 || mb_strlen(trim($this->request->post['name'])) > 100)
				$errors["name"] = $this->language->get('error_name');
			if (!isset($this->request->post['image']) || mb_strlen(trim($this->request->post['image'])) == 0 || mb_strlen(trim($this->request->post['image'])) > 100)
				$errors["image"] = $this->language->get('error_image');
			if (!isset($this->request->post['data']['angle']) || (int)$this->request->post['data']['angle'] < 0 || (int)$this->request->post['data']['angle'] >= 360)
				$errors["angle"] = $this->language->get('error_angle');

			if ($errors !== array()) {
				$this->session->flash->set('errors', $errors);
				throw new OcstoreException($this->language->get('exception_validate'), 3);
			}
		} catch (OcstoreException $e) {
			$this->session->flash->set('error', (string)$e);
			$this->session->flash->set('olddata', $this->request->post);
			unset($this->request->get['route']);
			$this->response->redirect($this->url->link('module/ocstore_badges/edit', 'token=' . $this->session->data['token'], 'SSL'));
		}

		return true;
	}

	public function remove() {
		$this->load->model('ocstore/badges');
		$this->load->language('module/ocstore_badges');

		if (!$this->user->hasPermission('modify', 'module/ocstore_badges')) {
			$this->session->flash->set('error', $this->language->get('error_persimission'));
			$this->response->redirect($this->url->link('module/ocstore_badges', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->model_ocstore_badges->remove($this->request->get["id"]);
		$this->session->flash->set('success', $this->language->get('success_remove'));
		$this->response->redirect($this->url->link('module/ocstore_badges', 'token=' . $this->session->data['token'], 'SSL'));
	}
	
	public function cron() {
		echo '!!!!!!!!!!!!!';
	}
	
	public function clean() {
		$this->load->model('ocstore/badges');
		$this->load->language('module/ocstore_badges');

		if (!$this->user->hasPermission('modify', 'module/ocstore_badges')) {
			$this->session->flash->set('error', $this->language->get('error_persimission'));
			$this->response->redirect($this->url->link('module/ocstore_badges', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->model_ocstore_badges->clean();
		$this->putJsContents();
		
		$this->session->flash->set('success', $this->language->get('success_empty'));
		$this->response->redirect($this->url->link('module/ocstore_badges', 'token=' . $this->session->data['token'], 'SSL'));
	}
	
	public function recache() {
		$this->load->model('ocstore/badges');
		$this->load->language('module/ocstore_badges');
		$this->load->model('catalog/product');

		if (!$this->user->hasPermission('modify', 'module/ocstore_badges')) {
			$this->session->flash->set('error', $this->language->get('error_persimission'));
			$this->response->redirect($this->url->link('module/ocstore_badges', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->model_ocstore_badges->cleanAuto();
		foreach ($this->model_ocstore_badges->badges() as $badge) {
			$data = json_decode($badge['data'], true);
			if (isset($data['category']) && !empty($data['category']) && count($data['category']) > 0) {
				foreach ($data['category'] as $c) {
					$products = $this->model_catalog_product->getProductsByCategoryId($c);
					foreach ($products as $p) {
						$this->model_ocstore_badges->addLink($p['product_id'], $badge['id']);
					}
				}
			}
			if (isset($data['tags']) && !empty($data['tags']) && count($data['tags']) > 0) {
				$products = $this->model_ocstore_badges->getProductsByTags($data['tags']);
				if (count($products) > 0)
					foreach ($products as $p)
						$this->model_ocstore_badges->addLink($p['product_id'], $badge['id']);
			}
		}
		
		$this->putJsContents();
		
		$this->session->flash->set('success', $this->language->get('success_update'));
		$this->response->redirect($this->url->link('module/ocstore_badges', 'token=' . $this->session->data['token'], 'SSL'));
	}
	
	private function putJsContents() {
		/* OC-Store: Badges */
		$badges = array();
		foreach ($this->model_ocstore_badges->badges() as $b) {
			$data = json_decode($b['data'], true);
			$data['ballontext'] = isset($data['ballontext'])? htmlspecialchars_decode($data['ballontext']): "";
			$badges[$b['id']] = array("image" => $b['image'], "data" => $data);
		}
		$badges_all = $this->model_ocstore_badges->badges_all();


		file_put_contents(DIR_CATALOG."view/theme/ocstore/js/badges_data.js", "var badges_product = ".json_encode($badges_all)."; var badges = ".json_encode($badges).";");
		/* /OC-Store: Badges */
	}
}

