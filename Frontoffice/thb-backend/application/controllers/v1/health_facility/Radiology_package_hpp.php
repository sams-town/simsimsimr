<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Radiology_package_hpp extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/Radiology_request_hpp_model", "Radiology_request_hpp");
        $this->load->model("$this->directory/Radiology_package_model", "Radiology_package");
        $this->load->model("$this->directory/Radiology_request_items_model", "Radiology_request_items");

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
    }

    /**
     * Radiology_package API
     * --------------------
     * @param: radiology_package_id
     * @param: pharmacy_catalog_id
     * --------------------
     * @method : POST
     * @link: version/health_facility/radiology_package
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
            $this->form_validation->set_rules('radiology_package_id', 'radiology_package_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('pharmacy_catalog_id', 'pharmacy_catalog_id', 'trim|required|max_length[36]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                if($this->Radiology_package->read($dt_post['radiology_package_id'],false) != []) {
                    $dt_radiology_package = array(
                        'radiology_package_id'            => $dt_post['radiology_package_id'],
                        'pharmacy_catalog_id'               => $dt_post['pharmacy_catalog_id'],
                    );
                    $id = $this->$model->create($dt_radiology_package);

                } else {
                    $this->REST_Return(404, 'Wrong radiology_package_id!');
                    return;
                }
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
                return;
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }


    public function generate_hpp_get($p = 0){
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;
    
        // Token Validation
        // $is_valid_token = $this->authorization_token->validateToken();
        // if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
        //     $response_data = 'Invalid Token.';
        //     $this->REST_Return(401, $is_valid_token['message'] , $response_data);
        //     return;
        // }
        
        $lab_request = $this->db->query("
        SELECT `a`.`id`, `a`.`radiology_request_id`, `a`.`radiology_package_id`, a.date_request
        FROM `radiology_request_item` as `a` 
        INNER JOIN `radiology_request` `b` ON `a`.`radiology_request_id` = `b`.`id` 
        INNER JOIN `soap` `s` ON b.soap_id = s.id
        INNER JOIN `registration_destination` `rd` ON `s`.`registration_destination_id` = `rd`.`id` 
        WHERE `rd`.`checkout_date` >= '2025-12-01 00:00:00' AND `rd`.`checkout_date` <= '2025-12-08 23:59:59'
        AND `a`.`hpp` = 1 AND `a`.`is_delete` = 0 AND `b`.`is_delete` = 0 AND `b`.`status` >= 2 ORDER BY a.date_request ASC")->result();

        $this->db->trans_start();

        for ($i=0; $i < count($lab_request); $i++) { 
            if ($lab_request[$i]->radiology_package_id) {

                $lab_package_hpp = $this->db->query("
                SELECT a.*, b.id as pharmacy_price_id, b.hpp_item as hpp
                FROM `radiology_package_hpp` as `a` 
                INNER JOIN `pharmacy_assets_price` `b` ON `a`.`pharmacy_catalog_id` = `b`.`pharmacy_catalog_id` 
                WHERE `a`.`is_delete` = 0 AND `b`.`is_active` = 1 AND `a`.`radiology_package_id` = '{$lab_request[$i]->radiology_package_id}' ")->result();
                $radiology_request_item_id = $lab_request[$i]->id;

                if ($lab_package_hpp) {
                    for ($d=0; $d < count($lab_package_hpp); $d++) { 
                        if ($lab_request[$i]->radiology_package_id == $lab_package_hpp[$d]->radiology_package_id) {

                            $pharmacy_catalog_id        = $lab_package_hpp[$d]->pharmacy_catalog_id;
                            $pharmacy_price_id          = $lab_package_hpp[$d]->pharmacy_price_id;
                            $package_id                 = $lab_package_hpp[$d]->package_id;
                            $package_name               = $lab_package_hpp[$d]->package_name;
                            $hpp                        = $lab_package_hpp[$d]->hpp;
                            $qty                        = $lab_package_hpp[$d]->qty;
                            $total                      = $hpp * $qty;

                            $dt_lab_request_hpp = array(
                                'radiology_request_item_id' => $radiology_request_item_id,
                                'pharmacy_catalog_id'        => $pharmacy_catalog_id,
                                'pharmacy_price_id'          => $pharmacy_price_id,
                                'package_id'                 => $package_id,
                                'package_name'               => $package_name,
                                'hpp'                        => $hpp,
                                'qty'                        => $qty,
                                'total'                      => $total,
                                'date_added'                 => date('Y-m-d H:i:s')
                            );

                            $hpp_create = $this->Radiology_request_hpp->create($dt_lab_request_hpp);
                            if ($hpp_create) {
                                $this->Radiology_request_items->update_v2($radiology_request_item_id, array('hpp' => '2'));
                            }
                        }
                    } 
                }
            }
        }

        $this->db->trans_complete();
        /* End Transaction */
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => [],
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying tr', $lab_request);
            return;
        }

        $this->REST_Return(200, 'SUCCESS', $lab_request);
        return;
    }
}