<?php class Room_model extends MY_Model {
	protected $tableName = 'room';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.name' => 'ASC');
	protected $selectFields = "a.*,b.name as type_room_name,c.name as health_facility_item_name,c.health_facility_id,hc.name as health_facility_name,d.name as costing_name,e.name as service_class_name";
	protected $joinFields = array(
        array('helper b', 'a.type_room_id = b.code AND b.variable="type_room"', 'LEFT'),
        array('health_facility_item c', 'a.health_facility_item_id = c.id', 'LEFT'),
        array('health_facility hc', 'c.health_facility_id = hc.id', 'LEFT'),
        array('costing d', 'a.costing_id = d.id', 'LEFT'),
        array('service_class e', 'a.service_class_id = e.id', 'LEFT'),
    );


	function room_monitoring($type_room_id = 0) {
		// type_room_id filter
		if($type_room_id != 0) {
			$where = "WHERE a.type_room_id = '$type_room_id' AND a.is_delete = 0 AND b.is_delete = 0";
		} else {
			$where = "";
		}

		$query = $this->db->query("
			SELECT a.name, a.id as room_id, a.type_room_id, c.name as type_room_name, b.type_status_bed_id, d.name as type_status_bed_name, COUNT(*) AS total 
			FROM room as a
			LEFT JOIN bed as b
			ON a.id = b.room_id
			LEFT JOIN helper as c
			ON a.type_room_id = c.code AND c.variable = 'type_room'
			LEFT JOIN helper as d
			ON b.type_status_bed_id = d.code AND d.variable = 'type_status_bed'
			$where
			GROUP BY a.name, b.type_status_bed_id
			ORDER BY a.name
		")->result();

		$result = array();

		usort($query, function($a, $b) {
			// Extract the numbers from the strings
			$numA = (int) filter_var($a->name, FILTER_SANITIZE_NUMBER_INT);
			$numB = (int) filter_var($b->name, FILTER_SANITIZE_NUMBER_INT);
			
			return $numB - $numA;
		});

		$skip = 0;
		for ($i=0; $i < count($query); $i++) { 
			if($skip == 0) {
				// default
				$result[$i]['Available'] = 0;
				$result[$i]['Filled'] = 0;
				$result[$i]['Linen'] = 0;
				$result[$i]['room_id'] = $query[$i]->room_id;
				$result[$i]['type_room_id'] = intval($query[$i]->type_room_id);
				$result[$i]['type_room_name'] = $query[$i]->type_room_name;
				$result[$i]['room_name'] = $query[$i]->name;
				
				$result[$i][$query[$i]->type_status_bed_name] = intval($query[$i]->total);
				if(isset($query[$i+1]->name)) {
					if($query[$i]->name == $query[$i+1]->name) {
						$result[$i][$query[$i+1]->type_status_bed_name] = intval($query[$i+1]->total);
						$skip++;
						if(isset($query[$i+2]->name)) {
							if($query[$i]->name == $query[$i+2]->name) {
								$result[$i][$query[$i+2]->type_status_bed_name] = intval($query[$i+2]->total);
								$skip++;
							}
						}
					}
				}
			} else {
				$skip--;
				continue;
			}
		}

		// Sort index
		$result = array_values($result);

		// Add total
		for ($i=0; $i < count($result); $i++) {
			$total = $result[$i]['Available'] + $result[$i]['Filled'] + $result[$i]['Linen'];
			$result[$i]['total'] = $total;
		}

		return $result;
	}

	function type_room_monitoring() {
		$query = $this->db->query("
			SELECT a.name, a.id as room_id, a.type_room_id, c.name as type_room_name, b.type_status_bed_id, d.name as type_status_bed_name, COUNT(*) AS total 
			FROM room as a
			LEFT JOIN bed as b ON a.id = b.room_id
			LEFT JOIN helper as c ON a.type_room_id = c.code AND c.variable = 'type_room'
			LEFT JOIN helper as d ON b.type_status_bed_id = d.code AND d.variable = 'type_status_bed'
			GROUP BY a.name, b.type_status_bed_id
			ORDER BY a.name
		")->result();

		$result = array();
		$tmp = array();

		$skip = 0;
		for ($i=0; $i < count($query); $i++) { 
			if($skip == 0) {
				// default
				$tmp[$i]['Available'] = 0;
				$tmp[$i]['Filled'] = 0;
				$tmp[$i]['Linen'] = 0;
				$tmp[$i]['type_room_id'] = intval($query[$i]->type_room_id);
				$tmp[$i]['type_room_name'] = $query[$i]->type_room_name;
				
				$tmp[$i][$query[$i]->type_status_bed_name] = intval($query[$i]->total);
				if(isset($query[$i+1]->name)) {
					if($query[$i]->name == $query[$i+1]->name) {
						$tmp[$i][$query[$i+1]->type_status_bed_name] = intval($query[$i+1]->total);
						$skip++;
						if(isset($query[$i+2]->name)) {
							if($query[$i]->name == $query[$i+2]->name) {
								$tmp[$i][$query[$i+2]->type_status_bed_name] = intval($query[$i+2]->total);
								$skip++;
							}
						}
					}
				}
			} else {
				$skip--;
				continue;
			}
		}

		// Sort index
		$tmp = array_values($tmp);

		$total_avail_type_room_1 = 0; $total_filled_type_room_1 = 0;
		$total_avail_type_room_2 = 0; $total_filled_type_room_2 = 0;
		$total_avail_type_room_3 = 0; $total_filled_type_room_3 = 0;
		$total_avail_type_room_4 = 0; $total_filled_type_room_4 = 0;
		$total_avail_type_room_5 = 0; $total_filled_type_room_5 = 0;
		for ($i=0; $i < count($tmp); $i++) {
			if($tmp[$i]['type_room_id'] == 1) {
				$total_avail_type_room_1 += $tmp[$i]['Available'];
				$total_filled_type_room_1 += $tmp[$i]['Filled'];
			} else if($tmp[$i]['type_room_id'] == 2) {
				$total_avail_type_room_2 += $tmp[$i]['Available'];
				$total_filled_type_room_2 += $tmp[$i]['Filled'];
			} else if($tmp[$i]['type_room_id'] == 3) {
				$total_avail_type_room_3 += $tmp[$i]['Available'];
				$total_filled_type_room_3 += $tmp[$i]['Filled'];
			} else if($tmp[$i]['type_room_id'] == 4) {
				$total_avail_type_room_4 += $tmp[$i]['Available'];
				$total_filled_type_room_4 += $tmp[$i]['Filled'];
			} else if($tmp[$i]['type_room_id'] == 5) {
				$total_avail_type_room_5 += $tmp[$i]['Available'];
				$total_filled_type_room_5 += $tmp[$i]['Filled'];
			}
		}
		
		$helper = $this->db->query("
			SELECT *
			FROM helper
			WHERE variable = 'type_room' AND is_delete = '0'
			ORDER BY code DESC
		")->result();

		for ($j=0; $j < count($helper); $j++) { 
			$result[$j]['type_room_id'] = intval($helper[$j]->code);
			$result[$j]['type_room_name'] = $helper[$j]->name;
			if(intval($helper[0]->code == 1)) {
				$result[$j]['total_available'] = $total_avail_type_room_1;
				$result[$j]['total_filled'] = $total_filled_type_room_1;
			} else if(intval($helper[0]->code == 2)) {
				$result[$j]['total_available'] = $total_avail_type_room_2;
				$result[$j]['total_filled'] = $total_filled_type_room_2;
			} else if(intval($helper[0]->code == 3)) {
				$result[$j]['total_available'] = $total_avail_type_room_3;
				$result[$j]['total_filled'] = $total_filled_type_room_3;
			} else if(intval($helper[0]->code == 4)) {
				$result[$j]['total_available'] = $total_avail_type_room_4;
				$result[$j]['total_filled'] = $total_filled_type_room_4;
			} else if(intval($helper[0]->code == 5)) {
				$result[$j]['total_available'] = $total_avail_type_room_5;
				$result[$j]['total_filled'] = $total_filled_type_room_5;
			}
		}

		return $result;
	}
}?>