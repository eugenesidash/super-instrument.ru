<?php
class TinyScript
{
	protected static
		$__instance		= null;
	protected
		$_ajax			= null,
		$_base			= null,
		$_ssl			= null,
		$_title			= null,
		$_wrapper		= null,
		$_headers_sent	= null;
	##
	public static function instance()
	{
		if(self::$__instance)
			return self::$__instance;
		return false;
	}
	protected static function initialize($app=null)
	{
		if(!$app or self::$__instance)
			return;
		self::$__instance = new $app;
	}
	protected function __construct()
	{
		self::clear_input();
		$this->_ajax = ('xmlhttprequest' === strtolower(ZWEI::srv('HTTP_X_REQUESTED_WITH'))) ? true : false;
		$this->_ssl = (ZWEI::srv('HTTPS') or ZWEI::srv('HTTP_X_HTTPS')) ? true : false;
		$this->_build_base_url();
		$this->_wrapper = './tiny/tiny.wrapper.php';
	}
	public function __get($k)
	{
		switch($k)
		{
			case 'xhr':
			case 'ajax':
				return $this->_ajax;
			break;
			case 'base':
			case 'base_url':
				return $this->_base;
			break;
			case 'https':
			case 'ssl':
				return $this->_ssl;
			break;
			default:
				return null;
			break;
		}
	}
	private function _build_base_url()
	{
		## Scheme
		$base = ($this->_ssl) ? 'https://' : 'http://';
		## Host
		$sni = ZWEI::srv('SERVER_NAME');
		if('*' == substr($sni,0,1) and substr($sni,1) == substr(ZWEI::srv('HTTP_HOST'),(strlen($sni)-1)*-1)
		or '*' == substr($sni,-1)  and substr($sni,0,-1) == substr(ZWEI::srv('HTTP_HOST'),0,strlen($sni)-1))
			$base .= ZWEI::srv('HTTP_HOST');
		else
			$base .= $sni;
		## Port
		if((80 != ZWEI::srv('SERVER_PORT') and !$this->_ssl) or (443 != ZWEI::srv('SERVER_PORT') and $this->_ssl))
			$base .= ':'.ZWEI::srv('SERVER_PORT');
		## Path
		if('/' != dirname(ZWEI::srv('SCRIPT_NAME')) and '\\' != dirname(ZWEI::srv('SCRIPT_NAME')))
			$base .= dirname(ZWEI::srv('SCRIPT_NAME'));
		$this->_base = $base;
	}
	private function srv_proto_prefix()
	{
		$proto = ($proto = ZWEI::srv('SERVER_PROTOCOL')) ? $proto : 'HTTP/1.0';
		$prefix = (false !== stripos(PHP_SAPI,'cgi')) ? 'Status:' : $proto;
		return $prefix;
	}
	protected function send_headers_dl($name,$size,$type='application/octet-stream',$expires=0)
	{
		if($this->_headers_sent)
			return;
		if(!$expires)
			$expires = -86400;
		$expires = gmstrftime('%c',time()+$expires);
		$prefix = $this->srv_proto_prefix();
		ob_end_clean();
		header($prefix.' 200 OK',true,200);
		header('Content-Type: '.$type);
		header('Content-Description: '.$name);
		header('Content-Disposition: attachment; filename='.$name);
		header('Content-Length: '.$size);
		header('Content-Transfer-Encoding: binary');
		header('Expires: '.$expires.' GMT');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		$this->_headers_sent = true;
	}
	protected function send_headers($raw=false,$gzip=true,$chrx='utf-8',$ct=null,$statusline='200 OK',$expires=0)
	{
		if($this->_headers_sent)
			return;
		$prefix = $this->srv_proto_prefix();
		##
		$stderr = ob_get_contents();
		ob_end_clean();
		header($prefix.$statusline);
		##
		if(!$ct)
		{
			if($this->_ajax)
				$ct = ($raw) ? 'text/plain' : 'application/json';
			else
				$ct = ($raw) ? 'text/plain' : 'text/html';
		}
		elseif('application/xhtml+xml' == $ct and !strstr(strtolower(ZWEI::srv('HTTP_ACCEPT')),'application/xhtml+xml'))
			$ct = 'text/html';
		header("Content-Type: $ct;charset=$chrx");
		##
		if(!$expires)
		{
			header('Expires: '.gmstrftime('%c',time()-86400).' GMT');
			header('Cache-Control: no-store, no-cache, must-revalidate');
			header('Cache-Control: post-check=0, pre-check=0',false);
			header('Pragma: no-cache');
		}
		else
		{
			header('Expires: '.gmstrftime('%c',time()+$expires).' GMT');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0, max-age='.$expires);
		}
		if($gzip)
			@ob_start('ob_gzhandler');
		$this->_headers_sent = true;
		return $stderr;
	}
	protected function send_body($content,$raw=false,$wrapper=null)
	{
		if(!$this->_headers_sent)
			$errors = $this->send_headers();
		else
			$errors = '';
		if($raw)
			echo $content;
		elseif($this->_ajax)
		{
			if(!is_array($content))
				$content = array('content'=>$content);
			if($errors)
				$content['stderr'] = $errors;
			echo json_encode($content);
		}
		else
		{
			if(!$wrapper)
				$wrapper = $this->_wrapper;
			require_once $wrapper;
		}
		exit;
	}
	protected function redirect($url=null,$abs=false,$type=0)
	{
		if(!$url)
			$url = $this->_base;
		elseif('/' != substr($url,0,1))
			$url = '/'.$url;
		if(!$abs)
			$url = $this->_base.$url;
		$errors = ob_get_contents();
		ob_end_clean();
		if($this->_ajax)
		{
			header('Content-Type: application/json;charset=utf-8');
			echo json_encode(array('redirect'=>$url,'stderr'=>$errors));
		}
		elseif($type > 1)
			echo '<html><head><meta http-equiv="refresh" content="3; url='.$url.'"></head><body></body></html>';
		elseif($type)
			header("Refresh: 0;url=$url");
		else
			header("Location: $url",true,302);
		exit;
	}
	protected function fatal($base='Kernel panic',$ext='')
	{
		$errors = ob_get_contents();
		ob_end_clean();
		if($this->_ajax)
		{
			@header('Content-Type: application/json;charset=utf-8');
			echo json_encode(array('errors'=>$base.'<hr>'.$ext));
			exit;
		}
		$ext = ($ext) ? "<p>$ext</p>" : '';
		echo <<<HFE
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head><title>TinyScript Rebooted :: Fatal error</title></head><body><h1>Fatal Error</h1><p>$base</p>$ext<address>TinyScript Rebooted</address></body></html>
HFE;
		exit;
	}
	## STATICS
	public static function route()
	{
		$sel = array();
		if($req = ZWEI::srv('QUERY_STRING'))
		{
			if(false !== ($q = strpos($req,'?')) or false !== ($a = strpos($req,'&')))
			{
				if($q and $a)
					$x = ($q > $a) ? $a : $q;
				else
					$x = ($a) ? $a : $q;
				$req = substr($req,0,$x);
				if(isset($_GET[substr($req,0,$x)]))
					unset($_GET[substr($req,0,$x)]);
			}
			if('/' == substr($req,0,1))
				$req = substr($req,1);
			$sel = (strpos($req,'/') !== false) ? explode('/',$req) : array($req);
		}
		return $sel;
	}
	private static function clear_input()
	{
		if(isset($_REQUEST))
			unset($_REQUEST);
		ZWEI::clear($_GET);
		ZWEI::clear($_POST);
		ZWEI::clear($_COOKIE);
		ZWEI::clear($_SERVER);
	}
}
?>