<?php
class Ocstore extends Model{
	protected $registry;

	public function __construct($registry) {
		$this->registry = $registry;
	}
	
	public function get($key) {
		return $this->db->query("select `value` from `" . DB_PREFIX . "ocstore_settings` where `key` = '".$this->db->escape($key)."';")->row['value'];
	}

	public function set($key, $value) {
		return $this->db->query("update`" . DB_PREFIX . "ocstore_settings` set `value` = '".$this->db->escape($value)."' where `key` = '".$this->db->escape($key)."';");
	}
}

class OcstoreException extends Exception
{
	public function __construct($message, $code = 0, Exception $previous = null) {
		parent::__construct($message, $code, $previous);
	}

	public function __toString() {
		return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}
}

class ModelOcstoreBadges extends Ocstore {
	public function install() { 
		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "_ocstore_badges` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `name` char(100) DEFAULT NULL,
				  `image` char(100) DEFAULT NULL,
				  `data` text,
				  `enabled` tinyint(1) DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$this->db->query($sql);

		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "_ocstore_badges_product` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `product_id` int(11) NOT NULL,
				  `badge_id` int(11) NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `product_id` (`product_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$this->db->query($sql);
	}

	public function uninstall() {
	}

	public function badges() {
		$sql = "select * from `" . DB_PREFIX . "ocstore_badges`";
		return $this->db->query($sql)->rows;
	}

	public function edit($id) {
		$sql = "select * from `" . DB_PREFIX . "ocstore_badges` where id = ".(int)$id;
		return $this->db->query($sql)->row;
	}
	public function add() {
		return array(
			"name" => "",
			"image" => "",
			"data" => array(
				"position" => "topright",
				"badgetext"	=> "",
				"badge" => array(
					"top" => "0",
					"left" => "",
					"right" => "0",
					"bottom" => ""
				),
				"angle" => 0
			),
			"enabled" => 1,
			"category" => array(),
			"tags" => array(),
		);
	}

	public function save($post) {
		$sql = "INSERT INTO `" . DB_PREFIX . "ocstore_badges` set
                         `name` = '".$this->db->escape($post['name'])."',
                         `image` = '".$this->db->escape($post['image'])."',
                         `data` = '".$this->db->escape($post['data'])."',
                         `enabled` = '".$this->db->escape($post['enabled'])."'";
		$this->db->query($sql);
	}

	public function update($id, $post) {
		$sql = "UPDATE `" . DB_PREFIX . "ocstore_badges` set
                         `name` = '".$this->db->escape($post['name'])."',
                         `image` = '".$this->db->escape($post['image'])."',
                         `data` = '".$this->db->escape($post['data'])."',
                         `enabled` = '".$this->db->escape($post['enabled'])."'
                WHERE
						 `id` = ".(int)$id;
		$this->db->query($sql);
	}

	public function remove($id) {
		$sql = "DELETE FROM `" . DB_PREFIX . "ocstore_badges` WHERE `id` = ".(int)$id;
		$this->db->query($sql);
	}

	public function badges_product($id) {
		$sql = "select * from `" . DB_PREFIX . "ocstore_badges_product` where product_id = ".(int)$id;
		$rows = $this->db->query($sql);
		if ($rows->num_rows > 0) {
			$temp = array();
			foreach ($rows->rows as $b)
				$temp[] = $b['badge_id'];
			return $temp;
		}
		return array();
	}
	
	public function badges_all() {
		$sql = "select * from `" . DB_PREFIX . "ocstore_badges_product`";
		$rows = $this->db->query($sql);
		if ($rows->num_rows > 0) {
			$temp = array();
			foreach ($rows->rows as $b)
				$temp[$b['product_id']][] = $b['badge_id'];
			return $temp;
		}
		return array();
	}
	
	public function getAllTags() {
		$sql = "select tag from `" . DB_PREFIX . "product` as p, `" . DB_PREFIX . "product_description` as d where p.product_id = d.product_id and p.status = 1";
		$rows = $this->db->query($sql);
		if ($rows->num_rows > 0) {
			$temp = array();
			foreach ($rows->rows as $b)
				foreach (explode(",", $b['tag']) as $t)
					if (trim($t) != "")
						$temp[] = trim($t);
			return $temp;
		}
		return array();
	}
	
	public function addLink($product_id, $badge_id) {
		$this->db->query("INSERT IGNORE INTO `" . DB_PREFIX . "ocstore_badges_product` SET `product_id` = " . (int)$product_id . ", badge_id = ".(int)$badge_id.", `auto` = 1");
	}
	
	public function clean() {
		$sql = "DELETE FROM `" . DB_PREFIX . "ocstore_badges_product`";
		$this->db->query($sql);
	}
	
	public function cleanAuto() {
		$sql = "DELETE FROM `" . DB_PREFIX . "ocstore_badges_product` where `auto` = 1";
		$this->db->query($sql);
	}
	
	public function getProductsByTags($tags) {
		$temp = array();
		foreach ($tags as $tag)
			$temp[] = " `tag` like '%".$tag."%' "; 
		$sql = "select product_id from `" . DB_PREFIX . "product_description` where ".implode(" OR ", $temp);
		return $this->db->query($sql)->rows;
	}
	
	
}