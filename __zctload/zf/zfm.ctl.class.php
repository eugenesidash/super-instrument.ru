<?php
class ZFMCtl extends ZFM
{
	private
		$type		= null,
		$name		= null,
		$value		= null,
		$required	= null,
		$blank		= null,
		$size		= null, # size for text/pass, cols for textarea
		$rows		= null,	# rows for textarea
		$label		= null,
		$checked	= null,
		$class		= null,
		$mask		= null,
		$inline		= null,
		$anonymous	= null,
		$init_val	= null, # internal use
		$modifled	= false; # internal use
	public function __construct($p=array())
	{
		if(!class_exists('ZWEI',false))
		{
			throw new Exception('ZWEI required in order to use ZFM!',11);
		}
		##
		mt_srand(intval(microtime(true)));
		##
		$this->type = $this->v($p,'type',ZFM::T_HIDDEN);
		$this->name = $this->v($p,'name',null);
		if(!$this->name)
		{
			## Spambots will gone crazy with it!
			$this->name = 'zfmctl'.crc32(microtime(true).mt_rand());
			$this->anonymous = true;
		}
		else
			$this->anonymous = false;
		$this->value = $this->v($p,'value','');
		$this->required = $this->v($p,'required',false);
		$this->blank = $this->v($p,'blank',false);
		$this->size = $this->v($p,'size',24);
		$this->rows = $this->v($p,'rows',5);
		$this->label = $this->v($p,'label','');
		$this->checked = $this->v($p,'checked',false);
		$this->class = $this->v($p,'class','');
		$this->raw = $this->v($p,'raw',false);
		$this->mask = $this->v($p,'mask',null);
		$this->inline = $this->v($p,'inline',false);
		##
		if($this->isMulti() and (!is_array($this->value) or !count($this->value)))
		{
			$this->type = ZFM::T_TEXT;
			$this->value = 'ZFM ERROR: SELECT REQUIRES ARRAY FOR VALUE!';
		}
	}
	public function __get($p)
	{
		switch($p)
		{
			case 'type':
			case 'name':
			case 'required':
			case 'blank':
			case 'label':
			case 'checked':
			case 'class':
			case 'anonymous':
			case 'inline':
			case 'mask':
				return $this->$p;
			break;
			case 'value':
				return ($this->isMulti() or $this->type == ZFM::T_CHECKBOX) ? $this->checked : $this->value;
			break;
			default:
				return false;
			break;
		}
	}
	## out
	public function render()
	{
		if(!$this->type and $this->type !== 0)
			return false;
		$id = 'zfm_'.$this->name;
		$creds = 'name="'.$this->name.'" id="'.$id.'"';
		$class = ($this->class) ? ' class="'.$this->class.'"' : '';
		$value = ($this->value !== '' and !is_array($this->value)) ? ' value="'.$this->value.'"' : '';
		##
		if($this->type == ZFM::T_TEXT or $this->type == ZFM::T_PASSWORD)
			$tt = ($this->type == ZFM::T_PASSWORD) ? 'password' : 'text';
		##
		if($this->type == ZFM::T_TEXT or $this->type == ZFM::T_PASSWORD)
			return '<input type="'.$tt.'" size="'.$this->size.'" '.$creds.$class.$value.'>';
		elseif($this->type == ZFM::T_TEXTAREA)
			return '<textarea cols="'.$this->size.'" rows="'.$this->rows.'" '.$creds.$class.'>'.$this->value.'</textarea>';
		elseif($this->type == ZFM::T_RADIO)
		{
			$out = '';
			foreach($this->value as $k => $v)
			{
				$marked = ($this->checked == $k) ? ' checked="checked"' : '';
				## TODO!
				$ll = ($v[1]) ? $v[0].'&nbsp;' : '';
				$lr = ($v[1]) ? '' : '&nbsp;'.$v[0];
				$out .= '<label>'.$ll.'<input type="radio" '.$creds.$class.' value="'.$k.'"'.$marked.'>'.$lr.'</label>'."\n";
			}
			return $out;
		}
		elseif($this->type == ZFM::T_CHECKBOX)
		{
			$marked = ($this->checked or $this->value) ? ' checked="checked"' : '';
			return '<input type="checkbox" '.$creds.$class.$marked.'>';
		}
		elseif($this->type == ZFM::T_SELECT)
		{
			$out = '';
			foreach($this->value as $k => $v)
			{
				$marked = ($this->checked == $k) ? ' selected="selected"' : '';
				$out .= '<option value="'.$k.'"'.$marked.'>'.$v.'</option>';
			}
			return '<select '.$creds.$class.'>'.$out.'</select>';
		}
		else
		{
			#assume hidden
			if($this->value === null)
				$this->value = '1';
			$v = ' value="'.$this->value.'"';
			return '<input type="hidden" '.$creds.$v.'>';
		}
	}
	## in
	public function IsReady()
	{
		if($this->type == ZFM::T_CHECKBOX and !$this->required)
			return true;
		else
			return (ZWEI::req($this->name) !== false);
	}
	public function Income($method=ZFM::M_POST)
	{
		if($this->IsReady())
		{
			if($this->isMulti())
			{
				$this->init_val = $this->checked;
				$this->checked = ZWEI::req($this->name);
				$this->modifled = ($this->checked != $this->init_val);
			}
			elseif($this->type == ZFM::T_CHECKBOX)
			{
				$this->init_val = $this->checked;
				$this->checked = (ZWEI::req($this->name) !== false);
				$this->modifled = ($this->checked != $this->init_val);
			}
			else
			{
				$this->init_val = $this->value;
				if($this->raw)
					$this->value = ($method == ZFM::M_POST) ? ZWEI::post($this->name) : ZWEI::get($this->name);
				else
					$this->value = ZWEI::req($this->name);
				$this->modifled = ($this->value != $this->init_val);
			}
		}
	}
}
?>