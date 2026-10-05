<?php class Patient_val_model extends MY_Model {
	protected $tableName = 'patient';
	protected $resultMode = 'object';	  
	public $primaryKey = 'norm';	
	public $whereFields;
    public $limit = 25;
    // public $limit;
    public $offset;
	public $orderFields = array('a.norm' => 'DESC');
	protected $selectFields = "a.norm,a.name,a.idcard,a.birth_date,a.sex,a.phone,a.bride_no_bpjs,a.is_new";
	protected $joinFields = array();

	public function filter_data($filter,$ssr = false) {
		$this->selectFields = "a.*,b.name as sex_name,g.name as patient_issue_name, h.name as alias_name, i.name as typeidcard_name,
								c.id_village as village_id,c.nm_village as village_name,
								d.id_district as district_id,d.nm_district as district_name,
								e.id_regency as regency_id,e.nm_regency as regency_name,
								f.id_province as province_id,f.nm_province as province_name";
		$this->joinFields = array(
			array('helper b', 'a.sex = b.code AND b.variable="sex"', 'LEFT'),
			array('village c', 'a.address_id = c.id_village', 'LEFT'),
			array('district d', 'c.id_district = d.id_district', 'LEFT'),
			array('regency e', 'd.id_regency = e.id_regency', 'LEFT'),
			array('province f', 'e.id_province = f.id_province', 'LEFT'),
			array('helper g', 'a.patient_issue_id = g.code AND g.variable="patient_issue"', 'LEFT'),
			array('helper h', 'a.alias = h.code AND h.variable="alias"', 'LEFT'),
			array('helper i', 'a.typeidcard = i.code AND i.variable="typeidcard"', 'LEFT'),
		);

		$institute_branch_id = $filter['institute_branch_id'];
		unset($filter['institute_branch_id']);
		// $this->db->where('a.institute_branch_id',$institute_branch_id);
		// $this->db->where('b.institute_branch_id',$institute_branch_id);
		// $this->db->where('g.institute_branch_id',$institute_branch_id);
		// $this->db->where('h.institute_branch_id',$institute_branch_id);
		// $this->db->where('i.institute_branch_id',$institute_branch_id);

		if(isset($filter['village_id'])) { $this->db->where('c.id_village', $filter['village_id']); unset($filter['village_id']); }
		if(isset($filter['district_id'])) { $this->db->where('d.id_district', $filter['district_id']); unset($filter['district_id']); }
		if(isset($filter['regency_id'])) { $this->db->where('e.id_regency', $filter['regency_id']); unset($filter['regency_id']); }
		if(isset($filter['province_id'])) { $this->db->where('f.id_province', $filter['province_id']); unset($filter['province_id']); }
		if(isset($filter['name'])) { $this->db->where('a.name', $filter['name']); unset($filter['name']); }

		$this->db->where($filter);
		if($ssr) {
			return $this->read_ssr();
		} else {
			return $this->read();
		}
	}
}?>