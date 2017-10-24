<?php
class ModelModuleNewsletters extends Model {
	public function createNewsletter()
	{
			
		$res0 = $this->db->query("SHOW TABLES LIKE '".DB_PREFIX."newsletter'");
		if($res0->num_rows == 0){
			$this->db->query("
				CREATE TABLE IF NOT EXISTS `". DB_PREFIX. "newsletter` (
				  `news_id` int(11) NOT NULL AUTO_INCREMENT,
				  `news_email` varchar(255) NOT NULL,
				  PRIMARY KEY (`news_id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
			");
		}
		
		
	}
	public function subscribes($data) {
		$res = $this->db->query("select * from ". DB_PREFIX ."newsletter where news_email='".$data['email']."'");
		if($res->num_rows == 1)
		{
			return "Вы уже делали подписку на этот Email!";
		}
		else
		{
		
			if($this->db->query("INSERT INTO " . DB_PREFIX . "newsletter(news_email) values ('".$data['email']."')"))
			{
				//$this->response->redirect($this->url->link('common/home', '', 'SSL'));
				return "Вы успешно подписались на рассылку новостей нашего магазина!";
			}
			else
			{
				//$this->response->redirect($this->url->link('common/home', '', 'SSL'));
				return "Ошибка подписки, обратитеся в службу поддержки";
			}
		}
	}
		
}