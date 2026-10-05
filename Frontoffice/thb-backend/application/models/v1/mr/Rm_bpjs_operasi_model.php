<?php class Rm_bpjs_operasi_model extends MY_Model {
	protected $tableName = 'rm_bpjs_operasi';
	protected $resultMode = 'object';	  
	public $primaryKey = 'kodebooking';	
	public $whereFields;
    public $limit = 25;
	public $offset;
	public $orderFields = array('a.kodebooking' => 'ASC');
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>