<?php class Preanesthesia_evaluation_model extends MY_Model {
	protected $tableName = 'preanesthesia_evaluation';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.id' => 'ASC');
	protected $selectFields = "a.*";
	protected $joinFields = array();
}?>