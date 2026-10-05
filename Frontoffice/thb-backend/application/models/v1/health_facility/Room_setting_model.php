<?php class Room_setting_model extends MY_Model {
	protected $tableName = 'room_setting';
	protected $resultMode = 'object';	  
	public $primaryKey = 'room_id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.room_id' => 'ASC');
	protected $selectFields = "a.*,";
	protected $joinFields = array();

	public function reset_default_if_empty($room_id) {
		// Set room setting to default
		$select = 'a.id';
		$where = array(
			'a.room_id' => $room_id,
			'a.is_delete' => 0,
		);
		$where_bed = 'a.type_status_bed_id != 1';
		$this->db->where($where_bed);
		$bed = $this->Bed->select(0, $select, $where);
		if (count($bed) == 0) {
			$select = 'a.is_surgery, a.is_internal, a.is_isolation, a.gender_availability, a.room_pressure, a.costing_id, a.service_class_id';
			$room_setting = $this->Room_setting->select($room_id, $select);

			$is_surgery = $room_setting[0]->is_surgery;
			$is_internal = $room_setting[0]->is_internal;
			$is_isolation = $room_setting[0]->is_isolation;
			$gender_availability = $room_setting[0]->gender_availability;
			$room_pressure = $room_setting[0]->room_pressure;
			$costing_id = $room_setting[0]->costing_id;
			$service_class_id = $room_setting[0]->service_class_id;

			$dt_room = array(
				'is_surgery' => $is_surgery,
				'is_internal' => $is_internal,
				'is_isolation' => $is_isolation,
				'gender_availability' => $gender_availability,
				'room_pressure' => $room_pressure,
				'costing_id' => $costing_id,
				'service_class_id' => $service_class_id,
			);
			$this->Room->update_v2($room_id, $dt_room);
		}
	}
}