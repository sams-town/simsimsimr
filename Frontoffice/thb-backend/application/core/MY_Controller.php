<?php defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Writer\Ods\Thumbnails;
use Restserver\Libraries\REST_Controller;
require APPPATH . '/libraries/REST_Controller.php';
class MY_Controller extends REST_Controller
{
  	public $data;
	public $basename;
	public $directory;
	public $tablename;
	public $name;
	public $action;
	public $title;
	public $method;
	public $menu = array();
	public $create_rules = array();
    public $update_rules = array();
	public $delete_rules = array();
	public $errors = array();
    public $debug_uniq;
    public $start_time;
    public $end_time;

	public function __construct() {
		parent::__construct();
        $this->start_time = microtime(true);
		
		$this->data['page'] = 'home';
		if (empty($this->name)) {
			$this->name = get_class($this);
		} 
		
		$this->directory = $this->router->directory;
		$this->basename = strtolower(get_class($this));
		$this->set('directory', strtolower($this->directory));
		$this->set('name', strtolower($this->name));
		$module = trim(strtolower($this->router->directory), '/');
		$controller = strtolower($this->name);
		$action = strtolower($this->router->fetch_method());
		date_default_timezone_set('Asia/Jakarta');
        $this->load->helper('logger');
        $this->load->helper('logger_performance');
        $this->load->library('Authorization_Token');
        set_error_handler(array($this, 'errorHandler'), E_ALL);

        // generate debug unique key
        $bytes = random_bytes(6 / 2);
        $this->debug_uniq = substr(bin2hex($bytes), 0, 6);
        
        Logger::info($this->debug_uniq, '---------------------- ' . strtolower($_SERVER['REQUEST_METHOD']) . ' request');

        Logger::info($this->debug_uniq, 'endpoint: ' . $_SERVER['REQUEST_URI']);

        if($_SERVER['REQUEST_METHOD'] == 'GET') {
            // Logger::info($this->debug_uniq, 'request data: ');
        }

        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        Logger::info($this->debug_uniq, 'query string:', $q_strings);

        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            $user_id = $this->security->xss_clean($is_valid_token['data']->id);
            $username = $this->security->xss_clean($is_valid_token['data']->username);
            $ip = $this->get_client_ip();

            Logger::info($this->debug_uniq, 'institute_branch_id: ' . $institute_branch_id);
            Logger::info($this->debug_uniq, 'user_id: ' . $user_id);
            Logger::info($this->debug_uniq, 'username: ' . $username);
            Logger::info($this->debug_uniq, 'ip: ' . $ip);
        }
	}
		
	public function set($key, $value) {
		$this->data[$key] = $value;
	}

  	public function get_client_ip() {
    	$ipaddress = '';
		if (getenv('HTTP_CLIENT_IP'))
			$ipaddress = getenv('HTTP_CLIENT_IP');
		else if(getenv('HTTP_X_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		else if(getenv('HTTP_X_FORWARDED'))
			$ipaddress = getenv('HTTP_X_FORWARDED');
		else if(getenv('HTTP_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_FORWARDED_FOR');
		else if(getenv('HTTP_FORWARDED'))
			$ipaddress = getenv('HTTP_FORWARDED');
		else if(getenv('REMOTE_ADDR'))
			$ipaddress = getenv('REMOTE_ADDR');
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}
	
  	public function xss_clean($DATA,$SERVER){
		// XSS CLEAN  N RETURN
		return array('dt_postput'=>$this->security->xss_clean($DATA),'dt_server'=>$this->security->xss_clean($SERVER));
	}
	
	public function json_post(){
		// HEADER
		header('Access-Control-Allow-Origin: *');
		// POST JSON DECODE
		$_POST = json_decode(file_get_contents('php://input'), true) ?? [];
        Logger::info($this->debug_uniq, 'raw request:', $_POST);
		// CHECK XSS CLEAN N RETURN
		return $this->xss_clean($_POST, $_SERVER);
	}
	
	public function json_put(){
		// HEADER
		header('Access-Control-Allow-Origin: *');
		// POST JSON DECODE
		$_PUT = json_decode(file_get_contents('php://input'), true) ?? [];
        Logger::info($this->debug_uniq, 'raw request:', $_PUT);
		// CHECK XSS CLEAN N RETURN
		return $this->xss_clean($_PUT, $_SERVER);
	}

	public function REST_Return($status = 404, $message = 'NOT FOUND', $response_data = array()) {
        // LOGGING PERFORMANCE
        $this->end_time = microtime(true);
        $executionTime = $this->end_time - $this->start_time;
        $formattedTime = number_format($executionTime, 3, '.', '');

        $query_latency = ENVIRONMENT == 'development' ? 1 : 3;

        // LOG PERFORMANCE IF RESPONSE TIME 5 SECS OR MORE
        if ((float) $formattedTime >= 5) {
            Logger_performance::info($this->debug_uniq, $_SERVER['REQUEST_URI']);
            $queries = $this->db->queries;
            $times = $this->db->query_times;
            for ($i=0; $i < count($times); $i++) { 
                $query_time = $times[$i];
                if ($query_time >= $query_latency) { // QUERY TOOK MORE THAN 3 SECS
                    Logger_performance::info($this->debug_uniq, $query_time . ', query: ' . $queries[$i]);
                }
            }
            Logger_performance::info($this->debug_uniq, 'this request took ' . $formattedTime . ' seconds');
        }

        $response    = array(
            'status'        => $status,
            'message'       => $message,
            'debug'         => $this->debug_uniq,
            'data'          => $response_data,
            'error_notice'  => $this->errors,
        );

		if ($status == 422) {
            $this->response($response, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
		} else if ($status == 200) {
            $this->response($response, REST_Controller::HTTP_OK);
		} else if ($status == 201) {
            $this->response($response, REST_Controller::HTTP_CREATED);
		} else if ($status == 400) {
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        } else if ($status == 401) {
            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
		} else if ($status == 404) {
		    $this->response($response, REST_Controller::HTTP_NOT_FOUND);
		} else if ($status == 409) {
		    $this->response($response, REST_Controller::HTTP_CONFLICT);
        } else if ($status == 500) {
            $this->response($response, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
		}

        if (strtolower($_SERVER['REQUEST_METHOD']) == 'post' || strtolower($_SERVER['REQUEST_METHOD']) == 'put') {
            if ($status == 422 || $status == 400 || $status == 401 || $status == 404 || $status == 409 || $status == 500) {
                Logger::error($this->debug_uniq, 'response: ', $response);
            } else {
                Logger::info($this->debug_uniq, 'response: ', $response);
            }
        } else {
            if ($status == 422 || $status == 400 || $status == 401 || $status == 404 || $status == 409 || $status == 500) {
                Logger::error($this->debug_uniq, 'response: ', $response);
            } else {
                Logger::info($this->debug_uniq, 'response: ', []);
            }
        }
	}

    function errorHandler($errno, $errstr, $errfile, $errline) {
        // Log error details or perform custom action
        $error_msg = "Error: [$errno] $errstr in $errfile on line $errline";

        $this->errors[] = $error_msg;

        // $this->REST_Return(500, "Internal Server Error", $error_msg);
    
        // You can customize the output or decide to exit the script if needed
        // if ($errno === E_USER_ERROR) {
        //     echo "Fatal error on line $errline in file $errfile";
        //     exit(1);
        // }
    
        return true; // Return true to indicate that the error has been handled
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
            if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
            if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
            $this->$model->whereFields = $q_strings;
            $this->$model->whereFields['a.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['a.is_delete'] = '0';
            $output = $this->$model->read($id);
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

    public function list_get($p = 0){
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
            $this->$model->whereLike = $q_strings;
            $this->$model->whereFields['a.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['a.is_delete'] = '0';
            $output = $this->$model->read_ssr($id);
            $response_data = array();
            if ($output->rows != [] && $id != 0) {
                $response_data = $output->rows[0];
                $error   = false;
            } else if($output->rows != [] or $id == 0) {
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
            $this->form_validation->set_rules($this->create_rules);
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
				$model = $this->name;
				/* Start Transaction */
				$this->db->trans_start();
				$want_to_create = array(
					'institute_branch_id'  => $institute_branch_id,
				);
				foreach ($dt_post as $key => $value) {
					$want_to_create[$key] = $value;
				}
				$id = $this->$model->create($want_to_create);
				$this->db->trans_complete();
				/* End Transaction */
				if ($this->db->trans_status() === false) {
					$response_data = array(
						'error'   => $this->db->trans_status()
					);
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

	public function update_put($p = 0) {
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
            $this->form_validation->set_rules($this->update_rules);
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                if(count($this->$model->read($id)) == 1) {
                    $want_to_update = array(
                        'id' => $id,
                    );
					foreach ($dt_put as $key => $value) {
						$want_to_update[$key] = $value;
					}
                    $id = $this->$model->update($id, $want_to_update);
                } else {
                    $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
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

	public function delete_delete($p = 0) {
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
            $user_id = $this->security->xss_clean($is_valid_token['data']->id);
            # Form Validation
            $this->form_validation->set_data($dt_put);
            $this->form_validation->set_rules($this->delete_rules);
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                if($this->$model->read($id) != []) {
                    $want_to_delete = array(
                        $this->$model->primaryKey => $id,
                    );
					foreach ($dt_put as $key => $value) {
						$want_to_delete[$key] = $value;
					}
                    $want_to_delete['user_deleted'] = $user_id;
                    $want_to_delete['date_deleted'] = date('Y-m-d H:i:s');
                    $id = $this->$model->update($id, $want_to_delete);
                } else {
                    $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
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
}