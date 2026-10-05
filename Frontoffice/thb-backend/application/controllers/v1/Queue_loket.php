<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Queue_loket extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("v1/Queue_loket_model", "Queue_loket");
        $this->load->model("v1/Loket_model", "Loket");
        $this->load->model("v1/Display_poli_model", "Display_poli");
        $this->load->model("v1/setting/Helper_model", "Helper");
        $this->load->model("v1/mr/Registration_model", "Registration");
        $this->load->model("v1/mr/Registration_destination_model", "Registration_destination");
    }

    /**
     * Queue_Loket API
     * --------------------
     * @param: type_loket_id
     * --------------------
     * @method : POST
     * @link: version/queue_loket
     **/

    public function create_post() 
    {
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
            $this->form_validation->set_rules('type_loket_id', 'type_loket_id', 'trim|required|max_length[2]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $user_id = $this->security->xss_clean($is_valid_token['data']->id);
                // Check Helper_type_loket_id
                $this->Helper->whereFields['a.code'] = $dt_post['type_loket_id'];
                $this->Helper->whereFields['a.variable'] = 'type_loket';
                $this->Helper->whereFields['a.institute_branch_id'] = $institute_branch_id;
                $dt_type_loket = $this->Helper->read(0, false);
                if($dt_type_loket) {
                    $model = $this->name;
                    /* Start Transaction */
                    $this->db->trans_start();
                    $codecounter = array(
                        'var_1' => 'ADM',
                        'var_2' => $dt_type_loket[0]->description,
                        'var_3' => date('Y-m'),
                        'var_5' => date('Y-m-d')
                    );
                    $this->load->library("transaction");
                    $checkcounter = $this->transaction->counter($codecounter);
                    $code = $checkcounter['counter_queue'];
                    if ($checkcounter['counter_queue'] == '0') {
                        $this->Counter_queue->replace(array(
                            'var_1' => 'ADM',
                            'var_2' => $dt_type_loket[0]->description,
                            'var_3' => date('Y-m'),
                            'var_5' => date('Y-m-d'),
                            'counter_queue' => 0
                        ));
                        $checkcounter = $this->transaction->counter($codecounter);
                        $code = $checkcounter['counter_queue'];
                    }
                    //NEW COUNTER KODEBOOKING
                    $codecounter_book = array(
                        'var_1' => '01',
                        'var_2' => date('ymd'),
                        'var_3' => 'BOOKPOLI',
                        'var_4' => 1,
                        'var_5' => date('d')
                    );
                    $checkcounter_book = $this->transaction->counter($codecounter_book);
                    $kodebooking = $codecounter_book['var_1'].$codecounter_book['var_2'].str_pad($checkcounter_book['counter_queue'], 4, "0", STR_PAD_LEFT);
                    if ($checkcounter_book['counter_queue'] == '0') {
                        $this->Counter_queue->replace(array(
                            'var_1' => '01',
                            'var_2' => date('ymd'),
                            'var_3' => 'BOOKPOLI',
                            'var_4' => 1,
                            'var_5' => date('d'),
                            'counter_queue' => 1
                        ));
                        $kodebooking = $codecounter_book['var_1'].$codecounter_book['var_2'].str_pad($checkcounter_book['counter_queue']+1, 4, "0", STR_PAD_LEFT);
                    }
                    $dt_queue_loket = array(
                        'institute_branch_id'   => $institute_branch_id,
                        'type_loket_id'         => $dt_post['type_loket_id'],
                        'queue'                 => $code,
                        'code_booking'          => $kodebooking,
                        'code'                  => $dt_type_loket[0]->description,
                        'date'                  => date('Y-m-d'),
                        'date_added'            => date('Y-m-d H:i:s',time()),
                        'date_task_1'           => date('Y-m-d H:i:s',time()),
                    );
                    $id = $this->$model->create($dt_queue_loket);
                    $this->db->trans_complete();
                    /* End Transaction */
                    if ($this->db->trans_status() === false) {
                        $response_data = $this->db->trans_status();
                        $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                    } else {
                        $response_data = array(
                            'queue'     => $dt_type_loket[0]->description.'-'.$code
                        );
                        $this->REST_Return(201, 'SUCCESS', $response_data);
                    }
                } else {
                    $this->REST_Return(404, 'Wrong type_loket_id!');
                }
                return;     
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    public function checkin_post() 
    {
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
            $this->form_validation->set_rules('code_booking', 'code_booking', 'trim|required');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                // CARI KODE BOOKING 012409090001
                if ($dt_post['code_booking']) {
                    $db_antrol = $this->load->database('antrol', true);
                    $db_antrol->select("a.*,");
                    $db_antrol->where("a.kodebooking",$dt_post['code_booking']);
                    $db_antrol->from("bpjs_jkn_antrean as a");
                    $data_booking = $db_antrol->get()->result();    
                }
                // var_dump($data_booking[0]);
                // die();
                // CARI KODE BOOKING
                if($data_booking[0]){
                    //CARI RUJUKAN REFERENSI
                    $nomorreferensi = $data_booking[0]->nomorreferensi;
                    // var_dump($nomorreferensi);die();
                    $this->load->library('Vclaim_sep','vclaim_sep');
                    $this->load->library('vclaim_antrol','vclaim_antrol');
                    // $output = $this->vclaim_sep->request("/Rujukan/RS/Peserta/"."$p", "GET");
                    // $output = $this->vclaim_sep->request("/Rujukan/List/Peserta/"."$p", "GET");
                    $count_find = 0;
                    $found_data = false;
                    $category_store = array('noru'=>array('/Rujukan/','/Rujukan/RS/'), 'noka'=>array('/Rujukan/Peserta/','/Rujukan/RS/Peserta/'));
                    // $category_store = array('noka'=>array('/Rujukan/List/Peserta/','/Rujukan/RS/Peserta/'));
                    $category_store = $category_store['noru'];
                    while ($count_find < 2 && $found_data == false) {
                        $cat    = $category_store[$count_find];
                        $hasil  = $this->vclaim_sep->request($cat.$nomorreferensi, "GET");
                        $d = json_decode($hasil, true);
                        if(isset($d['metaData']['code'])){
                            if($d['metaData']['code']=='200'){
                                if(!empty($d['response']['rujukan']['peserta']['noKartu'])){
                                    $dataRujukan = array(['noKunjungan' => $d['response']['rujukan']['noKunjungan'], 'nmAsuransi' => $d['response']['rujukan']['peserta']['cob']['nmAsuransi'],'noAsuransi' => $d['response']['rujukan']['peserta']['cob']['noAsuransi'],'tglTATcob' => $d['response']['rujukan']['peserta']['cob']['tglTAT'],'tglTMTcob' => $d['response']['rujukan']['peserta']['cob']['tglTMT'],'keteranganhakKelas' => $d['response']['rujukan']['peserta']['hakKelas']['keterangan'],'kodehakKelas' => $d['response']['rujukan']['peserta']['hakKelas']['kode'],'dinsos' => $d['response']['rujukan']['peserta']['informasi']['dinsos'],'noSKTM' => $d['response']['rujukan']['peserta']['informasi']['noSKTM'],'prolanisPRB' => $d['response']['rujukan']['peserta']['informasi']['prolanisPRB'],'keteranganjenisPeserta' => $d['response']['rujukan']['peserta']['jenisPeserta']['keterangan'],'kodejenisPeserta' => $d['response']['rujukan']['peserta']['jenisPeserta']['kode'],'noMR' => $d['response']['rujukan']['peserta']['mr']['noMR'],'noTelepon' => $d['response']['rujukan']['peserta']['mr']['noTelepon'],'nama' => $d['response']['rujukan']['peserta']['nama'],'nik' => $d['response']['rujukan']['peserta']['nik'],'noKartu' => $d['response']['rujukan']['peserta']['noKartu'],'pisa' => $d['response']['rujukan']['peserta']['pisa'],'kdProvider' => $d['response']['rujukan']['peserta']['provUmum']['kdProvider'],'nmProvider' => $d['response']['rujukan']['peserta']['provUmum']['nmProvider'],'sex' => $d['response']['rujukan']['peserta']['sex'],'keteranganstatusPeserta' => $d['response']['rujukan']['peserta']['statusPeserta']['keterangan'],'kodestatusPeserta' => $d['response']['rujukan']['peserta']['statusPeserta']['kode'],'tglCetakKartu' => $d['response']['rujukan']['peserta']['tglCetakKartu'],'tglLahir' => $d['response']['rujukan']['peserta']['tglLahir'],'tglTAT' => $d['response']['rujukan']['peserta']['tglTAT'],'tglTMT' => $d['response']['rujukan']['peserta']['tglTMT'],'umurSaatPelayanan' => $d['response']['rujukan']['peserta']['umur']['umurSaatPelayanan'],'umurSekarang' => $d['response']['rujukan']['peserta']['umur']['umurSekarang'],'kodediagnosa' => $d['response']['rujukan']['diagnosa']['kode'],'namadiagnosa' => $d['response']['rujukan']['diagnosa']['nama'],'kd_poli' =>  $d['response']['rujukan']['poliRujukan']['kode'],'nama_poli' =>  $d['response']['rujukan']['poliRujukan']['nama'],'tglKunjungan' => $d['response']['rujukan']['tglKunjungan'],'ppkRujukan' => $d['response']['rujukan']['provPerujuk']['kode'],'nmProviderPerujuk' => $d['response']['rujukan']['provPerujuk']['nama'],'code' => $d['metaData']['code'],'message' => $d['metaData']['message']]);
                                    if($count_find==0) {$dataRujukan[0]['AsalRujukanFaskes'] = '3';} //Faskes 1
                                    else if($count_find==1)
                                    {
                                    if (@$value['provPerujuk']['kode'] == '0070R032') {
                                        $dataRujukan[0]['AsalRujukanFaskes'] = '4';
                                    }else{
                                        $dataRujukan[0]['AsalRujukanFaskes'] = '6';
                                    }
                                    } //Faskes 2
                                    $found_data=true;
                                }
                            }
                        }
                    }
                    // $hasil_rujukan = json_encode($dataRujukan);
                    // var_dump($dataRujukan[0]);
                    // var_dump($dataRujukan[0]['noKunjungan']);
                    //CARI RUJUKAN REFERENSI

                    $this->Registration->whereFields['a.code_booking'] = $dt_post['code_booking'];
                    $dt_register_booking = $this->Registration->read(0, false);
                    // Check dt_register
                    /* Start Transaction */
                    $this->db->trans_start();
                    $DataPostSep = array();
                    $DataPostSep['request']['t_sep'] = array(
                        'noKartu'             => $data_booking->nomorkartu,
                        'tglSep'              => date('Y-m-d'),
                        'ppkPelayanan'        => '0070R032',
                        'jnsPelayanan'        => '2',
                        'klsRawat'            => 
                        array(
                        'klsRawatHak'       => $dataRujukan[0]['kodehakKelas'],
                        'klsRawatNaik'      => '',
                        'pembiayaan'        => '',
                        'penanggungJawab'   => ''
                        ),
                        'noMR'                => $data_booking->no_rm,
                        'rujukan'             => 
                        array(
                        'asalRujukan'       => $dataRujukan[0]['AsalRujukanFaskes'],
                        'tglRujukan'        => $dataRujukan[0]['tglKunjungan'],
                        'noRujukan'         => $data_booking->nomorreferensi,
                        'ppkRujukan'        => $dataRujukan[0]['ppkRujukan']
                        ),
                        'catatan'             => '',
                        'diagAwal'            => $dataRujukan[0]['kodediagnosa'],
                        'poli'                => 
                        array(
                        'tujuan'            => $data_booking->kodepoli,
                        'eksekutif'         => 0
                        ),
                        'cob'                 =>
                        array(
                            'cob'             => 0
                        ),
                        'katarak'             =>
                        array(
                        'katarak'           => 0
                        ),
                        'jaminan'             =>
                        array(
                        'lakaLantas'        => 0,
                        'noLP'              => $noLP,
                        'penjamin'          =>
                        array(),
                        // array(
                        //     'tglKejadian'     => $tglKejadian,
                        //     'keterangan'      => $keteranganKll,
                        //     'suplesi'         => 
                        //     array(
                        //     'suplesi'       => $suplesi,
                        //     'noSepSuplesi'  => $noSepSuplesi,
                        //     'lokasiLaka'    =>
                        //     array(
                        //         'kdPropinsi'  => $kdPropinsi,
                        //         'kdKabupaten' => $kdKabupaten,
                        //         'kdKecamatan' => $kdKecamatan
                        //     ),
                        //     ),
                        // ),
                        ),
                        'tujuanKunj'          => 0,
                        'flagProcedure'       => 0,
                        'kdPenunjang'         => "",
                        'assesmentPel'        => 4,
                        'skdp'                => 
                        array(
                        'noSurat'           => "",
                        'kodeDPJP'          => ""
                        ),
                        'dpjpLayan'           => $data_booking->kodedokter,
                        'noTelp'              => $data_booking->notelp,
                        'user'                => "admin",
                    );
                    // echo json_encode($DataPostSep);
                    // die();    
                    $hasil_sep = $this->vclaim_sep->request("/SEP/2.0/insert", "POST", json_encode($DataPostSep));
                    $dSEP = json_decode($hasil_sep, true);
                    $DSEPataGET = array(['noSEP' => $dSEP['response']['sep']['noSep'],
                                        'diagnosa' => $dSEP['response']['sep']['diagnosa'],
                                    'jnsPelayananNama' => $dSEP['response']['sep']['jnsPelayanan'],
                                    'kelasRawat' => $dSEP['response']['sep']['kelasRawat'],
                                    'asuransi' => $dSEP['response']['sep']['peserta']['asuransi'],
                                    'hakKelas' => $dSEP['response']['sep']['peserta']['hakKelas'],
                                    'jnsPeserta' => $dSEP['response']['sep']['peserta']['jnsPeserta'],
                                    'jnsPelayanan' => $dSEP['response']['sep']['peserta']['jnsPeserta'],
                                    'kelamin' => $dSEP['response']['sep']['peserta']['kelamin'],
                                    'code' => $dSEP['metaData']['code'],
                                    'message' => $dSEP['metaData']['message']]);
                    if ($dSEP->metaData->code == "200"){
                        $taskid = 3;
                        $data_antrean = array(
                            'kodebooking'       => $dt_post['code_booking'],
                            'taskid'            => $taskid,
                            'waktu'             => $this->tomiliseconds(date('Y-m-d H:i:s'))
                        );
                        $updatewaktu_antrean = $this->vclaim_antrol->request("/antrean/updatewaktu", "POST", json_encode($data_antrean));
                        $a = json_decode($updatewaktu_antrean, true);
                        // jika gagal update waktu antrean bpjs
                        if($a['metadata']['code'] != 200) {
                            $return_metadata = [
                                'message' => $a['metadata']['message'],
                                'code' => $a['metadata']['code']
                            ];
                            $message = [
                                'metadata' => $return_metadata,
                            ];
                            $this->response($message, REST_Controller::HTTP_CREATED);
                            return;
                        }
                        $this->db->trans_start();
                        $this->Registration_insurance->replace(array('registration_destination_id'=>$dt_register_booking[0]->id,'sep'=>$dSEP->response->sep->noSep,'sep_json'=>$d));
                        $this->db->trans_complete();
                    } else {
                        // var_dump(array('registration_destination_id'=>$dt_post['registration_destination_id'],'sep'=>$d->metaData->code));
                        $response_data = json_decode($d);
                        $this->REST_Return(201, 'SUCCESS', $response_data);
                        return;
                    }

                    $dt_checkin = array(
                        'id'                    => $dt_register_booking[0]->registration_id,
                        'is_booking'            => 1
                    );
                    $this->Registration->replace($dt_checkin);
                }
                $this->db->trans_complete();
                /* End Transaction */
                if ($this->db->trans_status() === false) {
                    $response_data = $this->db->trans_status();
                    $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                } else {

                    $response_data = array(
                        'id'         => $dt_register_booking[0]->id,
                        'norm'       => $dt_register_booking[0]->norm,
                        'name'       => $dt_register_booking[0]->name,
                        'dpjp'       => $dt_register_booking[0]->doctor_in_charge_name,
                        'destination'=> $dt_register_booking[0]->health_facility_item_name,
                        'sep'        => $d->response->sep->noSep.$dt_register_booking[0]->queue,
                        'queue'      => $dt_register_booking[0]->code_queue.'-'.$dt_register_booking[0]->queue
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

    public function action_put($p) {
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
            $this->form_validation->set_rules('id', 'id', 'trim|required');
            $this->form_validation->set_rules('loket_id', 'loket_id', 'trim|required');
            $this->form_validation->set_rules('action', 'action', 'trim|required');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $user_id = $this->security->xss_clean($is_valid_token['data']->id);
                // Check Helper_type_loket_id
                // $this->Queue_loket->whereFields['a.code'] = $dt_put['type_loket_id'];
                $dt_queue = $this->Queue_loket->read($id, false);
                if($dt_queue) {
                    if ($dt_put['action'] == 'call') {
                        $dt_queue_loket = array(
                            'id' => $id,
                            'loket_id' => $dt_put['loket_id'],
                            'date_task_2' => date('Y-m-d H:i:s'),
                        );
                    } else if ($dt_put['action'] == 'ok') {
                        $dt_queue_loket = array(
                            'id' => $id,
                            'flag' => 2,
                            'date_added' => null,
                            'date_added_ret' => $dt_queue[0]->date_added,
                            'loket_id' => $dt_put['loket_id']
                        );
                    } else if ($dt_put['action'] == 'close') {
                        $dt_queue_loket = array(
                            'id' => $id,
                            'flag' => 3,
                            'date_added' => null,
                            'date_added_ret' => $dt_queue[0]->date_added,
                            'loket_id' => $dt_put['loket_id']
                        );
                    }
                    $this->Queue_loket->replace($dt_queue_loket);
                    $this->db->trans_complete();
                    /* End Transaction */
                    if ($this->db->trans_status() === false) {
                        $response_data = $this->db->trans_status();
                        $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                    } else {
                        $response_data = array(
                            'id'     => $id
                        );
                        $this->REST_Return(201, 'SUCCESS', $response_data);
                    }
                } else {
                    $this->REST_Return(404, 'Wrong type_loket_id!');
                }
                return;     
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    private function tomiliseconds($tanggal)
    {
        date_default_timezone_set('Asia/Jakarta');
        return strtotime($tanggal) * 1000;
    }

    public function action_poli_put($p) {
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
            $this->form_validation->set_rules('id', 'id', 'trim|required');
            $this->form_validation->set_rules('action', 'action', 'trim|required');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $user_id = $this->security->xss_clean($is_valid_token['data']->id);
                // Check Helper_type_loket_id
                // $this->Registration->whereFields['a.code'] = $dt_put['type_loket_id'];
                $dt_queue = $this->Registration_destination->read($id, false);
                if($dt_queue) {
                    if ($dt_put['action'] == 'call') {
                        if($dt_queue[0]->code_booking && $dt_queue[0]->sts == 3){
                            // if($dt_queue[0]->code_booking && $dt_queue[0]->call_nurse_stamp && $dt_queue[0]->sts == 3){
                            $dt_Registration = array(
                                'id' => $dt_queue[0]->id,
                                'registration_id' => $dt_queue[0]->registration_id,
                                'health_facility_item_id' => $dt_queue[0]->health_facility_item_id,
                                'sts' => 4,
                                'date_task_4' => date('Y-m-d H:i:s'),
                                'call_doctor_stamp' => date('Y-m-d H:i:s')
                            );
                            $update_antrean_4 = array(
                                'kodebooking'       => $dt_queue[0]->code_booking,
                                'taskid'            => 4,
                                'waktu'             => $this->tomiliseconds(date("Y-m-d H:i:s"))
                            );
                            $this->load->library('Vclaim_antrol','vclaim_antrol');
                            $updatewaktu_antrean_4 = $this->vclaim_antrol->request("/antrean/updatewaktu", "POST", json_encode($update_antrean_4));
                            // $json_update_antrean_4 = json_decode($updatewaktu_antrean_4, true);
                            // $json_update_antrean_4 = json_decode($json_update_antrean_4);
                            // jika gagal update antrean bpjs
                            // var_dump($json_update_antrean_4);
                            // if($json_update_antrean_4->metadata->code != 200) {
                            //     // rollback
                            //     $this->db->trans_rollback();
                            //     $this->REST_Return(422, $json_update_antrean_4->metadata->message, $json_update_antrean_4->metadata->message);
                            //     return;
                            // }
                        }
                        if ($dt_queue[0]->health_facility_item_id == '38b10a7b-7006-451b-aa30-c11fa98635e0' || $dt_queue[0]->health_facility_item_id == 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e' || $dt_queue[0]->health_facility_item_id == '9c89d12e-63c1-4d1e-a082-e37bcadaeec1' || $dt_queue[0]->health_facility_item_id == 'f048cca5-d40f-4529-a4b1-c38e0451a4f3' || $dt_queue[0]->health_facility_item_id == 'cbd51a04-eb9b-4ddf-89fc-f5f2e2865144' || $dt_queue[0]->health_facility_item_id == 'd46e2eb6-85a9-4bd4-b515-a0858cc50c8f') {
                            $dt_Registration = array(
                                'id' => $dt_queue[0]->id,
                                'registration_id' => $dt_queue[0]->registration_id,
                                'health_facility_item_id' => $dt_queue[0]->health_facility_item_id,
                                'sts' => 4,
                                'date_task_4' => date('Y-m-d H:i:s'),
                                'call_doctor_stamp' => date('Y-m-d H:i:s')
                            );
                        }
                    } else if ($dt_put['action'] == 'ok') {
                        $dt_Registration = array(
                            'id' => $id,
                            'registration_id' => $dt_queue[0]->registration_id,
                            'health_facility_item_id' => $dt_queue[0]->health_facility_item_id,
                            'sts' => 5,
                        );
                    } else if ($dt_put['action'] == 'close') {
                        $dt_Registration = array(
                            'registration_id' => $dt_queue[0]->registration_id,
                            'health_facility_item_id' => $dt_queue[0]->health_facility_item_id,
                            'id' => $id,
                            'flag' => 5,
                        );
                    }
                    $this->Registration_destination->replace($dt_Registration);
                    $this->db->trans_complete();
                    /* End Transaction */
                    if ($this->db->trans_status() === false) {
                        $response_data = $this->db->trans_status();
                        $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                    } else {
                        $response_data = array(
                            'id'     => $id
                        );
                        $this->REST_Return(201, 'SUCCESS', $response_data);
                    }
                } else {
                    $this->REST_Return(404, 'Wrong type_loket_id!');
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
     * GET Regency with API
     * -------------------------
     * @method: GET
     */

    public function loket_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = "Loket";

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
            if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
            $this->$model->whereLike = $q_strings;
            $this->$model->whereFields['a.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['a.is_delete'] = '0';
            $output = $this->$model->read($id);
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

    public function display_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = "Loket";

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            $this->Loket->whereFields['a.institute_branch_id'] = $institute_branch_id;
            $output = $this->Loket->read(0, false);
            $output_call = array();
            for ($j = 0; $j < count($output); $j++){
                if ($id) {
                    $this->Queue_loket->whereFields['a.id'] = $id;
                }
                // $this->Queue_loket->whereFields['a.type_loket_id'] = array($loket->code);
                $this->Queue_loket->whereFields['a.date'] = date('Y-m-d');
                $this->Queue_loket->whereFields['a.loket_id'] = $output[$j]->id;
                $this->Queue_loket->whereFields['a.flag'] = 1;
                $output_queue = $this->Queue_loket->read(0, false)[0];
                $output[$j]->queue = $output_queue->code.'-'.$output_queue->queue;
                $output[$j]->code = $output_queue->code;
                $output[$j]->number = $output_queue->queue;
                if ($id) {
                    if ($output_queue->id == $id) {
                        $output_call = array(
                            'code' => $output_queue->code,
                            'loket' => $output_queue->loket,
                            'number' => $output_queue->queue
                        );
                    }
                }
            }
            $response_data = array();
            if ($id) {
                $response_data = $output_call;
                $error   = false;
            } else {
                $response_data = $output;
                $error   = false;
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

    public function display_poli_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        // $id     = $this->security->xss_clean($p);
        $id = isset($q_strings['id']) ? $q_strings['id'] : null;
        $model  = "Loket";

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            $dayNumber = date('N', strtotime(date('Y-m-d')));
            $this->Display_poli->whereFields['d.institute_branch_id'] = $institute_branch_id;
            $this->Display_poli->whereFields['a.display_id'] = $q_strings['display_id'];
            $this->Display_poli->whereFields['dsi.day'] = $dayNumber;
            $this->Display_poli->whereFields['d.is_delete'] = 0;
            $this->Display_poli->whereFields['a.is_delete'] = 0;
            $output = $this->Display_poli->read(0, false);
            $output_call = array();
            for ($j = 0; $j < count($output); $j++){
                if ($id) {
                    $this->Registration->whereFields['b.id'] = $id;
                }
                $this->Registration->whereFields['b.register_date >='] = date('Y-m-d').' 00:00:00';
                $this->Registration->whereFields['b.register_date <='] = date('Y-m-d').' 23:59:59';
                $this->Registration->whereFields['b.doctor_in_charge_id'] = $output[$j]->doctor_id;
                $this->Registration->whereFields['b.sts'] = 4;
                $output_queue = $this->Registration->read(0, false)[0];
                $output[$j]->queue = $output_queue->code_queue.'-'.$output_queue->queue;
                $output[$j]->code = $output_queue->code_queue;
                $output[$j]->number = $output_queue->queue;
                if ($id) {
                    if ($output_queue->registration_destination_id == $id) {
                        $output_call = array(
                            'code' => $output_queue->code_queue,
                            'number' => $output_queue->queue
                        );
                    }
                }
            }
            $response_data = array();
            if ($id) {
                $response_data = $output_call;
                $error   = false;
            } else {
                $response_data = $output;
                $error   = false;
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
     * GET Regency with API
     * -------------------------
     * @method: GET
     */

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
            if ($q_strings['flag']) {
                $this->$model->whereInFields['a.flag'] = $q_strings['flag'];
            }
            if ($q_strings['loket']) {
                $loket = $this->Loket->read($q_strings['loket'],false)[0];
                $loket_code = explode (",", $loket->code); 
                $integerIDs = array_map('intval', explode(',', $loket->code));
                $this->$model->whereInFields['a.type_loket_id'] = $integerIDs;
            }
            if(isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
                $this->$model->whereFields['a.date >='] = $q_strings['date_start'].' 00:00:00';
                $this->$model->whereFields['a.date <='] = $q_strings['date_end'].' 23:59:59';
            } else {
                $q_strings['date_start'] = date('Y-m-d');
                $q_strings['date_end'] = date('Y-m-d');
                $this->$model->whereFields['a.date >='] = $q_strings['date_start'].' 00:00:00';
                $this->$model->whereFields['a.date <='] = $q_strings['date_end'].' 23:59:59';
            }
            $this->$model->whereFields['a.institute_branch_id'] = $institute_branch_id;
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
}