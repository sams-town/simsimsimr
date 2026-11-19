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
    public $s1_actor = null;
    public $delete_decision = null;

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

        // S1: correlation id dari index.php supaya request_id di response == request_id di log
        $this->debug_uniq = defined('REQUEST_ID') ? REQUEST_ID : substr(bin2hex(random_bytes(3)), 0, 6);
        // S1: TypeError/Error tidak tertangkap catch(Exception) di REST_Controller, jadi
        // handler ini yang mencatat + mengirim 500 aman (sebelumnya exit bersih => HTTP 200 kosong).
        set_exception_handler(array($this, 'exceptionHandler'));
        
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
            // S1: identitas actor untuk log (id/username/branch saja, token tidak pernah dicatat)
            $this->s1_actor = array(
                'user_id'  => $user_id,
                'username' => $username,
                'branch'   => $institute_branch_id,
            );
            Logger::info($this->debug_uniq, 'user_id: ' . $user_id);
            Logger::info($this->debug_uniq, 'username: ' . $username);
            Logger::info($this->debug_uniq, 'ip: ' . $ip);

            // ===== P1-b: capability map + authorization backend =====
            // Dijalankan SEBELUM handler, jadi sebelum UPDATE/DELETE dan sebelum side effect
            // eksternal. Hanya untuk DELETE dan PUT .../cancel; GET/POST/PUT biasa tidak diperiksa.
            $this->guard_delete_operation($user_id, $username, $institute_branch_id);

            // Tahap 1 deployment readiness: role guard minimal untuk POST/PUT/DELETE
            // pada modul terdaftar (farmasi, piutang, surgery, registrasi).
            $this->guard_role_operation($user_id, $username, $institute_branch_id);
        }
	}
		
	public function set($key, $value) {
		$this->data[$key] = $value;
	}

    /**
     * P1-b: penegakan capability map + authorization untuk delete/cancel.
     *
     * Cakupan sengaja sempit supaya operasi normal tidak berubah:
     *   - semua HTTP DELETE  -> selalu diperiksa
     *   - HTTP PUT .../cancel -> diperiksa
     *   - selain itu (GET/POST/PUT update) -> tidak diperiksa
     *
     * Handler diturunkan dari segmen URI (bukan dari router state) supaya tidak
     * bisa "gagal terbuka" saat route ditulis ulang: kalau nama aksi tidak dapat
     * ditentukan, handler menjadi "_delete" yang tidak ada di map => 422.
     */
    protected function guard_delete_operation($user_id, $username, $branch) {
        $method = strtolower($_SERVER['REQUEST_METHOD']);

        $segments   = array_values($this->uri->segment_array());
        $controller = strtolower($this->name);
        $action_seg = '';
        $object_id  = '';
        foreach ($segments as $i => $seg) {
            if (strtolower($seg) === $controller) {
                $action_seg = isset($segments[$i + 1]) ? strtolower($segments[$i + 1]) : '';
                $object_id  = isset($segments[$i + 2]) ? $segments[$i + 2] : '';
                break;
            }
        }

        $is_guarded = ($method === 'delete') || ($method === 'put' && $action_seg === 'cancel');
        if (!$is_guarded) {
            return;
        }

        $handler = $action_seg . '_' . $method;

        $this->load->library('Delete_policy');
        $endpoint = Delete_policy::endpoint_key($this->directory, $this->name);
        $actor    = array('user_id' => $user_id, 'username' => $username, 'branch' => $branch);

        $decision = $this->delete_policy->authorize($endpoint, $handler, $object_id, $method, $actor);

        // Audit wajib untuk allow maupun deny. Gagal tulis audit => operasi dibatalkan.
        if (!$this->delete_policy->audit($decision, $actor, $endpoint, $object_id, $method)) {
            Logger::error($this->debug_uniq, 'audit write failed for ' . $endpoint);
            $this->REST_Return(500, 'Terjadi kesalahan pada server.', array(), 'AUDIT_WRITE_FAILED');
            $this->_flush_and_stop();
        }

        $this->delete_decision = $decision;
        Logger::info($this->debug_uniq, 'authz: ' . $endpoint . ' ' . $handler . ' => '
            . ($decision['allowed'] ? 'ALLOW' : 'DENY ' . $decision['code']));

        if (!$decision['allowed']) {
            $this->REST_Return($decision['http'], $decision['message'], array(
                'endpoint'  => $endpoint,
                'operation' => $decision['operation'],
            ), $decision['code']);
            $this->_flush_and_stop();
        }
    }

    /**
     * Tahap 1 — ROLE GUARD MINIMAL (POST/PUT/DELETE).
     *
     * Model peran FO: user -> user_group -> group. Endpoint yang terdaftar di
     * config/role_policy.php wajib dimiliki minimal satu group yang diizinkan.
     * User tanpa baris `user` (tidak dikenal) SELALU ditolak (fail-closed).
     *
     * GET tidak diperiksa: mode single cabang => satu cabang berarti semua data.
     * TODO: expand branch filter saat multi-cabang
     */
    protected function guard_role_operation($user_id, $username, $branch) {
        $method = strtolower(isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'get');

        // Cabang untuk resolusi group:
        //   1) branch dari token (bila ada) — sama seperti Delete_policy
        //   2) branch milik user sendiri (token lama tanpa claim branch)
        // Mode single cabang tidak boleh membuat user kehilangan rolenya hanya karena
        // token tidak membawa branch. Perilaku penolakan branch tetap diurus guard
        // _ph_branch/_pj_branch/_sg_branch di masing-masing controller.
        // TODO: expand branch filter saat multi-cabang
        $this->config->load('role_policy', TRUE);
        if (!$this->config->item('role_policy_enforce', 'role_policy')) {
            return;
        }
        $methods = $this->config->item('role_policy_methods', 'role_policy');
        $methods = is_array($methods) ? $methods : array('post', 'put', 'delete', 'patch');
        if (!in_array($method, $methods, TRUE)) {
            return;
        }

        $map = $this->config->item('role_policy', 'role_policy');
        if (!is_array($map) || !$map) {
            return;
        }
        // Kunci endpoint identik dengan Delete_policy::endpoint_key(), tetapi disalin
        // di sini supaya guard role tidak memaksa load library policy pada SETIAP
        // request POST/PUT/DELETE (library itu menyentuh DB di konstruktornya).
        $endpoint = strtolower(trim($this->directory, '/') . '/' . $this->name);
        $endpoint = preg_replace('#^v\d+/#', '', $endpoint);
        $endpoint = preg_replace('#/+#', '/', $endpoint);
        $endpoint = trim($endpoint, '/');

        if (!isset($map[$endpoint])) {
            return; // endpoint di luar cakupan policy => perilaku lama
        }

        $capability = $map[$endpoint];
        $groups_map = $this->config->item('role_policy_groups', 'role_policy');
        $allowed    = (is_array($groups_map) && isset($groups_map[$capability]))
            ? $groups_map[$capability] : array();
        // Administrator berhak atas SEMUA capability (admin = apoteker+kasir+dokter+registrasi).
        $admin = $this->config->item('role_policy_admin_groups', 'role_policy');
        if (is_array($admin)) {
            $allowed = array_merge($allowed, $admin);
        }

        // Group nyata milik user (pola Delete_policy::_resolve_actor(): user_group -> group,
        // difilter institute_branch_id). Branch token kosong => pakai branch user sendiri.
        // TODO: expand branch filter saat multi-cabang
        $rows = $this->db->query(
            "SELECT g.name
               FROM user_group ug
               INNER JOIN `group` g ON g.id = ug.group_id
              WHERE ug.user_id = ?
                AND IFNULL(ug.is_delete,0) = 0
                AND IFNULL(g.is_delete,0) = 0
                AND g.institute_branch_id = COALESCE(NULLIF(?,''),
                      (SELECT u.institute_branch_id FROM `user` u WHERE u.id = ?))",
            array($user_id, trim((string) $branch), $user_id))->result();
        $have = array();
        foreach ($rows as $r) {
            $have[] = strtoupper(trim((string) $r->name));
        }

        $ok = FALSE;
        foreach ($allowed as $a) {
            if (in_array(strtoupper(trim((string) $a)), $have, TRUE)) {
                $ok = TRUE;
                break;
            }
        }
        if ($ok) {
            return;
        }

        // Audit penolakan (append-only). Gagal tulis audit tidak mengubah keputusan tolak.
        $this->audit_role_denial($endpoint, $capability, $method, $user_id, $username, $branch,
            $allowed, $have);

        $this->REST_Return(403, 'Anda tidak berwenang melakukan operasi ini.', array(
            'endpoint'    => $endpoint,
            'capability'  => $capability,
            'method'      => $method,
        ), 'ROLE_NOT_AUTHORIZED');
        $this->_flush_and_stop();
    }

    protected function audit_role_denial($endpoint, $capability, $method, $user_id, $username, $branch,
                                         $allowed, $have) {
        $b = random_bytes(16);
        $b[6] = chr((ord($b[6]) & 0x0f) | 0x40);
        $b[8] = chr((ord($b[8]) & 0x3f) | 0x80);
        $audit_id = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($b), 4));
        $desc = json_encode(array(
            'request_id'   => $this->debug_uniq,
            'endpoint'     => $endpoint,
            'action'       => $method,
            'result'       => 'DENIED_ROLE_NOT_AUTHORIZED',
            'http_status'  => 403,
            'capability'   => $capability,
            'groups_allowed' => array_values($allowed),
            'groups_have'  => array_values($have),
            'branch'       => $branch,
        ));
        $ok = $this->db->query(
            "INSERT INTO log_activity (id, noregistrasi, user_id, user_name, category_id, description,
                related_id, related_table, created_at) VALUES (?,?,?,?,?,?,?,?,?)",
            array($audit_id, null, $user_id, $username, 0, $desc, null, 'role_policy',
                  date('Y-m-d H:i:s')));
        if (!$ok) {
            Logger::error($this->debug_uniq, 'role denial audit write failed for ' . $endpoint);
        }
        return (bool) $ok;
    }

    /**
     * Tahap 1 — BRANCH DEFAULT (mode single cabang).
     * Mengembalikan branch dari token; bila kosong dan mode single, pakai default.
     * TODO: expand branch filter saat multi-cabang
     */
    protected function resolve_branch_id($token_branch) {
        $branch = trim((string) $token_branch);
        if ($branch !== '') {
            return $branch;
        }
        $this->config->load('branch_default', TRUE);
        if ($this->config->item('branch_single_mode', 'branch_default')) {
            return trim((string) $this->config->item('default_branch_id', 'branch_default'));
        }
        return '';
    }

    /**
     * REST_Return hanya menaruh body di buffer Output; body baru dikirim oleh
     * CodeIgniter::_display() di akhir request. Karena penolakan harus menghentikan
     * request di dalam constructor, buffer itu harus di-flush manual sebelum exit —
     * kalau tidak, klien menerima status 403 dengan body kosong.
     */
    protected function _flush_and_stop() {
        if (method_exists($this->output, '_display')) {
            $this->output->_display();
        }
        exit;
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
        Logger::info($this->debug_uniq, 'raw request:', $this->mask_sensitive($_POST));
		// CHECK XSS CLEAN N RETURN
		return $this->xss_clean($_POST, $_SERVER);
	}

	// Copy for logging only; $data passed by value, original request untouched.
	protected function mask_sensitive($data){
		if (!is_array($data)) return $data;
		// Data pribadi sensitif: kredensial + NIK/nomor identitas + nomor telepon.
		// Nilai NIK dan nomor telepon TIDAK boleh tercetak di log/laporan.
		$keys = ['password','password_confirmation','token','access_token','refresh_token',
			'idcard','idcardparent','nik','phone','telephone',
			'responsible_person_idcard','responsible_person_phone'];
		foreach ($data as $k => $v) {
			if (is_array($v)) $data[$k] = $this->mask_sensitive($v);
			elseif (is_string($k) && in_array(strtolower($k), $keys, true)) $data[$k] = '***';
		}
		return $data;
	}
	
	public function json_put(){
		// HEADER
		header('Access-Control-Allow-Origin: *');
		// POST JSON DECODE
		$_PUT = json_decode(file_get_contents('php://input'), true) ?? [];
        Logger::info($this->debug_uniq, 'raw request:', $this->mask_sensitive($_PUT));
		// CHECK XSS CLEAN N RETURN
		return $this->xss_clean($_PUT, $_SERVER);
	}

	public function REST_Return($status = 404, $message = 'NOT FOUND', $response_data = array(), $code = null) {
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
            'code'          => $code !== null ? $code : $this->error_code($status),
            'message'       => $message,
            'request_id'    => $this->debug_uniq,
            'debug'         => $this->debug_uniq,
            'data'          => $response_data,
        );
        // S1: error_notice memuat path absolut server (terbukti bocor di 401). Hanya non-production.
        if (ENVIRONMENT !== 'production') {
            $response['error_notice'] = $this->errors;
        }

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
        } else if ($status == 403) {
            // S1: 403 dan 503 sebelumnya tidak punya cabang => REST_Return tidak mengirim apa pun.
            $this->response($response, REST_Controller::HTTP_FORBIDDEN);
        } else if ($status == 503) {
            $this->response($response, REST_Controller::HTTP_SERVICE_UNAVAILABLE);
        } else if ($status == 501) {
            // S1: tanpa cabang ini REST_Return tidak mengirim apa pun -> SILENT_EXIT
            // dan klien menerima 500. Dipakai list_get() untuk model/tabel yang
            // belum ada (feature belum dibangun), bukan error server.
            $this->response($response, REST_Controller::HTTP_NOT_IMPLEMENTED);
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

    /**
     * S1: satu-satunya tempat menulis detail error internal. Tujuan log = error_log
     * (env S1_ERROR_LOG / php.ini / SAPI), bukan file di dalam document root.
     * Tidak pernah menulis token, password, payload request, atau data pasien.
     */
    protected function _log_secure($tag, $detail = array()) {
        error_log('[S1] ' . json_encode(array(
            'ts'         => date('c'),
            'request_id' => $this->debug_uniq,
            'method'     => isset($_SERVER['REQUEST_METHOD']) ? strtolower($_SERVER['REQUEST_METHOD']) : '',
            'endpoint'   => isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '',
            'actor'      => isset($this->s1_actor) ? $this->s1_actor : null,
            'tag'        => $tag,
            'detail'     => $detail,
        )));
    }

    /**
     * S1: envelope error aman. Tanpa stack trace, path, SQL, atau pesan internal.
     */
    protected function _emit_safe_error($status = 500) {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        if (!headers_sent()) {
            http_response_code($status);
            header('Content-Type: application/json; charset=UTF-8');
        }
        echo json_encode(array(
            'status'     => $status,
            'code'       => $this->error_code($status),
            'message'    => 'Terjadi kesalahan pada server.',
            'request_id' => $this->debug_uniq,
        ));
    }

    public function error_code($status) {
        $map = array(
            200 => 'SUCCESS',
            201 => 'CREATED',
            400 => 'BAD_REQUEST',
            401 => 'UNAUTHORIZED',
            403 => 'FORBIDDEN',
            404 => 'NOT_FOUND',
            409 => 'CONFLICT',
            422 => 'UNPROCESSABLE_ENTITY',
            500 => 'INTERNAL_SERVER_ERROR',
            503 => 'SERVICE_UNAVAILABLE',
        );
        return isset($map[$status]) ? $map[$status] : 'ERROR';
    }

    /**
     * S1: TypeError/Error tidak tertangkap catch(Exception) di REST_Controller,
     * lalu handler CI3 menelan pesannya (log_threshold=0, display_errors=0) dan
     * keluar sebagai HTTP 200 + body kosong. Handler ini menggantikannya:
     * detail ke log, klien menerima 500 + request_id saja.
     */
    public function exceptionHandler($e) {
        $this->_log_secure('UNCAUGHT', array(
            'type'    => get_class($e),
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
        ));
        $this->_emit_safe_error(500);
        exit(1);
    }

    function errorHandler($errno, $errstr, $errfile, $errline) {
        // S1: deprecation/notice tidak dicatat ke log (noise 20+ baris per request) dan
        // tidak lagi masuk ke error_notice production (memuat path absolut server).
        if ($errno & ~(E_DEPRECATED | E_USER_DEPRECATED | E_NOTICE | E_USER_NOTICE | E_STRICT)) {
            $this->_log_secure('PHP_ERROR', array(
                'errno'   => $errno,
                'message' => $errstr,
                'file'    => $errfile,
                'line'    => $errline,
            ));
        }

        if (ENVIRONMENT !== 'production') {
            $this->errors[] = "Error: [$errno] $errstr in $errfile on line $errline";
        }

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
            // Sebagian controller tidak punya model bernama sama dengan controller
            // (mis. Pharmacy/Price, Health_facility/Laboratory_report). Tanpa guard
            // ini PHP fatal "Attempt to assign property on null" -> 500.
            // property_exists dipakai (bukan is_object langsung) karena membaca
            // properti tak terdefinisi sudah memicu warning->exception di PHP 8.3.
            if (!property_exists($this, $model) || !is_object($this->$model)) {
                $this->REST_Return(501, $this->name . ' list is not implemented: model '
                    . $model . '_model is not loaded by this controller', array(), 'NOT_IMPLEMENTED');
                return;
            }
            if (!$this->$model->table_exists()) {
                $this->REST_Return(501, $this->name . ' list is not implemented: table for '
                    . $model . '_model is not migrated yet', array(), 'NOT_IMPLEMENTED');
                return;
            }
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
            if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
            $this->$model->whereLike = $q_strings;
            // Filter hanya untuk tabel yang punya kolomnya. Tabel anak (partograph,
            // birth_record, newborn_baby, refer/control/prb_bpjs, template_*, queue)
            // tidak punya institute_branch_id / is_delete; filter buta = 500.
            if ($this->$model->has_column('institute_branch_id'))
                $this->$model->whereFields['a.institute_branch_id'] = $institute_branch_id;
            if ($this->$model->has_column('is_delete'))
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
                    // P1-b: kolom soft-delete ditulis HANYA kalau capability map menyatakan
                    // kolomnya ada. Sebelumnya dua kolom ini selalu ditulis, sehingga tabel
                    // yang hanya punya is_delete (mis. laboratory_parameter) gagal di SQL.
                    $policy_cols = isset($this->delete_decision['policy']['columns'])
                        ? $this->delete_decision['policy']['columns'] : null;
                    if ($policy_cols === null || in_array('user_deleted', $policy_cols, true)) {
                        $want_to_delete['user_deleted'] = $user_id;
                    }
                    if ($policy_cols === null || in_array('date_deleted', $policy_cols, true)) {
                        $want_to_delete['date_deleted'] = date('Y-m-d H:i:s');
                    }
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