<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

class Neonatus extends My_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('v1/mr/neonatus/Neonatus_model');
        $this->load->model('v1/mr/neonatus/NeonatusRiwayatKesehatanBayiIbu_model');
        $this->load->model('v1/mr/neonatus/NeonatusKebutuhanEdukasiOrangTua_model');
        $this->load->model('v1/mr/neonatus/NeonatusSpiritual_model');
        $this->load->model('v1/mr/neonatus/NeonatusSosialBudaya_model');
        $this->load->model('v1/mr/neonatus/NeonatusParu_model');
        $this->load->model('v1/mr/neonatus/NeonatusDownScore_model');
        $this->load->model('v1/mr/neonatus/NeonatusEliminasi_model', '');
        $this->load->model('v1/mr/neonatus/NeonatusGenitalia_model');
        $this->load->model('v1/mr/neonatus/NeonatusApgarScore_model');
        $this->load->model('v1/mr/neonatus/NeonatusKardiologi_model');
        $this->load->model('v1/mr/neonatus/NeonatusSkalaNyeri_model');
        $this->load->model('v1/mr/neonatus/NeonatusTraumaKulit_model');
        $this->load->model('v1/mr/neonatus/NeonatusAlergiReaksi_model');
        $this->load->model('v1/mr/neonatus/NeonatusMuskuloskeletal_model');
        $this->load->model('v1/mr/neonatus/NeonatusPenanggungJawab_model');
        $this->load->model('v1/mr/neonatus/NeonatusGastrointesnital_model');
        $this->load->library('Authorization_Token');
        $this->load->library('My_Library');
        $this->load->helper('url');
    }

    /**
     * Create a new neonatus record along with its child records.
     */
    public function create_post()
    {
        $data = $this->json_post();
        $dt_post = $data['dt_postput'];

        // Check if any value is empty and set it to null
        $dt_post = array_map(function ($value) {
            return empty($value) ? null : $value;
        }, $dt_post);

        // $this->REST_Return(200, 'SUCCESS', $dt_post['register_destination_id']);
        // return;
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

            // Insert the main neonatus record
            if ($this->validate_request_by_id($dt_post)) {
                $id = $this->Neonatus_model->create($data);
                // Insert neonatus records
                $this->insert_or_update_child_tables($id, $dt_post);
            }

            if (!$this->validate_request_by_id($dt_post)) {
                // Insert update records
                $this->insert_or_update_child_tables($dt_post['id'], $dt_post, $dt_post);
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
     * Handle insertion or updating of child records based on neonatus ID.
     */
    private function insert_or_update_child_tables($neonatus_id, $dt_post, $REQUEST = null)
    {
        // Execute only if the data exists in dt_post
        if (isset($dt_post['neonatus_riwayat_kesehatan_bayi_ibu'])) {
            $this->insert_or_update_riwayat_kesehatan_bayi_ibu($neonatus_id, $dt_post['neonatus_riwayat_kesehatan_bayi_ibu'], $REQUEST);
        }

        if (isset($dt_post['neonatus_kebutuhan_edukasi_orang_tua'])) {
            $this->insert_or_update_kebutuhan_edukasi_orang_tua($neonatus_id, $dt_post['neonatus_kebutuhan_edukasi_orang_tua'], $REQUEST);
        }

        if (isset($dt_post['neonatus_spiritual'])) {
            $this->insert_or_update_spiritual($neonatus_id, $dt_post['neonatus_spiritual'], $REQUEST);
        }

        if (isset($dt_post['neonatus_sosial_budaya'])) {
            $this->insert_or_update_sosial_budaya($neonatus_id, $dt_post['neonatus_sosial_budaya'], $REQUEST);
        }

        if (isset($dt_post['neonatus_paru'])) {
            $this->insert_or_update_paru_neonatus($neonatus_id, $dt_post['neonatus_paru'], $REQUEST);
        }

        if (isset($dt_post['neonatus_down_score'])) {
            $this->insert_or_update_down_score_neonatus($neonatus_id, $dt_post['neonatus_down_score'], $REQUEST);
        }

        if (isset($dt_post['neonatus_eliminasi'])) {
            $this->insert_or_update_eliminasi_neonatus($neonatus_id, $dt_post['neonatus_eliminasi'], $REQUEST);
        }

        if (isset($dt_post['neonatus_genitalia'])) {
            $this->insert_or_update_genitalia_neonatus($neonatus_id, $dt_post['neonatus_genitalia'], $REQUEST);
        }

        if (isset($dt_post['neonatus_apgar_score'])) {
            $this->insert_or_update_apgar_score_neonatus($neonatus_id, $dt_post['neonatus_apgar_score'], $REQUEST);
        }

        if (isset($dt_post['neonatus_kardiologi'])) {
            $this->insert_or_update_kardiologi_neonatus($neonatus_id, $dt_post['neonatus_kardiologi'], $REQUEST);
        }

        if (isset($dt_post['neonatus_skala_nyeri'])) {
            $this->insert_or_update_skala_nyeri_neonatus($neonatus_id, $dt_post['neonatus_skala_nyeri'], $REQUEST);
        }

        if (isset($dt_post['neonatus_trauma_kulit'])) {
            $this->insert_or_update_trauma_kulit_neonatus($neonatus_id, $dt_post['neonatus_trauma_kulit'], $REQUEST);
        }

        if (isset($dt_post['neonatus_alergi_reaksi'])) {
            $this->insert_or_update_alergi_reaksi_neonatus($neonatus_id, $dt_post['neonatus_alergi_reaksi'], $REQUEST);
        }

        if (isset($dt_post['neonatus_muskuloskeletal'])) {
            $this->insert_or_update_muskuloskeletal_neonatus($neonatus_id, $dt_post['neonatus_muskuloskeletal'], $REQUEST);
        }

        if (isset($dt_post['neonatus_penanggung_jawab'])) {
            $this->insert_or_update_penanggung_jawab_neonatus($neonatus_id, $dt_post['neonatus_penanggung_jawab'], $REQUEST);
        }

        if (isset($dt_post['neonatus_gastrointesnital'])) {
            $this->insert_or_update_gastrointesnital_neonatus($neonatus_id, $dt_post['neonatus_gastrointesnital'], $REQUEST);
        }
    }

    // Separate methods for each child object
    private function insert_or_update_riwayat_kesehatan_bayi_ibu($neonatus_id, $data, $REQUEST)
    {
        $riwayat_kesehatan_bayi_ibu_neonatus_data = array(
            'neonatus_id' => $neonatus_id,
            'hasil_laboratorium_ibu' => $data['hasil_laboratorium_ibu'] ?? null,
            'hasil_laboratorium_ibu_lainnya' => $data['hasil_laboratorium_ibu_lainnya'] ?? null,
            'golongan_darah_ibu' => $data['golongan_darah_ibu'] ?? null,
            'riwayat_persalinan' => $data['riwayat_persalinan'] ?? null,
            'riwayat_kelahiran' => $data['riwayat_kelahiran'] ?? null,
            'jenis_kelamin_bayi' => $data['jenis_kelamin_bayi'] ?? null,
            'pemberian_obat_setelah_lahir' => $data['pemberian_obat_setelah_lahir'] ?? null,
            'pemberian_obat_setelah_lahir_lainnya' => $data['pemberian_obat_setelah_lahir_lainnya'] ?? null,
            'diagnosa_partus' => $data['diagnosa_partus'] ?? null,
            'riwayat_penyakit_ibu_setelah_hamil' => $data['riwayat_penyakit_ibu_setelah_hamil'] ?? null,
            'riwayat_kehamilan' => $data['riwayat_kehamilan'] ?? null,
            'riwayat_kehamilan_usia_gestasi' => $data['riwayat_kehamilan_usia_gestasi'] ?? null,
            'riwayat_kehamilan_usia_koreksi' => $data['riwayat_kehamilan_usia_koreksi'] ?? null,
        );
        $this->upsert_child('NeonatusRiwayatKesehatanBayiIbu_model', $riwayat_kesehatan_bayi_ibu_neonatus_data, $REQUEST);
    }

    private function insert_or_update_kebutuhan_edukasi_orang_tua($neonatus_id, $data, $REQUEST)
    {
        $kebutuhan_edukasi_orang_tua_neonatus_data = array(
            'neonatus_id' => $neonatus_id,
            'bicara' => $data['bicara'] ?? null,
            'bahasa_sehari_hari' => $data['bahasa_sehari_hari'] ?? null,
            'perlu_penerjemah' => $data['perlu_penerjemah'] ?? null,
            'tingkat_pendidikan' => $data['tingkat_pendidikan'] ?? null,
            'keluarga_menginginkan_informasi_tentang_proses_penyakit' => $data['keluarga_menginginkan_informasi_tentang_proses_penyakit'] ?? null,
        );
        $this->upsert_child('NeonatusKebutuhanEdukasiOrangTua_model', $kebutuhan_edukasi_orang_tua_neonatus_data, $REQUEST);
    }

    private function insert_or_update_spiritual($neonatus_id, $data, $REQUEST)
    {
        $spiritual_neonatus_data = array(
            'neonatus_id' => $neonatus_id,
            'agama' => $data['agama'] ?? null,
            'permintaan_khusus_terkait_dengan_adat' => $data['permintaan_khusus_terkait_dengan_adat'] ?? null,
        );
        $this->upsert_child('NeonatusSpiritual_model', $spiritual_neonatus_data, $REQUEST);
    }

    private function insert_or_update_sosial_budaya($neonatus_id, $data, $REQUEST)
    {
        $sosial_budaya_neonatus_data = array(
            'neonatus_id' => $neonatus_id,
            'suku_bangsa' => $data['suku_bangsa'] ?? null,
            'warga_negara' => $data['warga_negara'] ?? null,
            'tempat_tinggal' => $data['tempat_tinggal'] ?? null,
            'yang_merawat_bayi' => $data['yang_merawat_bayi'] ?? null,
            'orang_tua' => $data['orang_tua'] ?? null,
            'berkunjung' => $data['berkunjung'] ?? null,
            'menyentuh' => $data['menyentuh'] ?? null,
            'menggendong' => $data['menggendong'] ?? null,
            'kontak_mata' => $data['kontak_mata'] ?? null,
            'berbicara' => $data['berbicara'] ?? null,
            'lainnya' => $data['lainnya'] ?? null,
        );
        $this->upsert_child('NeonatusSosialBudaya_model', $sosial_budaya_neonatus_data, $REQUEST);
    }

    //pemeriksaan fisik
    private function insert_or_update_paru_neonatus($neonatus_id, $data, $REQUEST)
    {
        $paru_neonatus_data = array(
            'neonatus_id' => $neonatus_id,
            'pergerakan_paru' => $data['pergerakan_paru'] ?? null,
            'frekuensi_pernapasan_paru' => $data['frekuensi_pernapasan_paru'] ?? null,
            'retraksi' => $data['retraksi'] ?? null,
            'nch' => $data['nch'] ?? null,
            'grunting' => $data['grunting'] ?? null,
            'dyspnoe' => $data['dyspnoe'] ?? null,
            'tachynoe' => $data['tachynoe'] ?? null,
            'gargling_napas_tambahan' => $data['gargling_napas_tambahan'] ?? null,
            'snooring_napas_tambahan' => $data['snooring_napas_tambahan'] ?? null,
            'stridor_napas_tambahan' => $data['stridor_napas_tambahan'] ?? null,
            'peruksi_paru_paru' => $data['peruksi_paru_paru'] ?? null,
            'ronkhi_paru_kanan' => $data['ronkhi_paru_kanan'] ?? null,
            'wheezing_paru_kanan' => $data['wheezing_paru_kanan'] ?? null,
            'lainnya_paru_kanan' => $data['lainnya_paru_kanan'] ?? null,
            'perkusi_paru_kiri' => $data['perkusi_paru_kiri'] ?? null,
            'ronkhi_paru_kiri' => $data['ronkhi_paru_kiri'] ?? null,
            'wheezing_paru_kiri' => $data['wheezing_paru_kiri'] ?? null,
            'lainnya_paru_kiri' => $data['lainnya_paru_kiri'] ?? null
        );
        $this->upsert_child('NeonatusParu_model', $paru_neonatus_data, $REQUEST);
    }

    private function insert_or_update_down_score_neonatus($neonatus_id, $data, $REQUEST)
    {
        $down_score_neonatus_data = array(
            'neonatus_id' => $neonatus_id,
            'frekuensi_napas' => $data['frekuensi_napas'] ?? null,
            'retraksi' => $data['retraksi'] ?? null,
            'sianosis' => $data['sianosis'] ?? null,
            'air_entry' => $data['air_entry'] ?? null,
            'merintih' => $data['merintih'] ?? null
        );
        $this->upsert_child('NeonatusDownScore_model', $down_score_neonatus_data, $REQUEST);
    }

    private function insert_or_update_eliminasi_neonatus($neonatus_id, $data, $REQUEST)
    {
        $eliminasi_neonatus_data = array(
            'neonatus_id' => $neonatus_id,
            'anus' => $data['anus'] ?? null,
            'defaksi_anus' => $data['defaksi_anus'] ?? null,
            'defaksi_stoma' => $data['defaksi_stoma'] ?? null,
            'frekuensi_defaksi' => $data['frekuensi_defaksi'] ?? null,
            'konsistensi_defaksi' => $data['konsistensi_defaksi'] ?? null,
            'karakteristik_fases' => $data['karakteristik_fases'] ?? null,
            'urin' => $data['urin'] ?? null,
            'diuresis' => $data['diuresis'] ?? null
        );
        $this->upsert_child('NeonatusEliminasi_model', $eliminasi_neonatus_data, $REQUEST);
    }

    private function insert_or_update_genitalia_neonatus($neonatus_id, $data, $REQUEST)
    {
        $genitalia_neonatus_data = array(
            'neonatus_id' => $neonatus_id,
            'jenis_kelamin' => $data['jenis_kelamin'] ?? null,
            'kelainan' => $data['kelainan'] ?? null,
            'testis_laki_laki' => $data['testis_laki_laki'] ?? null,
            'penis_laki_laki' => $data['penis_laki_laki'] ?? null,
            'labio_perempuan' => $data['labio_perempuan'] ?? null,
            'lainnya' => $data['lainnya'] ?? null
        );
        $this->upsert_child('NeonatusGenitalia_model', $genitalia_neonatus_data, $REQUEST);
    }

    private function insert_or_update_apgar_score_neonatus($neonatus_id, $data, $REQUEST)
    {
        $apgar_score_neonatus_data = array(
            'neonatus_id' => $neonatus_id,
            'menit_ke' => $data['menit_ke'] ?? null,
            'denyut_jantung' => $data['denyut_jantung'] ?? null,
            'usaha_napas' => $data['usaha_napas'] ?? null,
            'tonus_otot' => $data['tonus_otot'] ?? null,
            'refleks' => $data['refleks'] ?? null
        );
        $this->upsert_child('NeonatusApgarScore_model', $apgar_score_neonatus_data, $REQUEST);
    }

    private function insert_or_update_kardiologi_neonatus($neonatus_id, $data, $REQUEST)
    {
        $kardiologi_neonatus_data = array(
            'neonatus_id' => $neonatus_id,
            'kelainan_jantung' => $data['kelainan_jantung'] ?? null,
            'denyut_jantung' => $data['denyut_jantung'] ?? null,
            'type_denyut_jantung' => $data['type_denyut_jantung'] ?? null,
            'murmur' => $data['murmur'] ?? null,
            'keterangan_murmur' => $data['keterangan_murmur'] ?? null,
            'gallop' => $data['gallop'] ?? null,
            'keterangan_gallop' => $data['keterangan_gallop'] ?? null,
            'sianosis' => $data['sianosis'] ?? null,
            'pucat' => $data['pucat'] ?? null,
            'crt' => $data['crt'] ?? null,
            'akral' => $data['akral'] ?? null
        );
        $this->upsert_child('NeonatusKardiologi_model', $kardiologi_neonatus_data, $REQUEST);
    }

    private function insert_or_update_skala_nyeri_neonatus($neonatus_id, $data, $REQUEST)
    {
        $skala_nyeri_neonatus_data = array(
            'neonatus_id' => $neonatus_id,
            'nama' => $data['nama'] ?? null,
            'skor' => $data['skor'] ?? null
        );
        $this->upsert_child('NeonatusSkalaNyeri_model', $skala_nyeri_neonatus_data, $REQUEST);
    }

    private function insert_or_update_trauma_kulit_neonatus($neonatus_id, $data, $REQUEST)
    {
        $trauma_kulit_neonatus_data = array(
            'neonatus_id' => $neonatus_id,
            'usia_gestasi' => $data['usia_gestasi'] ?? null,
            'status_mental' => $data['status_mental'] ?? null,
            'mobilisasi' => $data['mobilisasi'] ?? null,
            'aktivitas' => $data['aktivitas'] ?? null,
            'nutrisi' => $data['nutrisi'] ?? null,
            'kelembaban' => $data['kelembaban'] ?? null
        );
        $this->upsert_child('NeonatusTraumaKulit_model', $trauma_kulit_neonatus_data, $REQUEST);
    }

    private function insert_or_update_alergi_reaksi_neonatus($neonatus_id, $data, $REQUEST)
    {
        $alergi_reaksi_neonatus_data = array(
            'neonatus_id' => $neonatus_id,
            'makanan' => $data['makanan'] ?? null,
            'makanan_reaksi' => $data['makanan_reaksi'] ?? null,
            'obat' => $data['obat'] ?? null,
            'obat_reaksi' => $data['obat_reaksi'] ?? null,
            'lainnya' => $data['lainnya'] ?? null,
            'lainnya_reaksi' => $data['lainnya_reaksi'] ?? null,
            'klip_tanda_alergi' => $data['klip_tanda_alergi'] ?? null,
            'beritahu_dokter' => $data['beritahu_dokter'] ?? null,
            'beritahu_farmasi' => $data['beritahu_farmasi'] ?? null,
            'beritahu_dietisien' => $data['beritahu_dietisien'] ?? null
        );
        $this->upsert_child('NeonatusAlergiReaksi_model', $alergi_reaksi_neonatus_data, $REQUEST);
    }

    private function insert_or_update_muskuloskeletal_neonatus($neonatus_id, $data, $REQUEST)
    {
        $muskuloskeletal_neonatus_data = array(
            'neonatus_id' => $neonatus_id,
            'tonus' => $data['tonus'] ?? null,
            'kelainan_tulang' => $data['kelainan_tulang'] ?? null,
            'gerakan_bayi' => $data['gerakan_bayi'] ?? null
        );
        $this->upsert_child('NeonatusMuskuloskeletal_model', $muskuloskeletal_neonatus_data, $REQUEST);
    }

    private function insert_or_update_penanggung_jawab_neonatus($neonatus_id, $data, $REQUEST)
    {
        $penanggung_jawab_neonatus_data = array(
            'neonatus_id' => $neonatus_id,
            'wali' => $data['wali'] ?? null,
            'nama' => $data['nama'] ?? null,
            'usia' => $data['usia'] ?? null,
            'perkawinan_ke' => $data['perkawinan_ke'] ?? null,
            'pekerjaan' => $data['pekerjaan'] ?? null,
            'pendidikan_terakhir' => $data['pendidikan_terakhir'] ?? null,
            'alamat' => $data['alamat'] ?? null,
            'telepon' => $data['telepon'] ?? null,
            'penghasilan_perbulan' => $data['penghasilan_perbulan'] ?? null
        );
        $this->upsert_child('NeonatusPenanggungJawab_model', $penanggung_jawab_neonatus_data, $REQUEST);
    }

    private function insert_or_update_gastrointesnital_neonatus($neonatus_id, $data, $REQUEST)
    {
        $gastrointesnital_neonatus_data = array(
            'neonatus_id' => $neonatus_id,
            'stomatitis' => $data['stomatitis'] ?? null,
            'labio_palatochiziz' => $data['labio_palatochiziz'] ?? null,
            'mukosa_kering' => $data['mukosa_kering'] ?? null,
            'pendarahan_gusi' => $data['pendarahan_gusi'] ?? null,
            'muntah' => $data['muntah'] ?? null,
            'frekuensi_muntah' => $data['frekuensi_muntah'] ?? null,
            'residu' => $data['residu'] ?? null,
            'asites' => $data['asites'] ?? null,
            'bising_usus' => $data['bising_usus'] ?? null,
            'nyeri_tekan' => $data['nyeri_tekan'] ?? null,
            'limpa' => $data['limpa'] ?? null,
            'hepar' => $data['hepar'] ?? null,
            'lokasi_nyeri_tekan' => $data['lokasi_nyeri_tekan'] ?? null
        );
        $this->upsert_child('NeonatusGastrointesnital_model', $gastrointesnital_neonatus_data, $REQUEST);
    }


    private function upsert_child($model, $data, $REQUEST)
    {
        return $this->my_library->update_or_create($REQUEST, $data, $this->$model, 'neonatus_id');
    }

    public function DetailNeonatus_get($id)
    {
        $id         = $this->security->xss_clean($id);
        $is_valid_token = $this->authorization_token->validateToken();
        if (empty($is_valid_token) or $is_valid_token['status'] === FALSE) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'], $response_data);
            return;
        }
        // Fetch the neonatus record by ID
        $neonatus = $this->Neonatus_model->get_by_nurse_id($id);
        if (!$neonatus) {
            $this->REST_Return(404, 'Neonatus record not found', []);
            return;
        }
        $neonatus_id = $neonatus->id;
        $neonatus->neonatus_riwayat_kesehatan_bayi_ibu = $this->NeonatusRiwayatKesehatanBayiIbu_model->get_by_neonatus_id($neonatus_id);
        $neonatus->neonatus_kebutuhan_edukasi_orang_tua = $this->NeonatusKebutuhanEdukasiOrangTua_model->get_by_neonatus_id($neonatus_id);
        $neonatus->neonatus_spiritual = $this->NeonatusSpiritual_model->get_by_neonatus_id($neonatus_id);
        $neonatus->neonatus_sosial_budaya = $this->NeonatusSosialBudaya_model->get_by_neonatus_id($neonatus_id);
        $neonatus->neonatus_paru = $this->NeonatusParu_model->get_by_neonatus_id($neonatus_id);
        $neonatus->neonatus_down_score = $this->NeonatusDownScore_model->get_by_neonatus_id($neonatus_id);
        $neonatus->neonatus_eliminasi = $this->NeonatusEliminasi_model->get_by_neonatus_id($neonatus_id);
        $neonatus->neonatus_genitalia = $this->NeonatusGenitalia_model->get_by_neonatus_id($neonatus_id);
        $neonatus->neonatus_apgar_score = $this->NeonatusApgarScore_model->get_by_neonatus_id($neonatus_id);
        $neonatus->neonatus_kardiologi = $this->NeonatusKardiologi_model->get_by_neonatus_id($neonatus_id);
        $neonatus->neonatus_skala_nyeri = $this->NeonatusSkalaNyeri_model->get_by_neonatus_id($neonatus_id);
        $neonatus->neonatus_trauma_kulit = $this->NeonatusTraumaKulit_model->get_by_neonatus_id($neonatus_id);
        $neonatus->neonatus_alergi_reaksi = $this->NeonatusAlergiReaksi_model->get_by_neonatus_id($neonatus_id);
        $neonatus->neonatus_muskuloskeletal = $this->NeonatusMuskuloskeletal_model->get_by_neonatus_id($neonatus_id);
        $neonatus->neonatus_penanggung_jawab = $this->NeonatusPenanggungJawab_model->get_by_neonatus_id($neonatus_id);
        $neonatus->neonatus_gastrointesnital = $this->NeonatusGastrointesnital_model->get_by_neonatus_id($neonatus_id);

        $this->REST_Return(200, 'SUCCESS', $neonatus);
    }


    public function PrintNeonatus_get($id)
    {
        $id         = $this->security->xss_clean($id);
        $is_valid_token = $this->authorization_token->validateToken();
        if (empty($is_valid_token) or $is_valid_token['status'] === FALSE) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'], $response_data);
            return;
        }
        // Fetch the neonatus record by ID
        $neonatus = $this->Neonatus_model->get_by_nurse_id($id);
        if (!$neonatus) {
            $this->REST_Return(404, 'Neonatus record not found', []);
            return;
        }
        $neonatus_id = $neonatus->id;
        $neonatus->neonatus_riwayat_kesehatan_bayi_ibu = $this->NeonatusRiwayatKesehatanBayiIbu_model->get_by_neonatus_id($neonatus_id);
        $neonatus->neonatus_kebutuhan_edukasi_orang_tua = $this->NeonatusKebutuhanEdukasiOrangTua_model->get_by_neonatus_id($neonatus_id);
        $neonatus->neonatus_spiritual = $this->NeonatusSpiritual_model->get_by_neonatus_id($neonatus_id);
        $neonatus->neonatus_sosial_budaya = $this->NeonatusSosialBudaya_model->get_by_neonatus_id($neonatus_id);
        $neonatus->neonatus_paru = $this->NeonatusParu_model->get_by_neonatus_id($neonatus_id);
        $neonatus->neonatus_down_score = $this->NeonatusDownScore_model->get_by_neonatus_id($neonatus_id);
        $neonatus->neonatus_eliminasi = $this->NeonatusEliminasi_model->get_by_neonatus_id($neonatus_id);
        $neonatus->neonatus_genitalia = $this->NeonatusGenitalia_model->get_by_neonatus_id($neonatus_id);
        $neonatus->neonatus_apgar_score = $this->NeonatusApgarScore_model->get_by_neonatus_id($neonatus_id);
        $neonatus->neonatus_kardiologi = $this->NeonatusKardiologi_model->get_by_neonatus_id($neonatus_id);
        $neonatus->neonatus_skala_nyeri = $this->NeonatusSkalaNyeri_model->get_by_neonatus_id($neonatus_id);
        $neonatus->neonatus_trauma_kulit = $this->NeonatusTraumaKulit_model->get_by_neonatus_id($neonatus_id);
        $neonatus->neonatus_alergi_reaksi = $this->NeonatusAlergiReaksi_model->get_by_neonatus_id($neonatus_id);
        $neonatus->neonatus_muskuloskeletal = $this->NeonatusMuskuloskeletal_model->get_by_neonatus_id($neonatus_id);
        $neonatus->neonatus_penanggung_jawab = $this->NeonatusPenanggungJawab_model->get_by_neonatus_id($neonatus_id);
        $neonatus->neonatus_gastrointesnital = $this->NeonatusGastrointesnital_model->get_by_neonatus_id($neonatus_id);

        $this->REST_Return(200, 'SUCCESS', $neonatus);
    }
}
