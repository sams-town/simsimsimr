<?php class Purchase_order_model extends MY_Model {
	protected $tableName = 'purchase_order';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.date_added' => 'DESC');
	protected $selectFields = "a.*,b.name as supplier_name, b.bride_code, b.address, b.phone, h.name as po_type_name, c.name as payment_method_name,d.name as pharmacy_depo_name, e.id as pharmacy_planning_id, e.no_document as no_document_planning, emp_1.fullname as validation_key_1_name";
	protected $joinFields = array(
		array('company b','a.supplier_id = b.id','LEFT'),
		array('helper h','a.po_type = h.code AND h.variable="type_purchase_order"','LEFT'),
		array('payment_method c','a.payment_method_id = c.id','LEFT'),
		array('pharmacy_depo d','a.pharmacy_depo_id = d.id','LEFT'),
		array('pharmacy_planning e','a.no_planning = e.id','LEFT'),
		array('user u','a.validation_key_1 = e.id','LEFT'),
		array('employee emp_1','u.employee_id = emp_1.id','LEFT'),
	);

	public function filter_data($filter,$ssr = false,$is_catalog) {
		$this->selectFields = "a.*,b.name as supplier_name, b.bride_code, h.name as po_type_name, c.name as payment_method_name,d.name as pharmacy_depo_name, e.id as pharmacy_planning_id, e.no_document as no_document_planning, emp_1.fullname as validation_key_1_name";
		if ($is_catalog){
			$this->joinFields = array(
				array('purchase_order_items poi','a.id = poi.purchase_order_id','LEFT'),
				array('company b','a.supplier_id = b.id','LEFT'),
				array('helper h','a.po_type = h.code AND h.variable="type_purchase_order"','LEFT'),
				array('payment_method c','a.payment_method_id = c.id','LEFT'),
				array('pharmacy_depo d','a.pharmacy_depo_id = d.id','LEFT'),
				array('pharmacy_planning e','a.no_planning = e.id','LEFT'),
				array('user u','a.validation_key_1 = e.id','LEFT'),
				array('employee emp_1','u.employee_id = emp_1.id','LEFT'),
			);
			$this->groupFields = array('a.id');
		}else{
			$this->joinFields = array(
				array('company b','a.supplier_id = b.id','LEFT'),
				array('helper h','a.po_type = h.code AND h.variable="type_purchase_order"','LEFT'),
				array('payment_method c','a.payment_method_id = c.id','LEFT'),
				array('pharmacy_depo d','a.pharmacy_depo_id = d.id','LEFT'),
				array('pharmacy_planning e','a.no_planning = e.id','LEFT'),
				array('user u','a.validation_key_1 = e.id','LEFT'),
				array('employee emp_1','u.employee_id = emp_1.id','LEFT'),
			);
		}
		if(isset($filter['no_document'])) { $this->db->where('a.no_document', $filter['no_document']); unset($filter['no_document']); }
		if(isset($filter['is_delete'])) { $this->db->where('a.is_delete', $filter['is_delete']); unset($filter['is_delete']); }
		unset($filter['date_start']);
		unset($filter['date_end']);

		$this->db->where($filter);
		if($ssr) {
			return $this->read_ssr();
		} else {
			return $this->read();
		}
	}
}
?>