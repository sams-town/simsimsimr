<?php class Catalog_model extends MY_Model {
	protected $tableName = 'pharmacy_catalog';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.date_added' => 'DESC');
	protected $selectFields = "a.*,g.name as generik_name,ci.name as category_name,cin.name as category_insurance_name,c.name as package_name,d.name as package_big_name, ds.name as dose_name, e.name as suppliers_name,f.name as factory_name,cc.name as category_catalog_name, pcs.signatura, pcs.morning, pcs.afternoon, pcs.evening, pcs.measure, pcs.how_to_use, pci.name as pharmacy_catalog_instruction_name, u.name as dose_unit_name, u2.name as packaging_unit_name";
	protected $joinFields = array(
		array('helper g', 'a.is_generik = g.code AND g.variable="type_generik_p"', 'LEFT'),
		array('helper ci', 'a.category_id = ci.code AND ci.variable="type_category_p"', 'LEFT'),
		array('helper cin', 'a.category_insurance_id = cin.code AND cin.variable="type_cat_insur_p"', 'LEFT'),
		array('category_catalog cc', 'a.category_catalog_id = cc.id', 'LEFT'),
		array('package c', 'a.package_id = c.id', 'LEFT'),
		array('package ds', 'a.dose_id = ds.id', 'LEFT'),
		array('package d', 'a.package_big_id = d.id', 'LEFT'),
		array('company e', 'a.suppliers_id = e.id', 'LEFT'),
		array('company f', 'a.factory_id = f.id', 'LEFT'),
		array('pharmacy_catalog_signatura pcs', 'a.id = pcs.pharmacy_catalog_id', 'LEFT'),
		array('pharmacy_catalog_instruction pci', 'a.pharmacy_catalog_instruction_id = pci.id', 'LEFT'),
		array('unit u', 'a.dose_unit_id = u.id', 'LEFT'),
		array('unit u2', 'a.packaging_unit_id = u2.id', 'LEFT'),
	);
}?>
