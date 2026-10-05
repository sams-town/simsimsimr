<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Allergy extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/Institute_branch_model", "Institute_branch");

        $this->update_rules = array(
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|max_length[100]'),
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
    }

    /**
     * Allergy API
     * --------------------
     * @param: institute_branch_id
     * @param: name
     * --------------------
     * @method : POST
     * @link: version/md/allergy
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
            if(is_array($dt_post['allergy'])) {
                $count = count($dt_post['allergy']);
                // print_r($count);
                // die();
                for($i=0; $i < $count; $i++) {
                    // Check institute_branch_id
                    $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                    $model = $this->name;
                        /* Start Transaction */
                        $this->db->trans_start();
                        $dt_allergy = array(
                            'institute_branch_id'   => $institute_branch_id,
                            'name'                  => $dt_post['allergy'][$i]['name'],
                        );
                        $id = $this->$model->create($dt_allergy);
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

                }
                return;
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }
}