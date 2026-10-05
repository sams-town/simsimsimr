<?php class Template_resume_model extends MY_Model {
	protected $tableName = 'template_resume';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.date_added' => 'DESC');
	public $selectFields = "a.*,";
	protected $joinFields = array();
}?>