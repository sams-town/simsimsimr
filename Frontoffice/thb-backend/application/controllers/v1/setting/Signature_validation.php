<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Signature_validation extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/Signature_validation", "Signature_validation");
        $this->load->model("$this->directory/{$this->name}_item_model", "SignatureItem");
        $this->load->model("v1/setting/Transaction_signature_validation_model", "TransactionSignatureValidation");
        $this->load->model("$this->directory/Module_model", "Module");
        $this->load->model("v1/Institute_branch_model", "Institute_branch");

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
    }

    public function create_post($p = 0) {
        // CALL JSON POST
        $data       = $this->json_post();
        // DEFINE ATTR
        $dt_post    = $data['dt_postput'];
        $device     = $data['dt_server']['HTTP_USER_AGENT'];
        $ip         = $this->get_client_ip();
        $error      = TRUE;

        // Token Validation
        $validatedToken = $this->authorization_token->validateToken();
        if (!empty($validatedToken) AND $validatedToken['status'] === TRUE){
            $this->form_validation->set_rules('module_id', 'module_id', 'trim|required');
            if ($this->form_validation->run() == FALSE) {
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            }
            $user_id = $this->security->xss_clean($validatedToken['data']->id);
            $model = $this->name;

            // transaction start
            $this->db->trans_start();
            $dt_signature = array(
                'module_id'         => $dt_post['module_id'],
                'user_added'        => $user_id,
                'date_added'        => date('Y-m-d H:i:s',time()),
            );

            $id_signature = $this->$model->create($dt_signature);

            if (!empty($id_signature)){
                for ($i= 0; $i < count($dt_post['signature_validation_items']); $i++){
                    $dt_signature_validation_item = array(
                        'signature_validation_id' => $id_signature,
                        'group_id' => $dt_post['signature_validation_items'][$i]['group_id'],
                        'level_validation' => $dt_post['signature_validation_items'][$i]['level_validation']
                    );
                    $this->SignatureItem->create($dt_signature_validation_item);
                }
            }
            // end transaction
            $this->db->trans_complete();
            if ($this->db->trans_status() === false) {
                $response_data = $this->db->trans_status();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $response_data = array(
                    'id'     => $id_signature
                );
                $this->REST_Return(201, 'SUCCESS', $response_data);
            }
        }else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $validatedToken['message'] ,$response_data);
            return;
        }
    }

    public function update_put($p = 0) {
        $id         = $this->security->xss_clean($p);
        // CALL JSON POST
        $data       = $this->json_put();
        // DEFINE ATTR
        $dt_put     = $data['dt_postput'];
        $device     = $data['dt_server']['HTTP_USER_AGENT'];
        $ip         = $this->get_client_ip();
        $error      = TRUE;

        // Token Validation
        $validatedToken = $this->authorization_token->validateToken();
        if (!empty($validatedToken) AND $validatedToken['status'] === TRUE){
            $this->form_validation->set_rules('module_id', 'module_id', 'trim|required');
            if ($this->form_validation->run() == FALSE) {
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            }
            $user_id = $this->security->xss_clean($validatedToken['data']->id);
            $model = $this->name;

            // transaction start
            $this->db->trans_start();
            $dt_signature = array(
                'id'                  => $id,
                'module_id'           => $dt_put['module_id'],
                'user_updated'        => $user_id,
                'date_updated'        => date('Y-m-d H:i:s',time()),
            );

            $this->$model->update($id,$dt_signature);
            
            for ($i= 0; $i < count($dt_put['signature_validation_items']); $i++){
                $dt_signature_validation_item = array(
                    'id' => $dt_put['signature_validation_items'][$i]['id'],
                    'signature_validation_id' => $id,
                    'group_id' => $dt_put['signature_validation_items'][$i]['group_id'],
                    'level_validation' => $dt_put['signature_validation_items'][$i]['level_validation']
                );
                $this->SignatureItem->replace($dt_signature_validation_item);
            }
            // end transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === false) {
                $response_data = $this->db->trans_status();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $response_data = array(
                    'id'     => $id
                );
                $this->REST_Return(200, 'SUCCESS', $response_data);
            }
        }else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $validatedToken['message'] ,$response_data);
            return;
        }
    }
    public function list_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $id         = $this->security->xss_clean($p);
        $data       = $this->json_put();
        $device     = $data['dt_server']['HTTP_USER_AGENT'];
        $ip         = $this->get_client_ip();
        $error      = TRUE;

        $validatedToken = $this->authorization_token->validateToken();
        if (!empty($validatedToken) AND $validatedToken['status'] === TRUE){
            $model = $this->name;
            if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
            if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
            $this->$model->whereLike = $q_strings;
            $output = $this->$model->read_ssr($id);
            $response_data = array();

            if(empty($output)){
                $this->REST_Return(404,'Signature validation does not exist!');
                return;
            }

            $response_data = $output;
            $this->REST_Return(200, 'SUCCESS', $response_data);
        }else{
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $validatedToken['message'] ,$response_data);
            return;
        }
    }
    public function detail_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $id         = $this->security->xss_clean($p);
        $ip         = $this->get_client_ip();
        $error      = TRUE;

        $validatedToken = $this->authorization_token->validateToken();
        if (!empty($validatedToken) AND $validatedToken['status'] === TRUE){
            $model = $this->name;
            if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
            if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
            $this->$model->whereFields = $q_strings;
            $output = $this->$model->read($id)[0];
            $response_data = array();
            if(empty($output)){
                $this->REST_Return(404,'Signature validation does not exist!');
                return;
            }
            $this->SignatureItem->whereFields['signature_validation_id'] = $output->id;
            $signature_validation_items = $this->SignatureItem->read();
            for($j = 0; $j < count($signature_validation_items); $j++){
                $output->signature_validation_items[$j]['id'] = $signature_validation_items[$j]->id;
                $output->signature_validation_items[$j]['signature_validation_id'] = $signature_validation_items[$j]->signature_validation_id;
                $output->signature_validation_items[$j]['group_id'] = $signature_validation_items[$j]->group_id;
                $output->signature_validation_items[$j]['level_validation'] = $signature_validation_items[$j]->level_validation;
                $output->signature_validation_items[$j]['group_name'] = $signature_validation_items[$j]->group_name;
            }
            $response_data = $output;
            $this->REST_Return(200, 'SUCCESS', $response_data);
        }else{
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $validatedToken['message'] ,$response_data);
            return;
        }
    }
}