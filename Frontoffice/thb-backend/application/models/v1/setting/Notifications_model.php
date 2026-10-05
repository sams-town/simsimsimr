<?php class Notifications_model extends MY_Model {
	protected $tableName = 'notifications';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.created_at' => 'DESC');
	protected $selectFields = "a.*,";
	protected $joinFields = array();
    
    public function createNotification($user_id = null, $position_id = null, $title = null, $message = null, $type, $data = null) {
        if ($data != null) {
            $data = json_encode($data);
        }

        $notification = array(
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'data' => $data,
            'read_at' => null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );

        if ($position_id != null) {
            $select = 'a.employee_id, u.id as user_id';
            $where = array(
                'a.position_id' => $position_id,
            );
            $join = array(
                array('user u', 'a.employee_id = u.employee_id', 'INNER'),
            );
            $employee_position = $this->Employee_position->select(0, $select, $where, $join);
            for ($i=0; $i < count($employee_position); $i++) { 
                $user_id = $employee_position[$i]->user_id;
                $notification['user_id'] = $user_id;
                $notification['position_id'] = $position_id;
                $this->create($notification);
            }
            return;
        } else {
            $notification['user_id'] = $user_id;
            $this->create($notification);
            return;
        }
    }

    public function readNotificationWithItem($user_id, $item_id, $item_name) {
        $notifications = $this->query("
            SELECT id, data
            FROM notifications
            WHERE user_id = '$user_id'
            AND read_at IS NULL
        ");

        $notification_id = null;

        for ($i=0; $i < count($notifications); $i++) { 
            $id = $notifications[$i]->id;
            $data = $notifications[$i]->data;

            if ($data != null) {
                $data = json_decode($data);
                
                foreach ($data as $key => $val) {
                    if ($key == $item_name && $val == $item_id) {
                        $notification_id = $id;
                    }
                }
            }
        }

        if ($notification_id == null) {
            return null;
        }

        $notification = array(
            'read_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );

        return $this->update_v2($notification_id, $notification);
    }

    public function readNotificationPositionWithItem($position_id, $type, $item_id, $item_name) {
        $notifications = $this->query("
            SELECT id, data
            FROM notifications
            WHERE position_id = '$position_id'
            AND type = '$type'
            AND read_at IS NULL
        ");

        $notification_ids = [];

        for ($i=0; $i < count($notifications); $i++) { 
            $id = $notifications[$i]->id;
            $data = $notifications[$i]->data;

            if ($data != null) {
                $data = json_decode($data);
                
                foreach ($data as $key => $val) {
                    if ($key == $item_name && $val == $item_id) {
                        array_push($notification_ids, $id);
                    }
                }
            }
        }

        if ($notification_ids == []) {
            return null;
        }

        $notification = array(
            'read_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );

        $where_notifiction = 'id IN ("'.join('","', $notification_ids).'")';
        $this->db->where($where_notifiction);
        $where = array(
            'type' => $type
        );
        
        return $this->update_v2(0, $notification, $where);
    }
}?>