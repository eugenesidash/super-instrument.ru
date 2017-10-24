<?php
require_once 'zfm.form.class.php';
require_once 'zfm.ctl.class.php';

abstract class ZFM
{
	const
		#
		M_GET		= 0,
		M_POST		= 1,
		M_PUT		= 2,
		#
		T_HIDDEN	= 0,
		T_TEXT		= 1,
		T_PASSWORD	= 2,
		T_TEXTAREA	= 3,
		T_RADIO		= 4,
		T_CHECKBOX	= 5,
		T_SELECT	= 6,
		T_FILE		= 7,
		T_YESNO		= 8,
		#
		C_SUBMIT	= 1,
		C_RESET		= 2,
		C_NRST		= 4;
	##
	public function __construct(){/*virtual*/}
	public function __clone(){trigger_error(__CLASS__.': cloning not allowed!',E_USER_ERROR);}
	public function __wakeup(){trigger_error(__CLASS__.': unfolding not allowed!',E_USER_ERROR);}
	public function __destruct(){/*virtual*/}
	protected function v(&$b,$k,$d)
	{
		if($d === null)
			$d = false;
		if(!isset($b[$k]) or $b[$k] === null)
			return $d;
		else
			return $b[$k];
	}
	protected function isMulti()
	{
		return ($this->type == ZFM::T_SELECT or $this->type == ZFM::T_RADIO);
	}
	abstract public function render();
}
?>