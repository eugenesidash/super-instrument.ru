<?php
class ZctCtlWrap extends Controller {
	private function loadModel($kind,$method){
		$this->load->model($kind);
		$runme = 'model_'.str_replace('/','_',$kind);
		if(!@is_object($this->$runme))
			return null;
		if(!is_callable(array($this->$runme,$method)))
			return null;
		return $runme;
	}
	public function loadData($kind,$method,$filter=null){
		$runme = $this->loadModel($kind,$method);
		if(!$runme)
			return $runme;
		return ($filter) ? $this->$runme->$method($filter) : $this->$runme->$method();
	}
	#
	public function createEntry($kind,$method,$data){
		$runme = $this->loadModel($kind,$method);
		if(!$runme)
			return $runme;
		return $this->$runme->$method($data);
	}
	#
	public function patchEntry($kind,$method,$id,$data){
		$runme = $this->loadModel($kind,$method);
		if(!$runme)
			return $runme;
		return $this->$runme->$method($id,$data);
	}
	#
	public function Q($q){
		return $this->db->query($q);
	}
	public function S($s){
		return $this->db->escape($s);
	}
}
?>