<?php class Early_warning_score_model extends MY_Model {
	protected $tableName = 'early_warning_score';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.observation_date' => 'ASC');
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>