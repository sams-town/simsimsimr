<?php class Receipt_po_comparison_model extends MY_Model {
	protected $tableName = 'receipt_po_comparison';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields;
	protected $selectFields = "a.*";
	protected $joinFields = array();
}
?>
