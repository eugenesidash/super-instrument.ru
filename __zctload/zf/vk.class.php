<?php
class vk
{
	const
		API_BASE_URL	= 'https://api.vk.com',
		API_REDIRECT	= 'https://oauth.vk.com/blank.html',
		API_VERSION		= '5.27';
	private
		$_curl			= null,
		$_captcha		= null,
		$_token			= null;
	private static
		$_instance		= null;
	## METHODS ##
	public static function auth_uri($appid,$scope)
	{
		return 'https://oauth.vk.com/authorize?client_id='.intval($appid).'&scope='.urlencode(urldecode($scope)).'&redirect_uri='.urlencode(self::API_REDIRECT).'&display=page&v='.self::API_VERSION.'&response_type=token';
	}
	public static function init($token)
	{
		if(!self::$_instance or self::$_instance->_token != $token)
			self::$_instance = new self($token);
	}
	private function __construct($token)
	{
		$this->_token = $token;
		$this->_curl = curl_init();
		##
		curl_setopt_array($this->_curl,array(
			CURLOPT_POST			=> false,
			CURLOPT_HEADER			=> false,
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_BINARYTRANSFER	=> true,
			CURLOPT_SSL_VERIFYHOST	=> false,
			CURLOPT_SSL_VERIFYPEER	=> false,
			CURLOPT_CONNECTTIMEOUT	=> 3,
			CURLOPT_TIMEOUT			=> 9
		));
	}
	private function __clone(){}
	private function __sleep(){}
	private function __wakeup(){}
	public function __destruct()
	{
		curl_close($this->_curl);
	}
	## WORKFLOW ##
	public static function captcha_key($k)
	{
		if(!empty(self::$_instance->_captcha[0]))
			self::$_instance->_captcha[1] = $k;
	}
	public static function user_query($id=0,$ext='')
	{
		$params = array(
			'user_ids'	=> $id,
			'fields'	=> $ext
		);
		return self::$_instance->send_api_request(self::$_instance->build_api_request('users.get',$params));
	}
	public static function photo_set_description($subject=0,$photo=0,$descr='')
	{
		$params = array(
			'owner_id'	=> $subject,
			'photo_id'	=> $photo,
			'caption'	=> $descr
		);
		return self::$_instance->send_api_request(self::$_instance->build_api_request('photos.edit',$params));
	}
	public static function album_read($subject=0,$album=0,$order=0,$offset=0,$limit=1,$ids=array())
	{
		$params = array(
			'owner_id'	=> $subject,
			'album_id'	=> $album,
			'offset'	=> $offset,
			'rev'		=> ($order) ? 1 : 0,
			'count'		=> $limit
		);
		if(is_array($ids) and count($ids))
			$params['photo_ids'] = implode(',',$ids);
		return self::$_instance->send_api_request(self::$_instance->build_api_request('photos.get',$params));
	}
	public static function wall_read($subj_id=0,$subject='',$id=0,$offset=0,$limit=1,$filter='all')
	{
		$params = array(
			'owner_id'	=> $subj_id,
			'domain'	=> $subject,
			'offset'	=> $offset,
			'count'		=> $limit,
			'filter'	=> $filter
		);
		return self::$_instance->send_api_request(self::$_instance->build_api_request('wall.get',$params));
	}
	public static function wall_channel($src)
	{
		if(empty($src['post_source']))
			return '?';
		$src = $src['post_source'];
		$out = array();
		foreach(array('type','data','platform','url') as $k)
			if(isset($src[$k]))
				$out[] = $src[$k];
		return implode(':',$out);
	}
	public static function wall_attached($ax)
	{
		$o = array();
		$txt = '';
		if(!is_array($ax) or !count($ax))
			return $o;
		foreach($ax as $a)
		{
			$t = $a['type'];
			$obj = $a[$t];
			$aid = (isset($obj['id'])) ? $obj['id'] : 0;
			$oid = (isset($obj['owner_id'])) ? $obj['owner_id'] : 0;
			$e = array($aid,$oid);
			if('photo' == $t or 'posted_photo' == $t)
			{
				$size = null;
				foreach(array(2560,1280,807,604,130,75) as $size)
					if(isset($obj['photo_'.$size]))
						break;
				$e[] = 'photo';
				$e[] = $obj['photo_'.$size];
				$e[] = $oid.'_'.$aid.'.jpg';
			}
			elseif('doc' == $t)
			{
				$e[] = $t;
				$e[] = $obj['url'];
				$e[] = $obj['title'];
			}
			elseif('audio' == $t)
			{
				$e[] = $t;
				$e[] = $obj['url'];
				$e[] = $obj['artist'].' - '.$obj['title'].' ['.$oid.'_'.$aid.'].mp3';
			}
			elseif('link' == $t)
			{
				$txt .= "\n{$obj['url']}";
				continue;
			}
			else
			{
				$txt .= "\n[$t{$oid}_$aid]";
				continue;
			}
			$o[] = $e;
		}
		return array($o,$txt);
	}
	## API ##
	private function build_api_request($f,$p)
	{
		$x = array();
		foreach($p as $k => $v)
			$x[] = $k.'='.urlencode($v);
		$p = implode('&',$x);
		$captcha = '';
		if(!empty($this->_captcha[0]) and !empty($this->_captcha[1])){
			$captcha = '&captcha_sid='.$this->_captcha[0].'&captcha_key='.$this->_captcha[1];
			$this->_captcha = null;
		}
		return self::API_BASE_URL.'/method/'.$f.'?'.$p.'&v='.self::API_VERSION.'&access_token='.$this->_token;
	}
	private function send_api_request($r)
	{
		curl_setopt($this->_curl,CURLOPT_URL,$r);
		$result = curl_exec($this->_curl);
		if(!$result)
		{
			if(!curl_errno($this->_curl))
				return null;
			else
				throw new Exception('cURL encountered an error during operation: '.curl_error($this->_curl),2);
		}
		##
		$json = json_decode($result,true);
		if(!$json)
			throw new Exception('error was found in JSON-encoded data: ['.json_last_error().'],[raw:'.$result.']',2);
		##
		if(!empty($json['error']))
		{
			if(5 == $json['error']['error_code'])
				return $json['error']['error_code'];
			elseif(14 == $json['error']['error_code']){
				curl_setopt($this->_curl,CURLOPT_URL,$json['error']['captcha_img']);
				$captcha = curl_exec($this->_curl);
				if(!$captcha)
					return 14;
				else{
					$this->_captcha = array($json['error']['captcha_sid'],'');
					return $captcha;
				}
			}
			throw new Exception($json['error']['error_msg']." [{$json['error']['error_code']}]",3);
		}
		return $json['response'];
	}
}
?>