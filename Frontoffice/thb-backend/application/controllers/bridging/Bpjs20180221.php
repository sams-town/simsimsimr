<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bpjs extends My_Controller {
    
      public function __construct() {
        parent::__construct();
        $this->load->model("{$this->directory}/Sep_model", "SEP");
        $this->load->model("{$this->directory}/Pendaftaran_model", "Pendaftaran");
        $this->load->model("{$this->directory}/Pengajuan_model", "pengajuan");
        $this->load->model("sdm/Pegawai_model",'pegawai');
        $this->load->library('Vclaim','vclaim');
        $this->title = 'Bridging BPJS';
    }
    
    public function index() {
        $this->render('index');
    }

    public function CekFaskes($a = 'null', $p, $f = '0'){
        if ($a == 'json') {
            $printarr = false;
            $printarr = true;
            $f = true;
            if ($f == 3) {
              $faskes = '/1';
            }else{
              $faskes = '/2';
            }
            $extraparam = "";
            ///$p = '0000079979951';
            $cat       = "/referensi/faskes/";
            $data      = "6895"; //Ganti dengan consumerID dari BPJS
            $secretKey = "4aY0B49CF4"; //Ganti dengan consumerSecret dari BPJS
            $localIP   = "dvlp.bpjs-kesehatan.go.id";
            $port      = 8081; //port url
            $tgl       = date("Y-m-d");
            $url       = "http://".$localIP.":".$port."/VClaim-rest".$cat.$p.$faskes; //Lihat katalog, jangan sertakan port

            date_default_timezone_set('UTC');
            $tStamp = strval(time()-strtotime('1970-01-01 00:00:00'));
            $signature = hash_hmac('sha256', $data."&".$tStamp, $secretKey, true);
            $encodedSignature = base64_encode($signature);
            $urlencodedSignature = urlencode($encodedSignature);

            $opts = array(
             'http'=>array(
             'method'=>"GET",
             'header'=>"Host: api.asterix.co.id\r\n".
             "Connection: close\r\n".
             "X-timestamp: ".$tStamp."\r\n".
             "X-signature: ".$encodedSignature."\r\n".
             "User-Agent: Mozilla/5.0 (Windows NT 6.3; Win64; x64)\r\n".
             "X-cons-id: ".$data."\r\n".
             "Accept: application/json\r\n"
             )
            );

            $context = stream_context_create($opts);
            $url = $url.$extraparam;
            $result = file_get_contents($url, false, $context);

            if ($result === false) { 
                echo "Tidak dapat menyambung ke server"; 
            } else { 
                if($printarr == true){
                    if (is_ajax()):            
                        if ($a == 'json') {
                            // $resultarr = json_encode($result, true);
                            $d = json_decode($result, true);
                            // echo $result;
                            $dataPeserta = array(['kode' => $d['response']['faskes'][0]['kode'],
                                           'nama' => $d['response']['faskes'][0]['nama'],
                                           'code' => $d['metaData']['code'],
                                           'message' => $d['metaData']['message'],
                                        ]);
                                echo json_encode($dataPeserta);
                            return;
                        }
                    endif;
                }
            }
        }else{
            $this->render('PesertaCek');
        }

    }

    public function CekPeserta($a = 'null', $p = '0'){
        if ($a == 'json') {
            $printarr = false;
            $printarr = true;
            $extraparam = "";
            ///$p = '0000079979951';
            $cat       = "/Peserta/nokartu/";
            $data      = "6895"; //Ganti dengan consumerID dari BPJS
            $secretKey = "4aY0B49CF4"; //Ganti dengan consumerSecret dari BPJS
            $localIP   = "dvlp.bpjs-kesehatan.go.id";
            $port      = 8081; //port url
            $tgl       = date("Y-m-d");
            $url       = "http://".$localIP.":".$port."/VClaim-rest".$cat.$p."/tglSEP/".$tgl; //Lihat katalog, jangan sertakan port

            date_default_timezone_set('UTC');
            $tStamp = strval(time()-strtotime('1970-01-01 00:00:00'));
            $signature = hash_hmac('sha256', $data."&".$tStamp, $secretKey, true);
            $encodedSignature = base64_encode($signature);
            $urlencodedSignature = urlencode($encodedSignature);

            $opts = array(
             'http'=>array(
             'method'=>"GET",
             'header'=>"Host: api.asterix.co.id\r\n".
             "Connection: close\r\n".
             "X-timestamp: ".$tStamp."\r\n".
             "X-signature: ".$encodedSignature."\r\n".
             "User-Agent: Mozilla/5.0 (Windows NT 6.3; Win64; x64)\r\n".
             "X-cons-id: ".$data."\r\n".
             "Accept: application/json\r\n"
             )
            );

            $context = stream_context_create($opts);
            $url = $url.$extraparam;
            $result = file_get_contents($url, false, $context);

            if ($result === false) { 
                echo "Tidak dapat menyambung ke server"; 
            } else { 
                if($printarr == true){
                    if (is_ajax()):            
                        if ($a == 'json') {
                            // $resultarr = json_encode($result, true);
                            // echo $resultarr['response']['peserta'];
                            $d = json_decode($result, true);
                            $dataPeserta = array(['nmAsuransi' => $d['response']['peserta']['cob']['nmAsuransi'],
                                           'noAsuransi' => $d['response']['peserta']['cob']['noAsuransi'],
                                           'tglTATcob' => $d['response']['peserta']['cob']['tglTAT'],
                                           'tglTMTcob' => $d['response']['peserta']['cob']['tglTMT'],
                                           'keteranganhakKelas' => $d['response']['peserta']['hakKelas']['keterangan'],
                                           'kodehakKelas' => $d['response']['peserta']['hakKelas']['kode'],
                                           'dinsos' => $d['response']['peserta']['informasi']['dinsos'],
                                           'noSKTM' => $d['response']['peserta']['informasi']['noSKTM'],
                                           'prolanisPRB' => $d['response']['peserta']['informasi']['prolanisPRB'],
                                           'keteranganjenisPeserta' => $d['response']['peserta']['jenisPeserta']['keterangan'],
                                           'kodejenisPeserta' => $d['response']['peserta']['jenisPeserta']['kode'],
                                           'noMR' => $d['response']['peserta']['mr']['noMR'],
                                           'noTelepon' => $d['response']['peserta']['mr']['noTelepon'],
                                           'nama' => $d['response']['peserta']['nama'],
                                           'nik' => $d['response']['peserta']['nik'],
                                           'noKartu' => $d['response']['peserta']['noKartu'],
                                           'pisa' => $d['response']['peserta']['pisa'],
                                           'kdProvider' => $d['response']['peserta']['provUmum']['kdProvider'],
                                           'nmProvider' => $d['response']['peserta']['provUmum']['nmProvider'],
                                           'sex' => $d['response']['peserta']['sex'],
                                           'keteranganstatusPeserta' => $d['response']['peserta']['statusPeserta']['keterangan'],
                                           'kodestatusPeserta' => $d['response']['peserta']['statusPeserta']['kode'],
                                           'tglCetakKartu' => $d['response']['peserta']['tglCetakKartu'],
                                           'tglLahir' => $d['response']['peserta']['tglLahir'],
                                           'tglTAT' => $d['response']['peserta']['tglTAT'],
                                           'tglTMT' => $d['response']['peserta']['tglTMT'],
                                           'umurSaatPelayanan' => $d['response']['peserta']['umur']['umurSaatPelayanan'],
                                           'umurSekarang' => $d['response']['peserta']['umur']['umurSekarang'],
                                           'code' => $d['metaData']['code'],
                                           'message' => $d['metaData']['message'],
                                        ]);
                                echo json_encode($dataPeserta);
                            return;
                        }
                    endif;
                }
            }
        }else{
            $this->render('PesertaCek');
        }

    }

    public function CekSEP($a = 'null', $p = '0'){
        if ($a == 'json') {
            $printarr = false;
            $printarr = true;
            $extraparam = "";
            ///$p = '0000079979951';
            $consumerID    = "20721"; //Ganti dengan consumerID dari BPJS
            $secretKey = "7hBA19D1F2"; //Ganti dengan consumerSecret dari BPJS
            $url       = "http://dvlp.bpjs-kesehatan.go.id:8081/devWsLokalRest/SEP/".$p; //Lihat katalog, jangan sertakan port
            date_default_timezone_set('UTC');
            $tStamp = strval(time()-strtotime('1970-01-01 00:00:00'));
            $signature = hash_hmac('sha256', $consumerID."&".$tStamp, $secretKey, true);
            $encodedSignature = base64_encode($signature);
            $urlencodedSignature = urlencode($encodedSignature);
            $arrheader =  array("X-cons-id: " .$consumerID, 
                                "X-timestamp:" .$tStamp,
                                "X-signature: " .$encodedSignature,
                                'Content-type: Application/x-www-form-urlencoded');
            $opts = array(
                'http'=>array(
                    'method'=>"GET",
                    'header'=>"Host: api.asterix.co.id\r\n".
                    "Connection: close\r\n".
                    "X-timestamp: ".$tStamp."\r\n".
                    "X-signature: ".$encodedSignature."\r\n".
                    "User-Agent: Mozilla/5.0 (Windows NT 6.3; Win64; x64)\r\n".
                    "X-cons-id: ".$consumerID."\r\n".
                    "Accept: application/json\r\n"
                )
            );

            $context = stream_context_create($opts);
            $url = $url.$extraparam;
            $result = file_get_contents($url, false, $context);

            if ($result === false) { 
                echo "Tidak dapat menyambung ke server"; 
            } else { 
                if($printarr == true){
                    if (is_ajax()):            
                        if ($a == 'json') {
                            // $resultarr = json_encode($result, true);
                            // echo $resultarr['response']['peserta'];
                            // echo json_encode($result, true);
                            $d = json_decode($result, true);
                            $dataPeserta = array(['byTagihan' => $d['response']['byTagihan'],
                                           'catatan' => $d['response']['catatan'],
                                           'kdDiag' => $d['response']['diagAwal']['kdDiag'],
                                           'nmDiag' => $d['response']['diagAwal']['nmDiag'],
                                           'jnsPelayanan' => $d['response']['diagAwal']['jnsPelayanan'],
                                           'klsRawat' => $d['response']['diagAwal']['klsRawat'],
                                           'kdKelas' => $d['response']['diagAwal']['kdKelas'],
                                           'nmKelas' => $d['response']['diagAwal']['nmKelas'],
                                           'keterangan' => $d['response']['lakaLantas']['keterangan'],
                                           'status' => $d['response']['lakaLantas']['status'],
                                           'noRujukan' => $d['response']['noRujukan'],
                                           'noSep' => $d['response']['noSep'],
                                           'kdJenisPeserta' => $d['response']['peserta']['jenisPeserta']['kdJenisPeserta'],
                                           'nmJenisPeserta' => $d['response']['peserta']['jenisPeserta']['nmJenisPeserta'],
                                           'kdKelasKT' => $d['response']['peserta']['kelasTanggungan']['kdKelas'],
                                           'nmKelasKT' => $d['response']['peserta']['kelasTanggungan']['nmKelas'],
                                           'nama' => $d['response']['peserta']['nama'],
                                           'nik' => $d['response']['peserta']['nik'],
                                           'noKartu' => $d['response']['peserta']['noKartu'],
                                           'noMr' => $d['response']['peserta']['noMr'],
                                           'pisa' => $d['response']['peserta']['pisa'],
                                           'kdCabang' => $d['response']['provUmum']['kdCabang'],
                                           'kdProvider' => $d['response']['provUmum']['kdProvider'],
                                           'nmCabang' => $d['response']['provUmum']['nmCabang'],
                                           'nmProvider' => $d['response']['provUmum']['nmProvider'],
                                           'sex' => $d['response']['peserta']['sex'],
                                           'statusPeserta' => $d['response']['peserta']['statusPeserta'],
                                           'tglLahir' => $d['response']['peserta']['tglLahir'],
                                           'tglTAT' => $d['response']['peserta']['tglTAT'],
                                           'tglTMT' => $d['response']['peserta']['tglTMT'],
                                           'umur' => $d['response']['peserta']['umur'],
                                           'kdPoli' => $d['response']['peserta']['poliTujuan']['kdPoli'],
                                           'nmPoli' => $d['response']['peserta']['poliTujuan']['nmPoli'],
                                           'kdCabangPel' => $d['response']['peserta']['provPelayanan']['kdCabang'],
                                           'kdProviderPel' => $d['response']['peserta']['provPelayanan']['kdProvider'],
                                           'nmCabangPel' => $d['response']['peserta']['provPelayanan']['nmCabang'],
                                           'nmProviderPel' => $d['response']['peserta']['provPelayanan']['nmProvider'],
                                           'kdCabangR' => $d['response']['peserta']['provRujukan']['kdCabang'],
                                           'kdProviderR' => $d['response']['peserta']['provRujukan']['kdProvider'],
                                           'nmCabangR' => $d['response']['peserta']['provRujukan']['nmCabang'],
                                           'nmProviderR' => $d['response']['peserta']['provRujukan']['nmProvider'],
                                           'kdStatSep' => $d['response']['peserta']['statSep']['kdStatSep'],
                                           'nmStatSep' => $d['response']['peserta']['statSep']['nmStatSep'],
                                           'kodeCOB' => $d['response']['peserta']['statusCOB']['kodeCOB'],
                                           'namaCOB' => $d['response']['peserta']['statusCOB']['namaCOB'],
                                           'tglPulang' => $d['response']['tglPulang'],
                                           'tglRujukan' => $d['response']['tglRujukan'],
                                           'tglSep' => $d['response']['tglSep'],
                                           'code' => $d['metadata']['code'],
                                           'message' => $d['metadata']['message'],
                                        ]);
                                echo json_encode($dataPeserta);
                            return;
                        }
                    endif;
                }
            }
        }else{
            $this->render('SepCek');
        }

    }

    public function CreateSEP($a = 'view', $p = '0'){
        $isi = 'isi';
        $pencet = 'pencet';
        $this->set('isi', $isi);
        $this->set('pencet', $pencet);
        $data = $this->input->post();
        if ($a == 'data') {
            $data = $this->input->post();
            if (isset($data[$pencet]['Proses'])) {
                $printarr = false;
                $printarr = true;
                $extraparam = "";
                ///$p = '0000079979951';
                $consumerID  = "6895"; //Ganti dengan consumerID dari BPJS
                $secretKey = "4aY0B49CF4"; //Ganti dengan consumerSecret dari BPJS
                $url       = "http://dvlp.bpjs-kesehatan.go.id:8081/VClaim-rest/SEP/insert"; //Lihat katalog, jangan sertakan port
                date_default_timezone_set('UTC');
                $tStamp = strval(time()-strtotime('1970-01-01 00:00:00'));
                $signature = hash_hmac('sha256', $consumerID."&".$tStamp, $secretKey, true);
                $encodedSignature = base64_encode($signature);
                $urlencodedSignature = urlencode($encodedSignature);
                $arrheader =  array("X-cons-id: " .$consumerID, 
                                    "X-timestamp:" .$tStamp,
                                    "X-signature: " .$encodedSignature,
                                    'Content-type: Application/x-www-form-urlencoded');
        
                $noKartu = $data[$isi]['noKartu'];
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
                $lakaLantas = $data[$isi]['lakaLantas'];
                if (isset($data[$isi]['penjamin'])) { $penjamin = implode(',', $data[$isi]['penjamin']); }else{$penjamin = '';}
                $lokasiLaka = $data[$isi]['lokasiLaka'];
                $noTelp = $data[$isi]['noTelp'];
                $user = $data[$isi]['user'];


                  $SEP = '{
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
                           "jaminan": {
                              "lakaLantas": "'.$lakaLantas.'",
                              "penjamin": "'.$penjamin.'",
                              "lokasiLaka": "'.$lokasiLaka.'"
                           },
                           "noTelp": "'.$noTelp.'",
                           "user": "'.$user.'"
                        }
                     }
                  }';                         
                // echo "<pre>";
                // var_dump($SEP);
                // echo "</pre>";
                //$url= "http://dvlp.bpjs-kesehatan.go.id:8081/devWsLokalRest/SEP/insert";
                $process = curl_init($url); 

                curl_setopt($process, CURLOPT_URL, $url);
                curl_setopt($process, CURLOPT_VERBOSE, true);
                curl_setopt($process, CURLOPT_TIMEOUT, 30); 
                curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($process, CURLOPT_HTTPHEADER,$arrheader);
                curl_setopt($process, CURLOPT_POST, true); 
                curl_setopt($process, CURLOPT_POSTFIELDS, $SEP); 

                $return = curl_exec($process); 
                curl_close($process);

                $response = json_decode($return, true);
                $d = json_decode($return, true);
                $dataSEP = array(['noSEP' => $d['response']['sep']['noSep'],
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
                            ]);
                  // echo "<pre>";
                  // var_dump($SEP);
                  // echo "</pre>";
                echo json_encode($dataSEP);
            }else if (isset($data[$pencet]['Simpan'])) {
                if (count($this->input->post()) > 0) {
                    $data = $this->input->post();

                    /* Start Transaction */
                    $this->db->trans_start();
                    if (isset($data[$isi]['penjamin'])) { $data[$isi]['penjamin'] = implode(',', $data[$isi]['penjamin']); }else{$data[$isi]['penjamin'] = '';}
                    $this->SEP->create($data[$isi]);
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
        }else if ($a == 'print') {
            $row = $this->SEP->read($p)[0];
            $this->set('row', $row);
            $this->load->view('bridging/Bpjs/SepPrint', $this->data);            
        }else if ($a == 'views') {
            $row = $this->SEP->read($p)[0];
            $this->set('row', $row);
            $this->render('SepView');
        }else if ($a == 'dataUpdate') {
            if (isset($data[$pencet]['Proses'])) {
                $printarr = false;
                $printarr = true;
                $extraparam = "";
                ///$p = '0000079979951';
                $consumerID  = "6895"; //Ganti dengan consumerID dari BPJS
                $secretKey = "4aY0B49CF4"; //Ganti dengan consumerSecret dari BPJS
                $url       = "http://dvlp.bpjs-kesehatan.go.id:8081/VClaim-rest/SEP/Update"; //Lihat katalog, jangan sertakan port
                date_default_timezone_set('UTC');
                $tStamp = strval(time()-strtotime('1970-01-01 00:00:00'));
                $signature = hash_hmac('sha256', $consumerID."&".$tStamp, $secretKey, true);
                $encodedSignature = base64_encode($signature);
                $urlencodedSignature = urlencode($encodedSignature);
                $arrheader =  array("X-cons-id: " .$consumerID, 
                                    "X-timestamp:" .$tStamp,
                                    "X-signature: " .$encodedSignature,
                                    'Content-type: Application/x-www-form-urlencoded');
        
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
                $lakaLantas = $data[$isi]['lakaLantas'];
                if (isset($data[$isi]['penjamin'])) { $penjamin = implode(',', $data[$isi]['penjamin']); }else{$penjamin = '';}
                $lokasiLaka = $data[$isi]['lokasiLaka'];
                $noTelp = $data[$isi]['noTelp'];
                $user = $data[$isi]['user'];


                  $SEP = '{
                     "request": {
                        "t_sep": {
                           "noSep": "'.$noSep.'",
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
                           "jaminan": {
                              "lakaLantas": "'.$lakaLantas.'",
                              "penjamin": "'.$penjamin.'",
                              "lokasiLaka": "'.$lokasiLaka.'"
                           },
                           "noTelp": "'.$noTelp.'",
                           "user": "'.$user.'"
                        }
                     }
                  }';                         
                // echo "<pre>";
                // var_dump($SEP);
                // echo "</pre>";
                //$url= "http://dvlp.bpjs-kesehatan.go.id:8081/devWsLokalRest/SEP/insert";
                $process = curl_init($url); 

                curl_setopt($process, CURLOPT_URL, $url);
                curl_setopt($process, CURLOPT_VERBOSE, true);
                curl_setopt($process, CURLOPT_TIMEOUT, 30); 
                curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($process, CURLOPT_HTTPHEADER,$arrheader);
                // curl_setopt($process, CURLOPT_PUT, true); 
                curl_setopt($process, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($process, CURLOPT_POSTFIELDS, $SEP); 

                $return = curl_exec($process); 
                curl_close($process);

                $response = json_decode($return, true);
                $d = json_decode($return, true);
                $dataSEP = array(['noSEP' => $d['response'],       
                               'code' => $d['metaData']['code'],
                               'message' => $d['metaData']['message'],
                            ]);
                  // echo "<pre>";
                  // var_dump($dataSEP);
                  // echo "</pre>";
                echo json_encode($dataSEP);
            }else if (isset($data[$pencet]['Simpan'])) {
                if (count($this->input->post()) > 0) {
                    $data = $this->input->post();
                    // echo "<pre>";
                    // var_dump($data[$isi]);
                    // echo "</pre>";
                    /* Start Transaction */
                    $this->db->trans_start();
                    if (isset($data[$isi]['penjamin'])) { $data[$isi]['penjamin'] = implode(',', $data[$isi]['penjamin']); }else{$data[$isi]['penjamin'] = '';}
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
        }else if ($a == 'update') {
            $row = $this->SEP->read($p)[0];
            $this->set('row', $row);
            $this->render('SepUpdate');
        }else if ($a == 'dataEditTglPulang') {
            if (isset($data[$pencet]['Proses'])) {
                $printarr = false;
                $printarr = true;
                $extraparam = "";
                ///$p = '0000079979951';
                $consumerID  = "6895"; //Ganti dengan consumerID dari BPJS
                $secretKey = "4aY0B49CF4"; //Ganti dengan consumerSecret dari BPJS
                $url       = "http://dvlp.bpjs-kesehatan.go.id:8081/VClaim-rest/Sep/updtglplg"; //Lihat katalog, jangan sertakan port
                date_default_timezone_set('UTC');
                $tStamp = strval(time()-strtotime('1970-01-01 00:00:00'));
                $signature = hash_hmac('sha256', $consumerID."&".$tStamp, $secretKey, true);
                $encodedSignature = base64_encode($signature);
                $urlencodedSignature = urlencode($encodedSignature);
                $arrheader =  array("X-cons-id: " .$consumerID, 
                                    "X-timestamp:" .$tStamp,
                                    "X-signature: " .$encodedSignature,
                                    'Content-type: Application/x-www-form-urlencoded');
        
                $noSep = $data[$isi]['noSep'];
                $tglPulang = $data[$isi]['tglPulang'];
                $user = $data[$isi]['user'];


                  $SEP = '{
                     "request": {
                        "t_sep": {
                           "noSep": "'.$noSep.'",
                           "tglPulang": "'.$tglPulang.'",
                           "user": "'.$user.'"
                        }
                     }
                  }';                         
                // echo "<pre>";
                // var_dump($SEP);
                // echo "</pre>";
                //$url= "http://dvlp.bpjs-kesehatan.go.id:8081/devWsLokalRest/SEP/insert";
                $process = curl_init($url); 

                curl_setopt($process, CURLOPT_URL, $url);
                curl_setopt($process, CURLOPT_VERBOSE, true);
                curl_setopt($process, CURLOPT_TIMEOUT, 30); 
                curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($process, CURLOPT_HTTPHEADER,$arrheader);
                // curl_setopt($process, CURLOPT_PUT, true); 
                curl_setopt($process, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($process, CURLOPT_POSTFIELDS, $SEP); 

                $return = curl_exec($process); 
                curl_close($process);

                $response = json_decode($return, true);
                $d = json_decode($return, true);
                $dataSEP = array(['noSEP' => $d['response'],       
                               'code' => $d['metaData']['code'],
                               'message' => $d['metaData']['message'],
                            ]);
                  // echo "<pre>";
                  // var_dump($dataSEP);
                  // echo "</pre>";
                echo json_encode($dataSEP);
            }else if (isset($data[$pencet]['Simpan'])) {
                if (count($this->input->post()) > 0) {
                    $data = $this->input->post();
                    // echo "<pre>";
                    // var_dump($data[$isi]);
                    // echo "</pre>";
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
        }else if ($a == 'edittglpulang') {
            $row = $this->SEP->read($p)[0];
            $this->set('row', $row);
            $this->render('SepEditTglPulang');    
        }else if ($a == 'dataDelete') {
            if (isset($data[$pencet]['Hapus'])) {
                $printarr = false;
                $printarr = true;
                $extraparam = "";
                ///$p = '0000079979951';
                $consumerID  = "6895"; //Ganti dengan consumerID dari BPJS
                $secretKey = "4aY0B49CF4"; //Ganti dengan consumerSecret dari BPJS
                $url       = "http://dvlp.bpjs-kesehatan.go.id:8081/VClaim-rest/SEP/Delete"; //Lihat katalog, jangan sertakan port
                date_default_timezone_set('UTC');
                $tStamp = strval(time()-strtotime('1970-01-01 00:00:00'));
                $signature = hash_hmac('sha256', $consumerID."&".$tStamp, $secretKey, true);
                $encodedSignature = base64_encode($signature);
                $urlencodedSignature = urlencode($encodedSignature);
                $arrheader =  array("X-cons-id: " .$consumerID, 
                                    "X-timestamp:" .$tStamp,
                                    "X-signature: " .$encodedSignature,
                                    'Content-type: Application/x-www-form-urlencoded');
        
                $noSep = $data[$isi]['noSep'];
                $user = $data[$isi]['user'];


                  $SEP = '{
                     "request": {
                        "t_sep": {
                           "noSep": "'.$noSep.'",
                           "user": "'.$user.'"
                        }
                     }
                  }';                         
                // echo "<pre>";
                // var_dump($SEP);
                // echo "</pre>";
                //$url= "http://dvlp.bpjs-kesehatan.go.id:8081/devWsLokalRest/SEP/insert";
                $process = curl_init($url); 

                curl_setopt($process, CURLOPT_URL, $url);
                curl_setopt($process, CURLOPT_VERBOSE, true);
                curl_setopt($process, CURLOPT_TIMEOUT, 30); 
                curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($process, CURLOPT_HTTPHEADER,$arrheader);
                // curl_setopt($process, CURLOPT_PUT, true); 
                curl_setopt($process, CURLOPT_CUSTOMREQUEST, 'DELETE');
                curl_setopt($process, CURLOPT_POSTFIELDS, $SEP); 

                $return = curl_exec($process); 
                curl_close($process);

                $response = json_decode($return, true);
                $d = json_decode($return, true);
                $dataSEP = array(['noSEP' => $d['response'],       
                               'code' => $d['metaData']['code'],
                               'message' => $d['metaData']['message'],
                            ]);
                  // echo "<pre>";
                  // var_dump($dataSEP);
                  // echo "</pre>";
                
            //}else if (isset($data[$pencet]['Simpan'])) {
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
        }else if ($a == 'delete') {
            $row = $this->SEP->read($p)[0];
            $this->set('row', $row);
            $this->render('SepDelete');    
        }else if ($a == 'redirect'){
            $DataPendaftaran = $this->Pendaftaran->read($p)[0];
            $this->set('DataPendaftaran', $DataPendaftaran);
            $this->render('SepCreate');
        }
    }

    public function cekKamar($data = null){
        //$url = "http://api.asterix.co.id/SepWebRest/sep/create/";
        //$url = "http://dvlp.bpjs-kesehatan.go.id:8081/devwslokalrest/sep/insert";
        /*url server lama*/
        //$url = "http://172.164.121.1:8082/SepLokalRest/sep/";
        
        /*url server baru*/
       /*url server baru*/
        $printarr = false;
        $printarr = true;
        $extraparam = "";
        ///$p = '0000079979951';
        $cat = "/rest/ref/kelas";

        $data    = "20721"; //Ganti dengan consumerID dari BPJS
        $secretKey = "7hBA19D1F2"; //Ganti dengan consumerSecret dari BPJS
        $localIP   = "192.168.144.144";
        $port      = 9080; //port url
        $url       = "http://".$localIP.":".$port."/aplicaresws".$cat; //Lihat katalog, jangan sertakan port

        date_default_timezone_set('UTC');
        $tStamp = strval(time()-strtotime('1970-01-01 00:00:00'));
        $signature = hash_hmac('sha256', $data."&".$tStamp, $secretKey, true);
        $encodedSignature = base64_encode($signature);
        $urlencodedSignature = urlencode($encodedSignature);

        $opts = array(
         'http'=>array(
         'method'=>"GET",
         'header'=>"Host: api.asterix.co.id\r\n".
         "Connection: close\r\n".
         "X-timestamp: ".$tStamp."\r\n".
         "X-signature: ".$encodedSignature."\r\n".
         "User-Agent: Mozilla/5.0 (Windows NT 6.3; Win64; x64)\r\n".
         "X-cons-id: ".$data."\r\n".
         "Accept: application/json\r\n"
         )
        );

        $context = stream_context_create($opts);
        $url = $url.$extraparam;
        $result = file_get_contents($url, false, $context);

        //echo "<br>Respon:";
        if ($result === false) 
        { 
         echo "Tidak dapat menyambung ke server"; 
        } else { 
         //echo $result;
         if($printarr==true){
            /* echo "<pre>";
            print_r(json_decode($result, true)); 
            echo "</pre>"; */

            
            //contoh penggunaan array untuk menampilkan peserta
            $resultarr = json_decode($result, true);
            echo json_encode($result, true);
            /* if ( $resultarr['metadata']['code'] == "201" )
            {
                echo "Pesan : ". $resultarr['metadata']['message']."<br />";
            }else if ( $resultarr['metadata']['code'] == "200" )    
            {
                echo "Pesan : ". $resultarr['metadata']['message']."<br />";
                echo "Nama :". $resultarr['response']['peserta']['nama'] ."<br />";
                echo "Nik  :". $resultarr['response']['peserta']['nik'] ."<br />";
                echo "Lahir:". date("d-m-Y", strtotime($resultarr['response']['peserta']['tglLahir'])) ."<br />";
                echo "Usia :". $resultarr['response']['peserta']['umur']['umurSekarang'] ."<br />";
                echo "#Krtu:". $resultarr['response']['peserta']['noKartu'] ."<br />";
                echo "Sex  :". $resultarr['response']['peserta']['sex'] ."<br />";
            } */
            
         }
        }
        //$this->load->view('medis/Pendaftaran/result', $this->data);
    }
    public function list_riwayat_pelayanan($p = "0000079979951"){
            $model = 'Pendaftaran';
            $printarr = false;
            $printarr = true;
            $extraparam = "";
            // $p = '0000079979951';
             $data      = "20721"; //Ganti dengan consumerID dari BPJS
            $cat = "/sep/peserta/";
            $secretKey = "7hBA19D1F2"; //Ganti dengan consumerSecret dari BPJS
            $localIP   = "dvlp.bpjs-kesehatan.go.id";
            $port      = 8081; //port url
            $url       = "http://".$localIP.":".$port."/devwslokalrest".$cat.$p; //Lihat katalog, jangan sertakan port

            date_default_timezone_set('UTC');
            $tStamp = strval(time()-strtotime('1970-01-01 00:00:00'));
            $signature = hash_hmac('sha256', $data."&".$tStamp, $secretKey, true);
            $encodedSignature = base64_encode($signature);
            $urlencodedSignature = urlencode($encodedSignature);

            $opts = array(
             'http'=>array(
             'method'=>"GET",
             'header'=>"Host: api.asterix.co.id\r\n".
             "Connection: close\r\n".
             "X-timestamp: ".$tStamp."\r\n".
             "X-signature: ".$encodedSignature."\r\n".
             "User-Agent: Mozilla/5.0 (Windows NT 6.3; Win64; x64)\r\n".
             "X-cons-id: ".$data."\r\n".
             "Accept: application/json\r\n"
             )
            );

            $context = stream_context_create($opts);
            $url = $url.$extraparam;
            $result = file_get_contents($url, false, $context);

            if ($result === false) { 
                echo "Tidak dapat menyambung ke server"; 
                $r = arry();
            } else { 
               $r = json_decode($result);
            }
           $this->$model->whereFields['e.noKartu'] = $p;
           $this->set('data',$this->$model->read());
           $this->set('result',$r);
           $this->render('list_riwayat_pelayanan');

    }
    public function list_index(){
        $model = 'Pendaftaran';
        $this->set('primaryKey', $this->$model->primaryKey);
        $this->render('list_index');
    }

    public function list_peserta($a = 'list'){
        $this->title = "Data Pasien BPJS";
        $model = 'Pendaftaran';
        if(is_ajax()): 
            $columns = array(
                array( "name" => "kd_trx_pendaftaran", "data" => "kd_trx_pendaftaran"),
                array( "name" => "noKartu", "data" => "noKartu"),
                array( "name" => "nm_mst_pasien", "data" => "nm_mst_pasien"),
                array( "name" => "nm_mst_unit_kerja", "data" => "nm_mst_unit_kerja"),
                array( "name" => "nm_mst_unit_kerja_sub", "data" => "nm_mst_unit_kerja_sub"),
                array( "name" => "action", "data" => array("_" => "action", "order" => false), "searchable" => false),

            );          
            $columnDefs = array(
                array( "targets" => array( 1 ), "orderable" => false),
            );
            
            if ($a == 'list') {
                $this->set('columns', $columns);
                $this->set('columnDefs', $columnDefs);
                $this->load->view($this->directory.ucwords($this->name)."/list_peserta", $this->data);
                return;
            } else if ($a == 'json') {              
                // $flag_kepegawaian = get_data_helper('flag_kepegawaian');
                // foreach ($flag_kepegawaian as $t) $kepegawaian[$t->kd_mst_data_helper] = $t;
                
                // $flag_perkawinan = get_data_helper('flag_perkawinan');
                // foreach ($flag_perkawinan as $t) $perkawinan[$t->kd_mst_data_helper] = $t;
                $dtPost = $this->input->post();
                if ( isset($dtPost['search']['value']) && $dtPost['search']['value'] != '' ) {
                    $whereLike = array(
                        'a.kd_trx_pendaftaran' => $dtPost['search']['value'],
                        'e.noKartu' => $dtPost['search']['value'],
                        'd.nm_mst_pasien' => $dtPost['search']['value']
                    );
                    $this->$model->set_variable('whereLike', $whereLike);
                }

                $array_data = array();
                $primaryKey = $this->$model->primaryKey;
                $return = $this->$model->dataTable('0', $columns, $columnDefs);
                
                if (count($return['data']) > 0):                    
                    
                    foreach ($return['data'] as $i => $r):          
                        
                        $r->DT_RowId = $r->kd_trx_pendaftaran;
                        $button = '';
                        if (is_auth($this->name, 'update')){
                            $button .= '<a href="'.base_url().'bridging/bpjs/list_riwayat_pelayanan/'.$r->noKartu.'"  data-toggle="tooltip" data-placement="top" title="Cek Riwayat Peserta"  class="btn btn-xs btn-info">Riwayat Peserta</a>';
                        }      
						
        				$sep = 'SEP';
                        $this->$sep->whereFields['a.noKartu'] = $r->noKartu;
                        $this->$sep->whereFields['a.flag_active'] = 1;
                        $cek = $this->$sep->read();
                        if (count($cek) > 0) {
                            $button .= '  <a href="'.base_url().'bridging/Bpjs/CreateSEP/update/'.$cek[0]->noSep.'"  data-toggle="tooltip" data-placement="top" title="Update SEP"  class="btn btn-xs btn-warning">Update</a>';
                            $button .= '  <a href="'.base_url().'bridging/Bpjs/CreateSEP/views/'.$cek[0]->noSep.'"  data-toggle="tooltip" data-placement="top" title="View SEP"  class="btn btn-xs btn-primary">View</a>';
                        	 if ($cek[0]->flag_mapping == 0) {
                                 $button .= '   <button id_data= "'.$cek[0]->noSep.'" data_ppk="'.$cek[0]->ppkPelayanan.'" onClick="return mapping(this)" class="btn btn-default btn-xs" data-toggle="tooltip" data-placement="top" title="Update SEP" > Mapping Transaksi</button>';
                            } 
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
    public function input_mapping(){
        $sep = 'SEP';
        $data = $this->input->post();
        $explod = explode('|', $data['nosep']);
        $datas['request']['t_map_sep'] = [
                                            "noSep" => $explod[0],
                                            "noTrans" => $data['noTrans'],
                                            "ppkPelayanan" => $explod[1]
                                        ];
        $parser =  json_encode($datas); 
            $cons      = "20721"; //Ganti dengan consumerID dari BPJS
            $cat = "/SEP/map/trans";
            $secretKey = "7hBA19D1F2"; //Ganti dengan consumerSecret dari BPJS
            $localIP   = "dvlp.bpjs-kesehatan.go.id";
            $port      = 8081; //port url
            $url       = "http://".$localIP.":".$port."/devwslokalrest".$cat; //Lihat katalog, jangan sertakan port

            date_default_timezone_set('UTC');
            $tStamp = strval(time()-strtotime('1970-01-01 00:00:00'));
            $signature = hash_hmac('sha256', $cons."&".$tStamp, $secretKey, true);
            $encodedSignature = base64_encode($signature);
            $urlencodedSignature = urlencode($encodedSignature);

            $arrheader =  array("X-cons-id: " .$cons, 
                            "X-timestamp:" .$tStamp,
                            "X-signature: " .$encodedSignature,
                            'Content-type: Application/x-www-form-urlencoded');

            $process = curl_init($url); 

                curl_setopt($process, CURLOPT_URL, $url);
                curl_setopt($process, CURLOPT_VERBOSE, true);
                curl_setopt($process, CURLOPT_TIMEOUT, 30); 
                curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($process, CURLOPT_HTTPHEADER,$arrheader);
                curl_setopt($process, CURLOPT_POST, true); 
                curl_setopt($process, CURLOPT_POSTFIELDS, $parser); 

                $return = curl_exec($process); 
                curl_close($process);
        		$res = json_decode($return);
                if ($res->metadata->code == "200") {
                     $update['flag_mapping'] = 1;
                     $update['noSep'] = $explod[0];
                     $this->$sep->replace($update);
                     $this->setFlash($res->metadata->message, 'Success Membuat Mapping Transaksi Untuk No Sep '.$explod[0].'', 'alert-success');
                } else{
                    $this->setFlash($res->metadata->message, 'Gagal Membuat Mapping Transaksi Untuk No Sep '.$explod[0].' Contact your IT Support.', 'alert-danger');
                }
        
        redirect($this->directory."$this->name"."/list_index");

    

    }
    public function pengajuan(){
        $trx_detail_1 = "pengajuan";
        $this->set('row',$this->$trx_detail_1->read());
        $this->render('pengajuan');
    }
    public function aprov(){
        $data['request']['t_sep']['noKartu'] = "0000000000022";
        $data['request']['t_sep']['tglSep'] = "2018-01-29";
        $data['request']['t_sep']['jnsPelayanan'] = "1";
        $data['request']['t_sep']['keterangan'] = "bpjs bermasalah";
        $data['request']['t_sep']['user'] = "tedi coba";
        $req = json_encode($data);
        $cat = "/Sep/aprovalSEP";
        $hasil = $this->vclaim->post_vclaim($req,$cat);
        $this->setFlash('FAILED: ', 'Failed '.$hasil['metaData']['message'].'. Contact your IT Support.', 'alert-danger');
        redirect('bridging/bpjs/pengajuan','refresh');
    }
    public function create_pengajuan(){
        $this->title = "Pengajuan Penjaminan";
        $trx_head_1 = "Pendaftaran";
        $trx_detail_1 = "pengajuan";
        $trx_detail_2 = "pegawai";

        if (count($this->input->post()) > 0) {
            $data = $this->input->post();
            $this->$trx_detail_2->whereFields['a.id_mst_pegawai'] = $this->auth->get_user('id_mst_pegawai');
            $peg = $this->$trx_detail_2->read()[0];
            $req['request']['t_sep']['noKartu'] = $data[$trx_detail_1]['noKartu'];
            $req['request']['t_sep']['tglSep'] = $data[$trx_detail_1]['tglSep'];
            $req['request']['t_sep']['jnsPelayanan'] = $data[$trx_detail_1]['jnsPelayanan'];
            $req['request']['t_sep']['keterangan'] = $data[$trx_detail_1]['keterangan'];
            $req['request']['t_sep']['user'] = $peg->nm_mst_pegawai_lengkap;
            $data[$trx_detail_1]['user'] = $peg->nm_mst_pegawai_lengkap;
            $this->db->trans_start();
              $req = json_encode($req);
              $cat = "/Sep/pengajuanSEP";
              $hasil = $this->vclaim->post_vclaim($req,$cat);
              if ($hasil['metaData']['code'] == '200') {
                  $this->$trx_detail_1->create($data[$trx_detail_1]);
              }else{
                    $this->setFlash('FAILED: ', 'Failed  '.$hasil['metaData']['message'].'. Contact your IT Support.', 'alert-danger');
              }
            $this->db->trans_complete();
        }
        $this->set('peserta',$this->$trx_head_1->read());
        $this->set('trx_detail_1',$trx_detail_1);
        $this->render('create_pengajuan');
    }
    public function cari_peserta(){
        $trx_detail_1 = "pengajuan";
        if (count($this->$trx_detail_1->get_peserta($this->input->post('no'))) > 0) {
           echo json_encode($this->$trx_detail_1->get_peserta($this->input->post('no')));
        }

    }
    public function test(){
      $cat = '/referensi/spesialistik';
      print_r($this->vclaim->get_vclaim($cat));
    }

}

?>
