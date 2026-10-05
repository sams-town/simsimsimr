<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Registration_insurance extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
    }

    /**
     * GET Registration_insurance with API
     * -------------------------
     * @method: GET
     */

     public function detail_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
            if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
            $this->$model->whereFields = $q_strings;
            $this->$model->whereFields['a.is_cancel'] = '0';
            $output = $this->$model->read($id);
            $response_data = array();
            if ($output != [] && $id != 0) {
                $response_data = $output;
                $error   = false;
            } else if($output != [] or $id == 0) {
                $response_data = $output;
                $error   = false;
            } else {
                $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
                return;
            }
            if ($error == true) {
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $this->REST_Return(200, 'SUCCESS', $response_data);
            }
            return;
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
	}

    /**
     * GET Registration_insurance with API
     * -------------------------
     * @method: GET
     */

     public function history_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
            if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
            if ($q_strings['norm']){
                $this->$model->whereFields['r.norm'] = $q_strings['norm'];
            }
            // $this->$model->whereFields = $q_strings;
            $this->$model->whereFields['r.is_cancel'] = '0';
            $this->$model->history();
            $output = $this->$model->read_ssr($id);
            $response_data = array();
            if ($output != [] && $id != 0) {
                $response_data = $output;
                $error   = false;
            } else if($output != [] or $id == 0) {
                $response_data = $output;
                $error   = false;
            } else {
                $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
                return;
            }
            if ($error == true) {
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $this->REST_Return(200, 'SUCCESS', $response_data);
            }
            return;
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
	}

    public function noka_get($p = 0){
       header("Access-Control-Allow-Origin: *");
       parse_str($_SERVER['QUERY_STRING'],$q_strings);
       $error  = true;
       $id     = $this->security->xss_clean($p);
       $model  = $this->name;

       // Token Validation
       $is_valid_token = $this->authorization_token->validateToken();
       if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
           $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
           if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
           if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
           if ($q_strings['number_insurance']){
               $this->$model->whereFields['a.number_insurance'] = $q_strings['number_insurance'];
           }
           if ($q_strings['registration_destination_id']){
               $this->$model->whereFields['a.registration_destination_id'] = $q_strings['registration_destination_id'];
           }
           // $this->$model->whereFields = $q_strings;
           $this->$model->whereFields['a.is_cancel'] = '0';
           $this->$model->noka();
           $output = $this->$model->read(0,false);
           $response_data = array();
           if ($output != [] && $id != 0) {
               $response_data = $output;
               $error   = false;
           } else if($output != [] or $id == 0) {
               $response_data = $output;
               $error   = false;
           } else {
               $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
               return;
           }
           if ($error == true) {
               $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
           } else {
               $this->REST_Return(200, 'SUCCESS', $response_data);
           }
           return;
       } else {
           $response_data = 'Invalid Token.';
           $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
           return;
       }
   }

    public function list_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
            if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
            $this->$model->whereLike = $q_strings;
            $this->$model->whereFields['a.is_cancel'] = '0';
            $output = $this->$model->read_ssr($id);
            $response_data = array();
            if ($output->rows != [] && $id != 0) {
                $response_data = $output->rows[0];
                $error   = false;
            } else if($output->rows != [] or $id == 0) {
                $response_data = $output;
                $error   = false;
            } else {
                $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
                return;
            }
            if ($error == true) {
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $this->REST_Return(200, 'SUCCESS', $response_data);
            }
            return;
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
	}

    public function upload_pdf_post($p = 0) {
        $_POST      = $this->security->xss_clean($_POST);
        $_FILES     = $this->security->xss_clean($_FILES);
        $_SERVER    = $this->security->xss_clean($_SERVER);
        $device     = $_SERVER['HTTP_USER_AGENT'];
        $id         = $this->security->xss_clean($p);
        $ip         = $this->get_client_ip();
        $error      = TRUE;
        $model      = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 

        $this->form_validation->set_data($_POST);
        $this->form_validation->set_rules('sep', 'sep', 'trim|required|max_length[19]');
        if ($this->form_validation->run() == FALSE){
            $response_data = array(
                'error'     => $this->form_validation->error_array()
            );
            $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
        }

        $select = 'a.registration_destination_id,a.insurance_id,a.is_primary';
        $where = array('registration_destination_id' => $id);
        $registration_destination = $this->$model->select(0, $select, $where);
        
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'registration_insurance with registration_destination_id ' . $id . ' does not exist!');
            return;
        }

        $sep = $_POST['sep'];
        $noKunjungan = $_POST['noKunjungan'];
        $jns_sep = $_POST['jns_sep'];

        if ($sep == NULL) {
            $this->REST_Return(400, 'field sep is required');
            return;
        }

        $registration_destination_id = $registration_destination[0]->registration_destination_id;

        # Upload document pdf
        if ($_FILES['file_insurance']['name'] != "" && $_FILES['file_insurance']['name'] != null) {
            $filename_raw = $_FILES['file_insurance']['name'];
            $filename_raw = str_replace(' ', '_', $filename_raw);
            $filename = 'assets/document/insurance/' . $registration_destination_id . '.pdf';
    
            $type = $_FILES['file_insurance']['type'];
            $tmp_name = $_FILES['file_insurance']['tmp_name'];
            $error = $_FILES['file_insurance']['error'];
            $size = $_FILES['file_insurance']['size'];

            $_FILES['images']['name']= $filename_raw;
            $_FILES['images']['type']= $type;
            $_FILES['images']['tmp_name']= $tmp_name;
            $_FILES['images']['error']= $error;
            $_FILES['images']['size']= $size;
            
            $config['upload_path'] = './assets/document/insurance';
            $config['allowed_types'] = 'pdf';
            $config['overwrite'] = true;	
            $config['file_name'] = $registration_destination_id;
            $this->load->library('upload', $config, 'photo_upload');
            $this->photo_upload->initialize($config);
        }

        if ($_FILES['file_insurance']['name'] != "" && $_FILES['file_insurance']['name'] != null) {
            if ($this->photo_upload->do_upload('images') ) {
                $fileData = $this->photo_upload->data();
            } else {       
                $response_data = array(
                    'error'     => $this->photo_upload->display_errors(),
                    'data'      => [],
                    'query'     => ""
                );                 
                $this->REST_Return(500, 'Error while trying to upload file', $response_data);
                return;
            }
        }

        if ($_FILES['file_spri']['name'] != "" && $_FILES['file_spri']['name'] != null) {
            $filename_raw_2 = $_FILES['file_spri']['name'];
            $filename_raw_2 = str_replace(' ', '_', $filename_raw_2);
            $filename_2 = 'assets/document/insurance/spri_' . $registration_destination_id . '.pdf';
    
            $type_2 = $_FILES['file_spri']['type'];
            $tmp_name_2 = $_FILES['file_spri']['tmp_name'];
            $error_2 = $_FILES['file_spri']['error'];
            $size_2 = $_FILES['file_spri']['size'];
    
            $_FILES['images']['name']= $filename_raw_2;
            $_FILES['images']['type']= $type_2;
            $_FILES['images']['tmp_name']= $tmp_name_2;
            $_FILES['images']['error']= $error_2;
            $_FILES['images']['size']= $size_2;
            
            $config_2['upload_path'] = './assets/document/insurance';
            $config_2['allowed_types'] = 'pdf';
            $config_2['overwrite'] = true;	
            $config_2['file_name'] = $registration_destination_id;
            $this->load->library('upload', $config_2, 'photo_upload');
            $this->photo_upload->initialize($config_2);
        }

        if ($_FILES['file_spri']['name'] != "" && $_FILES['file_spri']['name'] != null) {
            if ($this->photo_upload->do_upload('images') ) {
                $fileData = $this->photo_upload->data();
            } else {       
                $response_data = array(
                    'error'     => $this->photo_upload->display_errors(),
                    'data'      => [],
                    'query'     => ""
                );                 
                $this->REST_Return(500, 'Error while trying to upload file', $response_data);
                return;
            }
        }

        /* Start Transaction */
        $this->db->trans_start();

        for ($i=0; $i < count($registration_destination); $i++) { 
            $insurance_id = $registration_destination[$i]->insurance_id;
            $is_primary = $registration_destination[$i]->is_primary;

            $dt_registration_insurance = array(
                'registration_destination_id'   => $registration_destination_id,
                'insurance_id'                  => $insurance_id,
                'is_primary'                    => $is_primary,
                'sep'                           => $sep,
                'jns_sep'                       => $jns_sep,
                'noKunjungan'                   => $noKunjungan,
                'file'                          => $filename,
                'file_spri'                     => $filename_2
            );
            
            $this->$model->replace($dt_registration_insurance);
        }

        $this->db->trans_complete();
        /* End Transaction */
        
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_registration_insurance,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update registration_insurance', $response_data);
            return;
        }

        $response_data = $id;

        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;     
    }

    public function upload_kronologis_post($p = 0) {
        $_POST      = $this->security->xss_clean($_POST);
        $_FILES     = $this->security->xss_clean($_FILES);
        $_SERVER    = $this->security->xss_clean($_SERVER);
        $device     = $_SERVER['HTTP_USER_AGENT'];
        $id         = $this->security->xss_clean($p);
        $ip         = $this->get_client_ip();
        $error      = TRUE;
        $model      = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 

        $select = 'a.registration_destination_id,a.insurance_id,a.is_primary';
        $where = array('registration_destination_id' => $id);
        $registration_destination = $this->$model->select(0, $select, $where);
        
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'registration_insurance with registration_destination_id ' . $id . ' does not exist!');
            return;
        }

        // $sep = $_POST['sep'];

        // if ($sep == NULL) {
        //     $this->REST_Return(400, 'field sep is required');
        //     return;
        // }

        $registration_destination_id = $registration_destination[0]->registration_destination_id;

        # Upload document pdf
        $filename_raw = $_FILES['file_kronologis']['name'];
        $filename_raw = str_replace(' ', '_', $filename_raw);
        $filename = 'assets/document/insurance/kronologis_' . $registration_destination_id . '.pdf';
        $type = $_FILES['file_kronologis']['type'];
        $tmp_name = $_FILES['file_kronologis']['tmp_name'];
        $error = $_FILES['file_kronologis']['error'];
        $size = $_FILES['file_kronologis']['size'];

        $_FILES['images']['name']= $filename_raw;
        $_FILES['images']['type']= $type;
        $_FILES['images']['tmp_name']= $tmp_name;
        $_FILES['images']['error']= $error;
        $_FILES['images']['size']= $size;
        
        $config['upload_path'] = './assets/document/insurance';
        $config['allowed_types'] = 'pdf';
        $config['overwrite'] = true;	
        $config['file_name'] = 'kronologis_' . $registration_destination_id;
        $this->load->library('upload', $config, 'photo_upload');
        $this->photo_upload->initialize($config);
        if ($this->photo_upload->do_upload('images') ) {
            $fileData = $this->photo_upload->data();
        } else {       
            $response_data = array(
                'error'     => $this->photo_upload->display_errors(),
                'data'      => [],
                'query'     => ""
            );                 
            $this->REST_Return(500, 'Error while trying to upload file', $response_data);
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();

        for ($i=0; $i < count($registration_destination); $i++) { 
            $insurance_id = $registration_destination[$i]->insurance_id;
            $is_primary = $registration_destination[$i]->is_primary;

            $dt_registration_insurance = array(
                'registration_destination_id'   => $registration_destination_id,
                'insurance_id'                  => $insurance_id,
                'is_primary'                    => $is_primary,
                'file_kronologis'               => $filename
            );
            
            $this->$model->replace($dt_registration_insurance);
        }

        $this->db->trans_complete();
        /* End Transaction */
        
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_registration_insurance,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update registration_insurance', $response_data);
            return;
        }

        $response_data = $id;

        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;     
    }

    // /**
    //  * Registration_insurance API
    //  * --------------------
    //  * @param: insurances
    //  * --------------------
    //  * @method : POST
    //  * @link: version/mr/registration_insurance
    //  **/

    // public function update_put($p = 0) {
    //     $id         = $this->security->xss_clean($p);
    //     // CALL JSON POST
    //     $data       = $this->json_put();
    //     // DEFINE ATTR
    //     $dt_put     = $data['dt_postput'];
    //     $device     = $data['dt_server']['HTTP_USER_AGENT'];
    //     $ip         = $this->get_client_ip();
    //     $error      = TRUE;

    //     // Token Validation
    //     $is_valid_token = $this->authorization_token->validateToken();
    //     if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
    //         # Form Validation
    //         $this->form_validation->set_data($dt_put);
    //         $this->form_validation->set_rules('insurances[]', 'insurances', 'trim|required');
    //         if ($this->form_validation->run() == FALSE){
    //             // Form Validation Errors
    //             $response_data = array(
    //                 'error'     => $this->form_validation->error_array()
    //             );
    //             $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
    //         } else {
    //             $model = $this->name;
    //             /* Start Transaction */
    //             $this->db->trans_start();
    //             if($this->$model->read($id) != []) {
    //                 $registration_insurance = $this->$model->read($id);
    //                 if($registration_insurance != []) {
    //                     $temp = array();
    //                     for ($a=0; $a < count($registration_insurance); $a++) { 
    //                         array_push($temp, array(
    //                             'registration_destination_id'   => $id,
    //                             'company_id'                    => $registration_insurance[$a]->company_id,
    //                             'number_insurance'              => $registration_insurance[$a]->number_insurance,
    //                             'expired'                       => $registration_insurance[$a]->expired,
    //                             'service_class_id'              => $registration_insurance[$a]->service_class_id,
    //                             'service_class_name'            => $registration_insurance[$a]->service_class_name,
    //                             'is_cancel'                     => '1',
    //                         ));
    //                     }
    //                     $this->$model->replace_batch($temp);
        
    //                     $dt_reg_insu = array();
    //                     for ($i=0; $i < count($dt_put['insurances']) ; $i++) {
    //                         array_push($dt_reg_insu, array(
    //                             'registration_destination_id'   => $id,
    //                             'company_id'                    => $dt_put['insurances'][$i]['company_id'],
    //                             'is_cancel'                     => '0'
    //                         ));
    //                     }
    //                     $id = $this->$model->replace_batch($dt_reg_insu);
    //                 }
    //             } else {
    //                 $this->REST_Return(404, 'Registration Insurance with id ' . $id . ' does not exist!');
    //                 return;
    //             }
    //             $this->db->trans_complete();
    //             /* End Transaction */
    //             if ($this->db->trans_status() === false) {
    //                 $response_data = array();
    //                 $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
    //             } else {
    //                 $response_data = $id;
    //                 $this->REST_Return(200, 'SUCCESS', $response_data);
    //             }
    //             return;     
    //         }
    //     } else {
    //         $response_data = 'Invalid Token.';
    //         $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
    //         return;
    //     }
    // }
}