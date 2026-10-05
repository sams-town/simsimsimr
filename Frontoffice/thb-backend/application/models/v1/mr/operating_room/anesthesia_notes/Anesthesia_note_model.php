<?php class Anesthesia_note_model extends MY_Model {
	protected $tableName = 'anesthesia_note';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.id' => 'ASC');
	protected $selectFields = "a.*";
	protected $joinFields = array();
}?>