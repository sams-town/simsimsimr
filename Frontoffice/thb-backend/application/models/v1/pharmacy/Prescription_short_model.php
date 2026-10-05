<?php class Prescription_short_model extends MY_Model {
	protected $tableName = 'pharmacy_prescription';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.date_added' => 'DESC');
	protected $selectFields = "a.*,
		coalesce(a.registration_destination_id, s.registration_destination_id) as registration_destination_id, sp.soap_id,
	";
	protected $joinFields = array(
		array('soap_p sp', 'a.soap_p_id = sp.id', 'LEFT'),
		array('soap s', 'sp.soap_id = s.id', 'LEFT'),
		array('registration_insurance ri', 'coalesce(a.registration_destination_id, s.registration_destination_id) = ri.registration_destination_id AND ri.is_primary ="1"', 'LEFT'),
		array('registration_destination rd', 'coalesce(a.registration_destination_id, s.registration_destination_id) = rd.id', 'LEFT'),
	);

	public function filter_data($filter,$ssr = false) {
		$this->selectFields = "a.*,
		d.name as depo_name,
		p.name as patient_name, p.birth_date as patient_birth_date, p.birth_place as patient_birth_place,
		hfi.name as health_facility_item_name,
		coalesce(a.registration_destination_id, s.registration_destination_id) as registration_destination_id, sp.soap_id,
		hri.name as insurance_name, c.name as company_name,
		e.fullname as doctor_in_charge_name,
		hf.name as status
		";
		$this->joinFields = array(
			array('pharmacy_depo d', 'a.pharmacy_depo_id = d.id', 'LEFT'),
			array('patient p', 'a.norm = p.norm', 'LEFT'),
			array('health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'LEFT'),
			array('soap_p sp', 'a.soap_p_id = sp.id', 'LEFT'),
			array('soap s', 'sp.soap_id = s.id', 'LEFT'),
			array('registration_insurance ri', 'coalesce(a.registration_destination_id, s.registration_destination_id) = ri.registration_destination_id AND ri.is_primary ="1"', 'LEFT'),
			array('company c', 'ri.company_id = c.id', 'LEFT'),
			array('helper hri', 'ri.insurance_id = hri.code AND hri.variable="insurance"', 'LEFT'),
			array('employee e', 'a.doctor_in_charge_id = e.id', 'LEFT'),
			array('helper hf', 'a.flag = hf.code AND hf.variable = "type_prescription_flag"')
		);
		if(isset($filter['date_start']) && isset($filter['date_start'])) {
			$where = "`a`.`date_added` BETWEEN '" . $filter['date_start'] . " 00:00:00' AND '" . $filter['date_start'] . " 23:59:59'";
			$this->db->where($where);
			unset($filter['date_start']);
			unset($filter['date_start']);
		}

		if(isset($filter['institute_branch_id'])) { $this->db->where('hf.institute_branch_id', $filter['institute_branch_id']); unset($filter['registration_destination_id']); }
		if(isset($filter['institute_branch_id'])) { $this->db->where('hri.institute_branch_id', $filter['institute_branch_id']); unset($filter['registration_destination_id']); }
		// if(isset($filter['institute_branch_id'])) { $this->db->where('j.institute_branch_id', $filter['institute_branch_id']); unset($filter['registration_destination_id']); }
		// if(isset($filter['institute_branch_id'])) { $this->db->where('l.institute_branch_id', $filter['institute_branch_id']); unset($filter['registration_destination_id']); }
		// if(isset($filter['institute_branch_id'])) { $this->db->where('m.institute_branch_id', $filter['institute_branch_id']); unset($filter['registration_destination_id']); }
		// if(isset($filter['institute_branch_id'])) { $this->db->where('n.institute_branch_id', $filter['institute_branch_id']); unset($filter['registration_destination_id']); }
		// if(isset($filter['registration_destination_id'])) { $this->db->where('s.registration_destination_id', $filter['registration_destination_id']); unset($filter['registration_destination_id']); }
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