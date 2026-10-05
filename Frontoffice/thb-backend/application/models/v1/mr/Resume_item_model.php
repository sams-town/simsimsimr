<?php class Resume_item_model extends MY_Model {
	protected $tableName = 'resume_item';
	protected $resultMode = 'object';	  
	public $primaryKey = 'resume_id';	
	public $whereFields;
    public $limit = 25;
	public $offset;
	public $orderFields = array('a.resume_id' => 'ASC');
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>