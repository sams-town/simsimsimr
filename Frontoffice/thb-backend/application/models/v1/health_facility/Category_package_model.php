<?php class Category_package_model extends MY_Model {
	protected $tableName = 'category_package';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.index' => 'ASC');
	public $selectFields = "a.*";
	protected $joinFields = array();
}?>