<?php class Postoperative_nursing_model extends MY_Model {
	protected $tableName = 'postoperative_nursing';
	protected $resultMode = 'object';
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}