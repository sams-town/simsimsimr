<h2>Database Anda :</h2>
<?php
include "Accurate.php";

// pertama kita harus memilih database yang mana terlebih dahulu

$acc     = new accurate;
$result  =  $acc->get($_GET['token'],"db-list");

echo "Jumlah database : ".$result['s']."<p>";
echo "<table border='1' style='width:100%'>";

foreach ($result['d'] as $key => $data) {


    echo "<tr>";
    echo "<td colspan='2'>Detail Database</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td width='150'> Lisensi Berakhir </td><td> {$data['licenseEnd']} </td>";
    echo "</tr>";
    
    echo "<tr>";
    echo "<td width='150'> Nama </td><td> {$data['alias']} </td>";
    echo "</tr>";
    
    echo "<tr>";
    echo "<td width='150'> Link </td><td> <a href='open-db.php?token={$_GET['token']}&db={$data['id']}'>Open Database [ {$data['id']} ]</a> </td>";
    echo "</tr>";
    


}

echo "</table>";
?>
