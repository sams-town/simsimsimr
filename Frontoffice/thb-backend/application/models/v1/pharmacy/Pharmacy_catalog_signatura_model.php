<?php class Pharmacy_catalog_signatura_model extends MY_Model {
	protected $tableName = 'pharmacy_catalog_signatura';
	protected $resultMode = 'object';
	public $primaryKey = 'pharmacy_catalog_id';
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields;
	protected $selectFields = "a.*";
	protected $joinFields = array();
}
?>
