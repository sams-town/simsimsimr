<?php class Origin_model extends MY_Model {
	protected $tableName = 'origin';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields;
	protected $selectFields = "a.*,";
	protected $joinFields = array();

    public function check_origin($source_type, $source_id, $health_facility_item_id, $user_added, $date_added) {
        $select = 'a.id';
        $where = array(
            'a.source_id' => $source_id,
        );
        $origin = $this->select(0, $select, $where);
        if (count($origin) == 0) {
            $data = [
                'source_type' => $source_type,
                'source_id' => $source_id,
                'health_facility_item_id' => $health_facility_item_id,
                'user_added' => $user_added,
                'date_added' => $date_added,
            ];
        }
    }

    public function insert_origin($source_type, $source_id, $health_facility_item_id, $user_added) {
        $data = [
            'source_type' => $source_type,
            'source_id' => $source_id,
            'health_facility_item_id' => $health_facility_item_id,
            'user_added' => $user_added,
            'date_added' => date('Y-m-d H:i:s'),
        ];

        return $this->create($data);
    }

}?>