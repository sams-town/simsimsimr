<?php class Piutang_payment_model extends MY_Model {
	protected $tableName = 'piutang_payment';
	protected $resultMode = 'object';
	public $primaryKey = 'id';
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.paid_at' => 'DESC');
	protected $selectFields = "a.*";
	protected $joinFields = array();
}?>
