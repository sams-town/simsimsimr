<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

class Child extends My_Controller
{

    public function __construct()
    {
        parent::__construct();
        //first asesmen
        //umum
        $this->load->model('v1/mr/child/Child_model');
        $this->load->model('v1/mr/child/ChildAnakRiwayatKesehatanDahulu_model');
        $this->load->model('v1/mr/child/ChildRiwayatTumbuhKembang_model');
        $this->load->model('v1/mr/child/ChildRiwayatImunisasi_model');
        //pemeriksaan fisik
        $this->load->model('v1/mr/child/ChildPemeriksaanFisikTtv_model');
        $this->load->model('v1/mr/child/ChildPemeriksaanFisikBeratBadan_model');
        $this->load->model('v1/mr/child/ChildPemeriksaanFisikNeorologi_model');
        $this->load->model('v1/mr/child/ChildPemeriksaanFisikIntegument_model');
        $this->load->model('v1/mr/child/ChildPemeriksaanFisikKardiovaskuler_model');
        $this->load->model('v1/mr/child/ChildPemeriksaanFisikRespirasi_model');
        $this->load->model('v1/mr/child/ChildPemeriksaanFisikGastrointestinal_model');
        //penilaian persistem
        $this->load->model('v1/mr/child/ChildPenilaianPersistemUmum_model');
        $this->load->model('v1/mr/child/ChildPenilaianPersistemAsesmenFungsi_model');
        $this->load->model('v1/mr/child/ChildPenilaianPersistemAsesmenKeadaanMentalPsikologis_model');
        $this->load->model('v1/mr/child/ChildPenilaianPersistemEliminasi_model');
        $this->load->model('v1/mr/child/ChildPenilaianPersistemSeksual_model');
        //asesmen resiko jatuh and skala nyeri
        $this->load->model('v1/mr/child/ChildRisikoJatuh_model');
        $this->load->model('v1/mr/child/ChildSkalaNyeri_model');
        $this->load->library('Authorization_Token');
        $this->load->library('My_Library');
        $this->load->helper('url');
    }

