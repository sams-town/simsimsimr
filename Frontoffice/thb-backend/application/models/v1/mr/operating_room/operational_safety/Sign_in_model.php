<?php class Sign_in_model extends MY_Model {
	protected $tableName = 'operational_safety_sign_in';
	protected $resultMode = 'object';
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}