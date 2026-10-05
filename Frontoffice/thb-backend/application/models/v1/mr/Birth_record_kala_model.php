<?php class Birth_record_kala_model extends MY_Model {
	protected $tableName = 'birth_record_kala';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.id' => 'ASC');
	protected $selectFields = "a.*";
	protected $joinFields = array();
}?>