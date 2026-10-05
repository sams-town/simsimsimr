<?php class Opname_item_report_model extends MY_Model {
	protected $tableName = 'pharmacy_opname_item';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
    // public $limit = 25;
	public $offset;
	public $orderFields = array('a.sort' => 'ASC');
	protected $selectFields = "a.*,p.hna_item,pd.name as pharmacy_depo_name";
	protected $joinFields = array(
		array('pharmacy_opname po', 'a.opname_id = po.id', 'LEFT'),
		array('pharmacy_depo pd', 'po.pharmacy_depo_id = pd.id', 'LEFT'),
		array('pharmacy_assets_price p','a.pharmacy_catalog_id = p.pharmacy_catalog_id','LEFT'),
	);
}?>
