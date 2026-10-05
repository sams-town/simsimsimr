<?php class Opname_item_model extends MY_Model {
	protected $tableName = 'pharmacy_opname_item';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
    // public $limit = 25;
	public $offset;
	public $orderFields = array('pr.name' => 'ASC','a.catalog_name' => 'ASC');
	protected $selectFields = "a.id,a.pharmacy_catalog_id,a.catalog_name,a.expired_date,a.package_name,a.package_big_name,a.accumulation,a.hna,a.hp,a.sum_adm,a.sum_physical,a.user,a.user_bak,a.timestampt,a.batch,a.rack_id,a.rackname_id,
	pr.name as name_rack, prn.name as name_rackname,po.pharmacy_depo_id
	";
	// ci.name as category_name,po.pharmacy_depo_id,pd.name as pharmacy_depo_name
	protected $joinFields = array(
		// array('pharmacy_catalog pc', 'a.pharmacy_catalog_id = pc.id', 'LEFT'),
		array('pharmacy_opname po', 'a.opname_id = po.id', 'LEFT'),
		// array('pharmacy_depo pd','po.pharmacy_depo_id = pd.id','LEFT'),
		// array('pharmacy_opname_activation poa', 'po.pharmacy_activation_id = poa.id', 'LEFT'),
		// array('helper ci', 'pc.category_id = ci.code AND ci.variable="type_category_p"', 'LEFT'),
		array('pharmacy_rack pr', 'a.rack_id = pr.id', 'LEFT'),
		array('pharmacy_rackname prn', 'a.rackname_id = prn.id', 'LEFT'),
	);
}?>
 