    <?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Medical_support_bundle extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_item_model", "{$this->name}_item");
        $this->load->model("$this->directory/Laboratory_package_model", "Laboratory_package");
        $this->load->model("$this->directory/Radiology_package_model", "Radiology_package");
        $this->load->model("v1/finance/Costing_services_class_model", "Costing_services_class");

        $this->create_rules = array(
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|required'),
            array('field' => 'title', 'label' => 'title', 'rules' => 'trim|required'),
            array('field' => 'description', 'label' => 'description', 'rules' => 'trim|required'),
            array('field' => 'costing_id', 'label' => 'costing_id', 'rules' => 'trim|required'),
        );

        $this->update_rules = array(
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|required'),
            array('field' => 'title', 'label' => 'title', 'rules' => 'trim|required'),
            array('field' => 'description', 'label' => 'description', 'rules' => 'trim|required'),
            array('field' => 'costing_id', 'label' => 'costing_id', 'rules' => 'trim|required'),
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );

        $this->add_item_rules = array(
            array('field' => 'package_id', 'label' => 'package_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'item_type_id', 'label' => 'item_type_id', 'rules' => 'trim|required|max_length[1]'),
        );

        $this->delete_item_rules = array(
            array('field' => 'package_id', 'label' => 'package_id', 'rules' => 'trim|required|max_length[36]'),
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
        $medical_support_bundle_id = $q_strings['id'];
    
        if ($medical_support_bundle_id === 0 || $medical_support_bundle_id === "0") {
            $this->REST_Return(400, 'medical_support_bundle_id is required');
            return;
        }
    
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read($medical_support_bundle_id);
        $response_data = array();

        $select = 'a.*';
        $where = array('medical_support_bundle_id' => $medical_support_bundle_id, 'is_delete' => 0);
        $items = $this->Medical_support_bundle_item->select(0, $select, $where);

        for ($i=0; $i < count($items); $i++) { 
            $package_id = $items[$i]->package_id;
            $item_type_id = $items[$i]->item_type_id;

            if ($item_type_id == 1) {
                $select = 'a.id, a.name, a.category';
                $package = $this->Laboratory_package->select($package_id, $select);
                $package_name = $package[0]->name;
                $category = $package[0]->category;
                $items[$i]->package_name = $package_name;
                $items[$i]->category = $category;
            } elseif ($item_type_id == 2) {
                $select = 'a.id, a.name, a.category';
                $package = $this->Radiology_package->select($package_id, $select);
                $package_name = $package[0]->name;
                $category = $package[0]->category;
                $items[$i]->package_name = $package_name;
                $items[$i]->category = $category;
            } elseif ($item_type_id == 3) {
                $select = 'a.costing_id, c.name';
                $join = array(
                    array('costing c', 'a.costing_id = c.id', 'LEFT')
                );
                $package = $this->Costing_services_class->select(0, $select, array('a.costing_id'=>$package_id),$join);
                $package_name = $package[0]->name;
                $items[$i]->category = 'Dokter';
                $items[$i]->package_name = $package_name;
            } elseif ($item_type_id == 4) {
                $select = 'a.costing_id, c.name';
                $join = array(
                    array('costing c', 'a.costing_id = c.id', 'LEFT')
                );
                $package = $this->Costing_services_class->select(0, $select, array('a.costing_id'=>$package_id),$join);
                $package_name = $package[0]->name;
                $items[$i]->category = 'Administrasi';
                $items[$i]->package_name = $package_name;
            }
        }

        $output[0]->items = $items;
    
        $response_data = $output[0];
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
    
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
    
        if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
        if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
    
        $this->$model->whereFields = $q_strings;
        $output = $this->$model->read_ssr($id);

        for ($i=0; $i < count($output->rows); $i++) { 
            $medical_support_bundle_id = $output->rows[$i]->id;

            $select = 'a.*';
            $where = array('medical_support_bundle_id' => $medical_support_bundle_id, 'is_delete' => 0);
            $items = $this->Medical_support_bundle_item->select(0, $select, $where);

            for ($j=0; $j < count($items); $j++) { 
                $package_id = $items[$j]->package_id;
                $item_type_id = $items[$j]->item_type_id;

                if ($item_type_id == 1) {
                    $select = 'a.id, a.name, a.category';
                    $package = $this->Laboratory_package->select($package_id, $select);
                    $package_name = $package[0]->name;
                    $category = $package[0]->category;
                    $items[$j]->category = $category;
                    $items[$j]->package_name = $package_name;
                } elseif ($item_type_id == 2) {
                    $select = 'a.id, a.name, a.category';
                    $package = $this->Radiology_package->select($package_id, $select);
                    $package_name = $package[0]->name;
                    $category = $package[0]->category;
                    $items[$j]->category = $category;
                    $items[$j]->package_name = $package_name;
                } elseif ($item_type_id == 3) {
                    $select = 'a.costing_id, c.name';
                    $join = array(
                        array('costing c', 'a.costing_id = c.id', 'LEFT')
                    );
                    $package = $this->Costing_services_class->select(0, $select, array('a.costing_id'=>$package_id),$join);
                    $package_name = $package[0]->name;
                    $items[$j]->category = 'Dokter';
                    $items[$j]->package_name = $package_name;
                } elseif ($item_type_id == 4) {
                    $select = 'a.costing_id, c.name';
                    $join = array(
                        array('costing c', 'a.costing_id = c.id', 'LEFT')
                    );
                    $package = $this->Costing_services_class->select(0, $select, array('a.costing_id'=>$package_id),$join);
                    $package_name = $package[0]->name;
                    $items[$j]->category = 'Administrasi';
                    $items[$j]->package_name = $package_name;
                }
            }

            $output->rows[$i]->items = $items;
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
        
        $name = $dt_post['name'];
        $title = $dt_post['title'];
        $description = $dt_post['description'];
        $costing_id = $dt_post['costing_id'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_medical_support_bundle = array(
            'name'          => $name,
            'title'         => $title,
            'description'   => $description,
            'costing_id'    => $costing_id,
            'user_added'    => $user_id,
            'date_added'    => date('Y-m-d H:i:s',time()),
            'date_updated'  => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_medical_support_bundle);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_medical_support_bundle,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create medical_support_bundle', $response_data);
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
    
        $medical_support_bundle_id = $id;
    
        if ($medical_support_bundle_id === 0 || $medical_support_bundle_id === "0") {
            $this->REST_Return(400, 'medical_support_bundle_id is required');
            return;
        }

        $select = 'a.id';
        $medical_support_bundle = $this->$model->select($medical_support_bundle_id, $select);

        if (count($medical_support_bundle) == 0) {
            $this->REST_Return(404, 'there is no medical_support_bundle with id ' . $medical_support_bundle_id);
            return;
        }

        $name = $dt_put['name'];
        $title = $dt_put['title'];
        $description = $dt_put['description'];
        $costing_id = $dt_put['costing_id'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_medical_support_bundle = array(
            'name'          => $name,
            'title'         => $title,
            'description'   => $description,
            'costing_id'    => $costing_id,
            'user_updated'  => $user_id,
            'date_updated'  => date('Y-m-d H:i:s',time()),
        );
    
        $this->$model->update($medical_support_bundle_id, $dt_medical_support_bundle);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_medical_support_bundle,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update medical_support_bundle', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function add_item_put($p = 0) {
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
        $this->form_validation->set_rules($this->add_item_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $medical_support_bundle_id = $id;

        if ($medical_support_bundle_id === 0 || $medical_support_bundle_id === "0") {
            $this->REST_Return(400, 'medical_support_bundle_id is required');
            return;
        }

        $package_id = $dt_put['package_id'];
        $item_type_id = $dt_put['item_type_id'];

        $select = 'a.id';
        $medical_support_bundle = $this->$model->select($medical_support_bundle_id, $select);

        if (count($medical_support_bundle) == 0) {
            $this->REST_Return(404, 'there is no medical_support_bundle with id ' . $medical_support_bundle_id);
            return;
        }

        if ($item_type_id == 1) {
            $select = 'a.id';
            $package = $this->Laboratory_package->select($package_id, $select);
    
            if (count($package) == 0) {
                $this->REST_Return(404, 'there is no laboratory_package with id ' . $package_id);
                return;
            }
        } elseif ($item_type_id == 2) {
            $select = 'a.id';
            $package = $this->Radiology_package->select($package_id, $select);
    
            if (count($package) == 0) {
                $this->REST_Return(404, 'there is no radiology_package with id ' . $package_id);
                return;
            }
        }

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_medical_support_bundle_item = array(
            'medical_support_bundle_id' => $medical_support_bundle_id,
            'package_id'                => $package_id,
            'item_type_id'              => $item_type_id,
            'user_added'                => $user_id,
            'user_updated'              => $user_id,
            'date_updated'              => date('Y-m-d H:i:s',time()),
            'is_delete'                 => 0,
        );
    
        $this->Medical_support_bundle_item->replace($dt_medical_support_bundle_item);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_medical_support_bundle_item,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to add item medical_support_bundle', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function delete_item_delete($p = 0) {
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
        $this->form_validation->set_rules($this->delete_item_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        if ($id === 0 || $id === "0") {
            $this->REST_Return(400, 'medical_support_bundle_id is required');
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $medical_support_bundle_id = $this->$model->select_id($id);
    
        if ($medical_support_bundle_id == "") {
            $this->REST_Return(404, 'there is no medical_support_bundle with id ' . $id);
            return;
        }

        $package_id = $dt_put['package_id'];

        $select = 'a.medical_support_bundle_id';
        $where = array('medical_support_bundle_id' => $medical_support_bundle_id, 'package_id' => $package_id, 'is_delete' => 0);
        $item = $this->Medical_support_bundle_item->select(0, $select, $where);

        if (count($item) == 0) {
            $this->REST_Return(404, 'there is no package with id ' . $package_id . ' in medical_support_bundle_id ' . $medical_support_bundle_id);
            return;
        }
        
        /* Start Transaction */
        $this->db->trans_complete();
            
        $dt_medical_support_bundle_item = array(
            'medical_support_bundle_id' => $medical_support_bundle_id,
            'package_id'                => $package_id,
            'user_updated'              => $user_id,
            'date_updated'              => date('Y-m-d H:i:s'),
            'is_delete'                 => 1,
        );
    
        $this->Medical_support_bundle_item->replace($dt_medical_support_bundle_item);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_medical_support_bundle_item,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete medical_support_bundle_item', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}