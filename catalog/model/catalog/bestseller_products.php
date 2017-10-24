<?php
class ModelCatalogBestSellerProducts extends Model {
 
	public function getBestSellerProducts($product_id) {
		if(in_array($product_id, $this->getAllBestseller())) {
			return true;
		} else {
			return false;
		}
	}
 
	protected function getAllBestseller() {
		$result = $this->cache->get('unishop.sticker.bestseller');
		if(!$result) {
			$query = $this->db->query("SELECT op.product_id, SUM(op.quantity) AS total FROM " . DB_PREFIX . "order_product op LEFT JOIN " . DB_PREFIX . "product p ON (op.product_id = p.product_id) LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id) WHERE o.order_status_id > '0' AND p.date_available <= NOW() AND p.status = '1' GROUP BY op.product_id");
			$result = array();
			$this->load->model('unishop/setting');
			$settings = $this->model_unishop_setting->getSetting();
			foreach($query->rows as $product) {
				if((int)$product['total'] > $settings['sticker_bestseller_item']) {
					$result[] = $product['product_id'];
				}
			}
			$this->cache->set('unishop.sticker.bestseller', $result);
		}
		return $result;
	}
}
?>