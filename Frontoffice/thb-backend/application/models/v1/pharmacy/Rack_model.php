<?php class Rack_model extends MY_Model {
	protected $tableName = 'pharmacy_rack';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.name' => 'ASC');
	public $selectFields = "a.*,b.name as pharmacy_depo_name";
	protected $joinFields = array(
		array('pharmacy_depo b', 'a.pharmacy_depo_id = b.id','LEFT' )
	);
}?>
