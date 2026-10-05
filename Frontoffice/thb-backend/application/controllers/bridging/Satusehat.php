<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Satusehat extends My_Controller {
	// private $auth_url = "api-satusehat-stg.dto.kemkes.go.id/oauth2/v1";
	// private $base_url = "api-satusehat-stg.dto.kemkes.go.id/fhir-r4/v1";
	// private $consent_url = "api-satusehat-stg.dto.kemkes.go.id/consent/v1";
	// private $organization_id = "10085557";
	// private $client_id = "XU8oqOWYgNsJVP4SJIwgfhGkYXBGg0WQfZYV4a0CxG6aPkRb";
	// private $client_secret = "uTLrMUNge2esiVnhk76uKAKG5f0A2WkPSCQwOm6BmTVCA83cbpcPAeWdTkOvnlUo";
	private $auth_url = "api-satusehat.kemkes.go.id/oauth2/v1";
	private $base_url = "api-satusehat.kemkes.go.id/fhir-r4/v1";
	private $consent_url = "api-satusehat.kemkes.go.id/consent/v1";
	private $organization_id = "100025990";
	private $client_id = "YDnHJc2RmdMtFVJhANBQWr2FkPf4TSzuYG9WpJtBsGb2KvkK";
	private $client_secret = "xhXEvpUeY6vI1lQskwvws04pXwYL8PmXFaPjdFdQpqZt7LKGQJQBvtpvxSAWu4FY";
    public function __construct() {
        parent::__construct();
		$this->load->model("sdm/Unit_kerja_model", "Unit_kerja");
		$this->load->model("sdm/Unit_kerja_sub_model", "Unit_kerja_sub");
		$this->load->model("rekam_medis/Kedokteran_ecounter_model", "Kedokteran");
        $this->load->model("rekam_medis/Transaksi_icd9cm_model", "Transaksi_icd9cm");
        $this->load->model("rekam_medis/Transaksi_icd10_model", "Transaksi_icd10");
		$this->load->model("rekam_medis/Keperawatan_model", "Keperawatan");
		$this->load->model("medis/Pendaftaranrj_model", "Pendaftaran");
        $this->load->library('Satusehat','satusehat');
    }
    public function index () {
        $this->get_token();
        $this->render('index');
    }

	public function get_token($p='0'){
		$this->url = "https://".$this->auth_url."/accesstoken?grant_type=client_credentials";
		$arrheader = array('Content-Type: application/x-www-form-urlencoded');
		$curl = curl_init();
		$data = "client_id=YDnHJc2RmdMtFVJhANBQWr2FkPf4TSzuYG9WpJtBsGb2KvkK&client_secret=xhXEvpUeY6vI1lQskwvws04pXwYL8PmXFaPjdFdQpqZt7LKGQJQBvtpvxSAWu4FY";
		curl_setopt($curl, CURLOPT_URL, $this->url);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $arrheader);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($curl);	
        $err = curl_error($curl);
        curl_close ($curl);
		// var_dump($this->url);	
		// echo "<pre>";
		// var_dump($err);
		// var_dump($result);
		// var_dump(json_decode($result, true));
		// echo "</pre>";
		// die();
		return $this->session->set_userdata('authotize_satusehat', json_decode($result, true));
	}

	public function get_token_from_session($p='0'){
		echo "<pre>";
		var_dump($this->session->userdata('authotize_satusehat'));
		echo "</pre>";
		var_dump($this->session->userdata('authotize_satusehat')['access_token']);
	}

    public function organization () {
        $this->title = 'Organisasi Satu Sehat';
        $this->set('primaryKey', $this->Unit_kerja->primaryKey);
        $this->render('organization');
    }

    public function location () {
        $this->title = 'Lokasi Satu Sehat';
        $this->set('primaryKey', $this->Unit_kerja_sub->primaryKey);
        $this->render('location');
    }

    public function ecounter () {
        $this->title = 'Ecounter Satu Sehat';
        $this->set('primaryKey', $this->Kedokteran->primaryKey);
        $this->render('ecounter');
    }
	
	public function read($a = 'view', $p = '0') {
        $columns = array(
            array( "name" => "date_added_pendaftaran", "data" => "date_added_pendaftaran"),
            array( "name" => "kd_trx_pendaftaran", "data" => "kd_trx_pendaftaran"),
            array( "name" => "no_rm", "data" => "no_rm"),
            array( "name" => "nm_mst_pasien", "data" => "nm_mst_pasien"),
            array( "name" => "no_ktp", "data" => "no_ktp"),
            array( "name" => "no_satusehat", "data" => "no_satusehat"),
            array( "name" => "nm_mst_pegawai_lengkap", "data" => "nm_mst_pegawai_lengkap"),
            array( "name" => "nm_mst_unit_kerja_sub", "data" => "nm_mst_unit_kerja_sub"),
            array( "name" => "id_location", "data" => "id_location"),
            array( "name" => "sts_rawat", "data" => "sts_rawat"),
            array( "name" => "date_pulang", "data" => "date_pulang"),
            array( "name" => "nm_icd_icd10", "data" => "nm_icd_icd10"),
            array( "name" => "catatan", "data" => "catatan"),
            array( "name" => "action", "data" => array("_" => "action", "order" => false), "searchable" => false),

        );          
        $columnDefs = array(
            array( "targets" => array( 8 ), "orderable" => false),
        );
        if (is_ajax()):            
            if ($a == 'json_organization') {
                echo json_encode($this->Unit_kerja->read($p));
                return;
            } else if ($a == 'json_location') {
                echo json_encode($this->Unit_kerja_sub->read($p));
                return;
            
            } else if ($a == 'jsonecounter') {              
                $dtPost = $this->input->post();
                $array_data = array();
                $primaryKey = $this->Kedokteran->primaryKey;
                
                if(!empty($dtPost['tgl_daftar_awal']) && !empty($dtPost['tgl_daftar_akhir'])){
                    $this->Kedokteran->whereFields['a.date_added >=']=$dtPost['tgl_daftar_awal'];
                    $this->Kedokteran->whereFields['a.date_added <=']=$dtPost['tgl_daftar_akhir'];
                }else{
                    if($heed == false){
                        $this->Kedokteran->whereFields['a.date_added >=']=date('Y-m-d');
                        $this->Kedokteran->whereFields['a.date_added <=']=date('Y-m-d');
                    }
                }
                // $this->Kedokteran->whereFields['r.id_mst_unit_kerja_ref'] = 34;
                // $this->Kedokteran->whereFields['a.date_added >=']=date('Y-m-d'.' 00:00:00' );
                // $this->Kedokteran->whereFields['a.date_added <=']=date('Y-m-d'.' 23:59:59' );
                $return = $this->Kedokteran->dataTable_Pendaftaran('0', $columns, $columnDefs);
                
                if (count($return['data']) > 0):                    
                    $xa=1;
                    foreach ($return['data'] as $i => $r):     
                        $r->DT_RowId = $r->id_trx_kedokteran;
                        if ($r->catatan) {
                            if ($r->flag_naikKelas == 1) {
                                $r->action = '<a class="btn btn-sm btn-success btn-xs" href="javascript:void(0)" onclick="do_action(this)" id_data="'.$r->id_trx_kedokteran.'" call-flag="call" title="Kirim" data-container="body" data-toggle="tooltip" data-trigger="hover"><i class="fa fa-volume-control-phone" aria-hidden="true"></i> Condition</a>';
                            } else if ($r->flag_naikKelas == 3) {
                                $r->action = '<a class="btn btn-sm btn-success btn-xs" href="javascript:void(0)" onclick="do_action(this)" id_data="'.$r->id_trx_kedokteran.'" call-flag="call" title="Kirim" data-container="body" data-toggle="tooltip" data-trigger="hover"><i class="fa fa-volume-control-phone" aria-hidden="true"></i> Pulang</a>';
                            } else {
                                $r->action = '<a class="btn btn-sm btn-success btn-xs" href="javascript:void(0)" onclick="do_action(this)" id_data="'.$r->id_trx_kedokteran.'" call-flag="call" title="Kirim" data-container="body" data-toggle="tooltip" data-trigger="hover"><i class="fa fa-volume-control-phone" aria-hidden="true"></i> Masuk Ruang</a>';
                            }
                        } else {
                            $r->action = '<a class="btn btn-sm btn-success btn-xs" href="javascript:void(0)" onclick="do_action(this)" id_data="'.$r->id_trx_kedokteran.'" call-flag="call" title="Kirim" data-container="body" data-toggle="tooltip" data-trigger="hover"><i class="fa fa-volume-control-phone" aria-hidden="true"></i> Kunjungan Baru</a>';
                        }
                        $r->sts_rawat = "Sudah"; 
                        array_push($array_data, $r);
                    endforeach;
                endif;
                
                $return['data'] = $array_data;
                echo json_encode($return);
                return;
            } else if ($a == 'arrayorganizationp') {
                return $this->Unit_kerja->read($p);
            } else if ($a == 'arraylocationp') {
                return $this->Unit_kerja_sub->read($p);
            } else if ($a == 'listorganization') {
                $this->set('primaryKey', $this->Unit_kerja->primaryKey);
                $this->Unit_kerja->whereFields['a.' . 'flag_organization'] = 1;
                $this->set('rows', $this->Unit_kerja->read($p));
				$this->set('fieldsList', $this->Unit_kerja->get_field_list());
                $this->load->view($this->directory.ucwords($this->name)."/{$a}", $this->data);
				return;
            } else if ($a == 'listecounter') {
                $this->set('columns', $columns);
				$this->set('columnDefs', $columnDefs);
                $this->load->view($this->directory.ucwords($this->name)."/{$a}", $this->data);
				return;
            } else if ($a == 'listlocation') {
                $this->set('primaryKey', $this->Unit_kerja_sub->primaryKey);
                // $this->Unit_kerja_sub->whereFields['a.' . 'flag_location'] = 1;
                $this->set('rows', $this->Unit_kerja_sub->read($p));
				$this->set('fieldsList', $this->Unit_kerja_sub->get_field_list());
                $this->load->view($this->directory.ucwords($this->name)."/{$a}", $this->data);
				return;
            }
        endif;
        redirect($this->directory."$this->name");
		return;
	}
		
	public function create($a = 'view', $p = null) {
        if ($a == 'createorganization') {
            $model = 'Unit_kerja';
            if (is_ajax()):	
                if (COUNT($this->input->post()) > 0) {
                    if ($p == null || $p == 0) {
                    	$data = array();
                    	$data['resourceType'] = 'Organization';
                    	$data['active'] = true;
                    	$data['identifier'] = array([
                            "use"=> "official",
                            "system"=> "http://sys-ids.kemkes.go.id/organization/".$this->organization_id,
                            "value"=> "SS-UKP"
                        ]);
                    	$data['type'] = array([
                            "coding"=> array([
                                "system"=>"http://terminology.hl7.org/CodeSystem/organization-type",
                                "code"=>"team",
                                "display"=>"Organizational team"
                            ]),
                    	]);
                    	$data['name'] = $this->input->post('nm_mst_unit_kerja');
                    	// $data['partOf']['reference'] =  "Organization/".$this->organization_id;
                    	// $data['partOf']['display'] =  "Puskesmas SATUSEHAT";
                        $this->url = "https://".$this->base_url."/Organization";
		                $arrheader = array('Content-type: application/json','Authorization: Bearer '.$this->session->userdata('authotize_satusehat')['access_token']);
                        $curl = curl_init();
                        curl_setopt($curl, CURLOPT_URL, $this->url);
                        curl_setopt($curl, CURLOPT_POST, 1);
                        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                        curl_setopt($curl, CURLOPT_HTTPHEADER, $arrheader);
                        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        $result = curl_exec($curl);	
                        $err = curl_error($curl);
                        curl_close ($curl);
                        echo "<pre>";
                        var_dump(json_decode($result, true));
                        echo "</pre>";
                        die();
                        $dtPost = $this->input->post();
                        $result = json_decode($result, true);
                        if ($result['id']) {
                            $dtPost = array_merge($dtPost,array('id_organization'=>$result['id']));
                            $ret = $this->$model->create($dtPost);
                        } else {
                            $this->setFlash('FAILED: ', 'Failed save data '.ucwords($this->name).'. Contact your IT Support.', 'alert-danger');
                            return;
                        }
                    } else {
                        $ret = $this->$model->update($p);
                    } 
                    
                    if (!$ret) {
                        $this->setFlash('FAILED: ', 'Failed save data '.ucwords($this->name).'. Contact your IT Support.', 'alert-danger');
                    } else $this->setFlash('SUCCESS', 'Success save data '.ucwords($this->name), 'alert-success');
                    echo json_encode(array($ret, $this->session->flashdata('flashMessage'), $this->input->post()));
                    return;
                }
                
                if ($p != null && $p != 0) $this->set('rows', $this->$model->read($p)[0]);
                else $this->set('rows', array());
                
                $this->set('primaryKey', $this->$model->primaryKey);
                $this->set('fieldsList', $this->$model->get_field_list());
                $this->load->view($this->directory."$this->name/{$a}", $this->data);
                return;
            endif;
            redirect($this->directory."$this->name"."/organization");
        } else if ($a == 'createlocation') {
            $model = 'Unit_kerja_sub';
            if (is_ajax()):	
                if (COUNT($this->input->post()) > 0) {
                    $data = array();
                    $data['resourceType'] = 'Location';
                    $data['identifier'] = array([
                        "use"=> "official",
                        "system"=> "http://sys-ids.kemkes.go.id/location/".$this->organization_id,
                        "value"=> "SS-UKP-POLI-ROOM"
                    ]);
                    $data['status'] = "active";
                    $data['name'] = $this->input->post('nm_mst_unit_kerja_sub');
                    $data['description'] = $this->input->post('nm_mst_unit_kerja_sub');
                    $data['position']['longitude'] = floatval($this->input->post('longitude'));
                    $data['position']['latitude'] = floatval($this->input->post('latitude'));
                    $data['position']['altitude'] = floatval($this->input->post('altitude'));
                    $data['mode'] = "instance";
                    $data['physicalType']["coding"] = array([
                            "system"=>"http://terminology.hl7.org/CodeSystem/location-physical-type",
                            "code"=>"ro",
                            "display"=>"Room"
                    ]);
                    $data['managingOrganization']['reference'] = "Organization/".$this->Unit_kerja->read($this->input->post('id_mst_unit_kerja'))[0]->id_organization;
                    // $data['partOf']['reference'] =  "Organization/".$this->organization_id;
                    // $data['partOf']['display'] =  "Puskesmas SATUSEHAT";
                    $this->url = "https://".$this->base_url."/Location";
                    $arrheader = array('Content-type: application/json','Authorization: Bearer '.$this->session->userdata('authotize_satusehat')['access_token']);
                    if ($this->input->post('id_location') == "") {
                        $curl = curl_init();
                        curl_setopt($curl, CURLOPT_URL, $this->url);
                        curl_setopt($curl, CURLOPT_POST, 1);
                        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                        curl_setopt($curl, CURLOPT_HTTPHEADER, $arrheader);
                        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        $result = curl_exec($curl);	
                        $err = curl_error($curl);
                        curl_close ($curl);
                        $dtPost = $this->input->post();
                        $result = json_decode($result, true);
                        $dtPost = array_merge($dtPost,array('id_location'=>$result['id']));
                    }
                    if ($p == null || $p == 0) {
                        if ($result['id']) {
                            $ret = $this->$model->create($dtPost);
                        } else {
                            $this->setFlash('FAILED: ', 'Failed save data '.ucwords($this->name).'. Contact your IT Support.', 'alert-danger');
                            return;
                        }
                    } else {
                        $ret = $this->$model->update($p,$dtPost);
                    } 
                    
                    if (!$ret) {
                        $this->setFlash('FAILED: ', 'Failed save data '.ucwords($this->name).'. Contact your IT Support.', 'alert-danger');
                    } else $this->setFlash('SUCCESS', 'Success save data '.ucwords($this->name), 'alert-success');
                    echo json_encode(array($ret, $this->session->flashdata('flashMessage'), $this->input->post()));
                    return;
                }
                
                if ($p != null && $p != 0) $this->set('rows', $this->$model->read($p)[0]);
                else $this->set('rows', array());
                
                $this->set('primaryKey', $this->$model->primaryKey);
                $this->set('fieldsList', $this->$model->get_field_list());
                if ($p != null && $p != 0) {
                    $this->load->view($this->directory."$this->name/updatelocation", $this->data);
                } else {
                    $this->load->view($this->directory."$this->name/createlocation", $this->data);
                }
                return;
            endif;
            redirect($this->directory."$this->name"."/location");
        } else if ($a == 'sendecounter') {
            // $timestamp = \DateTime::createFromFormat('Y-m-d H:i', $dataPatient->date_added_pendaftaran)->getTimestamp();
            $model = 'Kedokteran';
            $trx_detail_6 = 'Transaksi_icd9cm';             /* << Ganti nama trx detail 6 >> */
            $trx_detail_7 = 'Transaksi_icd10';              /* << Ganti nama trx detail 7 >> */
            $this->set('trx_detail_6', $trx_detail_6);
            $this->set('trx_detail_7', $trx_detail_7);
            if (is_ajax()):	
                $dataPatient = $this->$model->read($p,false)[0];
                $timestamp = $dataPatient->date_added_pendaftaran;
                if (COUNT($this->input->post()) > 0) {
                    $data = array();
                    if ($dataPatient->catatan) {
                        if ($dataPatient->flag_naikKelas == 1) {
                            $data['resourceType'] = 'Condition';
                            $data['clinicalStatus']['coding'] = array([
                                "system"=> "http://terminology.hl7.org/CodeSystem/condition-clinical",
                                "code"=> "active",
                                "display"=> "Active"
                            ]);
                            $data['category'] = array([
                                "coding"=> array([
                                    "system"=> "http://terminology.hl7.org/CodeSystem/condition-category",
                                    "code"=> "encounter-diagnosis",
                                    "display"=> "Encounter Diagnosis"
                                ])
                            ]);
                            $data['code']['coding'] = array([
                                "system"=> "http://hl7.org/fhir/sid/icd-10",
                                "code"=> $dataPatient->kd_mst_icd10,
                                "display"=> $dataPatient->nm_icd_icd10
                            ]);
                            $data['subject']['reference'] = "Patient/".$this->input->post('no_satusehat_pasien');
                            $data['subject']['display'] = $dataPatient->nm_mst_pasien;
                            $data['encounter']['reference'] = "Encounter/".$dataPatient->catatan;
                            $data['onsetDateTime'] =  date("Y-m-d\TH:i:sP", strtotime($dataPatient->date_added));
                            $data['recordedDate'] =  date("Y-m-d\TH:i:sP", strtotime($dataPatient->date_added));
                            // $data['recorder']['reference'] = "Practitioner/".$dataPatient->no_satusehat;
                            // $data['recorder']['display'] = $dataPatient->nm_mst_pegawai_lengkap;
                            // $data['note'] = array([
                            //     "text"=>"Batuk Berdarah sejak 3bl yll"
                            // ]);
                            $this->url = "https://".$this->base_url."/Condition";
                            $arrheader = array('Content-type: application/json','Authorization: Bearer '.$this->session->userdata('authotize_satusehat')['access_token']);
                            // echo "<pre>";
                            // var_dump($data);
                            // echo "</pre>";
                            // die();// echo "<pre>";
                            // var_dump($data);
                            // echo "</pre>";
                            // die();
                            // if ($this->input->post('id_ecounter') == "") {
                                $curl = curl_init();
                                curl_setopt($curl, CURLOPT_URL, $this->url);
                                curl_setopt($curl, CURLOPT_POST, 1);
                                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                                curl_setopt($curl, CURLOPT_HTTPHEADER, $arrheader);
                                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                                $result = curl_exec($curl);	
                                $err = curl_error($curl);
                                curl_close ($curl);
                                $dtPost = $this->input->post();
                                $result = json_decode($result, true);
                                // $dtPost = array_merge($dtPost,array('id_ecounter'=>$result['id']));
                                var_dump($result);
                                // die();
                            // }
                            if ($result['id']) {
                                $this->db->trans_start();
                                $ret = $this->Pendaftaran->update($dataPatient->id_trx_pendaftaran, array('flag_naikKelas'=>3));
                                $this->db->trans_complete();
                            } else {
                                $this->setFlash('FAILED: ', 'Failed save data '.ucwords($this->name).'. Contact your IT Support.', 'alert-danger');
                                return;
                            }
                        } else if ($dataPatient->flag_naikKelas == 3) {
                            
                        } else {
                            $data['resourceType'] = 'Encounter';
                            $data['id'] = $dataPatient->catatan;
                            $data['identifier'] = array([
                                "system"=>"http://sys-ids.kemkes.go.id/encounter/".$this->organization_id,
                                "value"=>$dataPatient->kd_trx_pendaftaran
                            ]);
                            $data['status'] = "in-progress";
                            $data['class']['system'] = "http://terminology.hl7.org/CodeSystem/v3-ActCode";
                            $data['class']['code'] = "AMB";
                            $data['class']['display'] = "ambulatory";
                            $data['subject']['reference'] = "Patient/".$this->input->post('no_satusehat_pasien');
                            $data['subject']['display'] = $dataPatient->nm_mst_pasien;
                            $data['participant'] = array([
                                "type"=> array([
                                    "coding"=> array([
                                        "system"=>"http://terminology.hl7.org/CodeSystem/v3-ParticipationType",
                                        "code"=>"ATND",
                                        "display"=>"attender"
                                    ])
                                ]),
                                "individual"=> array(
                                    "reference"=> "Practitioner/".$dataPatient->no_satusehat,
                                    "display"=> $dataPatient->nm_mst_pegawai_lengkap
                                ),
                            ]);
                            $data['period']['start'] = date("Y-m-d\TH:i:sP", strtotime($timestamp));
                            $data['location'] = array([
                                "location"=> array(
                                    "reference"=>"Location/".$dataPatient->id_location,
                                    "display"=>$dataPatient->nm_mst_unit_kerja_sub
                                ),
                                "period"=> array(
                                    "start"=> date("Y-m-d\TH:i:sP", strtotime($timestamp))
                                ),
                                "extension"=> array([
                                    "url"=>"https://fhir.kemkes.go.id/r4/StructureDefinition/ServiceClass",
                                    "extension"=>array([
                                        "url"=>"value",
                                        "valueCodeableConcept"=>array(
                                            "coding"=> array([
                                                "system"=>"http://terminology.kemkes.go.id/CodeSystem/locationServiceClass-Outpatient",
                                                "code"=>"reguler",
                                                "display"=>"Kelas Reguler"
                                            ])
                                        )
                                    ],
                                    [
                                        "url"=>"upgradeClassIndicator",
                                        "valueCodeableConcept"=>array(
                                            "coding"=> array([
                                                "system"=>"http://terminology.kemkes.go.id/CodeSystem/locationUpgradeClass",
                                                "code"=>"kelas-tetap",
                                                "display"=>"Kelas Tetap Perawatan"
                                            ])
                                        )
                                    ])
                                ])
                            ]);
                            $data['statusHistory'] = array([
                                "status"=>"arrived",
                                "period"=>array("start"=>date("Y-m-d\TH:i:sP", strtotime($timestamp))),
                            ],
                            [
                                "status"=>"in-progress",
                                "period"=>array("start"=>date("Y-m-d\TH:i:sP", strtotime("+30 minutes",strtotime($timestamp)))),
                            ]);
                            $data['serviceProvider']['reference'] = "Organization/".$this->organization_id;
        
                            $this->url = "https://".$this->base_url."/Encounter/".$dataPatient->catatan;
                            $arrheader = array('Content-type: application/json','Authorization: Bearer '.$this->session->userdata('authotize_satusehat')['access_token']);
                            // echo "<pre>";
                            // var_dump($data);
                            // echo "</pre>";
                            // die();
                            // if ($this->input->post('id_ecounter') == "") {
                                $curl = curl_init();
                                curl_setopt($curl, CURLOPT_URL, $this->url);
                                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                                curl_setopt($curl, CURLOPT_HTTPHEADER, $arrheader);
                                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                                $result = curl_exec($curl);	
                                $err = curl_error($curl);
                                curl_close ($curl);
                                $dtPost = $this->input->post();
                                $result = json_decode($result, true);
                                // $dtPost = array_merge($dtPost,array('id_ecounter'=>$result['id']));
                                // var_dump($result);
                                // die();
                            // }
                            if ($result['id']) {
                                $this->db->trans_start();
                                $ret = $this->Pendaftaran->update($dataPatient->id_trx_pendaftaran, array('flag_naikKelas'=>1));
                                $this->db->trans_complete();
                            } else {
                                $this->setFlash('FAILED: ', 'Failed save data '.ucwords($this->name).'. Contact your IT Support.', 'alert-danger');
                                return;
                            }
                        }
                    } else {
                        $data['resourceType'] = 'Encounter';
                        $data['identifier'] = array([
                            "system"=>"http://sys-ids.kemkes.go.id/encounter/".$this->organization_id,
                            "value"=>$dataPatient->kd_trx_pendaftaran
                        ]);
                        $data['status'] = "arrived";
                        $data['class']['system'] = "http://terminology.hl7.org/CodeSystem/v3-ActCode";
                        $data['class']['code'] = "AMB";
                        $data['class']['display'] = "ambulatory";
                        $data['subject']['reference'] = "Patient/".$this->input->post('no_satusehat_pasien');
                        $data['subject']['display'] = $dataPatient->nm_mst_pasien;
                        $data['participant'] = array([
                            "type"=> array([
                                "coding"=> array([
                                    "system"=>"http://terminology.hl7.org/CodeSystem/v3-ParticipationType",
                                    "code"=>"ATND",
                                    "display"=>"attender"
                                ])
                            ]),
                            "individual"=> array(
                                "reference"=> "Practitioner/".$dataPatient->no_satusehat,
                                "display"=> $dataPatient->nm_mst_pegawai_lengkap
                            ),
                        ]);
                        $data['period']['start'] = date("Y-m-d\TH:i:sP", strtotime($timestamp));
                        $data['location'] = array([
                            "location"=> array(
                                "reference"=>"Location/".$dataPatient->id_location,
                                "display"=>$dataPatient->nm_mst_unit_kerja_sub
                            ),
                            "period"=> array(
                                "start"=> date("Y-m-d\TH:i:sP", strtotime($timestamp))
                            ),
                            "extension"=> array([
                                "url"=>"https://fhir.kemkes.go.id/r4/StructureDefinition/ServiceClass",
                                "extension"=>array([
                                    "url"=>"value",
                                    "valueCodeableConcept"=>array(
                                        "coding"=> array([
                                            "system"=>"http://terminology.kemkes.go.id/CodeSystem/locationServiceClass-Outpatient",
                                            "code"=>"reguler",
                                            "display"=>"Kelas Reguler"
                                        ])
                                    )
                                ],
                                [
                                    "url"=>"upgradeClassIndicator",
                                    "valueCodeableConcept"=>array(
                                        "coding"=> array([
                                            "system"=>"http://terminology.kemkes.go.id/CodeSystem/locationUpgradeClass",
                                            "code"=>"kelas-tetap",
                                            "display"=>"Kelas Tetap Perawatan"
                                        ])
                                    )
                                ])
                            ])
                        ]);
                        $data['statusHistory'] = array([
                            "status"=>"arrived",
                            "period"=>array("start"=>date("Y-m-d\TH:i:sP", strtotime($timestamp))),
                        ]);
                        $data['serviceProvider']['reference'] = "Organization/".$this->organization_id;
    
                        $this->url = "https://".$this->base_url."/Encounter";
                        $arrheader = array('Content-type: application/json','Authorization: Bearer '.$this->session->userdata('authotize_satusehat')['access_token']);
                        // echo "<pre>";
                        // var_dump($data);
                        // echo "</pre>";
                        // die();
                        // if ($this->input->post('id_ecounter') == "") {
                            $curl = curl_init();
                            curl_setopt($curl, CURLOPT_URL, $this->url);
                            curl_setopt($curl, CURLOPT_POST, 1);
                            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                            curl_setopt($curl, CURLOPT_HTTPHEADER, $arrheader);
                            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                            $result = curl_exec($curl);	
                            $err = curl_error($curl);
                            curl_close ($curl);
                            $dtPost = $this->input->post();
                            $result = json_decode($result, true);
                            // $dtPost = array_merge($dtPost,array('id_ecounter'=>$result['id']));
                            // var_dump($result);
                            // die();
                        // }
                        if ($result['id']) {
                            $this->db->trans_start();
                            $ret = $this->Pendaftaran->update($dataPatient->id_trx_pendaftaran, array('catatan'=>$result['id']));
                            $this->db->trans_complete();
                        } else {
                            $this->setFlash('FAILED: ', 'Failed save data '.ucwords($this->name).'. Contact your IT Support.', 'alert-danger');
                            return;
                        }
                    }
                    
                    if (!$ret) {
                        $this->setFlash('FAILED: ', 'Failed save data '.$result['id'].ucwords($this->name).'. Contact your IT Support.', 'alert-danger');
                    } else $this->setFlash('SUCCESS', 'Success save data '.$result['id'].ucwords($this->name), 'alert-success');
                    echo json_encode(array($ret, $this->session->flashdata('flashMessage'), $this->input->post()));
                    return;
                }
                
                if ($p != null && $p != 0) $this->set('rows', $this->$model->read($p)[0]);
                else $this->set('rows', array());
                
                $this->set('primaryKey', $this->$model->primaryKey);
                $this->set('fieldsList', $this->$model->get_field_list());
                if ($p != null && $p != 0) {
                    $this->url = "https://".$this->base_url."/Patient?identifier=https://fhir.kemkes.go.id/id/nik|".$this->$model->read($p)[0]->no_ktp;
                    $arrheader = array('Content-type: application/json','Authorization: Bearer '.$this->session->userdata('authotize_satusehat')['access_token']);
                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_URL, $this->url);
                    curl_setopt($curl, CURLOPT_HTTPHEADER, $arrheader);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    $result = curl_exec($curl);	
                    $err = curl_error($curl);
                    curl_close ($curl);
                    $result = json_decode($result, true);
                    // var_dump($result['entry'][0]['resource']['id']);
                    // var_dump($result);die();
                    $this->set('no_satusehat_pasien', $result['entry'][0]['resource']['id']);
                    $this->Keperawatan->whereFields['a.id_trx_pendaftaran'] = $dataPatient->id_trx_pendaftaran;
                    $this->set('nurse', $this->Keperawatan->read(0,false)[0]);
                    
                    $this->$trx_detail_6->whereFields['a.' . 'id_trx_kedokteran'] = $p;  /* << Ganti FK >> */
                    $this->set('trx'. $trx_detail_6, $this->$trx_detail_6->read(0,false));

                    $this->$trx_detail_7->whereFields['a.' . 'id_trx_kedokteran'] = $p;  /* << Ganti FK >> */
                    $this->set('trx'. $trx_detail_7, $this->$trx_detail_7->read(0,false));
                    $this->load->view($this->directory."$this->name/sendecounter", $this->data);
                } else {
                    $this->load->view($this->directory."$this->name/createlocation", $this->data);
                }
                return;
            endif;
            redirect($this->directory."$this->name"."/ecounter");
        }
	}


}
?>