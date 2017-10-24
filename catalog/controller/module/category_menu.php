<?php
class ControllerModuleCategoryMenu extends Controller {

	protected function index($settings) {
		if (!$this->cache_data) {
			$this->cache_data = array();
		}

		if($settings['style'] == 1){
			$tpl_name = "category_menu";
			$this->document->addScript('catalog/view/javascript/category_menu.js');
		}else{
			$tpl_name = "category_menu_vertical";
		}

		if (file_exists('catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/'.$tpl_name.'.css')) {
			$this->document->addStyle('catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/'.$tpl_name.'.css');
		} else {
			$this->document->addStyle('catalog/view/theme/default/stylesheet/'.$tpl_name.'.css');
		}


		$this->language->load('module/category_menu');
		$this->load->model('tool/image');

    	$this->data['heading_title'] = $this->language->get('heading_title');

		$this->load->model('catalog/category');
		$this->load->model('catalog/product');

		$this->data['categories'] = array();

		$categories = $this->model_catalog_category->getCategories(0);

		foreach ($categories as $category) {
					$this->cache_data['count'][$category['category_id']] = $total;
				}else{
			}

			$children_data = array();
            if( $settings['level'] > 1){
				$children = $this->model_catalog_category->getCategories($category['category_id']);
				foreach ($children as $child) {
					if($settings['child_count'] == 1){
							$data = array(
								'filter_category_id'  => $child['category_id'],
								'filter_sub_category' => true
							);
							$product_total = $this->model_catalog_product->getTotalProducts($data);
							$this->cache_data['count'][$child['category_id']] = $product_total;
						}else{
							$product_total = $this->cache_data['count'][$child['category_id']];
						}
					}

					$subchildren_data = array();
					if($settings['level'] > 2){
						$subchildren = $this->model_catalog_category->getCategories($child['category_id']);
						foreach ($subchildren as $subchild) {
							if($settings['subchild_count'] == 1){
									$subdata = array(
										'filter_category_id'  => $subchild['category_id'],
										'filter_sub_category' => true
									);
									$subproduct_total = $this->model_catalog_product->getTotalProducts($subdata);
									$this->cache_data['count'][$subchild['category_id']] = $subproduct_total;
								}else{
									$subproduct_total = $this->cache_data['count'][$subchild['category_id']];
								}
							}

							$subchildren_data[] = array(
								'category_id' => $subchild['category_id'],
								'name'  => $subchild['name'] . ($settings['subchild_count'] ? ' (' . $subproduct_total . ')' : ''),
								'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'] . '_' . $subchild['category_id']),
								'image' => ($settings['subchild_images']['status'] ? $this->model_tool_image->resize($subchild['image'], $settings['subchild_images']['width'], $settings['subchild_images']['height']) : false)
							);
						}
					}

					$children_data[] = array(
						'category_id' => $child['category_id'],
						'name'        => $child['name'] . ($settings['child_count'] ? ' (' . $product_total . ')' : ''),
						'href'        => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id']),
						'image'     => ($settings['child_images']['status'] ? $this->model_tool_image->resize($child['image'], $settings['child_images']['width'], $settings['child_images']['height']) : false),
						'subchildren' => $subchildren_data,
						'column'   => $child['column'] ? $child['column'] : 1,
					);
				}
			}

			$this->data['categories'][] = array(
				'category_id' => $category['category_id'],
				'name'        => $category['name'] . ($settings['category_count'] ? ' (' . $total . ')' : ''),
				'href'        => $this->url->link('product/category', 'path=' . $category['category_id']),
				'image'		  => ($settings['category_images']['status'] ? $this->model_tool_image->resize($category['image'], $settings['category_images']['width'], $settings['category_images']['height']) : false),
				'children'    => $children_data,
				'column'   => $category['column'] ? $category['column'] : 1,
			);
		}

		if($this->cache_data){

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/'.$tpl_name.'.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/'.$tpl_name.'.tpl';
		} else {
			$this->template = 'default/template/module/'.$tpl_name.'.tpl';
		}

		$this->render();
  	}

}
?>