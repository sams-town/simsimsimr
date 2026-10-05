<?php class Laboratory_package_model extends MY_Model {
	protected $tableName = 'laboratory_package';
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

	public function get_most_used_package() {
		// $query = "SELECT COUNT(*) AS total, rp.*, rp.name as costing_name
		// FROM laboratory_package rp
		// LEFT JOIN laboratory_request_item rri
		// ON rri.laboratory_package_id = rp.id
		// WHERE rp.is_delete = 0
		// GROUP BY rp.id
		// ORDER BY total DESC
		// ";

		// $query = "SELECT COUNT(*) AS total, rp.*, rp.name as costing_name,
		// csc.id as csc_id,
		// csc.health_facility_item_id as csc_health_facility_item_id,
		// csc.service_class_id as csc_service_class_id,
		// csc.company_insurance_id as csc_company_insurance_id,
		// csc.bride_code as csc_bride_code,
		// csc.bride_satuan as csc_bride_satuan,
		// csc.var_1 as csc_var_1,
		// csc.var_2 as csc_var_2,
		// csc.var_3 as csc_var_3,
		// csc.var_4 as csc_var_4,
		// csc.var_5 as csc_var_5,
		// csc.persediaan as csc_persediaan,
		// csc.penjualan as csc_penjualan,
		// csc.disc_penjualan as csc_disc_penjualan,
		// csc.bride_department as csc_bride_department
		// FROM laboratory_package rp
		// LEFT JOIN laboratory_request_item rri ON rri.laboratory_package_id = rp.id
		// LEFT JOIN costing_services_class csc ON rp.costing_id = csc.costing_id
		// WHERE rp.is_delete = 0
		// GROUP BY rp.id
		// ORDER BY total DESC
		// ";

		$query = "SELECT
		agg.total,
		agg.*,
		agg.name AS costing_name,
		csc.id AS csc_id,
		csc.health_facility_item_id AS csc_health_facility_item_id,
		csc.service_class_id AS csc_service_class_id,
		csc.company_insurance_id AS csc_company_insurance_id,
		csc.bride_code AS csc_bride_code,
		csc.bride_satuan AS csc_bride_satuan,
		csc.var_1 AS csc_var_1,
		csc.var_2 AS csc_var_2,
		csc.var_3 AS csc_var_3,
		csc.var_4 AS csc_var_4,
		csc.var_5 AS csc_var_5,
		csc.persediaan AS csc_persediaan,
		csc.penjualan AS csc_penjualan,
		csc.disc_penjualan AS csc_disc_penjualan,
		csc.bride_department AS csc_bride_department
		FROM (
			SELECT rp.*, COUNT(rri.id) AS total
			FROM laboratory_package rp
			LEFT JOIN laboratory_request_item rri ON rri.laboratory_package_id = rp.id
			WHERE rp.is_delete = 0
			GROUP BY rp.id
		) agg
		JOIN laboratory_package rp ON rp.id = agg.id
		LEFT JOIN costing_services_class csc ON rp.costing_id = csc.costing_id
		";
		
		$res = $this->db->query($query)->result();
		
		return $res;
	}

	public function get_most_used_category() {
		$query = "SELECT COUNT(*) AS total, rp.category
		FROM laboratory_package rp
		LEFT JOIN laboratory_request_item rri
		ON rri.laboratory_package_id = rp.id
		WHERE rp.is_delete = 0
		GROUP BY rp.category
		ORDER BY total DESC
		";
		
		$res = $this->db->query($query)->result();
		
		return $res;
	}
}?>