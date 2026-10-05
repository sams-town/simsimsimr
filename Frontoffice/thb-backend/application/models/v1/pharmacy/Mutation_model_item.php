<?php class Mutation_model_item extends MY_Model {
	protected $tableName = 'pharmacy_mutation_item';
	protected $resultMode = 'object';	  
	public $primaryKey = 'pharmacy_mutation_id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields;
	protected $selectFields = "a.*, b.name as catalog_name, c.name as package_name, d.username as user_name";
	protected $joinFields = array(
		array('pharmacy_catalog b', 'a.pharmacy_catalog_id = b.id','LEFT'),
		array('package c', 'a.package_id = c.id','LEFT'),
		array('user d', 'a.user_updated = d.id','LEFT')
	);
}
?>
