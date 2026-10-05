<script>
// cek url mengandung hash (#) yang berisi token apa tidak 
if(window.location.hash) {
    
    // ambil token 
    hasil  = decodeURIComponent(window.location.hash).split(new RegExp('=|&','g'));
    token  = hasil[1];
    window.location = "database.php?token="+token;


} else {
    
    // kita akan ambil token menggunakan method get 
    var url_token  = "https://account.accurate.id/oauth/authorize?"; // url authorize accurate
        url_token += "client_id=1f67b7ca-818c-4145-95b7-d01bb036b9a9"; // client_id diambil dari aplikasi yang kita buat tadi 
        url_token += "&response_type=token"; //kita gunakan token
        url_token += "&redirect_uri=http://localhost/api-accurate-master/token.php"; // berisi url calback sesuai dengan aplikasi yang sudah kita daftarkan di aplikasi 
        url_token += "&scope=item_view customer_save customer_view customer_delete branch_view branch_save branch_delete sales_invoice_save sales_invoice_view sales_receipt_save sales_receipt_view sales_receipt_delete department_view department_save department_delete item_view item_delete item_save employee_view employee_delete employee_save item_adjustment_save item_adjustment_view item_adjustment_delete"; // data apa yang mau kita ambil dilihat di area developer di menu "Daftar Api"

    window.location = url_token;

}

</script>