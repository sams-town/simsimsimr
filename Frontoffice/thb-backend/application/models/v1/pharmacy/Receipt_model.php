<?php class Receipt_model extends MY_Model {
	protected $tableName = 'receipts';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.date_added' => 'DESC');
	protected $selectFields = "a.*, b.name as pharmacy_depo_name, c.name as payment_method_name, c.days, ee.fullname as receipt_name, d.bride_code, h.name as payment_name, d.name as supplier_name, e.username as user_name, f.no_document as no_document_purchase, f.ndiscountotal, g.no_document as no_document_planning";
	protected $joinFields = array(
		array('pharmacy_depo b','a.pharmacy_depo_id = b.id','LEFT'),
		array('payment_method c','a.payment_method_id = c.id','LEFT'),
		array('company d','a.supplier_id = d.id','LEFT'),
		array('user e','a.user_added = e.id','LEFT'),
		array('employee ee','e.employee_id = ee.id','LEFT'),
		array('purchase_order f','a.purchase_order_id = f.id','LEFT'),
		array('helper h','f.price_type = h.code AND h.variable="type_price"','LEFT'),
		array('pharmacy_planning g','a.pharmacy_planning_id = g.id','LEFT'),
	);

	public function filter_data($filter,$ssr = false,$is_catalog) {
		$this->selectFields = "a.*, b.name as pharmacy_depo_name, c.name as payment_method_name, c.days, ee.fullname as receipt_name, d.bride_code, h.name as payment_name, d.name as supplier_name, e.username as user_name, f.no_document as no_document_purchase, f.ndiscountotal, g.no_document as no_document_planning";
		if ($is_catalog){
			$this->joinFields = array(
				array('receipt_items ri','a.id = ri.receipt_id','LEFT'),
				array('pharmacy_depo b','a.pharmacy_depo_id = b.id','LEFT'),
				array('payment_method c','a.payment_method_id = c.id','LEFT'),
				array('company d','a.supplier_id = d.id','LEFT'),
				array('user e','a.user_added = e.id','LEFT'),
				array('employee ee','e.employee_id = ee.id','LEFT'),
				array('purchase_order f','a.purchase_order_id = f.id','LEFT'),
				array('helper h','f.price_type = h.code AND h.variable="type_price"','LEFT'),
				array('pharmacy_planning g','a.pharmacy_planning_id = g.id','LEFT'),
			);
			$this->groupFields = array('a.id');
		}else{
			$this->joinFields = array(
				array('pharmacy_depo b','a.pharmacy_depo_id = b.id','LEFT'),
				array('payment_method c','a.payment_method_id = c.id','LEFT'),
				array('company d','a.supplier_id = d.id','LEFT'),
				array('user e','a.user_added = e.id','LEFT'),
				array('employee ee','e.employee_id = ee.id','LEFT'),
				array('purchase_order f','a.purchase_order_id = f.id','LEFT'),
				array('helper h','f.price_type = h.code AND h.variable="type_price"','LEFT'),
				array('pharmacy_planning g','a.pharmacy_planning_id = g.id','LEFT'),
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
