<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Template_prescription extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_item_model", "{$this->name}_item");
        $this->load->model("$this->directory/Setting_model", "Setting");
        $this->load->model("$this->directory/Package_model", "Package");
        $this->load->model("v1/pharmacy/Catalog_model", "Catalog");

        $this->create_rules = array(
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|required|max_length[150]')
        );

        $this->update_rules = array(
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|required|max_length[150]')
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
    }

    public function detail_get($p = 0) {
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

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $select = 'a.id';
        $where = array('a.user_id' => $user_id);
        $setting = $this->Setting->select(0, $select, $where);

        $setting_id = $setting[0]->id;

        $select = 'a.*';
        $where = array(
            'a.setting_id' => $setting_id,
            'a.is_delete' => 0,
        );
        $output = $this->$model->select(0, $select, $where);

        for ($i=0; $i < count($output); $i++) { 
            $template_prescription_id = $output[$i]->id;

            $select = 'a.*, pc.name as pharmacy_catalog_name, pc.code as pharmacy_catalog_code, p.name as package_name';
            $where = array(
                'a.template_prescription_id' => $template_prescription_id,
            );
            $join = array(
                array('pharmacy_catalog pc', 'a.pharmacy_catalog_id = pc.id', 'INNER'),
                array('package p', 'a.package_id = p.id', 'INNER'),
            );
            $template_prescription_item = $this->Template_prescription_item->select(0, $select, $where, $join);

            $form_prescription_parent = [];
            for ($j=0; $j < count($template_prescription_item); $j++) { 
                //------------------- detail form_prescription_parent -//
                $parent_id = $template_prescription_item[$j]->prescription_item_type_id; 
                $parent_used = $template_prescription_item[$j]->prescription_item_type_used; 
                $parent_value = $template_prescription_item[$j]->prescription_item_type_value; 
                $parent_package = $template_prescription_item[$j]->prescription_item_type_package; 
    
                $dt_form_prescription_parent = array(
                    'id' => $parent_id,
                    'used' => $parent_used,
                    'value' => $parent_value,
                    'package' => $parent_package,
                );
    
                if (count($form_prescription_parent) == 0) {
                    array_push($form_prescription_parent, $dt_form_prescription_parent);
                }
    
                $skip = 0;
                for ($ip=0; $ip < count($form_prescription_parent); $ip++) { 
                    if ($form_prescription_parent[$ip]['id'] == $parent_id) {
                        $skip = 1;
                    }
                }
    
                if ($skip == 0) {
                    array_push($form_prescription_parent, $dt_form_prescription_parent);
                }
                //----------------------------------------------------//
            }

            $output[$i]->form_prescription = $template_prescription_item;
            $output[$i]->form_prescription_parent = $form_prescription_parent;
        }
    
        $response_data = $output;
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function create_post() {
        // CALL JSON POST
        $data       = $this->json_post();
        // DEFINE ATTR
        $dt_post    = $data['dt_postput'];
        $model      = $this->name;
    
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 
    
        // Form Validation
        $this->form_validation->set_data($dt_post);
        $this->form_validation->set_rules($this->create_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $select = 'a.id';
        $where = array('a.user_id' => $user_id);
        $setting = $this->Setting->select(0, $select, $where);

        $setting_id = $setting[0]->id;
    
        /* Start Transaction */
        $this->db->trans_start();

        $name = $dt_post['name'];
    
        $dt_template_prescription = array(
            'setting_id' => $setting_id,
            'name' => $name,
            'date_added' => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_template_prescription);

        $form_prescription = $dt_post['form_prescription'];
        $form_prescription_parent = $dt_post['form_prescription_parent'];
        for ($i=0; $i < count($form_prescription); $i++) { 
            $pharmacy_catalog_id = $form_prescription[$i]['pharmacy_catalog_id'];
            $package_id = $form_prescription[$i]['package_id'];

            if ($pharmacy_catalog_id == null) {
                $this->REST_Return(400, 'pharmacy_catalog_id is required');
                return;
            }

            if ($package_id == null) {
                $this->REST_Return(400, 'package_id is required');
                return;
            }

            $select = 'a.id';
            $catalog = $this->Catalog->select($pharmacy_catalog_id, $select);
            if (count($catalog) == 0) {
                $this->REST_Return(404, 'catalog with id ' . $pharmacy_catalog_id . ' does not exist');
                return;
            }

            $select = 'a.id';
            $package = $this->Package->select($package_id, $select);
            if (count($package) == 0) {
                $this->REST_Return(404, 'package with id ' . $package_id . ' does not exist');
                return;
            }

            $dose = $form_prescription[$i]['dose'];
            $prescription_type_id = $form_prescription[$i]['prescription_type_id'];
            $value_package = $form_prescription[$i]['value_package'];
            $used = $form_prescription[$i]['used'];
            $desc = $form_prescription[$i]['desc'];
            $sort = $form_prescription[$i]['sort'];
            
            for ($j=0; $j < count($form_prescription_parent); $j++) { 
                $id_parent = $form_prescription_parent[$j]['id'];
                if ($id_parent == $prescription_type_id) {
                    $prescription_type_used = $form_prescription_parent[$j]['used'];
                    $prescription_type_value = $form_prescription_parent[$j]['value'];
                    $prescription_type_package = $form_prescription_parent[$j]['package'];
                }
            }

            $dt_template_prescription_item = array(
                'template_prescription_id'      => $id,
                'pharmacy_catalog_id'           => $pharmacy_catalog_id,
                'package_id'                    => $package_id,
                'dose'                          => $dose,
                'prescription_item_type_id'     => $prescription_type_id,
                'qty'                           => $value_package,
                'used'                          => $used,
                'desc'                          => $desc,
                'sort'                          => $sort,
                'prescription_item_type_used'   => $prescription_type_used,
                'prescription_item_type_value'  => $prescription_type_value,
                'prescription_item_type_package'  => $prescription_type_package,
            );
    
            $this->Template_prescription_item->create($dt_template_prescription_item);
        }
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_template_prescription,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create template_prescription', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
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
    
        $template_prescription_id = $id;
    
        if ($template_prescription_id === 0 || $template_prescription_id === "0") {
            $this->REST_Return(400, 'template_prescription_id is required');
            return;
        }

        $select = 'a.id';
        $where = array(
            'a.is_delete' => 0,
        );
        $template_prescription = $this->$model->select($template_prescription_id, $select, $where);
        if (count($template_prescription) == 0) {
            $this->REST_Return(404, 'there is no template_prescription with id ' . $template_prescription_id);
            return;
        }

        $name = $dt_put['name'];
        $form_prescription = $dt_put['form_prescription'];
        $form_prescription_parent = $dt_put['form_prescription_parent'];

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_template_prescription = array(
            'name' => $name
        );
    
        $this->$model->update($template_prescription_id, $dt_template_prescription);

        $dt_template_prescription_item_delete = array(
            'is_delete' => 1,
        );
        $where_delete = array(
            'template_prescription_id' => $template_prescription_id
        );
        $st_delete = $this->Template_prescription_item->update_v2(0, $dt_template_prescription_item_delete, $where_delete);
        if ($st_delete < 0) {
            $this->REST_Return(422, 'something when wrong');
            return;
        }

        for ($i=0; $i < count($form_prescription); $i++) { 
            $pharmacy_catalog_id = $form_prescription[$i]['pharmacy_catalog_id'];
            $package_id = $form_prescription[$i]['package_id'];

            if ($pharmacy_catalog_id == null) {
                $this->REST_Return(400, 'pharmacy_catalog_id is required');
                return;
            }

            if ($package_id == null) {
                $this->REST_Return(400, 'package_id is required');
                return;
            }

            $select = 'a.id';
            $catalog = $this->Catalog->select($pharmacy_catalog_id, $select);
            if (count($catalog) == 0) {
                $this->REST_Return(404, 'catalog with id ' . $pharmacy_catalog_id . ' does not exist');
                return;
            }

            $select = 'a.id';
            $package = $this->Package->select($package_id, $select);
            if (count($package) == 0) {
                $this->REST_Return(404, 'package with id ' . $package_id . ' does not exist');
                return;
            }

            $dose = $form_prescription[$i]['dose'];
            $prescription_type_id = $form_prescription[$i]['prescription_type_id'];
            $value_package = $form_prescription[$i]['value_package'];
            $used = $form_prescription[$i]['used'];
            $desc = $form_prescription[$i]['desc'];
            $sort = $form_prescription[$i]['sort'];
            
            for ($j=0; $j < count($form_prescription_parent); $j++) { 
                $id_parent = $form_prescription_parent[$j]['id'];
                if ($id_parent == $prescription_type_id) {
                    $prescription_type_used = $form_prescription_parent[$j]['used'];
                    $prescription_type_value = $form_prescription_parent[$j]['value'];
                    $prescription_type_package = $form_prescription_parent[$j]['package'];
                }
            }

            $dt_template_prescription_item = array(
                'template_prescription_id'      => $template_prescription_id,
                'pharmacy_catalog_id'           => $pharmacy_catalog_id,
                'package_id'                    => $package_id,
                'dose'                          => $dose,
                'prescription_item_type_id'     => $prescription_type_id,
                'qty'                           => $value_package,
                'used'                          => $used,
                'desc'                          => $desc,
                'sort'                          => $sort,
                'prescription_item_type_used'   => $prescription_type_used,
                'prescription_item_type_value'  => $prescription_type_value,
                'prescription_item_type_package'=> $prescription_type_package,
                'is_delete'                     => 0
            );

            $select = 'a.id';
            $where = array(
                'template_prescription_id' => $template_prescription_id,
                'pharmacy_catalog_id' => $pharmacy_catalog_id,
            );
            $check_is_exist = $this->Template_prescription_item->select(0, $select, $where);
            if (count($check_is_exist) == 0) {
                $this->Template_prescription_item->create($dt_template_prescription_item);
            } else {
                $template_prescription_item_id = $check_is_exist[0]->id;
                $this->Template_prescription_item->update_v2($template_prescription_item_id, $dt_template_prescription_item);
            }
        }
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_template_prescription,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update template_prescription', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function delete_delete($p = 0) {
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
        $this->form_validation->set_rules($this->delete_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        if ($id === 0 || $id === "0") {
            $this->REST_Return(400, 'template_prescription_id is required');
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $template_prescription_id = $this->$model->select_id($id);
    
        if ($template_prescription_id == "") {
            $this->REST_Return(404, 'there is no template_prescription with id ' . $id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $dt_template_prescription = array(
            'is_delete' => $is_delete,
        );
    
        $this->$model->update($template_prescription_id, $dt_template_prescription);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_template_prescription,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete template_prescription', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}