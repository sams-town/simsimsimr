<?php class Transaction_invoice_item_model extends MY_Model {
	protected $tableName = 'transaction_invoice_item';
	protected $resultMode = 'object';	  
	public $primaryKey = 'transaction_invoice_id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.transaction_invoice_id' => 'ASC');
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>