    /**
     * SKALA NYERI
     */
    public function nyeri_list_get($p = 0)
    {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'], $q_strings);

        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) and $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'], $response_data);
            return;
        }
        $query = $this->ChildSkalaNyeri_model->get_nyeri_by_register_destination_id($q_strings);
        $this->REST_Return(200, 'SuccessFully Data', $query);
    }

    public function nyeri_print_get($id)
    {
        $is_valid_token = $this->authorization_token->validateToken();
        if (empty($is_valid_token) or $is_valid_token['status'] === FALSE) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'], $response_data);
            return;
        }

        $resiko_jatuh = $this->ChildSkalaNyeri_model->get_by_id($id);
        if (!$resiko_jatuh) {
            $this->REST_Return(404, 'Skala Nyeri Not Found', []);
            return;
        }
        $this->REST_Return(200, 'SUCCESS', $resiko_jatuh);
    }

    public function nyeri_detail_get($id)
    {
        $is_valid_token = $this->authorization_token->validateToken();
        if (empty($is_valid_token) or $is_valid_token['status'] === FALSE) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'], $response_data);
            return;
        }

        $child_skala_nyeri = $this->HandlerValidateCollectDataRecordByModelChild('ChildSkalaNyeri_model', 'get_by_nurse_id', $id);
        if (!$child_skala_nyeri) {
            $this->REST_Return(404, 'Child skala nyeri record not found', []);
            return;
        }

        $this->REST_Return(200, 'SUCCESS', $child_skala_nyeri);
    }


    //update or create nyeri(parent)
    public function nyeri_create_or_update_post()
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
                "register_destination_id" => $dt_post['register_destination_id'] ?? null,
                "nurse_id" => $dt_post['nurse_id'] ?? null,
                "terdapat_keluhan_nyeri" => $dt_post['terdapat_keluhan_nyeri'] ?? null,
                "keluhan_nyeri" => $dt_post['keluhan_nyeri'] ?? null,
                "nyeri_hilang_ketika" => $dt_post['nyeri_hilang_ketika'] ?? null,
                "nyeri_hilang_ketika_lainnya" => $dt_post['nyeri_hilang_ketika_lainnya'] ?? null,
                "metoda" => $dt_post['metoda'] ?? null,
                "metoda_vas_nrs_tidak_nyeri" => $dt_post['metoda_vas_nrs_tidak_nyeri'] ?? null,
                "metoda_vas_nrs_nyeri_ringan" => $dt_post['metoda_vas_nrs_nyeri_ringan'] ?? null,
                "metoda_vas_nrs_nyeri_sedang" => $dt_post['metoda_vas_nrs_nyeri_sedang'] ?? null,
                "metoda_vas_nrs_nyeri_terkontrol" => $dt_post['metoda_vas_nrs_nyeri_terkontrol'] ?? null,
                "metoda_vas_nrs_nyeri_berat_tidak_terkontrol" => $dt_post['metoda_vas_nrs_nyeri_berat_tidak_terkontrol'] ?? null,
                "metoda_vas_nrs_frekuensi_nyeri" => $dt_post['metoda_vas_nrs_frekuensi_nyeri'] ?? null,
                "metoda_vas_nrs_status_diberitahukan_dokter" => $dt_post['metoda_vas_nrs_status_diberitahukan_dokter'] ?? null,
                "metoda_vas_nrs_diberitahukan_dokter_pukul" => $dt_post['metoda_vas_nrs_diberitahukan_dokter_pukul'] ?? null,
                "metoda_vas_nrs_total_skor" => $dt_post['metoda_vas_nrs_total_skor'] ?? null,
                "metoda_flacc_wajah" => $dt_post['metoda_flacc_wajah'] ?? null,
                "metoda_flacc_kaki" => $dt_post['metoda_flacc_kaki'] ?? null,
                "metoda_flacc_aktifitas" => $dt_post['metoda_flacc_aktifitas'] ?? null,
                "metoda_flacc_menangis" => $dt_post['metoda_flacc_menangis'] ?? null,
                "metoda_flacc_bicara_atau_bersuara" => $dt_post['metoda_flacc_bicara_atau_bersuara'] ?? null,
                "metoda_flacc_total_skor" => $dt_post['metoda_flacc_total_skor'] ?? null,
                "metoda_cries_menangis" => $dt_post['metoda_cries_menangis'] ?? null,
                "metoda_cries_kebutuhan_02" => $dt_post['metoda_cries_kebutuhan_02'] ?? null,
                "metoda_cries_peningkatan_tanda_tanda_vital" => $dt_post['metoda_cries_peningkatan_tanda_tanda_vital'] ?? null,
                "metoda_cries_expresi_wajah" => $dt_post['metoda_cries_expresi_wajah'] ?? null,
                "metoda_cries_tidur" => $dt_post['metoda_cries_tidur'] ?? null,
                "metoda_cries_total_skor" => $dt_post['metoda_cries_total_skor'] ?? null,
                "metoda_ccpot_ekspresi_wajah" => $dt_post['metoda_ccpot_ekspresi_wajah'] ?? null,
                "metoda_ccpot_gerakan_tubuh" => $dt_post['metoda_ccpot_gerakan_tubuh'] ?? null,
                "metoda_ccpot_ketegangan_otot" => $dt_post['metoda_ccpot_ketegangan_otot'] ?? null,
                "metoda_ccpot_mengikuti_ventilator" => $dt_post['metoda_ccpot_mengikuti_ventilator'] ?? null,
                "metoda_ccpot_vokalasi" => $dt_post['metoda_ccpot_vokalasi'] ?? null,
                "metoda_ccpot_penggunaan_analgetika" => $dt_post['metoda_ccpot_penggunaan_analgetika'] ?? null,
                "metoda_ccpot_total_skor" => $dt_post['metoda_ccpot_total_skor'] ?? null,
                "kualitas_nyeri_durasi" => $dt_post['kualitas_nyeri_durasi'] ?? null,
                "kualitas_nyeri_frekuensi" => $dt_post['kualitas_nyeri_frekuensi'] ?? null,
                "kualitas_nyeri_deskripsi_lainnya" => $dt_post['kualitas_nyeri_deskripsi_lainnya'] ?? null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            );
            if ($this->db->trans_status() === FALSE) {
                $this->REST_Return(422, 'Oops, something went wrong, data rollback.', []);
                return;
            }
            $this->my_library->update_or_create($dt_post, $data, $this->ChildSkalaNyeri_model, 'id');
            $this->db->trans_complete();
            $this->REST_Return(200, 'Create Or Update Successfully', $data);
        } catch (\Exception $e) {
            $this->REST_Return(422, 'Oops, something went wrong, execution error.', ["message" => $e->getMessage()]);
        }
    }
    /**
     * FIRST ASESMEN CHILD.
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
                'nurse_id' => $dt_post['nurse_id'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            );

            // Insert the main and sub child record
            if ($this->validate_request_by_id($dt_post)) {
                $id = $this->Child_model->create($data);
                // Insert child records
                $this->insert_or_update_child_tables($id, $dt_post);
            }

            // Uodate the main and sub child record
            if (!$this->validate_request_by_id($dt_post)) {
                $this->insert_or_update_child_tables($dt_post['id'], $dt_post, $dt_post);
                // Update child records
                $id = $dt_post['id'];
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->REST_Return(422, 'Oops, something went wrong, data rollback.', []);
                return;
            }
            $response_data = array('id' => $id);
            $this->REST_Return(200, 'SUCCESS', $response_data);
        } catch (\Exception $e) {
            $this->REST_Return(422, 'Oops, something went wrong, execution error.', ["message" => $e->getMessage()]);
        }
        return;
    }

    private function validate_request_by_id($dt_post): bool
    {
        return !empty($dt_post['id']) ? false : true;
    }

    /**
     * Handle insertion or updating of child records based on child ID.
     */
    private function insert_or_update_child_tables($child_id, $dt_post, $REQUEST = null)
    {
        // Execute only if the data exists in dt_post
        //umum
        if (isset($dt_post['child_riwayat_kesehatan_dahulu'])) {
            $this->insert_or_update_child_riwayat_kesehatan_dahulu($child_id, $dt_post['child_riwayat_kesehatan_dahulu'], $REQUEST);
        }
        if (isset($dt_post['child_riwayat_tumbuh_kembang'])) {
            $this->insert_or_update_child_riwayat_tumbuh_kembang($child_id, $dt_post['child_riwayat_tumbuh_kembang'], $REQUEST);
        }
        if (isset($dt_post['child_riwayat_imunisasi'])) {
            $this->insert_or_update_child_riwayat_imunisasi($child_id, $dt_post['child_riwayat_imunisasi'], $REQUEST);
        }
        if (isset($dt_post['child_resiko_jatuh'])) {
            $this->insert_or_update_child_resiko_jatuh($child_id, $dt_post['child_resiko_jatuh'], $REQUEST);
        }

        //pemeriksaan fisik
        if (isset($dt_post['child_pemeriksaan_fisik_ttv'])) {
            $this->insert_or_update_child_pemeriksaan_fisik_ttv($child_id, $dt_post['child_pemeriksaan_fisik_ttv'], $REQUEST);
        }
        if (isset($dt_post['child_pemeriksaan_fisik_berat_badan'])) {
            $this->insert_or_update_child_pemeriksaan_fisik_berat_badan($child_id, $dt_post['child_pemeriksaan_fisik_berat_badan'], $REQUEST);
        }
        if (isset($dt_post['child_pemeriksaan_fisik_neorologi'])) {
            $this->insert_or_update_child_pemeriksaan_fisik_neorologi($child_id, $dt_post['child_pemeriksaan_fisik_neorologi'], $REQUEST);
        }
        if (isset($dt_post['child_pemeriksaan_fisik_integument'])) {
            $this->insert_or_update_child_pemeriksaan_fisik_integument($child_id, $dt_post['child_pemeriksaan_fisik_integument'], $REQUEST);
        }
        if (isset($dt_post['child_pemeriksaan_fisik_kardiovaskuler'])) {
            $this->insert_or_update_child_pemeriksaan_fisik_kardiovaskuler($child_id, $dt_post['child_pemeriksaan_fisik_kardiovaskuler'], $REQUEST);
        }
        if (isset($dt_post['child_pemeriksaan_fisik_respirasi'])) {
            $this->insert_or_update_child_pemeriksaan_fisik_respirasi($child_id, $dt_post['child_pemeriksaan_fisik_respirasi'], $REQUEST);
        }
        if (isset($dt_post['child_pemeriksaan_fisik_gastrointestinal'])) {
            $this->insert_or_update_child_pemeriksaan_fisik_gastrointestinal($child_id, $dt_post['child_pemeriksaan_fisik_gastrointestinal'], $REQUEST);
        }

        //penilaian persistem
        if (isset($dt_post['child_penilaian_persistem_umum'])) {
            $this->insert_or_update_child_penilaian_persistem_umum($child_id, $dt_post['child_penilaian_persistem_umum'], $REQUEST);
        }
        if (isset($dt_post['child_penilaian_persistem_asesmen_fungsi'])) {
            $this->insert_or_update_child_penilaian_persistem_asesmen_fungsi($child_id, $dt_post['child_penilaian_persistem_asesmen_fungsi'], $REQUEST);
        }
        if (isset($dt_post['child_penilaian_persistem_keadaan_mental_dan_psikologis'])) {
            $this->insert_or_update_child_penilaian_persistem_keadaan_mental_dan_psikologis($child_id, $dt_post['child_penilaian_persistem_keadaan_mental_dan_psikologis'], $REQUEST);
        }
        if (isset($dt_post['child_penilaian_persistem_eliminasi'])) {
            $this->insert_or_update_child_penilaian_persistem_eliminasi($child_id, $dt_post['child_penilaian_persistem_eliminasi'], $REQUEST);
        }
        if (isset($dt_post['child_penilaian_persistem_seksual'])) {
            $this->insert_or_update_child_penilaian_persistem_seksual($child_id, $dt_post['child_penilaian_persistem_seksual'], $REQUEST);
        }
    }

    //set umum
    private function insert_or_update_child_riwayat_kesehatan_dahulu($child_id, $data, $REQUEST)
    {
        $child_riwayat_kesehatan_dahulu = array(
            'child_id' => $child_id,
            'jenis_persalinan' => $data['jenis_persalinan'] ?? null,
            'keterangan_lahir' => $data['keterangan_lahir'] ?? null,
            'lingkar_kepala' => $data['lingkar_kepala'] ?? null,
            'berat_badan_lahir' => $data['berat_badan_lahir'] ?? null,
            'tumbuh_kembang' => $data['tumbuh_kembang'] ?? null,
            'lila' => $data['lila'] ?? null,
            'tinggi_badan_lahir' => $data['tinggi_badan_lahir'] ?? null,
            'tinggi_badan' => $data['tinggi_badan'] ?? null,
            'imt' => $data['imt'] ?? null,
            'status_gizi' => $data['status_gizi'] ?? null,
            'berat_badan' => $data['berat_badan'] ?? null,
            'perawakan' => $data['perawakan'] ?? null,
        );

        $this->upsert_child('ChildAnakRiwayatKesehatanDahulu_model', $child_riwayat_kesehatan_dahulu, $REQUEST);
    }
    private function insert_or_update_child_riwayat_tumbuh_kembang($child_id, $data, $REQUEST)
    {
        $child_riwayat_tumbuh_kembang = array(
            'child_id' => $child_id,
            'bb_anak_saat_lahir' => $data['bb_anak_saat_lahir'] ?? null,
            'pb_anak_saat_lahir' => $data['pb_anak_saat_lahir'] ?? null,
            'asi_sampai_umur' => $data['asi_sampai_umur'] ?? null,
            'susu_formula' => $data['susu_formula'] ?? null,
            'makanan_padat' => $data['makanan_padat'] ?? null,
            'makanan_tambahan' => $data['makanan_tambahan'] ?? null,
            'tengkurap' => $data['tengkurap'] ?? null,
            'duduk' => $data['duduk'] ?? null,
            'merangkak' => $data['merangkak'] ?? null,
            'berdiri' => $data['berdiri'] ?? null,
            'berjalan' => $data['berjalan'] ?? null,
        );

        $this->upsert_child('ChildRiwayatTumbuhKembang_model', $child_riwayat_tumbuh_kembang, $REQUEST);
    }
    private function insert_or_update_child_riwayat_imunisasi($child_id, $data, $REQUEST)
    {
        $child_riwayat_imunisasi = [];
        foreach ($data as $key => $value) {
            if (!$this->HandlerValidateExistingRecordByModelRiwayatImunisasi($value['nama_vaksin'], $child_id)) {
                $value['id'] = UUID::v4();
                $value['child_id'] = $child_id;
                array_push($child_riwayat_imunisasi, $value);
            }
        }

        $this->upsert_child('ChildRiwayatImunisasi_model', $child_riwayat_imunisasi, $REQUEST);
    }

    private function insert_or_update_child_resiko_jatuh($child_id, $data, $REQUEST)
    {
        $child_resiko_jatuh = array(
            'child_id' => $child_id,
            "umur" => $data['umur'] ?? null,
            "jenis_kelamin" => $data['jenis_kelamin'] ?? null,
            "diagnosa" => $data['diagnosa'] ?? null,
            "gangguan_kognitif" => $data['gangguan_kognitif'] ?? null,
            "faktor_lingkungan" => $data['faktor_lingkungan'] ?? null,
            "respon_terhadap_operasi_obat_efek_anestasi" => $data['respon_terhadap_operasi_obat_efek_anestasi'] ?? null,
            "penggunaan_obat" => $data['penggunaan_obat'] ?? null,
            "skor_total" => $data['skor_total'] ?? null,
            "keterangan_skor" => $data['keterangan_skor'] ?? null,
            "asesmen_tanggal" => $data['asesmen_tanggal'] ?? null,
            "asesmen_jam" => $data['asesmen_jam'] ?? null,
        );

        $this->upsert_child('ChildRisikoJatuh_model', $child_resiko_jatuh, $REQUEST);
    }

    //set pemeriksaan fisik
    private function insert_or_update_child_pemeriksaan_fisik_ttv($child_id, $data, $REQUEST)
    {
        $child_pemeriksaan_fisik_ttv = array(
            'child_id' => $child_id,
            'tekanan_darah' => $data['tekanan_darah'] ?? null,
            'suhu' => $data['suhu'] ?? null,
            'nadi' => $data['nadi'] ?? null,
            'kesadaran' => $data['kesadaran'] ?? null,
        );

        $this->upsert_child('ChildPemeriksaanFisikTtv_model', $child_pemeriksaan_fisik_ttv, $REQUEST);
    }
    private function insert_or_update_child_pemeriksaan_fisik_berat_badan($child_id, $data, $REQUEST)
    {
        $child_pemeriksaan_fisik_berat_badan = array(
            'child_id' => $child_id,
            'berat_badan' => $data['berat_badan'] ?? null,
            'tinggi_badan' => $data['tinggi_badan'] ?? null,
            'lla' => $data['lla'] ?? null,
        );

        $this->upsert_child('ChildPemeriksaanFisikBeratBadan_model', $child_pemeriksaan_fisik_berat_badan, $REQUEST);
    }
    private function insert_or_update_child_pemeriksaan_fisik_neorologi($child_id, $data, $REQUEST)
    {
        $child_pemeriksaan_fisik_neorologi = array(
            'child_id' => $child_id,
            'penglihatan' => $data['penglihatan'] ?? null,
            'menggunakan_alat_bantu_penglihatan' => $data['menggunakan_alat_bantu_penglihatan'] ?? null,
            'menggunakan_alat_bantu_penglihatan_lainnya' => $data['menggunakan_alat_bantu_penglihatan_lainnya'] ?? null,
            'pendengaran' => $data['pendengaran'] ?? null,
            'pendengaran_lainnya' => $data['pendengaran_lainnya'] ?? null,
            'bicara' => $data['bicara'] ?? null,
            'bicara_lainnya' => $data['bicara_lainnya'] ?? null,
            'sensorik' => $data['sensorik'] ?? null,
            'sensorik_lainnya' => $data['sensorik_lainnya'] ?? null,
            'motorik' => $data['motorik'] ?? null,
            'motorik_lainnya' => $data['motorik_lainnya'] ?? null,
            'kekuatan_otot' => $data['kekuatan_otot'] ?? null,
            'menggunakan_alat_bantu_pendengaran' => $data['menggunakan_alat_bantu_pendengaran'] ?? null,
        );

        $this->upsert_child('ChildPemeriksaanFisikNeorologi_model', $child_pemeriksaan_fisik_neorologi, $REQUEST);
    }
    private function insert_or_update_child_pemeriksaan_fisik_integument($child_id, $data, $REQUEST)
    {
        $child_pemeriksaan_fisik_integument = array(
            'child_id' => $child_id,
            'warna_kulit' => $data['warna_kulit'] ?? null,
            'tugor' => $data['tugor'] ?? null,
            'tugor_lainnya' => $data['tugor_lainnya'] ?? null,
            'kulit' => $data['kulit'] ?? null,
            'kulit_lainnya' => $data['kulit_lainnya'] ?? null,
            'kriteria_resiko_dekubitus' => $data['kriteria_resiko_dekubitus'] ?? null,
        );

        $this->upsert_child('ChildPemeriksaanFisikIntegument_model', $child_pemeriksaan_fisik_integument, $REQUEST);
    }
    private function insert_or_update_child_pemeriksaan_fisik_kardiovaskuler($child_id, $data, $REQUEST)
    {
        $child_pemeriksaan_fisik_kardiovaskuler = array(
            'child_id' => $child_id,
            'warna_kulit' => $data['warna_kulit'] ?? null,
            'warna_kulit_lainnya' => $data['warna_kulit_lainnya'] ?? null,
            'denyut_nadi' => $data['denyut_nadi'] ?? null,
            'sirkulasi' => $data['sirkulasi'] ?? null,
            'sirkulasi_lainnya' => $data['sirkulasi_lainnya'] ?? null,
            'nyeri_dada' => $data['nyeri_dada'] ?? null,
            'nyeri_dada_lainnya' => $data['nyeri_dada_lainnya'] ?? null,
            'pulasi' => $data['pulasi'] ?? null,
            'pulasi_lainnya' => $data['pulasi_lainnya'] ?? null,
        );

        $this->upsert_child('ChildPemeriksaanFisikKardiovaskuler_model', $child_pemeriksaan_fisik_kardiovaskuler, $REQUEST);
    }
    private function insert_or_update_child_pemeriksaan_fisik_respirasi($child_id, $data, $REQUEST)
    {
        $child_pemeriksaan_fisik_respirasi = array(
            'child_id' => $child_id,
            'pola_nafas' => $data['pola_nafas'] ?? null,
            'irama_nafas' => $data['irama_nafas'] ?? null,
            'batuk' => $data['batuk'] ?? null,
            'batuk_jenis' => $data['batuk_jenis'] ?? null,
            'volume_pernafasan' => $data['volume_pernafasan'] ?? null,
            'kesulitan_bernafas' => $data['kesulitan_bernafas'] ?? null,
            'kesulitan_bernafas_jenis' => $data['kesulitan_bernafas_jenis'] ?? null,
            'kesulitan_bernafas_lainnya' => $data['kesulitan_bernafas_lainnya'] ?? null,
            'jenis_pernafasan' => $data['jenis_pernafasan'] ?? null,
            'jenis_pernafasan_lainnya' => $data['jenis_pernafasan_lainnya'] ?? null,
        );

        $this->upsert_child('ChildPemeriksaanFisikRespirasi_model', $child_pemeriksaan_fisik_respirasi, $REQUEST);
    }
    private function insert_or_update_child_pemeriksaan_fisik_gastrointestinal($child_id, $data, $REQUEST)
    {
        $child_pemeriksaan_fisik_gastrointestinal = array(
            'child_id' => $child_id,
            'mulut' => $data['mulut'] ?? null,
            'mulut_lainnya' => $data['mulut_lainnya'] ?? null,
            'gigi' => $data['gigi'] ?? null,
            'gigi_lainnya' => $data['gigi_lainnya'] ?? null,
            'lidah' => $data['lidah'] ?? null,
            'lidah_lainnya' => $data['lidah_lainnya'] ?? null,
            'tenggorokan' => $data['tenggorokan'] ?? null,
            'tenggorokan_lainnya' => $data['tenggorokan_lainnya'] ?? null,
            'abdomen' => $data['abdomen'] ?? null,
            'abdomen_asites' => $data['abdomen_asites'] ?? null,
            'abdomen_tegang' => $data['abdomen_tegang'] ?? null,
            'abdomen_lainnya' => $data['abdomen_lainnya'] ?? null,
        );

        $this->upsert_child('ChildPemeriksaanFisikGastrointestinal_model', $child_pemeriksaan_fisik_gastrointestinal, $REQUEST);
    }


    //set penilaian persistem
    private function insert_or_update_child_penilaian_persistem_umum($child_id, $data, $REQUEST)
    {
        $child_penilaian_persistem_umum = array(
            'child_id' => $child_id,
            'apakah_ada_nyeri' => $data['apakah_ada_nyeri'] ?? null,
            'riwayat_minuman_keras' => $data['riwayat_minuman_keras'] ?? null,
            'riwayat_minuman_keras_jumlah_per_hari' => $data['riwayat_minuman_keras_jumlah_per_hari'] ?? null,
            'riwayat_penggunaan_obat_penenang' => $data['riwayat_penggunaan_obat_penenang'] ?? null,
            'riwayat_penggunaan_obat_penenang_jenis' => $data['riwayat_penggunaan_obat_penenang_jenis'] ?? null,
            'riwayat_merokok' => $data['riwayat_merokok'] ?? null,
            'riwayat_merokok_jumlah_per_hari' => $data['riwayat_merokok_jumlah_per_hari'] ?? null,
            'riwayat_merokok_durasi' => $data['riwayat_merokok_durasi'] ?? null,
            'pola_istirahat' => $data['pola_istirahat'] ?? null,
        );

        $this->upsert_child('ChildPenilaianPersistemUmum_model', $child_penilaian_persistem_umum, $REQUEST);
    }
    private function insert_or_update_child_penilaian_persistem_asesmen_fungsi($child_id, $data, $REQUEST)
    {
        $child_penilaian_persistem_asesmen_fungsi = array(
            'child_id' => $child_id,
            'kemampuan_melakukan_aktivitas_sehari' => $data['kemampuan_melakukan_aktivitas_sehari'] ?? null,
            'ekstremitas_atas' => $data['ekstremitas_atas'] ?? null,
            'ekstremitas_atas_riwayat_patah_tulang' => $data['ekstremitas_atas_riwayat_patah_tulang'] ?? null,
            'alat_ambulasi' => $data['alat_ambulasi'] ?? null,
            'aktivitas' => $data['aktivitas'] ?? null,
            'kemampuan_menggenggam' => $data['kemampuan_menggenggam'] ?? null,
            'kemampuan_menggenggam_lainnya' => $data['kemampuan_menggenggam_lainnya'] ?? null,
            'kemampuan_menggenggam_masalah' => $data['kemampuan_menggenggam_masalah'] ?? null,
        );

        $this->upsert_child('ChildPenilaianPersistemAsesmenFungsi_model', $child_penilaian_persistem_asesmen_fungsi, $REQUEST);
    }
    private function insert_or_update_child_penilaian_persistem_keadaan_mental_dan_psikologis($child_id, $data, $REQUEST)
    {
        $child_penilaian_persistem_keadaan_mental_psikologis = array(
            'child_id' => $child_id,
            'status_mental' => $data['status_mental'] ?? null,
            'status_psikologi' => $data['status_psikologi'] ?? null,
            'status_psikologi_lainnya' => $data['status_psikologi_lainnya'] ?? null,
        );

        $this->upsert_child('ChildPenilaianPersistemAsesmenKeadaanMentalPsikologis_model', $child_penilaian_persistem_keadaan_mental_psikologis, $REQUEST);
    }
    private function insert_or_update_child_penilaian_persistem_eliminasi($child_id, $data, $REQUEST)
    {
        $child_penilaian_persistem_eliminasi = array(
            'child_id' => $child_id,
            'bab' => $data['bab'] ?? null,
            'bab_frekuensi_diare' => $data['bab_frekuensi_diare'] ?? null,
            'bak' => $data['bak'] ?? null,
        );

        $this->upsert_child('ChildPenilaianPersistemEliminasi_model', $child_penilaian_persistem_eliminasi, $REQUEST);
    }
    private function insert_or_update_child_penilaian_persistem_seksual($child_id, $data, $REQUEST)
    {
        $child_penilaian_persistem_seksual = array(
            'child_id' => $child_id,
            'wanita_haid' => $data['wanita_haid'] ?? null,
            'laki_laki' => $data['laki_laki'] ?? null,
            'laki_laki_sirkumsisi' => $data['laki_laki_sirkumsisi'] ?? null,
            'laki_laki_sirkumsisi_status' => $data['laki_laki_sirkumsisi_status'] ?? null,
            'laki_laki_masalah_lain' => $data['laki_laki_masalah_lain'] ?? null,
            'laki_laki_masalah_lain_status' => $data['laki_laki_masalah_lain_status'] ?? null,
        );

        $this->upsert_child('ChildPenilaianPersistemSeksual_model', $child_penilaian_persistem_seksual, $REQUEST);
    }

    //upsert conf
    private function upsert_child($model, $data, $REQUEST)
    {
        if (!$this->HandlerValidateInsertWithModelRiwayatImunisasi($model)) {
            $this->my_library->update_or_create($REQUEST, $data, $this->$model, 'child_id');
        }

        if ($this->HandlerValidateInsertWithModelRiwayatImunisasi($model)) {
            $CheckRecordVaksin = $this->HandlerValidateCollectDataRecordByModelRiwayatImunisasi($data);
            if (!$CheckRecordVaksin) {
                $this->$model->insert($data);
            }
        }
    }

    //set handler insert riwayat vaksin multiple records
    private static function HandlerValidateInsertWithModelRiwayatImunisasi(string $model): bool
    {
        return $model != 'ChildRiwayatImunisasi_model' ? false : true;
    }

    private function HandlerValidateExistingRecordByModelRiwayatImunisasi($data, $child_id): bool
    {
        return !$this->ChildRiwayatImunisasi_model->validate_existing_by_record($data, $child_id) ? false : true;
    }

    private static function HandlerValidateCollectDataRecordByModelRiwayatImunisasi($data): bool
    {
        return !empty($data) ? false : true;
    }

    //========================== end =========================================
    public function DeleteRiwayatImunisasi_delete($p = 0)
    {
        $id             = $this->security->xss_clean($p);
        $is_valid_token = $this->authorization_token->validateToken();
        if (empty($is_valid_token) or $is_valid_token['status'] === FALSE) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'], $response_data);
            return;
        }

        $child_riwayat_imunisasi = $this->HandlerValidateCollectDataRecordByModelChild('ChildRiwayatImunisasi_model', 'get_by_id', $id);
        if (!$child_riwayat_imunisasi) {
            $this->REST_Return(404, 'Child riwayat imunisasi not found record', []);
            return;
        }
        $child_riwayat_imunisasi = $child_riwayat_imunisasi->id;
        $this->ChildRiwayatImunisasi_model->delete_by_id($id);
        $this->REST_Return(200, 'Success', $child_riwayat_imunisasi);
    }

    public function DetailChild_get($id)
    {
        $is_valid_token = $this->authorization_token->validateToken();
        if (empty($is_valid_token) or $is_valid_token['status'] === FALSE) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'], $response_data);
            return;
        }

        $child = $this->HandlerValidateCollectDataRecordByModelChild('Child_model', 'get_by_nurse_id', $id);
        if (!$child) {
            $this->REST_Return(404, 'Child record not found', []);
            return;
        }
        $child_id = $child->id;
        //umum
        $child->child_anak_riwayat_kesehatan_dahulu = $this->ChildAnakRiwayatKesehatanDahulu_model->get_by_child_id($child_id);
        $child->child_riwayat_tumbuh_kembang = $this->ChildRiwayatTumbuhKembang_model->get_by_child_id($child_id);
        $child->child_riwayat_imunisasi = $this->ChildRiwayatImunisasi_model->get_by_child_id($child_id);
        $child->child_resiko_jatuh = $this->ChildRisikoJatuh_model->get_by_child_id($child_id);
        //pemeriksaan fisik
        $child->child_pemeriksaan_fisik_ttv = $this->ChildPemeriksaanFisikTtv_model->get_by_child_id($child_id);
        $child->child_pemeriksaan_fisik_berat_badan = $this->ChildPemeriksaanFisikBeratBadan_model->get_by_child_id($child_id);
        $child->child_pemeriksaan_fisik_neorologi = $this->ChildPemeriksaanFisikNeorologi_model->get_by_child_id($child_id);
        $child->child_pemeriksaan_fisik_integument = $this->ChildPemeriksaanFisikIntegument_model->get_by_child_id($child_id);
        $child->child_pemeriksaan_fisik_kardiovaskuler = $this->ChildPemeriksaanFisikKardiovaskuler_model->get_by_child_id($child_id);
        $child->child_pemeriksaan_fisik_respirasi = $this->ChildPemeriksaanFisikRespirasi_model->get_by_child_id($child_id);
        $child->child_pemeriksaan_fisik_gastrointestinal = $this->ChildPemeriksaanFisikGastrointestinal_model->get_by_child_id($child_id);
        //penilaian persistem
        $child->child_penilaian_persistem_umum = $this->ChildPenilaianPersistemUmum_model->get_by_child_id($child_id);
        $child->child_penilaian_persistem_asesmen_fungsi = $this->ChildPenilaianPersistemAsesmenFungsi_model->get_by_child_id($child_id);
        $child->child_penilaian_persistem_asesmenKeadaan_mental_psikologis = $this->ChildPenilaianPersistemAsesmenKeadaanMentalPsikologis_model->get_by_child_id($child_id);
        $child->child_penilaian_persistem_eliminasi = $this->ChildPenilaianPersistemEliminasi_model->get_by_child_id($child_id);
        $child->child_penilaian_persistem_seksual = $this->ChildPenilaianPersistemSeksual_model->get_by_child_id($child_id);
        $this->REST_Return(200, 'SUCCESS', $child);
    }

    public function PrintChild_get($id)
    {
        $is_valid_token = $this->authorization_token->validateToken();
        if (empty($is_valid_token) or $is_valid_token['status'] === FALSE) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'], $response_data);
            return;
        }

        // Fetch the child record by ID
        $child = $this->HandlerValidateCollectDataRecordByModelChild('Child_model', 'get_by_nurse_id', $id);
        if (!$child) {
            $this->REST_Return(404, 'Child record not found', []);
            return;
        }
        $child_id = $child->id;
        //umum
        $child->child_anak_riwayat_kesehatan_dahulu = $this->ChildAnakRiwayatKesehatanDahulu_model->get_by_child_id($child_id);
        $child->child_riwayat_tumbuh_kembang = $this->ChildRiwayatTumbuhKembang_model->get_by_child_id($child_id);
        $child->child_riwayat_imunisasi = $this->ChildRiwayatImunisasi_model->get_by_child_id($child_id);
        $child->child_resiko_jatuh = $this->ChildRisikoJatuh_model->get_by_child_id($child_id);
        //pemeriksaan fisik
        $child->child_pemeriksaan_fisik_ttv = $this->ChildPemeriksaanFisikTtv_model->get_by_child_id($child_id);
        $child->child_pemeriksaan_fisik_berat_badan = $this->ChildPemeriksaanFisikBeratBadan_model->get_by_child_id($child_id);
        $child->child_pemeriksaan_fisik_neorologi = $this->ChildPemeriksaanFisikNeorologi_model->get_by_child_id($child_id);
        $child->child_pemeriksaan_fisik_integument = $this->ChildPemeriksaanFisikIntegument_model->get_by_child_id($child_id);
        $child->child_pemeriksaan_fisik_kardiovaskuler = $this->ChildPemeriksaanFisikKardiovaskuler_model->get_by_child_id($child_id);
        $child->child_pemeriksaan_fisik_respirasi = $this->ChildPemeriksaanFisikRespirasi_model->get_by_child_id($child_id);
        $child->child_pemeriksaan_fisik_gastrointestinal = $this->ChildPemeriksaanFisikGastrointestinal_model->get_by_child_id($child_id);
        //penilaian persistem
        $child->child_penilaian_persistem_umum = $this->ChildPenilaianPersistemUmum_model->get_by_child_id($child_id);
        $child->child_penilaian_persistem_asesmen_fungsi = $this->ChildPenilaianPersistemAsesmenFungsi_model->get_by_child_id($child_id);
        $child->child_penilaian_persistem_asesmenKeadaan_mental_psikologis = $this->ChildPenilaianPersistemAsesmenKeadaanMentalPsikologis_model->get_by_child_id($child_id);
        $child->child_penilaian_persistem_eliminasi = $this->ChildPenilaianPersistemEliminasi_model->get_by_child_id($child_id);
        $child->child_penilaian_persistem_seksual = $this->ChildPenilaianPersistemSeksual_model->get_by_child_id($child_id);
        $this->REST_Return(200, 'SUCCESS', $child);
    }

    private function HandlerValidateCollectDataRecordByModelChild(string $model, string $method, $data)
    {
        return $this->$model->$method($data);
    }
}
