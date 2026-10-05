<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Initial extends My_Controller {
  public $institute_id;
  public $institute_branch_id;
  public $code;
  public $variable;
  public $description;
  public $health_facility_id;

  public function __construct() {
    parent::__construct();
    // Load Authorization Token Library
    $this->load->library('Authorization_Token');
    // Load Model
  }

  /**
   * Initial API
   * --------------------
   * @param: type_system
   * @param: is_holding
   * @param: name
   * @param: phone
   * @param: email
   * @param: password
   * --------------------
   * @method : POST
   * @link: version/initial/register
  **/

  public function create_post() {
    // CALL JSON POST
    $data       = $this->json_post();
    // DEFINE ATTR
    $dt_post    = $data['dt_post'];
    $device     = $data['dt_server']['HTTP_USER_AGENT'];
    $ip         = $this->get_client_ip();
    $error      = TRUE;
    # Form Validation
    $this->form_validation->set_rules('type', 'type', 'trim|required|max_length[3]');
    $this->form_validation->set_rules('name', 'name', 'trim|required|max_length[100]');
    $this->form_validation->set_rules('phone', 'phone', 'trim|required|max_length[15]');
    $this->form_validation->set_rules('email', 'email', 'trim|required|max_length[100]');
    $this->form_validation->set_rules('password', 'password', 'trim|required|max_length[50]');
    if ($this->form_validation->run() == FALSE){
      // Form Validation Errors
      $response_data = array(
        'error'     => $this->form_validation->error_array()
      );
      $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
    } else {
      // LOGIC_REGISTER
      // var_dump($dt_post);echo '<br>';
      // INIT_INSTITUTE | id | code | name | address | phone | email | type | logo
      $dt_institute = array(
        'name'  => $dt_post['name'],
        'phone' => $dt_post['phone'],
        'email' => $dt_post['email'],
        'type'  => $dt_post['type']
      );
      // INIT_INSTITUTE_BRANCH | id | institute_id | code | name | phone | email | logo  
      $dt_institute_branch = array(
        'institute_id' => $this->institute_id,
        'name'         => $dt_post['name'],
        'phone'        => $dt_post['phone'],
        'email'        => $dt_post['email'],
      );
      // INIT_HELPER  | id | institute_branch_id | code | name | variable | description | is_delete
      /**
      * 'IT'
      * 'Management'
      * 'Pendaftaran'
      * 'Rekam Medik'
      * 'Rawat Jalan'
      * 'Rawat Inap'
      * 'IGD'
      * 'Laboratorium'
      * 'Radiologi'
      * 'Farmasi'
      * 'Gizi'
      **/
      $dt_helper = array(
        'institute_branch_id' => $this->institute_branch_id,
        'code'                => $this->code,
        'name'                => $dt_post['name'],
        'variable'            => $this->variable,
        'description'         => $this->description
      );
      // INIT_HEALTH_FACILITY | id | institute_branch_id | name | is_delete
      $dt_health_facility = array(
        'institute_branch_id' => $this->institute_branch_id,
        'name'                => $dt_post['name']
      );
      // INIT_POSITION | id | institute_branch_id | health_facility_id | name | is_delete
      $dt_init_position = array(
        'institute_branch_id' => $this->institute_branch_id,
        'health_facility_id'  => $this->health_facility_id,
        'name'                => $dt_post['name']
      );
      var_dump($dt_institute);echo '<br>';
      var_dump($dt_institute_branch);echo '<br>';
      var_dump($dt_helper);echo '<br>';
      var_dump($dt_health_facility);echo '<br>';
      var_dump($dt_init_position);echo '<br>';
    }
  }

  /**
   * GET Institute Profile with API
   * -------------------------
   * @method: GET
   */
  public function profile_get() 
  {
    header("Access-Control-Allow-Origin: *");
    $rescode    = 401;
    $message    = array();
    $error      = true;
    $message    = [
      'message' => 'Unauthorized',
      'status'  => FALSE
    ];
    /**
     * User Token Validation
     */
    $is_valid_token = $this->authorization_token->validateToken();
    if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
        // Query Params
        $params = array();
        $id     = $this->security->xss_clean($is_valid_token['data']->id);
        // end Query Params
        $output = $this->User_m->read_by_user_id($id);
        $output_user_user_group = $this->User_user_group_m->read_by_user_id($id);
        if ($output) {
          foreach ($output as $key => $value) {
            $output[$key] = array(
              'id' => $value->id, 
              'type' => $value->type, 
              'email' => $value->email, 
              'name' => $value->name, 
              'sex' => $value->sex, 
              'address' => $value->address, 
              'phone' => $value->phone, 
              'device' => $value->device, 
              'last_login' => $value->last_login, 
              'last_ip' => $value->last_ip, 
              'last_device' => $value->last_device, 
              'oauth' => $value->oauth, 
              'status' => $value->status, 
              'user_user_group'=> $output_user_user_group
            );
          }
          $message = [
            'message' => "SUCCESS",
            'status' => true,
            'data' => $output[0]
          ];
          $error   = false;
          $rescode = 200;
        }
        // Success 200 Code Send
        if ($error == true) {
          if ($rescode == 422){
            $this->response($message, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
          } 
        } else {
          $this->response($message, REST_Controller::HTTP_OK);
        }
        return;
    } else {
      $this->response($message, REST_Controller::HTTP_UNAUTHORIZED);
      return;
    }
  }
}