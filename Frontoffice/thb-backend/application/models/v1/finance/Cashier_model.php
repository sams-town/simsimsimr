<?php class Cashier_model extends MY_Model {
	protected $tableName = 'cashier';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	protected $selectFields = "a.*,";
	protected $joinFields = array();


	public function daily_all_cashier($date_start, $date_end) {
		$query = "SELECT pm.name AS cara_bayar, p.date_added AS tanggal, r.norm, pn.name AS nama_pasien, e.fullname AS keterangan, ti.var_3 AS billing, pi.var_3 AS nominal_bayar
			FROM payment p
			INNER JOIN payment_item pi
			ON p.id = pi.payment_id
			INNER JOIN payment_method pm
			ON pi.payment_method_id = pm.id
			INNER JOIN transaction_invoice ti
			ON p.invoice_id = ti.id
			INNER JOIN registration r
			ON ti.registration_id = r.id
			INNER JOIN registration_destination rd
			ON rd.registration_id = r.id
			INNER JOIN employee e
			ON e.id = rd.doctor_in_charge_id
			INNER JOIN patient pn
			ON pn.norm = r.norm
			WHERE ti.is_delete = 0 AND p.date_added >= '$date_start' AND p.date_added <= '$date_end'
			ORDER BY cara_bayar, tanggal
		";
		
		$res = $this->db->query($query)->result();
		
		return $res;
	}

	public function daily_cashier($date_start, $date_end, $cashier_id) {
		$query = "SELECT ti.code AS nomor_nota, pm.name AS cara_bayar, p.date_added AS tanggal, r.norm, pn.name AS nama_pasien, h.name AS jenis_bayar, e.fullname AS keterangan, ti.var_3 AS billing, pi.var_3 AS nominal_bayar, dp.var_1 AS deposit_masuk
			FROM payment p
			INNER JOIN payment_item pi
			ON p.id = pi.payment_id
			INNER JOIN payment_method pm
			ON pi.payment_method_id = pm.id
			INNER JOIN transaction_invoice ti
			ON p.invoice_id = ti.id
			INNER JOIN registration r
			ON ti.registration_id = r.id
			INNER JOIN registration_destination rd
			ON rd.registration_id = r.id
			INNER JOIN registration_insurance ri
			ON rd.id = ri.registration_destination_id AND ri.is_primary = 1
			INNER JOIN helper h
			ON ri.insurance_id = h.code AND h.variable = 'insurance'
			INNER JOIN employee e
			ON e.id = rd.doctor_in_charge_id
			INNER JOIN patient pn
			ON pn.norm = r.norm
			LEFT JOIN downpayment dp
			ON r.id = dp.registration_id
			WHERE ti.is_delete = 0 AND p.date_added >= '$date_start' AND p.date_added <= '$date_end'
			ORDER BY tanggal
		";
		
		$res = $this->db->query($query)->result();
		
		return $res;
	}

	public function daily_shift($date) {
		$morning_shift_start = $date . ' 07:00:00';
		$morning_shift_end = $date . ' 14:00:00';
		$afternoon_shift_start = $date . ' 14:00:00';
		$afternoon_shift_end = $date . ' 21:00:00';
		$night_shift_start = $date . ' 21:00:00';
		$night_shift_end = $date . ' 00:00:00';
		$midnight_shift_start = $date . ' 00:00:00';
		$midnight_shift_end = $date . ' 07:00:00';

		$morning_total = 0;
		$afternoon_total = 0;
		$night_total = 0;
		$midnight_total = 0;

		$result = array();

		$query = "SELECT pi.var_3 AS nominal_bayar
			FROM payment p
			INNER JOIN payment_item pi
			ON p.id = pi.payment_id
			INNER JOIN payment_method pm
			ON pi.payment_method_id = pm.id
			INNER JOIN transaction_invoice ti
			ON p.invoice_id = ti.id
			INNER JOIN registration r
			ON ti.registration_id = r.id
			INNER JOIN registration_destination rd
			ON rd.registration_id = r.id
			INNER JOIN registration_insurance ri
			ON rd.id = ri.registration_destination_id AND ri.is_primary = 1
			INNER JOIN helper h
			ON ri.insurance_id = h.code AND h.variable = 'insurance'
			INNER JOIN employee e
			ON e.id = rd.doctor_in_charge_id
			INNER JOIN patient pn
			ON pn.norm = r.norm
			LEFT JOIN downpayment dp
			ON r.id = dp.registration_id
			WHERE ti.is_delete = 0 AND pm.name = 'CASH/TUNAI' AND p.date_added >= '$morning_shift_start' AND p.date_added <= '$morning_shift_end'
		";
		
		$res_morning = $this->db->query($query)->result_array();
		
		for ($i=0; $i < count($res_morning); $i++) { 
			$morning_total += (float) $res_morning[$i]['nominal_bayar'];
		}

		array_push($result, $morning_total);

		$query = "SELECT pi.var_3 AS nominal_bayar
			FROM payment p
			INNER JOIN payment_item pi
			ON p.id = pi.payment_id
			INNER JOIN payment_method pm
			ON pi.payment_method_id = pm.id
			INNER JOIN transaction_invoice ti
			ON p.invoice_id = ti.id
			INNER JOIN registration r
			ON ti.registration_id = r.id
			INNER JOIN registration_destination rd
			ON rd.registration_id = r.id
			INNER JOIN registration_insurance ri
			ON rd.id = ri.registration_destination_id AND ri.is_primary = 1
			INNER JOIN helper h
			ON ri.insurance_id = h.code AND h.variable = 'insurance'
			INNER JOIN employee e
			ON e.id = rd.doctor_in_charge_id
			INNER JOIN patient pn
			ON pn.norm = r.norm
			LEFT JOIN downpayment dp
			ON r.id = dp.registration_id
			WHERE ti.is_delete = 0 AND pm.name = 'CASH/TUNAI' AND p.date_added >= '$afternoon_shift_start' AND p.date_added <= '$afternoon_shift_end'
		";
		
		$res_afternoon = $this->db->query($query)->result_array();
		
		for ($i=0; $i < count($res_afternoon); $i++) { 
			$afternoon_total += (float) $res_afternoon[$i]['nominal_bayar'];
		}
		
		array_push($result, $afternoon_total);

		$query = "SELECT pi.var_3 AS nominal_bayar
			FROM payment p
			INNER JOIN payment_item pi
			ON p.id = pi.payment_id
			INNER JOIN payment_method pm
			ON pi.payment_method_id = pm.id
			INNER JOIN transaction_invoice ti
			ON p.invoice_id = ti.id
			INNER JOIN registration r
			ON ti.registration_id = r.id
			INNER JOIN registration_destination rd
			ON rd.registration_id = r.id
			INNER JOIN registration_insurance ri
			ON rd.id = ri.registration_destination_id AND ri.is_primary = 1
			INNER JOIN helper h
			ON ri.insurance_id = h.code AND h.variable = 'insurance'
			INNER JOIN employee e
			ON e.id = rd.doctor_in_charge_id
			INNER JOIN patient pn
			ON pn.norm = r.norm
			LEFT JOIN downpayment dp
			ON r.id = dp.registration_id
			WHERE ti.is_delete = 0 AND pm.name = 'CASH/TUNAI' AND p.date_added >= '$night_shift_start' AND p.date_added <= '$night_shift_end'
		";
		
		$res_night = $this->db->query($query)->result_array();
		
		for ($i=0; $i < count($res_night); $i++) { 
			$night_total += (float) $res_night[$i]['nominal_bayar'];
		}
		
		array_push($result, $night_total);

		$query = "SELECT pi.var_3 AS nominal_bayar
			FROM payment p
			INNER JOIN payment_item pi
			ON p.id = pi.payment_id
			INNER JOIN payment_method pm
			ON pi.payment_method_id = pm.id
			INNER JOIN transaction_invoice ti
			ON p.invoice_id = ti.id
			INNER JOIN registration r
			ON ti.registration_id = r.id
			INNER JOIN registration_destination rd
			ON rd.registration_id = r.id
			INNER JOIN registration_insurance ri
			ON rd.id = ri.registration_destination_id AND ri.is_primary = 1
			INNER JOIN helper h
			ON ri.insurance_id = h.code AND h.variable = 'insurance'
			INNER JOIN employee e
			ON e.id = rd.doctor_in_charge_id
			INNER JOIN patient pn
			ON pn.norm = r.norm
			LEFT JOIN downpayment dp
			ON r.id = dp.registration_id
			WHERE ti.is_delete = 0 AND pm.name = 'CASH/TUNAI' AND p.date_added >= '$midnight_shift_start' AND p.date_added <= '$midnight_shift_end'
		";
		
		$res_midnight = $this->db->query($query)->result_array();
		
		for ($i=0; $i < count($res_midnight); $i++) { 
			$midnight_total += (float) $res_midnight[$i]['nominal_bayar'];
		}
		
		array_push($result, $midnight_total);
		
		return $result;
	}

}?>