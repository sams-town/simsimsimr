<?php defined('BASEPATH') OR exit('No direct script access allowed');
use LZCompressor\LZString as LZString;
/**
*/
class Vclaim_icare{
	
	// private $tStamp		= strval(time()-strtotime('1970-01-01 00:00:00'));
	
	// DEV
	// Consid : 8719
	// Secretkey : 3bOC872AFD
	// User Key ANTROL: 222bbc7405928104ba2a2e9659055eb3
	// User Key VCLAIM : b9dc35e9f355e57ff943f04620ee3d5f
	// ANTROL apijkn-dev.bpjs-kesehatan.go.id/antreanrs_dev
	// VCLAIM apijkn-dev.bpjs-kesehatan.go.id/vclaim-rest-dev

	// PROD 
	// Consid : 9121
	// Secretkey : YdWEr921bSd
	// User Key ANTROL : cf6908d2c13efc03c59e019dae4abecd
	// User Key VCLAIM : 17d61ffaa112c5bd13aea2b311f05613
	// ANTROL apijkn.bpjs-kesehatan.go.id/antreanrs
	// VCLAIM apijkn.bpjs-kesehatan.go.id/vclaim-rest

	private $consid 	= "9121";
	private $secretkey 	= "YdWEr921bSd";
	private $ppkrs 		= "0070R032";
	private $userkey 	= "17d61ffaa112c5bd13aea2b311f05613";
	private $host 		= "apijkn.bpjs-kesehatan.go.id/wsihs";
	private $url;			
	private $ONVCLAIM	= true;


	public static function stringDecrypt($key, $string)
	{
		$encrtyp_method = 'AES-256-CBC';

        $key_hash = hex2bin(hash('sha256', $key));

        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);

        $output = openssl_decrypt(base64_decode($string), $encrtyp_method, $key_hash, OPENSSL_RAW_DATA, $iv);

        return $output;
	}

	// function lzstring decompress 
	// download libraries lzstring : https://github.com/nullpunkt/lz-string-php
	public static function decompress($string)
	{
		return LZString::decompressFromEncodedURIComponent($string);
	}

	public function doDecompress($jsonObject, $key)
	{
		if ($jsonObject->metaData->message == "Sukses") {
			return $this->mappingResponse($jsonObject->metaData, $jsonObject->response, $key);
		}
		return json_encode($jsonObject);
	}

	public function responseBpjsV2($dataJson, $key)
	{
		$result = json_decode($dataJson);
		if ($result->metaData->message == "Sukses") {
            return $this->doDecompress($result, $key);
        }
        return json_encode($result);
	}

	public function mappingResponse($metaData, $response, $key)
	{
		$data = [
            "metaData" => $metaData,
            "response" => json_decode($this->decompress($this->stringDecrypt($key, $response)), true)
        ];
		return $data;
	}

	public function request($para,$method,$DataPost=''){
		date_default_timezone_set('UTC');
		if($this->ONVCLAIM==true){
			$this->url  		 = "https://".$this->host.$para; 
			$tStamp 			 = strval(time()-strtotime('1970-01-01 00:00:00'));
			$signature 			 = hash_hmac('sha256', $this->consid."&".$tStamp, $this->secretkey, true);
			$encodedSignature 	 = base64_encode($signature);
			$urlencodedSignature = urlencode($encodedSignature);
		    if($method=='GET'){
				$header = array("X-timestamp: ".$tStamp."\r\n"."X-signature: ".$encodedSignature."\r\n"."user_key: ".$this->userkey."\r\n"."X-cons-id: ".$this->consid."\r\n"."Accept: application/json; charset=utf-8\r\n");
		    }else{
				$header = array("X-cons-id: " .$this->consid,"X-timestamp:" .$tStamp,"X-signature: " .$encodedSignature,"user_key: " .$this->userkey,'Content-type: application/json');
		    }
		    $curl = curl_init();
	        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
	        curl_setopt($curl, CURLOPT_URL, $this->url);
	  		// curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
			// curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		    if($method=='POST' || $method=='PUT' || $method=='DELETE'){ curl_setopt($curl, CURLOPT_POSTFIELDS, $DataPost); }
	        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 20);
	        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
	        $result = curl_exec($curl);
			// var_dump($result['response']);
			// var_dump($this->responseBpjsV2($result,$this->consid.$this->secretkey.$tStamp));
			// var_dump($this->url);
			// var_dump(json_decode($result));die();
			$result = $this->responseBpjsV2($result,$this->consid.$this->secretkey.$tStamp);
			if ($result) {
				$result = json_encode($result, true);
			}
			if ($result === false){ 
				echo "Tidak dapat menyambung ke server"; 
			} else { 
				return $result;
			}


		}else{
			$result='Pengaturan dari SIMRS bridging ke VLCAIM telah dimatikan';
		}
	    return $result;
	    // return $result;
	}
}