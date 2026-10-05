<?php class Template_prescription_item_model extends MY_Model {
	protected $tableName = 'template_prescription_item';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	public $selectFields = "a.*,";
	protected $joinFields = array();
}?>