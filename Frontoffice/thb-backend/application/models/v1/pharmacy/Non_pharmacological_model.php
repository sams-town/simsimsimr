<?php class Non_pharmacological_model extends MY_Model {
	protected $tableName = 'non_pharmacological';
	protected $resultMode = 'object';
	public $primaryKey = 'id';
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields;
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>
