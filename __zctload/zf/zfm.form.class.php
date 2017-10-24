<?php
class ZFMForm extends ZFM
{
	private
		$action			= null,
		$method			= null,
		$sysctl			= null,
		$ctl_lbl		= null,
		$fields			= null,
		$hash			= null,
		$file			= null;
	public function __construct($p=array())
	{
		if(!class_exists('ZWEI',false))
		{
			throw new Exception('ZWEI required in order to use ZFM!',11);
		}
		##
		$this->action = $this->v($p,'action',$_SERVER['PHP_SELF']);
		$this->method = $this->v($p,'method',ZFM::M_POST);
		$this->sysctl = $this->v($p,'sysctl',ZFM::C_SUBMIT);
		$this->ctl_lbl = $this->v($p,'ctl_lbl',array(ZFM::C_SUBMIT=>'Ok',ZFM::C_RESET=>'Ok/205',ZFM::C_NRST=>'Ok/204'));
		##
		$this->fields = $this->v($p,'fields',null); # TODO
		if($this->fields instanceof ZFMCtl)
			$this->fields = array($this->fields);
		if(!$this->fields or !is_array($this->fields))
			throw new Exception('Fieldset is required and not supplied!');
		##
		foreach($this->fields as &$field)
			$field->Income();
		## TODO!
		/*$anon = true;
		if(!$this->hash = ZWEI::s('ZFMHash'))
		{
			$this->hash = null;
			$anon = false;
		}
		##
		foreach($this->fields as $idx => $fld)
		{
			if(!$this->file and $fld->type == ZFM::T_FILE)
				$this->file = true;
			if($fld->anonymous)
			{
				if(!$this->hash)
					$this->hash = array();
				$this->hash[$idx] = $fld->name;
			}
		}
		if(count($this->hash))
			ZWEI::s('ZFMHash',$this->hash);*/
	}
	/*public function __get($p)
	{
		return $this->fields;
	}*/
	## out
	public function render()
	{
		$tpl = (func_num_args() == 1) ? func_get_arg(0) : null;
		if($this->file)
			$method = ($this->method == ZFM::M_PUT) ? 'put' : 'post';
		else
			$method = ($this->method == ZFM::M_GET) ? 'get' : 'post';
		$file = ($this->file) ? ' enctype="multipart/form-data"' : '';
		if($tpl)
		{
			if(file_exists($tpl))
				$tpl = file_get_contents($tpl);
			##
			$xref = key($this->fields);
			if(file_exists($tpl))
			{
				foreach($this->fields as $ix => $f)
				{
					$label_{$ix} = $f->label;
					$ctl_{$ix} = $f->render();
				}
				$act = $this->action;
				require_once $tpl;
			}
			elseif(strpos($tpl,'$label_'.$xref))
			{
				$out = str_replace('$act',$this->action,$tpl);
				foreach($this->fields as $ix => $f)
				{
					$out = str_replace('$label_'.$ix,$f->label,$out);
					$out = str_replace('$ctl_'.$ix,$f->render(),$out);
				}
			}
			elseif(strpos($tpl,"%LABEL_$xref%"))
			{
				$out = str_replace('%ACT%',$this->action,$tpl);
				foreach($this->fields as $ix => $f)
				{
					$out = str_replace("%LABEL_$ix%",$f->label,$out);
					$out = str_replace("%CTL_$ix%",$f->render(),$out);
				}
			}
			else $out = 'ZFM: render error!';
			return $out;
		}
		else
		{
			$out = '<form action="'.$this->action.'" method="'.$method.'"'.$file.'>';
			##
			foreach($this->fields as $f)
				if($f->type == ZFM::T_HIDDEN)
					$out .= $f->render();
			$out .= '<table class="zfm_tbl">';
			##
			$ctl = '';
			foreach($this->fields as $f)
			{
				if($f->type == ZFM::T_HIDDEN)
					continue;
				$label = $f->label;
				if($f->required)
					$label .= '*';
				elseif($f->inline)
				{
					if(!$ctl)
						$ctl = '<tr><td class="zfm_k">'.$label.'</td><td class="zfm_v">';
					elseif($label)
						$ctl .= "&nbsp;$label&nbsp;";
					else
						$ctl .= "&nbsp;";
					$ctl .= $f->render();
					continue;
				}
				elseif(!$f->inline and $ctl)
				{
					$out .= "$ctl</td></tr>";
					$ctl = '';
				}
				$out .=  '<tr><td class="zfm_k">'.$label.'</td><td class="zfm_v">'.$f->render().'</td></tr>';
			}
			##
			if($ctl)
				$out .= "$ctl</td></tr>";
			##
			$ctls = '';
			if($this->sysctl & ZFM::C_SUBMIT)
				$ctls .= '<input type="submit" name="zfm_submit" value="'.$this->ctl_lbl[ZFM::C_SUBMIT].'">';
			if($this->sysctl & ZFM::C_RESET)
				$ctls .= '<input type="submit" name="zfm_rstfrm" value="'.$this->ctl_lbl[ZFM::C_RESET].'">';
			if($this->sysctl & ZFM::C_NRST)
				$ctls .= '<input type="submit" name="zfm_nrstfrm" value="'.$this->ctl_lbl[ZFM::C_NRST].'">';
			##
			$out .= '<tr><th colspan="2">'.$ctls.'</th></tr></table></form>';
			return $out;
		}
	}
	## in
	public function IsReady()
	{
		$ready = true;
		foreach($this->fields as $fld)
			if(!$fld->IsReady())
				$ready = false;
		return $ready;
	}
	public function IsValid()
	{
		foreach($this->fields as $idx => $fld)
		{
			if($fld->required and (!$fld->IsReady() or !$fld->value) and (!$fld->mask or strpos($fld->mask,'ignore=') === false))
				return 'Не заполнено обязательное поле ['.$fld->label.']';
			if($fld->mask)
			{
				if(strpos($fld->mask,'match=') !== false)
				{
					$match = str_replace('match=','',$fld->mask);
					if(strpos($fld->mask,'ignore=') !== false)
					{
						$ignore = str_replace('ignore=','',$fld->mask);
						if(strpos($ignore,':') !== false)
							list($ignore,$iv) = explode(':',$ignore,2);
						else
							$iv = true;
						if(is_numeric($idx) and is_numeric($ignore) and $this->fields[intval($ignore)]->value == $iv)
							return null;
						elseif(!is_numeric($idx) and !is_numeric($ignore))
							foreach($this->fields as $fx)
								if($fx->name == $ignore and $fx->value == $iv)
									return null;
					}
					if(is_numeric($idx) and is_numeric($match) and $this->fields[intval($match)]->value != $fld->value)
						return 'Введенные в дублируемые поля ['.$fld->label.'/'.$this->fields[intval($match)]->label.'] данные не совпадают';
					elseif(!is_numeric($idx) and !is_numeric($match))
						foreach($this->fields as $fx)
							if($fx->name == $match and $fx->value != $fld->value)
								return 'Введенные в дублируемые поля ['.$fld->label.'/'.$fx->label.'] данные не совпадают';
				}
				elseif(!preg_match($fld->mask,$fld->value))
					return 'Введены некорректные данные в поле ['.$fld->label.']';
			}
		}
		return null;
	}
	public function Value($k=null)
	{
		if($k)
		{
			if(is_int($k) and isset($this->fields[$k]))
				return $this->fields[$k]->value;
			else
				foreach($this->fields as $f)
					if($f->name == $k)
						return $f->value;
		}
		else
		{
			$out = array();
			foreach($this->fields as $idx => $fld)
			{
				$ix = ($fld->anonymous) ? $idx : $fld->name;
				$out[$ix] = $fld->value;
			}
			return $out;
		}
	}
}
?>