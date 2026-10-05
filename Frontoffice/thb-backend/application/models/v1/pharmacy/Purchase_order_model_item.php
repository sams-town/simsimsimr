<?php class Purchase_order_model_item extends MY_Model {
	protected $tableName = 'purchase_order_items';
	protected $resultMode = 'object';	  
	public $primaryKey = 'purchase_order_id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.sort' => 'asc');
	protected $selectFields = "a.*,b.name as pharmacy_catalog_name, b.bride_code, p.pharmacy_depo_id, pb.name as package_name_big, ps.name as package_name";
	protected $joinFields = array(
		array('purchase_order p','a.purchase_order_id = p.id','LEFT'),
		array('pharmacy_catalog b','a.pharmacy_catalog_id = b.id','LEFT'),
		array('package pb','a.package_big_id = pb.id','LEFT'),
		array('package ps','a.package_id = ps.id','LEFT'),
	);
}
?>
