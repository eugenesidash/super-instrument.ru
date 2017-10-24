<?php
class ModelUnishopRelated extends Model {	
	public function getAutoRelated($product_id, $limit, $stock) {
		$product_data = $stock ? $this->cache->get('unishop.autorelated.stock.'.(int)$product_id) : $this->cache->get('unishop.autorelated.'.(int)$product_id);
		
		if(!$product_data) {
			$product_data = array();
		
			$category = $this->db->query("SELECT category_id FROM ".DB_PREFIX . "product_to_category WHERE product_id = '".(int)$product_id."'");
			$category_id = isset($category->row['category_id']) ? $category->row['category_id'] : '';

			$sql = "SELECT p.product_id FROM ".DB_PREFIX."product p LEFT JOIN ".DB_PREFIX."product_to_category p2c ON (p.product_id = p2c.product_id) WHERE p2c.category_id = '".(int)$category_id."' AND p.status = '1'";
			$stock ? $sql .=" AND p.quantity >= '1'" : '';
			$sql .=" AND p.date_available <= NOW() AND p.product_id > '".(int)$product_id."' ORDER BY p.product_id ASC LIMIT ".(int)$limit;
			
			$query = $this->db->query($sql);

			if(count($query->rows) < $limit){
				$sql = "SELECT p.product_id FROM ".DB_PREFIX."product p LEFT JOIN ".DB_PREFIX."product_to_category p2c ON (p.product_id = p2c.product_id) WHERE p2c.category_id = '".(int)$category_id."' AND p.status = '1'";
				$stock ? $sql .=" AND p.quantity >= '1'" : '';
				$sql .=" AND p.date_available <= NOW() AND p.product_id <> '".(int)$product_id."' ORDER BY p.product_id ASC LIMIT ".(int)$limit;
			
				$query = $this->db->query($sql);
			}
			
			if ($query->rows) {
				foreach ($query->rows as $result) {
					$product_data[$result['product_id']] = $this->model_catalog_product->getProduct((int)$result['product_id']);
				}
			}
			
			$stock ? $this->cache->set('unishop.autorelated.stock.'.(int)$product_id, $product_data) : $this->cache->set('unishop.autorelated.'.(int)$product_id, $product_data);
		}

		return $product_data;
	}
	
	public function getRelated($product_id) {
		$product_data = array();
		$limit = 10;

		if (isset($product_id) && (int)$product_id > 0) {
			$query = $this->db->query("SELECT * FROM ".DB_PREFIX."product_related pr LEFT JOIN ".DB_PREFIX."product p ON (pr.related_id = p.product_id) LEFT JOIN ".DB_PREFIX."product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pr.product_id = '".(int)$product_id."' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '".(int)$this->config->get('config_store_id')."' LIMIT ".(int)$limit);
			foreach ($query->rows as $result) {
				$product_data[] = $result['related_id'];
			}
		}

		return $product_data;
	}

	public function getRelated2($product_id) {
		$product_data = array();
		$limit = 10;
		
		if (isset($product_id) && (int)$product_id > 0) {		
			$query = $this->db->query("SELECT op.product_id FROM ".DB_PREFIX."order_product op LEFT JOIN ".DB_PREFIX."product p ON (op.product_id = p.product_id) LEFT JOIN ".DB_PREFIX."product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN ".DB_PREFIX."order_product op1 ON (op1.order_id = op.order_id) WHERE op1.product_id = '".(int)$product_id."' AND op.product_id <> '".(int)$product_id . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '".(int)$this->config->get('config_store_id')."' GROUP BY op.product_id LIMIT ".(int)$limit);
			foreach ($query->rows as $result) {
				$product_data[] = $result['product_id'];
			}					
		}
		
		return $product_data;
	}
}
?>