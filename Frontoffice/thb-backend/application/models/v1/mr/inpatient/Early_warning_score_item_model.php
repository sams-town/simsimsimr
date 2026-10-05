<?php class Early_warning_score_item_model extends MY_Model {
	protected $tableName = 'early_warning_score_item';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.observation_time' => 'ASC');
	protected $selectFields = "a.*,";
	protected $joinFields = array(
		array('employee e', 'a.employee_nurse_id = e.id', 'INNER'),	
	);
}?>