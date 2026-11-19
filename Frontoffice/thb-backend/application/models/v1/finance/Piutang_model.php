<?php class Piutang_model extends MY_Model {
	protected $tableName = 'piutang';
	protected $resultMode = 'object';
	public $primaryKey = 'id';
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.tgl_piutang' => 'DESC');
	protected $selectFields = "a.*";
	protected $joinFields = array();
}?>
