<?php class Bed_model extends MY_Model {
	protected $tableName = 'bed';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	protected $selectFields = "a.*,b.name as type_status_bed_name,";
	protected $joinFields = array(
        array('helper b', 'a.type_status_bed_id = b.code AND b.variable="type_status_bed"', 'LEFT'),
    );
}?>