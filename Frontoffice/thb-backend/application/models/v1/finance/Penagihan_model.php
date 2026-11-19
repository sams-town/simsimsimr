<?php class Penagihan_model extends MY_Model {
	protected $tableName = 'penagihan_piutang';
	protected $resultMode = 'object';
	public $primaryKey = 'no_tagihan';
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.tanggal_tagih' => 'DESC');
	protected $selectFields = "a.*";
	protected $joinFields = array();
}?>
