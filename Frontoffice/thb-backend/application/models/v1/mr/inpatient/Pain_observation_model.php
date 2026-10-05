<?php class Pain_observation_model extends MY_Model {
	protected $tableName = 'pain_observation';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.id' => 'ASC');
	protected $selectFields = "a.*, e.fullname as employee_nurse_name, e2.fullname as employee_nurse_2_name";
	protected $joinFields = array(
        array('employee e', 'a.employee_nurse_id = e.id', 'INNER'),
		array('employee e2', 'a.employee_nurse_2_id = e2.id', 'LEFT'),
    );
}?>