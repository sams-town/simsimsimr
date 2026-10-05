<?php class Registration_model extends MY_Model {
	protected $tableName = 'registration';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.register_date' => 'desc');
	protected $selectFields = "a.*,b.*,b.is_new as registration_destination_is_new,b.id as registration_destination_id,d.name as health_facility_item_name,
								e.name as health_facility_name,e.id as health_facility_id,f.fullname as doctor_in_charge_name, f.ttd as employee_signature, g.name as how_to_enter_name,
								c.*,h.name as alias_name,i.name as type_how_to_enter_name,i.code as type_how_to_enter_id,
								j.name as sex_name,k.name as exit_name,l.name as type_register_name,m.name as type_emergency_name,
								n.name as type_emergency_case_name, h2.name as type_visit_name, cp.logo_url,ri.company_id";
	protected $joinFields = array(
		array('registration_destination b', 'a.id = b.registration_id', 'LEFT'),
		array('registration_insurance ri', 'b.id = ri.registration_destination_id and ri.is_primary="1"', 'LEFT'),
		array('company cp', 'ri.company_id = cp.id', 'LEFT'),
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
		array('helper h2', 'a.type_visit_id = h2.code AND h2.variable="type_visit"', 'LEFT'),
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

	public function flag_patient_check($register_id, $health_facility_item_id) {
		$patient = $this->db->query("
			SELECT patient.norm, registration_destination.health_facility_item_id
			FROM registration
			LEFT JOIN patient
			ON patient.norm = registration.norm
			LEFT JOIN registration_destination
			ON registration_destination.registration_id = registration.id 
			WHERE registration_destination.registration_id = '$register_id' AND registration.is_cancel = 0 AND registration_destination.health_facility_item_id = '$health_facility_item_id'
		")->result();

		if($patient != []) {
			return 'N';
		} else {
			return 'Y';
		}
	}

	public function flag_patient_double($norm,$health_facility_item_id, $doctor_in_charge_id) {
		$patient = $this->db->query("
			SELECT patient.norm, registration_destination.health_facility_item_id
			FROM registration
			LEFT JOIN patient
			ON patient.norm = registration.norm
			LEFT JOIN registration_destination
			ON registration_destination.registration_id = registration.id 
			WHERE patient.norm = '$norm' AND registration.is_cancel = 0 AND registration_destination.doctor_in_charge_id = '$doctor_in_charge_id' AND registration_destination.health_facility_item_id = '$health_facility_item_id' AND registration_destination.register_date BETWEEN '" . date('Y-m-d') . " 00:00:00' AND '" . date('Y-m-d') . " 23:59:59'
		")->result();

		if($patient != []) {
			return 'N';
		} else {
			return 'Y';
		}
	}

	public function flag_appointment($norm,$health_facility_item_id, $doctor_in_charge_id) {
		$patient = $this->db->query("
			SELECT appointment.id, appointment.norm, appointment.health_facility_item_id, appointment.is_mjkn
			FROM appointment
			WHERE appointment.norm = '$norm' AND appointment.is_delete = 0 AND appointment.doctor_in_charge_id = '$doctor_in_charge_id' AND appointment.health_facility_item_id = '$health_facility_item_id' AND appointment.date_appointment = '".date('Y-m-d')."'")->result();

		if($patient != []) {
			if($patient[0]->is_mjkn == 1){
				return 'MJKN';
			} else {
				return 'AP';
			}
		} else {
			return 'Y';
		}
	}
	
	public function flag_patient_double_insurance($norm,$health_facility_item_id, $doctor_in_charge_id, $company) {
		$patient = $this->db->query("
			SELECT patient.norm, registration_destination.health_facility_item_id
			FROM registration
			LEFT JOIN patient ON patient.norm = registration.norm
			LEFT JOIN registration_insurance ON registration_destination.id = registration_insurance.registration_destination_id
			LEFT JOIN registration_destination ON registration_destination.registration_id = registration.id 
			WHERE patient.norm = '$norm' AND registration.is_cancel = 0 AND registration_destination.doctor_in_charge_id = '$doctor_in_charge_id' AND registration_destination.health_facility_item_id = '$health_facility_item_id' AND registration_insurance.is_primary = 1 AND registration_insurance.company_id = '$company' AND registration_destination.register_date BETWEEN '" . date('Y-m-d') . " 00:00:00' AND '" . date('Y-m-d') . " 23:59:59'
		")->result();

		if($patient != []) {
			return 'N';
		} else {
			return 'Y';
		}
	}

	public function filter_data($filter,$ssr = false) {
		$this->selectFields = "a.*,b.*,b.is_new as registration_destination_is_new,b.id as registration_destination_id,d.name as health_facility_item_name,
								e.name as health_facility_name,e.id as health_facility_id,f.fullname as doctor_in_charge_name, f.ttd as employee_signature, f.bride_bpjs,g.name as how_to_enter_name,
								c.*,h.name as alias_name,i.name as type_how_to_enter_name,i.code as type_how_to_enter_id,
								j.name as sex_name,k.name as exit_name,l.name as type_register_name,m.name as type_emergency_name,
								n.name as type_emergency_case_name,bd.name as bed_name, ro.id as room_id, ro.name as room_name,ro.service_class_id, h2.name as type_visit_name,ds.hour_start,ds.hour_end, cp.logo_url";
		$this->joinFields = array(
			array('registration_destination b', 'a.id = b.registration_id', 'LEFT'),
			array('registration_insurance ri', 'b.id = ri.registration_destination_id and ri.is_primary="1"', 'LEFT'),
			array('company cp', 'ri.company_id = cp.id', 'LEFT'),
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
			array('helper h2', 'a.type_visit_id = h2.code AND h2.variable="type_visit"', 'LEFT'),
			array('doctor_schedule_item ds', 'b.doctor_schedule_item_id = ds.id', 'LEFT'),
		);
		unset($filter['date_start']);
		unset($filter['date_end']);
		if(isset($filter['registration_destination_id'])) { $this->db->where('b.id', $filter['registration_destination_id']); unset($filter['registration_destination_id']); }
		if(isset($filter['is_new'])) { $this->db->where('c.is_new', $filter['is_new']); unset($filter['is_new']); }
		if(isset($filter['sex'])) { $this->db->where('c.sex', $filter['sex']); unset($filter['sex']); }
		if(isset($filter['code'])) { $this->db->where('a.code', $filter['code']); unset($filter['code']); }
		if(isset($filter['norm'])) { $this->db->where('c.norm', $filter['norm']); unset($filter['norm']); }
		if(isset($filter['name'])) { $this->db->where('c.name', $filter['name']); unset($filter['name']); }
		if(isset($filter['health_facility_id'])) { $this->db->where('d.health_facility_id', $filter['health_facility_id']); unset($filter['health_facility_id']); }
		if(isset($filter['health_facility_item_id'])) { $this->db->where('b.health_facility_item_id', $filter['health_facility_item_id']); unset($filter['health_facility_item_id']); }

		$this->db->where($filter);
		if($ssr) {
			return $this->read_ssr();
		} else {
			return $this->read();
		}
	}

	public function check_emergency_double($norm, $health_facility_item_id, $doctor_in_charge_id) {
		$now = date('Y-m-d');

		$query = "
			SELECT rd.id
			FROM registration_destination rd 
			INNER JOIN registration r ON rd.registration_id = r.id 
			WHERE r.norm = '$norm' 
			AND rd.health_facility_item_id = '$health_facility_item_id' 
			AND rd.doctor_in_charge_id = '$doctor_in_charge_id' 
			AND rd.is_checkout = 0 
			AND r.is_cancel = 0 
			AND r.register_date >= '$now 00:00:00'
			AND r.register_date <= '$now 23:59:59'
		";
		
		$result = $this->db->query($query)->result_array();

		if(count($result) > 0) {
			return 0;
		}

		return 1;
	}

	public function check_bpjs_feasible($norm) {
		$now = date('Y-m-d');

		$time7DaysBefore = date('Y-m-d', strtotime('+8 days'));

		$query = "
			SELECT rd.id
			FROM registration_destination rd 
			INNER JOIN registration_insurance ri ON ri.registration_destination_id = rd.id AND ri.is_primary = 1
			INNER JOIN registration r ON rd.registration_id = r.id
			INNER JOIN company c ON ri.company_id = c.id
			WHERE r.norm = '$norm'
			AND r.type_register_id = 1 
			AND rd.health_facility_item_id != 'd350e55d-6f8a-4c42-b269-cfda717fbf8b' 
			AND rd.health_facility_item_id != 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e' 
			AND ri.company_id IS NOT NULL
			AND r.is_cancel = 0 
			AND r.register_date >= '$time7DaysBefore 00:00:00'
			AND r.register_date <= '$now 23:59:59'
			ORDER BY r.register_date DESC
			LIMIT 1
		";
		
		$result = $this->db->query($query)->result_array();
		return $result;
	}

	public function check_bpjs_feasible_check_kontrol($norm, $tgl_control) {
		// $now = date('Y-m-d');
		$now = $tgl_control;

		$time7DaysBefore = date('Y-m-d', strtotime($tgl_control.' -90 days'));

		$query = "
			SELECT rd.id,r.register_date,hfi.name as health_facility_item_name
			FROM registration_destination rd 
			INNER JOIN registration_insurance ri ON ri.registration_destination_id = rd.id AND ri.is_primary = 1
			INNER JOIN registration r ON rd.registration_id = r.id
			INNER JOIN health_facility_item hfi ON rd.health_facility_item_id = hfi.id
			INNER JOIN company c ON ri.company_id = c.id
			WHERE r.norm = '$norm'
			AND r.type_register_id = 1 
			AND rd.health_facility_item_id != 'd350e55d-6f8a-4c42-b269-cfda717fbf8b' 
			AND rd.health_facility_item_id != 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e' 
			AND ri.company_id IS NOT NULL
			AND r.is_cancel = 0 
			ORDER BY r.register_date DESC
			LIMIT 1
		";
		// AND r.register_date >= '$time7DaysBefore 00:00:00'
		// AND r.register_date <= '$now 23:59:59'
		
		$result = $this->db->query($query)->result_array();
		return $result;
	}

	public function check_new_old_hf($norm) {
		$query = "
			SELECT registration.id
			FROM registration  
			WHERE registration.norm = '$norm' AND registration.is_cancel = 0 
		";
		
		$result = $this->db->query($query)->result_array();

		if(count($result) > 1) {
			return 0;
		}

		return 1;
	}
}?>