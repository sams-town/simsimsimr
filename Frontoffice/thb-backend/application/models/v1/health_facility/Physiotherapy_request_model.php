<?php class Physiotherapy_request_model extends MY_Model {
	protected $tableName = 'physiotherapy_request';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.id' => 'ASC');
	protected $selectFields = "a.*, e.fullname as employee_doctor_name, s.registration_destination_id, d.fullname as doctor_lab_name, l.fullname as user_lab_name";
	protected $joinFields = array(
		array('soap s', 'a.soap_id = s.id', 'LEFT'),
		array('employee e', 's.doctor_in_charge_id = e.id', 'LEFT'),
		array('registration_destination rd', 's.registration_destination_id = rd.id', 'LEFT'),
		array('registration r', 'rd.registration_id = r.id', 'LEFT'),
		array('employee d', 'a.doctor_lab_id = d.id', 'LEFT'),
		array('employee l', 'a.user_lab_id = l.id', 'LEFT'),
	);

	public function history() {
		$this->selectFields = "a.*, e.fullname as employee_doctor_name, s.registration_destination_id";
		$this->joinFields = array(
			array('soap s', 'a.soap_id = s.id', 'LEFT'),
			array('employee e', 's.doctor_in_charge_id = e.id', 'LEFT'),
			array('registration_destination rd', 's.registration_destination_id = rd.id', 'LEFT'),
			array('registration r', 'rd.registration_id = r.id', 'LEFT'),
		);
	}

	public function filter_data($filter,$ssr = false) {
		$this->selectFields = "a.*, e.fullname as employee_doctor_name, s.registration_destination_id";
		$this->joinFields = array(
			array('soap s', 'a.soap_id = s.id', 'LEFT'),
			array('employee e', 's.doctor_in_charge_id = e.id', 'LEFT'),
		);
		if(isset($filter['date_start']) && isset($filter['date_end'])) {
			$where = "`a`.`date_request` BETWEEN '" . $filter['date_start'] . " 00:00:00' AND '" . $filter['date_end'] . " 23:59:59'";
			$this->db->where($where);
			unset($filter['date_start']);
			unset($filter['date_end']);
		}

		// if(isset($filter['institute_branch_id'])) { $this->db->where('h.institute_branch_id', $filter['institute_branch_id']); unset($filter['registration_destination_id']); }
		// if(isset($filter['institute_branch_id'])) { $this->db->where('i.institute_branch_id', $filter['institute_branch_id']); unset($filter['registration_destination_id']); }
		// if(isset($filter['institute_branch_id'])) { $this->db->where('j.institute_branch_id', $filter['institute_branch_id']); unset($filter['registration_destination_id']); }
		// if(isset($filter['institute_branch_id'])) { $this->db->where('l.institute_branch_id', $filter['institute_branch_id']); unset($filter['registration_destination_id']); }
		// if(isset($filter['institute_branch_id'])) { $this->db->where('m.institute_branch_id', $filter['institute_branch_id']); unset($filter['registration_destination_id']); }
		// if(isset($filter['institute_branch_id'])) { $this->db->where('n.institute_branch_id', $filter['institute_branch_id']); unset($filter['registration_destination_id']); }
		if(isset($filter['registration_destination_id'])) { $this->db->where('s.registration_destination_id', $filter['registration_destination_id']); unset($filter['registration_destination_id']); }
		// if(isset($filter['is_new'])) { $this->db->where('c.is_new', $filter['is_new']); unset($filter['is_new']); }
		// if(isset($filter['sex'])) { $this->db->where('c.sex', $filter['sex']); unset($filter['sex']); }
		// if(isset($filter['code'])) { $this->db->where('a.code', $filter['code']); unset($filter['code']); }
		// if(isset($filter['norm'])) { $this->db->where('c.norm', $filter['norm']); unset($filter['norm']); }
		// if(isset($filter['name'])) { $this->db->where('c.name', $filter['name']); unset($filter['name']); }

		$this->db->where($filter);
		if($ssr) {
			return $this->read_ssr();
		} else {
			return $this->read();
		}

		// $query = $this->db->query("
		// 	SELECT `a`.`code`, `a`.`norm`, `a`.`type_register_id`, `a`.`how_to_enter_id`, `a`.`register_date`, `b`.`sex`, `c`.`health_facility_item_id`, `c`.`doctor_in_charge_id` 
		// 	FROM `registration` as `a` 
		// 	LEFT JOIN `patient` `b` 
		// 	ON `a`.`norm` = `b`.`norm` 
		// 	LEFT JOIN `registration_destination` `c` 
		// 	ON `a`.`id` = `c`.`registration_id` 
		// 	WHERE `a`.`is_cancel` = '0' AND `a`.`register_date` BETWEEN '" . $date_start . "' AND '" . $date_end . "'
		// 	ORDER BY `a`.`id` 
		// 	ASC
		// ")->result_array();

		// return $query;
	}
}?>