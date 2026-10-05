<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Letter extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/Queue_model", "Queue");
        $this->load->model("v1/mr/Patient_model", "Patient");
        $this->load->model("v1/mr/Nurse_model", "Nurse");
        $this->load->model("v1/mr/Registration_model", "Registration");
        $this->load->model("v1/mr/Responsible_person_model", "Responsible_person");
        $this->load->model("v1/setting/Letter_type_model", "Letter_Type");
        $this->load->model("$this->directory/Registration_destination_model", "Registration_Destination");

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
    }

    /**
     * Letter API
     * --------------------
     * @param: norm
     * @param: no
     * @param: letter_type_id
     * @param: date
     * --------------------
     * @method : POST
     * @link: version/mr/letter
     **/
    function numberToRomanRepresentation($number) {
        $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }
    

    public function create_post() {
        // CALL JSON POST
        $data       = $this->json_post();
        // DEFINE ATTR
        $dt_post    = $data['dt_postput'];
        $device     = $data['dt_server']['HTTP_USER_AGENT'];
        $ip         = $this->get_client_ip();
        $error      = TRUE;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            # Form Validation
            $this->form_validation->set_rules('registration_destination_id', 'registration_destination_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('letter_type_id', 'letter_type_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('date', 'date', 'trim|required');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                // Count no_letter
                $code_letter = '/RSHBH/BTM/';
                if ($dt_post['letter_type_id'] == '7cbf9ef9-71fa-4d14-9f65-a3021bd411ab') {
                    $facilty_health = 'SKRI';
                } else if ($dt_post['letter_type_id'] == 'e9432634-dd6b-41cd-b57d-7c34daba9efc') {
                    $code_letter = '/RSHBH/';
                    $facilty_health = 'RM/SKK';
                } else {
                    $facilty_health = 'POLI';
                    if ($this->Registration_Destination->read($dt_post['registration_destination_id'], false)[0]->health_facility_id == '3cb626f5-9148-4fb1-8663-3d7f028b01a6') {
                        $facilty_health = 'POLI';
                    } else if ($this->Registration_Destination->read($dt_post['registration_destination_id'], false)[0]->health_facility_id == '444b3134-5b75-4b67-af72-a558d46716d7') {
                        $facilty_health = 'IGD';
                    } else if ($this->Registration_Destination->read($dt_post['registration_destination_id'], false)[0]->health_facility_id == '40f06534-07ec-4221-ad9d-dea08196079f') {
                        $facilty_health = 'INAP';
                    } 
                }
                $codecounter = array(
                    'var_1' => $facilty_health,
                    'var_2' => $this->numberToRomanRepresentation(date('m')),
                    'var_3' => date('Y'),
                    'var_5' => date('Y-m')
                );
                $this->load->library("transaction");
                $checkcounter = $this->transaction->counter($codecounter);
                $no_letter = $checkcounter['counter_queue'].'/'.$codecounter['var_1'].$code_letter.$codecounter['var_2'].'/'.$codecounter['var_3'];
                if ($checkcounter['counter_queue'] == '0') {
                    $this->Counter_queue->replace(array(
                        'var_1' => $facilty_health,
                        'var_2' => $this->numberToRomanRepresentation(date('m')),
                        'var_3' => date('Y'),
                        'var_5' => date('Y-m'),
                        'counter_queue' => 0
                    ));
                    $checkcounter = $this->transaction->counter($codecounter);
                    $no_letter = $checkcounter['counter_queue'].'/'.$codecounter['var_1'].$code_letter.$codecounter['var_2'].'/'.$codecounter['var_3'];
                }
                // 83/POLI/RSHBH/BTM/VIII/2024
                // $no_letter = $this->Queue->get_no_letter();
                // Check registration_destination_id
                if($this->Registration_Destination->read($dt_post['registration_destination_id'], false) != []) {
                    $dt_letter_type = $this->Letter_Type->read($dt_post['letter_type_id'], false);
                    if ($dt_letter_type != []) {
                        // letter death
                        if ($dt_post['letter_type_id'] == 'e9432634-dd6b-41cd-b57d-7c34daba9efc') {
                            if ($dt_post['selected_doctor_id'] == '' && $dt_post['selected_doctor_id'] == null) {
                                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', array());
                                return;
                            }
                            $content = array(
                                'receipt_name' => $dt_post['receipt_name'],
                                'receipt_idcard' => $dt_post['receipt_idcard'],
                                'receipt_phone' => $dt_post['receipt_phone'],
                                'receipt_relation' => $dt_post['receipt_relation'],
                                'receipt_address' => $dt_post['receipt_address'],
                                'receipt_religion' => $dt_post['receipt_religion'],
                                'destination_of_the_corpse' => $dt_post['destination_of_the_corpse']
                            );
                            $dt_letter = array(
                                'registration_destination_id'   => $dt_post['registration_destination_id'],
                                'no'                            => $no_letter,
                                'letter_type_id'                => $dt_post['letter_type_id'],
                                'selected_doctor_id'            => $dt_post['selected_doctor_id'],
                                'deathtime'                     => $dt_post['deathtime'],
                                'content'                       => json_encode($content),
                            );
                        } else if ($dt_post['letter_type_id'] == '035e0b43-a5a9-41ae-a183-00398b4b3ef2') {
                            if ($dt_post['selected_doctor_id'] == '' && $dt_post['selected_doctor_id'] == null) {
                                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', array());
                                return;
                            }
                            $content = array(
                                'is_pregnant'       => $dt_post['is_pregnant'],
                                'age_pregnant'      => $dt_post['age_pregnant'],
                            );
                            $dt_letter = array(
                                'registration_destination_id'   => $dt_post['registration_destination_id'],
                                'no'                            => $no_letter,
                                'letter_type_id'                => $dt_post['letter_type_id'],
                                'selected_doctor_id'            => $dt_post['selected_doctor_id'],
                                'date'                          => isset($dt_post['date']) ? $dt_post['date'] : NULL,
                                'content'                       => json_encode($content),
                            );
                        } else {
                            $dt_letter = array(
                                'registration_destination_id'   => $dt_post['registration_destination_id'],
                                'no'                            => $no_letter,
                                'letter_type_id'                => $dt_post['letter_type_id'],
                                'date_added'                    => date('Y-m-d H:i:s'),
                                'content'                       => $dt_letter_type[0]->content,
                                'advice'                        => isset($dt_post['advice']) ? $dt_post['advice'] : "",
                                'conclusion'                    => isset($dt_post['conclusion']) ? $dt_post['conclusion'] : "",
                                'selected_doctor_id'            => isset($dt_post['selected_doctor_id']) ? $dt_post['selected_doctor_id'] : NULL,
                                'witness_name'                  => isset($dt_post['witness_name']) ? $dt_post['witness_name'] : "",
                                'responsible_id'                => isset($dt_post['responsible_id']) ? $dt_post['responsible_id'] : NULL,
                                'responsible_name'              => isset($dt_post['responsible_name']) ? $dt_post['responsible_name'] : "",
                                'content_plus'                  => isset($dt_post['content_plus']) ? $dt_post['content_plus'] : "",
                                'date'                          => isset($dt_post['date']) ? $dt_post['date'] : NULL,
                                'start'                         => isset($dt_post['start']) ? $dt_post['start'] : NULL,
                                'end'                           => isset($dt_post['end']) ? $dt_post['end'] : NULL,
                                'soap_id'                       => isset($dt_post['soap_id']) ? $dt_post['soap_id'] : ""
                            );
                            if (isset($dt_post['file'])) {
                                $file = $dt_post['file'];
                                $extension = $dt_post['extension'];
                                if ($extension == NULL) {
                                    $this->REST_Return(400, 'extension field is required');
                                    return;
                                }
                                $url = $this->base64ToFile($file, str_replace('/', '-', $no_letter) . '.' . $extension);
                                $dt_letter['content'] = $url;
                            }
                        }
                        $id = $this->$model->create($dt_letter);
                    } else {
                        Logger::error('rollback all transaction!');
                        $this->REST_Return(404, 'Wrong Letter ID!');
                        return;
                    }
                } else {
                    Logger::error('rollback all transaction!');
                    $this->REST_Return(404, 'Wrong Destination ID!');
                    return;
                }
                $this->db->trans_complete();
                /* End Transaction */
                if ($this->db->trans_status() === false) {
                    $response_data = $this->db->trans_status();
                    Logger::error('rollback all transaction!');
                    $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                } else {
                    $response_data = array(
                        'id'     => $id
                    );
                    $this->REST_Return(200, 'SUCCESS', $response_data);
                }
                return;     
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    private function base64ToFileNew ($p = 0, $filename = '', $upload_dir = 'assets/document/registration/patient_signature/') {
        $data         = base64_decode($p);
        $file_name    = $filename; // You can change it to anything
        $file         = $upload_dir . $file_name;
        $success      = file_put_contents($file, $data);
        return $file;
    }

    private function base64ToFile ($p = 0, $filename = '') {
        define('UPLOAD_DIR', 'assets/document/insurance/');
        $data         = base64_decode($p);
        $file_name    = $filename; // You can change it to anything
        $file         = UPLOAD_DIR . $file_name;
        $success      = file_put_contents($file, $data);
        return $file;
    }

    /**
     * GET Letter with API
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
            $this->$model->whereFields['a.is_delete'] = '0';
            $output = $this->$model->read($id);
            $response_data = array();
            for ($i=0; $i < count($output); $i++) { 
                $this->Letter_Type->whereFields['a.id'] = $output[$i]->letter_type_id;
                $this->Letter_Type->whereFields['institute_branch_id'] = $institute_branch_id;
                
                $letter_type = $this->Letter_Type->read();
                $output[$i]->letter_type_name = $letter_type[0]->name;

                // replacing placeholders content
                $this->Registration_Destination->whereFields['a.id'] = $output[$i]->registration_destination_id;
                $registration_destination = $this->Registration_Destination->read();
                $this->Registration->whereFields['a.id'] = $registration_destination[0]->registration_id;
                $registration = $this->Registration->read();
                $this->Responsible_person->whereFields['a.norm'] = $registration[0]->norm;
                $responsible_person = $this->Responsible_person->read();
                
                $fullname = $registration[0]->name;
                $witness_name = $output[$i]->witness_name;
                $tanggal_lahir = $registration[0]->birth_date;
                $alamat_pasien = $registration[0]->address;
                $no_telpon_pasien = $registration[0]->phone;
                $nama = $responsible_person[0]->name;
                $alamat = $responsible_person[0]->address_id;
                $no_telepon = $responsible_person[0]->phone;
                $date = $output[$i]->date;
                $responsible_type_name = $output[$i]->responsible_type_name;
                $doctor_in_charge_name = $output[$i]->doctor_in_charge_name;
                $content_plus = $output[$i]->content_plus;
                $sign_1 = '<img src="http://192.168.100.3:9001/'.$output[$i]->sign_1.'"/>';
                $sign_2 = '<img src="http://192.168.100.3:9001/'.$output[$i]->sign_2.'"/>';
                $sign_3 = '<img src="http://192.168.100.3:9001/'.$output[$i]->sign_3.'"/>';

                $placeholders = ["{fullname}", "{no_rm}", "{tanggal_lahir}", "{witness_name}", "{alamat_pasien}", "{no_telpon_pasien}", "{nama}", "{alamat}", "{no_telepon}", "{date}", "{doctor_in_charge_name}", "{responsible_type_name}", "{sign_1}", "{sign_2}", "{sign_3}", "{content_plus}"];
                $values = [$fullname, $no_rm, $tanggal_lahir, $witness_name, $alamat_pasien, $no_telpon_pasien, $nama, $alamat, $no_telepon, $date, $doctor_in_charge_name, $responsible_type_name, $sign_1, $sign_2, $sign_3, $content_plus];

                if ($output[$i]->sign_2) {
                    $output[$i]->content = str_replace($placeholders, $values, $output[$i]->content_html);
                }
                
            }
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

    public function death_get($p = 0){
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
            $this->$model->whereFields['a.letter_type_id'] = 'e9432634-dd6b-41cd-b57d-7c34daba9efc';
            $this->$model->whereFields['a.registration_destination_id'] = $id;
            $this->$model->whereFields['a.is_delete'] = '0';
            $output = $this->$model->read(0,false);
            $response_data = array();
            for ($i=0; $i < count($output); $i++) { 
                $this->Letter_Type->whereFields['a.id'] = $output[$i]->letter_type_id;
                $this->Letter_Type->whereFields['institute_branch_id'] = $institute_branch_id;
                
                $letter_type = $this->Letter_Type->read();
                $output[$i]->letter_type_name = $letter_type[0]->name;

                // replacing placeholders content
                $this->Registration_Destination->whereFields['a.id'] = $output[$i]->registration_destination_id;
                $registration_destination = $this->Registration_Destination->read();
                $this->Registration->whereFields['a.id'] = $registration_destination[0]->registration_id;
                $registration = $this->Registration->read();
                $this->Responsible_person->whereFields['a.norm'] = $registration[0]->norm;
                $responsible_person = $this->Responsible_person->read();
                
                $fullname = $registration[0]->name;
                $witness_name = $output[$i]->witness_name;
                $tanggal_lahir = $registration[0]->birth_date;
                $alamat_pasien = $registration[0]->address;
                $no_telpon_pasien = $registration[0]->phone;
                $nama = $responsible_person[0]->name;
                $alamat = $responsible_person[0]->address_id;
                $no_telepon = $responsible_person[0]->phone;
                $date = $output[$i]->date;
                $responsible_type_name = $output[$i]->responsible_type_name;
                $doctor_in_charge_name = $output[$i]->doctor_in_charge_name;
                $content_plus = $output[$i]->content_plus;
                $sign_1 = '<img src="http://192.168.100.3:9001/'.$output[$i]->sign_1.'"/>';
                $sign_2 = '<img src="http://192.168.100.3:9001/'.$output[$i]->sign_2.'"/>';
                $sign_3 = '<img src="http://192.168.100.3:9001/'.$output[$i]->sign_3.'"/>';

                $placeholders = ["{fullname}", "{no_rm}", "{tanggal_lahir}", "{witness_name}", "{alamat_pasien}", "{no_telpon_pasien}", "{nama}", "{alamat}", "{no_telepon}", "{date}", "{doctor_in_charge_name}", "{responsible_type_name}", "{sign_1}", "{sign_2}", "{sign_3}", "{content_plus}"];
                $values = [$fullname, $no_rm, $tanggal_lahir, $witness_name, $alamat_pasien, $no_telpon_pasien, $nama, $alamat, $no_telepon, $date, $doctor_in_charge_name, $responsible_type_name, $sign_1, $sign_2, $sign_3, $content_plus];

                if ($output[$i]->sign_2) {
                    $output[$i]->content = str_replace($placeholders, $values, $output[$i]->content_html);
                }
                
            }
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

    public function signature_update_put($p = 0) {
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

        $this->upload_signature_rules = array(
            array('field' => 'signature_1', 'label' => 'signature_1', 'rules' => 'trim|required'),
            array('field' => 'signature_2', 'label' => 'signature_2', 'rules' => 'trim|required'),
            array('field' => 'signature_3', 'label' => 'signature_3', 'rules' => 'trim|required'),
            array('field' => 'extension', 'label' => 'extension', 'rules' => 'trim|required'),
        );
    
        // Form Validation
        $this->form_validation->set_data($dt_put);
        $this->form_validation->set_rules($this->upload_signature_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        
        $letter_id = $id;

        if ($letter_id === 0 || $letter_id === "0") {
            $this->REST_Return(400, 'letter_id is required');
            return;
        }

        $select = 'a.id';
        $letter = $this->$model->select($letter_id, $select);
        if (count($letter) == 0) {
            $this->REST_Return(404, 'there is no letter with id ' . $letter_id);
            return;
        }

        $filename_1 = $letter_id . '_1_' . date('Ymd');

        // Get raw POST input
        $rawInput = file_get_contents('php://input');

        // Decode JSON to array
        $request_data = json_decode($rawInput, true);

        // Extract and clean base64 (optional: remove data URI prefix if needed)
        $base64WithPrefix_1 = $request_data['signature_1'] ?? '';
        $base64_1 = preg_replace('/^data:image\/\w+;base64,/', '', $base64WithPrefix_1);

        $extension = explode('/', $request_data['extension'])[1];
        
        $url_1 = $this->base64ToFileNew($base64_1, $filename_1 . '.' . $extension, 'assets/document/registration/patient_signature/');

        $filename_2 = $letter_id . '_2_' . date('Ymd');

        // Extract and clean base64 (optional: remove data URI prefix if needed)
        $base64WithPrefix_2 = $request_data['signature_2'] ?? '';
        $base64_2 = preg_replace('/^data:image\/\w+;base64,/', '', $base64WithPrefix_2);

        
        $url_2 = $this->base64ToFileNew($base64_2, $filename_2 . '.' . $extension, 'assets/document/registration/patient_signature/');

        $filename_3 = $letter_id . '_3_' . date('Ymd');

        // Extract and clean base64 (optional: remove data URI prefix if needed)
        $base64WithPrefix_3 = $request_data['signature_3'] ?? '';
        $base64_3 = preg_replace('/^data:image\/\w+;base64,/', '', $base64WithPrefix_3);

        
        $url_3 = $this->base64ToFileNew($base64_3, $filename_3 . '.' . $extension, 'assets/document/registration/patient_signature/');

        /* Start Transaction */
        $this->db->trans_start();

        $dt_letter = array(
            'sign_1' => $url_1,
            'sign_2' => $url_2,
            'sign_3' => $url_3,
            'content_plus'=> isset($dt_put['content_plus']) ? $dt_put['content_plus'] : $letter[0]->content_plus
        );

        $this->$model->update_v2($letter_id, $dt_letter);
       
        $this->db->trans_complete();
        /* End Transaction */
        
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_registration_destination,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to upload patient_signature', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
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
            $this->$model->whereFields['a.is_delete'] = '0';
            $output = $this->$model->read_ssr($id);
            $response_data = array();

            for ($i=0; $i < count($output->rows); $i++) { 
                $this->Letter_Type->whereFields['a.id'] = $output->rows[$i]->letter_type_id;
                $this->Letter_Type->whereFields['institute_branch_id'] = $institute_branch_id;
                
                $letter_type = $this->Letter_Type->read();
                $output->rows[$i]->letter_type_name = $letter_type[0]->name;

                // replacing placeholders content
                $this->Registration_Destination->whereFields['a.id'] = $output->rows[$i]->registration_destination_id;
                $registration_destination = $this->Registration_Destination->read();
                $this->Registration->whereFields['a.id'] = $registration_destination[0]->registration_id;
                $registration = $this->Registration->read();
                $this->Responsible_person->whereFields['a.norm'] = $registration[0]->norm;
                $responsible_person = $this->Responsible_person->read();
                
                $nama_pasien = $registration[0]->name;
                $no_rm = $registration[0]->norm;
                $tanggal_lahir = $registration[0]->birth_date;
                $alamat_pasien = $registration[0]->address;
                $no_telpon_pasien = $registration[0]->phone;
                $nama = $responsible_person[0]->name;
                $alamat = $responsible_person[0]->address_id;
                $no_telepon = $responsible_person[0]->phone;

                $placeholders = ["{nama_pasien}", "{no_rm}", "{tanggal_lahir}", "{alamat_pasien}", "{no_telpon_pasien}", "{nama}", "{alamat}", "{no_telepon}"];
                $values = [$nama_pasien, $no_rm, $tanggal_lahir, $alamat_pasien, $no_telpon_pasien, $nama, $alamat, $no_telepon];

                $output->rows[$i]->content = str_replace($placeholders, $values, $output->rows[$i]->content);
                
            }
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

    /**
     * Letter API
     * --------------------
     * @param: nurse_id
     * @param: been_hospitalized_type_id
     * @param: been_hospitalized_answer
     * @param: any_history_family_disease_type_id
     * @param: any_history_family_answer_arry
     * @param: any_history_family_answer
     * @param: any_surgery
     * @param: any_surgery_date
     * @param: complication
     * @param: history_psychological_arry
     * --------------------
     * @method : POST
     * @link: version/mr/letter
     **/

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
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            # Form Validation
            $this->form_validation->set_data($dt_put);
            $this->form_validation->set_rules('no', 'no', 'trim|max_length[100]');
            $this->form_validation->set_rules('letter_type_id', 'letter_type_id', 'trim|max_length[36]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                $letter = $this->$model->read($id); 
                // print_r($letter);
                // die();
                if($letter != []) {
                    $dt_letter = array(
                        'id'            => $id,
                        'no'          => isset($dt_put['no']) ? $dt_put['no'] : $letter[0]->no,
                        'letter_type_id'=> isset($dt_put['letter_type_id']) ? $dt_put['letter_type_id'] : $letter[0]->letter_type_id,
                        'selected_doctor_id'=> isset($dt_put['selected_doctor_id']) ? $dt_put['selected_doctor_id'] : $letter[0]->selected_doctor_id,
                        'witness_name'=> isset($dt_put['witness_name']) ? $dt_put['witness_name'] : $letter[0]->witness_name,
                        'responsible_id'=> isset($dt_put['responsible_id']) ? $dt_put['responsible_id'] : $letter[0]->responsible_id,
                        'responsible_name'=> isset($dt_put['responsible_name']) ? $dt_put['responsible_name'] : $letter[0]->responsible_name,
                        'content_plus'=> isset($dt_put['content_plus']) ? $dt_put['content_plus'] : $letter[0]->content_plus
                    );
                    $id = $this->$model->update($id, $dt_letter);
                } else {
                    $this->REST_Return(404, 'Letter with id ' . $id . ' does not exist!');
                    return;
                }
                $this->db->trans_complete();
                /* End Transaction */
                if ($this->db->trans_status() === false) {
                    $response_data = array();
                    $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                } else {
                    $response_data = array(
                        'id'     => $p
                    );
                    $this->REST_Return(200, 'SUCCESS', $response_data);
                }
                return;     
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }
}