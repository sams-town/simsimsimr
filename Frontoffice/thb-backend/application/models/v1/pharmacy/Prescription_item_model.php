<?php class Prescription_item_model extends MY_Model {
	protected $tableName = 'pharmacy_prescription_item';
	protected $resultMode = 'object';	  
	public $primaryKey = 'pharmacy_prescription_id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.sort'=>'ASC');
	protected $selectFields = "a.*,
		e.fullname as doctor_in_charge_name,pc.price,pc.bride_code,
		pc.name as pharmacy_catalog_name,
		sub.name as pharmacy_catalog_name_sub,
		ref.name as pharmacy_catalog_name_ref,
		u.name as dose_unit_name,
		st.name as signatura_times_name,
		pci.name as administer_name,
		p.name as package_name
	";
	protected $joinFields = array(
		array('employee e', 'a.doctor_in_charge_id = e.id', 'LEFT'),
		array('pharmacy_catalog pc', 'a.pharmacy_catalog_id = pc.id', 'LEFT'),
		array('pharmacy_catalog sub', 'a.pharmacy_catalog_id_sub = sub.id', 'LEFT'),
		array('pharmacy_catalog ref', 'a.pharmacy_catalog_id_ref = ref.id', 'LEFT'),
		array('package p', 'a.package_id = p.id', 'LEFT'),
		array('unit u', 'a.dose_unit_id = u.id', 'LEFT'),
		array('signatura_times st', 'a.signatura_times_id = st.id', 'LEFT'),
		array('pharmacy_catalog_instruction pci', 'a.administer_id = pci.id', 'LEFT'),
	);
}?>
