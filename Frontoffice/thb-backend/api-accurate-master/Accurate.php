<?php

class Accurate{
    
    public $url_acc   = 'https://account.accurate.id/api/';
    public $SESSIONID = '';

    function get($token = null, $url = null, $param = null){

       $ch = curl_init($this->url_acc.$url.'.do');
       $authorization   = array();
       $authorization[] = "Authorization:Bearer ".$token; 
    
       if($this->SESSIONID !== null){
    
        $authorization[] = "X-Session-ID:".$this->SESSIONID; 
       
       }
       
       if($param !== null){
    
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param );
 
       }
    
       curl_setopt($ch, CURLOPT_HTTPHEADER, $authorization ); 
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       $result = curl_exec($ch);
       curl_close($ch); 
       return json_decode($result,true); 


    }

}

?>