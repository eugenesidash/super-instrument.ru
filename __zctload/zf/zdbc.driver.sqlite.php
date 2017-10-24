<?php
class ZDBCDriverSqlite extends ZDBC
{
	protected
		$__drv_dclsname				= null;
	protected static
		$__drv_name					= 'SQLite',
		$__drv_family				= 'sqlite',
		$__drv_classname			= __class__,
		$__drv_required_zdbc_ver	= array('2','>='),
		$__drv_configutaion			= array('base','prms','init'),
		$__drv_required_set			= array('base'),
		$__drv_required_notblank	= array('base'),
		$__drv_str_quote			= "'",
		$__drv_obj_quote			= '';
	##
	public function __construct(&$x)
	{
		$this->__drv_dclsname = self::$__drv_classname;
		$this->__drv_caps = parent::c_mfetch+parent::c_cfetch;
		try{parent::__construct($x);}
		catch(Exception $e){$this->error($e->getMessage());}
		if($this->is_dbh() and $this->check_config($x))
			$this->connect($x);
	}
	private function connect(&$cfg)
	{
		$perms = (!empty($cfg['prms'])) ? $cfg['prms'] : 0666;
		$this->dbh = new SQLiteDatabase($cfg['base'],$perms,$e) or $this->error("Can't access SQLite database.",$e);
		if(!$e)
			$this->is_connected = true;
		if(!empty($cfg['init']))
			$this->dbh->queryExec($cfg['init'],$e) or $this->error("Can't run init-connect query!",$e);
	}
	protected function _query()
	{
		return $this->dbh->query($this->query->q);
	}
	protected function _exec()
	{
		return $this->dbh->queryExec($this->query->q);
	}
	protected function _fetch($num=false)
	{
		return $this->rsh->fetch(($num)?SQLITE_NUM:SQLITE_ASSOC);
	}
	protected function _fetch_all($num=false)
	{
		return $this->rsh->fetchAll(($num)?SQLITE_NUM:SQLITE_ASSOC);
	}
	protected function _fetch_col($offset=0)
	{
		return $this->rsh->column($offset);
	}
	protected function _num_rows()
	{
		return ($this->rsh) ? $this->rsh->numRows() : false;
	}
	protected function _aff_rows()
	{
		return $this->dbh->changes();
	}
	protected function _last_id()
	{
		return $this->dbh->lastInsertRowid();
	}
	protected function _prepare()
	{
		## N/A
	}
	protected function _execute(&$p)
	{
		## N/A
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
		return (!is_bool($this->rsh)) ? $this->rsh->valid() : false;
	}
	protected function _escape($s)
	{
		return sqlite_escape_string($s);
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
		$this->query->sth_end();
		return true;
	}
	protected function _close_statement()
	{
		$this->sth = null;
		return true;
	}
	protected function _free_result()
	{
		$this->rsh = null;
		return true;
	}
	protected function _close()
	{
		$this->dbh = null;
	}
	protected function _error_string()
	{
		return sqlite_error_string($this->dbh->lastError());
	}
	protected function _error_code()
	{
		return $this->dbh->lastError();
	}
	protected static function _backend_available(){return class_exists('SQLiteDatabase');}
}
?>