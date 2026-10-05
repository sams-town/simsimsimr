<?php class Planning_model extends MY_Model {
	protected $tableName = 'pharmacy_planning';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.date_added' => 'DESC');
	protected $selectFields = "a.*,b.name as institute_branch_name,c.name as pharmacy_depo_name";
	protected $joinFields = array(
		array('institute_branch b', 'a.institute_branch_id = b.id', 'LEFT'),
		array('pharmacy_depo c', 'a.pharmacy_depo_id = c.id', 'LEFT'),
	);
}
?>
