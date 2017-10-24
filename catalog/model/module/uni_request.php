<?php
class ModelModuleUniRequest extends Model {
	public function addRequest($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "unishop_request SET type = '" . $this->db->escape(strip_tags($data['type'])) . "', name = '" . $this->db->escape(strip_tags($data['name'])) . "', phone = '" . $this->db->escape(strip_tags($data['phone'])) . "', mail = '" . $this->db->escape(strip_tags($data['mail'])) . "', product_id = '" . (int)$data['product_id'] . "', comment = '" . $this->db->escape(strip_tags($data['comment'])) . "', admin_comment = '', date_added = NOW(), date_modified = NOW(), status = '" . (int)$data['status'] . "'");
		$request_id = $this->db->getLastId();
	}
}