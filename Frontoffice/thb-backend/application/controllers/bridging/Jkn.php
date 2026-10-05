<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Jkn extends My_Controller {
  public function __construct() {
  parent::__construct();
    $this->load->model("bridging/Antrean_model", "Antrean");
  }
  public function Rujukan($a = 'view', $p = '0'){
    $data = $this->input->post();
    if ($a == 'Getby_no') {
      ///$p = '0000079979951';
      if (is_ajax()){
        $found_data = false;
        $pencarian_berdasarkan = $this->input->post('rujuk_berdasarkan');
        if ($pencarian_berdasarkan == 'noru'){
          $category_store = $category_store['noru'];
        } else if ($pencarian_berdasarkan == 'noka'){
          $category_store = $category_store['noka'];
        }
        $this->Antrean->whereFields['a.kodebooking'] = $p;
        $hasil = $this->Antrean->read();
        // while($count_find < 2 && $found_data == false){
        //   $cat = $category_store[$count_find];
        //   $hasil = $this->vclaim_sep->request($cat.$p, "GET");
        //   // echo $cat.$p;
        //   $d = json_decode($hasil, true);
        //   if(isset($d['metaData']['code'])){
        //     if($d['metaData']['code']=='200'){
        //       if(!empty($d['response']['rujukan']['peserta']['noKartu'])){
        //         $dataRujukan = array(['noKunjungan' => $d['response']['rujukan']['noKunjungan'], 'nmAsuransi' => $d['response']['rujukan']['peserta']['cob']['nmAsuransi'],'noAsuransi' => $d['response']['rujukan']['peserta']['cob']['noAsuransi'],'tglTATcob' => $d['response']['rujukan']['peserta']['cob']['tglTAT'],'tglTMTcob' => $d['response']['rujukan']['peserta']['cob']['tglTMT'],'keteranganhakKelas' => $d['response']['rujukan']['peserta']['hakKelas']['keterangan'],'kodehakKelas' => $d['response']['rujukan']['peserta']['hakKelas']['kode'],'dinsos' => $d['response']['rujukan']['peserta']['informasi']['dinsos'],'noSKTM' => $d['response']['rujukan']['peserta']['informasi']['noSKTM'],'prolanisPRB' => $d['response']['rujukan']['peserta']['informasi']['prolanisPRB'],'keteranganjenisPeserta' => $d['response']['rujukan']['peserta']['jenisPeserta']['keterangan'],'kodejenisPeserta' => $d['response']['rujukan']['peserta']['jenisPeserta']['kode'],'noMR' => $d['response']['rujukan']['peserta']['mr']['noMR'],'noTelepon' => $d['response']['rujukan']['peserta']['mr']['noTelepon'],'nama' => $d['response']['rujukan']['peserta']['nama'],'nik' => $d['response']['rujukan']['peserta']['nik'],'noKartu' => $d['response']['rujukan']['peserta']['noKartu'],'pisa' => $d['response']['rujukan']['peserta']['pisa'],'kdProvider' => $d['response']['rujukan']['peserta']['provUmum']['kdProvider'],'nmProvider' => $d['response']['rujukan']['peserta']['provUmum']['nmProvider'],'sex' => $d['response']['rujukan']['peserta']['sex'],'keteranganstatusPeserta' => $d['response']['rujukan']['peserta']['statusPeserta']['keterangan'],'kodestatusPeserta' => $d['response']['rujukan']['peserta']['statusPeserta']['kode'],'tglCetakKartu' => $d['response']['rujukan']['peserta']['tglCetakKartu'],'tglLahir' => $d['response']['rujukan']['peserta']['tglLahir'],'tglTAT' => $d['response']['rujukan']['peserta']['tglTAT'],'tglTMT' => $d['response']['rujukan']['peserta']['tglTMT'],'umurSaatPelayanan' => $d['response']['rujukan']['peserta']['umur']['umurSaatPelayanan'],'umurSekarang' => $d['response']['rujukan']['peserta']['umur']['umurSekarang'],'kodediagnosa' => $d['response']['rujukan']['diagnosa']['kode'],'namadiagnosa' => $d['response']['rujukan']['diagnosa']['nama'],'kd_poli' =>  $d['response']['rujukan']['poliRujukan']['kode'],'nama_poli' =>  $d['response']['rujukan']['poliRujukan']['nama'],'tglKunjungan' => $d['response']['rujukan']['tglKunjungan'],'ppkRujukan' => $d['response']['rujukan']['provPerujuk']['kode'],'nmProviderPerujuk' => $d['response']['rujukan']['provPerujuk']['nama'],'code' => $d['metaData']['code'],'message' => $d['metaData']['message']]);
        //         if($count_find==0)
        //         {$dataRujukan[0]['AsalRujukanFaskes'] = '3';} //Faskes 1
        //         else if($count_find==1)
        //         {
        //           if ($value['provPerujuk']['kode'] == '1007R001') {
        //             $dataRujukan[0]['AsalRujukanFaskes'] = '4';
        //           }else{
        //             $dataRujukan[0]['AsalRujukanFaskes'] = '6';
        //           }
        //         } //Faskes 2
        //         $found_data=true;
        //       }
        //     }
        //   }
        //   $count_find++;
        // }
        // if($found_data==false){
        //   if(isset($d['metaData']['code'])){
        //     $dataRujukan = array(['code' => $d['metaData']['code'],'message' => $d['metaData']['message']]);
        //   }else{
        //     $dataRujukan = array(['code' => '500','message' => 'Tidak dapat terhubung dengan server']);
        //   }
        // }
      }
      echo json_encode($hasil);
      return;
    } 
  }
}