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
    $this->load->model("v1/{$this->name}_model", "{$this->name}");
    $this->load->model("v1/hr/Employee_model", "Employee");
    $this->load->model("v1/hr/Position_model", "Position");
    $this->load->model("v1/hr/Employee_position_model", "Employee_position");
    $this->load->model("v1/setting/User_model", "User");
    $this->load->model("v1/setting/Module_model", "Module");
    $this->load->model("v1/setting/Module_item_model", "Module_item");
    $this->load->model("v1/setting/Helper_model", "Helper");
    $this->load->model("v1/setting/Group_model", "Group");
    $this->load->model("v1/setting/User_group_model", "User_group");
    $this->load->model("v1/setting/Group_module_model", "Group_module");
    $this->load->model("v1/setting/Health_facility_model", "Health_facility");
    $this->load->model("v1/setting/Health_facility_item_model", "Health_facility_item");
    $this->load->model("v1/setting/Service_class_model", "Service_class");
    $this->load->model("v1/third_party/Company_model", "Company");
    $this->load->model("v1/setting/How_to_enter_model", "How_to_enter");
    $this->load->model("v1/setting/Exits_model", "Exits");
    $this->load->model("v1/finance/Costing_model", "Costing");
    $this->load->model("v1/finance/Costing_services_class_model", "Costing_services_class");
    $this->load->model("v1/setting/Service_class_model", "Service_class");
    $this->load->model("v1/Institute_model", "Institute");
    $this->load->model("v1/Institute_branch_model", "Institute_branch");
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

  public function register_post() {
    // CALL JSON POST
    $data       = $this->json_post();
    // DEFINE ATTR
    $dt_post    = $data['dt_postput'];
    $device     = $data['dt_server']['HTTP_USER_AGENT'];
    $ip         = $this->get_client_ip();
    $error      = TRUE;
    # Form Validation
    $this->form_validation->set_rules('type_sys', 'type_sys', 'trim|required|max_length[3]');
    $this->form_validation->set_rules('type_branch', 'type_branch', 'trim|required|max_length[3]');
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
      $this->db->trans_start();
        // INIT_INSTITUTE | id | code | name | address | phone | email | type_sys | logo
          $dt_institute = array(
            'code'  => $dt_post['code'],
            'name'  => $dt_post['name'],
            'phone' => $dt_post['phone'],
            'email' => $dt_post['email'],
            'type_sys'  => $dt_post['type_sys'],
            'type_branch'  => $dt_post['type_branch']
          );
          $this->institute_id = $this->Institute->create($dt_institute);
          // $this->institute_id = 1;
        // INIT_INSTITUTE_BRANCH | id | institute_id | code | name | phone | email | logo
          if ($this->institute_id) {
            $dt_institute_branch = array(
              'institute_id' => $this->institute_id,
              'code'         => $dt_post['code'],
              'name'         => $dt_post['name'],
              'phone'        => $dt_post['phone'],
              'email'        => $dt_post['email'],
            );
          }
          // $this->institute_branch_id = '2e084aa0-9f25-4ec4-a08c-e47b793079f6';
          $this->institute_branch_id = $this->Institute_branch->create($dt_institute_branch);
        // START INITIAL DATABASE
          if ($this->institute_branch_id) {
            // CONFIG INIT
              if ($dt_post['type_sys'] == 'HIS' && $dt_post['type_branch'] == 'STL') {
                $flag_init = 'his_stl';
              } else if ($dt_post['type_sys'] == 'HIS' && $dt_post['type_branch'] == 'LTD') {
                $flag_init = 'his_ltd';
              } else if ($dt_post['type_sys'] == 'CIS' && $dt_post['type_branch'] == 'STL') {
                $flag_init = 'cis_stl';
              } else if ($dt_post['type_sys'] == 'CIS' && $dt_post['type_branch'] == 'LTD') {
                $flag_init = 'cis_ltd';
              }
            
            // INIT_HELPER  | id | institute_branch_id | code | name | variable | description | is_delete
              $dt_init_to_helper = $this->Initial->whereFields['a.tbl'] = 'helper';
              $dt_init_to_helper = $this->Initial->whereFields['a.flag'] = $flag_init;
              $dt_init_to_helper = $this->Initial->read();
              foreach ($dt_init_to_helper as $d_ith_key => $d_ith) {
                $dt_helper[$d_ith_key] = array(
                  'institute_branch_id' => $this->institute_branch_id,
                  'code'                => $d_ith->variable_1,
                  'name'                => $d_ith->variable_2,
                  'variable'            => $d_ith->variable_3,
                  'description'         => $d_ith->variable_4
                );
              }
              $this->Helper->insert_batch($dt_helper);
            // INIT_HELPER  | id | institute_branch_id | code | name | variable | description | is_delete
            
            // INIT_MODULE | id | institute_branch_id | name | is_delete
              $dt_init_to_module = $this->Initial->whereFields['a.tbl'] = 'module';
              $dt_init_to_module = $this->Initial->whereFields['a.flag'] = $flag_init;
              $dt_init_to_module = $this->Initial->read();
              $dt_module_id = array();
              for ($key_itm=0; $key_itm < count($dt_init_to_module); $key_itm++) { 
                if ($dt_init_to_module[$key_itm]->variable_1 == $key_itm+1) {
                  ${'dt_module_' . $key_itm} = array(
                    'institute_branch_id' => $this->institute_branch_id,
                    'name'                => $dt_init_to_module[$key_itm]->variable_2,
                    'url'                 => $dt_init_to_module[$key_itm]->variable_3,
                    'icon'                => $dt_init_to_module[$key_itm]->variable_4
                  );
                }
                ${'module_id_' . $key_itm} = $this->Module->create(${'dt_module_' . $key_itm});
                $dt_module_id[$key_itm] = (object) array(
                  'parent_id' => $dt_init_to_module[$key_itm]->variable_1,
                  'module_id' => ${'module_id_' . $key_itm}
                );
              }
              // var_dump($dt_module_id);
            // INIT_MODULE | id | institute_branch_id | name | is_delete

            // INIT_MODULE_ITEM | id | institute_branch_id | module_id | name | is_delete
              $dt_init_to_module_item = $this->Initial->whereFields['a.tbl'] = 'module_item';
              $dt_init_to_module_item = $this->Initial->whereFields['a.flag'] = $flag_init;
              $dt_init_to_module_item = $this->Initial->read();
              $dt_module_item_id = array();
              for ($key_itmi=0; $key_itmi < count($dt_init_to_module_item); $key_itmi++) { 
                if ($dt_init_to_module_item[$key_itmi]->variable_1 == $key_itmi+1) {
                  for ($key_dmi=0; $key_dmi < count($dt_module_id); $key_dmi++) { 
                    if ($dt_init_to_module_item[$key_itmi]->variable_2 == $dt_module_id[$key_dmi]->parent_id) {
                      ${'dt_module_item_' . $key_itmi} = array(
                        'institute_branch_id' => $this->institute_branch_id,
                        'module_id'           => $dt_module_id[$key_dmi]->module_id,
                        'name'                => $dt_init_to_module_item[$key_itmi]->variable_3
                      );
                    }
                  }
                }
                $this->Module_item->create(${'dt_module_item_' . $key_itmi});
                // var_dump(${'dt_module_item_' . $key_itmi});
              }
            // INIT_MODULE_ITEM | id | institute_branch_id | module_id | name | is_delete
            
            // INIT_HEALTH_FACILITY | id | institute_branch_id | name | is_delete
              $dt_init_to_health_facility = $this->Initial->whereFields['a.tbl'] = 'health_facility';
              $dt_init_to_health_facility = $this->Initial->whereFields['a.flag'] = $flag_init;
              $dt_init_to_health_facility = $this->Initial->read();
              $dt_health_facility_id = array();
              for ($key_ithf=0; $key_ithf < count($dt_init_to_health_facility); $key_ithf++) { 
                if ($dt_init_to_health_facility[$key_ithf]->variable_1 == $key_ithf+1) {
                  ${'dt_health_facility_' . $key_ithf} = array(
                    'institute_branch_id' => $this->institute_branch_id,
                    'name'                => $dt_init_to_health_facility[$key_ithf]->variable_2
                  );
                }
                ${'health_facility_id_' . $key_ithf} = $this->Health_facility->create(${'dt_health_facility_' . $key_ithf});
                $dt_health_facility_id[$key_ithf] = (object) array(
                  'parent_id' => $dt_init_to_health_facility[$key_ithf]->variable_1,
                  'health_facility_id' => ${'health_facility_id_' . $key_ithf}
                );
              }
            // INIT_HEALTH_FACILITY | id | institute_branch_id | name | is_delete

            // INIT_HEALTH_FACILITY_ITEM | id | health_facility_id | name | is_delete
              $dt_init_to_health_facility_item = $this->Initial->whereFields['a.tbl'] = 'health_facility_item';
              $dt_init_to_health_facility_item = $this->Initial->whereFields['a.flag'] = $flag_init;
              $dt_init_to_health_facility_item = $this->Initial->read();
              $dt_health_facility_item_id = array();
              for ($key_ithfi=0; $key_ithfi < count($dt_init_to_health_facility_item); $key_ithfi++) { 
                if ($dt_init_to_health_facility_item[$key_ithfi]->variable_1 == $key_ithfi+1) {
                  for ($key_dhfi=0; $key_dhfi < count($dt_health_facility_id); $key_dhfi++) { 
                    if ($dt_init_to_health_facility_item[$key_ithfi]->variable_2 == $dt_health_facility_id[$key_dhfi]->parent_id) {
                      ${'dt_health_facility_item_' . $key_ithfi} = array(
                        'health_facility_id'  => $dt_health_facility_id[$key_dhfi]->health_facility_id,
                        'name'                => $dt_init_to_health_facility_item[$key_ithfi]->variable_3
                      );
                    }
                  }
                }
                ${'health_facility_item_id_' . $key_ithfi} = $this->Health_facility_item->create(${'dt_health_facility_item_' . $key_ithfi});
                $dt_health_facility_item_id[$key_ithfi] = (object) array(
                  'parent_id' => $dt_init_to_health_facility_item[$key_ithfi]->variable_1,
                  'health_facility_item_id' => ${'health_facility_item_id_' . $key_ithfi}
                );
              }
            // INIT_HEALTH_FACILITY_ITEM | id | health_facility_id | name | is_delete

            // INIT_GROUP  | id | institute_branch_id | code | name | variable | description | is_delete
              $dt_init_to_group = $this->Initial->whereFields['a.tbl'] = 'group';
              $dt_init_to_group = $this->Initial->whereFields['a.flag'] = $flag_init;
              $dt_init_to_group = $this->Initial->read();
              $dt_group_id = array();
              foreach ($dt_init_to_group as $d_itg_key => $d_itg) {
                $dt_group = array(
                  'institute_branch_id' => $this->institute_branch_id,
                  'name'                => $d_itg->variable_2,
                );
                ${'group_id_' . $d_itg_key} = $this->Group->create($dt_group);
                $dt_group_id[$d_itg_key] = (object) array(
                  'parent_id' => $d_itg->variable_1,
                  'group_id' => ${'group_id_' . $d_itg_key}
                );
              }
            // INIT_GROUP  | id | institute_branch_id | code | name | variable | description | is_delete
            
            // INIT_GROUP_MODULE | group_id | module_id 
              $dt_init_to_group_module = $this->Initial->whereFields['a.tbl'] = 'group_module';
              $dt_init_to_group_module = $this->Initial->whereFields['a.flag'] = $flag_init;
              $dt_init_to_group_module = $this->Initial->read();
              for ($key_igm=0; $key_igm < count($dt_init_to_group_module); $key_igm++) { 
                for ($key_dmm=0; $key_dmm < count($dt_module_id); $key_dmm++) { 
                  if ($dt_init_to_group_module[$key_igm]->variable_3 == $dt_module_id[$key_dmm]->parent_id) {
                    for ($key_dmg=0; $key_dmg < count($dt_group_id); $key_dmg++) { 
                      if ($dt_init_to_group_module[$key_igm]->variable_2 == $dt_group_id[$key_dmg]->parent_id) {
                        ${'dt_group_module_' . $key_igm} = array(
                          'group_id'            => $dt_group_id[$key_dmg]->group_id,
                          'module_id'           => $dt_module_id[$key_dmm]->module_id
                        );
                      }
                    }
                  }
                }
                $this->Group_module->replace(${'dt_group_module_' . $key_igm});
              }
            // INIT_GROUP_MODULE | id | institute_branch_id | module_id | name | is_delete

            // INIT_POSITION | id | institute_branch_id | health_facility_item_id | name | is_delete
              $dt_init_to_position = $this->Initial->whereFields['a.tbl'] = 'position';
              $dt_init_to_position = $this->Initial->whereFields['a.flag'] = $flag_init;
              $dt_init_to_position = $this->Initial->read();
              $dt_position_id = array();
              for ($key_ip=0; $key_ip < count($dt_init_to_position); $key_ip++) { 
                if ($dt_init_to_position[$key_ip]->variable_1 == $key_ip+1) {
                  for ($key_dhfi=0; $key_dhfi < count($dt_health_facility_item_id); $key_dhfi++) { 
                    if ($dt_init_to_position[$key_ip]->variable_2 == $dt_health_facility_item_id[$key_dhfi]->parent_id) {
                      ${'dt_position_' . $key_ip} = array(
                        'institute_branch_id'       => $this->institute_branch_id,
                        'health_facility_item_id'   => $dt_health_facility_item_id[$key_dhfi]->health_facility_item_id,
                        'name'                      => $dt_init_to_position[$key_ip]->variable_3
                      );
                    }
                  }
                }
                ${'position_id_' . $key_ip} = $this->Position->create(${'dt_position_' . $key_ip});
                $dt_position_id[$key_ip] = (object) array(
                  'parent_id' => $dt_init_to_position[$key_ip]->variable_1,
                  'position_id' => ${'position_id_' . $key_ip}
                );
              }
            // INIT_POSITION | id | health_facility_id | name | is_delete

            // INIT_EMPLOYEE | id | institute_branch_id | f_title | name | fullname | b_title | birth_date | sex | address | is_delete
              $dt_init_to_employee = $this->Initial->whereFields['a.tbl'] = 'employee';
              $dt_init_to_employee = $this->Initial->whereFields['a.flag'] = $flag_init;
              $dt_init_to_employee = $this->Initial->read();
              $dt_employee_id = array();
              for ($key_ie=0; $key_ie < count($dt_init_to_employee); $key_ie++) { 
                if ($dt_init_to_employee[$key_ie]->variable_1 == $key_ie+1) {
                  ${'dt_employee_' . $key_ie} = array(
                    'institute_branch_id'       => $this->institute_branch_id,
                    'name'                      => $dt_init_to_employee[$key_ie]->variable_2
                  );
                }
                ${'employee_id_' . $key_ie} = $this->Employee->create(${'dt_employee_' . $key_ie});
                $dt_employee_id[$key_ie] = (object) array(
                  'parent_id' => $dt_init_to_employee[$key_ie]->variable_1,
                  'employee_id' => ${'employee_id_' . $key_ie}
                );
              }
            // INIT_EMPLOYEE | id | health_facility_id | f_title | name | fullname | b_title | birth_date | sex | address | is_delete
            
            // INIT_EMPLOYEE_POSITION | employee_id | position_id | date_expired
              $dt_init_to_employee_position = $this->Initial->whereFields['a.tbl'] = 'employee_position';
              $dt_init_to_employee_position = $this->Initial->whereFields['a.flag'] = $flag_init;
              $dt_init_to_employee_position = $this->Initial->read();
              for ($key_itep=0; $key_itep < count($dt_init_to_employee_position); $key_itep++) { 
                for ($key_dmm=0; $key_dmm < count($dt_position_id); $key_dmm++) { 
                  if ($dt_init_to_employee_position[$key_itep]->variable_3 == $dt_position_id[$key_dmm]->parent_id) {
                    for ($key_dmg=0; $key_dmg < count($dt_employee_id); $key_dmg++) { 
                      if ($dt_init_to_employee_position[$key_itep]->variable_2 == $dt_employee_id[$key_dmg]->parent_id) {
                        ${'dt_employee_position_' . $key_itep} = array(
                          'employee_id'           => $dt_employee_id[$key_dmg]->employee_id,
                          'position_id'           => $dt_position_id[$key_dmm]->position_id,
                          'date_expired'          => $dt_init_to_employee_position[$key_itep]->variable_4
                        );
                      }
                    }
                  }
                }
                $this->Employee_position->replace(${'dt_employee_position_' . $key_itep});
              }
            // INIT_EMPLOYEE_POSITION | employee_id | position_id | date_expired

            // INIT_USER | id | institute_branch_id | employee_id | username | password sha1
              $dt_init_to_user = $this->Initial->whereFields['a.tbl'] = 'user';
              $dt_init_to_user = $this->Initial->whereFields['a.flag'] = $flag_init;
              $dt_init_to_user = $this->Initial->read();
              $dt_user_id = array();
              for ($key_itu=0; $key_itu < count($dt_init_to_user); $key_itu++) { 
                if ($dt_init_to_user[$key_itu]->variable_1 == $key_itu+1) {
                  for ($key_dhfi=0; $key_dhfi < count($dt_employee_id); $key_dhfi++) { 
                    if ($dt_init_to_user[$key_itu]->variable_2 == $dt_employee_id[$key_dhfi]->parent_id) {
                      ${'dt_user_' . $key_itu} = array(
                        'institute_branch_id'       => $this->institute_branch_id,
                        'employee_id'               => $dt_employee_id[$key_dhfi]->employee_id,
                        'username'                  => $dt_init_to_user[$key_itu]->variable_3,
                        'password'                  => sha1($dt_post['password'])
                      );
                    }
                  }
                }
                ${'user_id_' . $key_itu} = $this->User->create(${'dt_user_' . $key_itu});
                $dt_user_id[$key_itu] = (object) array(
                  'parent_id' => $dt_init_to_user[$key_itu]->variable_1,
                  'user_id' => ${'user_id_' . $key_itu}
                );
              }
            // INIT_USER | id | institute_branch_id | employee_id | username | password sha1
            
            // INIT_USER_GROUP | group_id | module_id 
              $dt_init_to_user_group = $this->Initial->whereFields['a.tbl'] = 'user_group';
              $dt_init_to_user_group = $this->Initial->whereFields['a.flag'] = $flag_init;
              $dt_init_to_user_group = $this->Initial->read();
              for ($key_igm=0; $key_igm < count($dt_init_to_user_group); $key_igm++) { 
                for ($key_dmm=0; $key_dmm < count($dt_group_id); $key_dmm++) { 
                  if ($dt_init_to_user_group[$key_igm]->variable_3 == $dt_group_id[$key_dmm]->parent_id) {
                    for ($key_dmg=0; $key_dmg < count($dt_user_id); $key_dmg++) { 
                      if ($dt_init_to_user_group[$key_igm]->variable_2 == $dt_user_id[$key_dmg]->parent_id) {
                        ${'dt_user_group_' . $key_igm} = array(
                          'user_id'            => $dt_user_id[$key_dmg]->user_id,
                          'group_id'           => $dt_group_id[$key_dmm]->group_id
                        );
                      }
                    }
                  }
                }
                $this->User_group->replace(${'dt_user_group_' . $key_igm});
              }
            // INIT_USER_GROUP | id | institute_branch_id | module_id | name | is_delete

            // INIT_SERVICE_CLASS  | id | institute_branch_id | code | name | is_delete
              $dt_init_to_service_class = $this->Initial->whereFields['a.tbl'] = 'service_class';
              $dt_init_to_service_class = $this->Initial->whereFields['a.flag'] = $flag_init;
              $dt_init_to_service_class = $this->Initial->read();
              $dt_service_class_id = array();
              foreach ($dt_init_to_service_class as $d_itg_key => $d_itg) {
                $dt_service_class = array(
                  'institute_branch_id' => $this->institute_branch_id,
                  'name'                => $d_itg->variable_2,
                );
                ${'service_class_id_' . $d_itg_key} = $this->Service_class->create($dt_service_class);
                $dt_service_class_id[$d_itg_key] = (object) array(
                  'parent_id' => $d_itg->variable_1,
                  'service_class_id' => ${'service_class_id_' . $d_itg_key}
                );
              }
            // INIT_SERVICE_CLASS  | id | institute_branch_id | code | name | is_delete

            // INIT_COMPANY  | id | institute_branch_id | name | flag | is_delete
              $dt_init_to_company = $this->Initial->whereFields['a.tbl'] = 'company';
              $dt_init_to_company = $this->Initial->whereFields['a.flag'] = $flag_init;
              $dt_init_to_company = $this->Initial->read();
              $dt_company_id = array();
              foreach ($dt_init_to_company as $d_itg_key => $d_itg) {
                $dt_company = array(
                  'institute_branch_id' => $this->institute_branch_id,
                  'name'                => $d_itg->variable_3,
                  'flag'                => $d_itg->variable_2,
                );
                ${'company_id_' . $d_itg_key} = $this->Company->create($dt_company);
              }
            // INIT_COMPANY  | id | institute_branch_id | name | flag | is_delete

            // INIT_HOW_TO_ENTER  | id | institute_branch_id | name | type_how_to_enter_id | is_delete
              $dt_init_to_how_to_enter = $this->Initial->whereFields['a.tbl'] = 'how_to_enter';
              $dt_init_to_how_to_enter = $this->Initial->whereFields['a.flag'] = $flag_init;
              $dt_init_to_how_to_enter = $this->Initial->read();
              $dt_how_to_enter_id = array();
              foreach ($dt_init_to_how_to_enter as $d_itg_key => $d_itg) {
                $dt_how_to_enter = array(
                  'institute_branch_id' => $this->institute_branch_id,
                  'type_how_to_enter_id'=> $d_itg->variable_2,
                  'name'                => $d_itg->variable_3,
                );
                ${'how_to_enter_id_' . $d_itg_key} = $this->How_to_enter->create($dt_how_to_enter);
              }
            // INIT_HOW_TO_ENTER  | id | institute_branch_id | name | type_how_to_enter_id | is_delete

            // INIT_EXIT  | id | institute_branch_id | name | is_delete
              $dt_init_to_exit = $this->Initial->whereFields['a.tbl'] = 'exit';
              $dt_init_to_exit = $this->Initial->whereFields['a.flag'] = $flag_init;
              $dt_init_to_exit = $this->Initial->read();
              $dt_exit_id = array();
              foreach ($dt_init_to_exit as $d_itg_key => $d_itg) {
                $dt_exit = array(
                  'institute_branch_id' => $this->institute_branch_id,
                  'name'                => $d_itg->variable_2,
                );
                ${'exit_id_' . $d_itg_key} = $this->Exits->create($dt_exit);
              }
            // INIT_EXIT  | id | institute_branch_id | name | is_delete

            // INIT_COSTING  | id | institute_branch_id | type_costing_id | code | name | is_delete
              $dt_init_to_costing = $this->Initial->whereFields['a.tbl'] = 'costing';
              $dt_init_to_costing = $this->Initial->whereFields['a.flag'] = $flag_init;
              $dt_init_to_costing = $this->Initial->read();
              $dt_costing_id = array();
              foreach ($dt_init_to_costing as $d_itg_key => $d_itg) {
                $dt_costing = array(
                  'institute_branch_id' => $this->institute_branch_id,
                  'type_costing_id'     => $d_itg->variable_2,
                  'name'                => $d_itg->variable_3,
                );
                ${'costing_id_' . $d_itg_key} = $this->Costing->create($dt_costing);
                $dt_costing_id[$d_itg_key] = (object) array(
                  'parent_id' => $d_itg->variable_1,
                  'costing_id' => ${'costing_id_' . $d_itg_key}
                );
              }
            // INIT_COSTING  | id | institute_branch_id | type_costing_id | code | name | is_delete

            // INIT_COSTING_SERVICES_CLASS | costing_id | service_class_id | var_1 | var_2 | var_3 | var_4 | var_5 | var_6 | var_7 | var_8
              $dt_init_to_costing_services_class = $this->Initial->whereFields['a.tbl'] = 'costing_services_class';
              $dt_init_to_costing_services_class = $this->Initial->whereFields['a.flag'] = $flag_init;
              $dt_init_to_costing_services_class = $this->Initial->read();
              for ($key_itep=0; $key_itep < count($dt_init_to_costing_services_class); $key_itep++) { 
                for ($key_dmm=0; $key_dmm < count($dt_service_class_id); $key_dmm++) { 
                  if ($dt_init_to_costing_services_class[$key_itep]->variable_3 == $dt_service_class_id[$key_dmm]->parent_id) {
                    for ($key_dmg=0; $key_dmg < count($dt_costing_id); $key_dmg++) { 
                      if ($dt_init_to_costing_services_class[$key_itep]->variable_2 == $dt_costing_id[$key_dmg]->parent_id) {
                        ${'dt_costing_services_class_' . $key_itep} = array(
                          'costing_id'          => $dt_costing_id[$key_dmg]->costing_id,
                          'service_class_id'    => $dt_service_class_id[$key_dmm]->service_class_id,
                          'var_1'               => $dt_init_to_costing_services_class[$key_itep]->variable_4
                        );
                      }
                    }
                  }
                }
                $this->Costing_services_class->replace(${'dt_costing_services_class_' . $key_itep});
              }
            // INIT_COSTING_SERVICES_CLASS | costing_id | service_class_id | var_1 | var_2 | var_3 | var_4 | var_5 | var_6 | var_7 | var_8
          }
        // START INITIAL DATABASE
      $this->db->trans_complete();
      /* End Transaction */
      if ($this->db->trans_status() === false) {
        $response_data = array(
          'error'   => $this->db->trans_status()
        );
        $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
      } else {
        $response_data = array(
          'id'     => $this->institute_id
        );
        $this->REST_Return(200, 'SUCCESS', $response_data);
      }
      return;     
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
              'type_sys' => $value->type_sys, 
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