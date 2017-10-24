<?php
class ZDBCDriverMysql extends ZDBC
{
	protected
		$__drv_dclsname				= null;
	protected static
		$__drv_name					= 'MySQL',
		$__drv_family				= 'mysql',
		$__drv_classname			= __class__,
		$__drv_required_zdbc_ver	= array('2','>='),
		$__drv_configutaion			= array('host','user','pass','base','pers','init','chrx'),
		$__drv_required_set			= array('host','user','pass','base'),
		$__drv_required_notblank	= array('host','user','base'),
		$__drv_str_quote			= "'",
		$__drv_obj_quote			= '`';
	##
	public function __construct(&$x)
	{
		$this->__drv_dclsname = self::$__drv_classname;
		$this->__drv_caps = parent::c_persistent+parent::c_transactions;
		try{parent::__construct($x);}
		catch(Exception $e){$this->error($e->getMessage());}
		if($this->is_dbh() and $this->check_config($x))
			$this->connect($x);
	}
	private function connect(&$cfg)
	{
		$this->dbh = (!empty($cfg['pers'])) ? mysql_pconnect($cfg['host'],$cfg['user'],$cfg['pass']) or $this->error("Can't connect to database server.") : mysql_connect($cfg['host'],$cfg['user'],$cfg['pass']) or $this->error("Can't connect to database server.");
		if(is_resource($this->dbh))
			$this->is_connected = true;
		mysql_select_db($cfg['base'],$this->dbh) or $this->error("Can't switch to database!");
		if(!empty($cfg['chrx']))
			mysql_query("SET NAMES {$cfg['chrx']}",$this->dbh) or $this->error("Can't set charset!");
		if(!empty($cfg['init']))
			mysql_query($cfg['init'],$this->dbh) or $this->error("Can't run init-connect query!");
	}
	protected function _query()
	{
		return mysql_query($this->query,$this->dbh);
	}
	protected function _exec()
	{
		return (mysql_query($this->query,$this->dbh)) ? true : false;
	}
	protected function _fetch($num=false)
	{
		return ($num) ? mysql_fetch_row($this->rsh) : mysql_fetch_assoc($this->rsh);
	}
	protected function _fetch_all($num=false)
	{
		## N/A
	}
	protected function _fetch_col($offset=0)
	{
		## N/A
	}
	protected function _num_rows()
	{
		return (is_resource($this->rsh)) ? mysql_num_rows($this->rsh) : false;
	}
	protected function _aff_rows()
	{
		return (is_resource($this->rsh)) ? mysql_affected_rows($this->rsh) : false;
	}
	protected function _last_id()
	{
		return mysql_insert_id($this->dbh);
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
		if(mysql_query('SET autocommit=0',$this->dbh))
			return mysql_query('BEGIN',$this->dbh);
		else
			return false;
	}
	protected function _rollback()
	{
		$r = mysql_query('ROLLBACK',$this->dbh);
		mysql_query('SET autocommit=1',$this->dbh);
		return $r;
	}
	protected function _commit()
	{
		$r = mysql_query('COMMIT',$this->dbh);
		mysql_query('SET autocommit=1',$this->dbh);
		return $r;
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
		return is_resource($this->rsh);
	}
	protected function _escape($s)
	{
		return mysql_real_escape_string($s,$this->dbh);
	}
	protected function _wrap($p,$obj)
	{
		if($obj)
			return "`$p`";
		else
			return "'".$this->_escape($p)."'";
	}
	protected function _limit($q,$l,$o=0,$order=null)
	{
		return "$q LIMIT $o,$l";
	}
	protected function _reset_statement()
	{
		$this->query = $this->query_src;
		return true;
	}
	protected function _close_statement()
	{
		$this->sth = null;
		return true;
	}
	protected function _free_result()
	{
		if(is_resource($this->rsh))
			return mysql_free_result($this->rsh);
	}
	protected function _close()
	{
		mysql_close($this->dbh);
	}
	protected function _error_string()
	{
		return mysql_error($this->dbh);
	}
	protected function _error_code()
	{
		return mysql_errno($this->dbh);
	}
	protected static function _backend_available(){return function_exists('mysql_connect');}
}
?>