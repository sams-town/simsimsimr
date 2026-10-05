<?php
function queue_time_interval($appointment_schedule, $hour_start, $hour_end, $quota, $quota_bo, $date_appointment) {
    $hour_start = strtotime($hour_start); 
    $hour_end = strtotime($hour_end);
    $mins = ($hour_end - $hour_start) / 60;
    $quota = $quota;
    $long = floor($mins / $quota);
    $last_time = $hour_start - $long * 60;

    $result = array();
    $result['minutes_long'] = $long;
    $result['queue'] = array();
    for ($i=1; $i < $quota_bo + 1; $i++) {
        $new_time = $last_time + $long * 60;
        $new_datetime = date('H:i:s', $new_time);
        $last_time = strtotime($new_datetime);
        
        $data = array("queue"=>$i,"time"=>$new_datetime,"reserve"=>0,"patient_name"=>'');
        
        for ($j=0; $j < count($appointment_schedule); $j++) {
            if($appointment_schedule[$j]->queue == $i) {
                $data['patient_name'] = $appointment_schedule[$j]->patient_name;
                $data['reserve'] = 1;
            }
        }

        array_push($result['queue'],$data);
    }

    return $result;
}

function no_request($name,$prefix) {
    $ci=& get_instance();
    $ci->load->model($name);
    $result = array(); 

    // Count no_request
    $ci->$name->whereFields['a.is_delete'] = 0;
    $ci->$name->orderFields = array('a.no_request' => 'DESC');
    $ci->$name->limit = 1;
    $tmp = $ci->$name->read(0,false);
    $waktu = date("Ymd",time());
    if($tmp != []) {
        $result['no_request'] = $prefix . $waktu . str_pad(intval(substr($tmp[0]->no_request,11)) + 1, 4, '0', STR_PAD_LEFT);
        $result['queue'] = abs(str_pad(intval(substr($tmp[0]->no_request,11)) + 1, 4, '0', STR_PAD_LEFT));
        // Check if reset is needed
        if(substr($tmp[0]->no_request,3,8) != date('Ymd',time())) {
            $result['no_request'] = $prefix . $waktu . '0001';
            $result['queue'] = 1;
        }
    } else {
        $result['no_request'] = $prefix . $waktu . '0001';
        $result['queue'] = 1;
    }

    // // Count queue
    // $ci->$name->whereFields['a.is_delete'] = 0;
    // $ci->$name->orderFields = array('a.queue' => 'DESC');
    // $ci->$name->limit = 1;
    // $tmp = $ci->$name->read(0,false);
    // if($tmp != []) {
    //     $result['queue'] = $tmp[0]->queue + 1;
    //     // Check if reset is needed
    //     if(substr($tmp[0]->no_request,3,8) != date('Ymd',time())) {
    //         $result['queue'] = 1;
    //     }
    // } else {
    //     $result['queue'] = 1;
    // }

    return $result;
}


// 1007R001OK231203004

function counter_queue($prefix, $data, $length) {
    $ci=& get_instance();
    $ci->load->model('Queue_model', 'Queue');
    
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
    $counter = $ci->Queue->select($prefix, $select, $where);

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

    // NOTE - dihapus trans_start & trans_complete sama angga tanggal 10 juni

    $ci->Queue->replace($dt_queue);

    $queue = str_pad($queue, $length, "0", STR_PAD_LEFT);

    $no = $prefix . $var_2 . $var_3 . $var_4 . $queue;

    return $no;
}