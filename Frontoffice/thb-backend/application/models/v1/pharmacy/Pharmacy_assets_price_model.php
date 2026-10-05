<?php class Pharmacy_assets_price_model extends MY_Model {
	protected $tableName = 'pharmacy_assets_price';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.id' => 'DESC');
	protected $selectFields = "a.*,b.name as depo_name,c.name as catalog_name";
	protected $joinFields = array(
		array('pharmacy_depo b','a.pharmacy_depo_id = b.id','LEFT'),
		array('pharmacy_catalog c','a.pharmacy_catalog_id = c.id','LEFT'),
	);
}
?>
