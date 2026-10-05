<?php class Patient_tasks_model extends MY_Model {
	protected $tableName = 'patient_tasks';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields;
	protected $selectFields = "a.*,";
	protected $joinFields = array();

	public function new_task($registration_destination_id, $date, $user_id) {
		$dt_patient_tasks = array(
			'registration_destination_id' => $registration_destination_id,
			'date' => $date,
			'user_added' => $user_id,
			'date_added' => date('Y-m-d H:i:s'),
			'date_updated' => date('Y-m-d H:i:s'),
		);

		$patient_tasks_id = $this->create($dt_patient_tasks);

		$select = 'a.id';
		$where = array(
			'a.registration_destination_id' => $registration_destination_id,
			'a.is_active' => 1,
			'a.is_delete' => 0,
		);
		$kardex = $this->Kardex->select(0, $select, $where);

		if (count($kardex) > 0) {
			$kardex_id = $kardex[0]->id;

			$select = 'a.id, a.pharmacy_catalog_id, a.signatura_times_id, a.status';
			$where = array(
				'a.kardex_id' => $kardex_id,
				'a.is_delete' => 0,
			);
			$kardex_item = $this->Kardex_item->select(0, $select, $where);

			for ($j=0; $j < count($kardex_item); $j++) { 
				$pharmacy_catalog_id = $kardex_item[$j]->pharmacy_catalog_id;
				$signatura_times_id = $kardex_item[$j]->signatura_times_id;
				$status = $kardex_item[$j]->status;

				if ($status == 4) {
					continue;
				}

				if ($signatura_times_id == '95727030-63ba-4e4d-9b82-2dfb064eaa74') { // 4 JAM
					$times = 6;
				} elseif ($signatura_times_id == '8f20d2ac-df6f-42e5-a804-009343c0d6f8') { // 6 JAM
					$times = 4;
				} elseif ($signatura_times_id == '26e33f75-a6bc-4624-b75a-64f545485c56') { // 8 JAM
					$times = 3;
				} elseif ($signatura_times_id == '20446fea-c42d-432e-9a1a-15ffad0c9dd4') { // 12 JAM
					$times = 2;
				} elseif ($signatura_times_id == 'a061feed-36d3-458e-9c5f-a1ac63e92075') { // 18 JAM
					$times = 2;
				} elseif ($signatura_times_id == 'b69d3707-ec6e-4f58-8aa2-e874d97c97e9') { // 24 JAM
					$times = 1;
				} elseif ($signatura_times_id == '7783211c-4d01-4a1a-921c-252b504e6a38') { // 36 JAM
					$times = 1;
				} else { // TODO - TIAP 1 JAM - TIAP 1 MENIT
					$times = 1;
				}

				for ($order=1; $order < $times+1; $order++) { 
					$dt_patient_tasks_item = array(
						'patient_tasks_id' => $patient_tasks_id,
						'pharmacy_catalog_id' => $pharmacy_catalog_id,
						'type' => 1,
						// 'time' => '06:00:00',
						'order' => $order,
						'user_added' => $user_id,
						'date_added' => date('Y-m-d H:i:s'),
						'date_updated' => date('Y-m-d H:i:s'),
					);

					$this->Patient_tasks_item->create($dt_patient_tasks_item);
				}
			}
			
			$select = 'a.id, a.non_pharmacological_id';
			$where = array(
				'a.kardex_id' => $kardex_id,
				'a.is_delete' => 0,
			);
			$kardex_non_pharmacological = $this->Kardex_non_pharmacological->select(0, $select, $where);

			for ($j=0; $j < count($kardex_non_pharmacological); $j++) { 
				$non_pharmacological_id = $kardex_non_pharmacological[$j]->non_pharmacological_id;
				
				$dt_patient_tasks_item = array(
					'patient_tasks_id' => $patient_tasks_id,
					'non_pharmacological_id' => $non_pharmacological_id,
					'type' => 2,
					'user_added' => $user_id,
					'date_added' => date('Y-m-d H:i:s'),
					'date_updated' => date('Y-m-d H:i:s'),
				);

				$this->Patient_tasks_item->create($dt_patient_tasks_item);
			}
		}

		return $patient_tasks_id;
	}

}?>