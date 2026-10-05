<?php class Patient_allergy_model extends MY_Model {
	protected $tableName = 'patient_allergy';
	protected $resultMode = 'object';	  
	public $primaryKey = 'norm';	
	public $whereFields;
    public $limit = 25;
	public $offset;
	protected $orderFields = array('a.norm' => 'ASC');
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>