<?php class Catalog_instruction_model extends MY_Model {
	protected $tableName = 'pharmacy_catalog_instruction';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.name' => 'ASC');
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>
