<?php class Opname_model extends MY_Model {
	protected $tableName = 'pharmacy_opname';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.no_document' => 'ASC');
	protected $selectFields = "a.*, b.name as pharmacy_depo_name, poa.name as opname_activation_name";
	protected $joinFields = array(
		array('pharmacy_opname_activation poa','a.pharmacy_activation_id = poa.id','LEFT'),
		array('pharmacy_depo b','a.pharmacy_depo_id = b.id','LEFT'),
	);
}?>
