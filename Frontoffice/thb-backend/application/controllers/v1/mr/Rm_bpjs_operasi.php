<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Rm_bpjs_operasi extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/Registration_model", "Registration");
        $this->load->model("$this->directory/Registration_destination_model", "Registration_destination");
        $this->load->model("$this->directory/Icd9cm_model", "Icd9cm");
        $this->load->model("v1/setting/Health_facility_item_model", "Health_facility_item");
        // Load Helper
        $this->load->helper("queue");

        $this->create_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'tindakan', 'label' => 'tindakan', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->update_rules = array(
            array('field' => 'tindakan', 'label' => 'tindakan', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->delete_rules = array(
            array('field' => 'is_deleted', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
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
    
        $kodebooking = $id;
    
        $this->$model->whereFields['a.is_deleted'] = 0;
        $output = $this->$model->read($kodebooking);
        $response_data = array();
    
        if ($output == []) {
            $this->REST_Return(404, 'rm_bpjs_operasi with kodebooking ' . $kodebooking . ' does not exist!');
            return;
        }
    
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
    
        if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
        if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);

        $this->$model->whereLike = $q_strings;
        $output = $this->$model->read_ssr($id);
        
        $response_data = $output;
        
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
	}

    public function create_post() {
        // CALL JSON POST
        $data       = $this->json_post();
        // DEFINE ATTR
        $dt_post    = $data['dt_postput'];
        $model      = $this->name;
    
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 
    
        // Form Validation
        $this->form_validation->set_data($dt_post);
        $this->form_validation->set_rules($this->create_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        $registration_destination_id = $dt_post['registration_destination_id'];
    
        $select = 'a.id, a.health_facility_item_id, a.registration_id';
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select);
    
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        } 

        $health_facility_item_id = $registration_destination[0]->health_facility_item_id;

        $select = 'a.id, a.bride_bpjs, a.bride_bpjs_name';
        $health_facility_item = $this->Health_facility_item->select($health_facility_item_id, $select);

        $kodepoli = $health_facility_item[0]->bride_bpjs;
        $namapoli = $health_facility_item[0]->bride_bpjs_name;

        $registration_id = $registration_destination[0]->registration_id;

        $select = 'a.id, c.name';
        $registration = $this->Registration->select($registration_id, $select);

        $nm_mst_pasien = $registration[0]->name;
    
        $tindakan_id = $dt_post['tindakan'];
    
        $select = 'a.id, a.code, a.name';
        $tindakan = $this->Icd9cm->select($tindakan_id, $select);
    
        if (count($tindakan) == 0) {
            $this->REST_Return(404, 'there is no tindakan (icd9cm) with id ' . $tindakan_id);
            return;
        }

        $kodetindakan = $tindakan[0]->code;
        $namatindakan = $tindakan[0]->name;

        $tanggal = $dt_post['tanggal'];
        $jam_operasi = $dt_post['jam_operasi'];
        $status = $dt_post['status'];
        $nomorkartu = $dt_post['nomorkartu'];
        $asa = $dt_post['asa'];
        $intubasi = $dt_post['intubasi'];
        $jenis_operasi = $dt_post['jenis_operasi'];
        $flag_prioritas = $dt_post['flag_prioritas'];
        $flag_infectious = $dt_post['flag_infectious'];
        $flag_acc = $dt_post['flag_acc'];

        // count kodebooking
        $prefix = '1007R001OK';
        $epoch = strtotime($tanggal);
        $dt_queue = array(
            'var_2' => date('y', $epoch),
            'var_3' => date('m', $epoch),
            'var_4' => date('d', $epoch),
            'var_5' => date('ymd', $epoch),
        );

        $kodebooking = counter_queue($prefix, $dt_queue, 4);
        $queue = round((int)substr($kodebooking, -4));

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_rm_bpjs_operasi = array(
            'kodebooking'                   => $kodebooking,
            'registration_destination_id'   => $registration_destination_id,
            'nm_mst_pasien'                 => $nm_mst_pasien,
            'kodepoli'                      => $kodepoli,
            'namapoli'                      => $namapoli,
            'tindakan'                      => $tindakan_id,
            'kodetindakan'                  => $kodetindakan,
            'namatindakan'                  => $namatindakan,
            'tanggal'                       => $tanggal,
            'jam_operasi'                   => $jam_operasi,
            'queue'                         => $queue,
            'status'                        => $status,
            'nomorkartu'                    => $nomorkartu,
            'asa'                           => $asa,
            'intubasi'                      => $intubasi,
            'jenis_operasi'                 => $jenis_operasi,
            'flag_prioritas'                => $flag_prioritas,
            'flag_infectious'               => $flag_infectious,
            'flag_acc'                      => $flag_acc,
        );
    
        $id = $this->$model->replace($dt_rm_bpjs_operasi);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_rm_bpjs_operasi,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create rm_bpjs_operasi', $response_data);
            return;
        }
    
        $response_data = array(
            'kodebooking'     => $kodebooking
        );
    
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
    
        $select = 'a.kodebooking';
        $where = array('kodebooking' => $id);
        $kodebooking = $this->$model->select(0, $select, $where);
    
        if (count($kodebooking) == 0) {
            $this->REST_Return(404, 'there is no rm_bpjs_operasi with kodebooking ' . $id);
            return;
        }

        $kodebooking_id = $kodebooking[0]->kodebooking;

        $tindakan_id = $dt_put['tindakan'];
    
        $select = 'a.id, a.code, a.name';
        $tindakan = $this->Icd9cm->select($tindakan_id, $select);
    
        if (count($tindakan) == 0) {
            $this->REST_Return(404, 'there is no tindakan (icd9cm) with id ' . $tindakan_id);
            return;
        }

        $kodetindakan = $tindakan[0]->code;
        $namatindakan = $tindakan[0]->name;

        $tanggal = $dt_put['tanggal'];
        $jam_operasi = $dt_put['jam_operasi'];
        $status = $dt_put['status'];
        $nomorkartu = $dt_put['nomorkartu'];
        $asa = $dt_put['asa'];
        $intubasi = $dt_put['intubasi'];
        $jenis_operasi = $dt_put['jenis_operasi'];
        $flag_prioritas = $dt_put['flag_prioritas'];
        $flag_infectious = $dt_put['flag_infectious'];
        $flag_acc = $dt_put['flag_acc'];

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_rm_bpjs_operasi = array(
            'tindakan'          => $tindakan_id,
            'kodetindakan'      => $kodetindakan,
            'namatindakan'      => $namatindakan,
            'tanggal'           => $tanggal,
            'jam_operasi'       => $jam_operasi,
            'status'            => $status,
            'nomorkartu'        => $nomorkartu,
            'asa'               => $asa,
            'intubasi'          => $intubasi,
            'jenis_operasi'     => $jenis_operasi,
            'flag_prioritas'    => $flag_prioritas,
            'flag_infectious'   => $flag_infectious,
            'flag_acc'          => $flag_acc,
        );
    
        $this->$model->update($kodebooking_id, $dt_rm_bpjs_operasi);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_rm_bpjs_operasi,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update rm_bpjs_operasi', $response_data);
            return;
        }
    
        $response_data = array(
            'kodebooking' => $kodebooking_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function delete_delete($p = 0) {
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
        $this->form_validation->set_rules($this->delete_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
        
        $select = 'a.kodebooking';
        $where = array('kodebooking' => $id);
        $kodebooking = $this->$model->select(0, $select, $where);
    
        if (count($kodebooking) == 0) {
            $this->REST_Return(404, 'there is no rm_bpjs_operasi with kodebooking ' . $id);
            return;
        }

        $kodebooking_id = $kodebooking[0]->kodebooking;
    
        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_deleted = $dt_put['is_deleted'];
    
        $dt_rm_bpjs_operasi = array(
            'is_deleted' => $is_deleted,
        );
    
        $id = $this->$model->update($kodebooking_id, $dt_rm_bpjs_operasi);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_rm_bpjs_operasi,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete rm_bpjs_operasi', $response_data);
            return;
        }
    
        $response_data = array(
            'kodebooking' => $kodebooking_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}