<?php class Mutation_model extends MY_Model {
	protected $tableName = 'pharmacy_mutation';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.date_added' => 'DESC');
	protected $selectFields = "a.*, b.name as from_depo_name, h.name as type_doc_name, hs.name as flag_veriflag_verification_name, c.name as dst_depo_name,
	e_1.fullname as user_validation_1,
	e_2.fullname as user_validation_2,
	e_3.fullname as user_validation_3
	";
	protected $joinFields = array(
		array('user u_1','a.validation_key_1 = u_1.id','LEFT'),array('employee e_1','u_1.employee_id = e_1.id','LEFT'),
		array('user u_2','a.validation_key_2 = u_2.id','LEFT'),array('employee e_2','u_2.employee_id = e_2.id','LEFT'),
		array('user u_3','a.validation_key_3 = u_3.id','LEFT'),array('employee e_3','u_3.employee_id = e_3.id','LEFT'),
		array('helper h','a.type_doc_id = h.code AND h.variable="type_doc_mutation"','LEFT'),
		array('helper hs','a.flag_verification = hs.code AND hs.variable="flag_verif_mutation"','LEFT'),
		array('pharmacy_depo b', 'a.pharmacy_depo_send_id = b.id','LEFT' ),
		array('pharmacy_depo c', 'a.pharmacy_depo_recive_id = c.id','LEFT' )
	);

	public function filter_data($filter,$ssr = false) {
		$this->selectFields = "a.*,a.*, b.name as from_depo_name, h.name as type_doc_name, hs.name as flag_veriflag_verification_name, c.name as dst_depo_name,
			e_1.fullname as user_validation_1,
			e_2.fullname as user_validation_2,
			e_3.fullname as user_validation_3";
		$this->joinFields = array(
			array('user u_1','a.validation_key_1 = u_1.id','LEFT'),array('employee e_1','u_1.employee_id = e_1.id','LEFT'),
			array('user u_2','a.validation_key_2 = u_2.id','LEFT'),array('employee e_2','u_2.employee_id = e_2.id','LEFT'),
			array('user u_3','a.validation_key_3 = u_3.id','LEFT'),array('employee e_3','u_3.employee_id = e_3.id','LEFT'),
			array('helper h','a.type_doc_id = h.code AND h.variable="type_doc_mutation"','LEFT'),
			array('helper hs','a.flag_verification = hs.code AND hs.variable="flag_verif_mutation"','LEFT'),
			array('pharmacy_depo b', 'a.pharmacy_depo_send_id = b.id','LEFT' ),
			array('pharmacy_depo c', 'a.pharmacy_depo_recive_id = c.id','LEFT' )
		);
		if(isset($filter['is_delete'])) { $this->db->where('a.is_delete', $filter['is_delete']); unset($filter['is_delete']); }
		unset($filter['date_start']);
		unset($filter['date_end']);

		$this->db->where($filter);
		if($ssr) {
			return $this->read_ssr();
		} else {
			return $this->read();
		}
	}
}
?>
