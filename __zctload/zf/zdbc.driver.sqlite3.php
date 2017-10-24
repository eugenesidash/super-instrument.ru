<?php
class ZDBCDriverSqlite3 extends ZDBC
{
	protected
		$__drv_dclsname				= null;
	protected static
		$__drv_name					= 'SQLite 3',
		$__drv_family				= 'sqlite',
		$__drv_classname			= __class__,
		$__drv_required_zdbc_ver	= array('2','>='),
		$__drv_configutaion			= array('base','ifnx','init','readonly'),
		$__drv_required_set			= array('base'),
		$__drv_required_notblank	= array('base'),
		$__drv_str_quote			= "'",
		$__drv_obj_quote			= '`';
	##
	public function __construct(&$x)
	{
		$this->__drv_dclsname = self::$__drv_classname;
		$this->__drv_caps = parent::c_prepared+parent::c_named;
		try{parent::__construct($x);}
		catch(Exception $e){$this->error($e->getMessage());}
		if($this->is_dbh() and $this->check_config($x))
			$this->connect($x);
	}
	private function connect(&$cfg)
	{
		$f = (!empty($cfg['readonly'])) ? SQLITE3_OPEN_READONLY : SQLITE3_OPEN_READWRITE;
		if(!isset($cfg['ifnx']) or $cfg['ifnx'])
			$f += SQLITE3_OPEN_CREATE;
		try
		{
			$this->dbh = new SQLite3($cfg['base'],$f);
			$this->is_connected = true;
		}
		catch(Exception $x){$this->error("Can't access SQLite database.",$x->getMessage());}
		if(!empty($cfg['init']))
			$this->dbh->exec($cfg['init']) or $this->error("Can't run init-connect query!",$this->dbh->lastErrorMsg());
	}
	protected function _query()
	{
		return $this->dbh->query($this->query);
	}
	protected function _exec()
	{
		return @$this->dbh->exec($this->query);
	}
	protected function _fetch($num=false)
	{
		return $this->rsh->fetchArray(($num)?SQLITE3_NUM:SQLITE3_ASSOC);
	}
	protected function _fetch_all($num=false)
	{
		$out = array();
		while($r = $this->_fetch($num))
			$out[] = $r;
		return $out;
	}
	protected function _fetch_col($offset=0)
	{
		## N/A
	}
	protected function _num_rows()
	{
		return ($this->rsh->numColumns() and $this->rsh->columnType(0) != SQLITE3_NULL) ? 1 : 0;
	}
	protected function _aff_rows()
	{
		return $this->dbh->changes();
	}
	protected function _last_id()
	{
		return $this->dbh->lastInsertRowID();
	}
	protected function _prepare()
	{
		return $this->dbh->prepare($this->query);
	}
	protected function _execute(&$p)
	{
		foreach($p as $k => $v)
		{
			if(!$this->is_named)
				$k = intval($k)+1;
			if(is_null($v))
				$t = SQLITE3_NULL;
			elseif(is_int($v))
				$t = SQLITE3_INTEGER;
			elseif(is_float($v))
				$t = SQLITE3_FLOAT;
			else
				$t = SQLITE3_TEXT;
			$this->sth->bindValue($k,$v,$t);
		}
		## TODO: BIND!
		$this->rsh = $this->sth->execute();
		if(!$this->rsh)
		{
			$this->rsh = null;
			return false;
		}
		return true;
	}
	protected function _begin()
	{
		## N/A
	}
	protected function _rollback()
	{
		## N/A
	}
	protected function _commit()
	{
		## N/A
	}
	protected function _sp_init($name)
	{
		## N/A
	}
	protected function _sp_exec(&$in,&$out)
	{
		## N/A
	}
	protected function _is_result()
	{
		return $this->rsh->numColumns();
	}
	protected function _escape($s)
	{
		return $this->dbh->escapeString($s);
	}
	protected function _wrap($p,$obj=false)
	{
		if($obj)
			return '`'.$p.'`';
		else
			return "'".$this->_escape($p)."'";
	}
	protected function _limit($q,$l,$o=0,$order=null)
	{
		$o = ($o) ? ' OFFSET '.$o : '';
		return "$q LIMIT $l$o";
	}
	protected function _reset_statement()
	{
		return $this->sth->reset();
	}
	protected function _close_statement()
	{
		if($this->sth)
			return @$this->sth->close();
	}
	protected function _free_result()
	{
		if($this->rsh)
			@$this->rsh->finalize();
	}
	protected function _close()
	{
		if($this->dbh)
			$this->dbh->close();
	}
	protected function _error_string()
	{
		if($this->dbh)
			return $this->dbh->lastErrorMsg();
		else
			return 'Unknown error, no database opened.';
	}
	protected function _error_code()
	{
		if($this->dbh)
			return $this->dbh->lastErrorCode();
		else
			return -13;
	}
	protected static function _backend_available(){return class_exists('SQLite3');}
}
?>