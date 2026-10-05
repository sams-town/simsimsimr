<?php class Depo_model extends MY_Model {
	protected $tableName = 'pharmacy_depo';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.code' => 'ASC');
	public $selectFields = "a.*,";
	protected $joinFields = array();
}?>
