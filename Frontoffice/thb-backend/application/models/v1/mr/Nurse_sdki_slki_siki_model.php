<?php class Nurse_sdki_slki_siki_model extends MY_Model {
	protected $tableName = 'nurse_sdki_slki_siki';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>