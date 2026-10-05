<?php class Catalog_supplier_model extends MY_Model {
	protected $tableName = 'pharmacy_catalog_supplier';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.date_added' => 'DESC');
	protected $selectFields = "a.*, c.name as package_name,d.name as package_big_name, e.name as suppliers_name";
	protected $joinFields = array(
		array('package c', 'a.package_id = c.id', 'LEFT'),
		array('package d', 'a.package_big_id = d.id', 'LEFT'),
		array('company e', 'a.suppliers_id = e.id', 'LEFT'),
	);
}?>
