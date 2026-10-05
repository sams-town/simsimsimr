<?php class User_history_model extends MY_Model {
	protected $tableName = 'user_history';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
    public $limit = 25;
    public $offset = 0;
	protected $orderFields = array();
	protected $selectFields = "a.*,e.fullname as employee_name";
	protected $joinFields = array(
		array('user u', "a.user_id = u.id", "LEFT"),
		array('employee e', "u.employee_id = e.id", "LEFT")
	);
}?>