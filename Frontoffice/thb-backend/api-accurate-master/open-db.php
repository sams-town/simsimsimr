<?php
include "Accurate.php";
// buka database


$acc     = new accurate;
$opt     = array("id"=>$_GET['db']);
$result  =  $acc->get($_GET['token'],"open-db",$opt);
$session = $result['session'];
$host    = $result['host'];
?>

<a href="produk.php?token=<?php echo $_GET['token']."&session=".$session."&host=".$host; ?>"><h4>Produk</h4></a>