<?php class Pre_procedure_questionnaire_model extends MY_Model {
	protected $tableName = 'pre_procedure_questionnaire';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.id' => 'ASC');
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>