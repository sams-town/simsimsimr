<?php class Module_model extends MY_Model {
	protected $tableName = 'module';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.index' => 'ASC');
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>