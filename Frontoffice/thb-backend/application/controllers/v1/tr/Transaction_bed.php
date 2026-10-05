<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Transaction_bed extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/mr/Registration_destination_model", "Registration_destination");
        $this->load->model("v1/health_facility/Bed_model", "Bed");

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
    }

    /**
     * Transaction_bed API
     * --------------------
     * @param: registration_destination_id
     * @param: bed_id
     * @param: var_1
     * @param: var_2
     * @param: var_3
     * @param: notes
     * --------------------
     * @method : POST
     * @link: version/tr/transaction_bed
     **/

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
            $this->form_validation->set_rules('bed_id', 'bed_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('var_1', 'var_1', 'trim|max_length[8]');
            $this->form_validation->set_rules('var_2', 'var_2', 'trim|max_length[8]');
            $this->form_validation->set_rules('var_3', 'var_3', 'trim|max_length[8]');
            $this->form_validation->set_rules('notes', 'notes', 'trim|max_length[100]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                // Check registration_destination_id and bed_id
                $dt_bed = $this->Bed->read($dt_post['bed_id'], false);
                if($this->Registration_destination->read($dt_post['registration_destination_id'], false) != [] && $dt_bed != []) {
                    $model = $this->name;
                    /* Start Transaction */
                    $this->db->trans_start();
                    $t = time();
                    $dt_transaction_bed = array(
                        'registration_destination_id'   => $dt_post['registration_destination_id'],
                        'bed_id'                        => $dt_post['bed_id'],
                        'bed_name'                      => $dt_bed[0]->name,
                        'var_1'                         => isset($dt_post['var_1']) ? $dt_post['var_1'] : '',
                        'var_2'                         => isset($dt_post['var_2']) ? $dt_post['var_2'] : '',
                        'var_3'                         => isset($dt_post['var_3']) ? $dt_post['var_3'] : '',
                        'notes'                         => isset($dt_post['notes']) ? $dt_post['notes'] : '',
                        'date_added'                    => date("Y-m-d H:i:s",$t)
                    );
                    $id = $this->$model->create($dt_transaction_bed);
                    $this->db->trans_complete();
                    /* End Transaction */
                    if ($this->db->trans_status() === false) {
                        $response_data = $this->db->trans_status();
                        $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                    } else {
                        $response_data = array(
                            'id'     => $id
                        );
                        $this->REST_Return(200, 'SUCCESS', $response_data);
                    }
                } else {
                    $this->REST_Return(404, 'Wrong registration_destination_id or bed_id!');
                }
                return;     
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    /**
     * GET Transaction_bed with API
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
            if (isset($q_strings['registration_id'])) {
                $this->$model->whereFields['rd.registration_id'] = $q_strings['registration_id'];
            }
            $this->$model->whereFields['a.is_delete'] = '0';
            $output = $this->$model->read($id);
            if (count($output) > 0) {
                for ($i=0; $i < count($output); $i++) {
                    // Total hours
                    // $checkin   = $output[$i]->checkin_date;
                    // $checkout  = $output[$i]->checkout_date;
                    // $start     = new DateTime($checkin);
                    // $end       = new DateTime($checkout);
                    // $output[$i]->checkin_date  = $checkin;
                    // $output[$i]->checkout_date = $checkout;

                    // $diff = $end->diff($start);
                    // $totalHours =  $diff->h + ($diff->days * 24) + ($diff->m > 30 ? 1 : 0);
                    
                    // // count prorate room
                    // $cost_prorate = $output[$i]->is_non_prorate == 0 ? $output[$i]->var_1 / 24 : $output[$i]->var_1;
                    // $var_2        = $cost_prorate * $totalHours;

                    // // count prorate room
                    // $output[$i]->var_2 = $output[$i]->var_2 == null && $output[$i]->var_2 == '' && $output[$i]->var_2 == 0 ? $totalHours : $output[$i]->var_2;
                    // $output[$i]->var_1 = $output[$i]->var_1 == null && $output[$i]->var_1 == '' && $output[$i]->var_1 == 0 ? $cost_prorate : $cost_prorate;
                    // $output[$i]->cost_prorate = $cost_prorate;

                    // Calculate costs
                        $checkin   = $output[$i]->checkin_date;
                        $checkout  = $output[$i]->checkout_date;
                        $start     = new DateTime($checkin);
                        $end       = new DateTime($checkout);
                        $output[$i]->checkin_date  = $checkin;
                        $output[$i]->checkout_date = $checkout;

                        // Hitung selisih waktu
                        $diff = $end->diff($start);

                        // Ambil selisih hari dasar
                        $totalDays = $diff->days;

                        // Jika ada sisa jam/menit, tambahkan 1 hari
                        if ($diff->h > 0 || $diff->i > 0) {
                            $totalDays += 1;
                        }

                        // Jika total hari 0, minimal tetap 1 hari
                        if ($totalDays == 0) {
                            $totalDays = 1;
                        }

                        // Hitung total harga
                        $cost_prorate = $output[$i]->is_non_prorate == 0 ? $output[$i]->var_1 : $output[$i]->var_1;
                        $var_2        = $cost_prorate * $totalDays;

                        $total_price = $price * $totalDays;
                        $output[$i]->var_2 = $output[$i]->var_2 == null && $output[$i]->var_2 == '' && $output[$i]->var_2 == 0 ? $totalDays : $output[$i]->var_2;
                        $output[$i]->var_1 = $output[$i]->var_1 == null && $output[$i]->var_1 == '' && $output[$i]->var_1 == 0 ? $cost_prorate : $cost_prorate;
                        $output[$i]->cost_prorate = $cost_prorate;
                    // Calculate costs
                }
            }

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
            $this->$model->whereFields['a.is_delete'] = '0';
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


    /**
     * Transaction_bed API
     * --------------------
     * @param: bed_id
     * @param: var_1
     * @param: var_2
     * @param: var_3
     * @param: notes
     * --------------------
     * @method : POST
     * @link: version/tr/transaction_bed
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
            $this->form_validation->set_rules('costing_id', 'costing_id', 'trim|required|max_length[36]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                $dt_bed = $this->Bed->read($dt_put['bed_id'],false);
                if($dt_bed != []) {
                    $tr_bed = $this->$model->read($id);
                    if($tr_bed != []) {
                        $dt_transaction_bed = array(
                            'id'            => $id,
                            'costing_id'    => $dt_put['costing_id'],
                            // 'bed_name'      => $dt_bed[0]->name,
                            // 'var_1'         => isset($dt_put['var_1']) ? $dt_put['var_1'] : $tr_bed[0]->var_1,
                            // 'var_2'         => isset($dt_put['var_2']) ? $dt_put['var_2'] : $tr_bed[0]->var_2,
                            // 'var_3'         => isset($dt_put['var_3']) ? $dt_put['var_3'] : $tr_bed[0]->var_3,
                            // 'notes'         => isset($dt_put['notes']) ? $dt_put['notes'] : $tr_bed[0]->notes,
                        );
                        $id = $this->$model->update($id, $dt_transaction_bed);
                    } else {
                        $this->REST_Return(404, 'Transaction_bed with id ' . $id . ' does not exist!');
                        return;
                    }
                } else {
                    $this->REST_Return(404, 'Wrong bed_id!');
                    return;
                }
                $this->db->trans_complete();
                /* End Transaction */
                if ($this->db->trans_status() === false) {
                    $response_data = array();
                    $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                } else {
                    $response_data = $id;
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