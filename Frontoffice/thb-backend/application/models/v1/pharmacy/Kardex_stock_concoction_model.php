<?php class Kardex_stock_concoction_model extends MY_Model {
	protected $tableName = 'kardex_stock_concoction';
	protected $resultMode = 'object';
	public $primaryKey = 'id';
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields;
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}