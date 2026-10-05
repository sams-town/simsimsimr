<?php class Insurance_model extends MY_Model {
	protected $tableName = 'insurance';
	protected $resultMode = 'object';	  
	public $primaryKey = 'norm';	
	public $whereFields;
    public $limit = 25;
	public $offset;
	protected $orderFields = array('a.norm' => 'ASC');
	protected $selectFields = "a.*,b.name as company_name, c.name as insurance_name, d.name as service_class_name";
	protected $joinFields = array(
		array('company b', 'a.company_id = b.id', 'LEFT'),
		array('helper c', 'a.insurance_id = c.code AND c.variable="insurance"', 'LEFT'),
		array('service_class d', 'a.service_class_id = d.id', 'LEFT'),
	);
}?>