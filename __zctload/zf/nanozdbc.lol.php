<?php
class NanoZDBC
{
	## nanozdbc, lol! [r17]
	const
		TREE_NODES_FIELD = 0;
	private
		$dbc	= null,
		$q		= null,
		$qr		= null,
		$st		= null;
	public function __construct($cfg)
	{
		$this->dbc = new mysqli($cfg['host'],$cfg['user'],$cfg['pass'],$cfg['base']) or die('DBC error!');
		if(!empty($cfg['init']))
			$this->dbc->query($cfg['init']);
	}
	public function query($q,$p=array(),$r=-3,$m=false)
	{
		if($this->st instanceof mysqli_stmt)
			@$this->st->close();
		if($this->qr instanceof mysqli_result)
			@$this->qr->free();
		$this->st = $this->qr = $this->q = null;
		if(!$q)
			die('nanozdbc: no query!');
		if($this->predict_ps($q))
			$this->qr = $this->prepare($q,$p);
		else
			$this->qr = $this->dbc->query($q);
		return $this->fetch($r,$m);
	}
	private function predict_ps($q)
	{
		$q = preg_replace('/(\'[^\']*?\')/','',$q);
		$q = preg_replace('/("[^"]*?")/','',$q);
		$q = preg_replace('/(\/*(.*?)*\/)/','',$q);
		$q = preg_replace('/--(.*?)$/','',$q);
		##
		return (strpos($q,'?') !== false or strpos($q,':') !== false);
	}
	public function prepare($q,$p=array())
	{
		if($this->st instanceof mysqli_stmt)
			@$this->st->close();
		if($this->qr instanceof mysqli_result)
			@$this->qr->free();
		$this->st = $this->qr = $this->q = null;
		##
		if(!$this->predict_ps($q))
			return false;
		##
		$this->st = $this->dbc->prepare($q);
		if(!$this->st)
			return false;
		$this->q = $q;
		if(count($p))
			return $this->execute($p);
		else
			return true;
	}
	private function parse_statement($p)
	{
		$q = $this->q;
		$NO_NAMED_PH = true; # for mysqli
		##
		$idxd = (strpos($q,'?') !== false and strpos($q,':') === false);
		$named = (strpos($q,'?') === false and strpos($q,':') !== false);
		##
		$vk = array_keys($p);
		$p_idxd = null;
		foreach($vk as $k => $v)
			$p_idxd = ($k == $v) ? true : false;
		##
		if($p_idxd and $named or !$p_idxd and $idxd)
			die("nanozdbc: wrong statement with query [$q]!");
		##
		if($named)
		{
			if(substr_count($q,':') != count($p))
				die("nanozdbc: params count mismatch for query [$q]!");
			foreach($vk as $k => $v)
				if(!preg_match("/:$k/",$q))
					die("nanozdbc: named substitue [$k] was not found in query [$q]!");
		}
		else
		{
			$qc = substr_count($q,'?');
			$pc = count($p);
			if($qc != $pc)
				die("nanozdbc: params count mismatch for query [$q]: $qc vs $pc!");
		}
		##
		if($NO_NAMED_PH and $named)
		{
			$c = 1;
			$z = $q;
			foreach($vk as $k => $v)
				$z = preg_replace("/:$k/",'$'.$k.'$'.$c++.'$',$z);
			##
			preg_match_all('/\$(.+?)\$([\d]+)\$/',$z,$mx);
			$np = array();
			foreach($mx as $mm)
			{
				$np[$mm[2]] = $p[$mm[1]];
				$q = preg_replace("/:{$mm[1]}/",'?',$q);
			}
			$p = $np;
			unset($np,$mx);
			ksort($p);
			reset($p);
		}
		##
		return true;
	}
	public function execute($p)
	{
		if(!$this->st)
			return false;
		$this->parse_statement($p);
		##
		$argc = '';
		$argv = array();
		$c = 1;
		foreach($p as $i => $x)
		{
			if(is_float($x))
				$argc .= 'd';
			elseif(is_int($x))
				$argc .= 'i';
			else
				$argc .= 's';
			$argv[$c++] =& $p[$i];
		}
		$argv[0] = $argc;
		ksort($argv);
		reset($argv);
		if(!call_user_func_array(array($this->st,'bind_param'),$argv))
			die('nanozdbc: failed to bind params!');
		##
		return $this->st->execute();
	}
	public function fetch($r=-2,$m=false)
	{
		if($this->st instanceof mysqli_stmt)
		{
			if($r == -3)
				return $this->st->affected_rows;
			##
			$refs = array();
			$meta = $this->st->result_metadata();
			$fx = array();
			$c = 0;
			while($f = $meta->fetch_field())
			{
				if($r > -2)
					$fx[] = &$refs[$c++];
				else
					$fx[] = &$refs[$f->name];
			}
			if(count($fx) < $r)
				return false;
			##
			if(!call_user_func_array(array($this->st,'bind_result'),$fx))
				die('nanozdbc: failed to bind result!');
			##
			if($m)
			{
				$out = array();
				while($this->st->fetch())
				{
					if($r > -1 and isset($refs[$r]))
						$out[] = $refs[$r];
					else
					{
						$ox = array();
						foreach($refs as $k => $v)
							$ox[$k] = $v;
						$out[] = $ox;
					}
				}
				$this->st->close();
				return $out;
			}
			else
			{
				if(!$this->st->fetch())
				{
					$this->st->close();
					return false;
				}
				##
				if($r > -1 and !isset($refs[$r]))
					return false;
				return ($r > -1) ? $refs[$r] : $refs;
			}
		}
		else
		{
			if($r > -3 and $this->qr)
			{
				if($r > -2)
				{
					if($r > -1)
					{
						if($m)
						{
							$x = $this->qr->fetch_all(MYSQLI_NUM);
							if(!isset($x[0][$r]))
								return false;
							$z = array();
							foreach($x as $y)
								$z[] = $y[$r];
							return $z;
						}
						$rx = $this->qr->fetch_row();
						if(isset($rx[$r]))
							return $rx[$r];
						else
							return false;
					}
					else
						return ($m) ? $this->qr->fetch_all(MYSQLI_NUM) : $this->qr->fetch_row();
				}
				else
					return ($m) ? $this->qr->fetch_all(MYSQLI_ASSOC) : $this->qr->fetch_assoc();
			}
			else
				return $this->qr;
		}
	}
	public function last()
	{
		return $this->dbc->insert_id;
	}
	public function rows()
	{
		if($this->st instanceof mysqli_stmt)
			return $this->st->num_rows;
		elseif($this->qr instanceof mysqli_result)
			return $this->qr->num_rows;
		else
			return false;
	}
	### X-A
	public function fetchTable($q,$p=null)
	{
		return $this->query($q,$p,-2,true);
	}
	public function fetchRow($q,$p=null)
	{
		return $this->query($q,$p,-2,false);
	}
	public function fetchCol($q,$p=null,$idx=0)
	{
		if($idx < 0)
			$idx = 0;
		return $this->query($q,$p,$idx,true);
	}
	public function fetchOne($q,$p=null,$idx=0)
	{
		if($idx < 0)
			$idx = 0;
		return $this->query($q,$p,$idx,false);
	}
	public function fetchPairs($q,$p=null,$k,$v)
	{
		$out = array();
		$this->query($q,$p);
		while($r = $this->fetch())
			if(isset($r[$k],$r[$v]))
				$out[$r[$k]] = $r[$v];
		return $out;
	}
	public function fetchDict($q,$p=null,$k)
	{
		$out = array();
		$this->query($q,$p);
		while($r = $this->fetch())
			if(isset($r[$k],$r[$v]))
				$out[$r[$k]] = $r;
		return $out;
	}
	private function tree_find_parent(&$rows,&$row,$id_field,$parent_field)
	{
		foreach($rows as $idx => $xref)
		{
			$f_moved = false;
			if(isset($xref[self::TREE_NODES_FIELD]))
			{
				if(!is_array($xref[self::TREE_NODES_FIELD]))
					trigger_error('NanoZDBC::tree_find_parent(): naming conflict is row! Name ['.self::TREE_NODES_FIELD.'] exists in selection.',E_USER_ERROR);
				if(count($xref[self::TREE_NODES_FIELD]))
					$f_moved = $this->tree_find_parent($rows[$idx][self::TREE_NODES_FIELD],$row,$id_field,$parent_field);
				if($f_moved)
					return $f_moved;
			}
			if($row[$parent_field] === $xref[$id_field])
			{
				if(!isset($rows[$idx][self::TREE_NODES_FIELD]))
					$rows[$idx][self::TREE_NODES_FIELD] = array();
				$rows[$idx][self::TREE_NODES_FIELD][] = $row;
				return true;
			}
		}
		return false;
	}
	public function fetchTree($q,$p=null,$id_field,$parent_field)
	{
		$rows = $this->query($q,$p,-2,true);
		if(!$rows)
			return $rows;
		##
		foreach($rows as $idx => $row)
		{
			if($row[$parent_field])
			{
				if($this->tree_find_parent($rows,$row,$id_field,$parent_field))
				{
					unset($rows[$idx]);
					continue;
				}
			}
		}
		return $rows;
	}
	public function exec($q,$p=null)
	{
		if($this->query($q,$p))
			return ($this->st instanceof mysqli_stmt) ? $this->st->affected_rows : $this->dbc->affected_rows;
		else
			return false;
	}
	public function error()
	{
		return ($this->st instanceof mysqli_stmt) ? $this->st->error : $this->dbc->error;
	}
}
?>