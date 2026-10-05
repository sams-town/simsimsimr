<?php defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Restserver\Libraries\REST_Controller;
class Catalog extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_supplier_model", "{$this->name}_supplier");
        $this->load->model("$this->directory/{$this->name}_instruction_model", "{$this->name}_instruction");
        $this->load->model("$this->directory/Generik_model", "Generik");
        $this->load->model("$this->directory/Pharmacy_assets_model", "Assets");
        $this->load->model("$this->directory/Pharmacy_assets_price_model", "PharmacyAssetsPrice");
        $this->load->model("$this->directory/Pharmacy_catalog_signatura_model", "Pharmacy_catalog_signatura");
        $this->load->model("$this->directory/Signatura_times_model", "Signatura_times");
        $this->load->model("v1/setting/Package_model", "Package");
        $this->load->model("v1/setting/Dose_model", "Dose");
        $this->load->model("v1/setting/Unit_model", "Unit");
        $this->load->model("v1/third_party/Category_catalog_model", "Category_catalog");
        $this->load->model("v1/third_party/Brand_model", "Brand");
        $this->load->model("v1/third_party/Company_model", "Company");
        // Load Helper
        $this->load->helper('logger');

        $this->update_rules = array(
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|required'),
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );

        $this->update_instruction_rules = array(
            array('field' => 'pharmacy_catalog_instruction_id', 'label' => 'pharmacy_catalog_instruction_id', 'rules' => 'trim|required|max_length[36]'),
        );
    }

    /**
     * Catalog API
     * --------------------
     * @param: code
     * @param: name
     * @param: name_brand
     * @param: composition
     * @param: category_catalog_id
     * @param: generik_id
     * @param: package_id
     * @param: value_package
     * @param: package_big_id
     * @param: value_package_big
     * @param: suppliers_id
     * @param: factory_id
     * @param: dose_id
     * @param: dose
     * @param: price
     * @param: min
     * @param: max
     * --------------------
     * @method : POST
     * @link: version/mr/catalog
     **/

    public function create_post() {
        // CALL JSON POST
        $data       = $this->json_post();
        // DEFINE ATTR
        $dt_post    = $data['dt_postput'];
        $device     = $data['dt_server']['HTTP_USER_AGENT'];
        $ip         = $this->get_client_ip();
        $error      = TRUE;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            # Form Validation
            $this->form_validation->set_rules('code', 'code', 'trim|max_length[10]');
            $this->form_validation->set_rules('name', 'name', 'trim|max_length[150]');
            $this->form_validation->set_rules('name_brand', 'name_brand', 'trim|max_length[150]');
            $this->form_validation->set_rules('composition', 'composition', 'trim|max_length[100]');
            $this->form_validation->set_rules('category_catalog_id', 'category_catalog_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('generik_id', 'generik_id', 'trim|required|max_length[36]');
            // $this->form_validation->set_rules('package_id', 'package_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('value_package', 'value_package', 'trim|max_length[13]');
            // $this->form_validation->set_rules('package_big_id', 'package_big_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('value_package_big', 'value_package_big', 'trim|max_length[13]');
            // $this->form_validation->set_rules('suppliers_id', 'suppliers_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('factory_id', 'factory_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('dose_id', 'dose_id', 'trim|max_length[36]');
            $this->form_validation->set_rules('dose', 'dose', 'trim|max_length[13]');
            $this->form_validation->set_rules('price', 'price', 'trim|max_length[13]');
            $this->form_validation->set_rules('min', 'min', 'trim|max_length[13]');
            $this->form_validation->set_rules('max', 'max', 'trim|max_length[13]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                // Check generik_id & package_id & dose_id & category_catalog_id & package_big_id & suppliers_id & factory_id
                if(
                    // $this->Generik->read($dt_post['generik_id'], false) != [] && 
                    // $this->Package->read($dt_post['package_id'], false) != [] && 
                    // $this->Dose->read($dt_post['dose_id'], false) != [] && 
                    $this->Category_catalog->read($dt_post['category_catalog_id'], false) != [] &&  
                    $this->Company->read($dt_post['factory_id'], false) != []) {
                    $model = $this->name;
                    /* Start Transaction */
                    $this->db->trans_start();
                    
                    $this->load->library("transaction");
                    $code = preg_split('/[\s,]+/', $this->Category_catalog->read($dt_post['category_catalog_id'], false)[0]->name, 3);
                    $code_first='';$code_two='';$code_third='';
                    if ($code[0]) {
                        $code_first = mb_substr($code[0], 0, 1);
                    }
                    if ($code[1]) {
                        $code_two = mb_substr($code[1], 0, 1);
                    }
                    if ($code[2]) {
                        $code_third = mb_substr($code[2], 0, 1);
                    }
                    $code_gen = $code_first.$code_two.$code_third;
                    $codecounter = array(
                        'var_1' => $dt_post['category_catalog_id'],
                        'var_2' => $code_gen
                    );
                    $checkcounter = $this->transaction->counter($codecounter);
                    $code = $codecounter['var_2'].str_pad($checkcounter['counter_queue'], 5, "0", STR_PAD_LEFT);
                    if ($checkcounter['counter_queue'] == '0') {
                        $this->Counter_queue->replace(array(
                            'var_1' => $dt_post['category_catalog_id'],
                            'var_2' => $code_gen,
                            'counter_queue' => 0
                        ));
                        $checkcounter = $this->transaction->counter($codecounter);
                        $code = $codecounter['var_2'].str_pad($checkcounter['counter_queue'], 5, "0", STR_PAD_LEFT);
                    }
                    $dt_catalog = array(
                        'institute_branch_id'   => $institute_branch_id,
                        'code'                  => $code,
                        // 'code'               => isset($dt_post['code']) ? $dt_post['code'] : '',
                        'name'                  => isset($dt_post['name']) ? $dt_post['name'] : '',
                        'name_brand'            => isset($dt_post['name_brand']) ? $dt_post['name_brand'] : '',
                        'composition'           => isset($dt_post['composition']) ? $dt_post['composition'] : '',
                        'category_catalog_id'   => $dt_post['category_catalog_id'],
                        'is_generik'            => $dt_post['generik_id'],
                        'is_antibiotics'        => $dt_post['is_antibiotics'],
                        'dose_id'               => $dt_post['dose_unit_id'],
                        'dose_unit_id'          => $dt_post['dose_unit_id'],
                        'packaging_value'       => $dt_post['packaging_value'],
                        // 'dose_doctor'           => $dt_post['dose_doctor'],
                        'packaging_unit_id'     => $dt_post['packaging_unit_id'],
                        'signatura_dose'        => $dt_post['signatura_dose'],
                        'signatura_unit_id'     => $dt_post['signatura_unit_id'],
                        'signatura_times_id'    => $dt_post['signatura_times_id'],
                        'pharmacy_catalog_instruction_id'    => $dt_post['pharmacy_catalog_instruction_id'],
                        'package_id'            => $dt_post['package_id'],
                        // 'package_id'            => $dt_post['supplier'][0]['package_id'],
                        'value_package'         => $dt_post['supplier'][0]['value_package'],
                        'package_big_id'        => $dt_post['supplier'][0]['package_big_id'],
                        'value_package_big'     => $dt_post['supplier'][0]['value_package_big'],
                        'suppliers_id'          => $dt_post['supplier'][0]['suppliers_id'],
                        'factory_id'            => $dt_post['factory_id'],
                        'price'                 => isset($dt_post['price']) ? $dt_post['price'] : '',
                        'min'                   => isset($dt_post['min']) ? $dt_post['min'] : '',
                        'max'                   => isset($dt_post['max']) ? $dt_post['max'] : '',
                        'date_added'            => date('Y-m-d H:i:s')
                    );
                    $id = $this->$model->create($dt_catalog);
                    if ($id) {
                        for ($i=0; $i < count($dt_post['supplier']); $i++) { 
                            $this->Catalog_supplier->replace(array(
                                'id'                    => $id,
                                'suppliers_id'          => $dt_post['supplier'][$i]['suppliers_id'],
                                'package_id'            => $dt_post['supplier'][$i]['package_id'],
                                'value_package'         => $dt_post['supplier'][$i]['value_package'],
                                'package_big_id'        => $dt_post['supplier'][$i]['package_big_id'],
                                'value_package_big'     => $dt_post['supplier'][$i]['value_package_big'],
                                'date_added'            => date('Y-m-d H:i:s')
                            ));
                        }
                    }
                    // if ($id) {
                    //     $dt_pharmacy_asset_price = array(
                    //         'pharmacy_depo_id' => '8da5d967-18da-4f87-86dc-0a9416611c11',
                    //         'pharmacy_catalog_id' => $id,
                    //         'package_id' => $dt_post['package_id'],
                    //         'hna_item' => $dt_post['price']*1.3,
                    //         'hp_item' => $dt_post['price'],
                    //         'hja_item' => $dt_post['price']*1.3,
                    //         'expired_date' => NULL,
                    //         'date_created' => date('Y-m-d H:i:s'),
                    //         'is_active' => 1
                    //     );
                    //     $this->PharmacyAssetsPrice->create($dt_pharmacy_asset_price);
                    // }
                    $this->db->trans_complete();
                    /* End Transaction */
                    if ($this->db->trans_status() === false) {
                        $response_data = $this->db->trans_status();
                        $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                    } else {
                        $response_data = array(
                            'id'     => $id
                        );
                        $this->REST_Return(201, 'SUCCESS', $response_data);
                    }
                } else {
                    $this->REST_Return(404, 'Wrong brand_id or package_id or package_big_id or suppliers_id or factory_id!');
                }
                return;     
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    public function update_put($p = 0) {
        // CALL JSON POST
        $data       = $this->json_put();
        // DEFINE ATTR
        $dt_put     = $data['dt_postput'];
        $model      = $this->name;
        $id         = $this->security->xss_clean($p);

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 

        // Form Validation
        $this->form_validation->set_data($dt_put);
        $this->form_validation->set_rules($this->update_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $catalog_id = $id;

        if ($catalog_id === 0 || $catalog_id === "0") {
            $this->REST_Return(400, 'catalog_id is required');
            return;
        }

        $select = 'a.id';
        $catalog = $this->$model->select($catalog_id, $select);
        if (count($catalog) == 0) {
            $this->REST_Return(404, 'there is no catalog with id ' . $catalog_id);
            return;
        }

        $category_catalog_id = $dt_put['category_catalog_id'];
        $packaging_unit_id = $dt_put['packaging_unit_id'];
        $dose_unit_id = $dt_put['dose_unit_id'];
        $is_antibiotics = $dt_put['is_antibiotics'];
        $signatura_unit_id = $dt_put['signatura_unit_id'];
        $signatura_times_id = $dt_put['signatura_times_id'];
        $pharmacy_catalog_instruction_id = $dt_put['pharmacy_catalog_instruction_id'];
        $package_id = $dt_put['package_id'];
        $package_big_id = $dt_put['package_big_id'];
        $suppliers_id = $dt_put['suppliers_id'];
        $factory_id = $dt_put['factory_id'];

        $select = 'a.id';
        $category_catalog = $this->Category_catalog->select($category_catalog_id, $select);
        if (count($category_catalog) == 0) {
            $this->REST_Return(404, 'there is no category_catalog with id ' . $category_catalog_id);
            return;
        }

        $select = 'a.id';
        $unit = $this->Unit->select($packaging_unit_id, $select);
        if (count($unit) == 0) {
            $this->REST_Return(404, 'there is no unit with id ' . $packaging_unit_id);
            return;
        }

        $select = 'a.id';
        $unit = $this->Unit->select($dose_unit_id, $select);
        if (count($unit) == 0) {
            $this->REST_Return(404, 'there is no unit with id ' . $dose_unit_id);
            return;
        }

        $select = 'a.id';
        $unit = $this->Unit->select($signatura_unit_id, $select);
        if (count($unit) == 0) {
            $this->REST_Return(404, 'there is no unit with id ' . $signatura_unit_id);
            return;
        }

        $select = 'a.id';
        $signatura_times = $this->Signatura_times->select($signatura_times_id, $select);
        if (count($signatura_times) == 0) {
            $this->REST_Return(404, 'there is no signatura_times with id ' . $signatura_times_id);
            return;
        }

        $select = 'a.id';
        $pharmacy_catalog_instruction = $this->Catalog_instruction->select($pharmacy_catalog_instruction_id, $select);
        if (count($pharmacy_catalog_instruction) == 0) {
            $this->REST_Return(404, 'there is no pharmacy_catalog_instruction with id ' . $pharmacy_catalog_instruction_id);
            return;
        }

        $select = 'a.id';
        $package = $this->Package->select($package_id, $select);
        if (count($package) == 0) {
            $this->REST_Return(404, 'there is no package with id ' . $package_id);
            return;
        }

        $select = 'a.id';
        $package = $this->Package->select($package_big_id, $select);
        if (count($package) == 0) {
            $this->REST_Return(404, 'there is no package with id ' . $package_big_id);
            return;
        }

        $select = 'a.id';
        $suppliers = $this->Company->select($suppliers_id, $select);
        if (count($suppliers) == 0) {
            $this->REST_Return(404, 'there is no suppliers with id ' . $suppliers_id);
            return;
        }

        $select = 'a.id';
        $factory = $this->Company->select($factory_id, $select);
        if (count($factory) == 0) {
            $this->REST_Return(404, 'there is no factory with id ' . $factory_id);
            return;
        }
        
        // $code = $dt_put['code'];
        $name = $dt_put['name'];
        // $name_brand = $dt_put['name_brand'];
        $composition = $dt_put['composition'];
        $dose = $dt_put['dose'];
        // $dose_id = $dt_put['dose_id'];
        // $dose_doctor = $dt_put['dose_doctor'];
        $packaging_value = $dt_put['packaging_value'];
        $signatura_dose = $dt_put['signatura_dose'];
        $value_package = $dt_put['value_package'];
        $value_package_big = $dt_put['value_package_big'];
        $min = $dt_put['min'];
        $max = $dt_put['max'];
        $price = $dt_put['price'];
        // $is_generik = $dt_put['generik_id'];
        $is_chronic = $dt_put['is_chronic'];
        $is_executive = $dt_put['is_executive'];
        $supplier = $dt_put['supplier'];

        /* Start Transaction */
        $this->db->trans_start();

        $dt_catalog = array(
            // 'code'                              => $code,
            'name'                              => $name,
            // 'name_brand'                        => $name_brand,
            'composition'                       => $composition,
            'category_catalog_id'               => $category_catalog_id,
            // 'dose_id'                           => $dose_id,
	        'dose'                              => $dose,
            'dose_unit_id'                      => $dose_unit_id,
            'is_antibiotics'                    => $is_antibiotics,
            // 'dose_doctor'                       => $dose_doctor,
            'packaging_value'                   => $packaging_value,
            'packaging_unit_id'                 => $packaging_unit_id,
            'signatura_dose'                    => $signatura_dose,
            'signatura_unit_id'                 => $signatura_unit_id,
            'signatura_times_id'                => $signatura_times_id,
            'pharmacy_catalog_instruction_id'   => $pharmacy_catalog_instruction_id,
            'package_id'                        => $package_id,
            'package_big_id'                    => $package_big_id,
            'value_package'                     => $value_package,
            'value_package_big'                 => $value_package_big,
            'suppliers_id'                      => $suppliers_id,
            'factory_id'                        => $factory_id,
            'min'                               => $min,
            'max'                               => $max,
            // 'price'                             => $price,
            // 'is_generik'                        => $is_generik,
            'is_chronic'                        => $is_chronic,
	        'is_executive'                      => $is_executive,
        );

        // Nadira Gustarani
        if ($user_id == '593e8a82-a9c4-40fe-a224-fb2dc209a88e') {
            $dt_catalog['price'] = $price;
        }

        $this->$model->update_v2($catalog_id, $dt_catalog);

        for ($i=0; $i < count($supplier); $i++) { 
            $supplier_suppliers_id = $supplier[$i]['suppliers_id'];
            $supplier_package_id = $supplier[$i]['package_id'];
            $supplier_value_package = $supplier[$i]['value_package'];
            $supplier_package_big_id = $supplier[$i]['package_big_id'];
            $supplier_value_package_big = $supplier[$i]['value_package_big'];

            $dt_catalog_supplier = array(
                'id'                => $catalog_id,
                'suppliers_id'      => $supplier_suppliers_id,
                'package_id'        => $supplier_package_id,
                'value_package'     => $supplier_value_package,
                'package_big_id'    => $supplier_package_big_id,
                'value_package_big' => $supplier_value_package_big,
                'date_added'        => date('Y-m-d H:i:s')
            );

            $select = 'a.id';
            $where = array(
                'a.id' => $catalog_id,
                'a.suppliers_id' => $supplier_suppliers_id,
            );
            $catalog_supplier = $this->Catalog_supplier->select(0, $select, $where);
            if (count($catalog_supplier) > 0) {
                unset($dt_catalog_supplier['date_added']);
            }

            $this->Catalog_supplier->replace($dt_catalog_supplier);
        }

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_catalog,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update catalog', $response_data);
            return;
        }

        $response_data = array(
            'id'     => $id
        );

        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    /**
     * Catalog API
     * --------------------
     * @param: code
     * @param: name
     * @param: name_brand
     * @param: brand_id
     * @param: package_id
     * @param: value_package
     * @param: package_big_id
     * @param: value_package_big
     * @param: suppliers_id
     * @param: factory_id
     * @param: min
     * @param: max
     * --------------------
     * @method : POST
     * @link: version/mr/catalog
     **/

    public function update_backup_put($p = 0) {
        $id         = $this->security->xss_clean($p);
        // CALL JSON POST
        $data       = $this->json_put();
        // DEFINE ATTR
        $dt_put     = $data['dt_postput'];
        $device     = $data['dt_server']['HTTP_USER_AGENT'];
        $ip         = $this->get_client_ip();
        $error      = TRUE;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            # Form Validation
            $this->form_validation->set_data($dt_put);
            $this->form_validation->set_rules('code', 'code', 'trim|max_length[10]');
            $this->form_validation->set_rules('name', 'name', 'trim|max_length[100]');
            $this->form_validation->set_rules('name_brand', 'name_brand', 'trim|max_length[100]');
            // $this->form_validation->set_rules('brand_id', 'brand_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('package_id', 'package_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('value_package', 'value_package', 'trim|max_length[13]');
            $this->form_validation->set_rules('package_big_id', 'package_big_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('value_package_big', 'value_package_big', 'trim|max_length[13]');
            $this->form_validation->set_rules('suppliers_id', 'suppliers_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('factory_id', 'factory_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('min', 'min', 'trim|max_length[13]');
            $this->form_validation->set_rules('max', 'max', 'trim|max_length[13]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                $catalog = $this->$model->read($id);
                if($catalog != []) {
                    // $this->PharmacyAssetsPrice->whereFields['a.pharmacy_depo_id'] = '8da5d967-18da-4f87-86dc-0a9416611c11';
                    // $this->PharmacyAssetsPrice->whereFields['a.pharmacy_catalog_id'] = $id;
                    // $this->PharmacyAssetsPrice->whereFields['a.is_active'] = 1;
                    // $id_price = $this->PharmacyAssetsPrice->read()[0]->id;
                    $dt_catalog = array(
                        'id'                    => $id,
                        // 'code'                  => isset($dt_put['code']) ? $dt_put['code'] : $catalog[0]->code,
                        'name'                  => isset($dt_put['name']) ? $dt_put['name'] : $catalog[0]->name,
                        'name_brand'            => isset($dt_put['name_brand']) ? $dt_put['name_brand'] : $catalog[0]->name_brand,
                        'composition'           => isset($dt_put['composition']) ? $dt_put['composition'] : '',
                        'category_catalog_id'   => $dt_put['category_catalog_id'],
                        'dose_id'               => $dt_put['dose_id'],
                        'dose_unit_id'          => $dt_put['dose_unit_id'],
                        'packaging_value'       => $dt_put['packaging_value'],
                        'dose_doctor'           => $dt_put['dose_doctor'],
                        'packaging_unit_id'     => $dt_put['packaging_unit_id'],
                        'signatura_dose'        => $dt_put['signatura_dose'],
                        'signatura_unit_id'     => $dt_put['signatura_unit_id'],
                        'signatura_times_id'    => $dt_put['signatura_times_id'],
                        'pharmacy_catalog_instruction_id'    => $dt_put['pharmacy_catalog_instruction_id'],
                        'package_id'            => $dt_put['package_id'],
                        'price'                 => $dt_put['price'],
                        'value_package'         => isset($dt_put['value_package']) ? $dt_put['value_package'] : '',
                        'package_big_id'        => $dt_put['package_big_id'],
                        'value_package_big'     => isset($dt_put['value_package_big']) ? $dt_put['value_package_big'] : '',
                        'suppliers_id'          => $dt_put['suppliers_id'],
                        'factory_id'            => $dt_put['factory_id'],
                        'min'                   => isset($dt_put['min']) ? $dt_put['min'] : $catalog[0]->min,
                        'max'                   => isset($dt_put['max']) ? $dt_put['max'] : $catalog[0]->max,
                    );
                    if ($id) {
                        for ($i=0; $i < count($dt_put['supplier']); $i++) { 
                            $this->Catalog_supplier->replace(array(
                                'id'                    => $id,
                                'suppliers_id'          => $dt_put['supplier'][$i]['suppliers_id'],
                                'package_id'            => $dt_put['supplier'][$i]['package_id'],
                                'value_package'         => $dt_put['supplier'][$i]['value_package'],
                                'package_big_id'        => $dt_put['supplier'][$i]['package_big_id'],
                                'value_package_big'     => $dt_put['supplier'][$i]['value_package_big'],
                                'date_added'            => date('Y-m-d H:i:s')
                            ));
                        }
                    }
                    $this->$model->update($id, $dt_catalog);
                    // if ($id_price) {
                    //     $dt_pharmacy_asset_price = array(
                    //         'id' => $id_price,
                    //         'hna_item' => $dt_put['price']*1.3,
                    //         'hp_item' => $dt_put['price'],
                    //         'hja_item' => $dt_put['price']*1.3,
                    //         'is_active' => 1
                    //     );
                    //     $this->PharmacyAssetsPrice->replace($dt_pharmacy_asset_price);
                    // } else {
                    //     $dt_pharmacy_asset_price = array(
                    //         'pharmacy_depo_id' => '8da5d967-18da-4f87-86dc-0a9416611c11',
                    //         'pharmacy_catalog_id' => $id,
                    //         'package_id' => $dt_put['package_id'],
                    //         'hna_item' => $dt_put['price']*1.3,
                    //         'hp_item' => $dt_put['price'],
                    //         'hja_item' => $dt_put['price']*1.3,
                    //         'expired_date' => NULL,
                    //         'date_created' => date('Y-m-d H:i:s'),
                    //         'is_active' => 1
                    //     );
                    //     $this->PharmacyAssetsPrice->create($dt_pharmacy_asset_price);
                    // }
                } else {
                    $this->REST_Return(404, 'Catalog with id ' . $id . ' does not exist!');
                    return;
                }
                $this->db->trans_complete();
                /* End Transaction */
                if ($this->db->trans_status() === false) {
                    $response_data = array();
                    $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                } else {
                    $response_data = $id;
                    $this->REST_Return(200, 'SUCCESS', $response_data);
                }
                return;     
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    public function bride_accurate($url = null,$param = null) {
        $this->load->model("v1/setting/Accurate_bride_model", "Accurate_bride");
        $dataBride = $this->Accurate_bride->read()[0];
        $url_acc   = 'https://zeus.accurate.id/accurate/';
        $sessionid = $dataBride->sessionid;
        $token = $dataBride->token;
        $ch = curl_init($url_acc.$url);
        $authorization   = array();
        $authorization[] = "Authorization:Bearer ".$token; 
        $authorization[] = "X-Session-ID:".$sessionid; 
        if($param !== null){
            $authorization[] = "Content-Type: application/json"; 
            curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $authorization ); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch); 
        return json_decode($result,true); 
    }

    // public function adjust_get($p = 0){
	// 	header("Access-Control-Allow-Origin: *");
    //     parse_str($_SERVER['QUERY_STRING'],$q_strings);
    //     $error  = true;
    //     $id     = $this->security->xss_clean($p);
    //     $model  = $this->name;
    //     $this->Assets->whereFields['a.'.'pharmacy_depo_id'] = '822d71e8-7a2b-4fc6-a372-40a6b458d8c3';
    //     $output = $this->Assets->read();
    //     $detailItem = array();
    //     // for ($i = 0; $i < 3; $i++) {
    //     $no = 0;
    //     for ($i = 1; $i < count($output); $i++) {
    //         if ($output[$i]->bride_code !== 0 && $output[$i]->bride_code !== NULL && $output[$i]->admin_amount > 0 && $output[$i]->price !== 0) {
    //             $detailItem[$no] = array(
    //                 'itemAdjustmentType' => 'ADJUSTMENT_IN',
    //                 'itemNo' => $output[$i]->bride_code,
    //                 'unitCost' => $output[$i]->price,
    //                 'quantity' => $output[$i]->admin_amount,
    //                 'warehouseName' => $output[$i]->depo_name
    //             );
    //             $no++;
    //         }
    //     }
    //     $json_body =array('detailItem' =>$detailItem,'transDate' => date('d/m/Y'));
    //     // echo "<pre>";
    //     // var_dump($json_body);
    //     // echo "</pre>";
    //     // die();
    //     // $return_bride = $this->bride_accurate('api/department/delete.do?id='.$output[$i]->bride_id, json_encode($json_body));
    //     $return_bride = $this->bride_accurate('api/item-adjustment/save.do', json_encode($json_body));
    //     // // var_dump($return_bride['r']['parent']['id']);
    //     // // die();
    //     if ($return_bride['s'] == true) {
    //         $this->REST_Return(200, 'Success',$json_body);
    //         return;
    //     } else {
    //         var_dump($return_bride);
    //         $this->REST_Return(404, 'Failed Accurate = ',$return_bride);
    //         return;
    //     }
	// }

    public function list_get($p = 0){
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 

        $limit = $q_strings['limit'] ?? null;
        $offset = $q_strings['offset'] ?? null;

        $this->$model->limit = $limit; 
        $this->$model->offset = $offset; 
        
        if(isset($q_strings['limit']) || isset($q_strings['offset'])) {
            unset($q_strings['limit']);
            unset($q_strings['offset']);
        }

        $name = $q_strings['name'] ?? null;        
        $code = $q_strings['code'] ?? null;        

        $select = 'a.*,sd.name as signatura_unit_name, st.name as signatura_times_name,g.name as generik_name,ci.name as category_name,cin.name as category_insurance_name,c.name as package_name,d.name as package_big_name, ds.name as dose_name, e.name as suppliers_name,f.name as factory_name,cc.name as category_catalog_name, pcs.signatura, pcs.morning, pcs.afternoon, pcs.evening, pcs.measure, pcs.how_to_use, pci.name as pharmacy_catalog_instruction_name, u.name as dose_unit_name, u2.name as packaging_unit_name';
        $where = array(
            'a.is_delete' => 0,
        );
        if ($code != null) {
            $where['a.code'] = $code;
        }
        $join = array(
            array('helper g', 'a.is_generik = g.code AND g.variable="type_generik_p"', 'LEFT'),
            array('helper ci', 'a.category_id = ci.code AND ci.variable="type_category_p"', 'LEFT'),
            array('helper cin', 'a.category_insurance_id = cin.code AND cin.variable="type_cat_insur_p"', 'LEFT'),
            array('category_catalog cc', 'a.category_catalog_id = cc.id', 'LEFT'),
            array('package c', 'a.package_id = c.id', 'LEFT'),
            array('package ds', 'a.dose_id = ds.id', 'LEFT'),
            array('package d', 'a.package_big_id = d.id', 'LEFT'),
            array('company e', 'a.suppliers_id = e.id', 'LEFT'),
            array('company f', 'a.factory_id = f.id', 'LEFT'),
            array('pharmacy_catalog_signatura pcs', 'a.id = pcs.pharmacy_catalog_id', 'LEFT'),
            array('pharmacy_catalog_instruction pci', 'a.pharmacy_catalog_instruction_id = pci.id', 'LEFT'),
            array('unit u', 'a.dose_unit_id = u.id', 'LEFT'),
            array('unit u2', 'a.packaging_unit_id = u2.id', 'LEFT'),
            array('unit sd', 'a.signatura_unit_id = sd.id', 'LEFT'),
            array('signatura_times st', 'a.signatura_times_id = st.id', 'LEFT'),
        );
        $order = array(
            'a.name' => 'ASC',
        );
        if ($name != null) {
            $select_name = 'a.id';
            $where_name = array(
                'a.is_delete' => 0,
            );
            $join_name = 'empty_join';
            $this->$model->whereLike['a.name'] = $name;
            $output_name_ids = $this->$model->select(0, $select_name, $where_name, $join_name);

            unset($this->$model->whereLike['a.name']);
            $this->$model->whereLike['a.composition'] = $name;
            $output_composition_ids = $this->$model->select(0, $select_name, $where_name, $join_name);

            $catalog_ids = [];

            for ($i=0; $i < count($output_name_ids); $i++) { 
                $output_name_id = $output_name_ids[$i]->id;
                if (!in_array($output_name_id, $catalog_ids)) {
                    array_push($catalog_ids, $output_name_id);
                }
            }

            for ($i=0; $i < count($output_composition_ids); $i++) { 
                $output_composition_id = $output_composition_ids[$i]->id;
                if (!in_array($output_composition_id, $catalog_ids)) {
                    array_push($catalog_ids, $output_composition_id);
                }
            }

            $this->$model->whereLike = [];
            $this->$model->whereInFields['a.id'] = $catalog_ids;
            $output = $this->$model->select(0, $select, $where, $join, $order);
        } else {
            $output = $this->$model->select(0, $select, $where, $join, $order);
        }

        for ($i = 0; $i < count($output); $i++) {
            // SEARCH LAST RECEIVE ITEM/LASTFAKTUR
            $where_s['a.id'] = $output[$i]->id;
            $select_s = 'a.*, c.name as package_name,d.name as package_big_name, e.name as suppliers_name';
            $join_s = array(
                array('package c', 'a.package_id = c.id', 'LEFT'),
                array('package d', 'a.package_big_id = d.id', 'LEFT'),
                array('company e', 'a.suppliers_id = e.id', 'LEFT'),
            );
            $order_s = array('e.name' => 'asc');
            $supplier_data = $this->Catalog_supplier->select(0, $select_s, $where_s, $join_s, $order_s);
            // SEARCH LAST RECEIVE ITEM/LASTFAKTUR
            $output[$i]->supplier = $supplier_data;
        }

        $this->$model->limit = null;
        $this->$model->offset = null;
        $select = 'count(*) as iTotal';
        $iTotal = $this->$model->select(0, $select, $where)[0]->iTotal;   

        $response_data = (object) array(
            'rows' => $output,
            'start' => ($offset + $limit),
            'iTotal' => $iTotal
        );
        
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function list_backup_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
            if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
            if(isset($q_strings['name'])) $this->$model->whereLike['a.name'] = $q_strings['name']; unset($q_strings['name']);
            $this->$model->whereFields['a.is_delete'] = '0';
            $output = $this->$model->read_ssr($id);
            $response_data = array();
            if ($output->rows != [] && $id != 0) {
                $response_data = $output->rows[0];
                $error   = false;
            } else if($output->rows != [] or $id == 0) {
                for ($i = 0; $i < count($output->rows); $i++) {
                    // SEARCH LAST RECEIVE ITEM/LASTFAKTUR
                    $where_s['a.id'] = $output->rows[$i]->id;
                    $select_s = 'a.*, c.name as package_name,d.name as package_big_name, e.name as suppliers_name';
                    $join_s = array(
                        array('package c', 'a.package_id = c.id', 'LEFT'),
                        array('package d', 'a.package_big_id = d.id', 'LEFT'),
                        array('company e', 'a.suppliers_id = e.id', 'LEFT'),
                    );
                    $order_s = array('e.name' => 'asc');
                    $supplier_data = $this->Catalog_supplier->select(0, $select_s, $where_s, $join_s, $order_s);
                    // SEARCH LAST RECEIVE ITEM/LASTFAKTUR
                    $output->rows[$i]->supplier = $supplier_data;
                }
                $response_data = $output;
                $error   = false;
            } else {
                $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
                return;
            }
            if ($error == true) {
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $this->REST_Return(200, 'SUCCESS', $response_data);
            }
            return;
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
	}

    public function list_options_get($p = 0){
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 

        $limit = $q_strings['limit'] ?? null;
        $offset = $q_strings['offset'] ?? null;

        $this->$model->limit = $limit; 
        $this->$model->offset = $offset; 

        $name = $q_strings['name'] ?? null;
        
        $select = '
        a.id,
        a.code,
        a.name,
        a.composition,
        a.package_id,
        p.name as package_name,
        a.dose,
        u.name as dose_name,
        a.signatura_dose,
        a.signatura_unit_id,
        a.signatura_times_id,
        a.pharmacy_catalog_instruction_id,
        pci.name as pharmacy_catalog_instruction_name,
        a.is_delete
        ';
        $where = array(
            'a.is_delete' => 0,
        );
        $join = array(
            array('package p', 'a.package_id = p.id', 'LEFT'),
            array('pharmacy_catalog_instruction pci', 'a.pharmacy_catalog_instruction_id = pci.id', 'LEFT'),
            array('unit u', 'a.dose_unit_id = u.id', 'LEFT'),
        );
        $order = array(
            'a.name' => 'ASC',
        );
       
        // $output = $this->$model->select(0, $select, $where, $join, $order);
       
        if ($name != null) {
            $select_name = 'a.id';
            $where_name = array(
                'a.is_delete' => 0,
            );
            $join_name = 'empty_join';
            $this->$model->whereLike['a.name'] = $name;
            $output_name_ids = $this->$model->select(0, $select_name, $where_name, $join_name);

            unset($this->$model->whereLike['a.name']);
            $this->$model->whereLike['a.composition'] = $name;
            $output_composition_ids = $this->$model->select(0, $select_name, $where_name, $join_name);

            $catalog_ids = [];

            for ($i=0; $i < count($output_name_ids); $i++) { 
                $output_name_id = $output_name_ids[$i]->id;
                if (!in_array($output_name_id, $catalog_ids)) {
                    array_push($catalog_ids, $output_name_id);
                }
            }

            for ($i=0; $i < count($output_composition_ids); $i++) { 
                $output_composition_id = $output_composition_ids[$i]->id;
                if (!in_array($output_composition_id, $catalog_ids)) {
                    array_push($catalog_ids, $output_composition_id);
                }
            }

            $this->$model->whereLike = [];
            $this->$model->whereInFields['a.id'] = $catalog_ids;
            $output = $this->$model->select(0, $select, $where, $join, $order);
        } else {
            $output = $this->$model->select(0, $select, $where, $join, $order);
        }

        $this->$model->limit = null;
        $this->$model->offset = null;
        $select = 'count(*) as iTotal';
        $iTotal = $this->$model->select(0, $select, $where)[0]->iTotal;   

        $response_data = (object) array(
            'rows' => $output,
            'start' => ($offset + $limit),
            'iTotal' => $iTotal
        );
        
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function detail_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            if (isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
            if (isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
            $this->$model->orderFields = array('a.name' => 'ASC');
            if (isset($q_strings['search'])) {
                $this->$model->whereLike['a.name'] = $q_strings['search'];
            } else {
                $this->$model->whereFields = $q_strings;
            }
            $this->$model->whereFields['a.is_delete'] = '0';
            $output = $this->$model->read($id,false);
            if($output == [] && $id == 0) {
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                if (isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
                if (isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
                if (isset($q_strings['search'])) {
                    $this->$model->set_variable('whereLike', array());
                    $this->$model->whereLike['a.composition'] = $q_strings['search'];
                    unset($q_strings['search']);
                } else {
                    $this->$model->whereFields = $q_strings;
                }
                $this->$model->whereFields['a.is_delete'] = '0';
                $output = $this->$model->read($id);
            }
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
            if ($error == true) {
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $this->REST_Return(200, 'SUCCESS', $response_data);
            }
            return;
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
	}

    public function export_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        // $is_valid_token = $this->authorization_token->validateToken();
        // if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
            if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
            if(isset($q_strings['name'])) $this->$model->whereLike['a.name'] = $q_strings['name']; unset($q_strings['name']);
            $output = $this->$model->read($id);
            $response_data = array();
            
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
            $sheet->setCellValue('A1', "id"); 
            $sheet->setCellValue('B1', "Kode"); 
            $sheet->setCellValue('C1', "Nama"); 
            $sheet->setCellValue('D1', "Jenis Katalog");
            $sheet->setCellValue('E1', "BPJS/ NON BPJS"); 
            $sheet->setCellValue('F1', "Obat Kronis"); 
            $sheet->setCellValue('G1', "Generik"); 
            $sheet->setCellValue('H1', "Komposisi"); 
            $sheet->setCellValue('I1', "Isi dose komposisi"); 
            $sheet->setCellValue('J1', "Satuan dose komposisi"); 
            $sheet->setCellValue('K1', "Satuan kecil"); 
            $sheet->setCellValue('L1', "Isi satuan kecil"); 
            $sheet->setCellValue('M1', "Satuan besar"); 
            $sheet->setCellValue('N1', "Isi satuan besar"); 
            $sheet->setCellValue('O1', "Harga Beli"); 
            $sheet->setCellValue('P1', "Harga Jual"); 
            $sheet->setCellValue('Q1', "Min"); 
            $sheet->setCellValue('R1', "Max"); 
            $sheet->setCellValue('S1', "Pabrik/ Produsen"); 
            $sheet->setCellValue('T1', "Supplier"); 
            $sheet->setCellValue('U1', "Kode Barang Accurate"); 
            $sheet->setCellValue('V1', "Active/Non Active"); 
            $sheet->setCellValue('W1', "Restriksi"); 
            $numrow = 2; // Set baris pertama untuk isi tabel adalah baris ke 4
            for ($i=0; $i < count($output); $i++) {
                $sheet->setCellValue('A'.$numrow, $output[$i]->id);
                $sheet->setCellValue('B'.$numrow, $output[$i]->code);
                $sheet->setCellValue('C'.$numrow, $output[$i]->name);
                $sheet->setCellValue('D'.$numrow, $output[$i]->category_catalog_name);
                $sheet->setCellValue('E'.$numrow, $output[$i]->category_catalog_name);
                $sheet->setCellValue('F'.$numrow, $output[$i]->name);
                $sheet->setCellValue('G'.$numrow, $output[$i]->is_chronic == 2 ? 'Obat Kronis' : '');
                $sheet->setCellValue('H'.$numrow, $output[$i]->composition);
                $sheet->setCellValue('I'.$numrow, $output[$i]->dose);
                $sheet->setCellValue('J'.$numrow, $output[$i]->dose_name);
                $sheet->setCellValue('K'.$numrow, $output[$i]->package_name);
                $sheet->setCellValue('L'.$numrow, $output[$i]->value_package);
                $sheet->setCellValue('M'.$numrow, $output[$i]->package_big_name);
                $sheet->setCellValue('N'.$numrow, $output[$i]->value_package_big);
                $sheet->setCellValue('O'.$numrow, $output[$i]->price);
                $sheet->setCellValue('P'.$numrow, $output[$i]->price*1.3);
                $sheet->setCellValue('Q'.$numrow, $output[$i]->min);
                $sheet->setCellValue('R'.$numrow, $output[$i]->max);
                $sheet->setCellValue('S'.$numrow, $output[$i]->factory_name);
                $sheet->setCellValue('T'.$numrow, $output[$i]->suppliers_name);
                $sheet->setCellValue('U'.$numrow, $output[$i]->bride_code);
                $sheet->setCellValue('V'.$numrow, $output[$i]->is_delete == 1 ? 'Non Active' : 'Active');
                $sheet->setCellValue('W'.$numrow, $output[$i]->restriction);
                $numrow++;
            }
            
            $sheet->getDefaultRowDimension()->setRowHeight(-1);
            $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $sheet->setTitle("Master Katalog");
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="Master Katalog'.date('YmdHis').'.xlsx"'); // Set nama file excel nya
            header('Cache-Control: max-age=0');
            $writer->save('php://output');
        // } else {
        //     $response_data = 'Invalid Token.';
        //     $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
        //     return;
        // }
	}

    public function get_signatura_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;
    
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 
    
        $pharmacy_catalog_id = $id;
    
        if ($pharmacy_catalog_id === 0 || $pharmacy_catalog_id === "0") {
            $this->REST_Return(400, 'pharmacy_catalog_id is required');
            return;
        }
    
        $select = 'a.*';
        $where = array();
        $join = array();
        $output = $this->Pharmacy_catalog_signatura->select($pharmacy_catalog_id, $select, $where, $join);
    
        $response_data = $output;
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function reset_signatura_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;
    
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 

        Logger::info($this->debug_uniq, "|---- start reset signatura ----|");

        $this->db->trans_start();

        $catalog = $this->db->query("
            SELECT pc.id
            FROM pharmacy_catalog pc
        ")->result();

        Logger::info($this->debug_uniq, 'total rows: ' . count($catalog));

        for ($i=0; $i < count($catalog); $i++) { 
            if ($i % 100 == 0) {
                Logger::info($this->debug_uniq, 'current processed rows: ' . $i);
            }

            $catalog_id = $catalog[$i]->id;

            $prescription_item = $this->db->query("
                SELECT ppi.pharmacy_catalog_id, ppi.used, ppi.desc, pp.date_added
                FROM pharmacy_prescription_item ppi
                INNER JOIN pharmacy_prescription pp ON ppi.pharmacy_prescription_id = pp.id
                WHERE ppi.pharmacy_catalog_id = '$catalog_id'
                AND (ppi.used IS NOT NULL
                AND ppi.used <> '')
                ORDER BY pp.date_added DESC
                LIMIT 300
            ")->result();
            
            $used_data_all = array(
                'signatura' => array(),
                'unit' => array(),
                'package' => array(),
                'morning' => array(),
                'afternoon' => array(),
                'evening' => array(),
                'measure' => array(),
                'how_to_use' => array(),
            );

            for ($j=0; $j < count($prescription_item); $j++) { 
                $raw_used_text = strtolower($prescription_item[$j]->used);
                $raw_desc_text = strtolower($prescription_item[$j]->desc);
            
                preg_match_all("/[\d] ?(?i)[x] ?[\d]*[\.,]?[\d]?\/?[\d]? ?(?i)(mg|ml|cc|tab)?|(?i)(pagi|siang|malam)|(sendok  ?makan|sendok  ?teh)|(?i)(sebelum  ?makan|setelah  ?makan|sesudah  ?makan)|(sdm|sdt)/", $raw_used_text, $regex_used);
                preg_match_all("/[\d] ?(?i)[x] ?[\d]*[\.,]?[\d]?\/?[\d]? ?(?i)(mg|ml|cc|tab)?|(?i)(pagi|siang|malam)|(sendok  ?makan|sendok  ?teh)|(?i)(sebelum  ?makan|setelah  ?makan|sesudah  ?makan)|(sdm|sdt)/", $raw_desc_text, $regex_desc);
            
                $used_data = array(
                    'signatura' => '',
                    'unit' => '',
                    'package' => '',
                    'morning' => '',
                    'afternoon' => '',
                    'evening' => '',
                    'measure' => '',
                    'how_to_use' => '',
                );
            
                $first = 0;

                for ($k=0; $k < count($regex_used); $k++) { 
                    for ($l=0; $l < count($regex_used[$k]); $l++) { 
                        $raw = explode(' ', $regex_used[$k][$l])[0];
                        $raw_raw = $regex_used[$k][$l];
                        
                        if ($first == 0) {
                            $first += 1;
            
                            preg_match_all("/[\d] ?(?i)[x] ?[\d]*[\.,]?[\d]?\/?[\d]?/", $regex_used[$k][$l], $raw_signatura);

                            if (count($raw_signatura[0]) > 0) {
                                $raw_signatura = str_replace(' ', '', $raw_signatura[0][0]);
                                $used_data['signatura'] = $raw_signatura;
                            }
                        } 
            
                        if ($raw == 'mg' || $raw == 'ml' || $raw == 'cc') {
                            $used_data['unit'] = $raw;
                        }
            
                        if ($raw == 'tab') {
                            $used_data['package'] = $raw;
                        }
            
                        if ($raw == 'pagi') {
                            $used_data['morning'] = 1;
                        }
            
                        if ($raw == 'siang') {
                            $used_data['afternoon'] = 1;
                        }
            
                        if ($raw == 'malam') {
                            $used_data['evening'] = 1;
                        }

                        if ($raw_raw == 'sendok makan' || $raw_raw == 'sdm') {
                            $used_data['measure'] = 'sdm';
                        }

                        if ($raw_raw == 'sendok teh' || $raw_raw == 'sdt') {
                            $used_data['measure'] = 'sdt';
                        }
            
                        if ($raw_raw == 'sesudah makan' || $raw_raw == 'setelah makan') {
                            $used_data['how_to_use'] = 'setelah makan';
                        }

                        if ($raw_raw == 'sebelum makan') {
                            $used_data['how_to_use'] = 'sebelum makan';
                        }
                    }
                }

                for ($k=0; $k < count($regex_desc); $k++) { 
                    for ($l=0; $l < count($regex_desc[$k]); $l++) { 
                        $raw = explode(' ', $regex_desc[$k][$l])[0];
                        $raw_raw = $regex_desc[$k][$l];
                        
                        if ($first == 0) {
                            $first += 1;
            
                            preg_match_all("/[\d] ?(?i)[x] ?[\d]*[\.,]?[\d]?\/?[\d]?/", $regex_desc[$k][$l], $raw_signatura);

                            if (count($raw_signatura[0]) > 0) {
                                $raw_signatura = str_replace(' ', '', $raw_signatura[0][0]);
                                $used_data['signatura'] = $raw_signatura;
                            }
                        } 
            
                        if ($raw == 'mg' || $raw == 'ml' || $raw == 'cc') {
                            $used_data['unit'] = $raw;
                        }
            
                        if ($raw == 'tab') {
                            $used_data['package'] = $raw;
                        }
            
                        if ($raw == 'pagi') {
                            $used_data['morning'] = 1;
                        }
            
                        if ($raw == 'siang') {
                            $used_data['afternoon'] = 1;
                        }
            
                        if ($raw == 'malam') {
                            $used_data['evening'] = 1;
                        }

                        if ($raw_raw == 'sendok makan' || $raw_raw == 'sdm') {
                            $used_data['measure'] = 'sdm';
                        }

                        if ($raw_raw == 'sendok teh' || $raw_raw == 'sdt') {
                            $used_data['measure'] = 'sdt';
                        }
            
                        if ($raw_raw == 'sesudah makan' || $raw_raw == 'setelah makan') {
                            $used_data['how_to_use'] = 'setelah makan';
                        }

                        if ($raw_raw == 'sebelum makan') {
                            $used_data['how_to_use'] = 'sebelum makan';
                        }
                    }
                }
            
                if ($used_data['signatura'] != '') {
                    if (key_exists($used_data['signatura'], $used_data_all['signatura']) != 1) {
                        $used_data_all['signatura'][$used_data['signatura']] = 1;
                    } else {
                        $used_data_all['signatura'][$used_data['signatura']] += 1;
                    }
                }
            
                if ($used_data['unit'] != '') {
                    if (key_exists($used_data['unit'], $used_data_all['unit']) != 1) {
                        $used_data_all['unit'][$used_data['unit']] = 1;
                    } else {
                        $used_data_all['unit'][$used_data['unit']] += 1;
                    }
                }
            
                if ($used_data['package'] != '') {
                    if (key_exists($used_data['package'], $used_data_all['package']) != 1) {
                        $used_data_all['package'][$used_data['package']] = 1;
                    } else {
                        $used_data_all['package'][$used_data['package']] += 1;
                    }
                }
            
                if ($used_data['morning'] != '') {
                    if (key_exists($used_data['morning'], $used_data_all['morning']) != 1) {
                        $used_data_all['morning'][$used_data['morning']] = 1;
                    } else {
                        $used_data_all['morning'][$used_data['morning']] += 1;
                    }
                }
            
                if ($used_data['afternoon'] != '') {
                    if (key_exists($used_data['afternoon'], $used_data_all['afternoon']) != 1) {
                        $used_data_all['afternoon'][$used_data['afternoon']] = 1;
                    } else {
                        $used_data_all['afternoon'][$used_data['afternoon']] += 1;
                    }
                }
            
                if ($used_data['evening'] != '') {
                    if (key_exists($used_data['evening'], $used_data_all['evening']) != 1) {
                        $used_data_all['evening'][$used_data['evening']] = 1;
                    } else {
                        $used_data_all['evening'][$used_data['evening']] += 1;
                    }
                }

                if ($used_data['measure'] != '') {
                    if (key_exists($used_data['measure'], $used_data_all['measure']) != 1) {
                        $used_data_all['measure'][$used_data['measure']] = 1;
                    } else {
                        $used_data_all['measure'][$used_data['measure']] += 1;
                    }
                }
            
                if ($used_data['how_to_use'] != '') {
                    if (key_exists($used_data['how_to_use'], $used_data_all['how_to_use']) != 1) {
                        $used_data_all['how_to_use'][$used_data['how_to_use']] = 1;
                    } else {
                        $used_data_all['how_to_use'][$used_data['how_to_use']] += 1;
                    }
                }
            }

            arsort($used_data_all['signatura']);
            arsort($used_data_all['unit']);
            arsort($used_data_all['package']);
            arsort($used_data_all['morning']);
            arsort($used_data_all['afternoon']);
            arsort($used_data_all['evening']);
            arsort($used_data_all['measure']);
            arsort($used_data_all['how_to_use']);

            $signatura = array(
                'pharmacy_catalog_id' => $catalog_id,
                'signatura' => '',
                'unit' => '',
                'package' => '',
                'morning' => 0,
                'afternoon' => 0,
                'evening' => 0,
                'measure' => '',
                'how_to_use' => '',
            );

            foreach ($used_data_all as $key => $val) {
                if (count($val) > 0) {
                    $first_idx = 0;
                    foreach ($val as $key_2 => $val_2) {
                        if ($first_idx != 0) {
                            break;
                        }
                        if ($val_2 >= 5) { // KONDISI MINIMUM MASUK DEFAULT VALUE
                            $signatura[$key] = $key_2;
                        } else {
                            $signatura[$key] = '';
                        }
                        $first_idx += 1;
                    }
                }
            }

            $this->Pharmacy_catalog_signatura->replace($signatura);
        }

        Logger::info($this->debug_uniq, "|---- finish reset signatura ----|");

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->REST_Return(422, 'something when wrong', array('query_error' => $this->db->error()));
            return;
        }
    
        $this->REST_Return(200, 'SUCCESS', []);
        return;
    }

    public function update_instruction_put($p = 0) {
        // CALL JSON POST
        $data       = $this->json_put();
        // DEFINE ATTR
        $dt_put     = $data['dt_postput'];
        $model      = $this->name;
        $id         = $this->security->xss_clean($p);

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 

        // Form Validation
        $this->form_validation->set_data($dt_put);
        $this->form_validation->set_rules($this->update_instruction_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $pharmacy_catalog_id = $id;

        if ($pharmacy_catalog_id === 0 || $pharmacy_catalog_id === "0") {
            $this->REST_Return(400, 'pharmacy_catalog_id is required');
            return;
        }

        $select = 'a.id';
        $catalog = $this->$model->select($pharmacy_catalog_id, $select);
        if (count($catalog) == 0) {
            $this->REST_Return(404, 'there is no catalog with id ' . $pharmacy_catalog_id);
            return;
        }

        $pharmacy_catalog_instruction_id = $dt_put['pharmacy_catalog_instruction_id'];

        $select = 'a.id';
        $catalog_instruction = $this->Catalog_instruction->select($pharmacy_catalog_instruction_id, $select);
        if (count($catalog_instruction) == 0) {
            $this->REST_Return(404, 'there is no catalog_instruction with id ' . $pharmacy_catalog_instruction_id);
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();

        $dt_pharmacy_catalog_signatura = array(
            'pharmacy_catalog_instruction_id' => $pharmacy_catalog_instruction_id,
        );

        $this->Pharmacy_catalog_signatura->update_v2($pharmacy_catalog_id, $dt_pharmacy_catalog_signatura);

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_pharmacy_catalog_signatura,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update pharmacy_catalog_signatura', $response_data);
            return;
        }

        $response_data = array(
            'id'     => $id
        );

        $this->REST_Return(200, 'SUCCESS', $response_data);
    }
}