<?php class Radiology_request_item_image_model extends MY_Model {
	protected $tableName = 'radiology_request_item_image';
	protected $resultMode = 'object';	  
	public $primaryKey = 'radiology_request_item_id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.date_added' => 'ASC');
	protected $selectFields = "a.*, b.fullname as doctor_lab_name, c.fullname as user_lab_name, b.ttd as ttd_name, c.ttd as ttds_name";
	protected $joinFields = array(
		array('employee b', 'a.doctor_lab_id = b.id', 'LEFT'),
		array('employee c', 'a.user_lab_id = c.id', 'LEFT'),
	);
}?>