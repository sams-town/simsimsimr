<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Stt extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        // $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/mr/Icd10_model", "Icd10");
        // Load Helper
        $this->load->helper('request');

        $this->upload_rules = array(
            array('field' => 'file', 'label' => 'file', 'rules' => 'trim'),
        );
    }

    public function get_get($p = 0) {
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

        $response = get_request('http://host.docker.internal:7777/');

        $this->REST_Return(200, 'SUCCESS', $response);
        return;
    }

    public function get_percentage_get($p = 0) {
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

        $transcript_id = $id;

        if ($transcript_id === 0 || $transcript_id === "0") {
            $this->REST_Return(400, 'transcript_id is required');
            return;
        }

        $response = get_request('http://host.docker.internal:7777/transcripts/' . $transcript_id);

        $this->REST_Return(200, 'SUCCESS', $response);
        return;
    }

    public function get_soap_get($p = 0) {
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

        $transcript_id = $id;

        if ($transcript_id === 0 || $transcript_id === "0") {
            $this->REST_Return(400, 'transcript_id is required');
            return;
        }

        $response = get_request('http://host.docker.internal:7777/transcripts/get_soap/' . $transcript_id);

        $subjective = $response['data']['subjective'];
        $objective = $response['data']['objective'];
        $assessment = $response['data']['assessment'];
        $plan = $response['data']['plan'];
        $icd10 = $response['data']['icd10'];

        $output = [
            "subjective" => $subjective,
            "objective" => $objective,
            "assessment" => $assessment,
            "plan" => $plan,
            "icd10" => [],
        ];

        foreach ($icd10 as $icd10_id) {
            $select = 'a.id, a.name, a.code';
            $icd10 = $this->Icd10->select($icd10_id, $select);

            $icd10_name = $icd10[0]->name;
            $icd10_code = $icd10[0]->code;

            $dt_icd10 = [
                'id' => $icd10_id,
                'code' => $icd10_code,
                'name' => $icd10_name,
            ];

            array_push($output['icd10'], $dt_icd10);
        }

        $this->REST_Return(200, 'SUCCESS', $output);
        return;
    }

    public function upload_post() {
        ini_set('upload_max_filesize', '10M');
        ini_set('post_max_size', '100M');
        $_POST   = $this->security->xss_clean($_POST);   
        $_SERVER = $this->security->xss_clean($_SERVER);
        $_FILES  = $this->security->xss_clean($_FILES);
        $model   = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 

        // Form Validation
        $this->form_validation->set_rules($this->upload_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        $curl_file = make_curl_file();
        
        $payload = [
            'file' => $curl_file,
        ];
        
        $response = post_request('http://host.docker.internal:7777/transcripts/upload/', $payload);
        // {
        //     "http_code": 201,
        //     "message": "success upload transcript audio",
        //     "data": {
        //         "id": "436b4f04529d4fc68c4c9bd45e8e7f53",
        //         "file_path": "./app/audio/1c6013dc0dbea50fd9a1e04b23bb2ca7.mp3",
        //         "loading_percentage": 0,
        //         "transcript": null
        //     }
        // }

        // unlink($filePath); // Optional: delete uploaded file from PHP server
    
        $this->REST_Return(200, 'SUCCESS', $response);
        return;
    }
}