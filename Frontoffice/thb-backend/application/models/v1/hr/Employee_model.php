<?php class Employee_model extends MY_Model {
	protected $tableName = 'employee';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('d.name' => 'ASC');
	public $selectFields = "a.*,s.name as sex_name, b.date_expired as position_date_expired,c.id as position_id,c.name as position_name,d.id as health_facility_item_id,d.name as health_facility_item_name,
								e.id as health_facility_id,e.name as health_facility_name";
	protected $joinFields = array(
		array('helper s', 'a.sex = s.code AND s.variable="sex"', 'LEFT'),
		array('employee_position b', 'a.id = b.employee_id', 'LEFT'),
		array('position c', 'b.position_id = c.id', 'LEFT'),
		array('health_facility_item d', 'c.health_facility_item_id = d.id', 'LEFT'),
		array('health_facility e', 'd.health_facility_id = e.id', 'LEFT'),
	);
	

	public function get_position_health_facility_item($institute_branch_id, $health_facility_item_id) {
		$detail = $this->db->query("
			SELECT employee.*, position.name AS position_name, health_facility_item.name AS health_facility_item_name  
			FROM employee
			LEFT JOIN employee_position
			ON employee.id = employee_position.employee_id
			LEFT JOIN position
			ON employee_position.position_id = position.id
			LEFT JOIN health_facility_item
			ON position.health_facility_item_id = health_facility_item.id
			WHERE position.institute_branch_id = '$institute_branch_id' AND health_facility_item.id = '$health_facility_item_id'
			ORDER BY employee.name
		")->result();
	
		return $this->convert_data($detail);
	}
	

	public function get_position_health_facility_item_schedule_day($institute_branch_id, $health_facility_item_id, $day) {
		$detail = $this->db->query("
			SELECT employee.*, position.name AS position_name, health_facility_item.name AS health_facility_item_name, doctor_schedule_item.hour_start, doctor_schedule_item.hour_end
			FROM employee
			LEFT JOIN employee_position
			ON employee.id = employee_position.employee_id
			LEFT JOIN position
			ON employee_position.position_id = position.id
			LEFT JOIN health_facility_item ON position.health_facility_item_id = health_facility_item.id
			LEFT JOIN doctor_schedule ON employee.id = doctor_schedule.doctor_id
			LEFT JOIN doctor_schedule_item ON doctor_schedule.id = doctor_schedule_item.doctor_schedule_id
			WHERE position.institute_branch_id = '$institute_branch_id' AND health_facility_item.bride_bpjs = '$health_facility_item_id' AND doctor_schedule_item.day = '$day'
		")->result();
	
		return $this->convert_data($detail);
	}
	

	public function get_position_specialist($institute_branch_id) {
		$detail = $this->db->query("
			SELECT employee.*, position.name AS position_name, health_facility_item.name AS health_facility_item_name  
			FROM employee
			LEFT JOIN employee_position
			ON employee.id = employee_position.employee_id
			LEFT JOIN position
			ON employee_position.position_id = position.id
			LEFT JOIN health_facility_item
			ON position.health_facility_item_id = health_facility_item.id
			WHERE position.institute_branch_id = '$institute_branch_id'
			ORDER BY employee.name
		")->result();
	
		return $this->convert_data($detail);
	}
	

	public function get_position_general($institute_branch_id) {
		$detail = $this->db->query("
			SELECT employee.*, position.name AS position_name, health_facility_item.name AS health_facility_item_name  
			FROM employee
			LEFT JOIN employee_position
			ON employee.id = employee_position.employee_id
			LEFT JOIN position
			ON employee_position.position_id = position.id
			LEFT JOIN health_facility_item
			ON position.health_facility_item_id = health_facility_item.id
			WHERE position.id = 'd93bd445-df1e-451a-bf95-5fc10546c133'
			AND position.institute_branch_id = '$institute_branch_id'
			ORDER BY employee.name
		")->result();
	
		return $this->convert_data($detail);
	}

	public function get_all_doctor() {
		$detail = $this->db->query("
			SELECT e.*, p.name AS position_name, hfi.name AS health_facility_item_name
			FROM employee e
			INNER JOIN employee_position ep
			ON e.id = ep.employee_id
			INNER JOIN position p
			ON ep.position_id = p.id
			INNER JOIN health_facility_item hfi
			ON p.health_facility_item_id = hfi.id
			WHERE p.is_doctor = 1
			ORDER BY e.name
		")->result();
	
		return $this->convert_data($detail);
	}
}?>