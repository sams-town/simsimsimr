<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Bpjs extends My_Controller {
  public function __construct() {
  parent::__construct();
    $this->load->model("medis/Pendaftaranrj_model", "Pendaftaranrjigd");
    $this->load->model("{$this->directory}/Sep_model", "SEP");
    $this->load->model("{$this->directory}/Sep_model", "SEP_HISTORY");
    $this->load->model("{$this->directory}/Rujukan_model", "Rujukan");
    $this->load->model("{$this->directory}/Pendaftaran_model", "Pendaftaran");
    $this->load->model("{$this->directory}/Pengajuan_model", "pengajuan");
    $this->load->model("{$this->directory}/Trx_klaim", "klaim");
    $this->load->model("{$this->directory}/Peserta_list_model", "Peserta_list");
    $this->load->model("billing/Rincian_tempat_tidur_model", "Ri_tempat_tidur");
    $this->load->model("sdm/Pegawai_model",'pegawai');
    // $this->load->model("medis/Tempat_tidur_model",'trx_tempat_tidur');
    $this->load->model("medis/Tempat_tidurs_model", "Tempat_tidur");
    $this->load->model("medis/Rincian_model", "Rincian");
    $this->load->model("medis/Cara_bayar_model", "Cara_bayar");
    $this->load->model("rawat_inap/Tempat_tidur_model", "Mst_tempat_tidur");
    $this->load->model("medis/Dokter_pelaksana_model", "Dokter_pelaksana");
    $this->load->model("rawat_inap/kamar_jenis_model", "Mst_kamar_jenis");
    $this->load->model("rawat_inap/kamar_model", "Master_kamar");
    $this->load->model('keuangan/Trx_pendapatan_model','pendapatan');
    $this->load->model("{$this->directory}/md_trx_eklaim_model",'trx_eklaim');
    $this->load->model('rekam_medis/Icd10_model','Diagnosa');
    $this->load->model('rekam_medis/Icd9cm_model','Tindakan');
    $this->load->model('billing/Pelunasan_model','Pelunasan');
    $this->load->model("rekam_medis/icd10_model", "icd10_model");
    $this->load->helper('master_helper');
    $this->load->library('Vclaim','vclaim');
    $this->load->library('Vclaim_sep','vclaim_sep');
    $this->load->library('eklaim');
    $this->title = 'Bridging BPJS';
  }

  public function index(){
    $this->render('index');
  }

  public function read_faskes($a = 'view', $p = '0') {
    if (is_ajax()):
      if ($a == 'json') {
        $hasil = $this->vclaim_sep->request("/referensi/faskes/".$p."/2", "GET");
        $d = json_decode($hasil, true);
        foreach ($d['response']['faskes'] as $key => $value) {
          $dataFaskes[$key] = array(['kodetujuanRujukan' => $value['kode'],'namatujuanRujukan' => $value['nama']]);
        }
        echo json_encode($dataFaskes);
        return;
      }
    endif;
  }

  public function read_history_peserta($p = '0', $datem = '0', $datea = '0') {
    $hasil = $this->vclaim_sep->request("/monitoring/HistoriPelayanan/NoKartu/".$p."/tglMulai/".$datem."/tglAkhir/".$datea, "GET");
    // $hasil = $this->vclaim_sep->request("/Peserta/nokartu/0000046292692/tglSEP/2018-09-19", "GET");
    $d = json_decode($hasil, true);
    // var_dump($d);
    foreach ($d['response']['histori'] as $key => $value) {
      $dataPesertaHistory[$key] = array(['diagnosa' => $value['diagnosa'],'jnsPelayanan' => $value['jnsPelayanan'],'kelasRawat' => $value['kelasRawat'],'namaPeserta' => $value['namaPeserta'],'noKartu' => $value['noKartu'],'noSep' => $value['noSep'],'poli' => $value['poli'],'ppkPelayanan' => $value['ppkPelayanan'],'tglPlgSep' => $value['tglPlgSep'],'tglSep' => $value['tglSep']]);
    }
    echo json_encode($dataPesertaHistory);
    return;
  }

  public function read_dpjp($a = 'view', $p = '0', $i = '0') {
    if (is_ajax()):
      if ($a == 'json') {
        $jenisPelayanan = $i;
        $tgl = date("Y-m-d");
        $poli = $p;
        $hasil = $this->vclaim_sep->request("/referensi/dokter/pelayanan/".$jenisPelayanan."/tglPelayanan/".$tgl."/Spesialis/".$poli, "GET");
        $d = json_decode($hasil, true);
        foreach ($d['response']['list'] as $key => $value) {
          $dataFaskes[$key] = array(['dpjp' => $value['kode'],'namadpjp' => $value['nama']]);
        }
        echo json_encode($dataFaskes);
        return;
      }
    endif;
  }

  public function suplesi($a = 'view', $p = '0') {
    if (is_ajax()):
      if ($a == 'json') {
        $tgl  = date("Y-m-d");
        $hasil = $this->vclaim_sep->request("/sep/JasaRaharja/Suplesi/".$p."/tglPelayanan/".$tgl, "GET");
        // echo json_encode($dataFaskes);
        // $hasil = '{"metaData": 
        //                   {
        //                       "code": "200",
        //                       "message": "Sukses"
        //                   },
        //                 "response": 
        //                     {
        //                     "jaminan": 
        //                         [
        //                             {
        //                                 "noRegister": "1234",
        //                                 "noSep": "0301R0110818V000008",
        //                                 "noSepAwal": "0301R0110818V000008",
        //                                 "noSuratJaminan": "-",
        //                                 "tglKejadian": "2018-08-06",
        //                                 "tglSep": "2018-08-08"                                    
        //                             },
        //                                             {
        //                                 "noRegister": "44222",
        //                                 "noSep": "0301R0110818V000018",
        //                                 "noSepAwal": "0301R0110818V000008",
        //                                 "noSuratJaminan": "-",
        //                                 "tglKejadian": "2018-08-06",
        //                                 "tglSep": "2018-08-08"                                    
        //                             }    
        //                         ]
        //                     }
        //             }';
        $d = json_decode($hasil, true);
        // var_dump($d);
        if ($d['metaData']['code'] == '200') {
          foreach ($d['response']['jaminan'] as $key => $value) {
            $regdisplay = '<a class="btn btn-warning btn-sm" data-act="ambil" title="Ambil" onClick="return do_action_ambil(this);" ><i class="fa fa-check-circle"></i> Pilih</a>'; 
            $dataSuplesi[$key] = array($regdisplay,$value['noRegister'],$value['noSep'],$value['noSepAwal'],$value['noSuratJaminan'],$value['tglKejadian'],$value['tglSep']);
          }
          $data = array('data' => $dataSuplesi);
          echo json_encode($data);
        }else{
          // $hasilx = '{"metaData": 
          //             {
          //               "code": "200",
          //               "message": "Sukses"
          //             },
          //             "response": 
          //                 {
          //                 "jaminan": 
          //                       [
          //                         {
          //                           "noRegister": "",
          //                           "noSep": "",
          //                           "noSepAwal": "",
          //                           "noSuratJaminan": "",
          //                           "tglKejadian": "",
          //                           "tglSep": ""
          //                         }                                        
          //                       ]
          //                 }
          //             }';
          // $dx = json_decode($hasilx, true);
          // foreach ($dx['response']['jaminan'] as $key => $value) {
          //   $regdisplay = '<a class="btn btn-warning btn-sm" data-act="ambil" title="Ambil" onClick="return do_action_ambil(this);" ><i class="fa fa-check-circle"></i> Pilih</a>'; 
          //   $dataSuplesix[$key] = array($value['noRegister'],$value['noRegister'],$value['noSep'],$value['noSepAwal'],$value['noSuratJaminan'],$value['tglKejadian'],$value['tglSep']);
          // }
          $data = array('data' => []);
          echo json_encode($data);         
        }
        return;
      }
    endif;
  }

  public function propinsi($a = 'view', $p = '0') {
    if (is_ajax()):
      if ($a == 'json') {
        $hasil = $this->vclaim_sep->request("/referensi/propinsi", "GET");
        $d = json_decode($hasil, true);
        foreach ($d['response']['list'] as $key => $value) {
          $dataFaskes[$key] = array(['kodepropinsi' => $value['kode'],'namapropinsi' => $value['nama']]);
        }
        echo json_encode($dataFaskes);
        return;
      }
    endif;
  }

  public function kabupaten($a = 'view', $p = '0') {
    if (is_ajax()):
      if ($a == 'json') {
        $hasil = $this->vclaim_sep->request("/referensi/kabupaten/propinsi/".$p, "GET");
        $d = json_decode($hasil, true);
        foreach ($d['response']['list'] as $key => $value) {
          $dataFaskes[$key] = array(['kodekabupaten' => $value['kode'],'namakabupaten' => $value['nama']]);
        }
        echo json_encode($dataFaskes);
        return;
      }
    endif;
  }

  public function kecamatan($a = 'view', $p = '0') {
    if (is_ajax()):
      if ($a == 'json') {
        $hasil = $this->vclaim_sep->request("/referensi/kecamatan/kabupaten/".$p, "GET");
        $d = json_decode($hasil, true);
        foreach ($d['response']['list'] as $key => $value) {
          $dataFaskes[$key] = array(['kodekecamatan' => $value['kode'],'namakecamatan' => $value['nama']]);
        }
        echo json_encode($dataFaskes);
        return;
      }
    endif;
  }

  public function CekDiag($a = 'null', $p = '0'){
    if ($a == 'json') {
      $hasil = $this->vclaim_sep->request("/referensi/diagnosa/".$p, "GET");
      $d = json_decode($hasil, true);
      if ($d == null) {
        $this->icd10_model->whereFields['a.' . 'kd_mst_icd10'] = $p;
        echo json_encode($this->icd10_model->read());
      } else {
        if (is_ajax()):
          if ($a == 'json') {
            foreach ($d['response']['diagnosa'] as $key => $value) {
              $dataFaskes[$key] = array(['kd_mst_icd10' => $value['kode'],'nm_icd_icd10' => $value['nama']]);
            }
            echo json_encode($dataFaskes);
          }
        endif;
      }
    }else{
      $this->render('PesertaCek');
    }
  }

  public function CekPeserta($a = 'null', $p = '0'){
    if ($a == 'json') {
      $tgl  = date("Y-m-d");
      $hasil = $this->vclaim_sep->request("/Peserta/nokartu/".$p."/tglSEP/".$tgl, "GET");
      $d = json_decode($hasil, true);
      $dataPeserta = array(['nmAsuransi' => $d['response']['peserta']['cob']['nmAsuransi'],'noAsuransi' => $d['response']['peserta']['cob']['noAsuransi'],'tglTATcob' => $d['response']['peserta']['cob']['tglTAT'],'tglTMTcob' => $d['response']['peserta']['cob']['tglTMT'],'keteranganhakKelas' => $d['response']['peserta']['hakKelas']['keterangan'],'kodehakKelas' => $d['response']['peserta']['hakKelas']['kode'],'dinsos' => $d['response']['peserta']['informasi']['dinsos'],'noSKTM' => $d['response']['peserta']['informasi']['noSKTM'],'prolanisPRB' => $d['response']['peserta']['informasi']['prolanisPRB'],'keteranganjenisPeserta' => $d['response']['peserta']['jenisPeserta']['keterangan'],'kodejenisPeserta' => $d['response']['peserta']['jenisPeserta']['kode'],'noMR' => $d['response']['peserta']['mr']['noMR'],'noTelepon' => $d['response']['peserta']['mr']['noTelepon'],'nama' => $d['response']['peserta']['nama'],'nik' => $d['response']['peserta']['nik'],'noKartu' => $d['response']['peserta']['noKartu'],'pisa' => $d['response']['peserta']['pisa'],'kdProvider' => $d['response']['peserta']['provUmum']['kdProvider'],'nmProvider' => $d['response']['peserta']['provUmum']['nmProvider'],'sex' => $d['response']['peserta']['sex'],'keteranganstatusPeserta' => $d['response']['peserta']['statusPeserta']['keterangan'],'kodestatusPeserta' => $d['response']['peserta']['statusPeserta']['kode'],'tglCetakKartu' => $d['response']['peserta']['tglCetakKartu'],'tglLahir' => $d['response']['peserta']['tglLahir'],'tglTAT' => $d['response']['peserta']['tglTAT'],'tglTMT' => $d['response']['peserta']['tglTMT'],'umurSaatPelayanan' => $d['response']['peserta']['umur']['umurSaatPelayanan'],'umurSekarang' => $d['response']['peserta']['umur']['umurSekarang'],'code' => $d['metaData']['code'],'message' => $d['metaData']['message']]);
        echo json_encode($dataPeserta);
        return;
      }else{
        $this->render('PesertaCek');
        return;
    }
  }

  public function CekPesertaNik($a = 'null', $p = '0'){
    if ($a == 'json') {
    $tgl  = date("Y-m-d");
    $hasil = $this->vclaim_sep->request("/Peserta/nik/".$p."/tglSEP/".$tgl, "GET");
    $d = json_decode($hasil, true);
    $dataPeserta = array(['nmAsuransi' => $d['response']['peserta']['cob']['nmAsuransi'],'noAsuransi' => $d['response']['peserta']['cob']['noAsuransi'],'tglTATcob' => $d['response']['peserta']['cob']['tglTAT'],'tglTMTcob' => $d['response']['peserta']['cob']['tglTMT'],'keteranganhakKelas' => $d['response']['peserta']['hakKelas']['keterangan'],'kodehakKelas' => $d['response']['peserta']['hakKelas']['kode'],'dinsos' => $d['response']['peserta']['informasi']['dinsos'],'noSKTM' => $d['response']['peserta']['informasi']['noSKTM'],'prolanisPRB' => $d['response']['peserta']['informasi']['prolanisPRB'],'keteranganjenisPeserta' => $d['response']['peserta']['jenisPeserta']['keterangan'],'kodejenisPeserta' => $d['response']['peserta']['jenisPeserta']['kode'],'noMR' => $d['response']['peserta']['mr']['noMR'],'noTelepon' => $d['response']['peserta']['mr']['noTelepon'],'nama' => $d['response']['peserta']['nama'],'nik' => $d['response']['peserta']['nik'],'noKartu' => $d['response']['peserta']['noKartu'],'pisa' => $d['response']['peserta']['pisa'],'kdProvider' => $d['response']['peserta']['provUmum']['kdProvider'],'nmProvider' => $d['response']['peserta']['provUmum']['nmProvider'],'sex' => $d['response']['peserta']['sex'],'keteranganstatusPeserta' => $d['response']['peserta']['statusPeserta']['keterangan'],'kodestatusPeserta' => $d['response']['peserta']['statusPeserta']['kode'],'tglCetakKartu' => $d['response']['peserta']['tglCetakKartu'],'tglLahir' => $d['response']['peserta']['tglLahir'],'tglTAT' => $d['response']['peserta']['tglTAT'],'tglTMT' => $d['response']['peserta']['tglTMT'],'umurSaatPelayanan' => $d['response']['peserta']['umur']['umurSaatPelayanan'],'umurSekarang' => $d['response']['peserta']['umur']['umurSekarang'],'code' => $d['metaData']['code'],'message' => $d['metaData']['message']]);
      echo json_encode($dataPeserta);
      return;
    }else{
      $this->render('PesertaCek');
      return;
    }
  }

  public function list_index(){
    $model = 'Pendaftaran';
    $this->set('primaryKey', $this->$model->primaryKey);
    $this->add_script('plugins/DataTables-1.10.12/datatables.min.js');
    $this->render('list_index');
  }

  public function list_peserta($a = 'list'){
    $this->title = "Data Pasien BPJS";
    $model = 'Peserta_list';
    if(is_ajax()):
      $columns = array(array( "name" => "no_rm", "data" => "no_rm"),array( "name" => "kd_trx_pendaftaran", "data" => "kd_trx_pendaftaran"),array( "name" => "noSep", "data" => "noSep"),array( "name" => "tglSep", "data" => "tglSep"),array( "name" => "noKartu", "data" => "noKartu"),array( "name" => "nm_mst_pasien", "data" => "nm_mst_pasien"),array( "name" => "kelamin", "data" => "kelamin"),array( "name" => "nm_mst_unit_kerja", "data" => "nm_mst_unit_kerja"),array( "name" => "poli", "data" => "poli_display"),array( "name" => "action", "data" => array("_" => "action", "order" => false), "searchable" => false),);
      $columnDefs = array(array( "targets" => array( 1 ), "orderable" => false),);
      if ($a == 'list') {
        $this->set('columns', $columns);
        $this->set('columnDefs', $columnDefs);
        $this->load->view($this->directory.ucwords($this->name)."/list_peserta", $this->data);
        return;
      } else if ($a == 'json') {
        $dtPost = $this->input->post();
        if (isset($dtPost)){
          $whereFields=array();
          // if (!$dtPost['noKartu']==''){
          //   $whereFields['e.noKartu']=$dtPost['noKartu'];
          // } else if (!$dtPost['no_rm']==''){
          //   $whereFields['a.no_rm']=$dtPost['no_rm'];
          // } else if (!$dtPost['date_added']==''){
          //   $whereFields['a.tgl_daftar']=$dtPost['date_added'];
          // } else if (!$dtPost['noSep']==''){
          //   $whereFields['i.noSep']=$dtPost['noSep'];
          // } else {
          //   $whereFields['a.tgl_daftar']=date("Y-m-d");
          // }
          $heed=false;
          if(!empty($dtPost['noKartu']))
          {
            $whereLike['e.noKartu'] = $dtPost['noKartu'];
            $heed=true;
          }
          if(!empty($dtPost['no_rm']))
          {
            $whereLike['a.no_rm'] = $dtPost['no_rm'];
            $heed=true;
          }
          if(!empty($dtPost['noSep']))
          {
            $whereLike['i.noSep'] = $dtPost['noSep'];
            $heed=true;
          }
          if(isset($whereLike))
          {
              $this->$model->set_variable('whereLike', $whereLike);
          }
        }
        if(!empty($dtPost['date_added']) && !empty($dtPost['date_added']))
        {
            $this->$model->whereFields['a.tgl_daftar']=$dtPost['date_added'];
        }else{
          if($heed == false){
            $this->$model->whereFields['a.tgl_daftar']=date('Y-m-d');
          }
        }
        $this->$model->whereFields['a.flag_batal']='0';
        $array_data = array();
        $primaryKey = $this->$model->primaryKey;
        $return = $this->$model->datatable_Pendaftaran('0', $columns, $columnDefs);
        if (count($return['data']) > 0):
          foreach ($return['data'] as $i => $r):
            $r->DT_RowId = $r->kd_trx_pendaftaran;
            if($r->poli == ''){$r->poli_display = $r->nm_mst_unit_kerja_sub;}else{$r->poli_display = $r->poli;}
            $button = '';
            if (is_auth($this->name, 'update')){
              // $button .= '<a href="'.base_url().'bridging/bpjs/list_riwayat_pelayanan/'.$r->noKartu.'"  data-toggle="tooltip" data-placement="top" title="Cek Riwayat Peserta"  class="btn btn-xs btn-info">Riwayat Peserta</a>';
            }
            if ($r->noSep != '-' AND $r->flag_active != '0') {
              $button .= '  <a href="'.base_url().'bridging/Bpjs/SEP/Edit/'.$r->noSep.'"  data-toggle="tooltip" data-placement="top" title="Update SEP"  class="btn btn-xs btn-warning">Update</a>';
              $button .= '  <a href="'.base_url().'bridging/Bpjs/SEP/Views/'.$r->noSep.'"  data-toggle="tooltip" data-placement="top" title="View SEP"  class="btn btn-xs btn-primary">View</a>';
              $button .= '  <a href="'.base_url().'bridging/Bpjs/Rujukan/Create/'.$r->noSep.'"  data-toggle="tooltip" data-placement="top" title="Rujuk Peserta"  class="btn btn-xs btn-primary">Rujuk Peserta</a>';
              if ($r->flag_klaim == 0 && $r->flag_grouper == 0 && $r->flag_coding == 1) {
                $button .= '  <a href="'.base_url().'bridging/Bpjs/CreateKlaimRedirect/'.$r->noSep.'"  data-toggle="tooltip" data-placement="top" title="Klaim"  class="btn btn-xs btn-default">Klaim</a>';
              } else if ($r->flag_klaim == 1 && $r->flag_grouper == 0 && $r->flag_coding == 1){
                $button .= '  <a href="'.base_url().'bridging/Bpjs/GrouperUlang/'.$r->noSep.'"  data-toggle="tooltip" data-placement="top" title="Grouper Ulang"  class="btn btn-xs btn-default">Grouper Ulang</a>';
              }else if($r->flag_klaim == 1 && $r->flag_grouper == 1 && $r->flag_coding == 1){
                $button .= '  <a href="'.base_url().'bridging/Bpjs/cetakKlaim/'.$r->noSep.'"  data-toggle="tooltip" data-placement="top" title="Cetak Klaim"  class="btn btn-xs bg-yellow">Cetak Klaim</a>';
              }
              if ($r->flag_klaim == 1 && $r->flag_grouper == 1 && $r->flag_final == 0 && $r->flag_coding == 1) {
                $button .= '  <a href="'.base_url().'bridging/Bpjs/finalKlaim/'.$r->noSep.'"  data-toggle="tooltip" data-placement="top" title="Kirim Data Ke Data Center"  class="btn btn-xs bg-yellow">Kirim Klaim</a>';
              }
              $r->tglSep = date("d-m-Y", strtotime($r->tglSep)); 
            }
            $r->action = '<ul class="table-button">'. $button .'</ul>';
          array_push($array_data, $r);
          endforeach;
        endif;
        $return['data'] = $array_data;
        echo json_encode($return);
        return;
      }
    endif;
    redirect($this->directory."$this->name"."/list_index");
  }

  public function SEP($a = 'view', $p = '0'){
    $dp = 'datapendaftaran';
    $isi = 'isi';
    $pencet = 'pencet';
    $Pendaftaran_Data = 'Pendaftaran';
    $this->set('dp', $dp);
    $this->set('Pendaftaran_Data', $Pendaftaran_Data);
    $this->set('isi', $isi);
    $this->set('pencet', $pencet);
    $data = $this->input->post();
    if ($a == 'dataCreate') {
      $data = $this->input->post();
      if (isset($data[$pencet]['Proses'])) {            
      $noKartu = $data[$isi]['noKartu'];
      $tglSep = date("Y-m-d", strtotime($data[$isi]['tglSep']));
      $ppkPelayanan = $data[$isi]['ppkPelayanan'];
      $jnsPelayanan = $data[$isi]['jnsPelayanan'];
      if (isset($data[$isi]['klsRawat'])) { $klsRawat = $data[$isi]['klsRawat']; }else{$klsRawat = '';}
      $noMR = $data[$isi]['noMr'];
      if (isset($data[$isi]['asalRujukan'])) { $asalRujukan = $data[$isi]['asalRujukan']; }else{$asalRujukan = '';}
      if (isset($data[$isi]['tglRujukan'])) { $tglRujukan = $data[$isi]['tglRujukan']; }else{$tglRujukan = '';}
      if (isset($data[$isi]['noRujukan'])) { $noRujukan = $data[$isi]['noRujukan']; }else{$noRujukan = '';}
      if (isset($data[$isi]['ppkRujukan'])) { $ppkRujukan = $data[$isi]['ppkRujukan']; }else{$ppkRujukan = '';}
      $catatan = $data[$isi]['catatan'];
      $diagAwal = $data[$isi]['diagAwal'];
      if (isset($data[$isi]['tujuan'])) { $tujuan = $data[$isi]['tujuan']; }else{$tujuan = '';}
      if (isset($data[$isi]['eksekutif'])) { $eksekutif = $data[$isi]['eksekutif']; }else{$eksekutif = 0;}
      if (isset($data[$isi]['cob'])) { $cob = $data[$isi]['cob']; }else{$cob = 0;}
      if (isset($data[$isi]['katarak'])) { $katarak = $data[$isi]['katarak']; }else{$katarak = 0;}
      if (isset($data[$isi]['kontrol'])) { $kontrol = $data[$isi]['kontrol']; }else{$kontrol = 0;}
      $lakaLantas = $data[$isi]['lakaLantas'];
      if (isset($data[$isi]['penjamin'])) { $penjamin = implode(',', $data[$isi]['penjamin']); }else{$penjamin = '';}
      $tglKejadian = $data[$isi]['tglKejadian'];
      $suplesi = $data[$isi]['suplesi'];
      $noSepSuplesi = $data[$isi]['noSepSuplesi'];
      $kdPropinsi = $data[$isi]['kodepropinsi'];
      $kdKabupaten = $data[$isi]['kodekabupaten'];
      $kdKecamatan = $data[$isi]['kodekecamatan'];
      $keteranganKll = $data[$isi]['keteranganKll'];
      if (isset($data[$isi]['noSurat'])) { $noSurat = $data[$isi]['noSurat']; }else{$noSurat = '';}
      if (isset($data[$isi]['kodeDPJP'])) { $kodeDPJP = $data[$isi]['kodeDPJP']; }else{$kodeDPJP = '';}
      $noTelp = $data[$isi]['noTelp'];
      $user = $data[$isi]['user'];
      $DataPost = '{
             "request": {
                "t_sep": {
                   "noKartu": "'.$noKartu.'",
                   "tglSep": "'.$tglSep.'",
                   "ppkPelayanan": "'.$ppkPelayanan.'",
                   "jnsPelayanan": "'.$jnsPelayanan.'",
                   "klsRawat": "'.$klsRawat.'",
                   "noMR": "'.$noMR.'",
                   "rujukan": {
                      "asalRujukan": "'.$asalRujukan.'",
                      "tglRujukan": "'.$tglRujukan.'",
                      "noRujukan": "'.$noRujukan.'",
                      "ppkRujukan": "'.$ppkRujukan.'"
                   },
                   "catatan": "'.$catatan.'",
                   "diagAwal": "'.$diagAwal.'",
                   "poli": {
                      "tujuan": "'.$tujuan.'",
                      "eksekutif": "'.$eksekutif.'"
                   },
                   "cob": {
                      "cob": "'.$cob.'"
                   },
                   "katarak": {
                      "katarak": "'.$katarak.'"
                   },
                   "jaminan": {
                      "lakaLantas": "'.$lakaLantas.'",
                      "penjamin": {
                        "penjamin": "'.$penjamin.'",
                        "tglKejadian": "'.$tglKejadian.'",
                        "keterangan": "'.$keteranganKll.'",
                        "suplesi": {
                          "suplesi": "'.$suplesi.'",
                          "noSepSuplesi": "'.$noSepSuplesi.'",
                          "lokasiLaka": {
                            "kdPropinsi": "'.$kdPropinsi.'",
                            "kdKabupaten": "'.$kdKabupaten.'",
                            "kdKecamatan": "'.$kdKecamatan.'"
                            }
                        }
                      }
                   },
                    "skdp": {
                      "noSurat": "'.$noSurat.'",
                      "kodeDPJP": "'.$kodeDPJP.'"
                   },
                   "noTelp": "'.$noTelp.'",
                   "user": "'.$user.'"
                }
             }
          }';
      // echo $DataPost;    
      $hasil = $this->vclaim_sep->request("/SEP/1.1/insert", "POST", $DataPost);
      $d = json_decode($hasil, true);
      $dataEklaim['metadata']['method'] = "new_claim";
      $dataEklaim['data']['nomor_kartu'] = $noKartu;
      $dataEklaim['data']['nomor_sep'] = $d['response']['sep']['noSep'];
      $dataEklaim['data']['nomor_rm'] = $noMR;
      $dataEklaim['data']['nama_pasien'] = $data[$isi]['nama'];
      $dataEklaim['data']['tgl_lahir'] = $data[$isi]['tgl_lahir'];
      $dataEklaim['data']['gender'] = ($data[$isi]['kelamin'] == 1) ? "1" : "2";
      $hasilEklaim = 'test';
      // $hasilEklaim = $this->eklaim->claim(json_encode($dataEklaim));
      $DataGET = array(['noSEP' => $d['response']['sep']['noSep'],
                       'diagnosa' => $d['response']['sep']['diagnosa'],
                       'jnsPelayananNama' => $d['response']['sep']['jnsPelayanan'],
                       'kelasRawat' => $d['response']['sep']['kelasRawat'],
                       'asuransi' => $d['response']['sep']['peserta']['asuransi'],
                       'hakKelas' => $d['response']['sep']['peserta']['hakKelas'],
                       'jnsPeserta' => $d['response']['sep']['peserta']['jnsPeserta'],
                       'jnsPelayanan' => $d['response']['sep']['peserta']['jnsPeserta'],
                       'kelamin' => $d['response']['sep']['peserta']['kelamin'],
                       'code' => $d['metaData']['code'],
                       'message' => $d['metaData']['message'],
                       'eklaim' => $hasilEklaim]);
        if ($d['metaData']['code'] == "200"){
          $data = $this->input->post();
          $this->db->trans_start();
          if (isset($data[$isi]['kontrol'])) {
            $data[$Pendaftaran_Data]['id_trx_pendaftaran'] = $data[$dp]['id_trx_pendaftaran'];
            $data[$Pendaftaran_Data]['id_mst_unit_kerja_ref'] = "34";
            $data[$Pendaftaran_Data]['id_mst_unit_kerja_sub'] = $data[$isi]['id_mst_unit_kerja_sub'];
            $data[$Pendaftaran_Data]['id_mst_unit_kerja_sub_ref'] = $data[$isi]['id_mst_unit_kerja_sub'];
            $this->$Pendaftaran_Data->replace($data[$Pendaftaran_Data]);
          }
          if (isset($data[$isi]['penjamin'])) { $data[$isi]['penjamin'] = implode(',', $data[$isi]['penjamin']); }else{$data[$isi]['penjamin'] = '';}
          if (isset($data[$isi]['katarak'])) { $data[$isi]['katarak']; }else{$data[$isi]['katarak'] = 0;}
          if (isset($data[$isi]['kontrol'])) { $data[$isi]['kontrol']; }else{$data[$isi]['kontrol'] = 0;}
          $data[$isi]['noSep'] = $d['response']['sep']['noSep'];
          $this->SEP->create($data[$isi]);
          $this->db->trans_complete();
        }
        echo json_encode($DataGET);
        // echo "<pre>";
        // var_dump($data[$isi]);
        // echo "</pre>";
        // $log['log'] = json_encode($DataGET);
        // $log['keterangan'] = 'Create SEP';
        // $this->trx_eklaim->insertlog($log);
      } else if (isset($data[$pencet]['Simpan'])) {
        if (count($this->input->post()) > 0) {
          $data = $this->input->post();
          /* Start Transaction */
          $this->db->trans_start();
          if (isset($data[$isi]['penjamin'])) { $data[$isi]['penjamin'] = implode(',', $data[$isi]['penjamin']); }else{$data[$isi]['penjamin'] = '';}
          if (isset($data[$isi]['kontrol'])) {
            $data[$Pendaftaran_Data]['id_trx_pendaftaran'] = $data[$dp]['id_trx_pendaftaran'];
            $data[$Pendaftaran_Data]['id_mst_unit_kerja_ref'] = "34";
            $data[$Pendaftaran_Data]['id_mst_unit_kerja_sub'] = $data[$isi]['id_mst_unit_kerja_sub'];
            $data[$Pendaftaran_Data]['id_mst_unit_kerja_sub_ref'] = $data[$isi]['id_mst_unit_kerja_sub'];
              // echo "<pre>";
              // var_dump($data[$Pendaftaran_Data]);
              // echo "</pre>";
            $this->$Pendaftaran_Data->replace($data[$Pendaftaran_Data]);
          }
          $this->SEP->replace($data[$isi]);
          $this->Pendaftaran->replace($data[$dp]);
          $id = $data[$isi]['noSep'];
          $this->db->trans_complete();
          /* End Transaction */
          if ($this->db->trans_status() === false) {
            if (is_ajax()) {
              echo json_encode(array('success' => false, 'id' => null));
            } else {
              $this->setFlash('FAILED: ', 'Failed save data ' . ucwords($this->name) . '. Contact your IT Support.', 'alert-danger');
              $this->render('SepCreate');
            }
            return;
          } else {
            if (is_ajax()) {
              echo json_encode(array('success' => true, 'id' => $id));
            } else {
              $this->setFlash('SUCCESS', 'Success save data ' . ucwords($this->name), 'alert-success');
              redirect($this->directory . strtolower($this->name) . '/SepViews/' . $id);
            }
            return;
          } 
        }
      }
    } else if ($a == 'Print') {
      $data[$isi]['noSep'] = $p;
      $row = $this->SEP->read($p)[0];
      if($row->session_cetak == 1){
        $data[$isi]['cetakanpertama'] = date('Y-m-d H:i:s');
      }else{}
      $data[$isi]['session_cetak'] = $row->session_cetak + 1;
      $this->SEP->replace($data[$isi]);
      $this->set('row', $row);
      $this->load->view('bridging/Bpjs/SepPrint', $this->data);
    } else if ($a == 'Views') {
      $row = $this->SEP->read($p)[0];
      $this->set('row', $row);
      $this->render('SepView');
    } else if ($a == 'dataUpdate') {
      if (isset($data[$pencet]['Proses'])) {
        $url       = "http://api.bpjs-kesehatan.go.id:8080/VClaim-rest/SEP/Update"; //Lihat katalog, jangan sertakan port
        $noKartu = $data[$isi]['noKartu'];
        $noSep = $data[$isi]['noSep'];
        $tglSep = date("Y-m-d", strtotime($data[$isi]['tglSep']));
        $ppkPelayanan = $data[$isi]['ppkPelayanan'];
        $jnsPelayanan = $data[$isi]['jnsPelayanan'];
        if (isset($data[$isi]['klsRawat'])) { $klsRawat = $data[$isi]['klsRawat']; }else{$klsRawat = '';}
        $noMR = $data[$isi]['noMr'];
        $asalRujukan = $data[$isi]['asalRujukan'];
        $tglRujukan = $data[$isi]['tglRujukan'];
        $noRujukan = $data[$isi]['noRujukan'];
        $ppkRujukan = $data[$isi]['ppkRujukan'];
        $catatan = $data[$isi]['catatan'];
        $diagAwal = $data[$isi]['diagAwal'];
        if (isset($data[$isi]['tujuan'])) { $tujuan = $data[$isi]['tujuan']; }else{$tujuan = '';}
        if (isset($data[$isi]['eksekutif'])) { $eksekutif = $data[$isi]['eksekutif']; }else{$eksekutif = 0;}
        if (isset($data[$isi]['cob'])) { $cob = $data[$isi]['cob']; }else{$cob = 0;}
        if (isset($data[$isi]['katarak'])) { $katarak = $data[$isi]['katarak']; }else{$katarak = 0;}
        if (isset($data[$isi]['kontrol'])) { $kontrol = $data[$isi]['kontrol']; }else{$kontrol = 0;}
        $lakaLantas = $data[$isi]['lakaLantas'];
        if (isset($data[$isi]['penjamin'])) { $penjamin = implode(',', $data[$isi]['penjamin']); }else{$penjamin = '';}
        $tglKejadian = $data[$isi]['tglKejadian'];
        $suplesi = $data[$isi]['suplesi'];
        $noSepSuplesi = $data[$isi]['noSepSuplesi'];
        $kdPropinsi = $data[$isi]['kodepropinsi'];
        $kdKabupaten = $data[$isi]['kodekabupaten'];
        $kdKecamatan = $data[$isi]['kodekecamatan'];
        $keteranganKll = $data[$isi]['keteranganKll'];
        $noSurat = $data[$isi]['noSurat'];
        $kodeDPJP = $data[$isi]['kodeDPJP'];
        $noTelp = $data[$isi]['noTelp'];
        $user = $data[$isi]['user'];
        $DataPost = '{
               "request": {
                  "t_sep": {
                     "noSep": "'.$noSep.'",
                     "klsRawat":"'.$klsRawat.'",
                     "noMR": "'.$noMR.'",
                     "rujukan": {
                        "asalRujukan": "'.$asalRujukan.'",
                        "tglRujukan": "'.$tglRujukan.'",
                        "noRujukan": "'.$noRujukan.'",
                        "ppkRujukan": "'.$ppkRujukan.'"
                     },
                     "catatan": "'.$catatan.'",
                     "diagAwal": "'.$diagAwal.'",
                     "poli": {
                        "tujuan": "'.$tujuan.'",
                        "eksekutif": "'.$eksekutif.'"
                     },
                     "cob": {
                        "cob": "'.$cob.'"
                     },
                     "katarak":{
                        "katarak":"'.$katarak.'"
                     },
                     "skdp":{
                        "noSurat":"'.$noSurat.'",
                        "kodeDPJP":"'.$kodeDPJP.'"            
                     },
                     "jaminan": {
                        "lakaLantas":"'.$lakaLantas.'",
                        "penjamin":
                        {
                          "penjamin":"'.$penjamin.'",
                          "tglKejadian":"'.$tglKejadian.'",       
                          "keterangan":"'.$keteranganKll.'",
                          "suplesi":
                          {
                            "suplesi":"'.$suplesi.'",
                            "noSepSuplesi":"'.$noSepSuplesi.'",
                            "lokasiLaka": 
                            {
                            "kdPropinsi":"'.$kdPropinsi.'",
                            "kdKabupaten":"'.$kdKabupaten.'",
                            "kdKecamatan":"'.$kdKecamatan.'"
                            }
                          }         
                        }
                     },
                     "noTelp": "'.$noTelp.'",
                     "user": "'.$user.'"
                  }
               }
            }';
        echo $DataPost;    
        $hasil = $this->vclaim_sep->request("/SEP/1.1/Update", "PUT", $DataPost);
        $d = json_decode($hasil, true);  
        $dataGET = array(['noSEP' => $d['response'],'code' => $d['metaData']['code'],'message' => $d['metaData']['message']]);
        echo json_encode($dataGET);
      } else if (isset($data[$pencet]['Simpan'])) {
        if (count($this->input->post()) > 0) {
          $data = $this->input->post();
          /* Start Transaction */
          $this->db->trans_start();
          if (isset($data[$isi]['penjamin'])) { $data[$isi]['penjamin'] = implode(',', $data[$isi]['penjamin']); }else{$data[$isi]['penjamin'] = '';}
          if (isset($data[$isi]['katarak'])) { $data[$isi]['katarak']; }else{$data[$isi]['katarak'] = 0;}
          if (isset($data[$isi]['kontrol'])) { $data[$isi]['kontrol']; }else{$data[$isi]['kontrol'] = 0;}
          $this->SEP->replace($data[$isi]);
          $id = $data[$isi]['noSep'];
          $this->db->trans_complete();
          /* End Transaction */
          if ($this->db->trans_status() === false) {
            if (is_ajax()) {
              echo json_encode(array('success' => false, 'id' => null));
            } else {
              $this->setFlash('FAILED: ', 'Failed save data ' . ucwords($this->name) . '. Contact your IT Support.', 'alert-danger');
              $this->render('SepCreate');
            }
            return;
          } else {
            if (is_ajax()) {
              echo json_encode(array('success' => true, 'id' => $id));
            } else {
              $this->setFlash('SUCCESS', 'Success save data ' . ucwords($this->name), 'alert-success');
              redirect($this->directory . strtolower($this->name) . '/Views/' . $id);
            }
            return;
          }
        }
      }
    } else if ($a == 'Edit') {
      $row = $this->SEP->read($p)[0];
      if(isset($row)){
        $this->set('row', $row);
        $this->render('SepEdit');
      } else {
        $this->setFlash('FAILED: ', 'Data SEP Tidak ditemukan!', 'alert-danger');
        redirect('bridging/Bpjs/list_index');
      }
    } else if ($a == 'dataEditTglPulang') {
      if (isset($data[$pencet]['Proses'])) {
        $noSep = $data[$isi]['noSep'];
        $tglPulang = $data[$isi]['tglPulang'];
        $user = $data[$isi]['user'];
        $DataPost = '{
           "request": {
              "t_sep": {
                 "noSep": "'.$noSep.'",
                 "tglPulang": "'.$tglPulang.'",
                 "user": "'.$this->auth->get_user('nm_mst_pegawai_lengkap').'",
              }
           }
        }';
        $hasil = $this->vclaim_sep->request("/Sep/updtglplg", "PUT", $DataPost);
        $d = json_decode($hasil, true);  
        $dataGET = array(['noSEP' => $d['response'],'code' => $d['metaData']['code'],'message' => $d['metaData']['message'],]);
        echo json_encode($dataGET);
      }else if (isset($data[$pencet]['Simpan'])) {
        if (count($this->input->post()) > 0) {
          $data = $this->input->post();
          /* Start Transaction */
          $this->db->trans_start();
          $this->SEP->replace($data[$isi]);
          $id = $data[$isi]['noSep'];
          $this->db->trans_complete();
          /* End Transaction */
          if ($this->db->trans_status() === false) {
            if (is_ajax()) {
              echo json_encode(array('success' => false, 'id' => null));
            } else {
              $this->setFlash('FAILED: ', 'Failed save data ' . ucwords($this->name) . '. Contact your IT Support.', 'alert-danger');
              $this->render('SepCreate');
            }
            return;
          } else {
            if (is_ajax()) {
              echo json_encode(array('success' => true, 'id' => $id));
            } else {
              $this->setFlash('SUCCESS', 'Success save data ' . ucwords($this->name), 'alert-success');
              redirect($this->directory . strtolower($this->name) . '/SepView/' . $id);
            }
            return;
          }
        }
      }
    } else if ($a == 'Edittglpulang') {
      $row = $this->SEP->read($p)[0];
      $this->set('row', $row);
      $this->render('SepEditTglPulang');
    } else if ($a == 'dataDelete') {
      if (isset($data[$pencet]['Hapus'])) {
        $noSep = $data[$isi]['noSep'];
        $user = $data[$isi]['user'];
        $DataPost = '{
           "request": {
              "t_sep": {
                 "noSep": "'.$noSep.'",
                 "user": "'.$user.'"
              }
           }
        }';
        $hasil = $this->vclaim_sep->request("/SEP/Delete", "DELETE", $DataPost);
        $d = json_decode($hasil, true);
        $DataGET = array(['noSEP' => $d['response'],'code' => $d['metaData']['code'],'message' => $d['metaData']['message']]);
        if (count($this->input->post()) > 0) {
          $data = $this->input->post();
          /* Start Transaction */
          $this->db->trans_start();
          $this->SEP->replace($data[$isi]);
          $id = $data[$isi]['noSep'];
          $this->db->trans_complete();
          /* End Transaction */
          if ($this->db->trans_status() === false) {
            if (is_ajax()) {
              echo json_encode(array('success' => false, 'id' => null));
              echo json_encode($dataSEP);
            } else {
              $this->setFlash('FAILED: ', 'Failed save data ' . ucwords($this->name) . '. Contact your IT Support.', 'alert-danger');
              $this->render('SepCreate');
            }
            return;
          } else {
            if (is_ajax()) {
              echo json_encode(array('success' => true, 'id' => $id, 'noSEP' => $d['response'], 'code' => $d['metaData']['code'], 'message' => $d['metaData']['message']));
            } else {
              $this->setFlash('SUCCESS', 'Success save data ' . ucwords($this->name), 'alert-success');
              echo json_encode($dataSEP);
              redirect($this->directory . strtolower($this->name) . '/SepView/' . $id);
            }
            return;
          }
        }
      }
    } else if ($a == 'delete') {
      $row = $this->SEP->read($p)[0];
      $this->set('row', $row);
      $this->render('SepDelete');
    } else if ($a == 'Redirect'){
      $this->SEP->whereFields['c.'.'id_trx_pendaftaran'] = $p;
      @$DataSEP_igd_poli = $this->SEP->read()[0];
      $DataPendaftaran = $this->Pendaftaran->read($p);
      if(isset($DataPendaftaran[0])){
        $DataPendaftaran=$DataPendaftaran[0];
        $this->pengajuan->whereFields['a.'.'id_trx_pendaftaran'] = $p;
        $DataPengajuanSEP=$this->pengajuan->read();
        if (isset($DataPengajuanSEP[0])){
          $DataPendaftaran->tglSEP=$DataPengajuanSEP[0]->tglSep;
        } else {
          $DataPendaftaran->tglSEP=date('Y-m-d');
        }
        $this->SEP_HISTORY->whereFields['a.noKartu'] = $DataPendaftaran->noKartu;
        $this->set('DataPesertaH', $this->SEP_HISTORY->read());
        // $this->set('DataPesertaH', $this->read_history_peserta($DataPendaftaran->noKartu));
        $this->set('DataSEP_igd_poli', @$DataSEP_igd_poli);
        $this->set('DataPendaftaran', $DataPendaftaran);
        $this->add_script('plugins/DataTables-1.10.12/datatables.min.js');
        $this->render('SepCreate');
      } else {
        $this->setFlash('FAILED: ', 'Tidak bisa membuat SEP, no Kartu belum diupdate di data pasien', 'alert-danger');
        redirect('sharing/pasien');
      }
    }
  }

  public function CetakanPertama($p ='0'){
      $isi = 'isi';
      $data[$isi]['noSep'] = $p;
      $row = $this->SEP->read($p)[0];
      // $data[$isi]['session_cetak'] = $row->session_cetak + 1;
      // $this->SEP->replace($data[$isi]);
      $this->set('row', $row);
      $this->load->view('bridging/Bpjs/SepCetakan1', $this->data);
  }

  public function CetakanKosong($p = 0){
    $DataPendaftaran = $this->Pendaftaran->read($p)[0];
    $this->set('DataPendaftaran',$DataPendaftaran);
    $this->load->view('bridging/Bpjs/SepCetakanKosong',$this->data);
  }

  public function RujukanList($a = 'list'){
    $model = 'Rujukan';
    if(is_ajax()):
      $columns = array(
        array( "name" => "noRujukan", "data" => "noRujukanDisplay"),
        array( "name" => "tglRujukan", "data" => "tglRujukan"),
        array( "name" => "jnsPelayanan", "data" => "jnsPelayananDisplay"),
        array( "name" => "noSep", "data" => "noSep"),
        array( "name" => "noKartu", "data" => "noKartu"),
        array( "name" => "nama", "data" => "nama"),
        array( "name" => "namatujuanRujukan", "data" => "namatujuanRujukan"),
        // array( "name" => "action", "data" => array("_" =>  "action", "order" => false), "searchable" => false),

      );
      $columnDefs = array(
        array( "targets" => array(0), "orderable" => false),
      );

      if ($a == 'list') {
        $this->set('columns', $columns);
        $this->set('columnDefs', $columnDefs);
        $this->add_script('plugins/DataTables-1.10.12/datatables.min.js');
        $this->load->view($this->directory.ucwords($this->name)."/RujukanList", $this->data);
        return;
      } else if ($a == 'json') {
        // $flag_kepegawaian = get_data_helper('flag_kepegawaian');
        // foreach ($flag_kepegawaian as $t) $kepegawaian[$t->kd_mst_data_helper] = $t;

        // $flag_perkawinan = get_data_helper('flag_perkawinan');
        // foreach ($flag_perkawinan as $t) $perkawinan[$t->kd_mst_data_helper] = $t;
        $dtPost = $this->input->post();
        if ( isset($dtPost['search']['value']) && $dtPost['search']['value'] != '' ) {
            $whereLike = array(
                'a.noSep' => $dtPost['search']['value'],
                'a.tglRujukan' => $dtPost['search']['value'],
            );
            $this->$model->set_variable('whereLike', $whereLike);
        }

        $array_data = array();
        $primaryKey = $this->$model->primaryKey;
        $return = $this->$model->dataTable('0', $columns, $columnDefs);

        if (count($return['data']) > 0):
          foreach ($return['data'] as $i => $r):
            $r->DT_RowId = $r->noRujukan;
            if ($r->jnsPelayanan == 1) {
              $r->jnsPelayananDisplay = "Rawat Inap";
            }elseif ($r->jnsPelayanan == 2) {
              $r->jnsPelayananDisplay = "Rawat Jalan";
            }
            $r->noRujukanDisplay = '<a class="btn btn-sm btn-default btn-xs" href="'.base_url().'bridging/Bpjs/Rujukan/Views/'.$r->{$primaryKey}.'" title="View Data Rujukan" data-container="body" data-toggle="tooltip" data-trigger="hover">'.$r->{$primaryKey}.'</a>';
            array_push($array_data, $r);
          endforeach;
        endif;

        $return['data'] = $array_data;
        echo json_encode($return);
        return;
      }
    endif;
    redirect($this->directory."$this->name"."/RujukanIndex");
  }

  public function Rujukan($a = 'view', $p = '0'){
    $isi = 'isi';
    $isisep = 'isisep';
    $pencet = 'pencet';
    $this->set('isi', $isi);
    $this->set('isisep', $isisep);
    $this->set('pencet', $pencet);
    $this->add_script('plugins/DataTables-1.10.12/datatables.min.js');
    $data = $this->input->post();
    if ($a == 'dataRujukan') {
      if (isset($data[$pencet]['Proses'])) {
        $noSep = $data[$isi]['noSep'];
        $tglRujukan = $data[$isi]['tglRujukan'];
        $ppkDirujuk = $data[$isi]['kodetujuanRujukan'];
        $jnsPelayanan = $data[$isi]['jnsPelayanan'];
        $catatan = $data[$isi]['catatan'];
        $diagRujukan = $data[$isi]['kodediagnosa'];
        $tipeRujukan = $data[$isi]['tipeRujukan'];
        $poliRujukan = $data[$isi]['kodepoliTujuan'];
        $user = $data[$isi]['user'];
        $DataPost = '{
           "request": {
              "t_rujukan": {
                 "noSep": "'.$noSep.'",
                 "tglRujukan": "'.$tglRujukan.'",
                 "ppkDirujuk": "'.$ppkDirujuk.'",
                 "jnsPelayanan": "'.$jnsPelayanan.'",
                 "catatan": "'.$catatan.'",
                 "diagRujukan": "'.$diagRujukan.'",
                 "tipeRujukan": "'.$tipeRujukan.'",
                 "poliRujukan": "'.$poliRujukan.'",
                 "user": "'.$user.'"
              }
           }
        }';
        // echo "<pre>";
        // var_dump($DataPost);
        // echo "</pre>";
        $hasil = $this->vclaim_sep->request("/Rujukan/insert", "POST", $DataPost);
        $d = json_decode($hasil, true);
        $DataGet = array(['kodeAsalRujukan' => $d['response']['rujukan']['AsalRujukan']['kode'],'namaAsalRujukan' => $d['response']['rujukan']['AsalRujukan']['nama'],'kodediagnosa' => $d['response']['rujukan']['diagnosa']['kode'],'namadiagnosa' => $d['response']['rujukan']['diagnosa']['nama'],'asuransi' => $d['response']['rujukan']['peserta']['asuransi'],'hakKelas' => $d['response']['rujukan']['peserta']['hakKelas'],'jnsPeserta' => $d['response']['rujukan']['peserta']['jnsPeserta'],'kelamin' => $d['response']['rujukan']['peserta']['kelamin'],'nama' => $d['response']['rujukan']['peserta']['nama'],'noKartu' => $d['response']['rujukan']['peserta']['noKartu'],'noMr' => $d['response']['rujukan']['peserta']['noMr'],'tglLahir' => $d['response']['rujukan']['peserta']['tglLahir'],'tglRujukan' => $d['response']['rujukan']['tglRujukan'],'kodetujuanRujukan' => $d['response']['rujukan']['tujuanRujukan']['kode'],'namatujuanRujukan' => $d['response']['rujukan']['tujuanRujukan']['nama'],'noRujukan' => $d['response']['rujukan']['noRujukan'],'code' => $d['metaData']['code'],'message' => $d['metaData']['message']]);
        echo json_encode($DataGet);
      } else if (isset($data[$pencet]['Simpan'])) {
        if (count($this->input->post()) > 0) {
          $data = $this->input->post();
          /* Start Transaction */
          $this->db->trans_start();
          $this->SEP->replace($data[$isisep]);
          $this->Rujukan->create($data[$isi]);
          $id = $data[$isi]['noRujukan'];
          $this->db->trans_complete();
          /* End Transaction */
          if ($this->db->trans_status() === false) {
            if (is_ajax()) {
                echo json_encode(array('success' => false, 'id' => null));
            } else {
              $this->setFlash('FAILED: ', 'Failed save data ' . ucwords($this->name) . '. Contact your IT Support.', 'alert-danger');
              $this->render('SepCreate');
            }
            return; 
          } else {
            if (is_ajax()) {
              echo json_encode(array('success' => true, 'id' => $id));
            } else {
              $this->setFlash('SUCCESS', 'Success save data ' . ucwords($this->name), 'alert-success');
              redirect($this->directory . strtolower($this->name) . '/RujukanView/' . $id);
            }
            return;
          }
        }
      }
    } else if ($a == 'Create') {
      $row = $this->SEP->read($p)[0];
      $this->set('row', $row);
      $this->render('RujukanCreate');
    } else if ($a == 'dataUpdate') {
      if (isset($data[$pencet]['Proses'])) {
        $noRujukan = $data[$isi]['noRujukan'];
        $ppkDirujuk = $data[$isi]['kodetujuanRujukan'];
        $jnsPelayanan = $data[$isi]['jnsPelayanan'];
        $catatan = $data[$isi]['catatan'];
        $diagRujukan = $data[$isi]['kodediagnosa'];
        $tipeRujukan = $data[$isi]['tipeRujukan'];
        $poliRujukan = $data[$isi]['kodepoliTujuan'];
        $user = $data[$isi]['user'];
        $DataPost = '{
           "request": {
              "t_rujukan": {
                 "noRujukan": "'.$noRujukan.'",
                 "ppkDirujuk": "'.$ppkDirujuk.'",
                 "tipe": "0",
                 "jnsPelayanan": "'.$jnsPelayanan.'",
                 "catatan": "'.$catatan.'",
                 "diagRujukan": "'.$diagRujukan.'",
                 "tipeRujukan": "'.$tipeRujukan.'",
                 "poliRujukan": "'.$poliRujukan.'",
                 "user": "'.$user.'"
              }
           }
        }';
        $hasil = $this->vclaim_sep->request("/Rujukan/update", "PUT", $DataPost);
        $d = json_decode($hasil, true);
        $DataGet = array(['response' => $d['response'], 'code' => $d['metaData']['code'], 'message' => $d['metaData']['message']]);
        echo json_encode($DataGet);
      } else if (isset($data[$pencet]['Simpan'])) {
        if (count($this->input->post()) > 0) {
          $data = $this->input->post();
          // echo "<pre>";
          // var_dump($data[$isi]);
          // echo "</pre>";
          /* Start Transaction */
          $this->db->trans_start();
          $this->Rujukan->replace($data[$isi]);
          $this->db->trans_complete();
          $id = $data[$isi]['noRujukan'];
          /* End Transaction */
          if ($this->db->trans_status() === false) {
            if (is_ajax()) {
              echo json_encode(array('success' => false, 'id' => null));
            } else {
              $this->setFlash('FAILED: ', 'Failed save data ' . ucwords($this->name) . '. Contact your IT Support.', 'alert-danger');
              $this->render('RujukanCreate');
            }
            return;
          } else {
            if (is_ajax()) {
              echo json_encode(array('success' => true, 'id' => $id));
            } else {
              $this->setFlash('SUCCESS', 'Success save data ' . ucwords($this->name), 'alert-success');
              redirect($this->directory . strtolower($this->name) . '/SepView/' . $id);
            }
            return;
          }
        }
      }
    } else if ($a == 'Edit') {
      $row = $this->Rujukan->read($p)[0];
      $this->set('row', $row);
      $this->render('RujukanEdit');
    } else if ($a == 'dataDelete') {
      if (isset($data[$pencet]['Hapus'])) {
        $noRujukan = $data[$isi]['noRujukan'];
        $user = $this->auth->get_user('nm_mst_pegawai_lengkap');
          $DataPost = '{
             "request": {
                "t_rujukan": {
                   "noRujukan": "'.$noRujukan.'",
                   "user": "'.$user.'"
                }
             }
          }';
        $hasil = $this->vclaim_sep->request("/Rujukan/delete", "DELETE", $DataPost);
        $d = json_decode($hasil, true);
        $DataGet = array(['response' => $d['response'], 'code' => $d['metaData']['code'], 'message' => $d['metaData']['message']]);
        // echo json_encode($DataGet);
          // echo "<pre>";
          // var_dump($dataSEP);
          // echo "</pre>";
        //}else if (isset($data[$pencet]['Simpan'])) {
        if (count($this->input->post()) > 0) {
          $data = $this->input->post();

          /* Start Transaction */
          $this->db->trans_start();
          $this->SEP->replace($data[$isisep]);
          $this->Rujukan->replace($data[$isi]);
          $id = $data[$isi]['noRujukan'];
          $this->db->trans_complete();
          /* End Transaction */

          if ($this->db->trans_status() === false) {
            if (is_ajax()) {
              echo json_encode(array('success' => false, 'id' => null));
              echo json_encode($dataSEP);
            } else {
              $this->setFlash('FAILED: ', 'Failed save data ' . ucwords($this->name) . '. Contact your IT Support.', 'alert-danger');
              $this->render('SepCreate');
            }
            return;
          } else {
            if (is_ajax()) {
              echo json_encode(array('success' => true, 'id' => $id, 'noSEP' => $d['response'], 'code' => $d['metaData']['code'], 'message' => $d['metaData']['message']));
            } else {
              $this->setFlash('SUCCESS', 'Success save data ' . ucwords($this->name), 'alert-success');
              echo json_encode($dataSEP);
              redirect($this->directory . strtolower($this->name) . '/SepView/' . $id);
            }
            return;
          }
        }
      }
    } else if ($a == 'Views') {
      $row = $this->Rujukan->read($p)[0];
      $this->set('row', $row);
      $this->render('RujukanView');
    } else if ($a == 'Print') {
      $row = $this->Rujukan->read($p)[0];
      $this->set('row', $row);
      $this->load->view('bridging/Bpjs/RujukanPrint', $this->data);  
    } else if ($a == 'Getby_no') {
      ///$p = '0000079979951';
      if (is_ajax()){
        $count_find = 0;
        $found_data = false;
        $category_store = array('noru'=>array('/Rujukan/','/Rujukan/RS/'), 'noka'=>array('/Rujukan/Peserta/','/Rujukan/RS/Peserta/'));
        $pencarian_berdasarkan = $this->input->post('rujuk_berdasarkan');
        if ($pencarian_berdasarkan == 'noru'){
          $category_store = $category_store['noru'];
        } else if ($pencarian_berdasarkan == 'noka'){
          $category_store = $category_store['noka'];
        }
        while($count_find < 2 && $found_data == false){
          $cat = $category_store[$count_find];
          $hasil = $this->vclaim_sep->request($cat.$p, "GET");
          // echo $cat.$p;
          $d = json_decode($hasil, true);
          if(isset($d['metaData']['code'])){
            if($d['metaData']['code']=='200'){
              if(!empty($d['response']['rujukan']['peserta']['noKartu'])){
                $dataRujukan = array(['noKunjungan' => $d['response']['rujukan']['noKunjungan'], 'nmAsuransi' => $d['response']['rujukan']['peserta']['cob']['nmAsuransi'],'noAsuransi' => $d['response']['rujukan']['peserta']['cob']['noAsuransi'],'tglTATcob' => $d['response']['rujukan']['peserta']['cob']['tglTAT'],'tglTMTcob' => $d['response']['rujukan']['peserta']['cob']['tglTMT'],'keteranganhakKelas' => $d['response']['rujukan']['peserta']['hakKelas']['keterangan'],'kodehakKelas' => $d['response']['rujukan']['peserta']['hakKelas']['kode'],'dinsos' => $d['response']['rujukan']['peserta']['informasi']['dinsos'],'noSKTM' => $d['response']['rujukan']['peserta']['informasi']['noSKTM'],'prolanisPRB' => $d['response']['rujukan']['peserta']['informasi']['prolanisPRB'],'keteranganjenisPeserta' => $d['response']['rujukan']['peserta']['jenisPeserta']['keterangan'],'kodejenisPeserta' => $d['response']['rujukan']['peserta']['jenisPeserta']['kode'],'noMR' => $d['response']['rujukan']['peserta']['mr']['noMR'],'noTelepon' => $d['response']['rujukan']['peserta']['mr']['noTelepon'],'nama' => $d['response']['rujukan']['peserta']['nama'],'nik' => $d['response']['rujukan']['peserta']['nik'],'noKartu' => $d['response']['rujukan']['peserta']['noKartu'],'pisa' => $d['response']['rujukan']['peserta']['pisa'],'kdProvider' => $d['response']['rujukan']['peserta']['provUmum']['kdProvider'],'nmProvider' => $d['response']['rujukan']['peserta']['provUmum']['nmProvider'],'sex' => $d['response']['rujukan']['peserta']['sex'],'keteranganstatusPeserta' => $d['response']['rujukan']['peserta']['statusPeserta']['keterangan'],'kodestatusPeserta' => $d['response']['rujukan']['peserta']['statusPeserta']['kode'],'tglCetakKartu' => $d['response']['rujukan']['peserta']['tglCetakKartu'],'tglLahir' => $d['response']['rujukan']['peserta']['tglLahir'],'tglTAT' => $d['response']['rujukan']['peserta']['tglTAT'],'tglTMT' => $d['response']['rujukan']['peserta']['tglTMT'],'umurSaatPelayanan' => $d['response']['rujukan']['peserta']['umur']['umurSaatPelayanan'],'umurSekarang' => $d['response']['rujukan']['peserta']['umur']['umurSekarang'],'kodediagnosa' => $d['response']['rujukan']['diagnosa']['kode'],'namadiagnosa' => $d['response']['rujukan']['diagnosa']['nama'],'kd_poli' =>  $d['response']['rujukan']['poliRujukan']['kode'],'nama_poli' =>  $d['response']['rujukan']['poliRujukan']['nama'],'tglKunjungan' => $d['response']['rujukan']['tglKunjungan'],'ppkRujukan' => $d['response']['rujukan']['provPerujuk']['kode'],'nmProviderPerujuk' => $d['response']['rujukan']['provPerujuk']['nama'],'code' => $d['metaData']['code'],'message' => $d['metaData']['message']]);
                if($count_find==0)
                {$dataRujukan[0]['AsalRujukanFaskes'] = '3';} //Faskes 1
                else if($count_find==1)
                {
                  if ($value['provPerujuk']['kode'] == '1007R001') {
                    $dataRujukan[0]['AsalRujukanFaskes'] = '4';
                  }else{
                    $dataRujukan[0]['AsalRujukanFaskes'] = '6';
                  }
                } //Faskes 2
                $found_data=true;
              }
            }
          }
          $count_find++;
        }
        if($found_data==false){
          if(isset($d['metaData']['code'])){
            $dataRujukan = array(['code' => $d['metaData']['code'],'message' => $d['metaData']['message']]);
          }else{
            $dataRujukan = array(['code' => '500','message' => 'Tidak dapat terhubung dengan server']);
          }
        }
      }
      echo json_encode($dataRujukan);
      return;
    } else if ($a == 'list_peserta') {
      ///$p = '0000079979951';
      if (is_ajax()){
        $count_find = 0;
        $found_data = false;
        $category_store = array('noka'=>array('/Rujukan/List/Peserta/','/Rujukan/RS/Peserta/'));
        $pencarian_berdasarkan = $this->input->post('rujuk_berdasarkan');
        $category_store = $category_store['noka'];
        while($count_find < 2 && $found_data == false){
            $cat = $category_store[$count_find];
          $hasil = $this->vclaim_sep->request($cat.$p, "GET");
          $d = json_decode($hasil, true);
          if(isset($d['metaData']['code'])){
            if($d['metaData']['code']=='200'){
              // if(!empty($d['response']['rujukan']['peserta']['noKartu'])){
                foreach ($d['response']['rujukan'] as $key => $value) {
                  $dataRujukan[$key] = ['noKunjungan' => $value['noKunjungan'], 'nmAsuransi' => $value['peserta']['cob']['nmAsuransi'],'noAsuransi' => $value['peserta']['cob']['noAsuransi'],'tglTATcob' => $value['peserta']['cob']['tglTAT'],'tglTMTcob' => $value['peserta']['cob']['tglTMT'],'keteranganhakKelas' => $value['peserta']['hakKelas']['keterangan'],'kodehakKelas' => $value['peserta']['hakKelas']['kode'],'dinsos' => $value['peserta']['informasi']['dinsos'],'noSKTM' => $value['peserta']['informasi']['noSKTM'],'prolanisPRB' => $value['peserta']['informasi']['prolanisPRB'],'keteranganjenisPeserta' => $value['peserta']['jenisPeserta']['keterangan'],'kodejenisPeserta' => $value['peserta']['jenisPeserta']['kode'],'noMR' => $value['peserta']['mr']['noMR'],'noTelepon' => $value['peserta']['mr']['noTelepon'],'nama' => $value['peserta']['nama'],'nik' => $value['peserta']['nik'],'noKartu' => $value['peserta']['noKartu'],'pisa' => $value['peserta']['pisa'],'kdProvider' => $value['peserta']['provUmum']['kdProvider'],'nmProvider' => $value['peserta']['provUmum']['nmProvider'],'sex' => $value['peserta']['sex'],'keteranganstatusPeserta' => $value['peserta']['statusPeserta']['keterangan'],'kodestatusPeserta' => $value['peserta']['statusPeserta']['kode'],'tglCetakKartu' => $value['peserta']['tglCetakKartu'],'tglLahir' => $value['peserta']['tglLahir'],'tglTAT' => $value['peserta']['tglTAT'],'tglTMT' => $value['peserta']['tglTMT'],'umurSaatPelayanan' => $value['peserta']['umur']['umurSaatPelayanan'],'umurSekarang' => $value['peserta']['umur']['umurSekarang'],'kodediagnosa' => $value['diagnosa']['kode'],'namadiagnosa' => $value['diagnosa']['nama'],'kd_poli' =>  $value['poliRujukan']['kode'],'nama_poli' =>  $value['poliRujukan']['nama'],'nmProviderPerujuk' => $value['provPerujuk']['nama'],'tglKunjungan' => $value['tglKunjungan'],'ppkRujukan' => $value['provPerujuk']['kode'],'code' => $d['metaData']['code'],'message' => $d['metaData']['message']];
                }
                // $dataRujukan = array();
                if($count_find==0)
                {$dataRujukan[0]['AsalRujukanFaskes'] = '3';} //Faskes 1
                else if($count_find==1)
                {$dataRujukan[0]['AsalRujukanFaskes'] = '4';} //Faskes 2
                $found_data=true;
              // }
            }
          }
          $count_find++;
        }
        if($found_data==false){
          if(isset($d['metaData']['code'])){
            $dataRujukan = array(['code' => $d['metaData']['code'],'message' => $d['metaData']['message']]);
          }else{
            $dataRujukan = array(['code' => '500','message' => 'Tidak dapat terhubung dengan server']);
          }
        }
      }
      echo json_encode($dataRujukan);
      return;  
    } else if ($a == 'list_peserta_rec') {
      ///$p = '0000079979951';
      if (is_ajax()){
        $count_find = 0;
        $found_data = false;
        $category_store = array('noka'=>array('/Rujukan/Peserta/','/Rujukan/RS/Peserta/'));
        $pencarian_berdasarkan = $this->input->post('rujuk_berdasarkan');
        $category_store = $category_store['noka'];
        while($count_find < 2 && $found_data == false){
            $cat = $category_store[$count_find];
          $hasil = $this->vclaim_sep->request($cat.$p, "GET");
          $d = json_decode($hasil, true);
          if(isset($d['metaData']['code'])){
            if($d['metaData']['code']=='200'){
              // if(!empty($d['response']['rujukan']['peserta']['noKartu'])){
                $value = $d['response']['rujukan'];
                  $dataRujukan[0] = ['noKunjungan' => $value['noKunjungan'], 'nmAsuransi' => $value['peserta']['cob']['nmAsuransi'],'noAsuransi' => $value['peserta']['cob']['noAsuransi'],'tglTATcob' => $value['peserta']['cob']['tglTAT'],'tglTMTcob' => $value['peserta']['cob']['tglTMT'],'keteranganhakKelas' => $value['peserta']['hakKelas']['keterangan'],'kodehakKelas' => $value['peserta']['hakKelas']['kode'],'dinsos' => $value['peserta']['informasi']['dinsos'],'noSKTM' => $value['peserta']['informasi']['noSKTM'],'prolanisPRB' => $value['peserta']['informasi']['prolanisPRB'],'keteranganjenisPeserta' => $value['peserta']['jenisPeserta']['keterangan'],'kodejenisPeserta' => $value['peserta']['jenisPeserta']['kode'],'noMR' => $value['peserta']['mr']['noMR'],'noTelepon' => $value['peserta']['mr']['noTelepon'],'nama' => $value['peserta']['nama'],'nik' => $value['peserta']['nik'],'noKartu' => $value['peserta']['noKartu'],'pisa' => $value['peserta']['pisa'],'kdProvider' => $value['peserta']['provUmum']['kdProvider'],'nmProvider' => $value['peserta']['provUmum']['nmProvider'],'sex' => $value['peserta']['sex'],'keteranganstatusPeserta' => $value['peserta']['statusPeserta']['keterangan'],'kodestatusPeserta' => $value['peserta']['statusPeserta']['kode'],'tglCetakKartu' => $value['peserta']['tglCetakKartu'],'tglLahir' => $value['peserta']['tglLahir'],'tglTAT' => $value['peserta']['tglTAT'],'tglTMT' => $value['peserta']['tglTMT'],'umurSaatPelayanan' => $value['peserta']['umur']['umurSaatPelayanan'],'umurSekarang' => $value['peserta']['umur']['umurSekarang'],'kodediagnosa' => $value['diagnosa']['kode'],'namadiagnosa' => $value['diagnosa']['nama'],'kd_poli' =>  $value['poliRujukan']['kode'],'nama_poli' =>  $value['poliRujukan']['nama'],'nmProviderPerujuk' => $value['provPerujuk']['nama'],'tglKunjungan' => $value['tglKunjungan'],'ppkRujukan' => $value['provPerujuk']['kode'],'code' => $d['metaData']['code'],'message' => $d['metaData']['message']];
                // $dataRujukan = array();
                if($count_find==0)
                {$dataRujukan[0]['AsalRujukanFaskes'] = '3';} //Faskes 1
                else if($count_find==1)
                {$dataRujukan[0]['AsalRujukanFaskes'] = '4';} //Faskes 2
                $found_data=true;
              // }
            }
          }
          $count_find++;
        }
        if($found_data==false){
          if(isset($d['metaData']['code'])){
            $dataRujukan = array(['code' => $d['metaData']['code'],'message' => $d['metaData']['message']]);
          }else{
            $dataRujukan = array(['code' => '500','message' => 'Tidak dapat terhubung dengan server']);
          }
        }
      }
      echo json_encode($dataRujukan);
      return;    
    } else {
      $this->title = 'Bridging BPJS';
      $model = 'Rujukan';
      $this->set('primaryKey', $this->$model->primaryKey);
      $this->render('RujukanIndex');
      $this->add_script('plugins/DataTables-1.10.12/datatables.min.js');
    }
  }

  public function pengajuan(){
    $trx_detail_1 = "pengajuan";
    $this->$trx_detail_1->whereFields['a.flag_aprov'] = 0;
    $this->set('row',$this->$trx_detail_1->read());
    $this->render('pengajuan');
  }

  public function aprov($id,$id_trx_pendaftaran=0){
    $trx_detail_1 = "pengajuan";
    $data_pengajuan = $this->$trx_detail_1->read($id)[0];
    $data['request']['t_sep']['noKartu'] = $data_pengajuan->noKartu;
    $data['request']['t_sep']['tglSep'] = $data_pengajuan->tglSep;
    $data['request']['t_sep']['jnsPelayanan'] = $data_pengajuan->jnsPelayanan;
    $data['request']['t_sep']['keterangan'] = $data_pengajuan->keterangan;
    $data['request']['t_sep']['user'] = $data_pengajuan->user;
    $data_update[$trx_detail_1]['id_trx_pengajuan_sep'] = $id;
    $data_update[$trx_detail_1]['flag_aprov'] = 1;
    $req = json_encode($data);
    $cat = "/Sep/aprovalSEP";
    // $hasil = $this->vclaim_sep->post_vclaim($req,$cat);
    $hasil = $this->vclaim_sep->request("/Sep/aprovalSEP", "POST", $req);
    $d = json_decode($hasil, true);
    // var_dump($d);
    if ($d['metaData']['code'] == '200') {
      $this->$trx_detail_1->replace($data_update[$trx_detail_1]);
      $this->setFlash('Suksess: ', 'Suksess  '.$d['metaData']['message'].'!.', 'alert-success');
    }else{
      $this->setFlash('FAILED: ', 'Failed '.$d['metaData']['message'].'.', 'alert-danger');
    }
    redirect('bridging/Bpjs/pengajuan','refresh');
  }

  public function create_pengajuan($id = 0){
    $this->title = "Pengajuan Penjaminan";
    $trx_head_1 = "Pendaftaran";
    $trx_detail_1 = "pengajuan";
    $trx_detail_2 = "pegawai";
    $this->set('id_trx_pendataran',$id);
    $this->$trx_head_1->whereFields['a.id_trx_pendaftaran'] = $id;
    $this->set('peserta',$this->$trx_head_1->read());
    $this->$trx_detail_1->whereFields['a.id_trx_pendaftaran'] = $id;
    $this->set('cek_data',$this->$trx_detail_1->read());
    $this->set('trx_detail_1',$trx_detail_1);
    $this->render('create_pengajuan');
  }

  public function buat_pengajuan(){
    $trx_head_1 = "Pendaftaran";
    $trx_detail_1 = "pengajuan";
    $trx_detail_2 = "pegawai";
    $data = $this->input->post();
    // print_r($data);
    $this->$trx_detail_2->whereFields['a.id_mst_pegawai'] = $this->auth->get_user('id_mst_pegawai');
    $peg = $this->$trx_detail_2->read()[0];
    $req['request']['t_sep']['noKartu'] = $data['noKartu'];
    $req['request']['t_sep']['tglSep'] = $data['tglSep'];
    $req['request']['t_sep']['jnsPelayanan'] = $data['jnsPelayanan'];
    $req['request']['t_sep']['keterangan'] = $data['keterangan'];
    $req['request']['t_sep']['user'] = $peg->nm_mst_pegawai_lengkap;
    $data[$trx_detail_1]['user'] = $peg->nm_mst_pegawai_lengkap;
    $this->db->trans_start();
      $req = json_encode($req);
      $cat = "/Sep/pengajuanSEP";
      $hasil = $this->vclaim->post_vclaim($req,$cat);
      if ($hasil['metaData']['code'] == '200') {
        $data[$trx_detail_1]['id_trx_pendaftaran'] = $data['id_trx_pendaftaran'];
        $data[$trx_detail_1]['noKartu'] = $data['noKartu'];
        $data[$trx_detail_1]['tglSep'] = $data['tglSep'];
        $data[$trx_detail_1]['jnsPelayanan'] = $data['jnsPelayanan'];
        $data[$trx_detail_1]['keterangan'] = $data['keterangan'];
        $this->$trx_detail_1->create($data[$trx_detail_1]);
        $this->setFlash('Suksess: ', 'Suksess  '.$hasil['metaData']['message'].'!.', 'alert-success');
      }else{
        $this->setFlash('FAILED: ',$hasil['metaData']['message'], 'alert-danger');
      }
    $this->db->trans_complete();
  }

  public function cari_peserta(){
    $trx_detail_1 = "pengajuan";
    if (count($this->$trx_detail_1->get_peserta($this->input->post('no'))) > 0) {
       echo json_encode($this->$trx_detail_1->get_peserta($this->input->post('no')));
    }
  }

  public function lpk(){
    if (count($this->input->post()) > 0) {
      $post = $this->input->post();
      $this->klaim->whereFields['a.tgl_klaim'] = $post['tgl'];
      $this->klaim->whereFields['b.jnsPelayanan'] = $post['jenis_layanan'];
      $this->klaim->whereFields['a.flag_aktif'] = '1';
      $this->set('hasil',$this->klaim->read());
    }
    $this->render('data_lpk');
  }

  public function CreateKlaimRedirect($sep,$kasir = null){
    $trx_detail_1 = "lpk";
    $trx_detail_2 = "pegawai";
    $totalDibayar = 0;
    $this->$trx_detail_2->whereFields['a.id_mst_pegawai'] = $this->auth->get_user('id_mst_pegawai');
    $peg = $this->$trx_detail_2->read()[0];
    $post['sep'] = $sep;
    $this->SEP->whereFields['a.noSep'] = $post['sep'];
    $dataSep = $this->SEP->read()[0];
    $dataSekunder = $this->SEP->get_sekunder($dataSep->id_trx_pendaftaran);
    $dataTindakan = $this->SEP->get_procedure($dataSep->id_trx_pendaftaran);
    $log['date'] = date('Y-m-d');
    $log['user'] = $this->session->userdata('nm_mst_pegawai_lengkap');
    $log['id_trx_pendaftaran'] = $dataSep->id_trx_pendaftaran;
    $diagnosa = [];
    $diagnosaEklaim = [];
    $tindakanEklaim = [];
    if ($dataSep->diagAwal != "") {
      $diagnosa[0]['kode'] = $dataSep->kd_icd_icd10;
      $diagnosa[0]['level'] = 1;
      $diagnosaEklaim[] = $dataSep->kd_icd_icd10;
    }
    foreach ($dataSekunder as $key => $ds) {
      $diagnosa[] = array('kode' => $ds->kd_mst_icd10, 'level' => 2);
      $diagnosaEklaim[] = $ds->kd_mst_icd10;
    }
    $tindakan = [];
    foreach ($dataTindakan as $key => $dt) {
      $tindakan[] = array('kode' => $dt->kd_mst_icd9cm);
      $tindakanEklaim[] = $dt->kd_mst_icd9cm;
    }
    $this->trx_tempat_tidur->whereFields['a.id_trx_pendaftaran'] = $dataSep->id_trx_pendaftaran;
    $dataruangRawat = $this->trx_tempat_tidur->read();
    $ruangRawat = "";
    $kelasRawat = "";
    foreach ($dataruangRawat as $key => $dr) {
      $ruangRawat = $dr->id_ruangrawat;
      $kelasRawat = $dr->id_mst_kelas_layanan;
    }
    $upgradeKelas = '0';
    $kelasNaik = '0';
    $cekKelas = [];
    $lamaNaik_kelas = '0';
    $total = 0;
    $dataTempatTidur = json_decode($this->Pendaftaran->getTempatTidur($dataSep->id_trx_pendaftaran));
    if (count($dataTempatTidur) > 0) {
      $kelasNaik = $dataTempatTidur[0]->kd_mst_kelas_layanan;
      foreach ($dataTempatTidur as $key => $value) {
        $cekKelas[] = $value->kd_mst_kelas_layanan;
        $total += $value->nilai_total;
        if ($value->kd_mst_kelas_layanan > 3) {
          if ($kelasNaik < $value->kd_mst_kelas_layanan ) {
            $kelasNaik = $value->kd_mst_kelas_layanan;
            $upgradeKelas ='1';
            $start_date = new DateTime($value->tgl_checkin);
            $end_date = new DateTime($value->tgl_checkout);
            $interval = $start_date->diff($end_date);
            $lamaNaik_kelas = $interval->days;
          }
        } else if($value->kd_mst_kelas_layanan < 3) {
          if ($kelasNaik > $value->kd_mst_kelas_layanan) {
            $kelasNaik = $value->kd_mst_kelas_layanan;
            $upgradeKelas ='1';
            $start_date = new DateTime($value->tgl_checkin);
            $end_date = new DateTime($value->tgl_checkout);
            $interval = $start_date->diff($end_date);
            $lamaNaik_kelas = $interval->days;
          }
        } else {
          $upgradeKelas = '0';
        }
      }
    }
    if ($lamaNaik_kelas == '0' && $upgradeKelas == '1') {
      $lamaNaik_kelas = '1';
    }
    $kodePoli = "null";
    $cara_pulang = '5';
    $this->Pendaftaran->whereFields['a.id_trx_pendaftaran'] = $dataSep->id_trx_pendaftaran;
    $DataPendaftaran = $this->Pendaftaran->read();
    foreach ($DataPendaftaran as $key => $DataPendaftaran) {
      $kodePoli = $DataPendaftaran->kd_poli_bpjs;
      $cara_pulang = $DataPendaftaran->flag_cara_pulang;
      if ($DataPendaftaran->flag_RTL>0 && $DataPendaftaran->flag_RTL<5) {
        $flag_RTL = $DataPendaftaran->flag_RTL;
        $tgl_kontrol = $DataPendaftaran->tgl_kontrol;
        $poliRTL=$dataSep->tujuan;
        if ($flag_RTL==3) {
          $this->Rujukan->whereFields['a.'.'noSep']=$dataSep->noSep;
          $data_rujukan=$this->Rujukan->read()[0];
          $kodePPK=$data_rujukan->kodetujuanRujukan;
        } else {
          $kodePPK='';
        }
      } else {
        $flag_RTL = '1';
        $tgl_kontrol = '';
        $poliRTL='';
        $kodePPK='';
      }
    }
    $totalBiaya = $this->pendapatan->getTotalTagihan($dataSep->id_trx_pendaftaran);
    $dataLpk['request']['t_lpk']['noSep'] = $post['sep'];
    $dataLpk['request']['t_lpk']['tglMasuk'] = date("Y-m-d", strtotime($dataSep->tglSep));
    $dataLpk['request']['t_lpk']['tglKeluar'] = date("Y-m-d", strtotime($dataSep->tglSep));
    $dataLpk['request']['t_lpk']['jaminan'] = "1";
    $dataLpk['request']['t_lpk']['poli']['poli'] = ($dataSep->jnsPelayanan == 2) ? $kodePoli : '';
    $dataLpk['request']['t_lpk']['perawatan']['ruangRawat'] = ($dataSep->jnsPelayanan == 1) ? $ruangRawat : "";
    $dataLpk['request']['t_lpk']['perawatan']['kelasRawat'] = ($dataSep->jnsPelayanan == 1) ? $kelasRawat : "";
    $dataLpk['request']['t_lpk']['perawatan']['spesialistik'] = ($dataSep->jnsPelayanan == 1) ? '11' : "";
    $dataLpk['request']['t_lpk']['perawatan']['caraKeluar'] = ($dataSep->jnsPelayanan == 2) ? "" : $dataSep->flag_cara_pulang;
    $dataLpk['request']['t_lpk']['perawatan']['kondisiPulang'] = ($dataSep->jnsPelayanan == 2) ? "" : $dataSep->flag_cara_pulang;
    $dataLpk['request']['t_lpk']['diagnosa'] = $diagnosa;
    $dataLpk['request']['t_lpk']['procedure'] = $tindakan;
    $dataLpk['request']['t_lpk']['rencanaTL']['tindakLanjut'] = $flag_RTL;
    $dataLpk['request']['t_lpk']['rencanaTL']['dirujukKe']['kodePPK'] = $kodePPK;
    $dataLpk['request']['t_lpk']['rencanaTL']['kontrolKembali']['tglKontrol'] = $tgl_kontrol;
    $dataLpk['request']['t_lpk']['rencanaTL']['kontrolKembali']['poli'] = $poliRTL;
    $dataLpk['request']['t_lpk']['DPJP'] = ($dataSep->dpjpbaru == "") ? $dataSep->idbpjslama : $dataSep->idbpjsbaru;
    $dataLpk['request']['t_lpk']['user'] = $peg->nm_mst_pegawai_lengkap;
    $cat = "/LPK/insert";
    $req = json_encode($dataLpk);
    $dSep['flag_klaim'] = 1;
    $dSep['noSep'] = $post['sep'];
    $dklaim['noSep'] = $post['sep'];
    $dklaim['tgl_klaim'] = date('Y-m-d');
    $dklaim['id_mst_pegawai'] = $this->auth->get_user('id_mst_pegawai');
    $hasil = $this->vclaim->post_vclaim($req,$cat);
    $log['log'] = json_encode($hasil);
    $log['keterangan'] = 'vklaim';
    $this->trx_eklaim->insertlog($log);
    $dataEklaim['metadata']['method'] = "set_claim_data";
    $dataEklaim['metadata']['nomor_sep'] = $post['sep'];
    $dataEklaim['data']['nomor_sep'] = $post['sep'];
    $dataEklaim['data']['nomor_kartu'] = $dataSep->noKartu;
    $dataEklaim['data']['tgl_masuk'] = date("Y-m-d H:i:s", strtotime($dataSep->tglSep));
    $dataEklaim['data']['tgl_pulang'] = date("Y-m-d H:i:s");
    $dataEklaim['data']['jenis_rawat'] = $dataSep->jnsPelayanan;
    $dataEklaim['data']['kelas_rawat'] = $dataSep->klsRawat;
    $dataEklaim['data']['adl_sub_acute'] = "45";
    $dataEklaim['data']['adl_chronic'] = "45";
    $dataEklaim['data']['icu_indikator'] = "0";
    $dataEklaim['data']['icu_los'] = "0";
    $dataEklaim['data']['ventilator_hour'] = "0";
    $dataEklaim['data']['upgrade_class_ind'] = $upgradeKelas;
    $dataEklaim['data']['upgrade_class_class'] = $kelasNaik;
    $dataEklaim['data']['upgrade_class_los'] = $lamaNaik_kelas;
    $dataEklaim['data']['add_payment_pct'] = "0";
    $dataEklaim['data']['birth_weight'] = "0";
    $dataEklaim['data']['discharge_status'] = $cara_pulang;
    $dataEklaim['data']['diagnosa'] = implode("#",$diagnosaEklaim);
    $dataEklaim['data']['procedure'] = implode("#",$tindakanEklaim);
    $dataEklaim['data']['tarif_rs']['prosedur_non_bedah'] = $totalBiaya + $total;
    $dataEklaim['data']['tarif_rs']['prosedur_bedah'] = "";
    $dataEklaim['data']['tarif_rs']['konsultasi'] = "";
    $dataEklaim['data']['tarif_rs']['tenaga_ahli'] = "";
    $dataEklaim['data']['tarif_rs']['keperawatan'] = "";
    $dataEklaim['data']['tarif_rs']['penunjang'] = "";
    $dataEklaim['data']['tarif_rs']['radiologi'] = "";
    $dataEklaim['data']['tarif_rs']['laboratorium'] = "";
    $dataEklaim['data']['tarif_rs']['pelayanan_darah'] = "";
    $dataEklaim['data']['tarif_rs']['rehabilitasi'] = "";
    $dataEklaim['data']['tarif_rs']['kamar'] = "";
    $dataEklaim['data']['tarif_rs']['rawat_intensif'] = "";
    $dataEklaim['data']['tarif_rs']['obat'] = "";
    $dataEklaim['data']['tarif_rs']['alkes'] = "";
    $dataEklaim['data']['tarif_rs']['bmhp'] = "";
    $dataEklaim['data']['tarif_rs']['sewa_alat'] = "";
    $dataEklaim['data']['tarif_poli_eks'] = "0";
    $dataEklaim['data']['nama_dokter'] = ($dataSep->dpjpbaru == "") ? $dataSep->dpjplama : $dataSep->dpjpbaru ;;
    $dataEklaim['data']['kode_tarif'] = "CP";
    $dataEklaim['data']['payor_id'] = "3";
    $dataEklaim['data']['payor_cd'] = "JKN";
    $dataEklaim['data']['cob_cd'] = "0001";
    $dataEklaim['data']['coder_nik'] = "123123123123";
    $hasilEklaim = $this->eklaim->claim(json_encode($dataEklaim));
    $hasil['eklaim'] = json_decode($hasilEklaim);
    $log['log'] = json_encode($hasil['eklaim']);
    $log['keterangan'] = 'eklaim';
    $this->trx_eklaim->insertlog($log);
    if ($hasil['eklaim']->metadata->code == '200') {
      $grouper1['metadata']['method'] = 'grouper';
      $grouper1['metadata']['stage'] = '1';
      $grouper1['data']['nomor_sep'] = $post['sep'];
      $hasilGrouper1 = json_decode($this->eklaim->claim(json_encode($grouper1)));
      $log['log'] = json_encode($hasilGrouper1);
      $log['keterangan'] = 'grouper1';
      $this->trx_eklaim->insertlog($log);
      if ($hasilGrouper1->metadata->code == '200') {
        if (isset($hasilGrouper1->special_cmg_option)) {
            $cmg = '#';
            $tmpCmg = [];
               foreach ($hasilGrouper1->special_cmg_option as $key => $sco) {
                  $tmpCmg[] = $sco->code;
                }
            if (count($tmpCmg) > 0) {
              $cmg = implode('#', $tmpCmg);
            }
            $grouper2['metadata']['method'] = 'grouper';
            $grouper2['metadata']['stage'] = '2';
            $grouper2['data']['nomor_sep'] = $post['sep'];
            $grouper2['data']['special_cmg'] = $cmg;
            $hasilGrouper2 = json_decode($this->eklaim->claim(json_encode($grouper2)));
            $log['log'] = json_encode($hasilGrouper2);
            $log['keterangan'] = 'grouper2';
            $this->trx_eklaim->insertlog($log);
            if ($hasilGrouper2->metadata->code == '200') {
              $flaggrouper['flag_grouper'] =  '1';
              $flaggrouper['noSep'] =  $post['sep'];
              $dataSave['id_trx_pendaftaran'] = $dataSep->id_trx_pendaftaran;
              $dataSave['no_sep'] = $post['sep'];
              $dataSave['tgl_klaim'] = date('Y-m-d');
              $dataSave['user_klaim'] = $peg->nm_mst_pegawai_lengkap;
              $dataSave['tagihan'] = $totalBiaya + $total;
              $dataSave['code'] = $hasilGrouper2->response->cbg->code;
              $dataSave['description'] = $hasilGrouper2->response->cbg->description;
              $dataSave['tarif'] = $hasilGrouper2->response->cbg->tariff;
              $totalDibayar += $hasilGrouper2->response->cbg->tariff;
              $dataSave['kelas'] = $hasilGrouper2->response->kelas;
              if (isset($hasilGrouper1->response->add_payment_amt)) {
                $dataSave['add_payment_amt'] = $hasilGrouper1->response->add_payment_amt;
              } else {
                $dataSave['add_payment_amt'] = '0';
              }
              $dataSave['inacbg_version'] = $hasilGrouper2->response->inacbg_version;
              $this->db->trans_start();
              $this->trx_eklaim->create($dataSave);
              $this->SEP->replace($flaggrouper);
              $this->db->trans_complete();
              if ($this->db->trans_status() === false) {
                $log['log'] = 'Gagal Simpan Data Eklaim';
                $this->trx_eklaim->insertlog($log);
              }else{
                $log['log'] = 'Berhasil Simpan Data Eklaim';
                $this->trx_eklaim->insertlog($log);
                $kirim['metadata']['method'] = 'send_claim_individual';
                $kirim['data']['nomor_sep'] = $post['sep'];
                $hasilKirim = json_decode($this->eklaim->claim(json_encode($kirim)));
                $log['log'] = json_encode($hasilGrouper2);
                $log['keterangan'] = 'grouper2';
                $this->trx_eklaim->insertlog($log);
                if ($hasilKirim->metadata->code == '200') {
                  $flagfinal['flag_final'] =  '1';
                  $flagfinal['noSep'] =  $post['sep'];
                  $this->SEP->replace($flagfinal);
                }
              }

              foreach ($hasilGrouper2->response->special_cmg as $key => $specialCmg) {
                $data['special_cmg']['id_trx_pendaftaran'] = $dataSep->id_trx_pendaftaran;
                $data['special_cmg']['no_sep'] = $post['sep'];
                $data['special_cmg']['tgl_klaim'] = date('Y-m-d');
                $data['special_cmg']['user_klaim'] = $peg->nm_mst_pegawai_lengkap;
                $data['special_cmg']['tagihan'] = $totalBiaya + $total;
                $data['special_cmg']['code'] = $hasilGrouper1->response->special_cmg->code;
                $data['special_cmg']['description'] = $hasilGrouper1->response->special_cmg->description;
                $data['special_cmg']['tarif'] = $hasilGrouper1->response->special_cmg->tariff;
                 $totalDibayar += $hasilGrouper1->response->special_cmg->tariff;
                $data['special_cmg']['kelas'] = $hasilGrouper1->response->kelas;
                $data['special_cmg']['add_payment_amt'] = $hasilGrouper1->response->add_payment_amt;
                $data['special_cmg']['inacbg_version'] = $hasilGrouper1->response->inacbg_version;
                $data['special_cmg']['keterangan'] = 'Sub Acute';
                $this->trx_eklaim->create($data['special_cmg']);
              }
          }
        } else {
          $flaggrouper['flag_grouper'] =  '1';
          $flaggrouper['noSep'] =  $post['sep'];
          $dataSave['id_trx_pendaftaran'] = $dataSep->id_trx_pendaftaran;
          $dataSave['no_sep'] = $post['sep'];
          $dataSave['tgl_klaim'] = date('Y-m-d');
          $dataSave['user_klaim'] = $peg->nm_mst_pegawai_lengkap;
          $dataSave['tagihan'] = $totalBiaya + $total;
          $dataSave['code'] = $hasilGrouper1->response->cbg->code;
          $dataSave['description'] = $hasilGrouper1->response->cbg->description;
          $dataSave['tarif'] = $hasilGrouper1->response->cbg->tariff;
          $totalDibayar += $hasilGrouper1->response->cbg->tariff;
          $dataSave['kelas'] = $hasilGrouper1->response->kelas;
          if (isset($hasilGrouper1->response->add_payment_amt)) {
            $dataSave['add_payment_amt'] = $hasilGrouper1->response->add_payment_amt;
          } else {
            $dataSave['add_payment_amt'] = '0';
          }
          $dataSave['inacbg_version'] = $hasilGrouper1->response->inacbg_version;
          $this->db->trans_start();
          $this->trx_eklaim->create($dataSave);
          $this->SEP->replace($flaggrouper);
          $this->db->trans_complete();
          if ($this->db->trans_status() === false) {
            $log['log'] = 'Gagal Simpan Data Eklaim';
            $this->trx_eklaim->insertlog($log);
          }else{
            $log['log'] = 'Berhasil Simpan Data Eklaim';
            $this->trx_eklaim->insertlog($log);
            $kirim['metadata']['method'] = 'send_claim_individual';
            $kirim['data']['nomor_sep'] = $post['sep'];
            $hasilKirim = json_decode($this->eklaim->claim(json_encode($kirim)));
            $log['log'] = json_encode($hasilGrouper2);
            $log['keterangan'] = 'grouper2';
            $this->trx_eklaim->insertlog($log);
            if ($hasilKirim->metadata->code == '200') {
              $flagfinal['flag_final'] =  '1';
              $flagfinal['noSep'] =  $post['sep'];
              $this->SEP->replace($flagfinal);
            }
          }
        }

        if (isset($hasilGrouper1->sub_acute)) {
          $data['sub_acute']['id_trx_pendaftaran'] = $dataSep->id_trx_pendaftaran;
          $data['sub_acute']['no_sep'] = $post['sep'];
          $data['sub_acute']['tgl_klaim'] = date('Y-m-d');
          $data['sub_acute']['user_klaim'] = $peg->nm_mst_pegawai_lengkap;
          $data['sub_acute']['tagihan'] = $totalBiaya + $total;
          $data['sub_acute']['code'] = $hasilGrouper1->response->sub_acute->code;
          $data['sub_acute']['description'] = $hasilGrouper1->response->sub_acute->description;
          $data['sub_acute']['tarif'] = $hasilGrouper1->response->sub_acute->tariff;
          $totalDibayar += $hasilGrouper1->response->sub_acute->tariff;
          $data['sub_acute']['kelas'] = $hasilGrouper1->response->kelas;
          $data['sub_acute']['add_payment_amt'] = $hasilGrouper1->response->add_payment_amt;
          $data['sub_acute']['inacbg_version'] = $hasilGrouper1->response->inacbg_version;
          $data['sub_acute']['keterangan'] = 'Sub Acute';
          $this->trx_eklaim->create($data['sub_acute']);
        }
        if (isset($hasilGrouper1->chronic)) {
          $data['chronic']['id_trx_pendaftaran'] = $dataSep->id_trx_pendaftaran;
          $data['chronic']['no_sep'] = $post['sep'];
          $data['chronic']['tgl_klaim'] = date('Y-m-d');
          $data['chronic']['user_klaim'] = $peg->nm_mst_pegawai_lengkap;
          $data['chronic']['tagihan'] = $totalBiaya + $total;
          $data['chronic']['code'] = $hasilGrouper1->response->chronic->code;
          $data['chronic']['description'] = $hasilGrouper1->response->chronic->description;
          $data['chronic']['tarif'] = $hasilGrouper1->response->chronic->tariff;
          $totalDibayar += $hasilGrouper1->response->chronic->tariff;
          $data['chronic']['kelas'] = $hasilGrouper1->response->kelas;
          $data['chronic']['add_payment_amt'] = $hasilGrouper1->response->add_payment_amt;
          $data['chronic']['inacbg_version'] = $hasilGrouper1->response->inacbg_version;
          $data['chronic']['keterangan'] = 'chronic';
          $this->trx_eklaim->create($data['chronic']);
        }
        $finish['metadata']['method'] = 'claim_final';
        $finish['data']['nomor_sep'] = $post['sep'];
        $finish['data']['coder_nik'] = '123123123123';
        $hasilfinish = json_decode($this->eklaim->claim(json_encode($finish)));
        $log['log'] = json_encode($hasilfinish);
        $log['keterangan'] = 'Final Eklaim';
        $this->trx_eklaim->insertlog($log);

        // $id = $this->Pendaftaran->getRincian($dataSep->id_trx_pendaftaran)->id_trx_rincian;

        // $this->load->model('bridging/Md_trx_eklaim_model','eklaim');
        // $this->load->model('billing/Pelunasan_detail_model','pelunasan_detail');
        // $this->load->model('billing/Pelunasan_model','pelunasan');
        // $this->eklaim->get_klaim();
        // $this->eklaim->whereFields['c.'.'id_trx_pelunasan']=$id;
        // $dataklaim=$this->eklaim->read();
        // if(isset($dataklaim[0]))
        // {
        //     $dataklaim=$dataklaim[0];
        //     $pelunasan_detail_data['id_trx_pelunasan']=$id;
        //     $pelunasan_detail_data['id_trx_pendaftaran']=$dataklaim->id_trx_pendaftaran;
        //     $pelunasan_detail_data['id_trx_rincian']=$dataklaim->id_trx_rincian;
        //     $pelunasan_detail_data['id_mst_cara_bayar_billing']='2';
        //     $pelunasan_detail_data['id_mst_cara_bayar_sub_billing']='2';
        //     $pelunasan_detail_data['nilai']=$dataklaim->tarif;
        //     $pelunasan_detail_data['biaya_kartu_kredit']='0';
        //     $pelunasan_detail_data['flag_active']='1';
        //     $this->pelunasan_detail->create($pelunasan_detail_data);
        //     $pelunasan_data['id_trx_pelunasan']=$id;
        //     $pelunasan_data['nilai_pembayaran']=$dataklaim->tarif;
        //     $this->pelunasan->replace($pelunasan_data);
        // }


        $dataRincian = $this->Pendaftaran->getRincian($dataSep->id_trx_pendaftaran);
        $dataPelunasan['id_trx_pendaftaran'] = $dataSep->id_trx_pendaftaran;
        $dataPelunasan['id_trx_rincian'] = $dataRincian->id_trx_rincian;
        $dataPelunasan['nilai_rincian'] = $dataRincian->nilai_total;
        $dataPelunasan['nilai_pembayaran'] = $totalDibayar;
        $dataRincianSave['flag_lunas'] = '1';
        $dataRincianSave['id_trx_rincian'] = $dataRincian->id_trx_rincian;
        $id = $this->Pelunasan->create($dataPelunasan);
        $this->udpdatePelunasan($id);
      }
    }
    if ($hasil['metaData']['code'] == '200' && $hasil['eklaim']->metadata->code == '200') {
      $this->db->trans_start();
      $this->SEP->replace($dSep);
      $this->klaim->create($dklaim);
      $this->db->trans_complete();
      if ($this->db->trans_status() === false) {
          $this->setFlash('FAILED: ', 'Failed Save Data To Database ! Contact your IT Support.', 'alert-danger');
      }else{
        $this->setFlash('SUCCESS:','Sukses Save Data to Database ! ','alert-success');
      }
      $this->setFlash('SUCCESS:',print_r($hasil),'alert-success');
      if ($kasir != null) {
        redirect('billing/rincian/detail/'.$dataSep->id_trx_pendaftaran.'/0/'.$dataSep->kd_trx_pendaftaran,'refresh');
      }else{
        redirect('bridging/Bpjs/list_index','refresh');
      }
    }else{
      $this->setFlash('FAILED: ', $hasil['metaData']['message'], 'alert-danger');
      if ($kasir != null) {
        redirect('billing/rincian/detail/'.$dataSep->id_trx_pendaftaran.'/0/'.$dataSep->kd_trx_pendaftaran,'refresh');
      }else{
        redirect('bridging/Bpjs/list_index','refresh');
      }
    }
  }

  public function create_klaim(){
    $trx_detail_1 = "lpk";
    $trx_detail_2 = "pegawai";
    if (count($this->input->post()) > 0) {
      $this->$trx_detail_2->whereFields['a.id_mst_pegawai'] = $this->auth->get_user('id_mst_pegawai');
      $peg = $this->$trx_detail_2->read()[0];
      $post = $this->input->post();
      $this->SEP->whereFields['a.noSep'] = $post['sep'];
      $dataSep = $this->SEP->read()[0];
      $dataSekunder = $this->SEP->get_sekunder($dataSep->id_trx_pendaftaran);
      $dataTindakan = $this->SEP->get_procedure($dataSep->id_trx_pendaftaran);
      $log['date'] = date('Y-m-d');
      $log['user'] = $this->session->userdata('nm_mst_pegawai_lengkap');
      $log['id_trx_pendaftaran'] = $dataSep->id_trx_pendaftaran;
      $diagnosa = [];
      $diagnosaEklaim = [];
      $tindakanEklaim = [];
      if ($dataSep->kd_mst_icd10 != "") {
        $diagnosa[0]['kode'] = $dataSep->diagAwal;
        $diagnosa[0]['level'] = 1;
        $diagnosaEklaim[] = $dataSep->diagAwal;
      }
      foreach ($dataSekunder as $key => $ds) {
        $diagnosa[] = array('kode' => $ds->kd_mst_icd10, 'level' => 2);
        $diagnosaEklaim[] = $ds->kd_mst_icd10;
      }
      $tindakan = [];
      foreach ($dataTindakan as $key => $dt) {
        $tindakan[] = array('kode' => $dt->kd_mst_icd9cm);
        $tindakanEklaim[] = $dt->kd_mst_icd9cm;
      }
      $this->trx_tempat_tidur->whereFields['a.id_trx_pendaftaran'] = $dataSep->id_trx_pendaftaran;
      $dataruangRawat = $this->trx_tempat_tidur->read();
      $ruangRawat = "";
      $kelasRawat = "";
      foreach ($dataruangRawat as $key => $dr) {
        $ruangRawat = $dr->id_ruangrawat;
        $kelasRawat = $dr->id_mst_kelas_layanan;
      }
      $upgradeKelas = '0';
      $kelasNaik = '0';
      $cekKelas = [];
      $lamaNaik_kelas = '0';
      $total = 0;
      $dataTempatTidur = json_decode($this->Pendaftaran->getTempatTidur($dataSep->id_trx_pendaftaran));
      if (count($dataTempatTidur) > 0) {
        $kelasNaik = $dataTempatTidur[0]->kd_mst_kelas_layanan;
        foreach ($dataTempatTidur as $key => $value) {
          $cekKelas[] = $value->kd_mst_kelas_layanan;
          $total += $value->nilai_total;
          if ($value->kd_mst_kelas_layanan > 3) {
            if ($kelasNaik < $value->kd_mst_kelas_layanan ) {
              $kelasNaik = $value->kd_mst_kelas_layanan;
              $upgradeKelas ='1';
              $start_date = new DateTime($value->tgl_checkin);
              $end_date = new DateTime($value->tgl_checkout);
              $interval = $start_date->diff($end_date);
              $lamaNaik_kelas = $interval->days;
            }
          }else if($value->kd_mst_kelas_layanan < 3){
            if ($kelasNaik > $value->kd_mst_kelas_layanan) {
              $kelasNaik = $value->kd_mst_kelas_layanan;
              $upgradeKelas ='1';
              $start_date = new DateTime($value->tgl_checkin);
              $end_date = new DateTime($value->tgl_checkout);
              $interval = $start_date->diff($end_date);
              $lamaNaik_kelas = $interval->days;
            }
          }else{
            $upgradeKelas = '0';
          }
        }
      }
      if ($lamaNaik_kelas == '0' && $upgradeKelas == '1') {
            $lamaNaik_kelas = '1';
      }
      $kodePoli = "null";
      $cara_pulang = '5';
      $this->Pendaftaran->whereFields['a.id_trx_pendaftaran'] = $dataSep->id_trx_pendaftaran;
      $DataPendaftaran = $this->Pendaftaran->read();
      foreach ($DataPendaftaran as $key => $DataPendaftaran) {
        $kodePoli = $DataPendaftaran->kd_poli_bpjs;
        $cara_pulang = $DataPendaftaran->flag_cara_pulang;
      }
      $totalBiaya = $this->pendapatan->getTotalTagihan($dataSep->id_trx_pendaftaran);
      $dataLpk['request']['t_lpk']['noSep'] = $post['sep'];
      $dataLpk['request']['t_lpk']['tglMasuk'] = date("Y-m-d", strtotime($dataSep->tglSep));
      $dataLpk['request']['t_lpk']['tglKeluar'] = date("Y-m-d", strtotime($dataSep->tglSep));
      $dataLpk['request']['t_lpk']['jaminan'] = "1";
      $dataLpk['request']['t_lpk']['poli']['poli'] = ($dataSep->jnsPelayanan == 2) ? $kodePoli : '';
      $dataLpk['request']['t_lpk']['perawatan']['ruangRawat'] = ($dataSep->jnsPelayanan == 1) ? $ruangRawat : "";
      $dataLpk['request']['t_lpk']['perawatan']['kelasRawat'] = ($dataSep->jnsPelayanan == 1) ? $kelasRawat : "";
      $dataLpk['request']['t_lpk']['perawatan']['spesialistik'] = ($dataSep->jnsPelayanan == 1) ? '11' : "";
      $dataLpk['request']['t_lpk']['perawatan']['caraKeluar'] = ($dataSep->jnsPelayanan == 2) ? "" : $dataSep->flag_cara_pulang;
      $dataLpk['request']['t_lpk']['perawatan']['kondisiPulang'] = ($dataSep->jnsPelayanan == 2) ? "" : $dataSep->flag_cara_pulang;
      $dataLpk['request']['t_lpk']['diagnosa'] = $diagnosa;
      $dataLpk['request']['t_lpk']['procedure'] = $tindakan;
      $dataLpk['request']['t_lpk']['rencanaTL']['tindakLanjut'] = $dataSep->flag_cara_pulang;
      $dataLpk['request']['t_lpk']['rencanaTL']['dirujukKe']['kodePPK'] = "";
      $dataLpk['request']['t_lpk']['rencanaTL']['kontrolKembali']['tglKontrol'] = "";
      $dataLpk['request']['t_lpk']['rencanaTL']['kontrolKembali']['poli'] = "";
      $dataLpk['request']['t_lpk']['DPJP'] = ($dataSep->dpjpbaru == "") ? $dataSep->idbpjslama : $dataSep->idbpjsbaru;
      $dataLpk['request']['t_lpk']['user'] = $peg->nm_mst_pegawai_lengkap;
      $cat = "/LPK/insert";
      $req = json_encode($dataLpk);
      $dSep['flag_klaim'] = 1;
      $dSep['noSep'] = $post['sep'];
      $dklaim['noSep'] = $post['sep'];
      $dklaim['tgl_klaim'] = date('Y-m-d');
      $dklaim['id_mst_pegawai'] = $this->auth->get_user('id_mst_pegawai');
      $hasil = $this->vclaim->post_vclaim($req,$cat);
      $log['log'] = json_encode($hasil);
      $log['keterangan'] = 'vklaim';
      $this->trx_eklaim->insertlog($log);
      $dataEklaim['metadata']['method'] = "set_claim_data";
      $dataEklaim['metadata']['nomor_sep'] = $post['sep'];
      $dataEklaim['data']['nomor_sep'] = $post['sep'];
      $dataEklaim['data']['nomor_kartu'] = $dataSep->noKartu;
      $dataEklaim['data']['tgl_masuk'] = date("Y-m-d H:i:s", strtotime($dataSep->tglSep));
      $dataEklaim['data']['tgl_pulang'] = date("Y-m-d H:i:s", strtotime($post['tgl_pulang']));
      $dataEklaim['data']['jenis_rawat'] = $dataSep->jnsPelayanan;
      $dataEklaim['data']['kelas_rawat'] = $dataSep->klsRawat;
      $dataEklaim['data']['adl_sub_acute'] = "45";
      $dataEklaim['data']['adl_chronic'] = "45";
      $dataEklaim['data']['icu_indikator'] = "0";
      $dataEklaim['data']['icu_los'] = "0";
      $dataEklaim['data']['ventilator_hour'] = "0";
      $dataEklaim['data']['upgrade_class_ind'] = $upgradeKelas;
      $dataEklaim['data']['upgrade_class_class'] = $kelasNaik;
      $dataEklaim['data']['upgrade_class_los'] = $lamaNaik_kelas;
      $dataEklaim['data']['add_payment_pct'] = "0";
      $dataEklaim['data']['birth_weight'] = "0";
      $dataEklaim['data']['discharge_status'] = $cara_pulang;
      $dataEklaim['data']['diagnosa'] = implode("#",$diagnosaEklaim);
      $dataEklaim['data']['procedure'] = implode("#",$tindakanEklaim);
      $dataEklaim['data']['tarif_rs']['prosedur_non_bedah'] = $totalBiaya + $total;
      $dataEklaim['data']['tarif_rs']['prosedur_bedah'] = "";
      $dataEklaim['data']['tarif_rs']['konsultasi'] = "";
      $dataEklaim['data']['tarif_rs']['tenaga_ahli'] = "";
      $dataEklaim['data']['tarif_rs']['keperawatan'] = "";
      $dataEklaim['data']['tarif_rs']['penunjang'] = "";
      $dataEklaim['data']['tarif_rs']['radiologi'] = "";
      $dataEklaim['data']['tarif_rs']['laboratorium'] = "";
      $dataEklaim['data']['tarif_rs']['pelayanan_darah'] = "";
      $dataEklaim['data']['tarif_rs']['rehabilitasi'] = "";
      $dataEklaim['data']['tarif_rs']['kamar'] = "";
      $dataEklaim['data']['tarif_rs']['rawat_intensif'] = "";
      $dataEklaim['data']['tarif_rs']['obat'] = "";
      $dataEklaim['data']['tarif_rs']['alkes'] = "";
      $dataEklaim['data']['tarif_rs']['bmhp'] = "";
      $dataEklaim['data']['tarif_rs']['sewa_alat'] = "";
      $dataEklaim['data']['tarif_poli_eks'] = "0";
      $dataEklaim['data']['nama_dokter'] = ($dataSep->dpjpbaru == "") ? $dataSep->dpjplama : $dataSep->dpjpbaru ;;
      $dataEklaim['data']['kode_tarif'] = "CP";
      $dataEklaim['data']['payor_id'] = "3";
      $dataEklaim['data']['payor_cd'] = "JKN";
      $dataEklaim['data']['cob_cd'] = "0001";
      $dataEklaim['data']['coder_nik'] = "123123123123";
      $hasilEklaim = $this->eklaim->claim(json_encode($dataEklaim));
      $hasil['eklaim'] = json_decode($hasilEklaim);
      $log['log'] = json_encode($hasil['eklaim']);
      $log['keterangan'] = 'eklaim';
      $this->trx_eklaim->insertlog($log);
      if ($hasil['eklaim']->metadata->code == '200') {
        $grouper1['metadata']['method'] = 'grouper';
        $grouper1['metadata']['stage'] = '1';
        $grouper1['data']['nomor_sep'] = $post['sep'];
        $hasilGrouper1 = json_decode($this->eklaim->claim(json_encode($grouper1)));
        $log['log'] = json_encode($hasilGrouper1);
        $log['keterangan'] = 'grouper1';
        $this->trx_eklaim->insertlog($log);
        if ($hasilGrouper1->metadata->code == '200') {
          if (isset($hasilGrouper1->special_cmg_option)) {
            $cmg = '#';
            $tmpCmg = [];
             foreach ($hasilGrouper1->special_cmg_option as $key => $sco) {
                $tmpCmg[] = $sco->code;
              }
            if (count($tmpCmg) > 0) {
              $cmg = implode('#', $tmpCmg);
            }
            $grouper2['metadata']['method'] = 'grouper';
            $grouper2['metadata']['stage'] = '2';
            $grouper2['data']['nomor_sep'] = $post['sep'];
            $grouper2['data']['special_cmg'] = $cmg;
            $hasilGrouper2 = json_decode($this->eklaim->claim(json_encode($grouper2)));
            $log['log'] = json_encode($hasilGrouper2);
            $log['keterangan'] = 'grouper2';
            $this->trx_eklaim->insertlog($log);
            if ($hasilGrouper2->metadata->code == '200') {
              $flaggrouper['flag_grouper'] =  '1';
              $flaggrouper['noSep'] =  $post['sep'];
              $dataSave['id_trx_pendaftaran'] = $dataSep->id_trx_pendaftaran;
              $dataSave['no_sep'] = $post['sep'];
              $dataSave['tgl_klaim'] = date('Y-m-d');
              $dataSave['user_klaim'] = $peg->nm_mst_pegawai_lengkap;
              $dataSave['tagihan'] = $totalBiaya + $total;
              $dataSave['code'] = $hasilGrouper2->response->cbg->code;
              $dataSave['description'] = $hasilGrouper2->response->cbg->description;
              $dataSave['tarif'] = $hasilGrouper2->response->cbg->tariff;
              $dataSave['kelas'] = $hasilGrouper2->response->kelas;
              $dataSave['add_payment_amt'] = $hasilGrouper2->response->add_payment_amt;
              $dataSave['inacbg_version'] = $hasilGrouper2->response->inacbg_version;
              $this->db->trans_start();
              $this->trx_eklaim->create($dataSave);
              $this->SEP->replace($flaggrouper);
              $this->db->trans_complete();
              if ($this->db->trans_status() === false) {
                $log['log'] = 'Gagal Simpan Data Eklaim';
                $this->trx_eklaim->insertlog($log);
              }else{
                $log['log'] = 'Berhasil Simpan Data Eklaim';
                $this->trx_eklaim->insertlog($log);
                $kirim['metadata']['method'] = 'send_claim_individual';
                $kirim['data']['nomor_sep'] = $post['sep'];
                $hasilKirim = json_decode($this->eklaim->claim(json_encode($kirim)));
                $log['log'] = json_encode($hasilGrouper2);
                $log['keterangan'] = 'grouper2';
                $this->trx_eklaim->insertlog($log);
                if ($hasilKirim->metadata->code == '200') {
                  $flagfinal['flag_final'] =  '1';
                  $flagfinal['noSep'] =  $post['sep'];
                  $this->SEP->replace($flagfinal);
                }
              }
              foreach ($hasilGrouper2->response->special_cmg as $key => $specialCmg) {
                $data['special_cmg']['id_trx_pendaftaran'] = $dataSep->id_trx_pendaftaran;
                $data['special_cmg']['no_sep'] = $post['sep'];
                $data['special_cmg']['tgl_klaim'] = date('Y-m-d');
                $data['special_cmg']['user_klaim'] = $peg->nm_mst_pegawai_lengkap;
                $data['special_cmg']['tagihan'] = $totalBiaya + $total;
                $data['special_cmg']['code'] = $hasilGrouper1->response->special_cmg->code;
                $data['special_cmg']['description'] = $hasilGrouper1->response->special_cmg->description;
                $data['special_cmg']['tarif'] = $hasilGrouper1->response->special_cmg->tariff;
                $data['special_cmg']['kelas'] = $hasilGrouper1->response->kelas;
                $data['special_cmg']['add_payment_amt'] = $hasilGrouper1->response->add_payment_amt;
                $data['special_cmg']['inacbg_version'] = $hasilGrouper1->response->inacbg_version;
                $data['special_cmg']['keterangan'] = 'Sub Acute';
                $this->trx_eklaim->create($data['special_cmg']);
              }
            }
          }else{
            $flaggrouper['flag_grouper'] =  '1';
            $flaggrouper['noSep'] =  $post['sep'];
            $dataSave['id_trx_pendaftaran'] = $dataSep->id_trx_pendaftaran;
            $dataSave['no_sep'] = $post['sep'];
            $dataSave['tgl_klaim'] = date('Y-m-d');
            $dataSave['user_klaim'] = $peg->nm_mst_pegawai_lengkap;
            $dataSave['tagihan'] = $totalBiaya + $total;
            $dataSave['code'] = $hasilGrouper1->response->cbg->code;
            $dataSave['description'] = $hasilGrouper1->response->cbg->description;
            $dataSave['tarif'] = $hasilGrouper1->response->cbg->tariff;
            $dataSave['kelas'] = $hasilGrouper1->response->kelas;
            $dataSave['add_payment_amt'] = $hasilGrouper1->response->add_payment_amt;
            $dataSave['inacbg_version'] = $hasilGrouper1->response->inacbg_version;
            $dataSave['keterangan'] = 'INA-CBG';
            $this->db->trans_start();
            $this->trx_eklaim->create($dataSave);
            $this->SEP->replace($flaggrouper);
            $this->db->trans_complete();
            if ($this->db->trans_status() === false) {
              $log['log'] = 'Gagal Simpan Data Eklaim';
              $this->trx_eklaim->insertlog($log);
            }else{
              $log['log'] = 'Berhasil Simpan Data Eklaim';
              $this->trx_eklaim->insertlog($log);
              $kirim['metadata']['method'] = 'send_claim_individual';
              $kirim['data']['nomor_sep'] = $post['sep'];
              $hasilKirim = json_decode($this->eklaim->claim(json_encode($kirim)));
              $log['log'] = json_encode($hasilGrouper2);
              $log['keterangan'] = 'grouper2';
              $this->trx_eklaim->insertlog($log);
              if ($hasilKirim->metadata->code == '200') {
                $flagfinal['flag_final'] =  '1';
                $flagfinal['noSep'] =  $post['sep'];
                $this->SEP->replace($flagfinal);
              }
            }
          }
          if (isset($hasilGrouper1->sub_acute)) {
            $data['sub_acute']['id_trx_pendaftaran'] = $dataSep->id_trx_pendaftaran;
            $data['sub_acute']['no_sep'] = $post['sep'];
            $data['sub_acute']['tgl_klaim'] = date('Y-m-d');
            $data['sub_acute']['user_klaim'] = $peg->nm_mst_pegawai_lengkap;
            $data['sub_acute']['tagihan'] = $totalBiaya + $total;
            $data['sub_acute']['code'] = $hasilGrouper1->response->sub_acute->code;
            $data['sub_acute']['description'] = $hasilGrouper1->response->sub_acute->description;
            $data['sub_acute']['tarif'] = $hasilGrouper1->response->sub_acute->tariff;
            $data['sub_acute']['kelas'] = $hasilGrouper1->response->kelas;
            $data['sub_acute']['add_payment_amt'] = $hasilGrouper1->response->add_payment_amt;
            $data['sub_acute']['inacbg_version'] = $hasilGrouper1->response->inacbg_version;
            $data['sub_acute']['keterangan'] = 'Sub Acute';
            $this->trx_eklaim->create($data['sub_acute']);
          }
          if (isset($hasilGrouper1->chronic)) {
            $data['chronic']['id_trx_pendaftaran'] = $dataSep->id_trx_pendaftaran;
            $data['chronic']['no_sep'] = $post['sep'];
            $data['chronic']['tgl_klaim'] = date('Y-m-d');
            $data['chronic']['user_klaim'] = $peg->nm_mst_pegawai_lengkap;
            $data['chronic']['tagihan'] = $totalBiaya + $total;
            $data['chronic']['code'] = $hasilGrouper1->response->chronic->code;
            $data['chronic']['description'] = $hasilGrouper1->response->chronic->description;
            $data['chronic']['tarif'] = $hasilGrouper1->response->chronic->tariff;
            $data['chronic']['kelas'] = $hasilGrouper1->response->kelas;
            $data['chronic']['add_payment_amt'] = $hasilGrouper1->response->add_payment_amt;
            $data['chronic']['inacbg_version'] = $hasilGrouper1->response->inacbg_version;
            $data['chronic']['keterangan'] = 'chronic';
            $this->trx_eklaim->create($data['chronic']);
          }
          $finish['metadata']['method'] = 'claim_final';
          $finish['data']['nomor_sep'] = $post['sep'];
          $finish['data']['coder_nik'] = '123123123123';
          $hasilfinish = json_decode($this->eklaim->claim(json_encode($finish)));
          $log['log'] = json_encode($hasilfinish);
          $log['keterangan'] = 'Final Eklaim';
          $this->trx_eklaim->insertlog($log);
        }
      }
      if ($hasil['metaData']['code'] == '200' && $hasil['eklaim']->metadata->code == '200') {
        $this->db->trans_start();
        $this->SEP->replace($dSep);
        $this->klaim->create($dklaim);
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->setFlash('FAILED: ', 'Failed Save Data To Database ! Contact your IT Support.', 'alert-danger');
        } else {
          $this->setFlash('SUCCESS:','Sukses Save Data to Database ! ','alert-success');
        }
        $this->setFlash('SUCCESS:',print_r($hasil),'alert-success');
      }else{
        $this->setFlash('FAILED: ', print_r($hasil), 'alert-danger');
      }
    }
    $this->set('trx_detail_1',$trx_detail_1);
    $this->render('create_klaim');
  }

  public function cari_sep(){
    $this->SEP->whereFields['a.flag_active'] = 1;
    $this->SEP->whereFields['a.flag_klaim'] = 0;
    $hasil = $this->SEP->read();
    echo json_encode($hasil);
  }

  public function get_data_lpk($id_trx_pendaftaran){
    $hasil['sekunder'] = $this->SEP->get_sekunder($id_trx_pendaftaran);
    $hasil['tindakan'] = $this->SEP->get_procedure($id_trx_pendaftaran);
    echo json_encode($hasil);
  }

  public function detail_klaim($id_trx_klaim = 0){
    $this->klaim->whereFields['a.id_trx_klaim'] = $id_trx_klaim;
    $dataKlaim = $this->klaim->read()[0];
    $this->SEP->whereFields['a.noSep'] = $dataKlaim->noSep;
    $dataSep = $this->SEP->read()[0];
    $dataSekunder = $this->SEP->get_sekunder($dataSep->id_trx_pendaftaran);
    $dataTindakan = $this->SEP->get_procedure($dataSep->id_trx_pendaftaran);
    $diagnosa = [];
    if ($dataSep->kd_mst_icd10 != "") {
      $diagnosa[0]['kode'] = $dataSep->kd_mst_icd10;
      $diagnosa[0]['level'] = 1;
      $diagnosa[0]['nama'] = $dataSep->nm_icd_icd10;
    }
    foreach ($dataSekunder as $key => $ds) {
      $diagnosa[] = array('kode' => $ds->kd_mst_icd10, 'level' => 2, 'nama' =>  $ds->nm_icd_icd10);
    }
    $tindakan = [];
    foreach ($dataTindakan as $key => $dt) {
      $tindakan[] = array('kode' => $dt->kd_mst_icd9cm, 'nama' => $dt->nm_mst_icd9cm);
    }
    $this->trx_tempat_tidur->whereFields['a.id_trx_pendaftaran'] = $dataSep->id_trx_pendaftaran;
    $dataruangRawat = $this->trx_tempat_tidur->read();
    $dataKlaim->Sep = $dataSep;
    $dataKlaim->diagnosa = $diagnosa;
    $dataKlaim->tindakan = $tindakan;
    $dataKlaim->ruangRawat = $dataruangRawat;
    $this->set('dataKlaim',json_encode($dataKlaim));
    $this->render('detail_klaim');
  }

  public function delete_klaim($sep = 0, $id_trx_klaim){
    $cat = '/LPK/delete';
    $data['request']['t_lpk']['noSep'] = $sep;
    $hasil = $this->vclaim->delete_vclaim(json_encode($data),$cat);
    $dklaim['flag_aktif'] = '0';
    $dklaim['id_trx_klaim'] = $id_trx_klaim;
    if ($hasil['metaData']['code'] == "200") {
      $this->klaim->replace($dklaim);
      $this->setFlash('Suksess: ', 'Suksess  '.$hasil['metaData']['message'].'!.', 'alert-success');
      redirect('bridging/bpjs/lpk','refresh');
    }else{
      $this->setFlash('FAILED: ', 'FAILED  '.$hasil['metaData']['message'].'!.', 'alert-danger');
      redirect('bridging/bpjs/lpk','refresh');
    }
  }

  public function monitoring_kunjungan(){
    if (count($this->input->post()) > 0) {
      $post = $this->input->post();
      $cat = "/Monitoring/Kunjungan/Tanggal/".$post['tgl']."/JnsPelayanan/".$post['jenis_layanan'];
      $hasil = $this->vclaim->get_vclaim($cat);
      if ($hasil['metaData']['code'] == "200") {
        $this->setFlash('Information: ', $hasil['metaData']['message'].'!.', 'alert-success');
      }else{
        $this->setFlash('Information: ', $hasil['metaData']['message'].'!.', 'alert-danger');
      }
      $this->set('hasil',$hasil);
    }
    $this->render('monitoring_kunjungan');
  }

  public function monitoring_klaim(){
    $data = $this->input->post();
    if (count($data) > 0) {
      $cat = "/Monitoring/Klaim/Tanggal/".$data['tgl']."/JnsPelayanan/".$data['jenis_layanan']."/Status/".$data['status'];
      $hasil = $this->vclaim->get_vclaim($cat);
      if ($hasil['metaData']['code'] == "200") {
        $this->setFlash('Information: ', $hasil['metaData']['message'].'!.', 'alert-success');
      }else{
        $this->setFlash('Information: ', $hasil['metaData']['message'].'!.', 'alert-danger');
      }
      $this->set('hasil',$hasil);
    }
    $this->render('monitoring_klaim');
  }

  function udpdatePelunasan($id){
    $idPelunasan['id_trx_pelunasan'] = $id;
    $hasil = $this->Rincian->SelectAll('5544','5544');
    $this->db->trans_start();
         $this->Rincian->UpdateAll($hasil,$idPelunasan);
    $this->db->trans_complete();
    if ($this->db->trans_status() === false) {
       echo 'gagal';
    }else{
      echo 'sukses';
    }
  }

  public function getDiagnosa(){
    $post = $this->input->post();
    $diag = explode('#',$post['diagnosa']);
    $data['code'] = '300';
    if (count($diag) > 0) {
      $data['primer'] = $this->Diagnosa->getDiagnosa($diag[0]);
      $i = 1;
      while ($i < count($diag)) {
        $data['sekunder'][] = $this->Diagnosa->getDiagnosa($diag[$i]);
        $i++;
      }
      $data['code'] = '200';
    }
    echo json_encode($data);
  }

  public function getProcedure(){
    $post = $this->input->post();
    $diag = explode('#',$post['procedure']);
    $data['code'] = '300';
    if (count($diag) > 0) {
      $i = 0;
      while ($i < count($diag)) {
        $data['procedure'][] = $this->Tindakan->getTindakan($diag[$i]);
        $i++;
      }
      $data['code'] = '200';
    }
    echo json_encode($data);
  }

  public function grouper1($sep){
    $grouper1['metadata']['method'] = 'grouper';
    $grouper1['metadata']['stage'] = '1';
    $grouper1['data']['nomor_sep'] = $sep;
    $hasilGrouper1 = $this->eklaim->claim(json_encode($grouper1));
    print_r(json_decode($hasilGrouper1));
  }

  public function rujukRanap($id_pendaftaran = 0){
    $this->title = "Rujuk Rawat Inap";
    $id_trx_pendaftaran = $id_pendaftaran;
    $Sep = 'SEP';
    $trx_head_1 = 'Pendaftaran';
    $trx_detail_1 = 'Tempat_tidur';
    $trx_detail_2 = 'Mst_tempat_tidur';
    $trx_detail_3 = 'Cara_bayar';
    $this->load->model('sharing/Tarif_model','Tarif');
    $this->load->model('billing/Rincian_administrasi_model','administrasi');
    $this->set('SEP', $Sep);
    $this->set('trx_head_1', $trx_head_1);
    $this->set('trx_detail_1', $trx_detail_1);
    $this->set('trx_detail_2', $trx_detail_2);
    $this->set('trx_detail_3', $trx_detail_3);
    $row['k'] = $this->Mst_kamar_jenis->read();
    $this->set('kamar_jenis', $row);
    $this->set('dokter',$this->$trx_head_1->get_dokter());
    $this->set('id_trx_pendaftaran',$id_trx_pendaftaran);
    
    if (count($this->input->post()) > 0) {
      $data = $this->input->post();

      $this->Tempat_tidur->whereFields['a.id_trx_pendaftaran'] = $id_trx_pendaftaran;
      $unit = $this->Tempat_tidur->read();

      if($unit != null){
        $this->setFlash('FAILED: ', 'Pasien Sudah Di Rujuk Hari Ini', 'alert-warning');
        redirect($this->directory . strtolower($this->name) . '/rujukRanap/' . $id_trx_pendaftaran);
      }else{
      $this->Pendaftaranrjigd->whereFields['a.' . 'id_trx_pendaftaran'] = $id_trx_pendaftaran;
      $this->Pendaftaranrjigd->get_id_rincian();
      $DataPendaftaranRJIGD = $this->Pendaftaranrjigd->read()[0];
      
      $this->Master_kamar->whereFields['a.' . 'id_mst_kamar'] = $data['Kamar']['id_mst_kamar'];
      $Data_Kamar = $this->Master_kamar->read()[0];

      $this->Tarif->whereFields['a.' . 'id_mst_tarif'] = $Data_Kamar->id_mst_tarif;
      $dtTarifAkomodasi=$this->Tarif->read()[0];

      $data[$trx_head_1]['id_mst_kelas_layanan'] = $data[$trx_detail_3]['id_mst_kelas_layanan'];
      $data[$trx_head_1]['id_mst_unit_kerja_ref'] = '23';
      
      $trx_rincian_tempat_tidur = array(
        'id_trx_rincian' => $DataPendaftaranRJIGD->id_trx_rincian,
        'id_mst_tempat_tidur' => $data[$trx_detail_1]['id_mst_tempat_tidur'],
        'id_trx_pendaftaran' => $data[$trx_head_1]['id_trx_pendaftaran'],
        'id_mst_unit_kerja_jaspel' => '23',
        'id_mst_unit_kerja_sub_jaspel' => $Data_Kamar->id_mst_unit_kerja_sub,
        'tgl_checkin' => $data[$trx_detail_1]['tgl_mulai'],
        'id_mst_tarif' => $dtTarifAkomodasi->id_mst_tarif,
        'nm_mst_tarif' => $dtTarifAkomodasi->nm_mst_tarif,
        'harga' => $dtTarifAkomodasi->harga,
      );
      
      $master_tempat_tidur = array(
        'id_mst_tempat_tidur' => $data[$trx_detail_1]['id_mst_tempat_tidur'],
        'id_mst_kamar' => $data['Kamar']['id_mst_kamar'],
        'flag_isi' => 2,
      );

      $kelas_layanan = array(
        'id_trx_cara_bayar' => $DataPendaftaranRJIGD->id_trx_cara_bayar,
        'id_trx_pendaftaran' => $DataPendaftaranRJIGD->id_trx_pendaftaran,
        'id_mst_kelas_layanan' => $data['Cara_bayar']['id_mst_kelas_layanan']
      );
      
      // if(isset($data[$trx_detail_3])){
      //   $data[$trx_detail_3]['id_trx_cara_bayar'] = $DataPendaftaranRJIGD->id_trx_cara_bayar;
      //   if ($data[$trx_detail_3]['id_mst_kelas_layanan'] == '123' ){
      //     $this->Tarif->whereFields['kd_mst_tarif'] = 1000;
      //   }else{
      //     if($data[$trx_detail_3]['id_mst_kelas_layanan'] == 1){
      //       $this->Tarif->whereFields['kd_mst_tarif'] = 'Kelas1';
      //     }else if($data[$trx_detail_3]['id_mst_kelas_layanan'] == 2){
      //       $this->Tarif->whereFields['kd_mst_tarif'] = 'Kelas2';
      //     }else if($data[$trx_detail_3]['id_mst_kelas_layanan'] == 3){
      //       $this->Tarif->whereFields['kd_mst_tarif'] = 'Kelas3';
      //     }else if($data[$trx_detail_3]['id_mst_kelas_layanan'] == 4){
      //       $this->Tarif->whereFields['kd_mst_tarif'] = 'KelasUtama04';
      //     }else if($data[$trx_detail_3]['id_mst_kelas_layanan'] == 7){
      //       $this->Tarif->whereFields['kd_mst_tarif'] = 'ICU01';
      //     }
      //   }
      //   $dtTarif=$this->Tarif->read()[0];
      //   $trx_rincian_administrasi['id_trx_rincian']=$DataPendaftaranRJIGD->id_trx_rincian;
      //   $trx_rincian_administrasi['id_trx_pendaftaran']=$data[$trx_head_1]['id_trx_pendaftaran'];
      //   $trx_rincian_administrasi['id_mst_tarif']=$dtTarif->id_mst_tarif;
      //   $trx_rincian_administrasi['nm_mst_tarif']=$dtTarif->nm_mst_tarif;
      //   $trx_rincian_administrasi['ket_mst_tarif']=$dtTarif->ket_mst_tarif;
      //   $trx_rincian_administrasi['id_mst_unit_kerja_jaspel']= '23';
      //   $trx_rincian_administrasi['id_mst_unit_kerja_sub_jaspel']= $Data_Kamar->id_mst_unit_kerja_sub;
      //   $trx_rincian_administrasi['harga']=$dtTarif->harga;
      //   $trx_rincian_administrasi['kuantitas']='1';
      //   $trx_rincian_administrasi['nilai']=$trx_rincian_administrasi['kuantitas']*$trx_rincian_administrasi['harga'];
      //   $trx_rincian_administrasi['persen_diskon']='0';
      //   $trx_rincian_administrasi['nilai_diskon']='0';
      //   $trx_rincian_administrasi['nilai_total']=$trx_rincian_administrasi['harga']*$trx_rincian_administrasi['kuantitas']-$trx_rincian_administrasi['nilai_diskon'];
      //   $trx_rincian_administrasi['flag_active']='1';
      // }

      // echo "<pre>";
      // var_dump($data[$trx_head_1]);
      // var_dump($data[$trx_detail_1]);
      // var_dump($data[$trx_detail_3]);
      // var_dump($trx_rincian_tempat_tidur);
      // var_dump($master_tempat_tidur);
      // var_dump($trx_rincian_administrasi);
      // echo "</pre>";


      $this->db->trans_start();
      
      $this->$trx_head_1->replace($data[$trx_head_1]);
      $this->$trx_detail_1->replace($data[$trx_detail_1]);
      $this->$trx_detail_3->replace($kelas_layanan);
      $this->Ri_tempat_tidur->create($trx_rincian_tempat_tidur);
      $this->$trx_detail_2->replace($master_tempat_tidur);
      // $this->administrasi->create($trx_rincian_administrasi);
      }
      $this->db->trans_complete();

      if ($this->db->trans_status() === false) {
        $this->setFlash('FAILED: ', 'Rujukan Gagal!', 'alert-danger');
        redirect($this->directory . strtolower($this->name) . '/rujukRanap/' . $data['id_trx_pendaftaran']);
      }else{
        $this->setFlash('Success: ', 'Pasien Berhasil di Rujuk', 'alert-success');
        if (isset($data['SEP']['noRujukan'])) {
          redirect("bridging/Bpjs/Sep/Redirect/".$id_trx_pendaftaran,"refresh");
        }else{
          redirect("medis/Pendaftaran");
        }
      }
    }else{
      $this->SEP->whereFields['c.' . 'id_trx_pendaftaran'] = $id_trx_pendaftaran;
      @$DataPendaftaran = $this->SEP->read()[0];
      $this->set('DataPendaftaran', @$DataPendaftaran);
      $this->render('rujukranap');
    }
  }

  public function cetakKlaim($sep){
    $getData['metadata']['method'] = 'get_claim_data';
    $getData['data']['nomor_sep'] = $sep;
    $data['data'] = json_decode($this->eklaim->claim(json_encode($getData)));
    if ($data['data']->metadata->code == '200') {
      $response['response'] = $data['data']->response->data;
      $this->load->view('bridging/Bpjs/cetakKlaim',$response);
    }else{
      $this->setFlash('FAILED: ', 'Bridging Data gagal', 'alert-danger');
      redirect('bridging/Bpjs/list_index','refresh');
    }
  }

  public function GrouperUlang($sep){
    $post['sep'] = $sep;
    $grouper1['metadata']['method'] = 'grouper';
    $grouper1['metadata']['stage'] = '1';
    $grouper1['data']['nomor_sep'] = $post['sep'];
    $hasilGrouper1 = json_decode($this->eklaim->claim(json_encode($grouper1)));
    $log['log'] = json_encode($hasilGrouper1);
    $log['keterangan'] = 'grouper1';
    $this->trx_eklaim->insertlog($log);
    if ($hasilGrouper1->metadata->code == '200') {
      if (isset($hasilGrouper1->special_cmg_option)) {
        $cmg = '#';
        $tmpCmg = [];
        foreach ($hasilGrouper1->special_cmg_option as $key => $sco) {
          $tmpCmg[] = $sco->code;
        }
        if (count($tmpCmg) > 0) {
          $cmg = implode('#', $tmpCmg);
        }
        $grouper2['metadata']['method'] = 'grouper';
        $grouper2['metadata']['stage'] = '2';
        $grouper2['data']['nomor_sep'] = $post['sep'];
        $grouper2['data']['special_cmg'] = $cmg;
        $hasilGrouper2 = json_decode($this->eklaim->claim(json_encode($grouper2)));
        $log['log'] = json_encode($hasilGrouper2);
        $log['keterangan'] = 'grouper2';
        $this->trx_eklaim->insertlog($log);
        if ($hasilGrouper2->metadata->code == '200') {
          $flaggrouper['flag_grouper'] =  '1';
          $flaggrouper['noSep'] =  $post['sep'];
          $dataSave['id_trx_pendaftaran'] = $dataSep->id_trx_pendaftaran;
          $dataSave['no_sep'] = $post['sep'];
          $dataSave['tgl_klaim'] = date('Y-m-d');
          $dataSave['user_klaim'] = $peg->nm_mst_pegawai_lengkap;
          $dataSave['tagihan'] = $totalBiaya + $total;
          $dataSave['code'] = $hasilGrouper2->response->cbg->code;
          $dataSave['description'] = $hasilGrouper2->response->cbg->description;
          $dataSave['tarif'] = $hasilGrouper2->response->cbg->tariff;
          $dataSave['kelas'] = $hasilGrouper2->response->kelas;
          $dataSave['add_payment_amt'] = $hasilGrouper2->response->add_payment_amt;
          $dataSave['inacbg_version'] = $hasilGrouper2->response->inacbg_version;
          $this->db->trans_start();
          $this->trx_eklaim->create($dataSave);
          $this->SEP->replace($flaggrouper);
          $this->db->trans_complete();
          if ($this->db->trans_status() === false) {
            $log['log'] = 'Gagal Simpan Data Eklaim';
            $this->trx_eklaim->insertlog($log);
          }else{
            $log['log'] = 'Berhasil Simpan Data Eklaim';
            $this->trx_eklaim->insertlog($log);
            $kirim['metadata']['method'] = 'send_claim_individual';
            $kirim['data']['nomor_sep'] = $post['sep'];
            $hasilKirim = json_decode($this->eklaim->claim(json_encode($kirim)));
            $log['log'] = json_encode($hasilGrouper2);
            $log['keterangan'] = 'grouper2';
            $this->trx_eklaim->insertlog($log);
            if ($hasilKirim->metadata->code == '200') {
              $flagfinal['flag_final'] =  '1';
              $flagfinal['noSep'] =  $post['sep'];
              $this->SEP->replace($flagfinal);
            }
          }

          foreach ($hasilGrouper2->response->special_cmg as $key => $specialCmg) {
            $data['special_cmg']['id_trx_pendaftaran'] = $dataSep->id_trx_pendaftaran;
            $data['special_cmg']['no_sep'] = $post['sep'];
            $data['special_cmg']['tgl_klaim'] = date('Y-m-d');
            $data['special_cmg']['user_klaim'] = $peg->nm_mst_pegawai_lengkap;
            $data['special_cmg']['tagihan'] = $totalBiaya + $total;
            $data['special_cmg']['code'] = $hasilGrouper1->response->special_cmg->code;
            $data['special_cmg']['description'] = $hasilGrouper1->response->special_cmg->description;
            $data['special_cmg']['tarif'] = $hasilGrouper1->response->special_cmg->tariff;
            $data['special_cmg']['kelas'] = $hasilGrouper1->response->kelas;
            $data['special_cmg']['add_payment_amt'] = $hasilGrouper1->response->add_payment_amt;
            $data['special_cmg']['inacbg_version'] = $hasilGrouper1->response->inacbg_version;
            $data['special_cmg']['keterangan'] = 'Sub Acute';
            $this->trx_eklaim->create($data['special_cmg']);
          }
        }
      }else{
        $flaggrouper['flag_grouper'] =  '1';
        $flaggrouper['noSep'] =  $post['sep'];
        $dataSave['id_trx_pendaftaran'] = $dataSep->id_trx_pendaftaran;
        $dataSave['no_sep'] = $post['sep'];
        $dataSave['tgl_klaim'] = date('Y-m-d');
        $dataSave['user_klaim'] = $peg->nm_mst_pegawai_lengkap;
        $dataSave['tagihan'] = $totalBiaya + $total;
        $dataSave['code'] = $hasilGrouper1->response->cbg->code;
        $dataSave['description'] = $hasilGrouper1->response->cbg->description;
        $dataSave['tarif'] = $hasilGrouper1->response->cbg->tariff;
        $dataSave['kelas'] = $hasilGrouper1->response->kelas;
        $dataSave['add_payment_amt'] = $hasilGrouper1->response->add_payment_amt;
        $dataSave['inacbg_version'] = $hasilGrouper1->response->inacbg_version;

        $this->db->trans_start();
        $this->trx_eklaim->create($dataSave);
        $this->SEP->replace($flaggrouper);
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
          $log['log'] = 'Gagal Simpan Data Eklaim';
          $this->trx_eklaim->insertlog($log);
        }else{
          $log['log'] = 'Berhasil Simpan Data Eklaim';
          $this->trx_eklaim->insertlog($log);
          $kirim['metadata']['method'] = 'send_claim_individual';
          $kirim['data']['nomor_sep'] = $post['sep'];
          $hasilKirim = json_decode($this->eklaim->claim(json_encode($kirim)));
          $log['log'] = json_encode($hasilGrouper2);
          $log['keterangan'] = 'grouper2';
          $this->trx_eklaim->insertlog($log);
          if ($hasilKirim->metadata->code == '200') {
            $flagfinal['flag_final'] =  '1';
            $flagfinal['noSep'] =  $post['sep'];
            $this->SEP->replace($flagfinal);
          }
        }
      }

      if (isset($hasilGrouper1->sub_acute)) {
        $data['sub_acute']['id_trx_pendaftaran'] = $dataSep->id_trx_pendaftaran;
        $data['sub_acute']['no_sep'] = $post['sep'];
        $data['sub_acute']['tgl_klaim'] = date('Y-m-d');
        $data['sub_acute']['user_klaim'] = $peg->nm_mst_pegawai_lengkap;
        $data['sub_acute']['tagihan'] = $totalBiaya + $total;
        $data['sub_acute']['code'] = $hasilGrouper1->response->sub_acute->code;
        $data['sub_acute']['description'] = $hasilGrouper1->response->sub_acute->description;
        $data['sub_acute']['tarif'] = $hasilGrouper1->response->sub_acute->tariff;
        $data['sub_acute']['kelas'] = $hasilGrouper1->response->kelas;
        $data['sub_acute']['add_payment_amt'] = $hasilGrouper1->response->add_payment_amt;
        $data['sub_acute']['inacbg_version'] = $hasilGrouper1->response->inacbg_version;
        $data['sub_acute']['keterangan'] = 'Sub Acute';
        $this->trx_eklaim->create($data['sub_acute']);
      }
      if (isset($hasilGrouper1->chronic)) {
        $data['chronic']['id_trx_pendaftaran'] = $dataSep->id_trx_pendaftaran;
        $data['chronic']['no_sep'] = $post['sep'];
        $data['chronic']['tgl_klaim'] = date('Y-m-d');
        $data['chronic']['user_klaim'] = $peg->nm_mst_pegawai_lengkap;
        $data['chronic']['tagihan'] = $totalBiaya + $total;
        $data['chronic']['code'] = $hasilGrouper1->response->chronic->code;
        $data['chronic']['description'] = $hasilGrouper1->response->chronic->description;
        $data['chronic']['tarif'] = $hasilGrouper1->response->chronic->tariff;
        $data['chronic']['kelas'] = $hasilGrouper1->response->kelas;
        $data['chronic']['add_payment_amt'] = $hasilGrouper1->response->add_payment_amt;
        $data['chronic']['inacbg_version'] = $hasilGrouper1->response->inacbg_version;
        $data['chronic']['keterangan'] = 'chronic';
        $this->trx_eklaim->create($data['chronic']);
      }
      $finish['metadata']['method'] = 'claim_final';
      $finish['data']['nomor_sep'] = $post['sep'];
      $finish['data']['coder_nik'] = '123123123123';
      $hasilfinish = json_decode($this->eklaim->claim(json_encode($finish)));
      $log['log'] = json_encode($hasilfinish);
      $log['keterangan'] = 'Final Eklaim';
      $this->trx_eklaim->insertlog($log);
    }
    $this->setFlash('Information: ', 'Jika Tombol Grouper Masih Tampil, Berarti Grouping Gagal, Silahkan Contact IT Support Anda !', 'alert-info');
    redirect($this->directory."$this->name"."/list_index");
  }

  public function finalKlaim($sep){
    $this->trx_eklaim->insertlog($log);
    $kirim['metadata']['method'] = 'send_claim_individual';
    $kirim['data']['nomor_sep'] = $post['sep'];
    $hasilKirim = json_decode($this->eklaim->claim(json_encode($kirim)));
    $log['log'] = json_encode($hasilGrouper2);
    $log['keterangan'] = 'grouper2';
    $this->trx_eklaim->insertlog($log);
    if ($hasilKirim->metadata->code == '200') {
      $flagfinal['flag_final'] =  '1';
      $flagfinal['noSep'] =  $post['sep'];
      $this->SEP->replace($flagfinal);
    }else{
      $this->setFlash('Information: ', $hasilKirim->metadata->message.', Silahkan Contact IT Support Anda !', 'alert-info');
      redirect($this->directory."$this->name"."/list_index");
    }
  }
}