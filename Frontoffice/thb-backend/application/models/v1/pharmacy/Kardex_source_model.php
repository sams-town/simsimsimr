<?php class Kardex_source_model extends MY_Model {
	protected $tableName = 'kardex_source';
	protected $resultMode = 'object';
	public $primaryKey = 'id';
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields;
	protected $selectFields = "a.*,";
	protected $joinFields = array();

    public function createSource($kardex_id, $source_id, $source_type_id) {
        $dt_kardex_source = array(
            'kardex_id' => $kardex_id,
            'source_id' => $source_id,
            'source_type_id' => $source_type_id,
            'date_added' => date('Y-m-d H:i:s'),
        );

        return $this->create($dt_kardex_source);
    }

    public function getSource($kardex_id, $source_id, $source_type_id) {
        $select = 'a.*';
        $where = array(
            'kardex_id' => $kardex_id,
            'source_id' => $source_id,
            'source_type_id' => $source_type_id,
            'is_delete' => 0,
        );
        return $this->select(0, $select, $where);
    }

}?>
