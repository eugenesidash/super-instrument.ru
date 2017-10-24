<?php
/**
 * ZCT Media Research Labs Database Connector
 * @package ZDBC
 * @version 2.0-alpha6
 * @author Katta aka M@Dne$$ [M3956] <katta@zct-mrl.com>
 * @copyright Copyleft 2011-2015, Katta aka M@Dne$$ [M3956], all rights fucking up!
 * 
 */

abstract class ZDBC implements Iterator
{
	## Defined ##
	const
		# Return type
		nores	= -4, # non-select query assumed
		none	= -3, # boolean true/false
		assoc	= -2, # array with associative indexes
		num		= -1, # array with iterational indexes
		# Multi-fetch
		free	= 0, # default
		single	= 1, # single row
		multi	= 2, # all result
		##
		debug	= 1, # Debug switch (hardcoded)
		hadh	= 0, # HA debug (hardcoded)
		##
		# Driver capabilities
		c_persistent	= 1, # capable of persistent connections
		c_prepared		= 2, # doesn't need to emulate prepared statements
		c_named			= 4, # doesn't need to emulate named params for prepared statements
		c_mfetch		= 8, # doesn't need to emulate multiple fetch
		c_cfetch		= 16, # doesn't need to emulate whole-column fetch
		c_transactions	= 32, # doesn't need to emulate transactions
		c_storedprocs	= 64, # supports stored procedures
		# Version (for checks)
		version = '2.0-alpha6';
	## Vars ##
	protected static	$_instancez		= null; # instance/instances set [singleton/selector]
	protected
		$dbh			= null,		# [dbh context]current db connection handler (can be emulated)
		$sth			= null,		# [stz context]current statement handler (can be emulated)
		$rsh			= null,		# [rsz context]current query result handler (can be emulated)
		#
		$stz			= null,		# statement context
		$rsz			= null,		# result context
		#
		$stv			= null,		# ?statement link-local vars
		$rsv			= null,		# ?result link-local vars
		#
		$query			= null,		# current working query
		$query_abs		= null,		# current unbuilded abstract query array (fallback/debug)
		$query_src		= null,		# current query source for ps emulation (fallback/debug)
		#
		$is_connected	= false,	# is connection opened, vary errors and stuff
		$is_persistent	= false,	# is connection persistent, doesn't close
		$is_prepared	= false,	# is ps
		$is_checked		= false,	# is query checked
		$is_parsed		= false,	# is ps parsed
		$is_named		= false,	# ps is named
		$trans			= false,	# is in transaction
		$params_count 	= null,		# ps param count
		#
		$derive			= false,	# is statement/result/procedure handler
		$is_sth			= false,	# is statement for derived
		##
		$queries		= 0,		# total number of queries executed
		$__iter			= 0,		# virtual iterator for traversable fetch
		$qcache			= array(),	# all executed queries (for debugging purposes)
		## QUERY BUILDER
		$__querybuilder_param_idx		= null,
		$__querybuilder_param_count_i	= null,
		$__querybuilder_param_count_n	= null;
	/**
	 * Instance/subinstance real constructor
	 * @param arrayref x Reference to array with either connection information or handler references and states for subinstance
	 * @throws Exception
	 * array('drvr'=>'mysql','host'=>'localhost','user'=>'root','pass'=>'root','base'=>'l2jdb','chrx'=>'utf8')
	 */
	public function __construct(&$x)
	{
		## Mode decision
		if(!is_array($x))
			throw new Exception('Must supply array.',0);
		else
		{
			$this->__hadh();
			if(!empty($x['dbh']))
			{
				## ZDBC::subinstance()
				$this->derive = true;
				$__nil = null;
				foreach($x as $k => &$v)
				{
					$this->$k =& $v;
					$v =& $__nil;
				}
			}
			else
			{
				## ZDBC::init()
				try{$this->check_config($x);}
				catch(Exception $e){$this->error($e->getMessage());}
			}
		}
	}
	public function __clone(){trigger_error('Cloning not allowed!',E_USER_ERROR);}
	public function __wakeup(){trigger_error('Unfolding not allowed!',E_USER_ERROR);}
	public function __destruct()
	{
		$this->__hadh();
		if($this->is_sth())
			$this->_close_statement();
		if($this->is_dbh())
			$this->_close();
		## public end
	}
	public function __get($param)
	{
		$param = strtolower($param);
		if(in_array($param,array('q','query')))
			return $this->query;
		elseif(in_array($param,array('r','row','fetch','fetchrow')))
			return $this->fetch();
		elseif(in_array($param,array('rc','nr','rows','nrows','numrows','num_rows','rowcount','row_count','rcount')))
			return $this->_num_rows();
		elseif(in_array($param,array('ar','arows','a_rows','affrows','aff_rows','aff','affected','affectedrows','affected_rows')))
			return $this->_aff_rows();
		elseif(in_array($param,array('id','last','lastid','last_id','insertid','insert_id','lastinsertid','last_insertid','last_insert_id')))
			return $this->_last_id();
		elseif(in_array($param,array('cache','qcache','querycache','query_cache')))
			return $this->qcache;
		elseif(in_array($param,array('err','errstr','stderr','error','error_string')))
			return $this->_error_string();
		elseif(in_array($param,array('errno','ecode','code','error_code')))
			return $this->_error_code();
		else
			return $this->queries; # TODO: if something special required
	}
	/**
	 * Initializes ZDBCDriver object in order to work with database. Not used with singleton/selector, see [instance] method below.
	 * @param array cfg Configuration array
	 * @return object ZDBCDriver instance
	 * @throws Exception
	 */
	public static function init($cfg)
	{
		if(!$cfg or !is_array($cfg) or empty($cfg['drvr']))
			throw new Exception('Wrong configuration supplied!',34);
		##
		$class_name = 'ZDBCDriver'.ucfirst(strtolower($cfg['drvr']));
		$class_file = 'zdbc.driver.'.strtolower($cfg['drvr']).'.php';
		##
		if(!class_exists($class_name,false))
			include $class_file;
		if(!class_exists($class_name,false))
			throw new Exception('ZDBC database driver unit ['.$cfg['drvr'].'] was not found either near ZDBC or in include path! Check your config.',34);
		return new $class_name($cfg);
	}
	/**
	* Singleton/selector implementation for ZDBC
	* @param mixed key Instance ID (string/numeric)
	* @param array cfg Instance configuration
	* @return object Instance for ID given or null on incorrect ID usage
	* @throws Exception
	*/
	public static function instance($key=null,$cfg=null)
	{
		if(!$key)
		{
			if(is_array(self::$_instancez))
				throw new Exception('Must supply instance key!',33);
			if(!self::$_instancez)
			{
				if(!$cfg or !is_array($cfg))
					throw new Exception('Incorrect configuration supplied',33);
				self::$_instancez = self::init($cfg);
			}
			return self::$_instancez;
		}
		else
		{
			if(null === self::$_instancez)
				self::$_instancez = array();
			if(!is_array(self::$_instancez))
				throw new Exception('Using of instance key on singleton is now allowed!',33);
			if(!isset(self::$_instancez[$key]))
			{
				if(!$cfg or !is_array($cfg))
					throw new Exception('Incorrect configuration supplied',33);
				self::$_instancez[$key] = self::init($cfg);
			}
			return self::$_instancez[$key];
		}
	}
	/**
	 * Enumerates all available drivers
	 * @return array List of available drivers IDs
	 */
	public static function list_drivers()
	{
		return array(
			'mysql',
			'mysqli',
			'sqlite',
			'sqlite3',
			'pgsql',
			'mssql',
			'sqlsrv'
			# TODO: all newly created drivers must be added here!
		);
	}
	public static function query_driver_info($drv)
	{
		if(!$drv or !in_array($drv,self::list_drivers()))
			return false;
		##
		$class_name = 'ZDBCDriver'.ucfirst(strtolower($drv));
		$class_file = 'zdbc.driver.'.strtolower($drv).'.php';
		##
		if(!class_exists($class_name,false))
			include $class_file;
		if(!class_exists($class_name,false))
			throw new Exception('ZDBC database driver ['.$drv.'] not found either near ZDBC or in include path! Check your config.',34);
		return array(
			$class_name::$__drv_name, # backend subsystem name
			$class_name::$__drv_family, # database family
			$class_name::_backend_available(), # is backend available on server
			$class_name::$__drv_required_zdbc_ver, # array(zdbc_version_reference,condition to compare)/equals to version_compare's 2nd and 3rd param
			$class_name::$__drv_configutaion, # configuration fields AVAILABLE list
			$class_name::$__drv_required_set, # configuration fields REQUIRED list
			$class_name::$__drv_required_notblank # configuration fields THAT MUST NOT BE BLANK list
		);
	}
	/**
	 * Creates statement/result subinstance
	 * @param boolean sth Create statement instance instead of result set
	 * @return ZDBCDriver Subinstance requested
	 *
	 */
	private function subinstance($sth=false)
	{
		$this->__hadh();
		if($sth)
			## stmt
			$arg = array(
				'dbh' => &$this->dbh,
				'query' => $this->query,
				'query_src' => $this->query_src,
				'query_abs' => $this->query_abs,
				'is_named' => $this->is_named,
				'params_count' => $this->params_count
			);
		else
			## result
			$arg = array(
				'dbh' => &$this->dbh,
				'sth' => &$this->sth,
				'rsh' => &$this->rsh,
				'query' => $this->query_src
			);
		$arg['is_sth'] = $sth;
		return new $this->__drv_dclsname($arg);
	}
	/**
	 * Validates config in order to open connection
	 * @param arrayref cfg Configuration array
	 * @return boolean|null Configuration status
	 * @throws Exception
	 *
	 */
	protected function check_config(&$cfg)
	{
		$this->__hadh();
		##
		$drv = $this->__drv_dclsname;
		if(!version_compare(self::version,$drv::$__drv_required_zdbc_ver[0],$drv::$__drv_required_zdbc_ver[1]))
			throw new Exception("Database driver [{$drv::$__drv_name}] required ZDBC version {$drv::$__drv_required_zdbc_ver[1]} {$drv::$__drv_required_zdbc_ver[0]}!",1);
		if(is_array($drv::$__drv_required_set) and count($drv::$__drv_required_set))
			foreach($drv::$__drv_required_set as $k)
				if(!isset($cfg[$k]))
					throw new Exception("Required by database driver [{$drv::$__drv_name}] configuration parameter [$k] is not set!",1);
		if(is_array($drv::$__drv_required_notblank) and count($drv::$__drv_required_notblank))
			foreach($drv::$__drv_required_notblank as $k)
				if(empty($cfg[$k]))
					throw new Exception("Required by database driver [{$drv::$__drv_name}] to be not blank configuration parameter [$k] is blank!",1);
		if(isset($cfg['pers']))
			$this->is_persistent = true;
		return true;
	}
	############################################################################
	## Highly abstract proxies
	############################################################################
	/**
	 * Executes non-select query
	 * @param string|array q The query
	 * @param array|null p Query parameters
	 * @return boolean Execution result
	 *
	 */
	public function exec($q,$p=null)
	{
		$this->__hadh();
		if($this->query($q,$p,self::nores))
		{
			$this->free();
			return true;
		}
		return false;
	}
	/**
	 * Executes a query and tries to return the whole result set in array
	 * @param string|array q The query
	 * @param array|null p Query parameters
	 * @return array|boolean Array of associative rows, true on non-select, false on error
	 *
	 */
	public function fetchTable($q,$p=null)
	{
		$this->__hadh();
		$out = $this->query($q,$p,self::assoc,self::multi);
		$this->free();
		return $out;
	}
	/**
	 * Executes a query and tries to return single row as associative array
	 * @param string|array q The query
	 * @param array|null p Query parameters
	 * @return array|boolean Associative row, true on non-select, false on error
	 * 
	 */
	public function fetchRow($q,$p=null)
	{
		$this->__hadh();
		$out = $this->query($q,$p,self::assoc,self::single);
		$this->free();
		return $out;
	}
	/**
	 * Executes a query and tries to return single column of whole result set in array
	 * @param string|array q The query
	 * @param array|null p Query parameters
	 * @param int o Column number in result set, default is 0
	 * @return array|boolean Array of cells, true on non-select, false on error
	 * 
	 */
	public function fetchCol($q,$p=null,$o=0)
	{
		$this->__hadh();
		$out = $this->query($q,$p,$o,self::multi);
		$this->free();
		return $out;
	}
	/**
	 * Executes a query and tries to return single cell of result set
	 * @param string|array q The query
	 * @param array|null p Query parameters
	 * @param int o Column number in result set, default is 0
	 * @return string|int|boolean Single cell (typed from backend), true on non-select, false on error
	 * 
	 */
	public function fetchOne($q,$p=null,$o=0)
	{
		$this->__hadh();
		$out = $this->query($q,$p,$o,self::single);
		$this->free();
		return $out;
	}
	/**
	 * Executes a query and tries to return hash table of key-value in 2D-array
	 * @param string|array q The query
	 * @param array|null p Query parameters
	 * @param string k Hash key field name
	 * @param string v Hash value field name
	 * @return array|boolean Hash table, true on non-select, false on error
	 */
	public function fetchPairs($q,$p=null,$k,$v)
	{
		$this->__hadh();
		$r = $this->fetchTable($q,$p);
		if(!$r)
			return $r;
		$out = array();
		foreach($r as $rw)
			if(isset($rw[$k],$rw[$v]))
				$out[$rw[$k]] = $rw[$v];
		return $out;
	}
	/**
	 * Executes a query and tries to return dictionary of key-row in 2D-array
	 * @param string|array q The query
	 * @param array|null p Query parameters
	 * @param string k Dictionary key field name
	 * @return array|boolean Dictionary, true on non-select, false on error
	 */
	public function fetchDict($q,$p=null,$k)
	{
		$this->__hadh();
		$r = $this->fetchTable($q,$p);
		if(!$r)
			return $r;
		$out = array();
		foreach($r as $rw)
			if(isset($rw[$k]))
				$out[$rw[$k]] = $rw;
		return $out;
	}
	/**
	 * Executes a query and tries to return tree of rows in hierarchical array
	 * @param string|array q The query
	 * @param array|null p Query parameters
	 * @param string k ID field name
	 * @param string pk Parent ID field name
	 * @param string|int rk Root ID in parent field
	 * @return array|boolean Tree array, true on non-select, false on error
	 */
	public function fetchTree($q,$p,$k,$pk,$rk=-1)
	{
		$this->__hadh();
		# TODO: must be refactored
		$data = $this->fetchTable($q,$p);
		if(!$data)
			return $data;
		$out = array();
		if($rk === false or $rk === null)
			$rk = -1;
		$out[$rk] = array();
		$parents = array();
		foreach($data as $r)
		{
			if(!isset($r[$k],$r[$pk]))
				break;
			$parents[$r[$k]] = $r[$pk];
		}
		foreach($parents as $key => $parent)
		{
			$x = $parent;
			$chain = array($key,$parent);
			while(isset($parents[$x]) and $x != $rk)
			{
				$x = $parents[$x];
				$chain[] = $x;
			}
			rsort($chain);
			$x =& $out[$rk];
			foreach($chain as $chunk)
			{
				if(!isset($x[$chunk]))
					$x[$chunk] = array();
				$x =& $x[$chunk];
			}
		}
		foreach($data as $r)
		{
			if(!isset($r[$k],$r[$pk]))
				break;
			$node = $this->tree_search($parents,$pk);
			if($node)
			{
				$x =& $out;
				foreach($node as $n)
					$x =& $x[$n];
				$x[$k] = $r;
			}
		}
		return $out;
	}
	/**
	 * Real query execution
	 * @param string|array q The query
	 * @param array|null p Query parameters
	 * @param int o Fetch mode/column number
	 * @param int m Multifetch mode
	 * @return mixed Result
	 * 
	 */
	public function query($q,$p=null,$o=self::none,$m=self::free)
	{
		$this->__hadh();
		$this->allowed('dbh');
		##
		$_n = null;
		$__nil = $_n;
		if($this->stz)
			$this->stz =& $_n;
		if($this->rsz)
			$this->rsz =& $_n;
		$this->rsh =& $__nil;
		$this->rsh = $_n;
		$this->is_checked = false;
		##
		$this->check_query($q,$p);
		##
		if($this->is_prepared and $p)
		{
			$tsth = $this->prepare($q,true);
			if($tsth and $tsth->execute($p))
			{
				$this->query_stat();
				if(self::nores != $o)
					return $tsth->fetch($o,$m);
				else
					return true;
			}
			return false;
		}
		else
		{
			if(self::nores == $o)
				return $this->_exec();
			$this->rsh = $this->_query();
			if($this->rsh)
				return $this->fetch($o,$m);
			return false;
		}
	}
	/**
	 * Prepares a statement
	 * @param string|array q The query
	 * @param boolean checked Has been checked before
	 * @return boolean Result of preparation
	 *
	 */
	public function prepare($q,$checked=false)
	{
		$this->__hadh();
		$this->allowed('dbh','sth');
		##
		if(!$checked)
		{
			$this->is_checked = false;
			$this->check_query($q);
		}
		##
		if($this->is_dbh())
		{
			## main context
			if($this->stz)
			{
				# detach internal statement pointer
				$_n = null;
				$this->stz =& $_n;
			}
			# create new subinstance for statement
			$this->stz = $this->subinstance(true);
			if($this->stz->prepare($q,$checked))
				return $this->stz;
			else
			{
				$this->stz = null;
				return false;
			}
		}
		else
		{
			## statement
			if($this->__drv_caps & self::c_prepared)
				$this->sth = $this->_prepare(); # backend has native support of prepared statements
			else
				$this->sth = 'ZDBCEmulatedPS@'.dechex(crc32($this->query));
			return $this->sth ? true : false;
		}
	}
	/**
	 * Executes a prepared statement
	 * @param array p Parameters for statement
	 */
	public function execute($p)
	{
		$this->__hadh();
		$this->allowed('dbh','sth');
		##
		if($this->is_dbh())
			return ($this->stz) ? $this->stz->execute($p) : false;
		else
		{
			if(
			   (
				!($this->__drv_caps & self::c_prepared) or
				!($this->__drv_caps & self::c_named)
				)
			   and $this->query_src
			   and $this->is_prepared
			)
				$this->query = $this->query_src;
			$this->is_prepared = $this->parse_statement($p);
			if($this->__drv_caps & self::c_prepared)
			{
				if($this->_execute($p))
				{
					if($this->rsz)
						$this->rsz = null;
					return true;
				}
				else
					return false;
			}
			else
			{
				## emulation: finish
				$tq = $this->query;
				$this->query = vsprintf($this->query,$p);
				$this->rsh = $this->_query();
				$this->query = $tq;
				return ($this->rsh) ? true : false;
			}
		}
	}
	/**
	 * Returns result set with optional detach
	 * @param boolean detach Perform detach
	 * @return ZDBCDriver Result set subinstance
	 */
	public function &result($detach=true)
	{
		$this->__hadh();
		$this->allowed('dbh','sth');
		if(!$this->rsz)
			$rsx = $this->subinstance();
		else
		{
			$rsx =& $this->rsz;
			if($detach)
			{
				$_n = null;
				$this->rsz =& $_n;
			}
		}
		return $rsx;
	}
	/**
	 * Real fetch
	 * @param int o Fetch mode/column number
	 * @param int m Multifetch mode
	 * @return mixed Result/state
	 */
	public function fetch($o=self::assoc,$m=self::free)
	{
		$this->__hadh();
		if(!$this->is_rsh())
		{
			if($this->is_dbh() and $this->stz)
				return $this->stz->fetch($o,$m);
			elseif($this->_is_result())
			{
				if(!$this->rsz)
					$this->rsz = ($this->is_dbh() and $this->stz) ? $this->stz->result() : $this->subinstance();
				return ($this->rsz) ? $this->rsz->fetch($o,$m) : false;
			}
			else
				return ($this->rsh) ? true : false;
		}
		## assume result context
		if($m == self::multi)
		{
			if($o <= self::num and ($this->__drv_caps & self::c_mfetch))
				return $this->_fetch_all($o == self::assoc ? false : true);
			elseif($o > self::num and ($this->__drv_caps & self::c_cfetch))
				return $this->_fetch_col($o);
			else
			{
				## legacy emulation
				$out = array();
				while($r = $this->fetch($o,self::single))
					$out[] = $r;
				$this->_free_result();
				return $out;
			}
		}
		else
		{
			$ox = ($o > self::num) ? $o : null;
			if($this->_is_result() and $o > self::none)
			{
				if($o > self::assoc)
				{
					$row = $this->_fetch(true);
					if(!$row or $o == self::num)
						return $row;
					elseif(!isset($row[$ox]))
						return false;
					else
						return $row[$ox];
				}
				else
					return $this->_fetch();
			}
			elseif($this->_is_result())
			{
				if(self::single == $m)
					return true;
				else
					return $this;
			}
			elseif($this->rsh)
				return true;
			else
				return false;
		}
	}
	/**
	 * Begins transaction
	 * @return boolean Transaction started
	 * 
	 */
	public function begin()
	{
		$this->__hadh();
		if($this->trans or !($this->__drv_caps & self::c_transactions))
			return false;
		if($this->_begin())
		{
			$this->trans = true;
			return true;
		}
		return false;
	}
	/**
	 * Rollback transaction
	 * @return boolean Rollback status
	 * 
	 */
	public function rollback()
	{
		$this->__hadh();
		if(!$this->trans)
			return false;
		if($this->__drv_caps & self::c_transactions)
		{
			if($this->_rollback())
			{
				$this->trans = false;
				return true;
			}
		}
		return false;
	}
	/**
	 * Commit transaction
	 * @return boolean Commit status
	 *
	 */
	public function commit()
	{
		$this->__hadh();
		if(!$this->trans)
			return false;
		if($this->__drv_caps & self::c_transactions)
		{
			if($this->_commit())
			{
				$this->trans = false;
				return true;
			}
		}
		return false;
	}
	/**
	 * Executes stored procedure
	 * @param string name Stored procedure name
	 * @param array params Input parameters
	 * @return mixed Output parameters or boolean status
	 *
	 */
	public function proc_exec($name,$params)
	{
		$this->__hadh();
		$this->allowed('dbh');
		if(!($this->__drv_caps & self::c_storedprocs))
			return false;
		## TODO
		if($this->_sp_init($name))
		{
			$out = null;
			if($this->_sp_exec($params,$out))
				return ($out) ? $out : true;
		}
		return false;
	}
	/**
	 * Quotes/escapes string for use with current database
	 * @param string str Raw string
	 * @return Secured string
	 * 
	 */
	public function escape($str)
	{
		$this->__hadh();
		return $this->_escape($str);
	}
	/**
	 * Resets prepared statement
	 * @return boolean Status
	 *
	 */
	public function reset()
	{
		$this->__hadh();
		$this->allowed('dbh','sth');
		if($this->is_sth())
			return $this->_reset_statement();
		elseif($this->is_dbh() and $this->stz)
			return $this->stz->reset();
		else
			return false;
	}
	/**
	 * Releases all resources
	 * @return void
	 *
	 */
	public function free()
	{
		$this->__hadh();
		$this->allowed('dbh','sth','rsh');
		if($this->rsz)
			$this->rsz->free();
		if($this->stz)
			$this->stz->free();
		$this->_free_result();
	}
	/**
	 * Returns query cache
	 * @param int idx Query ordinal
	 * @return mixed Query with ordinal given or all queries in cache
	 * 
	 */
	public function getQCache($idx=-1)
	{
		if($idx > -1)
			return $this->qcache[$idx];
		else
			return $this->qcache;
	}
	/**
	 * @return boolean Is current instance main
	 */
	public function is_dbh()
	{
		return !$this->derive;
	}
	/**
	 * @return boolean Is current instance prepared statement
	 */
	public function is_sth()
	{
		return ($this->derive and $this->is_sth);
	}
	/**
	 * @return boolean Is current instance result set
	 */
	public function is_rsh()
	{
		return ($this->derive and !$this->is_sth);
	}
	/**
	 * Validates incoming query
	 * @param string|array q The query
	 * @param array|null p Query params
	 * @return void
	 * 
	 */
	private function check_query(&$q,$p=null)
	{
		$this->__hadh();
		$this->allowed('dbh','sth');
		if($this->is_checked)
			return;
		$this->is_prepared = false;
		$this->is_named = false;
		if(is_array($q)) # array('select'=>'a','from'=>'b','where'=>array('c','=','d'))
		{
			## Quick check
			if($q === $this->query_abs and $this->is_sth() and ($this->__drv_caps & self::c_prepared))
				$this->_reset_statement();
			elseif($q !== $this->query_abs)
				$this->query_abs = $q;
			## Assume we've got a brand new query, so let's build it and...
			$this->__querybuilder($q,($this->__drv_caps & self::c_prepared ? null : $p));
			$this->query_src = $this->query;
			if($p)
				$this->is_prepared = $this->parse_statement($p);
			## ...close all cursors and destroy statements/results
			if($this->is_sth())
				$this->_close_statement();
			$this->is_checked = true;
			return;
		}
		elseif(is_string($q)) # 'select a from b where c = d'
		{
			## Quick check
			if(
			   ($this->query_src and $q == $this->query_src) or
			   (!$this->query_src and $q == $this->query)
			   )
			{
				$this->is_checked = true;
				return;
			}
			## Assume we've got a brand new query, so...
			$this->query_src = $q;
			## ...close all cursors and destroy statements/results
			if($this->is_sth())
				$this->_close_statement();
			$this->query = $q;
			if(strpos($this->__querybuilder_clear($q),'?') !== false or strpos($this->__querybuilder_clear($q),':') !== false)
				$this->is_prepared = true;
			if($p)
				$this->is_prepared = $this->parse_statement($p);
			$this->is_checked = true;
			return;
		}
		else
			$this->error('ZDBC::check_query(): can\'t work with query type of ['.gettype($q).']!');
	}
	############################################################################
	##                          THE QUERY BUILDER                             ##
	############################################################################
	## TODO: Move out?
	/**
	 * Builds abstract query for target backend
	 * @param array q Abstract query
	 * @param array|null p Query params
	 * @return void
	 *
	 */
	private function __querybuilder(&$q,$p=null)
	{
		$this->__hadh();
		$this->allowed('dbh','sth');
		## Inspired by Matt Mecham & Brandon Farber
		/* EXAMPLES:
			array(
			'select' => '*',
			'from' => 'titles',
			'where' => array('id','=',17)
			)
			array(
			'select' => '*',
			'from' => 'titles t',
			'join' => array('authors a','a.a_id','t.author_id','left'),
			'where' => array(array('t.year','=','2001','and'),array('a.id','>','34')),
			'order' => array(array('t.month','desc'),array('a.id')),
			'limit' => array(0,30)
			)
			array(
			'select' => 'id',
			'from' => 'titles',
			'where' => array('desc','fulltext boolean','infinity'),
			'limit' => 9
			)
			array(
			'insert' => 'titles',
			'values' => array(17,'Ever17','Out of infinity')
			)
			array(
			'insert' => 'titles',
			'values' => array('id'=>17,'name'=>'Ever17','desc'=>'Out of infinity')
			)
			array(
			'update' => 'titles',
			'set' => array('name'=>'Clannad','desc'=>'Its a wonderful life'),
			'where' => array('id','=',4)
			array(
			'delete' => 'titles',
			'where' => array('id','>',4913)
			)
			array(
			'delete' => 'sessions'
			)
			array(
			'truncate' => 'sessions'
			)
		*/
		if(isset($q['__querybuilder_parsed']))
			return true;
		$_q = '';
		$this->__querybuilder_param_idx = 0;
		if(isset($q['select']))
		{
			$this->__querybuilder_unset($q,'select');
			$this->__querybuilder_parse($q['select'],1);
			if(!empty($q['from']))
			{
				$this->__querybuilder_parse($q['from'],2);
				$q['from'] = ' FROM '.$q['from'];
			}
			else
				$q['from'] = '';
			$_q = $q['select'].$q['from'];
			#if(!isset($q['limit']))
			$_q = 'SELECT '.$_q;
			if(isset($q['join']))
			{
				## 'join' => array('authors a','a.a_id','t.author_id','left'),
				## 'join' => array(array('authors a','a.a_id','t.author_id','left'),array('authors a','a.a_id','t.author_id','left')),
				$this->__querybuilder_join($q['join']);
				$_q .= ' '.$q['join'];
			}
		}
		elseif(isset($q['insert'],$q['values']))
		{
			$this->__querybuilder_unset($q,'insert');
			$this->__querybuilder_parse($q['insert'],1);
			if(isset($q['into']))
			{
				$this->__querybuilder_parse($q['into'],2);
				$q['into'] = " ({$q['into']})";
			}
			else
				$q['into'] = '';
			$this->__querybuilder_parse($q['values'],3,$p);
			$_q = "INSERT INTO {$q['insert']}{$q['into']} VALUES ({$q['values']})";
		}
		elseif(isset($q['update'],$q['set']))
		{
			$this->__querybuilder_unset($q,'update');
			$this->__querybuilder_parse($q['update'],1);
			$this->__querybuilder_parse($q['set'],4,$p);
			$_q = "UPDATE {$q['update']} SET {$q['set']}";
			#var_dump($_q);exit;
		}
		elseif(isset($q['delete']) and !empty($q['where']))
		{
			$this->__querybuilder_unset($q,'delete');
			$this->__querybuilder_parse($q['delete'],1);
			$_q = "DELETE FROM {$q['delete']}";
		}
		elseif(isset($q['delete']) or isset($q['truncate']) or isset($q['clear']))
		{
			$tbl = null;
			foreach(array('delete','truncate','clear') as $x)
				if(!empty($q[$x]))
					$tbl = $q[$x];
			$this->__querybuilder_unset($q,'truncate');
			$this->__querybuilder_parse($tbl,1);
			$dx = $this->__drv_dclsname;
			if('mysql' == $dx::$__drv_family)
				$_q = "TRUNCATE $tbl";
			else#if($this->__drv_family == 'sqlite')
				$_q = "DELETE FROM $tbl";
		}
		##
		if(isset($q['where']) and (!isset($q['insert']) or !isset($q['truncate'])))
		{
			$this->__querybuilder_parsecond($q['where'],$p);
			$_q .= ' WHERE '.$q['where'];
		}
		if(isset($q['select']) or isset($q['update']))
		{
			$order = null;
			if(isset($q['order']))
			{
				$this->__querybuilder_order($q['order']);
				$order = " ORDER BY {$q['order']}";
				$_q .= $order;
			}
			if(isset($q['group']))
			{
				$this->__querybuilder_parse($q['group'],1);
				$_q .= " GROUP BY {$q['group']}";
			}
			if(isset($q['limit']))
			{
				if(!isset($q['offset']))
					$q['offset'] = 0;
				$_q = $this->_limit($_q,$q['limit'],$q['offset'],$order);
			}
		}
		##
		$this->query = $_q;
		$q['__querybuilder_parsed'] = true;
		##
		if($this->__querybuilder_param_count_i and $this->__querybuilder_param_count_n)
		{
			throw new Exception('ZDBC::__querybuilder(): both params style found! Not supported!',5);
			return false;
		}
		elseif($this->__querybuilder_param_count_n)
			$this->is_named = true;
		$this->params_count = $this->__querybuilder_param_count_i+$this->__querybuilder_param_count_n;
	}
	/**
	 * Sets query type and unsets the rest
	 * @param arrayref query The query
	 * @param string mode What is remain
	 * @return void
	 *
	 */
	private function __querybuilder_unset(&$query,$mode='select')
	{
		if($mode == 'clear')
			$mode = 'truncate';
		foreach(array('select','insert','update','delete','truncate','clear') as $p)
			if($p != $mode)
				unset($query[$p]);
	}
	/**
	 * Generic recursive parse
	 * @param mixedref node Node to parse
	 * @param int type Node type
	 * @param array|boolean Replacements
	 * @return void
	 */
	private function __querybuilder_parse(&$node,$type=0,$replace=false)
	{
		## Recursive parse obj-lists
		$this->__querybuilder_split($node);
		## Alias?
		if(!is_array($node) and $type == 2 and preg_match('/^([\S]+?)\s+([\S]+?)$/',$node,$xm))
			$node = array($xm[1]=>$xm[2]);
		## Array test
		if(is_array($node))
		{
			## Insert/update?
			if($type > 3)
			{
				$fields = array_keys($node);
				if($fields[0] === 0 and (count($fields) === 1 or $fields[count($fields)-1] === count($fields)-1))
					foreach($node as &$subj)
						$this->__querybuilder_parse($subj,3,$replace);
				else
				{
					foreach($node as $field => $value)
					{
						$k = $field;
						$this->__querybuilder_parse($field,1);
						if($value == '++' or $value == '--')
							$value = "$field {$value[0]} 1";
						else
							$this->__querybuilder_parse($value,0,$replace);
						$node[$k] = "$field = $value";
					}
					$node = implode(', ',$node);
					return;
				}
			}
			elseif($type == 3)
			{
				foreach($node as $k => $v)
				{
					$this->__querybuilder_parse($v,0,$replace);
					$node[$k] = $v;
				}
				$node = implode(', ',$node);
				return;
			}
			## Alias?
			if($type == 2)
			{
				$endp = array();
				foreach($node as $idx => &$subj)
				{
					if(!is_int($idx))
					{
						$this->__querybuilder_parse($idx,1);
						if(!preg_match('/^[\da-z\-_]$/i',$subj))
							throw new Exception('ZDBC::__querybuilder_parse(): invalid alias ['.$subj.']!',14);
						$this->__querybuilder_parse($subj,1);
						$endp[] = "$idx $subj";
					}
				}
				if($endp)
				{
					$node = implode(', ',$endp);
					return;
				}
			}
			foreach($node as &$subj)
				if($type != 3)
					$this->__querybuilder_parse($subj,$type,$replace);
			$node = implode(', ',$node);
			return;
		}
		## Assume string value
		$node = trim($node);
		## Replace mode: exhausted
		if(!$type and is_array($replace) and count($replace) == $this->__querybuilder_param_idx)
		{
			throw new Exception('ZDBC::__querybuilder_parse(): replacement list index out of bounds.',14);
			return false;
		}
		## obj = value?
		if(preg_match('/^([\w]+?)\s*?=\s*?([\w]+?)$/',$node,$x))
		{
			# EXAMPLE:
			## dbo.ssn = blabla
			$this->__querybuilder_parse($x[1],1);
			## [dbo].[ssn]
			$this->__querybuilder_parse($x[2],0,$replace);
			## 'blabla'
			$node = "{$x[1]} = {$x[2]}";
			## [dbo].[ssn] = 'blabla'
		}
		## Prepared statement?
		elseif(($node == '?' or preg_match('/^:\S+?$/',$node)))
		{
			$this->is_prepared = true;
			if($replace)
			{
				$node = $replace[$this->__querybuilder_param_idx++];
				$node = $this->_wrap($node);
			}
			return;
		}
		## Traverse dot-path
		elseif($type and strpos($node,'.') !== false)
		{
			$node = explode('.',$node);
			foreach($node as &$subj)
				$this->__querybuilder_parse($subj,$type);
			$node = implode('.',$node);
		}
		## Is sql function (e.g. `COUNT(*)`) called? (context: object, param)
		elseif(preg_match('/\w+?\(+?.*?\)+?/',$node))
		{
			$node = strtoupper($node);
			return;
		}
		## Wildcard for all?
		elseif($node == '*')
			return;
		else ## TODO!!
			$node = $this->_wrap($node,$type);
	}
	/**
	 * Parses condition
	 * @param mixed where Where clause
	 * @param array|null p Query params
	 * @return void
	 * 
	 */
	private function __querybuilder_parsecond(&$where,$p=null)
	{
		$this->__querybuilder_split($where);
		## Recursive parse obj-lists
		if(is_array($where))
		{
			$next = false;
			foreach($where as $i => &$s)
			{
				if((is_array($where[1]) or (stripos($where[1],'range') === false and false === stripos($where[1],'in')) or $i != 2) and is_array($s))
				{
					$this->__querybuilder_parsecond($s,$p);
					$next = true;
				}
			}
			if($next)
			{
				$where = implode(' ',$where);
				return;
			}
			## The whole trick!
			if(count($where) < 3)
			{
				throw new Exception('ZDBC::__querybuilder_parsecond(): empty where (sub)clause found.',14);
				return false;
			}
			$this->__querybuilder_parse($where[0],1);
			##
			if(is_array($where[2])){
				if(false !== stripos($where[1],'in'))
					$this->__querybuilder_parse($where[2],3,$p);
				elseif(false !== stripos($where[1],'range')){
					$this->__querybuilder_parse($where[2][0],0,$p);
					$this->__querybuilder_parse($where[2][1],0,$p);
				}
				else{
					throw new Exception("ZDBC::__querybuilder_parsecond(): don't know how to process {$where[1]} with array of options.",14);
					return false;
				}
			}
			else{
				$this->__querybuilder_parse($where[2],0,$p);
				$ft_emul = '%'.$where[2].'%';
				$this->__querybuilder_parse($ft_emul);
			}
			##
			$where[1] = strtolower($where[1]);
			if(isset($where[3]) and $where[3])
				if(in_array(strtoupper($where[3]),array('AND','OR')))
					$where[3] = ' '.strtoupper($where[3]).' ';
				else
					$where[3] = ' AND ';
			else
				$where[3] = '';
			$not = ('!' == substr($where[1],0,1) or 'not' == strtolower(substr($where[1],0,3))) ? ' NOT' : '';
			if(stripos($where[1],'fulltext') !== false or stripos($where[1],'fts') !== false)
			{# FTS
				$dx = $this->__drv_dclsname;
				if($dx::$__drv_family == 'mysql')
				{
					$out = "$not MATCH {$where[0]} AGAINST ($where[2]";
					if(stripos($where[1],'bool'))
						$out .= ' IN BOOLEAN MODE';
					elseif(stripos($where[1],'exp'))
						$out .= ' WITH QUERY EXPANSION';
					$where = "$out){$where[3]}";
				}
				## TODO?
				else
					$where = "{$where[0]}$not LIKE $ft_emul{$where[3]}";
			}
			elseif(stripos($where[1],'in') !== false) # IN (list)
				$where = "{$where[0]}$not IN ({$where[2]}){$where[3]}";
			elseif(stripos($where[1],'range') !== false) # BETWEEN X AND Y
				$where = "{$where[0]}$not BETWEEN {$where[2][0]} AND {$where[2][1]}{$where[3]}";
			else
				$where = "{$where[0]}$not {$where[1]} {$where[2]}{$where[3]}";
		}
	}
	/**
	 * Builds JOIN clause
	 * @param mixedref j Join clause
	 * @return void
	 *
	 */
	private function __querybuilder_join(&$j)
	{
		if(is_array($j))
		{
			if(4 == count($j) and (!is_array($j[1]) or 2 == count($j[1])) and (!is_array($j[2]) or 2 == count($j[2])) and !is_array($j[3]))
			{
				$jt = '';
				if(isset($j[3]))
				{
					$jt = strtoupper($j[3]);
					if(!in_array($jt,array('LEFT','RIGHT','INNER','OUTER')))
						$jt = '';
					else
						$jt .= ' ';
				}
				$this->__querybuilder_parse($j[0],2);
				$this->__querybuilder_parse($j[1],1);
				$this->__querybuilder_parse($j[2],1);
				$j = $jt.'JOIN '.$j[0].' ON ('.$j[1].' = '.$j[2].')';
			}
			else
				foreach($j as &$jj)
					$this->__querybuilder_join($jj);
		}
	}
	/**
	 * Parses and build order clause
	 * @param mixedref o Order clause
	 * @return void
	 *
	 */
	private function __querybuilder_order(&$o)
	{
		$this->__querybuilder_split($o);
		if(isset($o[0]))
		{
			$multi = false;
			foreach($o as &$so)
			{
				if(is_array($so) and isset($so[0]))
				{
					$multi = true;
					$this->__querybuilder_order($so);
				}
			}
			if($multi)
			{
				$o = implode(', ',$so);
				return;
			}
			if(count($o) < 0 or count($o) > 2)
			{
				throw new Exception('ZDBC::__querybuilder_order(): wrong order clause.',14);
				return;
			}
			if(!isset($o[1]) or !in_array(strtoupper($o[1]),array('ASC','DESC')))
				$o[1] = '';
			else
				$o[1] = ' '.strtoupper($o[1]);
			$this->__querybuilder_parse($o[0],1);
			$o = "{$o[0]}{$o[1]}";
		}
	}
	/**
	 * Splits obj-list
	 * @param stringref x obj-list
	 * @return void
	 *
	 */
	private function __querybuilder_split(&$x)
	{
		if(!is_array($x) and strpos($x,',') !== false)
			$x = preg_split('/\s*?,\s*?/',$x);
	}
	/**
	 * Genrates a query with stripped comments and strings
	 * @param string q The query
	 * @return string Stripped query
	 *
	 */
	private function __querybuilder_clear($q)
	{
		if(!$q or !is_string($q))
			return $q;
		$q = preg_replace('/(\'[^\']*?\')/','',$q);	## single-quoted strings
		$q = preg_replace('/("[^"]*?")/','',$q);	## double-quoted strings
		$q = preg_replace('/(\/*(.*?)*\/)/','',$q);	## nested c-style comments
		$q = preg_replace('/\/\/(.*?)$/','',$q);	## c-style comments
		$q = preg_replace('/--(.*?)$/','',$q);		## dashed comments
		return $q;
	}
	/**
	 * Counts placeholders in current query
	 * @param string ph Placeholder to count
	 * @return int Placeholders found in raw query
	 *
	 */
	private function count_placeholders($ph='?')
	{
		return substr_count($this->__querybuilder_clear($this->query),$ph);
	}
	/**
	 * Parses prepared statement
	 * @param arrayref p Params
	 * @return boolean Status
	 *
	 */
	private function parse_statement(&$p)
	{
		$this->__hadh();
		if(!$this->query)
			throw new Exception('ZDBC::parse_statement(): no query!',14);
		## Parse/validate prepared statement + workarounds
		if(!$this->query_src)
			$this->query_src = $this->query;
		##
		$q = $this->query;
		##
		$idxd = $this->count_placeholders();
		$named = $this->count_placeholders(':');
		##
		$xpc = count($p);
		$vk = array_keys($p);
		$p_idxd = null;
		foreach($vk as $k => $v)
			$p_idxd = ($k == $v) ? true : false;
		##
		if($p_idxd and $named)
		{
			throw new Exception("ZDBC::parse_statement(): wrong statement for query [$q]: excepted named, got indexed.",2);
			return false;
		}
		elseif(!$p_idxd and $idxd)
		{
			throw new Exception("ZDBC::parse_statement(): wrong statement for query [$q]: excepted indexed, got named.",2);
			return false;
		}
		##
		if($named)
		{
			## test named parameters
			$qpc = $this->count_placeholders(':');
			if($qpc != $xpc)
				throw new Exception("ZDBC::parse_statement(): params count mismatch for query [$q]: expected $xpc, got $qpc.",2);
			foreach($vk as $k => $v)
			{
				if(!preg_match("/:$k/",$q))
				{
					throw new Exception("ZDBC::parse_statement(): named substitue [$k] was not found in query [$q]!",2);
					return false;
				}
			}
		}
		else
		{
			## test indexed parameters
			$qpc = $this->count_placeholders();
			if($qpc != $xpc)
			{
				throw new Exception("ZDBC::parse_statement(): params count mismatch for query [$q]: expected $xpc, got $qpc.",2);
				return false;
			}
		}
		##
		if(!($this->__drv_caps & self::c_prepared))
		{
			## full ps emulation to go, e.g. legacy mysql
			$this->query_src = $this->query;
			$pp = array();
			foreach($p as $k => $v)
			{
				## prepare values for replacement
				if(is_null($v))
				{
					$pp[$k] = 'NULL';
					$p[$k] = 'NULL';
				}
				elseif(is_int($v))
				{
					$pp[$k] = '%d';
					$p[$k] = intval($v);
				}
				elseif(is_float($v))
				{
					$pp[$k] = '%f';
					$p[$k] = floatval($v);
				}
				else
				{
					$dx = $this->__drv_dclsname;
					$pp[$k] = $dx::$__drv_str_quote.'%s'.$dx::$__drv_str_quote;
					$p[$k] = $this->_escape($v);
				}
				if($named)
					$this->query = preg_replace("/:$k/",$pp[$k],$this->query);
			}
			$x = array();
			foreach($pp as $ix)
				$x[] = '?';
			if(!$named)
				$this->query = str_replace($x,$pp,$this->query);
			$this->sth = 'ZDBCEmulatedSth@'.crc32(microtime(true));
		}
		elseif($named and !($this->__drv_caps & self::c_named))
		{
			## convert named -> indexed, e.g. mysqli
			$c = 1;
			$z = $q;
			foreach($vk as $k => $v)
				$z = preg_replace("/:$k/",'[[$'.$k.'$'.$c++.'$]]',$z);
			##
			preg_match_all('/\[\[\$(.+?)\$([\d]+)\$\]\]/',$z,$mx);
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
		return true;
	}
	/**
	 * Writes query to cache and increments query counter
	 * @return void
	 *
	 */
	private function query_stat()
	{
		$this->__hadh();
		$this->queries++;
		$this->qcache[] = $this->query_src;
	}
	/**
	 * If we can run this method in such context
	 * @param string ... List of contexts allowed
	 * @return void
	 *
	 */
	private function allowed()
	{
		for($c=0;$c<func_num_args();$c++)
		{
			$check = 'is_'.func_get_arg($c);
			if(method_exists($this,$check) and $this->$check())
				return;
		}
		$this->error('ZDBC::allowed(): method not allowed in current context!',debug_backtrace());
	}
	/**
	 * Detaches variable
	 * @param mixedred w Variable for detaching
	 * @return void
	 *
	 */
	private function detach(&$w)
	{
		$this->__hadh();
		$n = null;
		$w =& $n;
	}
	/**
	 * Dies with fatal error display
	 * @param string error Error information
	 * @param string custominfo Custom event information
	 *
	 */
	protected function error($error='',$custominfo='')
	{
		$debug = '';
		if(self::debug)
		{
			$es = $this->_error_string();
			$ec = $this->_error_code();
			if($ec)
				$ec = ' [code #'.$ec.']';
			$drv_err = ($es) ? "<hr>Error:&nbsp;<pre><code>«{$es}» ($ec)</code></pre>" : '';
			if(is_array($custominfo))
			{
				$out = '';
				foreach($custominfo as $k => $v)
					$out .= ($k) ? " <- {$v['function']}" : $v['function'];
				$custominfo = "<hr><pre><code>$out</code></pre>";
			}
			else $custominfo = ($custominfo) ? "<hr><pre><code>$custominfo</code></pre>" : '';
			$debug = "<hr>Query:&nbsp;<pre><code>{$this->query_src}</code></pre>$custominfo$drv_err";
		}
		$this->_close();
		die("$error:$debug");
	}
	############################################################################
	## PROTOTYPES for driver-specific code
	############################################################################
	/**
	 * Basic query that returns result set
	 * 
	 */
	abstract protected function _query();
	/**
	 * Non-select query
	 * 
	 */
	abstract protected function _exec();
	/**
	 * Fetch single row
	 * 
	 */
	abstract protected function _fetch($num);
	/**
	 * Fetch all rows
	 * 
	 */
	abstract protected function _fetch_all($num);
	/**
	 * Fetch entire column with given offset
	 * 
	 */
	abstract protected function _fetch_col($offset);
	/**
	 * Get row count in result set
	 * 
	 */
	abstract protected function _num_rows();
	/**
	 * Get affected rows count
	 * 
	 */
	abstract protected function _aff_rows();
	/**
	 * Get rlast insert id
	 * 
	 */
	abstract protected function _last_id();
	/**
	 * Prepare a statement
	 * 
	 */
	abstract protected function _prepare();
	/**
	 * Execute statement with parameters supplied
	 * 
	 */
	abstract protected function _execute(&$p);
	/**
	 * Initialize stored procedure
	 * 
	 */
	abstract protected function _sp_init($name);
	/**
	 * Execute stored procedure
	 * 
	 */
	abstract protected function _sp_exec(&$in,&$out);
	/**
	 * Begin transaction
	 * 
	 */
	abstract protected function _begin();
	/**
	 * Roll transaction back
	 * 
	 */
	abstract protected function _rollback();
	/**
	 * Commit transaction
	 * 
	 */
	abstract protected function _commit();
	/**
	 * If have result set
	 * 
	 */
	abstract protected function _is_result();
	/**
	 * Backend-specific query LIMIT clause
	 * 
	 */
	abstract protected function _limit($q,$l,$o,$order);
	/**
	 * Escape a string to use in query
	 * 
	 */
	abstract protected function _escape($s);
	/**
	 * Wrap string/object name to use in query
	 * 
	 */
	abstract protected function _wrap($p,$obj);
	/**
	 * Reset prepared statement
	 * 
	 */
	abstract protected function _reset_statement();
	/**
	 * Close prepared statement
	 * 
	 */
	abstract protected function _close_statement();
	/**
	 * Free result/close cursor
	 * 
	 */
	abstract protected function _free_result();
	/**
	 * Close connection to database
	 * 
	 */
	abstract protected function _close();
	/**
	 * Get error message if any
	 * 
	 */
	abstract protected function _error_string();
	/**
	 * Get error code if any
	 * 
	 */
	abstract protected function _error_code();
	/**
	 * Iterator interface implementation
	 *
	 */
	public function valid()
	{
		return ($this->is_rsh()) ? true : false;
	}
	public function current()
	{
		return ($this->is_rsh()) ? $this->fetch() : false;
	}
	public function key()
	{
		return $this->__iter;
	}
	public function next()
	{
		++$this->__iter;
	}
	public function rewind()
	{
		return false;
		#$this->__iter = 0;
	}
	############################################################################
	## PUBLIC END
	############################################################################
	/**
	 * HADH: Hard Anal Debug Handler
	 *
	 */
	private function __hadh()
	{
		if(!self::hadh)
			return false;
		$bt = debug_backtrace();
		$x = count($bt)-1;
		$out = '';
		for($c=$x;$c>0;$c--)
		{
			$point =& $bt[$c];
			if($out)$out .= ' » ';
			$out .= '[';
			if(isset($point['class']))
			{
				$cn = $point['class'];
				if($point['class'] == 'ZDBC')
					$cn .= (!$this->derive) ? ':dbh' : ($this->is_sth ? ':sth' : ':rsh');
				$out .= $cn.$point['type'];
			}
			$out .= $point['function'];
			if(isset($point['args']) and count($point['args']))
			{
				$out .= '(';
				foreach($point['args'] as $v)
					$out .= $this->__hadhLst($v);
				$out[strlen($out)-1] = ')';
			}
			else $out .= '(void)';
			if(isset($point['file']))
				$out .= ' @ '.basename($point['file']);
			if(isset($point['line']))
				$out .= ':'.$point['line'];
			$out .= ']';
		}
		$type = (!$this->derive) ? 'dbh' : ($this->is_sth ? 'sth' : 'rsh');
		file_put_contents('hadh.log',"[$type]$out\n".str_repeat('*',90)."\n",FILE_APPEND);
	}
	private function __hadhLst(&$node,$k=false)
	{
		$out = '';
		if(is_array($node))
		{
			$out .= 'array(';
			foreach($node as $k => $v)
				$out .= $this->__hadhLst($k,1).' => '.$this->__hadhLst($v);
			$out[strlen($out)-1] = ')';
		}
		elseif(is_object($node))
		{
			$cn = get_class($node);
			if($cn == 'ZDBC')
				$cn .= (!$this->derive) ? ':dbh' : ($this->is_sth ? ':sth' : ':rsh');
			$out .= "[$cn]";
		}
		elseif(is_int($node) or is_float($node))
			$out .= $node;
		elseif(is_null($node))
			$out .= 'NULL';
		else
			$out .= "'$node'";
		if(!$k)
			$out .= ',';
		return $out;
	}
}
?>