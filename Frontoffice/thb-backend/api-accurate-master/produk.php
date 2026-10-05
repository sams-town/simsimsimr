<?php
include "Accurate.php";

$acc            = new accurate;
$acc->url_acc   = $_GET['host'].'/accurate/api/'; // url accurate
$acc->SESSIONID = $_GET['session']; // session dari database yang kita buka

$page    = isset($_GET['p'])?$_GET['p']:1; // halaman yang di tuju

$opt     = array("fields"=>"id,name,no,unitPrice",'sp.page'=>$page); // fields yang akan di tampilkan
$result  =  $acc->get($_GET['token'],"item/list",$opt); // ambil data produk

$info_halaman = $result['sp']; // berisi total yang di tampilkan,total yang di tampilkan,total halaman

echo "<h3>Data Produk</h3>";
echo "Jumlah Produk :".$info_halaman['rowCount'];

echo "<table border='1'>";
echo "<tr>
        <th>id</th>  
        <th>Nama</th>  
        <th>Harga</th>  
        <th>Stok</th>  
        <th>Detail</th>  
      </tr>";

foreach ($result['d'] as $key => $value) {

   $whereStok = array("no"=>$value['no']); // ambil no dari data produk
   $stock     = $acc->get($_GET['token'],"item/get-stock",$whereStok); // ambil data stok
   $stokSkrg  = $stock['d']['availableStock']; // stok 
  
   echo "<tr>
        <td>{$value['id']}</td>  
        <td>{$value['name']}</td>  
        <td>{$value['unitPrice']}</td>  
        <td>{$stokSkrg}</td>  
        <td><a href='produk-detail.php?id={$value['id']}&session={$_GET['session']}&host={$_GET['host']}&token={$_GET['token']}'>Detail</a></td>  
      </tr>";

}

echo "</table>";
echo "<p>";
echo "hal : ";
for ($i=1; $i <= $info_halaman['pageCount'] ; $i++) { 
    echo "<a href='?p={$i}&session={$_GET['session']}&host={$_GET['host']}&token={$_GET['token']}'>$i</a> " ;
}