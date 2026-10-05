<?php class Kardex_non_pharmacological_model extends MY_Model {
	protected $tableName = 'kardex_non_pharmacological';
	protected $resultMode = 'object';
	public $primaryKey = 'id';
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields;
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>
