<?php class Soap_examination_model extends MY_Model {
	protected $tableName = 'soap_examination';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.date_added' => 'DESC');
	public $selectFields = "a.*, ce.name as category_examination_name";
	protected $joinFields = array(
		array('category_examination ce', 'a.category_examination_id = ce.id', 'INNER'),
	);
}?>