<?php class Blood_transfusion_monitoring_item_model extends MY_Model {
	protected $tableName = 'blood_transfusion_monitoring_item';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.id' => 'ASC');
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>