<?php
if(version_compare(PHP_VERSION,'5.3.0','>=') and extension_loaded('mysqlnd'))
	require_once 'zdbc.driver.mysqli.ext.php';
elseif(version_compare(PHP_VERSION,'5.0.0','>='))
	require_once 'zdbc.driver.mysqli.old.php';
else
	die('ZDBC::MySQLi: can\'t find suitable driver for php version '.PHP_VERSION.'!');
?>