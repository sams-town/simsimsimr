<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Transaction {
	protected $data;
	protected $ci;
	protected $allows = array();

	public function __construct() {
		$this->ci =& get_instance();
		$this->ci->load->library('session');
	}
	
	public function counter($data = array()) {
		$this->ci->load->model('Counter_queue_model', 'Counter_queue');
		if (count($data) == 0) {
			$data = $this->input->post();
		}
		
		$fields = array();
		$values = array();
		foreach ($data as $f => $v) {
			array_push($fields, $f);
			array_push($values, $v);
		}
		return $this->ci->Counter_queue->do_counter($fields, $values);
	}

	function HitungTotalRincian($view_data){
		$totalSemua = 0;
        	if (count($view_data['trxRincian_administrasi']) > 0) {
        		foreach ($view_data['trxRincian_administrasi'] as $key => $adm) {
        			$totalSemua += $adm->nilai_total;
        		}
        	}
        	if (count($view_data['trxRincian_kedokteran']) > 0) {
        		foreach ($view_data['trxRincian_kedokteran'] as $key => $dok) {
        			$totalSemua += $dok->nilai_total;
        		}
        	}
        	if (count($view_data['trxRincian_keperawatan']) > 0) {
        		foreach ($view_data['trxRincian_keperawatan'] as $key => $per) {
        			$totalSemua += $per->nilai_total;
        		}
        	}
        	if (count($view_data['trxRincian_laboratorium']) > 0) {
        		foreach ($view_data['trxRincian_laboratorium'] as $key => $lab) {
        			$totalSemua += $lab->nilai_total;
        		}
        	}
        	if (count($view_data['trxRincian_radiologi']) > 0) {
        		foreach ($view_data['trxRincian_radiologi'] as $key => $rad) {
        			$totalSemua += $rad->nilai_total;
        		}
        	}
        	if (count($view_data['trxRincian_penunjang']) > 0) {
        		foreach ($view_data['trxRincian_penunjang'] as $key => $pen) {
        			$totalSemua += $pen->nilai_total;
        		}
        	}
        	if (count($view_data['trxRincian_tempat_tidur']) > 0) {
        		foreach ($view_data['trxRincian_tempat_tidur'] as $key => $tem) {
        			$totalSemua += $tem->nilai_total;
        		}
        	}
        	if (count($view_data['trxRincian_resep']) > 0) {
        		foreach ($view_data['trxRincian_resep'] as $key => $res) {
        			$totalSemua += $res->nilai_total;
        		}
        	}
        	if (count($view_data['trxRincian_obat']) > 0) {
        		foreach ($view_data['trxRincian_obat'] as $key => $obat) {
        			$totalSemua += $obat->nilai_total;
        		}
        	}
        	if (count($view_data['trxRincian_umum']) > 0) {
        		foreach ($view_data['trxRincian_umum'] as $key => $umum) {
        			$totalSemua += $umum->nilai_total;
        		}
        	}
        	if (count($view_data['trxRincian_lainnya']) > 0) {
        		foreach ($view_data['trxRincian_lainnya'] as $key => $lain) {
        			$totalSemua += $lain->nilai_total;
        		}
        	}
        	if (count($view_data['trxRincian_kebidanan']) > 0) {
        		foreach ($view_data['trxRincian_kebidanan'] as $key => $bidan) {
        			$totalSemua += $bidan->nilai_total;
        		}
        	}
        	if (count($view_data['trxRincian_ruang_tindakan']) > 0) {
        		foreach ($view_data['trxRincian_ruang_tindakan'] as $key => $rt) {
        			$totalSemua += $rt->nilai_total;
        		}
        	}
        	if (count($view_data['trxRincian_fisioterapi']) > 0) {
        		foreach ($view_data['trxRincian_fisioterapi'] as $key => $darah) {
        			$totalSemua += $darah->nilai_total;
        		}
        	}
        	if (count($view_data['trxTransaksi_darah_item']) > 0) {
        		foreach ($view_data['trxTransaksi_darah_item'] as $key => $fisio) {
        			$totalSemua += $fisio->nilai_total;
        		}
        	}
        
        return $totalSemua;
	}

        
	function cekKelasNaik($kamar = 0,$pasien = 0){
                if ($pasien > 3 && $kamar > 3) {
                        return $kamar > $pasien;
                }elseif ($pasien <= 3 && $kamar <= 3) {
                        return $kamar < $pasien;
                }elseif ($kamar < $pasien && $pasien <= 3) {
                        return true;
                }elseif ($kamar < $pasien && $pasien > 3) {
                        return false;
                }else{
                        return false;
                }
        }
}
?>
