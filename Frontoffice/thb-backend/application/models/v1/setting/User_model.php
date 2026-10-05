<?php class User_model extends MY_Model {
	protected $tableName = 'user';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
    public $limit = 25;
    public $offset = 0;
	protected $orderFields = array();
	protected $selectFields = "a.*,ug.group_id,e.fullname as employee_name";
	protected $joinFields = array(
		array('user_group ug', "a.id = ug.user_id", "LEFT"),
		array('employee e', "a.employee_id = e.id", "LEFT"),
	);
}?>