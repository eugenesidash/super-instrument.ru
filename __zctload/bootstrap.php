<?php
require_once '../admin/config.php';
require_once DIR_SYSTEM.'startup.php';

$registry = new Registry();
$config = new Config();
$registry->set('config',$config);
$db = new DB(DB_DRIVER,DB_HOSTNAME,DB_USERNAME,DB_PASSWORD,DB_DATABASE,DB_PORT);
$registry->set('db',$db);
$query = $db->query('SELECT * FROM '.DB_PREFIX.'setting WHERE store_id = 0');
foreach($query->rows as $setting)
	$config->set($setting['key'],($setting['serialized']?json_decode($setting['value'],true):$setting['value']));
$loader = new Loader($registry);
$registry->set('load',$loader);
$url = new Url(HTTP_SERVER,HTTP_SERVER);
$registry->set('url',$url);
$cache = new Cache('file');
$registry->set('cache',$cache);
$languages = array();
$query = $db->query('SELECT * FROM `'.DB_PREFIX.'language`');
foreach($query->rows as $result)
	$languages[$result['code']] = $result;
$config->set('config_language_id',$languages[$config->get('config_admin_language')]['language_id']);
$language = new Language($languages[$config->get('config_admin_language')]['directory']);
$language->load($languages[$config->get('config_admin_language')]['directory']);
$registry->set('language',$language);
$event = new Event($registry);
$registry->set('event',$event);
$query = $db->query('SELECT * FROM '.DB_PREFIX.'event');
foreach($query->rows as $result)
	$event->register($result['trigger'],$result['action']);
?>