<?php
/* +-----------------------------+--------------------------------------+
   |                Module name: | Flash data                           |
   |                     Author: | Sergey Pechenyuk                     |
   |                       Site: | http://oc-store.com                  |
   +-----------------------------+--------------------------------------+
   | Sometimes you may wish to store items in the session only for the  |
   | next request. You may do so using the flash method. Data stored in |
   | the session using this method will only be available during the    |
   | subsequent HTTP request, and then will be deleted. Flash data is   |
   | primarily useful for short-lived status messages.                  |
   +--------------------------------------------------------------------+ */
   
class Flash {
	private $prefix = "ocstore-flash-";
	private $session = null;
	
	public function __construct(&$session) {
		$this->session = &$session;
		$this->session[$this->prefix."old"] = isset($this->session[$this->prefix."new"])? $this->session[$this->prefix."new"]: array();
		unset($this->session[$this->prefix."new"]);
	}
	
	/*
		Set flash data
		
		* @param  string  $variable
		* @param  string  $value
	*/
	public function set($variable, $value) {
		$this->session[$this->prefix."new"][$variable] = $value;
		$this->session[$this->prefix."old"][$variable] = $value;
	}
	
	/*
		Get flash data
		
		* @param  string  $variable
		* @return string 
	*/
	public function get($variable) {
		if (isset($this->session[$this->prefix."old"][$variable]))
			return $this->session[$this->prefix."old"][$variable];
		
		return false;
	}
	
	/*
		If you need to keep your flash data around for even more requests, 
		you may use the reflash method, which will keep all of the flash 
		data around for an additional request
	*/
	public function reflash() {
		if (isset($this->session[$this->prefix."old"]))
			foreach ($this->session[$this->prefix."old"] as $key => $value)
				$this->session[$this->prefix."new"][$key] = $value;
	}
	
	/*
		If you only need to keep specific flash data around, you may use the 
		keep method.
		
		* @param  array  $variables
	*/
	public function keep($variables) {
		foreach ($variables as $variable)
			if (isset($this->session[$this->prefix."old"][$variable]))
				$this->session[$this->prefix."new"][$variable] = $this->session[$this->prefix."old"][$variable];
	}
}