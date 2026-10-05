<?php class Registration_inv_model extends MY_Model {
	protected $tableName = 'registration';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.register_date' => 'desc');
	protected $selectFields = "a.code,c.norm,c.name,b.is_new as registration_destination_is_new,b.id as registration_destination_id,d.name as health_facility_item_name,
								e.name as health_facility_name,e.id as health_facility_id,f.fullname as doctor_in_charge_name,g.name as how_to_enter_name,
								h.name as alias_name,i.name as type_how_to_enter_name,i.code as type_how_to_enter_id,
								j.name as sex_name,k.name as exit_name,l.name as type_register_name,m.name as type_emergency_name,
								n.name as type_emergency_case_name";
	protected $joinFields = array(
		array('registration_destination b', 'a.id = b.registration_id', 'LEFT'),
		array('patient c', 'a.norm = c.norm', 'LEFT'),
		array('health_facility_item d', 'b.health_facility_item_id = d.id', 'LEFT'),
		array('health_facility e', 'd.health_facility_id = e.id', 'LEFT'),
		array('employee f', 'b.doctor_in_charge_id = f.id', 'LEFT'),
		array('how_to_enter g', 'a.how_to_enter_id = g.id', 'LEFT'),
		array('helper h', 'c.alias = h.code AND h.variable="alias"', 'LEFT'),
		array('helper i', 'g.type_how_to_enter_id = i.code AND i.variable="type_how_to_enter"', 'LEFT'),
		array('helper j', 'c.sex = j.code AND j.variable="sex"', 'LEFT'),
		array('exit k', 'b.exit_id = k.id', 'LEFT'),
		array('helper l', 'a.type_register_id = l.code AND l.variable="type_register"', 'LEFT'),
		array('helper m', 'b.type_emergency_id = m.code AND m.variable="type_emergency"', 'LEFT'),
		array('helper n', 'b.type_emergency_case_id = n.code AND n.variable="type_emergency_case"', 'LEFT'),
	);

	public function flag_patient($norm) {
		$patient = $this->db->query("
			SELECT *
			FROM patient
			LEFT JOIN registration
			ON registration.norm = patient.norm 
			WHERE patient.norm = '$norm' AND registration.id IS NOT NULL
		")->result();
		if($patient != []) {
			$this->db->query("
				UPDATE patient 
				SET is_new = 'N'
				WHERE norm = '$norm'
			");
		}
	}

	public function flag_patient_destination($norm, $health_facility_item_id) {
		$patient = $this->db->query("
			SELECT patient.norm, registration_destination.health_facility_item_id
			FROM registration
			LEFT JOIN patient
			ON patient.norm = registration.norm
			LEFT JOIN registration_destination
			ON registration_destination.registration_id = registration.id 
			WHERE patient.norm = '$norm' AND registration_destination.health_facility_item_id = '$health_facility_item_id'
		")->result();

		if($patient != []) {
			return 'N';
		} else {
			return 'Y';
		}
	}

	public function filter_data($filter,$ssr = false) {
		$this->selectFields = "b.checkin_date,b.id as id,a.code,b.registration_id,c.norm,a.norm,b.health_facility_item_id,b.is_new as registration_destination_is_new,b.id as registration_destination_id,d.name as health_facility_item_name,
								e.name as health_facility_name,e.id as health_facility_id,f.fullname as doctor_in_charge_name,i.name as type_how_to_enter_name,i.code as type_how_to_enter_id,
								c.name, c.fullname,h.name as alias_name, c.birth_date, c.birth_place,c.address,c.phone,
								j.name as sex_name,bd.name as bed_name, ro.name as room_name";
		$this->joinFields = array(
			array('registration_destination b', 'a.id = b.registration_id', 'LEFT'),
			array('registration_insurance ri', 'b.id = ri.registration_destination_id AND ri.is_primary="1"', 'LEFT'),
			array('patient c', 'a.norm = c.norm', 'LEFT'),
			array('health_facility_item d', 'b.health_facility_item_id = d.id', 'LEFT'),
			array('health_facility e', 'd.health_facility_id = e.id', 'LEFT'),
			array('employee f', 'b.doctor_in_charge_id = f.id', 'LEFT'),
			array('bed bd', 'b.bed_id = bd.id', 'LEFT'),
			array('room ro', 'bd.room_id = ro.id', 'LEFT'),
			array('how_to_enter g', 'a.how_to_enter_id = g.id', 'LEFT'),
			array('helper h', 'c.alias = h.code AND h.variable="alias"', 'LEFT'),
			array('helper i', 'g.type_how_to_enter_id = i.code AND i.variable="type_how_to_enter"', 'LEFT'),
			array('helper j', 'c.sex = j.code AND j.variable="sex"', 'LEFT'),
			array('exit k', 'b.exit_id = k.id', 'LEFT'),
			array('helper l', 'a.type_register_id = l.code AND l.variable="type_register"', 'LEFT'),
			array('helper m', 'b.type_emergency_id = m.code AND m.variable="type_emergency"', 'LEFT'),
			array('helper n', 'b.type_emergency_case_id = n.code AND n.variable="type_emergency_case"', 'LEFT'),
		);
		// if(isset($filter['date_start']) && isset($filter['date_end'])) {
		// 	$where = "`a`.`register_date` BETWEEN '" . $filter['date_start'] . " 00:00:00' AND '" . $filter['date_end'] . " 23:59:59'";
		// 	$this->db->where($where);
		// }
		unset($filter['date_start']);
		unset($filter['date_end']);

		// if(isset($filter['institute_branch_id'])) { $this->db->where('h.institute_branch_id', $filter['institute_branch_id']); unset($filter['registration_destination_id']); }
		// if(isset($filter['institute_branch_id'])) { $this->db->where('i.institute_branch_id', $filter['institute_branch_id']); unset($filter['registration_destination_id']); }
		// if(isset($filter['institute_branch_id'])) { $this->db->where('j.institute_branch_id', $filter['institute_branch_id']); unset($filter['registration_destination_id']); }
		// if(isset($filter['institute_branch_id'])) { $this->db->where('l.institute_branch_id', $filter['institute_branch_id']); unset($filter['registration_destination_id']); }
		// if(isset($filter['institute_branch_id'])) { $this->db->where('m.institute_branch_id', $filter['institute_branch_id']); unset($filter['registration_destination_id']); }
		// if(isset($filter['institute_branch_id'])) { $this->db->where('n.institute_branch_id', $filter['institute_branch_id']); unset($filter['registration_destination_id']); }
		if(isset($filter['registration_destination_id'])) { $this->db->where('b.id', $filter['registration_destination_id']); unset($filter['registration_destination_id']); }
		if(isset($filter['is_new'])) { $this->db->where('c.is_new', $filter['is_new']); unset($filter['is_new']); }
		if(isset($filter['sex'])) { $this->db->where('c.sex', $filter['sex']); unset($filter['sex']); }
		if(isset($filter['code'])) { $this->db->where('a.code', $filter['code']); unset($filter['code']); }
		if(isset($filter['norm'])) { $this->db->where('c.norm', $filter['norm']); unset($filter['norm']); }
		if(isset($filter['name'])) { $this->db->where('c.name', $filter['name']); unset($filter['name']); }

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