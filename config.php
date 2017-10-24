<?php
# admin area
if(!defined('OCZ_ADM'))
	define('OCZ_ADM',0);

# load config
$cfg = parse_ini_file(dirname(__FILE__).'/../ocz.ini',true);

# is dev server settings used?
$cfg['dev']['enable'] = isset($cfg['dev']['enable']) ? intval($cfg['dev']['enable']) : 0;

# find and prepare base dir
if(!isset($cfg['dir']['base']) and ($cfg['dev']['enable'] and isset($cfg['dev']['dir_base'])))
	$cfg['dir']['base'] = $cfg['dev']['dir_base'];
if((!$cfg['dev']['enable'] and (!isset($cfg['dir']['base']) or !is_dir($cfg['dir']['base']))) or ($cfg['dev']['enable'] and (!isset($cfg['dev']['dir_base']) or !is_dir($cfg['dev']['dir_base']))))
	die("Configuration is damaged, can't start!\n");
$base_dir = $cfg['dev']['enable'] ? $cfg['dev']['dir_base'] : $cfg['dir']['base'];
if(isset($cfg['dir']['base']))
	unset($cfg['dir']['base']);

# fill const array
$cst = array();
foreach($cfg as $sect => $ls){
	$pr = strtoupper($sect);
	ksort($ls);
	if(in_array($sect,array('adm','dev','devadm')))
		continue;
	foreach($ls as $k => $v){
		$x = $pr.'_'.strtoupper($k);
		$cst[$x] = ('dir' == $sect) ? $base_dir.$v : $v;
	}
}

# hooks
if($cfg['dev']['enable']){
	foreach($cfg['dev'] as $k => $v){
		if('enable' == $k)
			continue;
		if('dir_' == substr($k,0,4))
			$v = $base_dir.$v;
		$k = strtoupper($k);
		$cst[$k] = $v;
	}
}

if(OCZ_ADM){
	if(isset($cst[strtoupper('http_admin')]))
		unset($cst[strtoupper('http_admin')]);
	if(isset($cst[strtoupper('https_admin')]))
		unset($cst[strtoupper('https_admin')]);
	foreach($cfg['adm'] as $k => $v){
		if($cfg['dev']['enable'] and isset($cfg['devadm'][$k]))
			$v = $cfg['devadm'][$k];
		if('dir_' == substr($k,0,4))
			$v = $base_dir.$v;
		$k = strtoupper($k);
		$cst[$k] = $v;
	}
}

foreach($cst as $k => $v)
	define($k,$v);

unset($base_dir,$cst,$cfg,$sect,$ls,$k,$v,$pr,$x);
