<?php class Partograph_item_model extends MY_Model {
	protected $tableName = 'partograph_item';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.item_type_id' => 'ASC');
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>