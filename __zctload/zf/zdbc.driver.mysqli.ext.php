<?php
class ZDBCDriverMysqli extends ZDBC
{
	protected
		$__drv_dclsname				= null;
	protected static
		$__drv_name					= 'MySQLi',
		$__drv_family				= 'mysql',
		$__drv_classname			= __class__,
		$__drv_required_zdbc_ver	= array('2','>='),
		$__drv_configutaion			= array('host','user','pass','base','init','chrx'),
		$__drv_required_set			= array('host','user','pass','base'),
		$__drv_required_notblank	= array('host','user','base'),
		$__drv_str_quote			= "'",
		$__drv_obj_quote			= '`';
	## PRIVATE ##
	private
		$stored						= false;
	##
	public function __construct(&$x)
	{
		$this->__drv_dclsname = self::$__drv_classname;
		$this->__drv_caps = parent::c_mfetch+parent::c_prepared+parent::c_transactions;
		try{parent::__construct($x);}
		catch(Exception $e){$this->error($e->getMessage());}
		if($this->is_dbh() and $this->check_config($x))
			$this->connect($x);
	}
	private function connect(&$cfg)
	{
		$this->dbh = new mysqli($cfg['host'],$cfg['user'],$cfg['pass'],$cfg['base']);
		if($this->dbh->connect_errno)
			$this->error("Can't connect to database server.",$this->dbh->connect_error());
		if(!empty($cfg['chrx']))
			$this->dbh->set_charset($cfg['chrx']) or $this->error("Can't set charset!");
		if(!empty($cfg['init']))
			$this->exec($cfg['init']) or $this->error("Can't run init-connect query!",$cfg['init']);
	}
	protected function _query()
	{
		$this->stored = false;
		return $this->dbh->query($this->query);
	}
	protected function _exec()
	{
		$this->stored = false;
		$res = $this->dbh->real_query($this->query);
		$xr = @$this->dbh->use_result();
		if($xr instanceof mysqli_result)
			$xr->free();
		return $res;
	}
	protected function _fetch($num=false)
	{
		$this->__mysqli53prefetch();
		if($this->rsh instanceof mysqli_result)
		{
			if(@!empty($this->rsh->field_count))
			{
				$out = ($num) ? $this->rsh->fetch_row() : $this->rsh->fetch_assoc();
				if(null === $out)
					return false;
				return $out;
			}
			else
			{
				$this->free();
				return false;
			}
		}
		else
			return $this->rsh;
	}
	protected function _fetch_all($num=false)
	{
		$this->__mysqli53prefetch();
		if($this->rsh instanceof mysqli_result)
			return $this->rsh->fetch_all(($num)?MYSQLI_NUM:MYSQLI_ASSOC);
		else
			return $this->rsh;
	}
	protected function _fetch_col($offset=0)
	{
		## N/A
	}
	protected function _num_rows()
	{
		if($this->rsh instanceof mysqli_result)
			return $this->rsh->num_rows;
		elseif($this->sth instanceof mysqli_stmt)
			return $this->sth->num_rows;
		else return false;
	}
	protected function _aff_rows()
	{
		if($this->sth instanceof mysqli_stmt)
			return $this->sth->affected_rows;
		else
			return $this->dbh->affected_rows;
	}
	protected function _last_id()
	{
		return ($this->sth instanceof mysqli_stmt) ? $this->sth->insert_id : $this->dbh->insert_id;
	}
	protected function _prepare()
	{
		return $this->dbh->prepare($this->query);
	}
	protected function _execute(&$p)
	{
		$this->stored = false;
		$types = '';
		## Public contribution by nieprzeklinaj@gmail.com, 10x 2 him --M3956
		foreach($p as $param)
		{
			## TODO!
			if(is_int($param))
				$types .= 'i';
			elseif(is_float($param))
				$types .= 'd';
			else $types .= 's';
		}
		$params = array($types);
		foreach($p as $idx => $param)
			$params[] = &$p[$idx];
		unset($types);
		$xp = array();
		foreach($p as $px)
			$xp[] = (is_numeric($px)) ? $px : "'$px'";
		$xp = implode(',',$xp);
		if(call_user_func_array(array($this->sth,'bind_param'),$params))
			return $this->sth->execute();
		else
			return false;
	}
	protected function _begin()
	{
		if($this->dbh->autocommit(false))
		{
			if(version_compare(PHP_VERSION,'5.5.0','>='))
				return $this->dbh->begin_transaction();
			else
				return $this->dbh->real_query('BEGIN');
		}
		else
			return false;
	}
	protected function _rollback()
	{
		$r = $this->dbh->rollback();
		$this->dbh->autocommit(true);
		return $r;
	}
	protected function _commit()
	{
		$r = $this->dbh->commit();
		$this->dbh->autocommit(true);
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
		if($this->derive and $this->is_sth)
			return ($this->sth->field_count) ? true : false;
		else
			return ($this->dbh->field_count) ? true : false;
	}
	protected function _escape($s)
	{
		return $this->dbh->real_escape_string($s);
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
		if($this->sth instanceof mysqli_stmt)
			return $this->sth->reset();
		else
			return false;
	}
	protected function _close_statement()
	{
		if($this->sth instanceof mysqli_stmt)
		{
			if($this->sth->close())
			{
				$this->sth = null;
				return true;
			}
			else
				return false;
		}
		else
			return false;
	}
	protected function _free_result()
	{
		if($this->sth instanceof mysqli_stmt and $this->sth->field_count)
			$this->sth->free_result();
		if($this->rsh instanceof mysqli_result and $this->rsh->field_count)
		{
			$this->rsh->free();
			$this->rsh = null;
		}
		return true;
	}
	protected function _close()
	{
		$this->_free_result();
		$this->_close_statement();
		return $this->dbh->close();
	}
	protected function _error_string()
	{
		if(mysqli_connect_errno())
			return mysqli_connect_error();
		elseif($this->sth instanceof mysqli_stmt and $this->sth->error)
			return $this->sth->error;
		else
			return $this->dbh->error;
	}
	protected function _error_code()
	{
		if(mysqli_connect_errno())
			return mysqli_connect_errno();
		elseif($this->sth instanceof mysqli_stmt and $this->sth->errno)
			return $this->sth->errno;
		else
			return $this->dbh->errno;
	}
	protected static function _backend_available(){return class_exists('mysqli');}
	## PRIVATE ##
	private function __mysqli53prefetch()
	{
		if(!$this->stored and $this->sth instanceof mysqli_stmt)
		{
			if($this->sth->result_metadata() !== null)
				$this->rsh = $this->sth->get_result();
			else
				$this->rsh = true;
			$this->stored = true;
		}
	}
}
?>