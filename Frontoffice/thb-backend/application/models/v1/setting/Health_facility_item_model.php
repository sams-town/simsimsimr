<?php class Health_facility_item_model extends MY_Model {
	protected $tableName = 'health_facility_item';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.name' => 'ASC');
	protected $selectFields = "a.*,hf.name as health_facility_name";
	protected $joinFields = array(
		array('health_facility hf', 'a.health_facility_id = hf.id', 'LEFT'),
	);

	public function detail($institute_branch_id, $id) {

		
		if($id == '0') {
			$where = '';
		} else {
			$where = "AND health_facilitys.id = '$id'";
		}
		var_dump($where);
		$detail = $this->db->query("
			SELECT health_facility_item.* 
			FROM health_facility_item
			LEFT JOIN health_facility
			ON health_facility_item.health_facility_id = health_facility.id
			WHERE institute_branch_id = '$institute_branch_id' " . $where
		)->result();
			
		return $this->convert_data($detail);
	}
}?>
