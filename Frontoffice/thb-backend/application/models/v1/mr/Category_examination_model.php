<?php class Category_examination_model extends MY_Model {
	protected $tableName = 'category_examination';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.name' => 'ASC');
	protected $selectFields = "a.*";
	protected $joinFields = array();
}?>