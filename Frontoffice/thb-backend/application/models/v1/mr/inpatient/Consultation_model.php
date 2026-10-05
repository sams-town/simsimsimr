<?php class Consultation_model extends MY_Model {
	protected $tableName = 'consultation';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.date_added' => 'DESC');
	protected $selectFields = "a.*, e2.fullname as doctor_in_charge_name, e.fullname as consultant_name";
	protected $joinFields = array(
        array('employee e', 'a.consultant_id = e.id', 'INNER'),
        array('employee e2', 'a.doctor_in_charge_id = e2.id', 'INNER'),
    );

	public function check_if_answering($registration_destination_id, $consultant_id, $cppt_id) {
		$select = 'a.id, a.consultation_type_id';
		$where = array(
			'a.registration_destination_id' => $registration_destination_id,
			'a.consultant_id' => $consultant_id,
			'a.status' => 0,
			'a.is_cancel' => 0,
		);
		$consultation = $this->select(0, $select, $where);
		if (count($consultation) > 0) {
			$consultation_id = $consultation[0]->id;
			$dt_consultation = array(
				'cppt_id' => $cppt_id,
			);
			$this->update_v2($consultation_id, $dt_consultation);
		}

		return $consultation;
	}
}?>