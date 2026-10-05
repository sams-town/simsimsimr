<?php class Letter_model extends MY_Model {
	protected $tableName = 'letter';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	protected $selectFields = "a.*,l.content as content_html,e.fullname as doctor_in_charge_name, e.ttd as ttd_doctor, h.name as responsible_type_name";
	protected $joinFields = array(
		array('letter_type l', 'a.letter_type_id = l.id', 'LEFT'),
		array('helper h', 'a.responsible_id = h.code AND h.variable = "responsible"', 'LEFT'),
		array('employee e', 'a.selected_doctor_id = e.id', 'LEFT'),
	);
}?>