<?php class Opname_activation_model extends MY_Model {
	protected $tableName = 'pharmacy_opname_activation';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.no_document' => 'ASC');
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>
