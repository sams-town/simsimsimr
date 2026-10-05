<?php class Operating_room_operator_model extends MY_Model {
	protected $tableName = 'operating_room_operator';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.operator_role_type_id' => 'ASC');
	protected $selectFields = "a.*, e.fullname as operator_name, e.ttd";
	protected $joinFields = array(
		array('employee e', 'a.operator_id = e.id', 'INNER'),
	);
}?>