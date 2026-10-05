<?php class Hemodialysis_initiation_master_model extends MY_Model {
	protected $tableName = 'hemodialysis_initiation_master';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>