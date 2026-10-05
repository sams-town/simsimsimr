<?php class Pews_model extends MY_Model {
	protected $tableName = 'pews';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.observation_date' => 'ASC');
	protected $selectFields = "a.*,";
	protected $joinFields = array();

	public function source_create($registration_destination_id, $observation_date, $data) {
		$birth_date = $data['birth_date'] ?? null;
		$source_id = $data['source_id'] ?? null;
		$employee_nurse_id = $data['employee_nurse_id'] ?? null;
		$user_id = $data['user_id'] ?? null;
		$observation_time = $data['observation_time'] ?? null;
        $breathing = ($data['breathing'] == null) ? 0: $data['breathing'];
        $oxygen = ($data['oxygen'] == 0 || $data['oxygen'] == null) ? 2: $data['oxygen'];
        $oxygen_saturation = ($data['oxygen_saturation'] == null) ? 0: $data['oxygen_saturation'];
        $pulse = ($data['pulse'] == null) ? 0: $data['pulse'];
        $skin_color = ($data['skin_color'] == null) ? 0: $data['skin_color'];
        $systolic = ($data['systolic'] == null) ? 0: $data['systolic'];
        $behavior = ($data['behavior'] == null) ? 0: $data['behavior'];

		// if ($observation_time == null || $breathing == null || $oxygen == null || $oxygen_saturation == null || $pulse == null || $skin_color == null || $systolic == null || $behavior == null) {
		// 	return null;
		// }

		$select = 'a.id';
		$where = array(
			'registration_destination_id' => $registration_destination_id, 
			'observation_date' => $observation_date, 
			'is_delete' => 0
		);
		$pews = $this->select(0, $select, $where);
		if (count($pews) == 0) {
			$dt_pews = array(
				'registration_destination_id'   => $registration_destination_id,
				'observation_date'              => $observation_date,
				'user_added'                    => $user_id,
				'date_added'                    => date('Y-m-d H:i:s',time()),
				'date_updated'                  => date('Y-m-d H:i:s',time()),
			);

			$pews_id = $this->create($dt_pews);
		} else {
			$pews_id = $pews[0]->id;
		}

		$pews_score = $this->Pews_item->count_pews($birth_date,$breathing,$oxygen,$oxygen_saturation,$pulse,$skin_color,$systolic,$behavior);
	
		$dt_pews_item = array(
			'pews_id' 				 => $pews_id,
			'employee_nurse_id'      => $employee_nurse_id,
			'source_id'              => $source_id,
			'observation_time'       => $observation_time,
			'breathing'				 => $breathing,
			'oxygen'				 => $oxygen,
			'oxygen_saturation'		 => $oxygen_saturation,
			'pulse'					 => $pulse,
			'skin_color'			 => $skin_color,
			'systolic'				 => $systolic,
			'behavior'				 => $behavior,
			'total'                  => $pews_score,
			'user_added'             => $user_id,
			'date_added'             => date('Y-m-d H:i:s',time()),
			'date_updated'           => date('Y-m-d H:i:s',time()),
		);

		$select = 'a.id';
		$where = array(
			'a.pews_id' => $pews_id,
			'a.source_id' => $source_id,
			'a.is_delete' => 0,
		);
		$ewsi = $this->Pews_item->select(0, $select, $where);
		if (count($ewsi) == 0) {
			$this->Pews_item->create($dt_pews_item);
		} else {
			$ewsi_id = $ewsi[0]->id;
			$dt_pews_item['user_updated'] = $user_id;
			unset($dt_pews_item['user_added']);
			unset($dt_pews_item['date_added']);
			$this->Pews_item->update_v2($ewsi_id, $dt_pews_item);
		}
	}
}?>