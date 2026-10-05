<?php class Receipt_model_item extends MY_Model {
	protected $tableName = 'receipt_items';
	protected $resultMode = 'object';	  
	public $primaryKey = 'receipt_id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.sort' => 'asc');
	protected $selectFields = "a.*,b.name as pharmacy_catalog_name, b.bride_code";
	protected $joinFields = array(
		array('pharmacy_catalog b','a.pharmacy_catalog_id = b.id','LEFT'),
	);
}
?>
