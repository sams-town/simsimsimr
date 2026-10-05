<?php class Queue_model extends MY_Model {
	protected $tableName = 'registration';
	// protected $resultMode = 'object';	  
	// public $primaryKey = 'norm';	
	// public $whereFields;
    // public $limit;
	// public $orderFields = array('a.norm' => 'DESC');
	// protected $selectFields = "a.*,";
	// protected $joinFields = array();

    public function display_queue($health_facility_item_id, $doctor_in_charge_id, $doctor_schedule_item_id) {
		$queue = $this->db->query("
			SELECT *
			FROM counter_queue
			WHERE var_1 = '$health_facility_item_id' AND var_2 = '$doctor_in_charge_id' AND var_3 = '$doctor_schedule_item_id'
		")->result();

		if($queue != []) {
			return $this->convert_data($queue);
		} else {
			return false;
		}
	}

	public function get_queue_number($health_facility_item_id, $doctor_in_charge_id, $appointment = '0', $emergency = false) {
		if($emergency) { return; } // EMERGENCY
		$result = array(
			'queue' 					=> 0,
			'doctor_schedule_item_id' 	=> 0,
		);

		// CAN OR NOT?
		$doctor_schedule = $this->db->query("
			SELECT a.code as queue_code, b.* 
			FROM doctor_schedule AS a
			LEFT JOIN doctor_schedule_item AS b
			ON a.id = b.doctor_schedule_id
			WHERE a.doctor_id = '$doctor_in_charge_id' AND a.health_facility_item_id = '$health_facility_item_id' AND b.is_delete = 0
			ORDER BY b.hour_end ASC
		")->result();

		$thereis = false;
		$can = false;
		$full = false;
		if($doctor_schedule != []) {
			for ($i=0; $i < count($doctor_schedule); $i++) { 
				// Check if there is schedule on this day
				if($doctor_schedule[$i]->day == date('N')) {
					$thereis = true;
					$quota_bo = 0;

					// Check if the schedule has not passed
					if(date('Hi',strtotime($doctor_schedule[$i]->hour_end)) >= date('Hi')) {
						$can = true;

						$doctor_schedule_item_id = $doctor_schedule[$i]->id;
						$result['queue_code'] = $doctor_schedule[$i]->queue_code;
						$result['doctor_schedule_item_id'] = $doctor_schedule_item_id;

						// Check if the quota is full
						$full = false;
						$tmp = $this->db->query("
							SELECT *
							FROM counter_queue
							WHERE var_1 = '$health_facility_item_id' AND var_2 = '$doctor_in_charge_id' AND var_3 = '$doctor_schedule_item_id'
						")->result_array();
						// echo $doctor_schedule[$i]->day." ";var_dump($tmp);
						if($tmp != []) {
							// Check if reset is needed
							if($tmp[0]['var_5'] != date('Y-m-d')) {
								$data = array(
									'var_1' 		=> $health_facility_item_id,
									'var_2' 		=> $doctor_in_charge_id,
									'var_3'			=> $doctor_schedule_item_id,
									'var_5' 		=> date('Y-m-d'),
								);
								$data['var_4'] = 1;
								$data['counter_queue'] = $tmp[0]['counter_queue'];
								$quota_bo = $tmp[0]['counter_queue'];
								$this->db->replace('counter_queue',$data);
								break;
							}

							if($tmp[0]['counter_queue'] == $doctor_schedule[$i]->quota && $appointment == '0') {
								$full = true;
								continue;
							}
						}
						break;
					}
				}
			}
		} else {
			$result['error0'] = 'Doctor schedule not found!'; 
			return $result;
		}
		// if error, return
		if(!$thereis) { $result['error1'] = 'No schedule today!'; return $result; }
		if(!$can) { $result['error2'] = 'You missed the schedule!'; return $result; }
		if($full) { $result['error3'] = 'The quota is full for today!'; return $result; }
		
		$doctor_schedule_item_id = $result['doctor_schedule_item_id'];
		// Counter Queue
		$queue = $this->db->query("
			SELECT *
			FROM counter_queue
			WHERE var_1 = '$health_facility_item_id' AND var_2 = '$doctor_in_charge_id' AND var_3 = '$doctor_schedule_item_id'
		")->result_array();

		$data = array(
			'var_1' 		=> $health_facility_item_id,
			'var_2' 		=> $doctor_in_charge_id,
			'var_3'			=> $doctor_schedule_item_id,
			'var_4'			=> 1,
			'var_5' 		=> date('Y-m-d'),
			'counter_queue' => $quota_bo + 1,
		);

		// var_dump($queue);
		// die();
		
		// if appointment queue already set
		if($appointment != '0') {
			$appointment_schedule = $this->db->query("
				SELECT a.* 
				FROM appointment_schedule AS a
				WHERE a.appointment_id = '$appointment'
			")->result();
			$appointment_queue = $appointment_schedule[0]->queue;
			$data['counter_queue'] = $quota_bo;
		} 

		if($queue != []) {
			// default value
			$data['var_4'] = $queue[0]['var_4'];
			$data['counter_queue'] = $queue[0]['counter_queue'];

			// if appointment the counter is not increasing
			if($appointment == '0') {
				$no = $queue[0]['counter_queue'] + 1;
				$data['counter_queue'] = $no;
			}
			
			$this->db->replace('counter_queue',$data);
			$result['queue'] = isset($appointment_queue) ? $appointment_queue : $data['counter_queue'];
			return $result;
		} else {
			$this->db->insert('counter_queue',$data);
			$result['queue'] = isset($appointment_queue) ? $appointment_queue : $data['counter_queue'];
			// var_dump($result['queue']);die();
			return $result;
		}
	}

	public function next_queue($health_facility_item_id, $doctor_in_charge_id, $doctor_schedule_item_id) {
		$queue = $this->db->query("
			SELECT *
			FROM counter_queue
			WHERE var_1 = '$health_facility_item_id' AND var_2 = '$doctor_in_charge_id' AND var_3 = '$doctor_schedule_item_id'
		")->result_array();

		$t = time();
		$date = date('Ymd', $t);

		// var_dump($queue);
		// die();

		if($queue != []) {
			$data = array(
				'counter_queue' => $queue[0]['counter_queue'],
				'var_5' 			=> $date,
			);

			$where = array(
				'var_1' 		=> $health_facility_item_id,
				'var_2' 		=> $doctor_in_charge_id,
				'var_3' 		=> $doctor_schedule_item_id,
			);

			$no = $queue[0]['var_4'] + 1;
			$data['var_4'] = $no;
			$this->db->where($where);
			$this->db->update('counter_queue',$data);			
			return $no;
		} else {
			return 'No queue!';
		}
	}

	public function get_no_letter() {
		$query = "SELECT * FROM counter_queue WHERE var_1 = 'no_letter' ORDER BY counter_queue DESC";
		$queue = $this->db->query($query)->result();
		Logger::debug('query: ' . $query);
		$year = date('Y');
		$date = date('Y-m-d');

		if($queue != []) {
			$counter_queue = $queue[0]->counter_queue;
			Logger::debug('found last counter: ' . $counter_queue);
			$no_letter = $year . 'DOC' . str_pad(intval($counter_queue) + 1, 4, '0', STR_PAD_LEFT);
			Logger::debug('no_letter: ' . $no_letter);

			$data = array(
				'counter_queue' => $counter_queue + 1,
				'var_2' 			=> $date,
			);

			$where = array(
				'var_1'			=> 'no_letter'
			);

			$this->db->where($where);
			$queue = $this->db->update('counter_queue',$data);
			if ($queue == '1') {
				Logger::debug('trans: success update counter_queue');
			} else {
				Logger::error('trans: error update counter_queue: ' . print_r($queue));
			}
			return $no_letter;
		} else {
			Logger::debug('creating new no_letter');
			$no_letter = $year . 'DOC' .'0001';
			Logger::debug('no_letter: ' . $no_letter);
			
			$data = array(
				'var_1'			=> 'no_letter',
				'counter_queue' => 1,
				'var_2' 			=> $date,
			);

			$queue = $this->db->insert('counter_queue',$data);	
			if ($queue == '1') {
				Logger::debug('trans: success insert counter_queue');
			} else {
				Logger::error('trans: error insert counter_queue: ' . print_r($queue));
			}
			return $no_letter;
		}
	}

	function counter_queue($prefix, $data, $length) {
		$this->tableName = 'counter_queue';

		$var_2 = $data['var_2'];
		$var_3 = $data['var_3'];
		$var_4 = $data['var_4'];
		$var_5 = $data['var_5'];
	
		$select = 'a.var_1, a.var_2, a.var_3, a.var_4, a.var_5, a.counter_queue';
		$where = array(
			'var_2' => $var_2,
			'var_3' => $var_3,
			'var_4' => $var_4,
			'var_5' => $var_5,
		);
		$counter = $this->select($prefix, $select, $where);
	
		$queue = 1;
	
		if (count($counter) > 0) {
			$queue = $counter[0]->counter_queue + 1;
		}
		
		$dt_queue = array(
			'var_1' => $prefix,
			'var_2' => $data['var_2'],
			'var_3' => $data['var_3'],
			'var_4' => $data['var_4'],
			'var_5' => $data['var_5'],
			'counter_queue' => $queue,
			'date_update'   => date('Y-m-d H:i:s'),
		);
	
		$this->db->trans_start();
	
		$this->replace($dt_queue);
	
		$this->db->trans_complete();
	
		$queue = str_pad($queue, $length, "0", STR_PAD_LEFT);
	
		$no = $prefix . $var_2 . $var_3 . $var_4 . $queue;
	
		$this->tableName = 'registration';
		return $no;
	}
}
?>