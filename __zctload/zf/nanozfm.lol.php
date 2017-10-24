<?php
require_once 'zwei.class.php';

class NanoZFM
{
	# [r2]
	private
		$fields		= null,
		$action		= null,
		$post		= null,
		$files		= null,
		$subs		= null,
		$submitted	= null,
		$valid		= null,
		$msg		= null,
		$ctls		= null;
	public function __construct($params=array())
	{
		if(!count($params))
			trigger_error('NanoZFM: no params specified, passed.',E_USER_WARNING);
		##
		$this->fields = $this->names = array();
		$this->action = (isset($params['action'])) ? $params['action'] : ZWEI::srv('PHP_SELF');
		$this->post = (!isset($params['get'])) ? true : false;
		$this->files = (isset($params['files'])) ? true : false;
		$this->subs = false;
		$this->submitted = true;
		#$this->ctls = (isset($params['ctls'])) ? $params['ctls'] : 'submit'; # todo?
		foreach($params as $k => $v)
		{
			if(in_array($k,array('action','get','files','ctls')))
				continue;
			if(!is_array($v))
			{
				trigger_error('NanoZFM: wrong param @ ['.$k.'], ignored.',E_USER_WARNING);
				continue;
			}
			##
			if(isset($v['type']) and 'sub' == $v['type'])
			{
				if(empty($v['list']))
				{
					trigger_error('NanoZFM: empty list for section ['.$k.'], ignored.',E_USER_WARNING);
					continue;
				}
				$this->subs = true;
				$this->fields[$k] = array(0=>'sub',1=>array());
				foreach($v['list'] as $kk => $vv)
				{
					$this->fields[$k][1][$kk] = $this->traverse($vv);
					if($this->submitted and (isset($vv['type']) and 'radio' != $vv['type'] and 'checkbox' != $vv['type']) and (($this->post and ZWEI::post($kk) === false) or (!$this->post and ZWEI::get($kk) === false)))
						$this->submitted = false;
				}
			}
			else
			{
				$this->fields[$k] = $this->traverse($v);
				if($this->submitted and (isset($v['type']) and 'radio' != $v['type'] and 'checkbox' != $v['type']) and (($this->post and ZWEI::post($k) === false) or (!$this->post and ZWEI::get($k) === false)))
					$this->submitted = false;
			}
		}
		if($this->submitted)
			$this->valid = $this->validate();
	}
	public function __get($p)
	{
		$raw = false;
		if(substr($p,0,5) == 'raw__')
		{
			$p = substr($p,5);
			$raw = true;
		}
		if('valid' == $p)
			return $this->valid;
		if(isset($this->fields[$p]))
		{
			if($this->fields[$p]['type'] == 'checkbox')
			{
				if($this->post)
					return (ZWEI::post($p)) ? true : false;
				else
					return (ZWEI::get($p)) ? true : false;
			}
			elseif($this->valid)
			{
				if($raw)
					return ($this->post) ? ZWEI::post($p) : ZWEI::get($p);
				else
					return ZWEI::req($p);
			}
		}
		else
		{
			foreach($this->fields as $field)
			{
				if(!isset($field[0]) or 'sub' != $field[0])
					continue;
				if(isset($field[1][$p]))
				{
					if($field[1][$p]['type'] == 'checkbox')
					{
						if($this->post)
							return (ZWEI::post($p)) ? true : false;
						else
							return (ZWEI::get($p)) ? true : false;
					}
					elseif($this->valid)
					{
						if($raw)
							return ($this->post) ? ZWEI::post($p) : ZWEI::get($p);
						else
							return ZWEI::req($p);
					}
				}
			}
		}
		return null;
	}
	public function render()
	{
		$fx = ($this->files) ? ' enctype="multipart/form-data"' : '';
		$out = '<form action="'.$this->action.'" method="'.($this->post ? 'post' : 'get').'"'.$fx.'>';
		$out .= "\n";
		##
		if($this->msg)
			$out .= '<div style="color:red;font-weight:bold;">'.$this->msg."</div>\n";
		##
		$dlo = false;
		if(!$this->subs)
			$out .= "<dl>\n";
		foreach($this->fields as $k => $v)
		{
			if(count($v) == 2 and $v[0] == 'sub')
			{
				if($dlo)
				{
					$out .= "</dl>\n";
					$dlo = false;
				}
				$out .= "<fieldset>\n<legend>$k</legend>\n<dl>\n";
				foreach($v[1] as $kk => $vv)
				{
					$req = ($vv['required']) ? '<abbr title="Required field">*</abbr>' : '';
					$out .= $this->render_ctl($kk,$vv,$this->post);
				}
				$out .= "</dl></fieldset>\n";
			}
			else
			{
				if($this->subs and !$dlo)
				{
					$out .= "<dl>\n";
					$dlo = true;
				}
				$out .= $this->render_ctl($k,$v,$this->post);
			}
		}
		if(!$this->subs or $dlo)
			$out .= "</dl>\n";
		$out .= "</form>\n";
		return $out;
	}
	public function ready()
	{
		return $this->submitted;
	}
	private function traverse(&$v)
	{
		$type = (!empty($v['type'])) ? $v['type'] : null;
		if(!$type)
			$type = (empty($v['label'])) ? 'hidden' : 'text';
		##
		if(($type == 'radio' or $type == 'select') and empty($v['list']))
		{
			trigger_error('NanoZFM: empty list for section ['.$k.'], ignored.',E_USER_WARNING);
			return;
		}
		##
		return array(
			'type' => $type,
			'label' => (isset($v['label'])) ? $v['label'] : '',
			'value' => (isset($v['value'])) ? $v['value'] : '',
			'size' => (isset($v['size'])) ? $v['size'] : 30,
			'list' => (isset($v['list'])) ? $v['list'] : null,
			'class' => (isset($v['class'])) ? $v['class'] : '',
			'placeholder' => (isset($v['placeholder'])) ? $v['placeholder'] : '',
			'blank' => (isset($v['blank'])) ? $v['blank'] : false,
			'required' => (isset($v['required'])) ? $v['required'] : false,
			'disabled' => (isset($v['disabled'])) ? $v['disabled'] : false,
			'mask' => (isset($v['mask'])) ? $v['mask'] : ''
		);
	}
	private function render_ctl($k,$v,$post=true)
	{
		if(!isset($v['value']) and (($post and ZWEI::post($k)) or (!$post and ZWEI::get($k))))
			$v['value'] = ($post) ? ZWEI::post($k) : ZWEI::get($k);
		$type = $v['type'];
		$ext = ($v['disabled']) ? ' disabled="disabled"' : '';
		$ext .= ($v['placeholder']) ? ' placeholder="'.$v['placeholder'].'"' : '';
		if('textarea' == $type)
			$out = '<textarea name="'.$k.'" cols="'.$v['size'].'" rows="5"'.$ext.'>'.$v['value'].'</textarea>'."\n";
		elseif('select' == $type)
		{
			$out = '<select name="'.$k.'"'.$ext.'>'."\n";
			foreach($v['list'] as $kk => $vv)
			{
				$d = '';
				if('!!nzfm!x!' == substr($kk,0,9))
				{
					$d = ' disabled="disabled"';
					$kk = substr($kk,9);
				}
				$out .= ($v['value'] == $kk) ? '<option value="'.$kk.'" selected="selected"'.$d.'>'.$vv.'</option>'."\n" : '<option value="'.$kk.'"'.$d.'>'.$vv.'</option>'."\n";
			}
			$out .= "</select>\n";
		}
		elseif('yesno' == $type)
			$out = '<label>Yes <input type="radio" name="'.$k.'" value="1" checked="checked"'.$ext.'></label>&nbsp;<label><input type="radio" name="'.$k.'" value="0"'.$ext.'> No</label>'."\n";
		elseif('radio' == $type)
		{
			$out = '';
			foreach($v['list'] as $kk => $vv)
				$out .= ($v['value'] == $kk) ? $vv.' <input type="radio" name="'.$k.'" value="'.$kk.'" checked="checked"'.$ext.'>'."<br>\n" : $vv.' <input type="radio" name="'.$k.'" value="'.$kk.'"'.$ext.'>'."\n";
		}
		elseif('checkbox' == $type)
		{
			$xc = ($v['value']) ? ' checked="checked"' : '';
			$out = '<input type="checkbox" name="'.$k.'"'.$xc.$ext.'>';
		}
		else
			$out = '<input type="'.$type.'" name="'.$k.'" value="'.$v['value'].'"'.$ext.'>';
		##
		if('hidden' != $type)
		{
			$req = ($v['required']) ? '<abbr style="color:red;" title="Required field">*</abbr>' : '';
			if($v['label'])
				$out = "<dt>{$v['label']}$req</dt>\n<dd>$out</dd>\n";
			else
				$out = "<div class=\"ac\">$req$out</div>\n";
		}
		else
			$out .= "\n";
		return $out;
	}
	private function validate()
	{
		foreach($this->fields as $k => $v)
		{
			if($this->msg)
				break;
			if(isset($v[0]) and 'sub' == $v[0])
			{
				foreach($v[1] as $kk => $vv)
				{
					if($this->msg)
						break;
					if(in_array($vv['type'],array('radio','checkbox')))
						$vv['blank'] = true;
					if('file' != $vv['type'])
					{
						$iv = ($this->post) ? ZWEI::post($kk) : ZWEI::get($kk);
						if($vv['required'] and ($iv === false or $iv === ''))
							$this->msg = 'Required field ['.$kk.'] not set!';
						elseif(!$vv['blank'] and $iv === '')
							$this->msg = 'Required to be not blank field ['.$kk.'] is blank!';
						elseif($vv['mask'] and !preg_match($vv['mask'],$iv))
							$this->msg = 'Invalid input in field ['.$kk.']!';
					}
				}
			}
			else
			{
				if(in_array($v['type'],array('radio','checkbox')))
					$v['blank'] = true;
				if('file' != $v['type'])
				{
					$iv = ($this->post) ? ZWEI::post($k) : ZWEI::get($k);
					if($v['required'] and !$iv)
						$this->msg = 'Required field ['.$k.'] not set!';
					elseif(!$v['blank'] and !$iv)
						$this->msg = 'Required to be not blank field ['.$k.'] is blank!';
					elseif($v['mask'] and !preg_match($v['mask'],$iv))
						$this->msg = 'Invalid input in field ['.$k.']!';
				}
			}
		}
		return ($this->msg) ? false : true;
	}
}
?>