<?php class Dashboard_model extends MY_Model {
	protected $tableName = 'registration';
	// protected $resultMode = 'object';	  
	// public $primaryKey = 'norm';	
	// public $whereFields;
    // public $limit;
	// public $orderFields = array('a.norm' => 'DESC');
	// protected $selectFields = "a.*,";
	// protected $joinFields = array();

	function get_dashboard_data($filter) {
		$data = array();

		$income = $this->db->query("
			SELECT SUM(b.var_3) as total
			FROM payment as a
			LEFT JOIN payment_item as b ON a.id = b.payment_id
			LEFT JOIN transaction_invoice as c ON a.invoice_id = c.id
			WHERE c.date_added BETWEEN '" . $filter['date_start'] . " 00:00:00' AND '" . $filter['date_end'] . " 23:59:59'
		")->result_array();

		$pharmacy_income = $this->db->query("
			SELECT SUM(a.var_3) as total
			FROM pharmacy_prescription as a
			WHERE a.date_added BETWEEN '" . $filter['date_start'] . " 00:00:00' AND '" . $filter['date_end'] . " 23:59:59'
		")->result_array();

		$clinic_income = $this->db->query("
			SELECT SUM(b.var_3) as total
			FROM payment as a
			LEFT JOIN payment_item as b ON a.id = b.payment_id
			LEFT JOIN transaction_invoice as c ON a.invoice_id = c.id
			LEFT JOIN registration as d ON c.registration_id = d.id
			LEFT JOIN registration_destination as e ON d.id = e.registration_id
			LEFT JOIN health_facility_item as f ON e.health_facility_item_id = f.id
			LEFT JOIN health_facility as g ON f.health_facility_id = g.id
			WHERE c.date_added BETWEEN '" . $filter['date_start'] . " 00:00:00' AND '" . $filter['date_end'] . " 23:59:59' AND g.name = 'Poliklinik'
		")->result_array();

		$sex = $this->db->query("
			SELECT sex, count(*) as total
			FROM registration
			LEFT JOIN patient
			ON registration.norm = patient.norm
			WHERE registration.register_date BETWEEN '" . $filter['date_start'] . " 00:00:00' AND '" . $filter['date_end'] . " 23:59:59'
			GROUP BY sex
		")->result_array();
		
		$is_new = $this->db->query("
			SELECT is_new, count(*) as total
			FROM registration
			LEFT JOIN patient
			ON registration.norm = patient.norm
			WHERE registration.register_date BETWEEN '" . $filter['date_start'] . " 00:00:00' AND '" . $filter['date_end'] . " 23:59:59'
			GROUP BY is_new
		")->result_array();

		$how_to_enter = $this->db->query("
			SELECT how_to_enter.name, how_to_enter_id, count(*) as total
			FROM registration
			LEFT JOIN how_to_enter
			ON registration.how_to_enter_id = how_to_enter.id
			WHERE registration.register_date BETWEEN '" . $filter['date_start'] . " 00:00:00' AND '" . $filter['date_end'] . " 23:59:59'
			GROUP BY how_to_enter_id
		")->result_array();

		$type_register = $this->db->query("
			SELECT type_register_id, count(*) as total
			FROM registration
			WHERE registration.register_date BETWEEN '" . $filter['date_start'] . " 00:00:00' AND '" . $filter['date_end'] . " 23:59:59'
			GROUP BY type_register_id
		")->result_array();

		$radiology = $this->db->query("
			SELECT COUNT(*) as total
			FROM radiology_request as a
			LEFT JOIN soap as b ON a.soap_id = b.id
			LEFT JOIN registration_destination as c ON b.registration_destination_id = c.id
			LEFT JOIN registration as d ON c.registration_id = d.id
			WHERE d.register_date BETWEEN '" . $filter['date_start'] . " 00:00:00' AND '" . $filter['date_end'] . " 23:59:59'
		")->result_array();

		$laboratory = $this->db->query("
			SELECT COUNT(*) as total
			FROM laboratory_request as a
			LEFT JOIN soap as b ON a.soap_id = b.id
			LEFT JOIN registration_destination as c ON b.registration_destination_id = c.id
			LEFT JOIN registration as d ON c.registration_id = d.id
			WHERE d.register_date BETWEEN '" . $filter['date_start'] . " 00:00:00' AND '" . $filter['date_end'] . " 23:59:59'
		")->result_array();

		$physiotherapy = $this->db->query("
			SELECT COUNT(*) as total
			FROM physiotherapy_request as a
			LEFT JOIN soap as b ON a.soap_id = b.id
			LEFT JOIN registration_destination as c ON b.registration_destination_id = c.id
			LEFT JOIN registration as d ON c.registration_id = d.id
			WHERE d.register_date BETWEEN '" . $filter['date_start'] . " 00:00:00' AND '" . $filter['date_end'] . " 23:59:59'
		")->result_array();

		// $health_facility_item = $this->db->query("
		// 	SELECT health_facility_item.name name, health_facility_item_id, count(*) as total
		// 	FROM registration
		// 	LEFT JOIN registration_destination
		// 	ON registration.id = registration_destination.registration_id
		// 	LEFT JOIN health_facility_item
		// 	ON registration_destination.health_facility_item_id = health_facility_item.id
		// 	WHERE registration.register_date BETWEEN '" . $filter['date_start'] . " 00:00:00' AND '" . $filter['date_end'] . " 23:59:59'
		// 	GROUP BY health_facility_item_id
		// ")->result_array();

		// $health_facility = $this->db->query("
		// 	SELECT health_facility.name, health_facility.id, count(*) as total
		// 	FROM registration
		// 	LEFT JOIN registration_destination
		// 	ON registration.id = registration_destination.registration_id
		// 	LEFT JOIN health_facility_item
		// 	ON registration_destination.health_facility_item_id = health_facility_item.id
		// 	LEFT JOIN health_facility
		// 	ON health_facility_item.health_facility_id = health_facility.id
		// 	WHERE registration.register_date BETWEEN '" . $filter['date_start'] . " 00:00:00' AND '" . $filter['date_end'] . " 23:59:59'
		// 	GROUP BY health_facility.id
		// ")->result_array();

		$data['income'] = ($income[0]['total'] != null) ? intval($income[0]['total']) : 0;
		$data['pharmacy_income'] = ($pharmacy_income[0]['total'] != null) ? intval($pharmacy_income[0]['total']) : 0;
		$data['clinic_income'] = ($clinic_income[0]['total'] != null) ? intval($clinic_income[0]['total']) : 0;

		$data['total_patient'] = 0;
		
		// Default value
		$data['sex']['male'] = 0;
		$data['sex']['female'] = 0;
		foreach ($sex as $row) {
			if($row['sex'] == 1) {
				$data['sex']['male'] = intval($row['total']);
				$data['total_patient'] = $data['total_patient'] + intval($row['total']);
			} else if($row['sex'] == 0) {
				$data['sex']['female'] = intval($row['total']);
				$data['total_patient'] = $data['total_patient'] + intval($row['total']);
			}
		}

		foreach ($is_new as $row) {
			if($row['is_new'] == 'Y') {
				$data['patient_type']['new'] = intval($row['total']);
			} else if($row['is_new'] == 'N') {
				$data['patient_type']['old'] = intval($row['total']);
			}
		}

		foreach ($how_to_enter as $row) {
			$data['how_to_enter_id'][$row['name']] = intval($row['total']);
		}

		// Default value
		$data['type_register']['Outpatient'] = 0;
		$data['type_register']['External'] = 0;
		$data['type_register']['Emergency'] = 0;
		$data['type_register']['Inpatient'] = 0;
		foreach ($type_register as $row) {
			if($row['type_register_id'] == '1') {
				$data['type_register']['Outpatient'] = intval($row['total']);
			} else if($row['type_register_id'] == '2') {
				$data['type_register']['External'] = intval($row['total']);
			} else if($row['type_register_id'] == '3') {
				$data['type_register']['Emergency'] = intval($row['total']);
			} else if($row['type_register_id'] == '4') {
				$data['type_register']['Inpatient'] = intval($row['total']);
			}
		}

		$data['radiology'] = intval($radiology[0]['total']);
		$data['laboratory'] = intval($laboratory[0]['total']);
		$data['physiotherapy'] = intval($physiotherapy[0]['total']);

		// foreach ($health_facility_item as $row) {
		// 	$data['health_facility_item_id'][$row['name']] = intval($row['total']);
		// }

		// foreach ($health_facility as $row) {
		// 	$data['health_facility_id'][$row['name']] = intval($row['total']);
		// }

		return $data;
	}

	function get_service_activity($date_start, $date_end) {
		$result = [];

		$query = $this->db->query("
			SELECT registration.register_date date, health_facility_item.name health_facility_item_name, health_facility_item_id, employee.name employee_name, employee.id employee_id, COUNT(*) as total
			FROM registration
			LEFT JOIN registration_destination
			ON registration.id = registration_destination.registration_id
			LEFT JOIN health_facility_item
			ON registration_destination.health_facility_item_id = health_facility_item.id
			LEFT JOIN employee
			ON registration_destination.doctor_in_charge_id= employee.id
			WHERE registration.register_date BETWEEN '" . $date_start . " 00:00:00' AND '" . $date_end . " 23:59:59'
			GROUP BY MONTH(registration.register_date), employee.id, health_facility_item_id
		")->result_array();

		$query2 = $this->db->query("
			SELECT registration.register_date date, health_facility_item.name health_facility_item_name, health_facility_item_id, employee.name employee_name, employee.id employee_id, COUNT(*) as total
			FROM registration
			LEFT JOIN registration_destination
			ON registration.id = registration_destination.registration_id
			LEFT JOIN health_facility_item
			ON registration_destination.health_facility_item_id = health_facility_item.id
			LEFT JOIN employee
			ON registration_destination.doctor_in_charge_id= employee.id
			WHERE registration.register_date BETWEEN '" . $date_start . "' AND '" . $date_end . "'
			GROUP BY health_facility_item_id
			ORDER BY registration.register_date ASC
		")->result_array();

		$query3 = $this->db->query("
			SELECT registration.register_date date, health_facility_item.name health_facility_item_name, health_facility_item_id, employee.name employee_name, employee.id employee_id, COUNT(*) as total
			FROM registration
			LEFT JOIN registration_destination
			ON registration.id = registration_destination.registration_id
			LEFT JOIN health_facility_item
			ON registration_destination.health_facility_item_id = health_facility_item.id
			LEFT JOIN employee
			ON registration_destination.doctor_in_charge_id= employee.id
			WHERE registration.register_date BETWEEN '" . $date_start . "' AND '" . $date_end . "'
			GROUP BY employee_id
			ORDER BY registration.register_date ASC
		")->result_array();

		$month = array(
			0 => 'January',
			1 => 'February',
			2 => 'March',
			3 => 'April',
			4 => 'May',
			5 => 'June',
			6 => 'July',
			7 => 'August',
			8 => 'September',
			9 => 'October',
			10 => 'November',
			11 => 'December',
		); 

		for ($j=0; $j < 12; $j++) { 
			$result[0][$j]['month'] = date('F',strtotime($month[$j]));
			$result[0][$j]['data'] = array();
			$tmp_total_month = 0;
			for ($i=0; $i < count($query); $i++) { 
				if(date('m',strtotime($query[$i]['date'])) == date('m',strtotime($month[$j]))) {
					$tmp = array(
						'health_facility_item_id' => $query[$i]['health_facility_item_id'],
						'health_facility_item_name' => $query[$i]['health_facility_item_name'],
						'employee_id' => $query[$i]['employee_id'],
						'employee_name' => $query[$i]['employee_name'],
						'total' => intval($query[$i]['total']),
					);
					array_push($result[0][$j]['data'],$tmp);					
					$tmp_total_month += intval($query[$i]['total']);
				}
			}
			$result[0][$j]['total'] = $tmp_total_month;
		}

		$result[1] = array();
		for ($i=0; $i < count($query2); $i++) { 
			$tmp = array(
				'health_facility_item_id' => $query2[$i]['health_facility_item_id'],
				'health_facility_item_name' => $query2[$i]['health_facility_item_name'],
				'total' => intval($query2[$i]['total']),
			);
			array_push($result[1],$tmp);
		}

		$result[2] = array();
		for ($i=0; $i < count($query3); $i++) { 
			$tmp = array(
				'employee_id' => $query3[$i]['employee_id'],
				'employee_name' => $query3[$i]['employee_name'],
				'total' => intval($query3[$i]['total']),
			);
			array_push($result[2],$tmp);
		}

		return $result;
	}

	public function get_new_old_visits($date_start, $date_end) {
		$result = [];

		$query = $this->db->query("
			SELECT registration.register_date date, health_facility_item.name health_facility_item_name, health_facility_item_id, employee.name employee_name, employee.id employee_id, is_new, COUNT(*) AS total
			FROM registration
			LEFT JOIN registration_destination
			ON registration.id = registration_destination.registration_id
			LEFT JOIN health_facility_item
			ON registration_destination.health_facility_item_id = health_facility_item.id
			LEFT JOIN employee
			ON registration_destination.doctor_in_charge_id= employee.id
			WHERE registration.register_date BETWEEN '" . $date_start . "' AND '" . $date_end . "' AND is_new = 'Y'
			GROUP BY MONTH(registration.register_date), is_new, employee.id, health_facility_item_id
		")->result_array();

		$query2 = $this->db->query("
			SELECT registration.register_date date, health_facility_item.name health_facility_item_name, health_facility_item_id, employee.name employee_name, employee.id employee_id, is_new, COUNT(*) AS total
			FROM registration
			LEFT JOIN registration_destination
			ON registration.id = registration_destination.registration_id
			LEFT JOIN health_facility_item
			ON registration_destination.health_facility_item_id = health_facility_item.id
			LEFT JOIN employee
			ON registration_destination.doctor_in_charge_id= employee.id
			WHERE registration.register_date BETWEEN '" . $date_start . "' AND '" . $date_end . "' AND is_new = 'N'
			GROUP BY MONTH(registration.register_date), is_new, employee.id, health_facility_item_id
		")->result_array();

		$month = array(
			0 => 'January',
			1 => 'February',
			2 => 'March',
			3 => 'April',
			4 => 'May',
			5 => 'June',
			6 => 'July',
			7 => 'August',
			8 => 'September',
			9 => 'October',
			10 => 'November',
			11 => 'December',
		); 

		for ($j=0; $j < 12; $j++) { 
			$result[$j]['month'] = date('F',strtotime($month[$j]));
			$result[$j]['data'] = array();
			$tmp_total_month_new = 0;
			for ($i=0; $i < count($query); $i++) { 
				if(date('m',strtotime($query[$i]['date'])) == date('m',strtotime($month[$j]))) {
					$tmp = array(
						'health_facility_item_id' => $query[$i]['health_facility_item_id'],
						'health_facility_item_name' => $query[$i]['health_facility_item_name'],
						'employee_id' => $query[$i]['employee_id'],
						'employee_name' => $query[$i]['employee_name'],
						'new_patient' => intval($query[$i]['total']),
						'old_patient' => 0,
					);
					array_push($result[$j]['data'],$tmp);
					$tmp_total_month_new += intval($query[$i]['total']);
				}
			}
			$result[$j]['total_new'] = $tmp_total_month_new;
		}

		for ($j=0; $j < 12; $j++) { 
			$tmp_total_month_old = 0;
			for ($i=0; $i < count($query2); $i++) { 
				if(date('m',strtotime($query2[$i]['date'])) == date('m',strtotime($month[$j]))) {
					for ($a=0; $a < count($result[$j]['data']); $a++) { 
						if($result[$j]['data'][$a]['health_facility_item_id'] == $query2[$i]['health_facility_item_id'] && $result[$j]['data'][$a]['employee_id'] == $query2[$i]['employee_id']) {
							$result[$j]['data'][$a]['old_patient'] = intval($query2[$i]['total']);
							$tmp_total_month_old += intval($query2[$i]['total']);
						}
					}
				}
			}
			$result[$j]['total_old'] = $tmp_total_month_old;
		}
		
		return $result;
	}

	public function get_visit_history($norm) {
		$data = [];

		$tr_administration = $this->db->query("
			SELECT registration.*, registration_destination.*, transaction_administration.*
			FROM registration
			LEFT JOIN registration_destination
			ON registration.id = registration_destination.registration_id
			LEFT JOIN transaction_administration
			ON registration.id = transaction_administration.registration_id
			WHERE costing_id IS NOT NULL AND registration.norm = '$norm'
		")->result_array();
		
		for ($i=0; $i < count($tr_administration); $i++) { 
			$data['tr_administration'][$i]['registration_id'] = $tr_administration[$i]['registration_id'];
			$data['tr_administration'][$i]['costing_id'] = $tr_administration[$i]['costing_id'];
			$data['tr_administration'][$i]['costing_name'] = $tr_administration[$i]['costing_name'];
			$data['tr_administration'][$i]['var_1'] = $tr_administration[$i]['var_1'];
			$data['tr_administration'][$i]['date_added'] = $tr_administration[$i]['date_added'];
		}

		$tr_nurse = $this->db->query("
			SELECT registration.*, registration_destination.*, nurse.*, transaction_nurse.*
			FROM registration 
			LEFT JOIN registration_destination
			ON registration.id = registration_destination.registration_id
			LEFT JOIN nurse
			ON registration_destination.id = nurse.registration_destination_id
			LEFT JOIN transaction_nurse
			ON nurse.id = transaction_nurse.nurse_id
			WHERE costing_id IS NOT NULL AND registration.norm = '$norm'
		")->result_array();
		
		for ($i=0; $i < count($tr_nurse); $i++) { 
			$data['tr_nurse'][$i]['tr_nurse']['nurse_id'] = $tr_nurse[$i]['nurse_id'];
			$data['tr_nurse'][$i]['tr_nurse']['costing_id'] = $tr_nurse[$i]['costing_id'];
			$data['tr_nurse'][$i]['tr_nurse']['costing_name'] = $tr_nurse[$i]['costing_name'];
			$data['tr_nurse'][$i]['tr_nurse']['var_1'] = $tr_nurse[$i]['var_1'];
			$data['tr_nurse'][$i]['tr_nurse']['date_added'] = $tr_nurse[$i]['date_added'];

			$data['tr_nurse'][$i]['registration']['code'] = $tr_nurse[$i]['code'];
			$data['tr_nurse'][$i]['registration']['type_register_id'] = $tr_nurse[$i]['type_register_id'];
			$data['tr_nurse'][$i]['registration']['how_to_enter_id'] = $tr_nurse[$i]['how_to_enter_id'];
			$data['tr_nurse'][$i]['registration']['icd10_id'] = $tr_nurse[$i]['icd10_id'];
			$data['tr_nurse'][$i]['registration']['icd10_code'] = $tr_nurse[$i]['icd10_code'];
			$data['tr_nurse'][$i]['registration']['icd10_name'] = $tr_nurse[$i]['icd10_name'];
			$data['tr_nurse'][$i]['registration']['register_date'] = $tr_nurse[$i]['register_date'];

			$data['tr_nurse'][$i]['registration_destination']['health_facility_item_id'] = $tr_nurse[$i]['health_facility_item_id'];
			$data['tr_nurse'][$i]['registration_destination']['doctor_in_charge_id'] = $tr_nurse[$i]['doctor_in_charge_id'];
			$data['tr_nurse'][$i]['registration_destination']['checkin_date'] = $tr_nurse[$i]['checkin_date'];
			$data['tr_nurse'][$i]['registration_destination']['checkout_date'] = $tr_nurse[$i]['checkout_date'];
			$data['tr_nurse'][$i]['registration_destination']['exit_id'] = $tr_nurse[$i]['exit_id'];
			$data['tr_nurse'][$i]['registration_destination']['exit_reason'] = $tr_nurse[$i]['exit_reason'];
			$data['tr_nurse'][$i]['registration_destination']['is_new'] = $tr_nurse[$i]['is_new'];

			$data['tr_nurse'][$i]['nurse']['employee_nurse_id'] = $tr_nurse[$i]['employee_nurse_id'];
			$data['tr_nurse'][$i]['nurse']['anamesa_type_id'] = $tr_nurse[$i]['anamesa_type_id'];
			$data['tr_nurse'][$i]['nurse']['family_name'] = $tr_nurse[$i]['family_name'];
			$data['tr_nurse'][$i]['nurse']['family_relation_id'] = $tr_nurse[$i]['family_relation_id'];
			$data['tr_nurse'][$i]['nurse']['main_complaint'] = $tr_nurse[$i]['main_complaint'];
			$data['tr_nurse'][$i]['nurse']['history_disease_now'] = $tr_nurse[$i]['history_disease_now'];
			$data['tr_nurse'][$i]['nurse']['history_disease_past'] = $tr_nurse[$i]['history_disease_past'];
			$data['tr_nurse'][$i]['nurse']['systolic_blood_pressure_arry'] = $tr_nurse[$i]['systolic_blood_pressure_arry'];
			$data['tr_nurse'][$i]['nurse']['general_state_arry'] = $tr_nurse[$i]['general_state_arry'];
			$data['tr_nurse'][$i]['nurse']['systolic_blood_pressure'] = $tr_nurse[$i]['systolic_blood_pressure'];
			$data['tr_nurse'][$i]['nurse']['diastolic_blood_pressure'] = $tr_nurse[$i]['diastolic_blood_pressure'];
			$data['tr_nurse'][$i]['nurse']['classification_blood_pressure_type_id'] = $tr_nurse[$i]['classification_blood_pressure_type_id'];
			$data['tr_nurse'][$i]['nurse']['pulse_frequency'] = $tr_nurse[$i]['pulse_frequency'];
			$data['tr_nurse'][$i]['nurse']['breath_frequency'] = $tr_nurse[$i]['breath_frequency'];
			$data['tr_nurse'][$i]['nurse']['oximetry_frequency'] = $tr_nurse[$i]['oximetry_frequency'];
			$data['tr_nurse'][$i]['nurse']['classification_oximetry_type_id'] = $tr_nurse[$i]['classification_oximetry_type_id'];
			$data['tr_nurse'][$i]['nurse']['temperature'] = $tr_nurse[$i]['temperature'];
			$data['tr_nurse'][$i]['nurse']['classification_temperature_type_id'] = $tr_nurse[$i]['classification_temperature_type_id'];
			$data['tr_nurse'][$i]['nurse']['blood_sugar'] = $tr_nurse[$i]['blood_sugar'];
			$data['tr_nurse'][$i]['nurse']['weight'] = $tr_nurse[$i]['weight'];
			$data['tr_nurse'][$i]['nurse']['classification_weight_type_id'] = $tr_nurse[$i]['classification_weight_type_id'];
			$data['tr_nurse'][$i]['nurse']['height'] = $tr_nurse[$i]['height'];
			$data['tr_nurse'][$i]['nurse']['imt'] = $tr_nurse[$i]['imt'];
			$data['tr_nurse'][$i]['nurse']['head_circumference'] = $tr_nurse[$i]['head_circumference'];
			$data['tr_nurse'][$i]['nurse']['chest_circumference'] = $tr_nurse[$i]['chest_circumference'];
			$data['tr_nurse'][$i]['nurse']['arm_circumference'] = $tr_nurse[$i]['arm_circumference'];
			$data['tr_nurse'][$i]['nurse']['stomach_circumference'] = $tr_nurse[$i]['stomach_circumference'];
			$data['tr_nurse'][$i]['nurse']['knee_circumference'] = $tr_nurse[$i]['knee_circumference'];
			$data['tr_nurse'][$i]['nurse']['aids'] = $tr_nurse[$i]['aids'];
			$data['tr_nurse'][$i]['nurse']['prothesa'] = $tr_nurse[$i]['prothesa'];
			$data['tr_nurse'][$i]['nurse']['disabled'] = $tr_nurse[$i]['disabled'];
			$data['tr_nurse'][$i]['nurse']['adl_type_id'] = $tr_nurse[$i]['adl_type_id'];
		}

		$tr_doctor = $this->db->query("
			SELECT registration.*, registration_destination.*, soap.*, soap_s.*, soap_o.*, soap_o_action.*, soap_a.*, soap_p.*, soap_p_action.*,transaction_doctor.*
			FROM registration
			LEFT JOIN registration_destination
			ON registration.id = registration_destination.registration_id
			LEFT JOIN soap
			ON registration_destination.id = soap.registration_destination_id
			LEFT JOIN soap_s
			ON soap.id = soap_s.soap_id
			LEFT JOIN soap_o
			ON soap.id = soap_o.soap_id
			LEFT JOIN soap_o_action
			ON soap_o.id = soap_o_action.soap_o_id
			LEFT JOIN soap_a
			ON soap.id = soap_a.soap_id
			LEFT JOIN soap_p
			ON soap.id = soap_p.soap_id
			LEFT JOIN soap_p_action
			ON soap_p.id = soap_p_action.soap_p_id
			LEFT JOIN transaction_doctor
			ON soap.id = transaction_doctor.soap_id
			WHERE costing_id IS NOT NULL AND registration.norm = '$norm'
		")->result_array();
		
		for ($i=0; $i < count($tr_doctor); $i++) { 
			$data['tr_doctor'][$i]['tr_doctor']['soap_id'] = $tr_doctor[$i]['soap_id'];
			$data['tr_doctor'][$i]['tr_doctor']['costing_id'] = $tr_doctor[$i]['costing_id'];
			$data['tr_doctor'][$i]['tr_doctor']['costing_name'] = $tr_doctor[$i]['costing_name'];
			$data['tr_doctor'][$i]['tr_doctor']['var_1'] = $tr_doctor[$i]['var_1'];
			$data['tr_doctor'][$i]['tr_doctor']['date_added'] = $tr_doctor[$i]['date_added'];

			$data['tr_doctor'][$i]['registration']['code'] = $tr_doctor[$i]['code'];
			$data['tr_doctor'][$i]['registration']['type_register_id'] = $tr_doctor[$i]['type_register_id'];
			$data['tr_doctor'][$i]['registration']['how_to_enter_id'] = $tr_doctor[$i]['how_to_enter_id'];
			$data['tr_doctor'][$i]['registration']['icd10_id'] = $tr_doctor[$i]['icd10_id'];
			$data['tr_doctor'][$i]['registration']['icd10_code'] = $tr_doctor[$i]['icd10_code'];
			$data['tr_doctor'][$i]['registration']['icd10_name'] = $tr_doctor[$i]['icd10_name'];
			$data['tr_doctor'][$i]['registration']['register_date'] = $tr_doctor[$i]['register_date'];

			$data['tr_doctor'][$i]['registration_destination']['health_facility_item_id'] = $tr_doctor[$i]['health_facility_item_id'];
			$data['tr_doctor'][$i]['registration_destination']['doctor_in_charge_id'] = $tr_doctor[$i]['doctor_in_charge_id'];
			$data['tr_doctor'][$i]['registration_destination']['checkin_date'] = $tr_doctor[$i]['checkin_date'];
			$data['tr_doctor'][$i]['registration_destination']['checkout_date'] = $tr_doctor[$i]['checkout_date'];
			$data['tr_doctor'][$i]['registration_destination']['exit_id'] = $tr_doctor[$i]['exit_id'];
			$data['tr_doctor'][$i]['registration_destination']['exit_reason'] = $tr_doctor[$i]['exit_reason'];
			$data['tr_doctor'][$i]['registration_destination']['is_new'] = $tr_doctor[$i]['is_new'];

			$data['tr_doctor'][$i]['soap']['soap_s']['anamesa_type_id'] = $tr_doctor[$i]['anamesa_type_id'];
			$data['tr_doctor'][$i]['soap']['soap_s']['family_name'] = $tr_doctor[$i]['family_name'];
			$data['tr_doctor'][$i]['soap']['soap_s']['family_relation_id'] = $tr_doctor[$i]['family_relation_id'];
			$data['tr_doctor'][$i]['soap']['soap_s']['main_complaint'] = $tr_doctor[$i]['main_complaint'];
			$data['tr_doctor'][$i]['soap']['soap_s']['history_disease_now'] = $tr_doctor[$i]['history_disease_now'];
			$data['tr_doctor'][$i]['soap']['soap_s']['history_disease_past'] = $tr_doctor[$i]['history_disease_past'];
			$data['tr_doctor'][$i]['soap']['soap_s']['history_drugs'] = $tr_doctor[$i]['history_drugs'];
			$data['tr_doctor'][$i]['soap']['soap_s']['history_allergy'] = $tr_doctor[$i]['history_allergy'];
			
			$data['tr_doctor'][$i]['soap']['soap_o']['vitals'] = $tr_doctor[$i]['vitals'];
			$data['tr_doctor'][$i]['soap']['soap_o']['anthropometry'] = $tr_doctor[$i]['anthropometry'];
			$data['tr_doctor'][$i]['soap']['soap_o']['physical_check'] = $tr_doctor[$i]['physical_check'];
			$data['tr_doctor'][$i]['soap']['soap_o']['anythingelse'] = $tr_doctor[$i]['anythingelse'];

			$data['tr_doctor'][$i]['soap']['soap_o_action']['canvasing_type_id'] = $tr_doctor[$i]['canvasing_type_id'];
			$data['tr_doctor'][$i]['soap']['soap_o_action']['canvasing_text'] = $tr_doctor[$i]['canvasing_text'];
			$data['tr_doctor'][$i]['soap']['soap_o_action']['attr1'] = $tr_doctor[$i]['attr1'];
			$data['tr_doctor'][$i]['soap']['soap_o_action']['attr2'] = $tr_doctor[$i]['attr2'];

			$data['tr_doctor'][$i]['soap']['soap_a']['diagnosis'] = $tr_doctor[$i]['diagnosis'];
			$data['tr_doctor'][$i]['soap']['soap_a']['development_notes'] = $tr_doctor[$i]['development_notes'];
			$data['tr_doctor'][$i]['soap']['soap_a']['anythingelse'] = $tr_doctor[$i]['anythingelse'];

			$data['tr_doctor'][$i]['soap']['soap_p']['icd9cm_id'] = $tr_doctor[$i]['icd9cm_id'];
			$data['tr_doctor'][$i]['soap']['soap_p']['therapy_plan'] = $tr_doctor[$i]['therapy_plan'];

			$data['tr_doctor'][$i]['soap']['soap_p_action']['icd10_id'] = $tr_doctor[$i]['icd10_id'];
		}
		// var_dump($patient);

		return $data;
	}

}?>