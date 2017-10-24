<?php
## BOOTSTRAP ##
ob_start();
ob_implicit_flush(0);

#session_start();

ini_set('display_errors','on');
error_reporting(E_ALL);

if(version_compare(PHP_VERSION,'5.3','<'))
	@set_magic_quotes_runtime(0);

date_default_timezone_set('Europe/Kiev');

setlocale(LC_ALL,'ru-RU.UTF-8');

define('TINY_AREA','public');

define('TINY_ROOT',str_replace('\\','/',realpath(dirname(__FILE__))).'/');
define('APP_ROOT',TINY_ROOT.'app/');
define('HELPER_ROOT',TINY_ROOT.'tiny/');
define('ZF_ROOT',TINY_ROOT.'zf/'); # change it if you need it

define('TINY_APP','ZctLoader');

## REQUIRES ZCT FRAMEWORK!! ##
require_once ZF_ROOT.'zwei.class.php';
require_once ZF_ROOT.'zpage.class.php';
require_once ZF_ROOT.'zdbc.class.php';

## TINY CORE ##
require_once HELPER_ROOT.'tiny.class.php';
## TINY STUFF ON DEMAND ##
#require_once HELPER_ROOT.'plugins/tiny.uncover.class.php';
## CONTRIB ##
require_once TINY_ROOT.'contrib/PHPExcel.php';
require_once TINY_ROOT.'contrib/PHPExcel/IOFactory.php';

## RUNME! ##
require_once TINY_ROOT.'bootstrap.php';
require_once APP_ROOT.strtolower(TINY_APP).'.class.php';
require_once APP_ROOT.strtolower(TINY_APP).'.templates.class.php';
require_once APP_ROOT.'ctlwrap.class.php';
$runme = TINY_APP;
$runme::Run();
?>