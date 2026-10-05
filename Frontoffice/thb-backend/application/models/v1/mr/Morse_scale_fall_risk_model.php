<?php class Morse_scale_fall_risk_model extends MY_Model {
	protected $tableName = 'morse_scale_fall_risk';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>