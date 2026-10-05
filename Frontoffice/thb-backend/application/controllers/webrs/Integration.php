<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Integration extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("v1/hr/Employee_model", "Employee");
        $this->load->model("v1/mr/Patient_model", "Patient");
    }

    /**
     * GET Dokter
     * -------------------------
     * @method: GET
    */

    public function dokter_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;
    
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response = array(
                'status' => 401,
                'message' => 'invalid token'
            );

            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
            Logger::info("[$this->debug_uniq]" . ' response: ', $response);
            return;
        } 

        $select = 'a.id, a.fullname, a.phone_number, a.occupation, a.sex, a.sip, a.address';
        $where = array();
        $join = array();
        $order = array('a.name' => 'ASC');
        $this->Employee->limit = $q_strings['limit'];
        $this->Employee->offset = $q_strings['offset'];
        $employee = $this->Employee->select(0, $select, $where, $join, $order);

        $total_employee = count($employee);

        $output = array();

        for ($i=0; $i < $total_employee; $i++) { 
            $id = $employee[$i]->id;
            $nama_lengkap = $employee[$i]->fullname; 
            $no_hp = $employee[$i]->phone_number; 
            $spesialisasi = $employee[$i]->occupation; 
            $jenis_kelamin = ($employee[$i]->sex == 0) ? 'P' : 'L'; 
            $no_str = $employee[$i]->sip; // NOTE - REQUIRED
            $alamat = $employee[$i]->address; 
            $profil_lengkap = ''; 
            $sip_url = ''; // NOTE - REQUIRED
            $biaya_konsultasi = ''; // NOTE - REQUIRED
            $pendidikan = ''; 
            $pengalaman = ''; 

            $data = array(
                'id' => $id,
                'nama_lengkap' => $nama_lengkap,
                'no_hp' => $no_hp,
                'spesialisasi' => $spesialisasi,
                'jenis_kelamin' => $jenis_kelamin,
                'no_str' => $no_str,
                'alamat' => $alamat,
                'profil_lengkap' => $profil_lengkap,
                'sip_url' => $sip_url,
                'biaya_konsultasi' => $biaya_konsultasi,
                'pendidikan' => $pendidikan,
                'pengalaman' => $pengalaman,
            );

            array_push($output, $data);
        }
            
        $response = array(
            'status' => 200,
            'total' => $total_employee,
            'data' => $output
        );
    
        $this->response($response, REST_Controller::HTTP_OK);
        Logger::info("[$this->debug_uniq]" . ' response: ', $response);
        return;
    }

    /**
     * GET Pasien
     * -------------------------
     * @method: GET
    */

    public function pasien_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;
    
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response = array(
                'status' => 401,
                'message' => 'invalid token'
            );

            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
            Logger::info("[$this->debug_uniq]" . ' response: ', $response);
            return;
        } 

        $select = 'a.norm, a.email, a.photo, a.fullname, a.phone, a.birth_date, a.sex, a.address';
        $where = array();
        $join = array();
        $order = array('a.name' => 'ASC');
        $this->Patient->limit = $q_strings['limit'];
        $this->Patient->offset = $q_strings['offset'];
        $patient = $this->Patient->select(0, $select, $where, $join, $order);

        $total_patient = count($patient);

        $output = array();

        for ($i=0; $i < $total_patient; $i++) { 
            $no_rekam_medis = $patient[$i]->norm; 
            $email = $patient[$i]->email; 
            $foto_url = $patient[$i]->photo; 
            $nama_lengkap = trim($patient[$i]->fullname); 
            $penjamin_asuransi = '';
            $perusahaan = ''; 
            $no_hp = $patient[$i]->phone;
            $tanggal_lahir = $patient[$i]->birth_date;
            $jenis_kelamin = ($patient[$i]->sex == 0) ? 'Perempuan' : 'Laki-laki';
            $ringkasan_riwayat_kesehatan = ''; 
            $alamat = $patient[$i]->address; 

            $data = array(
                'no_rekam_medis' => $no_rekam_medis,
                'email' => $email,
                'foto_url' => $foto_url,
                'nama_lengkap' => $nama_lengkap,
                'penjamin_asuransi' => $penjamin_asuransi,
                'perusahaan' => $perusahaan,
                'no_hp' => $no_hp,
                'tanggal_lahir' => $tanggal_lahir,
                'jenis_kelamin' => $jenis_kelamin,
                'ringkasan_riwayat_kesehatan' => $ringkasan_riwayat_kesehatan,
                'alamat' => $alamat,
            );

            array_push($output, $data);
        }
            
        $response = array(
            'status' => 200,
            'total' => $total_patient,
            'data' => $output
        );
    
        $this->response($response, REST_Controller::HTTP_OK);
        Logger::info("[$this->debug_uniq]" . ' response: ', $response);
        return;
    }

    /**
     * POST Submit Janji Temu
     * -------------------------
     * @method: POST
    */

    public function submit_janji_temu_post() {
        // CALL JSON POST
        $data       = $this->json_post();
        // DEFINE ATTR
        $dt_post    = $data['dt_postput'];
        $model      = $this->name;
    
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response = array(
                'status' => 401,
                'message' => 'invalid token'
            );

            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
            Logger::info("[$this->debug_uniq]" . ' response: ', $response);
            return;
        } 
    
        // Form Validation
        $this->form_validation->set_data($dt_post);
        $this->form_validation->set_rules($this->create_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response = array(
                'status' => 400,
                'data' => $this->form_validation->error_array()
            );

            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
            Logger::info("[$this->debug_uniq]" . ' response: ', $response);
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_janji_temu = array();
    
        $id = $this->$model->create($dt_janji_temu);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response = array(
                'error'     => $this->db->error(),
                'data'      => $dt_janji_temu,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'gagal ketika mencoba membuat janji_temu', $response);
            return;
        }
    
        $response = array(
            'id'     => $id
        );
    
        $this->response($response, REST_Controller::HTTP_OK);
        Logger::info("[$this->debug_uniq]" . ' response: ', $response);
        return;   
    }
}
    