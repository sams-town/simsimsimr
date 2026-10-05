<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

class FirstAsesmen extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        //load model first asesmen
        $this->load->model('v1/mr/first_asesmen/FirstAsesmen_model');
        /**
         * child
         */
        $this->load->model('v1/mr/child/ChildAnakRiwayatKesehatanDahulu_model');
        $this->load->model('v1/mr/child/ChildRiwayatTumbuhKembang_model');
        $this->load->model('v1/mr/child/ChildRiwayatImuniasi_model');
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

        /**
         * neonatus
         */
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

    public function ListAsesmen_get($id)
    {
        header("Access-Control-Allow-Origin: *");

        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) and $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'], $response_data);
            return;
        }
        $listAsesmen = $this->FirstAsesmen_model->get_by_registration_id($id);
        if (!$listAsesmen) {
            $this->REST_Return(404, 'List record not found', []);
            return;
        }

        $data = [];
        foreach ($listAsesmen as $key => $value) {
            $list = array(
                /**
                 * child
                 */
                //umum
                'id'                                    => $value->id,
                'register_destination_id'               => $value->register_destination_id,
                'nurse_id'                              => $value->nurse_id,
                'ChildAnakRiwayatKesehatanDahulu'       => $this->ChildAnakRiwayatKesehatanDahulu_model->get_by_first_asesmen_id($value->id),
                'ChildRiwayatTumbuhKembang'             => $this->ChildRiwayatTumbuhKembang_model->get_by_first_asesmen_id($value->id),
                'ChildRiwayatImuniasi'                  => $this->ChildRiwayatImuniasi_model->get_by_first_asesmen_id($value->id),
                //pemeriksaan fisik
                'ChildPemeriksaanFisikTtv'              => $this->ChildPemeriksaanFisikTtv_model->get_by_first_asesmen_id($value->id),
                'ChildPemeriksaanFisikBeratBadan'       => $this->ChildPemeriksaanFisikBeratBadan_model->get_by_first_asesmen_id($value->id),
                'ChildPemeriksaanFisikNeorologi'        => $this->ChildPemeriksaanFisikNeorologi_model->get_by_first_asesmen_id($value->id),
                'ChildPemeriksaanFisikIntegument'       => $this->ChildPemeriksaanFisikIntegument_model->get_by_first_asesmen_id($value->id),
                'ChildPemeriksaanFisikKardiovaskuler'   => $this->ChildPemeriksaanFisikKardiovaskuler_model->get_by_first_asesmen_id($value->id),
                'ChildPemeriksaanFisikRespirasi'        => $this->ChildPemeriksaanFisikRespirasi_model->get_by_first_asesmen_id($value->id),
                'ChildPemeriksaanFisikGastrointestinal' => $this->ChildPemeriksaanFisikGastrointestinal_model->get_by_first_asesmen_id($value->id),
                //penilaian persistem
                'ChildPenilaianPersistemUmum'                               => $this->ChildPenilaianPersistemUmum_model->get_by_first_asesmen_id($value->id),
                'ChildPenilaianPersistemAsesmenFungsi'                      => $this->ChildPenilaianPersistemAsesmenFungsi_model->get_by_first_asesmen_id($value->id),
                'ChildPenilaianPersistemAsesmenKeadaanMentalPsikologis'     => $this->ChildPenilaianPersistemAsesmenKeadaanMentalPsikologis_model->get_by_first_asesmen_id($value->id),
                'ChildPenilaianPersistemEliminasi'                          => $this->ChildPenilaianPersistemEliminasi_model->get_by_first_asesmen_id($value->id),
                'ChildPenilaianPersistemSeksual'                            => $this->ChildPenilaianPersistemSeksual_model->get_by_first_asesmen_id($value->id),
                /**
                 * neonatus
                 */
                'NeonatusRiwayatKesehatanBayiIbu'       => $this->NeonatusRiwayatKesehatanBayiIbu_model->get_by_first_asesmen_id($value->id),
                'NeonatusKebutuhanEdukasiOrangTua'      => $this->NeonatusKebutuhanEdukasiOrangTua_model->get_by_first_asesmen_id($value->id),
                'NeonatusSpiritual'                     => $this->NeonatusSpiritual_model->get_by_first_asesmen_id($value->id),
                'NeonatusSosialBudaya'                  => $this->NeonatusSosialBudaya_model->get_by_first_asesmen_id($value->id),
                'NeonatusParu'                          => $this->NeonatusParu_model->get_by_first_asesmen_id($value->id),
                'NeonatusDownScore'                     => $this->NeonatusDownScore_model->get_by_first_asesmen_id($value->id),
                'NeonatusEliminasi'                     => $this->NeonatusEliminasi_model->get_by_first_asesmen_id($value->id),
                'NeonatusGenitalia'                     => $this->NeonatusGenitalia_model->get_by_first_asesmen_id($value->id),
                'NeonatusApgarScore'                    => $this->NeonatusApgarScore_model->get_by_first_asesmen_id($value->id),
                'NeonatusKardiologi'                    => $this->NeonatusKardiologi_model->get_by_first_asesmen_id($value->id),
                'NeonatusSkalaNyeri'                    => $this->NeonatusSkalaNyeri_model->get_by_first_asesmen_id($value->id),
                'NeonatusTraumaKulit'                   => $this->NeonatusTraumaKulit_model->get_by_first_asesmen_id($value->id),
                'NeonatusAlergiReaksi'                  => $this->NeonatusAlergiReaksi_model->get_by_first_asesmen_id($value->id),
                'NeonatusMuskuloskeletal'               => $this->NeonatusMuskuloskeletal_model->get_by_first_asesmen_id($value->id),
                'NeonatusPenanggungJawab'               => $this->NeonatusPenanggungJawab_model->get_by_first_asesmen_id($value->id),
                'NeonatusGastrointesnital'              => $this->NeonatusGastrointesnital_model->get_by_first_asesmen_id($value->id),
            );
            array_push($data, $list);
        }
        $this->REST_Return(200, 'Success', $data);
    }
}
