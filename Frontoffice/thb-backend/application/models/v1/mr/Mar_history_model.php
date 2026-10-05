<?php class Mar_history_model extends MY_Model {
	protected $tableName = 'mar_history';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.date_added' => 'ASC');
	public $selectFields = "a.*, mar.registration_destination_id";
	protected $joinFields = array(
		array('medication_administration_record_item mari', 'a.medication_administration_record_item_id = mari.id', 'LEFT'),
		array('medication_administration_record mar', 'mari.medication_administration_record_id = mar.id', 'LEFT'),
	);
}?>
