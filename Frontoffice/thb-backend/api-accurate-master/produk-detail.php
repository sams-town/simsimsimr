<?php
include "Accurate.php";

$acc            = new accurate;
$acc->url_acc   = $_GET['host'].'/accurate/api/';
$acc->SESSIONID = $_GET['session'];

$opt     = array("id"=>$_GET['id']);
$result  =  $acc->get($_GET['token'],"item/detail",$opt);
echo "<pre>";
print_r($result);