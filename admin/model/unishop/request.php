<?php
class ModelUniShopRequest extends Model {
	public function editRequest($request_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "unishop_request SET type = '" . $this->db->escape($data['type']) . "', name = '" . $this->db->escape($data['name']) . "', phone = '" . $this->db->escape(strip_tags($data['phone'])) . "', mail = '" . $this->db->escape(strip_tags($data['mail'])) . "', comment = '" . $this->db->escape($data['comment']) . "', admin_comment = '" . $this->db->escape($data['admin_comment']) . "', date_added = '" . $this->db->escape($data['date_added']) . "', date_modified = NOW(), status = '" . (int)$data['status'] . "' WHERE request_id = '" . (int)$request_id . "'");
	}

	public function deleteRequest($request_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "unishop_request WHERE request_id = '" . (int)$request_id . "'");
	}

	public function getRequest($request_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "unishop_request WHERE request_id = '" . (int)$request_id . "'");

		return $query->row;
	}

	public function getRequests($data = array()) {
		$sql = "SELECT request_id, type, name, phone, mail, product_id, comment, admin_comment, date_added, date_modified, status FROM " . DB_PREFIX . "unishop_request WHERE request_id != '0'";																																					  

		$sort_data = array(
			'type',
			'name',
			'phone',
			'mail',
			'comment',
			'admin_comment',
			'date_added',
			'date_modified',
			'status'
		);
		
		if (!empty($data['filter_type'])) {
			$sql .= " AND type LIKE '" . $this->db->escape($data['filter_type']) . "%'";
		}
		
		if (!empty($data['filter_name'])) {
			$sql .= " AND name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (isset($data['filter_status']) && $data['filter_status'] != '') {
			$sql .= " AND status = '" . (int)$data['filter_status'] . "'";
		}

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];	
		} else {
			$sql .= " ORDER BY date_added";	
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}			

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}	

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
		
		//print_r($data);
		//print_r($sql);

		$query = $this->db->query($sql);	
		
		return $query->rows;
			
		$this->install();
	}

	public function getTotalRequests($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "unishop_request WHERE request_id != '0'";
		
		if (!empty($data['filter_type'])) {
			$sql .= " AND type LIKE '" . $this->db->escape($data['filter_type']) . "%'";
		}
		
		if (!empty($data['filter_name'])) {
			$sql .= " AND name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}		

		if (isset($data['filter_status']) && $data['filter_status'] != '') {
			$sql .= " AND status = '" . (int)$data['filter_status'] . "'";
		}
		
		$query = $this->db->query($sql);
		return $query->row['total'];
	}
	
	public function setComment($comment, $request_id) {
		$query = $this->db->query("UPDATE " . DB_PREFIX . "unishop_request SET admin_comment = '" . $this->db->escape($comment) . "' WHERE request_id = '" . (int)$request_id . "'");
	}
	
	public function setStatus($status, $request_id) {
		$query = $this->db->query("UPDATE " . DB_PREFIX . "unishop_request SET status = '" . (int)$status . "' WHERE request_id = '" . (int)$request_id . "'");
	}
	
	public function getStatuses() {
		$query = $this->db->query("SELECT DISTINCT type FROM " . DB_PREFIX . "unishop_request WHERE request_id != '0' ORDER BY type ASC");
		
		return $query->rows;
	}

	public function getNewRequests() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "unishop_request WHERE status = '1'");

		return $query->row['total'];
	}
	
	public function getProcessingRequests() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "unishop_request WHERE status = '2'");

		return $query->row['total'];
	}

	public function setApproval($request_id, $column_name, $value){
		$this->db->query("UPDATE " . DB_PREFIX . "blog_review SET " . $column_name . " = '" . (int)$value . "' WHERE request_id = '" . (int)$request_id . "'");
	}
	
	public function Install() {
		$this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."unishop_request` (`request_id` int(11) NOT NULL AUTO_INCREMENT, `type` varchar(64) NOT NULL, `name` varchar(64) NOT NULL, `phone` varchar(64) NOT NULL, `mail` varchar(64) NOT NULL, `product_id` varchar(64) NOT NULL, `comment` varchar(255) NOT NULL, `admin_comment` varchar(255) NOT NULL, `date_added` date NOT NULL DEFAULT '0000-00-00', `date_modified` date NOT NULL DEFAULT '0000-00-00', `status` tinyint(1) NOT NULL DEFAULT '1', PRIMARY KEY (`request_id`)) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

	}
}
?>