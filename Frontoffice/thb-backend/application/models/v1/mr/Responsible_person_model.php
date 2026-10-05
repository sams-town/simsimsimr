<?php class Responsible_person_model extends MY_Model {
	protected $tableName = 'responsible_person';
	protected $resultMode = 'object';	  
	public $primaryKey = 'norm';	
	public $whereFields;
    public $limit = 25;
	public $offset;
	public $orderFields = array('a.norm' => 'ASC');
	protected $selectFields = "a.*,l.name as responsible_name";
	protected $joinFields = array(
		array('helper l', 'a.responsible_id = l.code AND l.variable="responsible"', 'LEFT'),
	);
}?>