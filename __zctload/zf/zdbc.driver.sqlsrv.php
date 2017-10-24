<?php
class ZDBCDriverSqlsrv extends ZDBC
{
	protected
		$__drv_dclsname				= null;
	protected static
		$__drv_name					= 'M$ SQLSRV',
		$__drv_family				= 'mssql',
		$__drv_classname			= __class__,
		$__drv_required_zdbc_ver	= array('2','>='),
		$__drv_configutaion			= array('host','user','pass','base','init','chrx'),
		$__drv_required_set			= array('base'),
		$__drv_required_notblank	= array('base'),
		$__drv_str_quote			= '',
		$__drv_obj_quote			= '[]',
		$_sth_bounds				= null,
		$_sth_ready					= false;
	##
	public function __construct(&$x)
	{
		$this->__drv_dclsname = self::$__drv_classname;
		$this->__drv_caps = parent::c_prepared+parent::c_transactions;
		try{parent::__construct($x);}
		catch(Exception $e){$this->error($e->getMessage());}
		if($this->is_dbh() and $this->check_config($x))
			$this->connect($x);
	}
	private function connect(&$cfg)
	{
		$srv = (!empty($cfg['host'])) ? $cfg['host'] : '(local)';
		$ci = array('Database'=>$cfg['base']);
		if(!empty($cfg['user']))
			$ci['UID'] = $cfg['user'];
		if(!empty($cfg['pass']))
			$ci['PWD'] = $cfg['pass'];
		if(!empty($cfg['chrx']))
			$ci['CharacterSet'] = $cfg['chrx'];
		$this->dbh = sqlsrv_connect($srv,$ci) or $this->error("Can't connect to database server.");
		if(is_resource($this->dbh))
			$this->is_connected = true;
		if(!empty($cfg['init']))
			sqlsrv_query($this->dbh,$cfg['init']) or $this->error("Can't run init-connect query!");
	}
	protected function _query()
	{
		return sqlsrv_query($this->dbh,$this->query);
	}
	protected function _exec()
	{
		return (sqlsrv_query($this->dbh,$this->query)) ? true : false;
	}
	protected function _fetch($num=false)
	{
		if(is_resource($this->sth))
			$src =& $this->sth;
		else
			$src =& $this->rsh;
		return sqlsrv_fetch_array($src,($num)?SQLSRV_FETCH_NUMERIC:SQLSRV_FETCH_ASSOC);
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
		if(is_resource($this->sth))
			$src =& $this->sth;
		else
			$src =& $this->rsh;
		return sqlsrv_num_rows($src);
	}
	protected function _aff_rows()
	{
		if(is_resource($this->sth))
			$src =& $this->sth;
		else
			$src =& $this->rsh;
		return sqlsrv_rows_affected($src);
	}
	protected function _last_id()
	{
		list($x) = sqlsrv_fetch_array(sqlsrv_query($this->dbh,'SELECT SCOPE_IDENTITY()'),SQLSRV_FETCH_NUMERIC);
		return $x;
	}
	protected function _prepare()
	{
		## TODO: DEFER IN DEFER LAL!
		$this->_sth_bounds = null;
		$this->_sth_ready = false;
		if($stx = sqlsrv_prepare($this->dbh,$this->query))
		{
			sqlsrv_free_stmt($stx);
			return true;
		}
		return false;
	}
	protected function _execute(&$p)
	{
		if(!is_array($this->_sth_bounds) or !count($this->_sth_bounds))
			$this->_sth_bounds = array();
		foreach($p as $k => &$v)
			$this->_sth_bounds[$k] = &$v;
		if(!$this->_sth_ready)
		{
			$this->sth = sqlsrv_prepare($this->dbh,$this->query,$this->_sth_bounds);
			$this->_sth_ready = true;
		}
		#return sqlsrv_execute($this->sth);
		#debug_zval_dump($this->query,$this->_sth_bounds);
		$x = sqlsrv_execute($this->sth);
		debug_zval_dump($x,sqlsrv_errors()[0]['message']);
		exit;
	}
	protected function _begin()
	{
		return sqlsrv_begin_transaction($this->dbh);
	}
	protected function _rollback()
	{
		return sqlsrv_rollback($this->dbh);
	}
	protected function _commit()
	{
		return sqlsrv_commit($this->dbh);
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
		return (bool)sqlsrv_num_fields($this->rsh);#!is_bool($this->rsh);
	}
	protected function _escape($s)
	{
		list($x) = unpack('H*0',$s);
		return '0x'.strtoupper($x);
	}
	protected function _wrap($p,$obj=false)
	{
		$p = trim($p);
		if($obj)
		{
			if(strpos($p,'[') or strpos($p,']'))
				$p = preg_replace('/[\[\]]/','',$p);
			if(strpos($p,'[') === false)
				$p = '['.$p;
			if(strpos($p,']') === false)
				$p .= ']';
		}
		else
		{
			if(!preg_match('/^[\'"].+?[\'"]$/',$p)) ## if not 'param' or "param"
			{
				if((strpos($p,'"') and strpos($p,'"')+1 < strlen($p)) or (strpos($p,"'") and strpos($p,"'")+1 < strlen($p))) # ' or " in middle-string
					$p = $this->_escape($p);
				elseif($x = strpos($p,"'")) # '
				{
					if($x == strlen($p)-1)
						$p = "'$p";
					else
						$p = "$p'";
				}
				elseif($x = strpos($p,'"')) # "
				{
					if($x == strlen($p)-1)
						$p = "\"$p";
					else
						$p = "$p\"";
				}
				/*$p = preg_replace('/[\'"]/','',$p);
					if(strpos($p,"'") === false and strpos($p,'"') === false)
					$p = '['.$p;
				if(strpos($p,']') === false)
					$p .= ']';*/
			}
		}
		return $p;
	}
	protected function _limit($q,$l,$o=0,$order=null)
	{
		throw new Exception('ZDBCDriver::_limit(): not implemented due to large amount of work required.',14);
	}
	protected function _reset_statement()
	{
		if(is_resource($this->sth))
			return sqlsrv_cancel($this->sth);
		else
			return false;
	}
	protected function _close_statement()
	{
		if(is_resource($this->sth))
			return sqlsrv_free_stmt($this->sth);
		else
			return false;
	}
	protected function _free_result()
	{
		if(is_resource($this->rsh))
			return sqlsrv_free_stmt($this->rsh);
		else
			return false;
	}
	protected function _close()
	{
		if($this->dbh)
			sqlsrv_close($this->dbh);
	}
	protected function _error_string()
	{
		$e = sqlsrv_errors();
		return $e[count($e)-1]['message'];
	}
	protected function _error_code()
	{
		$e = sqlsrv_errors();
		return $e[count($e)-1]['code'];
	}
	protected static function _backend_available(){return function_exists('sqlsrv_connect');}
}
?>