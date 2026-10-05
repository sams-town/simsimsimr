<?php class Consultation_item_model extends MY_Model {
	protected $tableName = 'consultation_item';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.date_added' => 'DESC');
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>