<?php class Temperature_chart_item_model extends MY_Model {
	protected $tableName = 'temperature_chart_item';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.observation_hours' => 'ASC');
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>