<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

class Citr extends My_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('v1/mr/citr/Citr_model');
        // $this->load->model('v1/mr/citr/CitrEdukasiLanjutan_model');
        // $this->load->model('v1/mr/citr/CitrPersiapanEdukasi_model');
        $this->load->library('Authorization_Token');
        $this->load->helper('url');
    }

    /**
     * Create a new citr record along with its citr records.
     */
    public function create_post()
    {
        $data = $this->json_post();
        $dt_post = $data['dt_postput'];

        // Check if any value is empty and set it to null
        $dt_post = array_map(function ($value) {
            return empty($value) ? null : $value;
        }, $dt_post);

        $is_valid_token = $this->authorization_token->validateToken();
        if (empty($is_valid_token) or $is_valid_token['status'] === FALSE) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'], $response_data);
            return;
        }

        $this->form_validation->set_rules('register_destination_id', 'register_destination_id', 'trim|required|max_length[36]');
        if ($this->form_validation->run() == FALSE) {
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Oops, something went wrong, Please try again later.', $response_data);
            return;
        }

        $this->db->trans_start();

        try {
            $data = array(
                'register_destination_id' => $dt_post['register_destination_id'],
                'doctor_id' => $dt_post['doctor_id'],
                'nurse_id' => $dt_post['nurse_id'],
                'created_at' => date('Y-m-d H:i:s'),
                //persiapan edukasi
                'bahasa' => $dt_post['bahasa'] ?? null,
                'kebutuhan_penerjemah' => $dt_post['kebutuhan_penerjemah'] ?? null,
                'pendidikan_pasien' => $dt_post['pendidikan_pasien'] ?? null,
                'baca_dan_tulis' => $dt_post['baca_dan_tulis'] ?? null,
                'hambatan_edukasi' => $dt_post['hambatan_edukasi'] ?? null,
                //edukasi lanjutan
                'tanggal_pukul' => $dt_post['tanggal_pukul'] ?? null,
                'profesi_bagian' => $dt_post['profesi_bagian'] ?? null,
                'informasi_diberikan' => $dt_post['informasi_diberikan'] ?? null,
                'metode_edukasi' => $dt_post['metode_edukasi'] ?? null,
                'nama_pemberi_informasi' => $dt_post['nama_pemberi_informasi'] ?? null,
                'tanda_tangan_pemberi_informasi' => $dt_post['tanda_tangan_pemberi_informasi'] ?? null,
                'nama_penerima_informasi' => $dt_post['nama_penerima_informasi'] ?? null,
                'tanda_tangan_penerima_informasi' => $dt_post['tanda_tangan_penerima_informasi'] ?? null,
                'hubungan_dengan_pasien_penerima_informasi' => $dt_post['hubungan_dengan_pasien_penerima_informasi'] ?? null,
                'evaluasi' => $dt_post['evaluasi'] ?? null,
                'tanggal_pukul_reedukasi' => $dt_post['tanggal_pukul_reedukasi'] ?? null,
            );

            // Insert the main citr record
            // $id = $this->Citr_model->create($data); (deprecated)
            $data = $this->Citr_model->create($data);
            // Insert citr records
            // $this->insert_or_update_citr_tables($id, $dt_post);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->REST_Return(422, 'Oops, something went wrong, data rollback.', []);
                return;
            }
            // $response_data = array('id' => $id);
            $response_data = $data;
            $this->REST_Return(200, 'SUCCESS', $response_data);
        } catch (\Exception $e) {
            $this->REST_Return(422, 'Oops, something went wrong, execution error.', ["message" => $e->getMessage()]);
        }
        return;
    }

    /**
     * (deprecated) begin Handle insertion or updating of citr records based on citr ID.
     */
    // private function insert_or_update_citr_tables($citr_id, $dt_post)
    // {
    //     // Execute only if the data exists in dt_post
    //     if (isset($dt_post['citr_persiapan_edukasi'])) {
    //         $this->insert_or_update_citr_persiapan_edukasi($citr_id, $dt_post['citr_persiapan_edukasi']);
    //     }
    //     if (isset($dt_post['citr_edukasi_lanjutan'])) {
    //         $this->insert_or_update_citr_edukasi_lanjutan($citr_id, $dt_post['citr_edukasi_lanjutan']);
    //     }
    // }

    // private function insert_or_update_citr_persiapan_edukasi($citr_id, $data)
    // {
    //     $citr_persiapan_edukasi = array(
    //         'citr_id' => $citr_id,
    //         'bahasa' => $data['bahasa'] ?? null,
    //         'kebutuhan_penerjemah' => $data['kebutuhan_penerjemah'] ?? null,
    //         'pendidikan_pasien' => $data['pendidikan_pasien'] ?? null,
    //         'baca_dan_tulis' => $data['baca_dan_tulis'] ?? null,
    //         'hambatan_edukasi' => $data['hambatan_edukasi'] ?? null,
    //     );

    //     $this->upsert_citr('CitrPersiapanEdukasi_model', $citr_id, $citr_persiapan_edukasi);
    // }

    // // Separate methods for each citr object
    // private function insert_or_update_citr_edukasi_lanjutan($citr_id, $data)
    // {
    //     $citr_edukasi_lanjutan = array(
    //         'citr_id' => $citr_id,
    //         'tanggal_pukul' => $data['tanggal_pukul'] ?? null,
    //         'profesi_bagian' => $data['profesi_bagian'] ?? null,
    //         'informasi_diberikan' => $data['informasi_diberikan'] ?? null,
    //         'metode_edukasi' => $data['metode_edukasi'] ?? null,
    //         'nama_pemberi_informasi' => $data['nama_pemberi_informasi'] ?? null,
    //         'tanda_tangan_pemberi_informasi' => $data['tanda_tangan_pemberi_informasi'] ?? null,
    //         'nama_penerima_informasi' => $data['nama_penerima_informasi'] ?? null,
    //         'tanda_tangan_penerima_informasi' => $data['tanda_tangan_penerima_informasi'] ?? null,
    //         'hubungan_dengan_pasien_penerima_informasi' => $data['hubungan_dengan_pasien_penerima_informasi'] ?? null,
    //         'evaluasi' => $data['evaluasi'] ?? null,
    //         'tanggal_pukul_reedukasi' => $data['tanggal_pukul_reedukasi'] ?? null,
    //     );
    //     $this->upsert_citr('CitrEdukasiLanjutan_model', $citr_id, $citr_edukasi_lanjutan);
    // }

    // private function upsert_citr($model, $citr_id, $data)
    // {
    //     $existing = $this->$model->get_by_citr_id($citr_id);
    //     if (!empty($existing)) {
    //         $this->$model->update_by_citr_id($existing[0]->id, $data);
    //     } else {
    //         $this->$model->create($data);
    //     }
    // }

    /**
     * end Handle insertion or updating of citr records based on citr ID.
     */
    public function detailCitr_get($id)
    {
        // Fetch the citr record by ID
        $citr = $this->Citr_model->get_by_registration_id($id);
        if (!$citr) {
            $this->REST_Return(404, 'Citr record not found', []);
            return;
        }
        $citr_id = $citr->id;
        $citr->citr_persiapan_edukasi = $this->CitrPersiapanEdukasi_model->get_by_citr_id($citr_id);
        $citr->citr_edukasi_lanjutan = $this->CitrEdukasiLanjutan_model->get_by_citr_id($citr_id);

        $this->REST_Return(200, 'SUCCESS', $citr);
    }

    public function list_get($p = 0)
    {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'], $q_strings);
        $id     = $this->security->xss_clean($p);

        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) and $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'], $response_data);
            return;
        }

        if (isset($q_strings['limit'])) $this->Citr_model->limit = $q_strings['limit'];
        unset($q_strings['limit']);
        if (isset($q_strings['offset'])) $this->Citr_model->offset = $q_strings['offset'];
        unset($q_strings['offset']);

        $this->Citr_model->whereFields = $q_strings;
        $output = $this->Citr_model->read_ssr($id);

        $response_data = $output;
        $this->REST_Return(200, 'Success', $response_data);
        return;
    }

    public function update_put($p = 0)
    {
        $data       = $this->json_put();
        $dt_post = $data['dt_postput'];
        $id         = $this->security->xss_clean($p);
        // Check if any value is empty and set it to null
        $dt_post = array_map(function ($value) {
            return empty($value) ? null : $value;
        }, $dt_post);

        $is_valid_token = $this->authorization_token->validateToken();
        if (empty($is_valid_token) or $is_valid_token['status'] === FALSE) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'], $response_data);
            return;
        }

        $citr = $this->Citr_model->get_by_registration_id($id);
        if (!$citr) {
            $this->REST_Return(404, 'Citr record not found', []);
            return;
        }

        $data = array(
            'register_destination_id' => $dt_post['register_destination_id'],
            'doctor_id' => $dt_post['doctor_id'],
            'nurse_id' => $dt_post['nurse_id'],
            'updated_at' => date('Y-m-d H:i:s')
        );

        $citr_id = $citr->id;

        $this->db->trans_start();
        $this->Citr_model->update_by_id($citr_id, $data);
        try {
            // Insert citr records
            $this->insert_or_update_citr_tables($citr_id, $dt_post);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->REST_Return(422, 'Oops, something went wrong, data rollback.', []);
                return;
            }
            $response_data = array('id' => $citr_id);
            $this->REST_Return(200, 'SUCCESS', $response_data);
        } catch (\Exception $e) {
            $this->REST_Return(422, 'Oops, something went wrong, execution error.', ["message" => $e->getMessage()]);
        }
        return;
    }

    public function delete_delete($p = 0)
    {
        $id         = $this->security->xss_clean($p);
        $is_valid_token = $this->authorization_token->validateToken();
        if (empty($is_valid_token) or $is_valid_token['status'] === FALSE) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'], $response_data);
            return;
        }

        $citr = $this->Citr_model->get_by_id($id);
        if (!$citr) {
            $this->REST_Return(404, 'Citr record not found', []);
            return;
        }
        $citr_id = $citr->id;

        // Delete citr records
        // $this->CitrPersiapanEdukasi_model->delete_by_citr_id($citr_id); (deprecated)
        // $this->CitrEdukasiLanjutan_model->delete_by_citr_id($citr_id); (deprecated)
        $this->Citr_model->delete_by_id($citr_id);

        $this->REST_Return(200, 'SUCCESS', []);
        return;
    }
}
