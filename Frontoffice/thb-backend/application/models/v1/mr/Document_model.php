<?php class Document_model extends MY_Model {
	protected $tableName = 'document';
	protected $resultMode = 'object';	  
	public $primaryKey = 'norm';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.norm' => 'ASC');
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>