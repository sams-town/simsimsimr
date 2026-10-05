<?php class Radiology_package_hpp_model extends MY_Model {
	protected $tableName = 'radiology_package_hpp';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	public $selectFields = "a.*,b.name as pharmacy_catalog_name";
	protected $joinFields = array(
		array('pharmacy_catalog b', 'a.pharmacy_catalog_id = b.id', 'LEFT'),
	);
}?>