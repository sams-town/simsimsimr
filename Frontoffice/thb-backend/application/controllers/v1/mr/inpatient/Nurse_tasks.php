<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Nurse_tasks extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_item_model", "{$this->name}_item");
        $this->load->model("$this->directory/Nurse_patient_encounter_model", "Nurse_patient_encounter");
        $this->load->model("v1/medical_support/Nurse_schedule_model", "Nurse_schedule");
        $this->load->model("v1/medical_support/Nurse_schedule_item_model", "Nurse_schedule_item");
        $this->load->model("v1/mr/Registration_destination_model", "Registration_destination");
        $this->load->model("v1/pharmacy/Kardex_model", "Kardex");
        $this->load->model("v1/pharmacy/Kardex_item_model", "Kardex_item");
        $this->load->model("v1/pharmacy/Kardex_non_pharmacological_model", "Kardex_non_pharmacological");

        $this->create_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->update_rules = array(
            array('field' => 'note', 'label' => 'note', 'rules' => 'trim|required'),
        );
    }

    public function detail_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 

        $nurse_tasks_id = $id;

        if ($nurse_tasks_id === 0 || $nurse_tasks_id === "0") {
            $this->REST_Return(400, 'nurse_tasks_id is required');
            return;
        }

        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read($nurse_tasks_id);
        $response_data = array();

        $response_data = $output;

        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function list_get($p = 0){
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 

        $limit = $q_strings['limit'] ?? 20;
        $offset = $q_strings['offset'] ?? 0;

        $this->$model->limit = $limit; 
        $this->$model->offset = $offset; 
        
        if(isset($q_strings['limit']) || isset($q_strings['offset'])) {
            unset($q_strings['limit']);
            unset($q_strings['offset']);
        }

        $where = [];

        $health_facility_item_id = $q_strings['health_facility_item_id'] ?? null;

        if ($health_facility_item_id != null) {
            $where['rd.health_facility_item_id'] = $health_facility_item_id;
        }

        $select = 'a.*';
        $where['a.is_delete'] = 0;
        $join = array(
            array('registration_destination rd', 'a.registration_destination_id = rd.id', 'INNER'),
        );
        $output = $this->$model->select(0, $select, $where, $join);

        for ($i=0; $i < count($output); $i++) { 
            $nurse_tasks_id = $output[$i]->id;

            $output[$i]->task_type_one = [];
            $output[$i]->task_type_two = [];

            $select = 'a.*';
            $where = array(
                'a.nurse_tasks_id' => $nurse_tasks_id,
                'a.is_delete' => 0,
            );
            $nurse_tasks_item = $this->Nurse_tasks_item->select(0, $select, $where);

            for ($j=0; $j < count($nurse_tasks_item); $j++) { 
                $type = $nurse_tasks_item[$j]->type;

                if ($type == 1) {
                    array_push($output[$i]->task_type_one, $nurse_tasks_item[$j]);
                } elseif ($type == 2) {
                    array_push($output[$i]->task_type_two, $nurse_tasks_item[$j]);
                }
            }
        }

        $this->$model->limit = null;
        $this->$model->offset = null;
        $select = 'count(*) as iTotal';
        $iTotal = $this->$model->select(0, $select)[0]->iTotal;   

        $response_data = (object) array(
            'rows' => $output,
            'start' => ($offset + $limit),
            'iTotal' => $iTotal
        );
        
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function assign_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        // $nurse_tasks_id = $id;

        $health_facility_item_id = $q_strings['health_facility_item_id'] ?? null;
        $date = $q_strings['date'] ?? null;
        $shift_type_id = $q_strings['shift_type_id'] ?? null;

        if ($health_facility_item_id == null) {
            $this->REST_Return(400, 'health_facility_item_id is required');
            return;
        }

        if ($date == null) {
            $this->REST_Return(400, 'date is required');
            return;
        }

        if ($shift_type_id == null) {
            $this->REST_Return(400, 'shift_type_id is required');
            return;
        }


        // Pasien yang sedang perawatan sesuai dengan bangsal
        $select = 'a.id, r.norm';
        $where = array(
            'a.health_facility_item_id' => $health_facility_item_id,
            'a.is_checkout' => 0,
            'a.is_cancel' => 0,
        );
        $join = array(
            array('registration r', 'a.registration_id = r.id', 'INNER'),
        );
        $registration_destination = $this->Registration_destination->select(0, $select, $where, $join);
        
        // Untuk mendapatkan perawat yang sedang bertugas
        // $month_schedule = date('Y-m', strtotime($date));
        
        $select = 'a.id, nsi.id as nurse_schedule_item_id, nsi.nurse_id';
        $where = array(
            'a.health_facility_item_id' => $health_facility_item_id,
            // 'a.date' => "$month_schedule-01",
            'nsi.date' => $date,
            'nsi.shift_type_id' => $shift_type_id,
        );
        $join = array(
            array('nurse_schedule_item nsi', 'a.id = nsi.nurse_schedule_id', 'INNER'),
        );
        $nurse_schedule = $this->Nurse_schedule->select(0, $select, $where, $join);

        if (count($nurse_schedule) == 0) {
            $this->REST_Return(404, 'Tidak ada perawat yang bertugas pada tanggal ' . $date);
            return;
        }

        // Hitung berapa banyak pasien
        $patient_count = count($registration_destination);
        // Hitung berapa banyak perawat
        $nurse_count = count($nurse_schedule);

        $modulus_result = $patient_count % $nurse_count;
        if ($modulus_result == 0) {
            $max_per_nurse = $patient_count / $nurse_count;
            $rest_patient_count = 0;
        } else {
            $max_per_nurse = ($patient_count - $modulus_result) / $nurse_count;
            $rest_patient_count = $modulus_result;
        }

        
        // Define perawat yang tersedia
        $nurse_tasks = [];
        for ($i=0; $i < count($nurse_schedule); $i++) { 
            $nurse_schedule[$i]->patients = [];
            array_push($nurse_tasks, $nurse_schedule[$i]);
        }

        $patient_not_assigned = []; // Pasien yang belum mendapat perawat dimasukan kesini

        // Membagi rata pasien ke perawat yang sedang bertugas
        for ($i=0; $i < count($registration_destination); $i++) { 
            $available = false;
            for ($j=0; $j < count($nurse_tasks); $j++) { 
                $patients = $nurse_tasks[$j]->patients;

                if (count($patients) < $max_per_nurse) {
                    $available = true;
                    $nurse_tasks_index = $j;
                    break;
                }
            }

            if ($available) {
                array_push($nurse_tasks[$nurse_tasks_index]->patients, $registration_destination[$i]);
            } else {
                array_push($patient_not_assigned, $registration_destination[$i]);
            }
        }

        // Membagi pasien yang berlebih ke perawat sama rata
        for ($i=0; $i < count($patient_not_assigned); $i++) { 
            array_push($nurse_tasks[$i]->patients, $patient_not_assigned[$i]);
        }

        /* Start Transaction */
        $this->db->trans_start();


        for ($i=0; $i < count($nurse_tasks); $i++) { 
            $nurse_schedule_item_id = $nurse_tasks[$i]->nurse_schedule_item_id;
            $nurse_id = $nurse_tasks[$i]->nurse_id;
            $patients = $nurse_tasks[$i]->patients;

            // var_dump($patients);

            for ($j=0; $j < count($patients); $j++) { 
                $registration_destination_id = $patients[$j]->id;
                $norm = $patients[$j]->norm;

                /* 
                    Mencari task perawat terakhir dari pasien jika pasien memiliki Kardex
                    Jika tidak ada, membuat task baru dengan mengambil perintah dr dari Kardex aktif
                    Jika ada, men-copy catatan dan item dari shift perawat sebelum ke task perawat shift baru
                */ 
                $select = 'a.id, a.note';
                $where = array(
                    'a.registration_destination_id' => $registration_destination_id,
                    'a.is_delete' => 0,
                );
                $join = array();
                $order = array('a.date_added' => 'DESC');
                $this->Nurse_tasks->limit = 1;
                $nurse_tasks_last = $this->Nurse_tasks->select(0, $select, $where, $join, $order);
                $this->Nurse_tasks->limit = 0;

                if (count($nurse_tasks_last) == 0) { // Tidak ada task sebelum
                    // var_dump("test");

                    $dt_nurse_tasks = array(
                        'registration_destination_id' => $registration_destination_id,
                        'nurse_schedule_item_id' => $nurse_schedule_item_id,
                        'nurse_id' => $nurse_id,
                        'date_added' => date('Y-m-d H:i:s'),
                        'date_updated' => date('Y-m-d H:i:s'),
                        'user_added' => $user_id,
                    );

                    // echo "test";

                    $nurse_tasks_id = $this->Nurse_tasks->create($dt_nurse_tasks);

                    $select = 'a.id';
                    $where = array(
                        'a.registration_destination_id' => $registration_destination_id,
                        'a.is_active' => 1,
                        'a.is_delete' => 0,
                    );
                    $kardex = $this->Kardex->select(0, $select, $where);

                    // if ($registration_destination_id == '9ca2a39f-9851-40b2-b015-7c5ce2fdf284') {
                    //     var_dump($kardex);
                    //     die();
                    // }
                    if (count($kardex) > 0) {
                        $kardex_id = $kardex[0]->id;

                        $select = 'a.id, a.pharmacy_catalog_id';
                        $where = array(
                            'a.kardex_id' => $kardex_id,
                            'a.is_delete' => 0,
                        );
                        $where_kardex_item = 'a.status != 4';
                        $this->db->where($where_kardex_item);
                        $kardex_item = $this->Kardex_item->select(0, $select, $where);

                        // if ($registration_destination_id == '9ca2a39f-9851-40b2-b015-7c5ce2fdf284') {
                        //     var_dump($this->db->last_query());
                        //     var_dump($kardex_item);
                        //     die();
                        // }

                        for ($k=0; $k < count($kardex_item); $k++) { 
                            $pharmacy_catalog_id = $kardex_item[$k]->pharmacy_catalog_id;

                            $dt_nurse_tasks_item = array(
                                'nurse_tasks_id' => $nurse_tasks_id,
                                'pharmacy_catalog_id' => $pharmacy_catalog_id,
                                'type' => 1,
                                'date_added' => date('Y-m-d H:i:s'),
                                'date_updated' => date('Y-m-d H:i:s'),
                                'user_added' => $user_id,
                            );

                            $this->Nurse_tasks_item->create($dt_nurse_tasks_item);
                        }

                        $select = 'a.id, np.name';
                        $where = array(
                            'a.kardex_id' => $kardex_id,
                            'a.is_delete' => 0,
                        );
                        $join = array(
                            array('non_pharmacological np', 'a.non_pharmacological_id = np.id', 'INNER'),
                        );
                        $kardex_non_pharmacological = $this->Kardex_non_pharmacological->select(0, $select, $where, $join);

                        for ($k=0; $k < count($kardex_non_pharmacological); $k++) { 
                            $name = $kardex_non_pharmacological[$k]->name;

                            $dt_nurse_tasks_item = array(
                                'nurse_tasks_id' => $nurse_tasks_id,
                                'type' => 2,
                                'name' => $name,
                                'date_added' => date('Y-m-d H:i:s'),
                                'date_updated' => date('Y-m-d H:i:s'),
                                'user_added' => $user_id,
                            );

                            $this->Nurse_tasks_item->create($dt_nurse_tasks_item);
                        }

                        // var_dump($kardex_item);
                    }
                } elseif (count($nurse_tasks_last) > 0) { // Ada task sebelum
                    $nurse_tasks_last_id = $nurse_tasks_last[0]->id;
                    $note = $nurse_tasks_last[0]->note;

                    // Mencari existing task perawat agar tidak perlu membuat ulang (berbeda dengan TASK SEBELUM)
                    $select = 'a.id';
                    $where = array(
                        'registration_destination_id' => $registration_destination_id,
                        'nurse_schedule_item_id' => $nurse_schedule_item_id,
                        'nurse_id' => $nurse_id,
                    );
                    $nurse_tasks_exists = $this->Nurse_tasks->select(0, $select, $where);
                    if (count($nurse_tasks_exists) == 0) { // Tidak punya task, Assign
                        // var_dump("masuk");


                        $dt_nurse_tasks = array(
                            'registration_destination_id' => $registration_destination_id,
                            'nurse_schedule_item_id' => $nurse_schedule_item_id,
                            'nurse_id' => $nurse_id,
                            'note' => $note,
                            'date_added' => date('Y-m-d H:i:s'),
                            'date_updated' => date('Y-m-d H:i:s'),
                            'user_added' => $user_id,
                        );

                        $nurse_tasks_id = $this->Nurse_tasks->create($dt_nurse_tasks);

                        $select = 'a.id, a.type, a.pharmacy_catalog_id, a.name';
                        $where = array(
                            'a.nurse_tasks_id' => $nurse_tasks_last_id,
                            'a.is_delete' => 0,
                        );
                        $nurse_tasks_item_last = $this->Nurse_tasks_item->select(0, $select, $where);

                        for ($k=0; $k < count($nurse_tasks_item_last); $k++) { 
                            $last_type = $nurse_tasks_item_last[0]->type;
                            $last_pharmacy_catalog_id = $nurse_tasks_item_last[0]->pharmacy_catalog_id;
                            $last_name = $nurse_tasks_item_last[0]->name;

                            $dt_nurse_tasks_item = array(
                                'nurse_tasks_id' => $nurse_tasks_id,
                                'pharmacy_catalog_id' => $last_pharmacy_catalog_id,
                                'type' => $last_type,
                                'name' => $last_name,
                                'date_added' => date('Y-m-d H:i:s'),
                                'date_updated' => date('Y-m-d H:i:s'),
                                'user_added' => $user_id,
                            );

                            $this->Nurse_tasks_item->create($dt_nurse_tasks_item);
                        }
                    }

                    // var_dump($nurse_tasks_exists);
                    // die();

                    // echo "masuk";
                }
                // var_dump($nurse_tasks_last);
    
                // if ($norm == '051534') {
                //     $select = 'a.id';
                //     $where = array();
                //     $this->Kardex->select(0, $select, $where);
                // }
            }
        }

        // die();

        // var_dump($nurse_tasks);
        // die();


        // while ($a <= 10) {
        //     # code...
        // }
        
        // var_dump($nurse_tasks);
        // var_dump($patient_not_assigned);
        // var_dump($patient_count);
        // var_dump($nurse_count);
        // var_dump($rest_patient_count);
        // var_dump($max_per_nurse);
        // die();




        // die();


        $response_data = $registration_destination;

        // $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $registration_destination,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to assign nurse tasks', $response_data);
            return;
        }



        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function update_put($p = 0) {
        // CALL JSON POST
        $data       = $this->json_put();
        // DEFINE ATTR
        $dt_put     = $data['dt_postput'];
        $model      = $this->name;
        $id         = $this->security->xss_clean($p);

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 

        // Form Validation
        $this->form_validation->set_data($dt_put);
        $this->form_validation->set_rules($this->update_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $nurse_tasks_id = $id;

        if ($nurse_tasks_id === 0 || $nurse_tasks_id === "0") {
            $this->REST_Return(400, 'nurse_tasks_id is required');
            return;
        }

        $select = 'a.id';
        $nurse_tasks = $this->Nurse_tasks->select($nurse_tasks_id, $select);
        if (count($nurse_tasks) == 0) {
            $this->REST_Return(404, 'there is no nurse_tasks with id ' . $nurse_tasks_id);
            return;
        }

        $note = $dt_put['note'];

        /* Start Transaction */
        $this->db->trans_start();

        $dt_nurse_tasks = array(
            'note' => $note,
            'date_updated' => date('Y-m-d H:i:s'),
            'user_updated' => $user_id,
        );

        $this->$model->update_v2($nurse_tasks_id, $dt_nurse_tasks);

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_nurse_tasks,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update nurse_tasks', $response_data);
            return;
        }

        $response_data = array(
            'id'     => $id
        );

        $this->REST_Return(200, 'SUCCESS', $response_data);
    }


    public function assign_patient_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $health_facility_item_id = $q_strings['health_facility_item_id'] ?? null;
        $date = $q_strings['date'] ?? null;
        $shift_type_id = $q_strings['shift_type_id'] ?? null;

        if ($health_facility_item_id == null) {
            $this->REST_Return(400, 'health_facility_item_id is required');
            return;
        }

        if ($date == null) {
            $this->REST_Return(400, 'date is required');
            return;
        }

        if ($shift_type_id == null) {
            $this->REST_Return(400, 'shift_type_id is required');
            return;
        }

        $norms = [];

        // Pasien yang sedang perawatan sesuai dengan bangsal
        $select = 'a.id, r.norm';
        $where = array(
            'a.health_facility_item_id' => $health_facility_item_id,
            'a.is_checkout' => 0,
            'a.is_cancel' => 0,
        );
        $join = array(
            array('registration r', 'a.registration_id = r.id', 'INNER'),
        );
        $registration_destination = $this->Registration_destination->select(0, $select, $where, $join);
        for ($i=0; $i < count($registration_destination); $i++) { 
            $norm = $registration_destination[$i]->norm;
            array_push($norms, $norm);
        }
        
        // Untuk mendapatkan perawat yang sedang bertugas
        $select = 'a.id, nsi.id as nurse_schedule_item_id, nsi.nurse_id, nsi.status, nsi.nurse_subs_id';
        $where = array(
            'a.health_facility_item_id' => $health_facility_item_id,
            'nsi.date' => $date,
            'nsi.shift_type_id' => $shift_type_id,
        );
        $join = array(
            array('nurse_schedule_item nsi', 'a.id = nsi.nurse_schedule_id', 'INNER'),
        );
        $nurse_schedule = $this->Nurse_schedule->select(0, $select, $where, $join);

        $nurse_schedule_filtered = [];
        $nurse_ids = [];

        // Filter perawat yang sakit atau tukeran jadwal
        for ($i=0; $i < count($nurse_schedule); $i++) { 
            $status = $nurse_schedule[$i]->status;
            $nurse_id = $nurse_schedule[$i]->nurse_id;
            $nurse_subs_id = $nurse_schedule[$i]->nurse_subs_id;

            if ($status == 3) { // Tukeran jadwal
                $nurse_schedule[$i]->nurse_id = $nurse_subs_id;
                $nurse_id = $nurse_subs_id;
            }

            if ($status == 4) { // Sakit
                continue;
            }

            array_push($nurse_ids, $nurse_id);
            array_push($nurse_schedule_filtered, $nurse_schedule[$i]);
        }

        if (count($nurse_schedule) == 0) {
            $this->REST_Return(404, 'Tidak ada perawat yang bertugas pada tanggal ' . $date);
            return;
        }

        // Hitung berapa banyak pasien
        $patient_count = count($registration_destination);
        // Hitung berapa banyak perawat
        $nurse_count = count($nurse_schedule_filtered);

        /* 
            $max_per_nurse = berapa banyak pasien yang bisa dihandle perawat
            $rest_patient_count = sisaan pasien yang belum terbagi karena $max_per_nurse telah tercapai
        */
        $modulus_result = $patient_count % $nurse_count;
        if ($modulus_result == 0) {
            $max_per_nurse = $patient_count / $nurse_count;
            $rest_patient_count = 0;
        } else {
            $max_per_nurse = ($patient_count - $modulus_result) / $nurse_count;
            $rest_patient_count = $modulus_result;
        }

        /* 
            Define perawat yang tersedia
            Perawat -> Pasien[]
        */ 
        $nurse_tasks = [];
        for ($i=0; $i < count($nurse_schedule_filtered); $i++) { 
            $nurse_schedule_filtered[$i]->patients = [];
            array_push($nurse_tasks, $nurse_schedule_filtered[$i]);
        }

        // Mencari berapa banyak pasien yang continuation
        $nurse_patient_connection = []; // Untuk yang punya koneksi masuk ke array ini
        $nurse_count_to_patient = []; // Untuk yang punya koneksi masuk ke array ini
        $select = 'a.count, a.nurse_id, a.norm';
        $where = array();
        $this->Nurse_patient_encounter->whereInFields['a.nurse_id'] = $nurse_ids;
        $this->Nurse_patient_encounter->whereInFields['a.norm'] = $norms;
        $join = array();
        $order = array('a.count' => 'DESC');
        $nurse_patient_encounter = $this->Nurse_patient_encounter->select(0, $select, $where, $join, $order);
        for ($i=0; $i < count($nurse_patient_encounter); $i++) { 
            $nurse_id = $nurse_patient_encounter[$i]->nurse_id;
            $norm = $nurse_patient_encounter[$i]->norm;

            if (!key_exists($nurse_id, $nurse_count_to_patient)) {
                $nurse_count_to_patient[$nurse_id] = 1;
            } else {
                $nurse_count_to_patient[$nurse_id] = $nurse_count_to_patient[$nurse_id] + 1;
            }

            if (!key_exists($norm, $nurse_patient_connection) && $nurse_count_to_patient[$nurse_id] <= $max_per_nurse) {
                $nurse_patient_connection[$norm] = $nurse_id;
            }

            // Jika perawat sudah penuh dengan pasien tapi masih ada sisa pasien masukin pasien yang continuation ke perawat
            if (!key_exists($norm, $nurse_patient_connection) && $nurse_count_to_patient[$nurse_id] <= $max_per_nurse + 1 && $rest_patient_count > 0) {
                $nurse_patient_connection[$norm] = $nurse_id;
                $rest_patient_count = $rest_patient_count - 1;
            }
        }

        /* Start Transaction */
        $this->db->trans_start();

        /* 
            Assign pasien yang continuation ke perawat
            Perawat -> Pasien['NORM']
        */
        for ($i=0; $i < count($nurse_tasks); $i++) { 
            $nurse_id = $nurse_tasks[$i]->nurse_id;

            foreach ($nurse_patient_connection as $norm => $nurse_connection_id) {
                if ($nurse_id != $nurse_connection_id) {
                    continue;
                }

                for ($j=0; $j < count($registration_destination); $j++) { 
                    $registration_destination_norm = $registration_destination[$j]->norm;
                    // if ($norm == $registration_destination_norm && count($nurse_tasks[$i]->patients) < $max_per_nurse) {
                    if ($norm == $registration_destination_norm) {
                        // $this->Nurse_patient_encounter->encounter($nurse_id, $norm);
                        array_push($nurse_tasks[$i]->patients, $registration_destination[$j]);
                        break;
                    }
                }
            }
        }

        // Filter variable $registration_destination pasien yang sudah ter-assign ke perawat agar tidak assign ulang ke perawat lain
        $registration_destination_filtered = [];
        for ($i=0; $i < count($registration_destination); $i++) { 
            $norm = $registration_destination[$i]->norm;
            if (!key_exists($norm, $nurse_patient_connection)) {
                array_push($registration_destination_filtered, $registration_destination[$i]);
            }
        }

        $patient_not_assigned = []; // Pasien yang belum mendapat perawat dimasukan kesini

        // Membagi rata pasien ke perawat yang sedang bertugas
        for ($i=0; $i < count($registration_destination_filtered); $i++) { 
            $norm = $registration_destination_filtered[$i]->norm;

            $available = false;
            for ($j=0; $j < count($nurse_tasks); $j++) { 
                $patients = $nurse_tasks[$j]->patients;

                if (count($patients) < $max_per_nurse) {
                    $available = true;
                    $nurse_tasks_index = $j;
                    break;
                }
            }

            if ($available) {
                // $this->Nurse_patient_encounter->encounter($nurse_tasks[$nurse_tasks_index]->nurse_id, $norm);
                array_push($nurse_tasks[$nurse_tasks_index]->patients, $registration_destination_filtered[$i]);
            } else {
                array_push($patient_not_assigned, $registration_destination_filtered[$i]);
            }
        }

        // Membagi pasien yang berlebih ke perawat sama rata
        usort($nurse_tasks, function($a, $b) {
            return count($a->patients) <=> count($b->patients); // ascending order
        });
        for ($i=0; $i < count($patient_not_assigned); $i++) { 
            array_push($nurse_tasks[$i]->patients, $patient_not_assigned[$i]);
        }

        for ($i=0; $i < count($nurse_tasks); $i++) { 
            $nurse_id = $nurse_tasks[$i]->nurse_id;
            $nurse_schedule_item_id = $nurse_tasks[$i]->nurse_schedule_item_id;
            $patients = $nurse_tasks[$i]->patients;

            for ($j=0; $j < count($patients); $j++) { 
                $registration_destination_id = $patients[$j]->id;
                $norm = $patients[$j]->norm;

                $dt_nurse_tasks = array(
                    'registration_destination_id' => $registration_destination_id,
                    'nurse_schedule_item_id' => $nurse_schedule_item_id,
                    'nurse_id' => $nurse_id,
                    'user_added' => $user_id,
                    'date_added' => date('Y-m-d H:i:s'),
                    'date_updated' => date('Y-m-d H:i:s'),
                );

                $select = 'a.id';
                $where = array(
                    'a.registration_destination_id' => $registration_destination_id,
                    'a.nurse_schedule_item_id' => $nurse_schedule_item_id,
                    'a.nurse_id' => $nurse_id,
                );
                $check_nurse_tasks = $this->Nurse_tasks->select(0, $select, $where);
                if (count($check_nurse_tasks) == 0) {
                    $this->Nurse_tasks->create($dt_nurse_tasks);
                    $this->Nurse_patient_encounter->encounter($nurse_id, $norm);
                }
            }
        }

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $registration_destination,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to assign patient', $response_data);
            return;
        }

        $response_data = $nurse_tasks;

        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}