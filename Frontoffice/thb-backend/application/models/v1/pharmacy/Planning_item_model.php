<?php class Planning_item_model extends MY_Model {
	protected $tableName = 'pharmacy_planning_item';
	protected $resultMode = 'object';	  
	public $primaryKey = 'pharmacy_planning_id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.sort' => 'ASC');
	protected $selectFields = "a.*,b.name as pharmacy_catalog_name,pp.pharmacy_depo_id";
	protected $joinFields = array(
		array('pharmacy_catalog b','a.pharmacy_catalog_id = b.id','LEFT'),
		array('pharmacy_planning pp','a.pharmacy_planning_id = pp.id','LEFT'),
	);
}
?>
