<?php class Pharmacy_assets_model extends MY_Model {
	protected $tableName = 'pharmacy_assets';
	protected $resultMode = 'object';	  
	public $primaryKey = 'pharmacy_depo_id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields;
	protected $selectFields = "a.*,b.name as depo_name,c.name as catalog_name,c.bride_code,c.price";
	protected $joinFields = array(
		array('pharmacy_depo b','a.pharmacy_depo_id = b.id','INNER'),
		array('pharmacy_catalog c','a.pharmacy_catalog_id = c.id','INNER'),
	);
}
?>
