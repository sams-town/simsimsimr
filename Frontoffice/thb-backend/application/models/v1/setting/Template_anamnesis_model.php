<?php class Template_anamnesis_model extends MY_Model {
	protected $tableName = 'template_anamnesis';
	protected $resultMode = 'object';
	public $primaryKey = 'id';
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	public $selectFields = "a.*,";
	protected $joinFields = array();
}?>