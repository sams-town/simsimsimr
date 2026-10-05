<?php class Bromage_score_model extends MY_Model {
	protected $tableName = 'bromage_score';
	protected $resultMode = 'object';
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}