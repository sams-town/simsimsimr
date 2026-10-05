<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Vclaim_aplicares{
	private $consid 	= "9121";
	private $secretkey 	= "YdWEr921bSd";
	private $ppkrs 		= "0070R032";
	private $host 		= "new-api.bpjs-kesehatan.go.id/aplicaresws";
	// private $host 		= "dvlp.bpjs-kesehatan.go.id:8888/aplicaresws";
	private $url;
	private $ONVCLAIM	= true;

	
	public function request($para,$method,$DataPost=''){
		if($this->ONVCLAIM==true){
			$this->url  		 = "https://".$this->host.$para; 
			date_default_timezone_set('UTC');
			$tStamp 			 = strval(time()-strtotime('1970-01-01 00:00:00'));
			$signature 			 = hash_hmac('sha256', $this->consid."&".$tStamp, $this->secretkey, true);
			$encodedSignature 	 = base64_encode($signature);
			$urlencodedSignature = urlencode($encodedSignature);
		    if($method=='GET'){
				$header = array (
					'X-cons-id: '.$this->consid,
					'X-Timestamp: '.$tStamp,
					'X-Signature: '.$encodedSignature,
					'Accept: application/json',
					'Content-Type: application/json'
				);
		    }else{
				$header = array (
					'X-cons-id: '.$this->consid,
					'X-Timestamp: '.$tStamp,
					'X-Signature: '.$encodedSignature,
					'Accept: application/json',
					'Content-Type: application/json'
				);
		    }
		    $curl = curl_init();
	        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
	        curl_setopt($curl, CURLOPT_URL, $this->url);
	  		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		    if($method=='POST' || $method=='PUT'){ curl_setopt( $curl, CURLOPT_POST, true ); curl_setopt($curl, CURLOPT_POSTFIELDS, $DataPost); }
	        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 20);
	        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
	        $result = curl_exec($curl);
	        $result = json_encode($result);
			if ($result === false){ 
				echo "Tidak dapat menyambung ke server"; 
			} else { 
				$resultarr = json_decode($result, true);
				return $resultarr;
			}
		}else{
			$result='Pengaturan dari SIMRS bridging ke VLCAIM telah dimatikan';
		}
	    return $result;
	    // return $result;
	}
}