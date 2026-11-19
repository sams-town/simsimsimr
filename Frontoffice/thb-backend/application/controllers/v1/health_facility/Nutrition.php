<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Nutrition extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/mr/Soap_model", "Soap");
        // P1-2: model untuk validasi kolom aktual (nutrition tidak punya soap_id/diet_type_id/diet/explanation)
        $this->load->model("v1/mr/Registration_destination_model", "Registration_destination");
        $this->load->model("v1/health_facility/Room_model", "Room");
        $this->load->model("v1/hr/Employee_model", "Employee");
        $this->load->model("v1/setting/Health_facility_item_model", "Health_facility_item");
        $this->load->model("v1/mr/Diet_model", "Diet");
        $this->load->model("v1/setting/User_model", "User");

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
    }

    /**
     * P1-2: kolom nyata tabel `nutrition` (information_schema):
     *   id, registration_destination_id, health_facility_item_id, date, employee_nurse_id,
     *   room_id, diet_id, time_type_id, delivery_time, notes, status, employee_approved_id,
     *   approved_date, user_added, user_updated, date_added, date_updated, is_delete
     * Kolom lama yang dipakai controller ini (soap_id, diet_type_id, diet, explanation)
     * TIDAK ADA -> MySQL 1054. Tidak ada kolom baru yang dibuat.
     */

    /** Alias lama -> kolom nyata. Hanya pemetaan yang maknanya terbukti. */
    protected function _p12_notes($dt) {
        if (isset($dt['notes']) && $dt['notes'] !== '') return $dt['notes'];
        // explanation = teks bebas penjelasan diet; satu-satunya kolom teks bebas = notes
        return isset($dt['explanation']) ? $dt['explanation'] : '';
    }

    /** registration_destination_id dari payload, atau diturunkan dari soap_id (soap.registration_destination_id). */
    protected function _p12_rd_id($dt) {
        if (isset($dt['registration_destination_id']) && $dt['registration_destination_id'] !== '') {
            return $dt['registration_destination_id'];
        }
        if (isset($dt['soap_id']) && $dt['soap_id'] !== '') {
            $soap = $this->Soap->read($dt['soap_id'], false);
            if ($soap != [] && isset($soap[0]->registration_destination_id)) {
                return $soap[0]->registration_destination_id;
            }
        }
        return '';
    }

    /** Kolom NOT NULL yang wajib ada di payload; tanpa nilai tebakan. */
    protected function _p12_missing($row, $fields) {
        $missing = array();
        foreach ($fields as $f) {
            if (!isset($row[$f]) || $row[$f] === '' || $row[$f] === null) $missing[] = $f;
        }
        return $missing;
    }

    /**
     * notes = varchar(250). Koneksi CI3 memakai stricton=FALSE, jadi MySQL MEMOTONG
     * diam-diam (bukan error). Ditolak di sini supaya tidak ada data hilang tanpa pesan.
     */
    protected function _p12_notes_ok($notes) {
        return mb_strlen((string) $notes, 'UTF-8') <= 250;
    }

    /** Tanggal harus Y-m-d yang benar-benar ada. '9999-99-99' diterima MySQL jadi 0000-00-00. */
    protected function _p12_date_ok($date) {
        if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', (string) $date, $m)) return false;
        return checkdate((int) $m[2], (int) $m[3], (int) $m[1]);
    }

    /** Validasi keberadaan + scope cabang lewat room.institute_branch_id (nutrition tidak punya kolom cabang). */
    protected function _p12_check_refs($row, $institute_branch_id) {
        $rd = $this->Registration_destination->select($row['registration_destination_id'], 'a.id');
        if (count($rd) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $row['registration_destination_id'], array(), 'REGISTRATION_DESTINATION_NOT_FOUND');
            return false;
        }
        if (count($this->Health_facility_item->select($row['health_facility_item_id'], 'a.id')) == 0) {
            $this->REST_Return(404, 'there is no health_facility_item with id ' . $row['health_facility_item_id'], array(), 'HEALTH_FACILITY_ITEM_NOT_FOUND');
            return false;
        }
        if (count($this->Employee->select($row['employee_nurse_id'], 'a.id')) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $row['employee_nurse_id'], array(), 'EMPLOYEE_NOT_FOUND');
            return false;
        }
        $room = $this->Room->select($row['room_id'], 'a.id, a.institute_branch_id');
        if (count($room) == 0) {
            $this->REST_Return(404, 'there is no room with id ' . $row['room_id'], array(), 'ROOM_NOT_FOUND');
            return false;
        }
        if ((string) $room[0]->institute_branch_id !== (string) $institute_branch_id) {
            $this->REST_Return(403, 'Anda tidak berwenang melakukan operasi ini.', array(), 'NUTRITION_BRANCH_FORBIDDEN');
            return false;
        }
        if (count($this->Diet->select($row['diet_id'], 'a.id')) == 0) {
            $this->REST_Return(404, 'there is no diet with id ' . $row['diet_id'], array(), 'DIET_NOT_FOUND');
            return false;
        }
        return true;
    }

    /**
     * Nutrition API
     * --------------------
     * @param: soap_id
     * @param: diet_type_id
     * @param: diet
     * @param: explanation
     * --------------------
     * @method : POST
     * @link: version/health_facility/nutrition
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
            // P1-2: soap_id bukan kolom nutrition; wajib salah satu dari
            // registration_destination_id / soap_id (soap diturunkan ke registration_destination_id).
            $this->form_validation->set_rules('registration_destination_id', 'registration_destination_id', 'trim|max_length[36]');
            $this->form_validation->set_rules('soap_id', 'soap_id', 'trim|max_length[36]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $user_id = $this->security->xss_clean($is_valid_token['data']->id);
                $model = $this->name;

                // P1-2: bangun baris dari KOLOM AKTUAL (tanpa menebak nilai wajib).
                $row = array(
                    'registration_destination_id' => $this->_p12_rd_id($dt_post),
                    'health_facility_item_id'     => isset($dt_post['health_facility_item_id']) ? $dt_post['health_facility_item_id'] : '',
                    'employee_nurse_id'           => isset($dt_post['employee_nurse_id']) ? $dt_post['employee_nurse_id'] : '',
                    'room_id'                     => isset($dt_post['room_id']) ? $dt_post['room_id'] : '',
                    'diet_id'                     => isset($dt_post['diet_id']) ? $dt_post['diet_id'] : '',
                    'date'                        => isset($dt_post['date']) ? $dt_post['date'] : '',
                );
                $missing = $this->_p12_missing($row, array('registration_destination_id', 'health_facility_item_id',
                                                           'employee_nurse_id', 'room_id', 'diet_id', 'date'));
                if ($missing != array()) {
                    $this->REST_Return(422, 'Field wajib belum lengkap.', array('missing' => $missing), 'NUTRITION_REQUIRED_FIELDS');
                    return;
                }
                if (!$this->_p12_date_ok($row['date'])) {
                    $this->REST_Return(422, 'Format tanggal tidak valid (Y-m-d).', array('date' => $row['date']), 'INVALID_DATE');
                    return;
                }
                if (!$this->_p12_notes_ok($this->_p12_notes($dt_post))) {
                    $this->REST_Return(422, 'Catatan maksimal 250 karakter.', array('max_length' => 250), 'NOTES_TOO_LONG');
                    return;
                }
                if (!$this->_p12_check_refs($row, $institute_branch_id)) {
                    return;
                }

                $dt_nutrition = array(
                    'registration_destination_id' => $row['registration_destination_id'],
                    'health_facility_item_id'     => $row['health_facility_item_id'],
                    'employee_nurse_id'           => $row['employee_nurse_id'],
                    'room_id'                     => $row['room_id'],
                    'diet_id'                     => $row['diet_id'],
                    'date'                        => $row['date'],
                    'time_type_id'                => isset($dt_post['time_type_id']) && $dt_post['time_type_id'] !== '' ? $dt_post['time_type_id'] : null,
                    'delivery_time'               => isset($dt_post['delivery_time']) && $dt_post['delivery_time'] !== '' ? $dt_post['delivery_time'] : null,
                    'notes'                       => $this->_p12_notes($dt_post),
                    'status'                      => 0,
                    'user_added'                  => $user_id,
                    'date_added'                  => date('Y-m-d H:i:s',time()),
                    'date_updated'                => date('Y-m-d H:i:s',time()),
                );

                /* Start Transaction */
                $this->db->trans_start();
                $id = $this->$model->create($dt_nutrition);
                $this->db->trans_complete();
                /* End Transaction */
                if ($this->db->trans_status() === false || !$id) {
                    $response_data = array();
                    $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                } else {
                    $response_data = array(
                        'id'     => $id
                    );
                    $this->REST_Return(201, 'SUCCESS', $response_data);
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
     * GET Nutrient with API
     * -------------------------
     * @method: GET
     */

     public function detail_get($p = 0) {
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
     * Nutrition API
     * --------------------
     * @param: soap_id
     * @param: diet_type_id
     * @param: diet
     * @param: explanation
     * --------------------
     * @method : POST
     * @link: version/mr/nutrition
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
            // P1-2: validasi memakai field aktual, bukan diet_type_id/diet/explanation.
            $this->form_validation->set_rules('registration_destination_id', 'registration_destination_id', 'trim|max_length[36]');
            $this->form_validation->set_rules('soap_id', 'soap_id', 'trim|max_length[36]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $user_id = $this->security->xss_clean($is_valid_token['data']->id);
                $model = $this->name;

                $current = $this->$model->read($id, false);
                if ($current == []) {
                    $this->REST_Return(404, 'Nutrition with id ' . $id . ' does not exist!', array(), 'NUTRITION_NOT_FOUND');
                    return;
                }

                // P1-2: field yang tidak dikirim tetap memakai nilai tersimpan (tidak menebak).
                $row = array(
                    'registration_destination_id' => $this->_p12_rd_id($dt_put) !== '' ? $this->_p12_rd_id($dt_put) : $current[0]->registration_destination_id,
                    'health_facility_item_id'     => isset($dt_put['health_facility_item_id']) && $dt_put['health_facility_item_id'] !== '' ? $dt_put['health_facility_item_id'] : $current[0]->health_facility_item_id,
                    'employee_nurse_id'           => isset($dt_put['employee_nurse_id']) && $dt_put['employee_nurse_id'] !== '' ? $dt_put['employee_nurse_id'] : $current[0]->employee_nurse_id,
                    'room_id'                     => isset($dt_put['room_id']) && $dt_put['room_id'] !== '' ? $dt_put['room_id'] : $current[0]->room_id,
                    'diet_id'                     => isset($dt_put['diet_id']) && $dt_put['diet_id'] !== '' ? $dt_put['diet_id'] : $current[0]->diet_id,
                    'date'                        => isset($dt_put['date']) && $dt_put['date'] !== '' ? $dt_put['date'] : $current[0]->date,
                );
                $missing = $this->_p12_missing($row, array('registration_destination_id', 'health_facility_item_id',
                                                           'employee_nurse_id', 'room_id', 'diet_id', 'date'));
                if ($missing != array()) {
                    $this->REST_Return(422, 'Field wajib belum lengkap.', array('missing' => $missing), 'NUTRITION_REQUIRED_FIELDS');
                    return;
                }
                if (!$this->_p12_date_ok($row['date'])) {
                    $this->REST_Return(422, 'Format tanggal tidak valid (Y-m-d).', array('date' => $row['date']), 'INVALID_DATE');
                    return;
                }
                if (!$this->_p12_notes_ok($this->_p12_notes($dt_put))) {
                    $this->REST_Return(422, 'Catatan maksimal 250 karakter.', array('max_length' => 250), 'NOTES_TOO_LONG');
                    return;
                }
                if (!$this->_p12_check_refs($row, $institute_branch_id)) {
                    return;
                }

                $status = isset($dt_put['status']) ? $dt_put['status'] : $current[0]->status;

                $dt_nutrition = array(
                    'id'                          => $id,
                    'registration_destination_id' => $row['registration_destination_id'],
                    'health_facility_item_id'     => $row['health_facility_item_id'],
                    'employee_nurse_id'           => $row['employee_nurse_id'],
                    'room_id'                     => $row['room_id'],
                    'diet_id'                     => $row['diet_id'],
                    'date'                        => $row['date'],
                    'time_type_id'                => array_key_exists('time_type_id', $dt_put) ? $dt_put['time_type_id'] : $current[0]->time_type_id,
                    'delivery_time'               => array_key_exists('delivery_time', $dt_put) ? $dt_put['delivery_time'] : $current[0]->delivery_time,
                    'notes'                       => array_key_exists('notes', $dt_put) || array_key_exists('explanation', $dt_put) ? $this->_p12_notes($dt_put) : $current[0]->notes,
                    'status'                      => $status,
                    'user_updated'                => $user_id,
                    'date_updated'                => date('Y-m-d H:i:s',time()),
                );

                // Approval hanya bila kolomnya tersedia (employee_approved_id, approved_date) dan status = 1.
                if ((string) $status === '1') {
                    $user = $this->User->select($user_id, 'a.id, a.employee_id');
                    $dt_nutrition['employee_approved_id'] = (count($user) > 0) ? $user[0]->employee_id : null;
                    $dt_nutrition['approved_date']        = date('Y-m-d H:i:s',time());
                }

                /* Start Transaction */
                $this->db->trans_start();
                $this->$model->update($id, $dt_nutrition);
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