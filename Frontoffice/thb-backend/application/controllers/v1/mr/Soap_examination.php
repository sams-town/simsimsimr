<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Soap_examination extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/Category_examination_model", "Category_examination");
        $this->load->model("$this->directory/Soap_model", "Soap");
        $this->load->model("$this->directory/Patient_model", "Patient");
        // Load Helper
        $this->load->helper("upload");

        $this->upload_rules = array(
            array('field' => 'file', 'label' => 'file', 'rules' => 'trim'),
            array('field' => 'soap_id', 'label' => 'soap_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'category_examination_id', 'label' => 'category_examination_id', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->update_rules = array(
            array('field' => 'examination_date', 'label' => 'examination_date', 'rules' => 'trim|required'),
            array('field' => 'notes', 'label' => 'notes', 'rules' => 'trim'),
            array('field' => 'impression', 'label' => 'impression', 'rules' => 'trim'),
        );

        $this->update_file_rules = array(
            array('field' => 'file_code', 'label' => 'file_code', 'rules' => 'trim|required'),
            array('field' => 'expertise', 'label' => 'expertise', 'rules' => 'trim'),
        );

        $this->update_image_rules = array(
            array('field' => 'file', 'label' => 'file', 'rules' => 'trim'),
            array('field' => 'file_code', 'label' => 'file_code', 'rules' => 'trim|required'),
        );

        $this->delete_file_rules = array(
            array('field' => 'file_code', 'label' => 'file_code', 'rules' => 'trim|required'),
        );

        $this->delete_image_rules = array(
            array('field' => 'file_code', 'label' => 'file_code', 'rules' => 'trim|required'),
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

        $where = $q_strings;

        if (isset($q_strings['norm'])) {
            $where['r.norm'] = $q_strings['norm'];
            unset($where['norm']);
        }

        if (isset($q_strings['registration_id'])) {
            $where['rd.registration_id'] = $q_strings['registration_id'];
            unset($where['registration_id']);
        }

        $select = 'a.*, r.norm, ce.name as category_examination_name, e.fullname as doctor_name, e.ttd as ttd_doctor';
        $where['a.is_delete'] = 0;
        $join = array(
            array('soap s', 'a.soap_id = s.id', 'INNER'),
            array('employee e', 'a.doctor_id = e.id', 'LEFT'),
            array('registration_destination rd', 's.registration_destination_id = rd.id', 'INNER'),
            array('registration r', 'rd.registration_id = r.id', 'INNER'),
            array('category_examination ce', 'a.category_examination_id = ce.id', 'INNER'),
        );
        $order = array('a.date_added' => 'DESC');
        $output = $this->$model->select(0, $select, $where, $join, $order);
        
        $real_output = [];

        for ($i=0; $i < count($output); $i++) { 
            $data = json_decode($output[$i]->data);
            $delete_con = 0;

            // filtering deleted file
            if (isset($data->files)) {
                $files =& $data->files;
                $tmp_idx = array();

                // looping through files
                for ($j=0; $j < count($files); $j++) { 
                    $is_delete = $files[$j]->is_delete;
                    
                    // pushing deleted file index into temp array
                    if ($is_delete == 1) {
                        array_push($tmp_idx, $j);
                    }

                    $base64_output = "";

                    $url = $files[$j]->url;
                    $extension = explode('.',$url)[1];

                    $file = file_get_contents($url);

                    if ($file) {
                        $base64 = base64_encode($file);
                        $base64_output = 'data:application/'.$extension.';base64,' . $base64;
                    }

                    // $files[$j]->base64 = $base64_output;
                }

                // unsetting the deleted file from files
                for ($k=0; $k < count($tmp_idx); $k++) { 
                    unset($files[$tmp_idx[$k]]);
                }

                // re-index files
                $files = array_values($files);

                if (count($files) == 0) {
                    $delete_con = 1;
                }
            }

            $output[$i]->data = $data;

            if ($this->is_json($output[$i]->data)) {
                $output[$i]->data = json_decode($output[$i]->data);
            }

            if ($delete_con != 1) {
                array_push($real_output, $output[$i]);
                // unset($output[$i]);
            }
            
            $output = array_values($output);
        }

        $response_data = $real_output;

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

        $where = array();

        $limit = $q_strings['limit'] ?? 20;
        $offset = $q_strings['offset'] ?? 0;

        $this->$model->limit = $limit;
        $this->$model->offset = $offset; 
        
        if (isset($q_strings['norm'])) {
            $where['r.norm'] = $q_strings['norm'];
        }

        if (isset($q_strings['registration_destination_id'])) {
            $where['s.registration_destination_id'] = $q_strings['registration_destination_id'];
        }

        $select = 'a.*, r.norm, ce.name as category_examination_name';
        $where['a.is_delete'] = 0;
        $join = array(
            array('soap s', 'a.soap_id = s.id', 'INNER'),
            array('registration_destination rd', 's.registration_destination_id = rd.id', 'INNER'),
            array('registration r', 'rd.registration_id = r.id', 'INNER'),
            array('category_examination ce', 'a.category_examination_id = ce.id', 'INNER'),
        );
        $order = array('a.examination_date' => 'DESC');
        $output = $this->$model->select(0, $select, $where, $join, $order);

        $real_output = [];

        for ($i=0; $i < count($output); $i++) { 
            $data = json_decode($output[$i]->data);
            $delete_con = 0;

            // filtering deleted file
            if (isset($data->files)) {
                $files =& $data->files;
                $tmp_idx = array();

                // looping through files
                for ($j=0; $j < count($files); $j++) { 
                    $is_delete = $files[$j]->is_delete;
                    
                    // pushing deleted file index into temp array
                    if ($is_delete == 1) {
                        array_push($tmp_idx, $j);
                    }

                    $base64_output = "";

                    $url = $files[$j]->url;
                    $extension = explode('.',$url)[1];

                    $file = file_get_contents($url);

                    if ($file) {
                        $base64 = base64_encode($file);
                        $base64_output = 'data:application/'.$extension.';base64,' . $base64;
                    }

                    $files[$j]->base64 = $base64_output;
                }

                // unsetting the deleted file from files
                for ($k=0; $k < count($tmp_idx); $k++) { 
                    unset($files[$tmp_idx[$k]]);
                }

                // re-index files
                $files = array_values($files);

                if (count($files) == 0) {
                    $delete_con = 1;
                }
            }

            $output[$i]->data = $data;

            if ($this->is_json($output[$i]->data)) {
                $output[$i]->data = json_decode($output[$i]->data);
            }

            if ($delete_con != 1) {
                array_push($real_output, $output[$i]);
                // unset($output[$i]);
            }

            $output = array_values($output);
        }

        $this->$model->limit = null;
        $this->$model->offset = null;
        $select = 'count(*) as iTotal';
        $iTotal = $this->$model->select(0, $select, $where, $join)[0]->iTotal;   

        $response_data = (object) array(
            'rows' => $real_output,
            'start' => ($offset + $limit),
            'iTotal' => $iTotal
        );
        
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    private function is_json($string){
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }

    // public function create_post() {
    //     $data       = $this->json_post();
    //     // CALL JSON POST
    //     $dt_post    = $data['dt_postput'];
    //     $model   = $this->name;
    
    //     // Token Validation
    //     $is_valid_token = $this->authorization_token->validateToken();
    //     if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
    //         $response_data = 'Invalid Token.';
    //         $this->REST_Return(401, $is_valid_token['message'] , $response_data);
    //         return;
    //     } 
    
    //     // // Form Validation
    //     // $this->form_validation->set_data($dt_post);
    //     // $this->form_validation->set_rules($this->create_rules);
    //     // if ($this->form_validation->run() == FALSE){
    //     //     // Form Validation Errors
    //     //     $response_data = $this->form_validation->error_array();
    //     //     $this->REST_Return(422, 'Format error', $response_data);
    //     //     return;
    //     // }

    //     $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        
    //     $soap_id = $dt_post['soap_id'];
    //     $category_examination_id = $dt_post['category_examination_id'];

    //     $select = 'a.id';
    //     $soap = $this->Soap->select($soap_id, $select);

    //     if (count($soap) == 0) {
    //         $this->REST_Return(404, 'there is no soap with id ' . $soap_id);
    //         return;
    //     }

    //     $data = array();

    //     $data_encode = json_encode($data);
        
    //     /* Start Transaction */
    //     $this->db->trans_start();

    //     $notes = $dt_post['notes'];
    //     $impression = $dt_post['impression'];
    //     $examination_date = $dt_post['examination_date'];
    
    //     $dt_soap_examination = array(
    //         'soap_id' => $soap_id,
    //         'category_examination_id' => $category_examination_id,
    //         'data' => '{}',
    //         'notes' => $notes,
    //         'impression' => $impression,
    //         'examination_date' => $examination_date,
    //         'date_added' => date('Y-m-d H:i:s',time()),
    //     );
    
    //     $id = $this->$model->create($dt_soap_examination);
    
    //     $this->db->trans_complete();
    //     /* End Transaction */
    
    //     if ($this->db->trans_status() === false) {
    //         $response_data = array(
    //             'error'     => $this->db->error(),
    //             'data'      => $dt_soap_examination,
    //             'query'     => $this->db->last_query()
    //         );
    //         $this->REST_Return(500, 'error while trying to create soap_examination', $response_data);
    //         return;
    //     }
    
    //     $response_data = array(
    //         'id'     => $id
    //     );
    
    //     $this->REST_Return(200, 'SUCCESS', $response_data);
    //     return;     
    
    // }
    
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

        $soap_id = $_POST['soap_id'];
        $category_examination_id = $_POST['category_examination_id'];
        // $notes = $_POST['notes'];
        // $impression = $_POST['impression'];
        $expertise = $_POST['expertise'];
        $doctor_id = $_POST['doctor_id'];
        $examination_date = $_POST['examination_date'];

        $select = 'a.id, r.norm';
        $soap = $this->Soap->select($soap_id, $select);

        if (count($soap) == 0) {
            $this->REST_Return(404, 'there is no soap with id ' . $soap_id);
            return;
        }

        $norm = $soap[0]->norm;

        $select = 'a.id, a.name';
        $category_examination = $this->Category_examination->select($category_examination_id, $select);

        if (count($category_examination) == 0) {
            $this->REST_Return(404, 'there is no category_examination with id ' . $category_examination_id);
            return;
        }

        $category_examination_name = $category_examination[0]->name;

        /* Start Transaction */
        $this->db->trans_start();

        $select = 'a.id, a.data';
        $where = array('soap_id' => $soap_id, 'category_examination_id' => $category_examination_id);
        $soap_examination = $this->$model->select(0, $select, $where);

        if (count($soap_examination) == 0) {
            $dt_soap_examination = array(
                'soap_id' => $soap_id,
                'category_examination_id' => $category_examination_id,
                'data' => '{}',
                // 'notes' => $notes,
                // 'impression' => $impression,
                // 'examination_date' => $examination_date,
                'date_added' => date('Y-m-d H:i:s',time()),
                'date_updated' => date('Y-m-d H:i:s',time()),
            );

            $soap_examination_id = $this->$model->create($dt_soap_examination);

            $soap_examination = $this->$model->select(0, $select, $where);
        } else {
            $soap_examination_id = $soap_examination[0]->id;
        }

        $data = json_decode($soap_examination[0]->data);
        
        $select = 'a.norm, a.name';
        $patient = $this->Patient->select($norm, $select);

        $patient_name = $patient[0]->name;

        $patient_name = str_replace("'",'',str_replace(']', '', str_replace('[', '', $patient_name)));
        
        $bytes = random_bytes(6 / 2);
        $file_code = substr(bin2hex($bytes), 0, 6);
        $filename = $file_code . '_' . str_replace(' ', '_', $category_examination_name) . '_' . $norm . '_' . str_replace(' ', '_', $patient_name) . '_' . date('Ymd_His');

        $dt_file = (object) array(
            'file_code' => $file_code,
            'filename' => '',
            'expertise' => $expertise,
            'doctor_id' => $doctor_id,
            'url' => '',
            'examination_date' => $examination_date,
            'date_added' => date('Y-m-d H:i:s', time()),
            'is_delete' => 0,
        );

        if (count($_FILES) > 0) {
            $upload = upload_file($_FILES['file'], $filename, 'assets/document/examination');
    
            if ($upload['status'] == 'failed') {
                $this->REST_Return(422, $upload['message'], array());
                return;
            }
            
            $ext_type = $upload['type'];
    
            $filename = $filename . '.' . $ext_type;
            $url = 'assets/document/examination/' . $filename;

            $dt_file->filename = $filename;
            $dt_file->url = $url;
        }

        if (!isset($data->files)) {
            $data->files = array();
        }

        $files =& $data->files;
            
        array_push($files, $dt_file);
        
        $data_encode = json_encode($data);

        $dt_soap_examination = array(
            'id' => $soap_examination_id,
            'soap_id' => $soap_id,
            'category_examination_id' => $category_examination_id,
            'data' => $data_encode,
        );
    
        $this->$model->replace($dt_soap_examination);

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_soap_examination,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to upload soap_examination', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $soap_examination_id
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
            
        $soap_examination_id = $id;

        $select = 'a.id';
        $soap_examination = $this->$model->select($soap_examination_id, $select);

        if (count($soap_examination) == 0) {
            $this->REST_Return(404, 'there is no soap_examination with id ' . $soap_examination_id);
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_soap_examination = array(
            'examination_date' => $dt_put['examination_date'],
            'notes' => $dt_put['notes'],
            'impression' => $dt_put['impression'],
            'date_updated' => date('Y-m-d H:i:s', time()),
        );
    
        $this->$model->update($soap_examination_id, $dt_soap_examination);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_soap_examination,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update soap_examination', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $soap_examination_id
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

        $soap_examination_id = $id;
    
        $select = 'a.id';
        $soap_examination = $this->$model->select($soap_examination_id, $select);
    
        if (count($soap_examination) == 0) {
            $this->REST_Return(404, 'there is no soap_examination with id ' . $soap_examination_id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $dt_soap_examination = array(
            'date_updated' => date('Y-m-d H:i:s'),
            'is_delete' => $is_delete,
        );
    
        $this->$model->update($soap_examination_id, $dt_soap_examination);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_soap_examination,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete soap_examination', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $soap_examination_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function update_file_put($p = 0) {
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
        $this->form_validation->set_rules($this->update_file_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        $soap_examination_id = $id;

        $select = 'a.id, a.data';
        $soap_examination = $this->$model->select($soap_examination_id, $select);
    
        if (count($soap_examination) == 0) {
            $this->REST_Return(404, 'there is no soap_examination with id ' . $soap_examination_id);
            return;
        }

        $data = json_decode($soap_examination[0]->data);

        $file_code = $dt_put['file_code'];
        $examination_date = $dt_put['examination_date'];
        $expertise = $dt_put['expertise'];
        $doctor_id = $dt_put['doctor_id'];

        /* Start Transaction */
        $this->db->trans_complete();

        $files =& $data->files;

        $valid = 0;
        for ($i=0; $i < count($files); $i++) { 
            $file_code_exist = $files[$i]->file_code;
            $filename = $files[$i]->filename;
            $url = $files[$i]->url;
            $date_added = $files[$i]->date_added;

            if ($file_code_exist == $file_code) {
                $valid = 1;

                $dt_files = array(
                    'file_code' => $file_code,
                    'filename' => $filename,
                    'expertise' => $expertise,
                    'doctor_id' => $doctor_id,
                    'url' => $url,
                    'examination_date' => $examination_date,
                    'date_added' => $date_added,
                    'is_delete' => 0,
                );

                $files[$i] = $dt_files;
                $data_encode = json_encode($data);

                $dt_soap_examination = array(
                    'date_updated' => date('Y-m-d H:i:s'),
                    'doctor_id' => $doctor_id,
                    'data' => $data_encode,
                );

                $this->$model->update_v2($soap_examination_id, $dt_soap_examination);
                break;
            }
        }

        if ($valid == 0) {
            $this->REST_Return(404, 'there is no file_code ' . $file_code . ' in soap_examination with id ' . $soap_examination_id);
            return;
        }
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_soap_examination,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update file soap_examination', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $soap_examination_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function update_image_post($p = 0) {
        ini_set('upload_max_filesize', '10M');
        ini_set('post_max_size', '100M');
        $_POST   = $this->security->xss_clean($_POST);   
        $_SERVER = $this->security->xss_clean($_SERVER);
        $_FILES  = $this->security->xss_clean($_FILES);
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
        $this->form_validation->set_rules($this->update_image_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        $soap_examination_id = $id;

        $select = 'a.id, a.data, r.norm, p.name as patient_name, ce.name as category_examination_name';
        $where = array();
        $join = array(
            array('soap s', 'a.soap_id = s.id', 'INNER'),
            array('registration_destination rd', 's.registration_destination_id = rd.id', 'INNER'),
            array('registration r', 'rd.registration_id = r.id', 'INNER'),
            array('patient p', 'r.norm = p.norm', 'INNER'),
            array('category_examination ce', 'a.category_examination_id = ce.id', 'INNER'),
        );
        $soap_examination = $this->$model->select($soap_examination_id, $select, $where, $join);
        if (count($soap_examination) == 0) {
            $this->REST_Return(404, 'there is no soap_examination with id ' . $soap_examination_id);
            return;
        }

        $norm = $soap_examination[0]->norm;
        $patient_name = $soap_examination[0]->patient_name;
        $category_examination_name = $soap_examination[0]->category_examination_name;

        $data = json_decode($soap_examination[0]->data);

        $file_code = $_POST['file_code'];

        /* Start Transaction */
        $this->db->trans_complete();

        $files =& $data->files;

        $valid = 0;
        for ($i=0; $i < count($files); $i++) { 
            $file_code_exist = $files[$i]->file_code;
            $expertise = $files[$i]->expertise;
            $examination_date = $files[$i]->examination_date;
            $date_added = $files[$i]->date_added;

            if ($file_code_exist == $file_code) {
                $valid = 1;

                $patient_name = str_replace("'",'',str_replace(']', '', str_replace('[', '', $patient_name)));
                $filename = $file_code . '_' . str_replace(' ', '_', $category_examination_name) . '_' . $norm . '_' . str_replace(' ', '_', $patient_name) . '_' . date('Ymd_His');
                
                $upload = upload_file($_FILES['file'], $filename, 'assets/document/examination');
                if ($upload['status'] == 'failed') {
                    $this->REST_Return(422, $upload['message'], array());
                    return;
                }
                
                $ext_type = $upload['type'];
                $filename = $filename . '.' . $ext_type;
                $url = 'assets/document/examination/' . $filename;

                $dt_files = array(
                    'file_code' => $file_code,
                    'filename' => $filename,
                    'expertise' => $expertise,
                    'url' => $url,
                    'examination_date' => $examination_date,
                    'date_added' => $date_added,
                    'is_delete' => 0,
                );

                $files[$i] = $dt_files;
                $data_encode = json_encode($data);

                $dt_soap_examination = array(
                    'date_updated' => date('Y-m-d H:i:s'),
                    'data' => $data_encode,
                );

                $this->$model->update_v2($soap_examination_id, $dt_soap_examination);
                break;
            }
        }

        if ($valid == 0) {
            $this->REST_Return(404, 'there is no file_code ' . $file_code . ' in soap_examination with id ' . $soap_examination_id);
            return;
        }
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_soap_examination,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update image soap_examination', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $soap_examination_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function delete_file_delete($p = 0) {
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
        $this->form_validation->set_rules($this->delete_file_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        $soap_examination_id = $id;

        $select = 'a.id, a.data';
        $soap_examination = $this->$model->select($soap_examination_id, $select);
    
        if (count($soap_examination) == 0) {
            $this->REST_Return(404, 'there is no soap_examination with id ' . $soap_examination_id);
            return;
        }

        $data = json_decode($soap_examination[0]->data);

        $file_code = $dt_put['file_code'];

        /* Start Transaction */
        $this->db->trans_complete();

        $files =& $data->files;

        $valid = 0;
        for ($i=0; $i < count($files); $i++) { 
            $file_code_exist = $files[$i]->file_code;
            $filename = $files[$i]->filename;
            $expertise = $files[$i]->expertise;
            $url = $files[$i]->url;
            $examination_date = $files[$i]->examination_date;
            $date_added = $files[$i]->date_added;

            if ($file_code_exist == $file_code) {
                $valid = 1;

                $dt_files = array(
                    'file_code' => $file_code,
                    'filename' => $filename,
                    'url' => $url,
                    'expertise' => $expertise,
                    'examination_date' => $examination_date,
                    'date_added' => $date_added,
                    'is_delete' => 1,
                );

                $files[$i] = $dt_files;
                $data_encode = json_encode($data);

                $dt_soap_examination = array(
                    'date_updated' => date('Y-m-d H:i:s'),
                    'data' => $data_encode,
                );

                $this->$model->update_v2($soap_examination_id, $dt_soap_examination);
                break;
            }
        }

        if ($valid == 0) {
            $this->REST_Return(404, 'there is no file_code ' . $file_code . ' in soap_examination with id ' . $soap_examination_id);
            return;
        }
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_soap_examination,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete file soap_examination', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $soap_examination_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function delete_image_delete($p = 0) {
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
        $this->form_validation->set_rules($this->delete_image_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        $soap_examination_id = $id;

        $select = 'a.id, a.data';
        $soap_examination = $this->$model->select($soap_examination_id, $select);
    
        if (count($soap_examination) == 0) {
            $this->REST_Return(404, 'there is no soap_examination with id ' . $soap_examination_id);
            return;
        }

        $data = json_decode($soap_examination[0]->data);

        $file_code = $dt_put['file_code'];

        /* Start Transaction */
        $this->db->trans_complete();

        $files =& $data->files;

        $valid = 0;
        for ($i=0; $i < count($files); $i++) { 
            $file_code_exist = $files[$i]->file_code;
            $filename = $files[$i]->filename;
            $expertise = $files[$i]->expertise;
            $url = $files[$i]->url;
            $examination_date = $files[$i]->examination_date;
            $date_added = $files[$i]->date_added;

            if ($file_code_exist == $file_code) {
                $valid = 1;

                $dt_files = array(
                    'file_code' => $file_code,
                    'filename' => '',
                    'url' => '',
                    'expertise' => $expertise,
                    'examination_date' => $examination_date,
                    'date_added' => $date_added,
                    'is_delete' => 0,
                );

                $files[$i] = $dt_files;
                $data_encode = json_encode($data);

                $dt_soap_examination = array(
                    'date_updated' => date('Y-m-d H:i:s'),
                    'data' => $data_encode,
                );

                $this->$model->update_v2($soap_examination_id, $dt_soap_examination);
                break;
            }
        }

        if ($valid == 0) {
            $this->REST_Return(404, 'there is no file_code ' . $file_code . ' in soap_examination with id ' . $soap_examination_id);
            return;
        }
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_soap_examination,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete file soap_examination', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $soap_examination_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function pdf_to_base64_get($p = 0){
        $p = basename($p); // Sanitize

        // Path to your PDF (can be inside assets, uploads, etc.)
        $filePath = 'assets/document/examination/' . $p;
        
        // Check if file exists
        if (!file_exists($filePath)) {
            $this->REST_Return(404, 'pdf file not found', []);
            return;
        }

        // Read file contents
        $pdfData = file_get_contents($filePath);

        // Convert to base64
        $base64 = base64_encode($pdfData);

        // Return as JSON
        $response = [
            'filename' => basename($filePath),
            'base64' => 'data:application/pdf;base64,' . $base64
        ];

        $this->REST_Return(200, 'SUCCESS', $response);
        return;
    }
}