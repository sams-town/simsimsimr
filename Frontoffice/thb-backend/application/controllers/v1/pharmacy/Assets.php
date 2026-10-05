<?php defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Restserver\Libraries\REST_Controller;
class Assets extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/Purchase_order_model", "PurchaseOrder");
        $this->load->model("$this->directory/Purchase_order_model_item", "PurchaseOrderItem");
        $this->load->model("$this->directory/Catalog_model", "Catalog");
        $this->load->model("v1/setting/Transaction_signature_validation_model", "TransactionSignatureValidation");
        $this->load->model("$this->directory/Pharmacy_assets_model", "PharmacyAssets");
        $this->load->model("$this->directory/Mutation_model_item", "Mutation_item");
        $this->load->model("$this->directory/Receipt_model_item", "Receipt_item");
        $this->load->model("$this->directory/Prescription_item_model", "Prescription_item");
        $this->load->model("$this->directory/Pharmacy_assets_card_model", "PharmacyAssetsCard");
        $this->load->model("$this->directory/Pharmacy_assets_price_model", "PharmacyAssetsPrice");
        $this->load->model("v1/setting/Signature_validation_model", "SignatureValidation");
        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
    }

    /**
     * Planning API
     * --------------------
     * @param: pharmacy_depo_id
     * @param: type_doc_id
     * @param: no_document
     * @param: ntotal
     * @param: ndiscount
     * @param: nmargins
     * @param: nfinish
     * @param: date
     * --------------------
     * @method : POST
     * @link: version/mr/planning
     **/

    

     public function card_get($p = 0){
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $id     = $this->security->xss_clean($p);
        $model  = 'PharmacyAssetsCard';
    
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 
        if($q_strings['pharmacy_catalog_id'] == "" || $q_strings['pharmacy_catalog_id'] == null){
            $response_data = 'Error.';
            $this->REST_Return(404, $validateToken['message'] ,$response_data);
            return;
        }

        if($q_strings['start_date'] == "" || $q_strings['start_date'] == null){
            $response_data = 'Error.';
            $this->REST_Return(404, $validateToken['message'] ,$response_data);
            return;
        }
        if($q_strings['end_date'] == "" || $q_strings['end_date'] == null){
            $response_data = 'Error.';
            $this->REST_Return(404, $validateToken['message'] ,$response_data);
            return;
        }
    
        $where = $q_strings;
        
        if(isset($q_strings['limit'])) {
            $limit = $q_strings['limit'];
            $this->$model->limit = $limit; 
            unset($where['limit']);
        }

        if(isset($q_strings['offset'])) {
            $offset = $q_strings['offset'];
            $this->$model->offset = $offset; 
            unset($where['offset']);
        }

        if (isset($q_strings['pharmacy_depo_id'])) {
            $pharmacy_depo_id = $q_strings['pharmacy_depo_id'];
        }

        if (isset($q_strings['pharmacy_catalog_id'])) {
            $pharmacy_catalog_id = $q_strings['pharmacy_catalog_id'];
        }

        
        $flag = '';
        if(isset($q_strings['flag'])) {
            $flag = $q_strings['flag'];
            $where['a.flag'] = $flag;
            unset($where['flag']);
            $flag = "AND `flag` = '$flag'";
        }
    
        $start_date = date('Y-m-d') . ' 00:00:00';
        $end_date = date('Y-m-d') . ' 23:59:59';
    
        if(isset($q_strings['start_date']) && isset($q_strings['end_date'])) {
            $start_date = $q_strings['start_date'] . ' 00:00:00';
            $end_date = $q_strings['end_date'] . ' 23:59:59';
    
            unset($where['start_date']);
            unset($where['end_date']);
        } 

        $where['a.date_created >='] = $start_date;
        $where['a.date_created <='] = $end_date;
        // var_dump($where);die();
        $select = 'a.*,
        b.name as depo_name,
        c.name as catalog_name,
        p.name as package';
        // mds.name as depo_name_send,
        // mdr.name as depo_name_recive
        $join = array(
            array('pharmacy_depo b','a.pharmacy_depo_id = b.id','LEFT'),
            array('pharmacy_catalog c','a.pharmacy_catalog_id = c.id','LEFT'),
            array('package p','c.package_id = p.id','LEFT'),
            // array('pharmacy_mutation m','a.reff_id = m.id','INNER'),
            // array('pharmacy_depo mds','m.pharmacy_depo_send_id = mds.id','LEFT'),
            // array('pharmacy_depo mdr','m.pharmacy_depo_recive_id = mdr.id','LEFT'),
        );
        $order = array();
        // $order = array('a.date_created' => 'desc');
        // $output = $this->$model->select(0, $select, $where, $join, $order);

        if($pharmacy_depo_id && $pharmacy_catalog_id) {
            $output = $this->db->query("
                SELECT
                `full_a`.*, 
                `b`.`name` as `depo_name`, 
                `c`.`name` as `catalog_name`, 
                `p`.`name` as `package`
                FROM (
                    SELECT id, pharmacy_depo_id, pharmacy_catalog_id
                    FROM pharmacy_assets_card
                    WHERE `pharmacy_depo_id` = '$pharmacy_depo_id' AND
                    `pharmacy_catalog_id` = '$pharmacy_catalog_id' AND
                    `date_created` >= '$start_date'
                    AND `date_created` <= '$end_date' 
                    $flag
                    ORDER BY `date_created` DESC 
                    LIMIT $limit
                    OFFSET $offset
                ) as `a`
                LEFT JOIN `pharmacy_assets_card` `full_a` ON `a`.`id` = `full_a`.`id`
                LEFT JOIN `pharmacy_depo` `b` ON `a`.`pharmacy_depo_id` = `b`.`id`
                LEFT JOIN `pharmacy_catalog` `c` ON `a`.`pharmacy_catalog_id` = `c`.`id`
                LEFT JOIN `package` `p` ON `c`.`package_id` = `p`.`id`
            ")->result();
        } else if($pharmacy_depo_id) {
            $output = $this->db->query("
                SELECT
                `full_a`.*, 
                `b`.`name` as `depo_name`, 
                `c`.`name` as `catalog_name`, 
                `p`.`name` as `package`
                FROM (
                    SELECT id, pharmacy_depo_id, pharmacy_catalog_id
                    FROM pharmacy_assets_card
                    WHERE `pharmacy_depo_id` = '$pharmacy_depo_id' AND
                    `date_created` >= '$start_date'
                    AND `date_created` <= '$end_date' 
                    $flag
                    ORDER BY `date_created` DESC 
                    LIMIT $limit
                    OFFSET $offset
                ) as `a`
                LEFT JOIN `pharmacy_assets_card` `full_a` ON `a`.`id` = `full_a`.`id`
                LEFT JOIN `pharmacy_depo` `b` ON `a`.`pharmacy_depo_id` = `b`.`id`
                LEFT JOIN `pharmacy_catalog` `c` ON `a`.`pharmacy_catalog_id` = `c`.`id`
                LEFT JOIN `package` `p` ON `c`.`package_id` = `p`.`id`
            ")->result();
        
        } else if ($pharmacy_catalog_id) {
            $output = $this->db->query("
                SELECT
                `full_a`.*, 
                `b`.`name` as `depo_name`, 
                `c`.`name` as `catalog_name`, 
                `p`.`name` as `package`
                FROM (
                    SELECT id, pharmacy_depo_id, pharmacy_catalog_id
                    FROM pharmacy_assets_card
                    WHERE `pharmacy_catalog_id` = '$pharmacy_catalog_id' AND
                    `date_created` >= '$start_date'
                    AND `date_created` <= '$end_date' 
                    $flag
                    ORDER BY `date_created` DESC 
                    LIMIT $limit
                    OFFSET $offset
                ) as `a`
                LEFT JOIN `pharmacy_assets_card` `full_a` ON `a`.`id` = `full_a`.`id`
                LEFT JOIN `pharmacy_depo` `b` ON `a`.`pharmacy_depo_id` = `b`.`id`
                LEFT JOIN `pharmacy_catalog` `c` ON `a`.`pharmacy_catalog_id` = `c`.`id`
                LEFT JOIN `package` `p` ON `c`.`package_id` = `p`.`id`
            ")->result();
        } else {
            $output = $this->db->query("
                SELECT
                `full_a`.*, 
                `b`.`name` as `depo_name`, 
                `c`.`name` as `catalog_name`, 
                `p`.`name` as `package`
                FROM (
                    SELECT id, pharmacy_depo_id, pharmacy_catalog_id
                    FROM pharmacy_assets_card
                    WHERE `date_created` >= '$start_date'
                    AND `date_created` <= '$end_date' 
                    $flag
                    ORDER BY `date_created` DESC 
                    LIMIT $limit
                    OFFSET $offset
                ) as `a`
                LEFT JOIN `pharmacy_assets_card` `full_a` ON `a`.`id` = `full_a`.`id`
                LEFT JOIN `pharmacy_depo` `b` ON `a`.`pharmacy_depo_id` = `b`.`id`
                LEFT JOIN `pharmacy_catalog` `c` ON `a`.`pharmacy_catalog_id` = `c`.`id`
                LEFT JOIN `package` `p` ON `c`.`package_id` = `p`.`id`
            ")->result();
        }
        if ($output) {
            for ($i = 0; $i < count($output); $i++) {
                $output[$i]->in_amount = (int) $output[$i]->in_amount; 
                $output[$i]->out_amount = (int) $output[$i]->out_amount; 
                $output[$i]->prev_amount = (int) $output[$i]->prev_amount; 
                $output[$i]->current_amount = (int) $output[$i]->current_amount; 
                $output[$i]->flag = (int) $output[$i]->flag; 
                // SEARCH MUTATION
                if ($output[$i]->flag == 4) {
                    $where_m['a.pharmacy_mutation_id'] = $output[$i]->reff_id;
                    $where_m['a.pharmacy_catalog_id'] = $output[$i]->pharmacy_catalog_id;
                    $select_m = 'pm.no_document, mds.name as depo_name_send, mdr.name as depo_name_recive, e2.fullname as user_send, e3.fullname as user_recive';
                    $join_m = array(
                        array('pharmacy_mutation pm','a.pharmacy_mutation_id = pm.id','LEFT'),
                        array('user u2','pm.validation_key_2 = u2.id','LEFT'),
                        array('user u3','pm.validation_key_3 = u3.id','LEFT'),
                        array('employee e2','u2.employee_id = e2.id','LEFT'),
                        array('employee e3','u3.employee_id = e3.id','LEFT'),
                        array('pharmacy_depo mds','pm.pharmacy_depo_send_id = mds.id','LEFT'),
                        array('pharmacy_depo mdr','pm.pharmacy_depo_recive_id = mdr.id','LEFT'),
                    );
                    $order_m = array('a.pharmacy_mutation_id' => 'asc');
                    $output_m = $this->Mutation_item->select(0, $select_m, $where_m, $join_m, $order_m)[0];
                    if ($output[$i]->in_amount == 0) {
                        $output[$i]->flag_desc = $output_m->no_document ? 'Room Inventory Movement Asal : '.$output_m->depo_name_send.', Tujuan : '.$output_m->depo_name_recive.', User : '.$output_m->user_send.', Jam : '.date('H:i:s', strtotime($output[$i]->date_created)) : $output_m->flag;
                    } else if ($output[$i]->out_amount == 0) {
                        $output[$i]->flag_desc = $output_m->no_document ? 'Room Inventory Movement Asal : '.$output_m->depo_name_send.', Tujuan : '.$output_m->depo_name_recive.', User : '.$output_m->user_recive.', Jam : '.date('H:i:s', strtotime($output[$i]->date_created)) : $output_m->flag;
                    }
                }
                // SEARCH MUTATION
                // SEARCH RECEIPT
                if ($output[$i]->flag == 1) {
                    $where_r['a.receipt_id'] = $output[$i]->reff_id;
                    $where_r['a.pharmacy_catalog_id'] = $output[$i]->pharmacy_catalog_id;
                    $select_r = 'r.no_document, r.no_invoice, po.no_document as no_document_po, pd.name as depo_name, s.name as supplier_name, e.fullname';
                    $join_r = array(
                        array('receipts r','a.receipt_id = r.id','LEFT'),
                        array('company s','r.supplier_id = s.id','LEFT'),
                        array('purchase_order po','r.purchase_order_id = po.id','LEFT'),
                        array('user u','r.validation_key_1 = u.id','LEFT'),
                        array('employee e','u.employee_id = e.id','LEFT'),
                        array('pharmacy_depo pd','r.pharmacy_depo_id = pd.id','LEFT'),
                    );
                    $order_r = array('a.receipt_id' => 'asc');
                    $output_r = $this->Receipt_item->select(0, $select_r, $where_r, $join_r, $order_r)[0];
                    $output[$i]->flag_desc = $output_r->no_document ? 'Material Receipt : '.$output_r->no_document.', Supplier : '.$output_r->supplier_name.', No PO : '.$output_r->no_document_po.', FAKTUR : '.$output_r->no_invoice.', GUDANG : '.$output_r->depo_name.', User : '.$output_r->fullname.', Jam : '.date('H:i:s', strtotime($output[$i]->date_created)) : $output_r->flag;
                }
                // SEARCH RECEIPT
                // SEARCH RECEIPT RETUR
                if ($output[$i]->flag == 11) {
                    $where_r['a.receipt_id'] = $output[$i]->reff_id;
                    $where_r['a.pharmacy_catalog_id'] = $output[$i]->pharmacy_catalog_id;
                    $select_r = 'r.no_document, r.no_invoice, po.no_document as no_document_po, pd.name as depo_name, s.name as supplier_name, e.fullname';
                    $join_r = array(
                        array('receipts r','a.receipt_id = r.id','LEFT'),
                        array('company s','r.supplier_id = s.id','LEFT'),
                        array('purchase_order po','r.purchase_order_id = po.id','LEFT'),
                        array('user u','r.validation_key_1 = u.id','LEFT'),
                        array('employee e','u.employee_id = e.id','LEFT'),
                        array('pharmacy_depo pd','r.pharmacy_depo_id = pd.id','LEFT'),
                    );
                    $order_r = array('a.receipt_id' => 'asc');
                    $output_r = $this->Receipt_item->select(0, $select_r, $where_r, $join_r, $order_r)[0];
                    $output[$i]->flag_desc = $output_r->no_document ? 'Material Out : '.$output_r->no_document.', Supplier : '.$output_r->supplier_name.', No PO : '.$output_r->no_document_po.', FAKTUR : '.$output_r->no_invoice.', GUDANG : '.$output_r->depo_name.', User : '.$output_r->fullname.', Jam : '.date('H:i:s', strtotime($output[$i]->date_created)) : $output_r->flag;
                }
                // SEARCH RECEIPT RETUR
                // SEARCH PRESCRIPTION
                if ($output[$i]->flag == 3) {
                    $where_p['a.pharmacy_prescription_id'] = $output[$i]->reff_id;
                    $where_p['a.pharmacy_catalog_id'] = $output[$i]->pharmacy_catalog_id;
                    $select_p = 'pp.no_order, p.fullname as patient, pd.name as depo_name, e.fullname';
                    $join_p = array(
                        array('pharmacy_prescription pp','a.pharmacy_prescription_id = pp.id','LEFT'),
                        array('patient p','pp.norm = p.norm','LEFT'),
                        array('user u','pp.user_1 = u.id','LEFT'),
                        array('employee e','u.employee_id = e.id','LEFT'),
                        array('pharmacy_depo pd','pp.pharmacy_depo_id = pd.id','LEFT'),
                    );
                    $order_p = array('a.pharmacy_prescription_id' => 'asc');
                    $output_p = $this->Prescription_item->select(0, $select_p, $where_p, $join_p, $order_p)[0];
                    $output[$i]->flag_desc = $output_p->no_order ? 'Pemakaian Obat/Alkes No : '.$output_p->no_order.', Pasien A/N : '.$output_p->patient.', GUDANG : '.$output_p->depo_name.', User : '.$output_p->fullname.', Jam : '.date('H:i:s', strtotime($output[$i]->date_created)) : $output_p->flag;
                }
                // SEARCH PRESCRIPTION
            }
        } else {
            $this->REST_Return(200, 'NO DATA', []);
            return;
        }
        $this->$model->limit = null;
        $this->$model->offset = null;
        $select = 'count(*) as iTotal';
        // $iTotal = $this->$model->select(0, $select, $where, $join)[0]->iTotal;
        $iTotal = $this->db->query("
            SELECT COUNT(id) AS iTotal
            FROM pharmacy_assets_card
            WHERE `date_created` >= '$start_date'
            AND `date_created` <= '$end_date' 
            $flag
        ")->result()[0]->iTotal;

		$response_data = (object) array(
			'rows' => $output,
			'start' => ($offset + $limit),
			'iTotal' => $iTotal
		);
                
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function price_get($p = 0)
    {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        $validateToken = $this->authorization_token->validateToken();

        if(!empty($validateToken && $validateToken['status'] == TRUE)){
            // var_dump($q_strings['date_created']);
            // die();
            $institute_branch_id = $this->security->xss_clean($validateToken['data']->institute_branch_id);
            if(isset($q_strings['limit'])) $this->PharmacyAssetsPrice->limit = $q_strings['limit']; unset($q_strings['limit']);
            if(isset($q_strings['offset'])) $this->PharmacyAssetsPrice->offset = $q_strings['offset']; unset($q_strings['offset']);
            $start_date = '';
            $end_date = '';
            if(isset($q_strings['start_date'])){
                $start_date = $q_strings['start_date']; 
                $start_date .= ' 00:00:00';
                unset($q_strings['start_date']);
            }
            if(isset($q_strings['end_date'])){
                $end_date = $q_strings['end_date']; 
                $end_date .= ' 23:59:59';
                unset($q_strings['end_date']);
            }
            $this->PharmacyAssetsPrice->whereFields = $q_strings;
            if($start_date != ''){
                $this->PharmacyAssetsPrice->whereFields['a.date_created >='] = $start_date; 
                $this->PharmacyAssetsPrice->whereFields['a.date_created <='] = $end_date; 
            };

            $output = $this->PharmacyAssetsPrice->read_ssr();
            $response_data = array();
            // code here
            $response_data = $output;
            $this->REST_Return(200, 'SUCCESS', $response_data);    
        }else{
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $validateToken['message'] ,$response_data);
            return;
        }
    }

    public function price_ref_get($p = 0)
    {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        $validateToken = $this->authorization_token->validateToken();

        if(!empty($validateToken && $validateToken['status'] == TRUE)){
            // var_dump($q_strings['date_created']);
            // die();
            $institute_branch_id = $this->security->xss_clean($validateToken['data']->institute_branch_id);
            if(isset($q_strings['limit'])) $this->PharmacyAssetsPrice->limit = $q_strings['limit']; unset($q_strings['limit']);
            if(isset($q_strings['offset'])) $this->PharmacyAssetsPrice->offset = $q_strings['offset']; unset($q_strings['offset']);
            $q_strings['pharmacy_catalog_id'] = explode (",", $q_strings['pharmacy_catalog_id']); 
            // $q_strings['pharmacy_catalog_id'] = array_map('intval', explode(',', $loket->code));
            $this->PharmacyAssetsPrice->whereInFields['a.pharmacy_catalog_id'] = $q_strings['pharmacy_catalog_id']; 
            $this->PharmacyAssetsPrice->limit = 5;
            $output = $this->PharmacyAssetsPrice->read_ssr();
            $response_data = array();
            // code here
            $response_data = $output;
            $this->REST_Return(200, 'SUCCESS', $response_data);    
        }else{
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $validateToken['message'] ,$response_data);
            return;
        }
    }

    public function monitoring_get($p = 0)
    {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        $validateToken = $this->authorization_token->validateToken();

        if(!empty($validateToken && $validateToken['status'] == TRUE)){
            // var_dump($q_strings['date_created']);
            // die();
            $institute_branch_id = $this->security->xss_clean($validateToken['data']->institute_branch_id);
            if(isset($q_strings['limit'])) $this->PharmacyAssets->limit = $q_strings['limit']; unset($q_strings['limit']);
            if(isset($q_strings['offset'])) $this->PharmacyAssets->offset = $q_strings['offset']; unset($q_strings['offset']);
            $start_date = '';
            $end_date = '';

            if(isset($q_strings['start_date'])){
                $start_date = $q_strings['start_date']; 
                $start_date .= ' 00:00:00';
                unset($q_strings['start_date']);
            }
            if(isset($q_strings['end_date'])){
                $end_date = $q_strings['end_date']; 
                $end_date .= ' 23:59:59';
                unset($q_strings['end_date']);
            }
            $this->PharmacyAssets->whereFields = $q_strings;
            if($start_date != ''){
                $this->PharmacyAssets->whereFields['a.date_created >='] = $start_date; 
                $this->PharmacyAssets->whereFields['a.date_created <='] = $end_date; 
            };

            $output = $this->PharmacyAssets->read_ssr();
            $response_data = array();
            // code here
            $response_data = $output;
            $this->REST_Return(200, 'SUCCESS', $response_data);    
        }else{
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $validateToken['message'] ,$response_data);
            return;
        }
    }

    // public function excel_get($p = 0){
	// 	header("Access-Control-Allow-Origin: *");
    //     parse_str($_SERVER['QUERY_STRING'],$q_strings);
    //     $error  = true;
    //     $id     = $this->security->xss_clean($p);
    //     $model  = 'PharmacyAssets';

    //     $response_data = array();
    //     $spreadsheet = new Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();
    //     // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
    //     $style_col = [
    //         'font' => ['bold' => true], // Set font nya jadi bold
    //         'alignment' => [
    //         'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
    //         'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
    //         ],
    //         'borders' => [
    //         'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
    //         'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
    //         'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
    //         'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
    //         ]
    //     ];
    //     // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
    //     $style_row = [
    //         'alignment' => [
    //         'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
    //         ],
    //         'borders' => [
    //         'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
    //         'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
    //         'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
    //         'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
    //         ]
    //     ];
    //     $sheet->setCellValue('A1', $response_data[0]->name); // Set kolom A1 dengan tulisan "DATA "
    //     $sheet->mergeCells('A1:E1'); // Set Merge Cell pada kolom A1 sampai E1
    //     $sheet->getStyle('A1')->getFont()->setBold(true); // Set bold kolom A1
    //     // Buat header tabel nya pada baris ke 3
    //     $sheet->setCellValue('A3', "ID"); 
    //     $sheet->setCellValue('B3', "KATALOG"); 
    //     $sheet->setCellValue('C3', "STOK"); 
    //     // $sheet->setCellValue('D3', "NO FAKTUR");
    //     // $sheet->setCellValue('E3', "TOP"); 
    //     // $sheet->setCellValue('F3', "TANGGAL JT TEMPO"); 
    //     // $sheet->setCellValue('G3', "NILAI FAKTUR"); 
    //     // $sheet->setCellValue('H3', "PBF"); 
    //     $numrow = 4; // Set baris pertama untuk isi tabel adalah baris ke 4
    //     $this->$model->whereFields['a.pharmacy_depo_id'] = '8da5d967-18da-4f87-86dc-0a9416611c11';
    //     $output_opname_item = $this->$model->read(0,false);
    //     $no=1;
    //     foreach($output_opname_item as $data){ // Lakukan looping pada variabel 
    //         $sheet->setCellValue('A'.$numrow, $data->pharmacy_catalog_id);
    //         $sheet->setCellValue('B'.$numrow, $data->catalog_name);
    //         $sheet->setCellValue('C'.$numrow, $data->admin_amount);
    //         // $sheet->setCellValue('D'.$numrow, $data->no_invoice);
    //         // $sheet->setCellValue('E'.$numrow, $data->days);
    //         // $sheet->setCellValue('F'.$numrow, date('Y-m-d', strtotime($data->date. ' + '.$data->days.' day')));
    //         // $sheet->setCellValue('G'.$numrow, (int)$data->nfinish);
    //         // $sheet->setCellValue('H'.$numrow, $data->supplier_name);
    //         $no++;
    //         $numrow++;
    //     }
    //     $sheet->getDefaultRowDimension()->setRowHeight(-1);
    //     $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
    //     $sheet->setTitle('Monitoring Stok');
    //     $writer = new Xlsx($spreadsheet);
    //     header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //     header('Content-Disposition: attachment; filename="'.'Monitoring Stok'.'.xlsx"'); // Set nama file excel nya
    //     header('Cache-Control: max-age=0');
    //     $writer->save('php://output');
    //     // $this->REST_Return(200, 'SUCCESS', $response_data);
    //     return;
	// }
}