<?php class Operative_report_model extends MY_Model {
	protected $tableName = 'operative_report';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.id' => 'ASC');
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>