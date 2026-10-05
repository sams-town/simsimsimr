<?php class Rackname_model extends MY_Model {
	protected $tableName = 'pharmacy_rackname';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.name' => 'ASC');
	public $selectFields = "a.*,b.name as pharmacy_rack_name";
	protected $joinFields = array(
		array('pharmacy_rack b', 'a.pharmacy_rack_id = b.id','LEFT' )
	);
}?>
