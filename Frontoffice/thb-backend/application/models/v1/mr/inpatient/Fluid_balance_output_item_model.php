<?php class Fluid_balance_output_item_model extends MY_Model {
	protected $tableName = 'fluid_balance_output_item';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.id' => 'ASC');
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>