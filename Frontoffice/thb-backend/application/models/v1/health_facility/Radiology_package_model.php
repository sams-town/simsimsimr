<?php class Radiology_package_model extends MY_Model {
	protected $tableName = 'radiology_package';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	public $selectFields = "a.*,b.name as costing_name";
	protected $joinFields = array(
        array('costing b', 'a.costing_id = b.id', 'LEFT'),
	);

	public function get_most_used_package($q_strings) {
		// $query = "SELECT COUNT(*) AS total, rp.*, rp.name as costing_name
		// FROM radiology_package rp
		// LEFT JOIN radiology_request_item rri
		// ON rri.radiology_package_id = rp.id
		// WHERE rp.is_delete = 0
		// GROUP BY rp.id
		// ORDER BY total DESC
		// ";
		
		$where = '';

		if ($q_strings['company_insurance_id'] == 'bb0d633f-99e1-4990-901b-0417a14e3cb6') {
			$where = "AND csc.company_insurance_id = '".$q_strings['company_insurance_id']."'";
		}

		$query = "SELECT COUNT(*) AS total, rp.*, rp.name as costing_name,
		csc.id as csc_id,
		csc.health_facility_item_id as csc_health_facility_item_id,
		csc.service_class_id as csc_service_class_id,
		csc.company_insurance_id as csc_company_insurance_id,
		csc.bride_code as csc_bride_code,
		csc.bride_satuan as csc_bride_satuan,
		csc.var_1 as csc_var_1,
		csc.var_2 as csc_var_2,
		csc.var_3 as csc_var_3,
		csc.var_4 as csc_var_4,
		csc.var_5 as csc_var_5,
		csc.persediaan as csc_persediaan,
		csc.penjualan as csc_penjualan,
		csc.disc_penjualan as csc_disc_penjualan,
		csc.bride_department as csc_bride_department
		FROM radiology_package rp
		LEFT JOIN radiology_request_item rri ON rri.radiology_package_id = rp.id
		LEFT JOIN costing_services_class csc ON rp.costing_id = csc.costing_id
		WHERE rp.is_delete = 0 {$where}
		GROUP BY rp.id
		ORDER BY total DESC
		";
		
		$res = $this->db->query($query)->result();
		
		return $res;
	}

	public function get_most_used_category() {
		$query = "SELECT COUNT(*) AS total, rp.category
		FROM radiology_package rp
		LEFT JOIN radiology_request_item rri
		ON rri.radiology_package_id = rp.id
		WHERE rp.is_delete = 0
		GROUP BY rp.category
		ORDER BY total DESC
		";
		
		$res = $this->db->query($query)->result();
		
		return $res;
	}
}
?>