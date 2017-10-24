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
		$stored						= false,
		$bind_key					= '',
		$bind_vars					= array(),
		$bind_data					= array();
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
		$this->dbh = new mysqli($cfg['host'],$cfg['user'],$cfg['pass'],$cfg['base']);
		if(mysqli_connect_errno())
			$this->error("Can't connect to database server.",mysqli_connect_error());
		if(!empty($cfg['chrx']))
			$this->dbh->set_charset($cfg['chrx']) or $this->error("Can't set charset!");
		if(!empty($cfg['init']))
			$this->query($cfg['init']) or $this->error("Can't run init-connect query!");
	}
	protected function _query()
	{
		$this->stored = false;
		return $this->dbh->real_query($this->query);
	}
	protected function _exec()
	{
		$this->stored = false;
		$res = $this->dbh->real_query($this->query);
		$xr = $this->dbh->use_result();
		$xr->free();
		return $res;
	}
	protected function _fetch($num=false)
	{
		$this->__mysqli52prefetch();
		if($this->sth instanceof mysqli_stmt)
		{
			## statement
			if(!$this->stored)
				return false;
			##
			$bk = crc32($this->query.'::'.$this->query_src.'::'.$num);
			if($this->bind_key != $bk)
			{
				## Public contribution by nieprzeklinaj@gmail.com, 10x 2 him --M3956
				$this->bind_vars = $this->bind_data = array();
				$meta = $this->sth->result_metadata();
				$banal_iterator = 0;
				while($field = $meta->fetch_field())
				{
					$this->bind_vars[] =& $this->bind_data[(!$num)?$field->name:$banal_iterator];
					$banal_iterator++;
				}
				$meta->free();
				call_user_func_array(array($this->sth,'bind_result'),$this->bind_vars);
				$this->bind_key == $bk;
			}
			$state = $this->sth->fetch();
			if($state !== true) # false[error]/null[no_more_rows]
				return false;
			/*elseif($state === false)
				throw new Exception('When trying to fetch result from statement.',15);*/
			$result = array();
			foreach($this->bind_data as $k => $v)
				$result[$k] = $v;
			return $result;
		}
		elseif($this->rsh instanceof mysqli_result)
		{
			$out = ($num) ? $this->rsh->fetch_row() : $this->rsh->fetch_assoc();
			if(null === $out)
				$out = false;
			return $out;
		}
		else
			return $this->rsh;
	}
	protected function _fetch_all($num=false)
	{
		##
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
		if($this->sth instanceof mysqli_stmt)
			return $this->sth->insert_id;
		else
			return $this->dbh->insert_id;
	}
	protected function _prepare()
	{
		$this->stored = false;
		return $this->dbh->prepare($this->query);
	}
	protected function _execute(&$p)
	{
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
		if(call_user_func_array(array($this->sth,'bind_param'),$params))
		{
			$e = $this->sth->execute();
			return $e;
		}
		else
			return false;
	}
	protected function _begin()
	{
		if($this->dbh->real_query('SET autocommit=0'))
			return $this->dbh->real_query('BEGIN');
		else
			return false;
	}
	protected function _rollback()
	{
		$r = $this->dbh->real_query('ROLLBACK');
		$this->dbh->real_query('SET autocommit=1');
		return $r;
	}
	protected function _commit()
	{
		$r = $this->dbh->real_query('COMMIT');
		$this->dbh->real_query('SET autocommit=1');
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
	private function __mysqli52prefetch()
	{
		if($this->stored or !$this->_is_result())
			return;
		if($this->sth instanceof mysqli_stmt)
			if($this->sth->store_result())
				$this->stored = true;
		elseif($this->rsh = $this->dbh->store_result())
			$this->stored = ($this->rsh instanceof mysqli_result) ? true : false;
	}
}
?>