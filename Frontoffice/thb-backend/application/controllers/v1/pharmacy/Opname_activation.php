<?php defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Restserver\Libraries\REST_Controller;
class Opname_activation extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/Opname_model", "Opname");
        $this->load->model("$this->directory/Opname_item_report_model", "Opname_item");
    }

    

    public function excel_get($p = 0){
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 0); 
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        $id = '';
        if(isset($q_strings['id'])){
            $id = $q_strings['id']; 
            unset($q_strings['id']);
        }
        if($id != ''){
            $this->$model->whereFields['a.id'] = $id; 
        };
        $output = $this->$model->read();
        $response_data = array();
        if ($output != [] && $id != 0) {
            $response_data = $output;
            $error   = false;
        } else if($output != [] or $id == 0) {
            $response_data = $output;
            $error   = false;
        } else {
            $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
            return;
        }
        if ($output || $output == [] && $id == '0') {
            $response_data = $output;
            $error   = false;
        } else {
            $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
            return;
        }
        if ($error == true) {
            $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
        } else {
            // echo "<pre>";
            // var_dump($response_data[0]);
            // // foreach($response_data as $data){ // Lakukan looping pada variabel 
            // //     foreach($data->transactions as $data_detail){ // Lakukan looping pada variabel 
            // //         echo "<pre>";
            // //         var_dump($data_detail['costing_name']);
            // //         var_dump($data_detail['var_1']);
            // //     }
            // // }
            // die();
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
            $style_col = [
                'font' => ['bold' => true], // Set font nya jadi bold
                'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
                ],
                'borders' => [
                'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
                'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
                'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
                'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
                ]
            ];
            // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
            $style_row = [
                'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
                ],
                'borders' => [
                'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
                'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
                'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
                'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
                ]
            ];
            $sheet->setCellValue('A1', $response_data[0]->name); // Set kolom A1 dengan tulisan "DATA "
            $sheet->mergeCells('A1:E1'); // Set Merge Cell pada kolom A1 sampai E1
            $sheet->getStyle('A1')->getFont()->setBold(true); // Set bold kolom A1
            // Buat header tabel nya pada baris ke 3
            $sheet->setCellValue('A3', "LOKASI"); 
            $sheet->setCellValue('B3', "NO"); 
            $sheet->setCellValue('C3', "SO"); 
            $sheet->setCellValue('D3', "TIMESTAMP");
            $sheet->setCellValue('E3', "USER"); 
            $sheet->setCellValue('F3', "NAMA"); 
            $sheet->setCellValue('G3', "JENIS BARANG"); 
            $sheet->setCellValue('H3', "EXD"); 
            $sheet->setCellValue('I3', "HARGA BELI"); 
            $sheet->setCellValue('J3', "HARGA JUAL"); 
            $sheet->setCellValue('K3', "STOK AWAL"); 
            $sheet->setCellValue('L3', "JUMLAH FISIK"); 
            $sheet->setCellValue('M3', "SELISIH"); 
            $sheet->setCellValue('N3', "NILAI"); 
            $sheet->setCellValue('O3', "NOMER RAK"); 
            $sheet->setCellValue('P3', "NAMA RAK"); 
            $numrow = 4; // Set baris pertama untuk isi tabel adalah baris ke 4
            // $this->Opname->whereFields['a.pharmacy_activation_id'] = $response_data[0]->id; 
            // $output_opname = $this->Opname->read(0,false)[0];
            $this->Opname_item->whereFields['po.pharmacy_activation_id'] = $response_data[0]->id; 
            $this->Opname_item->whereFields['p.is_active'] = 1; 
            $output_opname_item = $this->Opname_item->read(0,false);
            // var_dump($output_opname_item);die();
            $no=1;
            foreach($output_opname_item as $data){ // Lakukan looping pada variabel 
                $sheet->setCellValue('A'.$numrow, $data->pharmacy_depo_name);
                $sheet->setCellValue('B'.$numrow, $no);
                $sheet->setCellValue('C'.$numrow, $response_data[0]->name);
                $sheet->setCellValue('D'.$numrow, date('d/m/Y H:i:s', strtotime($data->timestampt)));
                $sheet->setCellValue('E'.$numrow, $data->user_bak);
                $sheet->setCellValue('F'.$numrow, $data->catalog_name);
                $sheet->setCellValue('G'.$numrow, $data->category_name);
                $sheet->setCellValue('H'.$numrow, $data->expired_date);
                $sheet->setCellValue('I'.$numrow, $data->hna_item);
                $sheet->setCellValue('J'.$numrow, $data->hna_item*1.3);
                $sheet->setCellValue('K'.$numrow, $data->sum_adm);
                $sheet->setCellValue('L'.$numrow, $data->accumulation);
                $sheet->setCellValue('M'.$numrow, $data->accumulation-$data->sum_adm);
                $sheet->setCellValue('N'.$numrow, $data->accumulation*$data->hna_item);
                $sheet->setCellValue('O'.$numrow, $data->name_rack);
                $sheet->setCellValue('P'.$numrow, $data->name_rackname);
                $no++;
                $numrow++;
            }
            $sheet->getDefaultRowDimension()->setRowHeight(-1);
            $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $sheet->setTitle($response_data[0]->name);
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="'.$response_data[0]->name.'.xlsx"'); // Set nama file excel nya
            header('Cache-Control: max-age=0');
            $writer->save('php://output');
            // $this->REST_Return(200, 'SUCCESS', $response_data);
        }
        return;
	}
}