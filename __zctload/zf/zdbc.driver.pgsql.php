<?php
class ZDBCDriverPgsql extends ZDBC
{
	protected
		$__drv_dclsname				= null;
	protected static
		$__drv_name					= 'PostgreSQL',
		$__drv_family				= 'pgsql',
		$__drv_classname			= __class__,
		$__drv_required_zdbc_ver	= array('2','>='),
		$__drv_configutaion			= array('host','user','pass','base','pers','init','chrx'),
		$__drv_required_set			= array('host','user','pass','base'),
		$__drv_required_notblank	= array('host','user','base'),
		$__drv_str_quote			= "'",
		$__drv_obj_quote			= "'";
	##
	public function __construct(&$x)
	{
		$this->__drv_dclsname = self::$__drv_classname;
		$this->__drv_caps = parent::c_mfetch+parent::c_cfetch+parent::c_persistent+parent::c_prepared;
		try{parent::__construct($x);}
		catch(Exception $e){$this->error($e->getMessage());}
		if($this->is_dbh() and $this->check_config($x))
			$this->connect($x);
	}
	private function connect(&$cfg)
	{
		$dsn = "host={$cfg['host']} ";
		if(!empty($cfg['port']))
			$dsn .= "port={$cfg['port']} ";
		if($cfg['user'] != $cfg['base'])
			$dsn .= "user={$cfg['user']} ";
		if(!empty($cfg['pass']))
			$dsn .= "password={$cfg['pass']} ";
		if(!empty($cfg['chrx']))
			$dsn .= "options='--client_encoding={$cfg['chrx']}' ";
		##
		$this->dbh = (!empty($cfg['pers'])) ? pg_pconnect($dsn) or $this->error("Can't connect to database server.") : pg_connect($dsn) or $this->error("Can't connect to database server.");
		##
		if(!empty($cfg['init']))
			pg_exec($this->dbh,$cfg['init']);
	}
	protected function _query()
	{
		return pg_query($this->dbh,$this->query);
	}
	protected function _exec()
	{
		return pg_exec($this->dbh,$this->query);
	}
	protected function _fetch($num=false)
	{
		return ($num) ? pg_fetch_row($this->rsh) : pg_fetch_assoc($this->rsh);
	}
	protected function _fetch_all($num=false)
	{
		if($num)
		{
			$out = array();
			while($r = $this->_fetch(true))
				$out[] = $r;
			return $out;
		}
		else
			return pg_fetch_all($this->rsh);
	}
	protected function _fetch_col($offset=0)
	{
		return pg_fetch_all_columns($this->rsh,$offset);
	}
	protected function _num_rows()
	{
		return (is_resource($this->rsh)) ? pg_num_rows($this->rsh) : false;
	}
	protected function _aff_rows()
	{
		return (is_resource($this->rsh)) ? pg_affected_rows($this->rsh) : false;
	}
	protected function _last_id()
	{
		return pg_last_oid($this->rsh);
	}
	protected function _prepare()
	{
		$c = 0;
		$q = preg_replace_callback('/?/',create_function('$m',"\$c++;return '\$$c';"),$this->query);
		return pg_prepare($this->dbh,dechex(crc32($this->query)),$q);
	}
	protected function _execute(&$p)
	{
		if($this->rsh = pg_execute($this->dbh,dechex(crc32($this->query)),$p))
			return true;
		else
			return $this->rsh;
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
		return is_resource($this->rsh);
	}
	protected function _escape($s)
	{
		return pg_escape_string($this->dbh,$s);
	}
	protected function _wrap($p,$obj)
	{
		if(version_compare(PHP_VERSION,'5.4.4','>='))
			return ($obj) ? pg_escape_identifier($this->dbh,$s) : pg_escape_literal($this->dbh,$s);
		elseif($obj)
			return '"'.str_replace('"','""',$p).'"';
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
		return $this->_free_result();
	}
	protected function _close_statement()
	{
		$this->sth = null;
		return pg_exec($this->dbh,'DEALLOCATE '.$this->_wrap(dechex(crc32($this->query))),true);
	}
	protected function _free_result()
	{
		return (is_resource($this->rsh)) ? pg_free_result($this->rsh) : false;
	}
	protected function _close()
	{
		pg_close($this->dbh);
	}
	protected function _error_string()
	{
		if($this->_is_result())
			return pg_result_error($this->rsh);
		else
			return pg_last_error($this->dbh);
	}
	protected function _error_code()
	{
		return 0;
	}
	protected static function _backend_available(){return function_exists('pg_connect');}
}
?>