<?php class Medicine_types_model extends MY_Model {
	protected $tableName = 'medicine_types';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.id' => 'ASC');
	public $selectFields = "a.*,";
	protected $joinFields = array();
}?>
