<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Dashboard extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/mr/Registration_model", "Registration");
        $this->load->model("v1/mr/Registration_destination_model", "Registration_destination");
        $this->load->model("v1/pharmacy/Receipt_model", "Receipt");
    }

    /**
     * GET Dashboard with API
     * -------------------------
     * @method: GET
     */

     public function detail_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {

            $this->REST_Return(200, 'ok' , []);
            return;

            $output = $this->Dashboard->get_dashboard_data($q_strings);

            $response_data = $output;
            $error   = false;
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

    public function get_get($p = 0) {
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
        
        $period = $q_strings['period'];

        if ($period == null) {
            $this->REST_Return(400, 'period is required');
            return;
        }

        $today = date('Y-m-d');

        if ($period == 'weekly') {
            $idate_start = date('Y-m-d', strtotime('-1 week'));
            $date_start = date('Y-m-d', strtotime($idate_start . '+1 day'));
            $date_end = date('Y-m-d');

            $date_before_start = date("Y-m-d", strtotime($date_start . ' -1 week'));
            $date_before_end = $idate_start;
        } elseif ($period == 'monthly') {
            $idate_start = date('Y-m-d', strtotime('-1 month'));
            $date_start = date('Y-m-d', strtotime($idate_start . '+1 day'));
            $date_end = date('Y-m-d');

            $date_before_start = date("Y-m-d", strtotime($date_start . ' -1 month'));
            $date_before_end = $idate_start;
        } elseif ($period == 'daily') {
            $date_start = date('Y-m-d');
            $date_end = date('Y-m-d');

            $date_before_start = date("Y-m-d", strtotime($date_start . ' -1 day'));
            $date_before_end = $date_before_start;
        } elseif ($period == 'annually') {
            $idate_start = date('Y-m-d', strtotime('-1 year'));
            $date_start = date('Y-m-d', strtotime($idate_start . '+1 day'));
            $date_end = date('Y-m-d');

            $date_before_start = date("Y-m-d", strtotime($date_start . ' -1 year'));
            $date_before_end = $idate_start;
        } else {
            $this->REST_Return(400, 'invalid period');
            return;
        }

        // GET REVENUE
        $revenue = $this->db->query("
            SELECT SUM(pi.var_3) as total
            FROM payment p 
            INNER JOIN payment_item pi ON p.id = pi.payment_id
            INNER JOIN transaction_invoice ti ON p.invoice_id = ti.id
            WHERE p.date_added >= '$date_start 00:00:00' AND p.date_added <= '$date_end 23:59:59' AND p.is_delete = 0
        ")->result();

        // GET TOTAL REVENUE BEFORE
        $revenue_before = $this->db->query("
			SELECT SUM(pi.var_3) as total
            FROM payment p 
            INNER JOIN payment_item pi ON p.id = pi.payment_id
            INNER JOIN transaction_invoice ti ON p.invoice_id = ti.id
            WHERE p.date_added >= '$date_before_start 00:00:00' AND p.date_added <= '$date_before_end 23:59:59' AND p.is_delete = 0
		")->result();

        // GET TOTAL TYPE REGISTRATION
        $health_facility = $this->db->query("
			SELECT hfi.health_facility_id, count(*) as total
            FROM registration r
            INNER JOIN registration_destination rd ON r.id = rd.registration_id
            INNER JOIN health_facility_item hfi ON rd.health_facility_item_id = hfi.id
            WHERE r.register_date >= '$date_start 00:00:00' AND r.register_date <= '$date_end 23:59:59' AND r.is_cancel = 0 AND rd.is_cancel = 0
            GROUP BY hfi.health_facility_id
		")->result();

        // GET TOTAL TYPE REGISTRATION BEFORE
        $health_facility_before = $this->db->query("
			SELECT hfi.health_facility_id, count(*) as total
            FROM registration r
            INNER JOIN registration_destination rd ON r.id = rd.registration_id
            INNER JOIN health_facility_item hfi ON rd.health_facility_item_id = hfi.id
            WHERE r.register_date >= '$date_before_start 00:00:00' AND r.register_date <= '$date_before_end 23:59:59' AND r.is_cancel = 0 AND rd.is_cancel = 0
            GROUP BY hfi.health_facility_id
		")->result();

        // GET TOTAL PATIENT
        $registration_destination = $this->db->query("
			SELECT count(*) as total
            FROM registration_destination rd
            INNER JOIN registration r ON rd.registration_id = r.id
            WHERE r.register_date >= '$date_start 00:00:00' AND r.register_date <= '$date_end 23:59:59' AND r.is_cancel = 0 AND rd.is_cancel = 0
		")->result();

        // GET TOTAL PATIENT BEFORE
        $registration_destination_before = $this->db->query("
			SELECT count(*) as total
            FROM registration_destination rd
            INNER JOIN registration r ON rd.registration_id = r.id
            WHERE r.register_date >= '$date_before_start 00:00:00' AND r.register_date <= '$date_before_end 23:59:59' AND r.is_cancel = 0 AND rd.is_cancel = 0
		")->result();

        $total_revenue = $revenue[0]->total == null ? 0 : $revenue[0]->total;
        $total_patient = intval($registration_destination[0]->total);
        $total_patient_before = intval($registration_destination_before[0]->total) == 0 ? 1 : intval($registration_destination_before[0]->total);
        $total_revenue_before = intval($revenue_before[0]->total) == 0 ? 1 : intval($revenue_before[0]->total);

        $total_revenue_change = round((float)($total_revenue - $total_revenue_before) / ($total_revenue_before == 0 ? 1 : $total_revenue_before) * 100);
        $total_revenue_change = $total_revenue_change > 0 ? '+' . $total_revenue_change : $total_revenue_change;

        $total_patient_change = round((float)($total_patient - $total_patient_before) / ($total_patient_before == 0 ? 1 : $total_patient_before) * 100);
        $total_patient_change = $total_patient_change > 0 ? '+' . $total_patient_change : $total_patient_change;

        $output = array(
            'date_start' => $date_start,
            'date_end' => $date_end,
            'date_before_start' => $date_before_start,
            'date_before_end' => $date_before_end,
            'total_revenue' => (float) $total_revenue,
            'total_revenue_before' => $total_revenue_before,
            'total_revenue_change' => $total_revenue_change . '%',
            'total_patient' => $total_patient,
            'total_patient_before' => $total_patient_before,
            'total_patient_change' => $total_patient_change . '%',
            'total_outpatient' => 0,
            'total_outpatient_before' => 0,
            'total_outpatient_change' => '',
            'total_inpatient' => 0,
            'total_inpatient_before' => 0,
            'total_inpatient_change' => '',
            'total_emergency' => 0,
            'total_emergency_before' => 0,
            'total_emergency_change' => '',
            'total_support' => 0,
            'total_support_before' => 0,
            'total_support_change' => '',
            'statistics' => array(),
        );

        for ($i=0; $i < count($health_facility); $i++) { 
            $health_facility_id = $health_facility[$i]->health_facility_id;
            $total = intval($health_facility[$i]->total);
            
            if ($health_facility_id == '3cb626f5-9148-4fb1-8663-3d7f028b01a6') { // RAWAT JALAN
                $output['total_outpatient'] = $total;
            } elseif ($health_facility_id == '40f06534-07ec-4221-ad9d-dea08196079f') { // RAWAT INAP
                $output['total_inpatient'] = $total;
            } elseif ($health_facility_id == '444b3134-5b75-4b67-af72-a558d46716d7') { // IGD
                $output['total_emergency'] = $total;
            } elseif ($health_facility_id == '00e8fed1-a85d-4f42-96dd-bfff63086945') { // IGD
                $output['total_support'] = $total;
            }
        }

        for ($i=0; $i < count($health_facility_before); $i++) { 
            $health_facility_id = $health_facility_before[$i]->health_facility_id;
            $total = intval($health_facility_before[$i]->total);
            
            if ($health_facility_id == '3cb626f5-9148-4fb1-8663-3d7f028b01a6') { // RAWAT JALAN
                $output['total_outpatient_before'] = $total;
            } elseif ($health_facility_id == '40f06534-07ec-4221-ad9d-dea08196079f') { // RAWAT INAP
                $output['total_inpatient_before'] = $total;
            } elseif ($health_facility_id == '444b3134-5b75-4b67-af72-a558d46716d7') { // IGD
                $output['total_emergency_before'] = $total;
            } elseif ($health_facility_id == '00e8fed1-a85d-4f42-96dd-bfff63086945') { // IGD
                $output['total_support_before'] = $total;
            }
        }

        $total_outpatient = $output['total_outpatient'];
        $total_inpatient = $output['total_inpatient'];
        $total_emergency = $output['total_emergency'];
        $total_support = $output['total_support'];
        $total_outpatient_before = $output['total_outpatient_before'];
        $total_inpatient_before = $output['total_inpatient_before'];
        $total_emergency_before = $output['total_emergency_before'];
        $total_support_before = $output['total_support_before'];
    
        $total_outpatient_change = round((float)($total_outpatient - $total_outpatient_before) / ($total_outpatient_before == 0 ? 1 : $total_outpatient_before) * 100);
        $total_outpatient_change = $total_outpatient_change > 0 ? '+' . $total_outpatient_change : $total_outpatient_change;

        $total_inpatient_change = round((float)($total_inpatient - $total_inpatient_before) / ($total_inpatient_before == 0 ? 1 : $total_inpatient_before) * 100);
        $total_inpatient_change = $total_inpatient_change > 0 ? '+' . $total_inpatient_change : $total_inpatient_change;

        $total_emergency_change = round((float)($total_emergency - $total_emergency_before) / ($total_emergency_before == 0 ? 1 : $total_emergency_before) * 100);
        $total_emergency_change = $total_emergency_change > 0 ? '+' . $total_emergency_change : $total_emergency_change;

        $total_support_change = round((float)($total_support - $total_support_before) / ($total_support_before == 0 ? 1 : $total_support_before) * 100);
        $total_support_change = $total_support_change > 0 ? '+' . $total_support_change : $total_support_change;

        $output['total_outpatient_change'] =  $total_outpatient_change . '%';
        $output['total_inpatient_change'] = $total_inpatient_change . '%';
        $output['total_emergency_change'] = $total_emergency_change . '%';
        $output['total_support_change'] = $total_support_change . '%';

        for ($i=0; $i < 6; $i++) {
            if ($period == 'weekly') {
                if ($i == 0) {
                    $date_start = date('Y-m-d', strtotime('+1 day'));
                    $date_before_start = date("Y-m-d", strtotime($date_start . ' -1 week'));
                    $date_before_end = date('Y-m-d');
                } else {
                    $date_start = date('Y-m-d', strtotime('-'.$i.' week'));
                    $date_before_start = date("Y-m-d", strtotime($date_start . ' -1 week'));
                    $date_before_start = date("Y-m-d", strtotime($date_before_start . ' +1 day'));
                    $date_before_end = $date_start;
                }
            } elseif ($period == 'monthly') {
                if ($i == 0) {
                    $date_start = date('Y-m-d');
                } else {
                    $date_start = date('Y-m-d', strtotime('-'.$i.' month'));
                }
    
                $date_before_start = date("Y-m-d", strtotime($date_start . ' -1 month'));
                $date_before_start = date("Y-m-d", strtotime($date_before_start . ' +1 day'));
                $date_before_end = $date_start;
            } elseif ($period == 'daily') {
                if ($i == 0) {
                    $date_start = date('Y-m-d');
                    $date_before_start = $date_start;
                } else {
                    $date_start = date('Y-m-d', strtotime('-'.$i.' day'));
                    $date_before_start = date("Y-m-d", strtotime('-'.$i.' day'));
                }
    
                $date_before_end = $date_start;
            } elseif ($period == 'annually') {
                if ($i == 0) {
                    $date_start = date('Y-m-d');
                } else {
                    $date_start = date('Y-m-d', strtotime('-'.$i.' year'));
                }
    
                $date_before_start = date("Y-m-d", strtotime($date_start . ' -1 year'));
                $date_before_start = date("Y-m-d", strtotime($date_before_start . ' +1 day'));
                $date_before_end = $date_start;
            }

            $data = array(
                'date_start' => $date_before_start,
                'date_end' => $date_before_end,
                'total_patient' => 0,
                'total_outpatient' => 0,
                'total_inpatient' => 0,
            );

            $registration_destination = $this->db->query("
                SELECT count(*) as total
                FROM registration_destination rd
                INNER JOIN registration r ON rd.registration_id = r.id
                WHERE r.register_date >= '$date_before_start 00:00:00' AND r.register_date <= '$date_before_end 23:59:59' AND r.is_cancel = 0 AND rd.is_cancel = 0
            ")->result();

            $revenue = $this->db->query("
                SELECT SUM(pi.var_3) as total
                FROM payment p 
                INNER JOIN payment_item pi ON p.id = pi.payment_id
                INNER JOIN transaction_invoice ti ON p.invoice_id = ti.id
                WHERE p.date_added >= '$date_before_start 00:00:00' AND p.date_added <= '$date_before_end 23:59:59' AND p.is_delete = 0
            ")->result();

            $health_facility = $this->db->query("
                SELECT hfi.health_facility_id, count(*) as total
                FROM registration r
                INNER JOIN registration_destination rd ON r.id = rd.registration_id
                INNER JOIN health_facility_item hfi ON rd.health_facility_item_id = hfi.id
                WHERE r.register_date >= '$date_before_start 00:00:00' AND r.register_date <= '$date_before_end 23:59:59' AND r.is_cancel = 0 AND rd.is_cancel = 0
                GROUP BY hfi.health_facility_id
            ")->result();

            $data['total_patient'] = intval($registration_destination[0]->total);
            $data['total_revenue'] = intval($revenue[0]->total);

            for ($j=0; $j < count($health_facility); $j++) { 
                $health_facility_id = $health_facility[$j]->health_facility_id;
                $total = intval($health_facility[$j]->total);
                
                if ($health_facility_id == '3cb626f5-9148-4fb1-8663-3d7f028b01a6') { // RAWAT JALAN
                    $data['total_outpatient'] = $total;
                } elseif ($health_facility_id == '40f06534-07ec-4221-ad9d-dea08196079f') { // RAWAT INAP
                    $data['total_inpatient'] = $total;
                } elseif ($health_facility_id == '444b3134-5b75-4b67-af72-a558d46716d7') { // IGD
                    $data['total_emergency'] = $total;
                } elseif ($health_facility_id == '00e8fed1-a85d-4f42-96dd-bfff63086945') { // IGD
                    $data['total_support'] = $total;
                }
            }

            $data['total_revenue'] = intval($revenue[0]->total);

            array_push($output['statistics'], $data);
        }

        $output['statistics'] = array_reverse($output['statistics']);

        $response_data = $output;
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function get_statistics_get($p = 0) {
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
        
        $period = $q_strings['period'];

        if ($period == null) {
            $this->REST_Return(400, 'period is required');
            return;
        }

        $today = date('Y-m-d');

        $output = array();

        for ($i=0; $i < 8; $i++) {
            if ($period == 'weekly') {
                if ($i == 0) {
                    $date_start = date('Y-m-d', strtotime('+1 day'));
                    $date_before_start = date("Y-m-d", strtotime($date_start . ' -1 week'));
                    $date_before_end = date('Y-m-d');
                } else {
                    $date_start = date('Y-m-d', strtotime('-'.$i.' week'));
                    $date_before_start = date("Y-m-d", strtotime($date_start . ' -1 week'));
                    $date_before_start = date("Y-m-d", strtotime($date_before_start . ' +1 day'));
                    $date_before_end = $date_start;
                }
            } elseif ($period == 'monthly') {
                if ($i == 0) {
                    $date_start = date('Y-m-d');
                } else {
                    $date_start = date('Y-m-d', strtotime('-'.$i.' month'));
                }
    
                $date_before_start = date("Y-m-d", strtotime($date_start . ' -1 month'));
                $date_before_start = date("Y-m-d", strtotime($date_before_start . ' +1 day'));
                $date_before_end = $date_start;
            } elseif ($period == 'daily') {
                if ($i == 0) {
                    $date_start = date('Y-m-d');
                    $date_before_start = $date_start;
                } else {
                    $date_start = date('Y-m-d', strtotime('-'.$i.' day'));
                    $date_before_start = date("Y-m-d", strtotime('-'.$i.' day'));
                }
    
                $date_before_end = $date_start;
            } elseif ($period == 'annually') {
                if ($i == 0) {
                    $date_start = date('Y-m-d');
                } else {
                    $date_start = date('Y-m-d', strtotime('-'.$i.' year'));
                }
    
                $date_before_start = date("Y-m-d", strtotime($date_start . ' -1 year'));
                $date_before_start = date("Y-m-d", strtotime($date_before_start . ' +1 day'));
                $date_before_end = $date_start;
            }

            $data = array(
                'date_start' => $date_before_start,
                'date_end' => $date_before_end,
                'total_patient' => 0,
                'total_bpjs' => 0,
                'total_non_bpjs' => 0,
            );

            $registration_destination = $this->db->query("
                SELECT count(*) as total
                FROM registration_destination rd
                INNER JOIN registration r ON rd.registration_id = r.id
                WHERE r.register_date >= '$date_before_start 00:00:00' AND r.register_date <= '$date_before_end 23:59:59' AND r.is_cancel = 0 AND rd.is_cancel = 0
            ")->result();

            $registration_insurance = $this->db->query("
                SELECT ri.company_id, count(*) as total
                FROM registration_destination rd
                INNER JOIN registration r ON rd.registration_id = r.id
                INNER JOIN registration_insurance ri ON rd.id = ri.registration_destination_id AND ri.is_primary = 1
                WHERE r.register_date >= '$date_before_start 00:00:00' AND r.register_date <= '$date_before_end 23:59:59' AND r.is_cancel = 0 AND rd.is_cancel = 0
                GROUP BY ri.company_id
            ")->result();

            $data['total_patient'] = intval($registration_destination[0]->total);

            for ($j=0; $j < count($registration_insurance); $j++) { 
                $company_id = $registration_insurance[$j]->company_id;
                $total = intval($registration_insurance[$j]->total);

                if ($company_id == '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8') { // BPJS
                    $data['total_bpjs'] = $total;
                } else {
                    $data['total_non_bpjs'] = $data['total_non_bpjs'] + $total;
                }
            }

            array_push($output, $data);
        }

        $output = array_reverse($output);

        $response_data = $output;
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function latest_patient_get($p = 0) {
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

        $latest_patient = $this->db->query("
            SELECT p.fullname, p.birth_date, h.name as sex_name, p.address, r.icd10_name, hfi.name as health_facility_item_name
            FROM registration_destination rd
            INNER JOIN health_facility_item hfi ON rd.health_facility_item_id = hfi.id
            INNER JOIN registration r ON rd.registration_id = r.id
            INNER JOIN patient p ON r.norm = p.norm
            INNER JOIN helper h ON p.sex = h.code AND h.variable = 'sex'
            WHERE rd.is_cancel = 0
            GROUP BY r.norm
            ORDER BY rd.checkin_date DESC
            LIMIT 3
        ")->result();

        $response_data = $latest_patient;
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function get_schedule_doctor_get($p = 0) {
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

        $date = $q_strings['date'];

        if ($date == null) {
            $this->REST_Return(400, 'date is required');
            return;
        }

        $day = date('N', strtotime($date));

        $data = [];

        for ($i=0; $i < 24; $i++) { 
            $fmt_i = sprintf('%02d', $i);
            $hour_start = $fmt_i.':00:00';
            $hour_end = $fmt_i.':59:59';

            $schedule_item = $this->db->query("
                SELECT dsi.hour_start, dsi.hour_end, e.name as doctor_name, e.fullname as doctor_fullname, hfi.name as health_facility_item_name 
                FROM doctor_schedule_item dsi
                INNER JOIN doctor_schedule ds ON dsi.doctor_schedule_id = ds.id
                INNER JOIN employee e ON ds.doctor_id = e.id
                INNER JOIN health_facility_item hfi ON ds.health_facility_item_id = hfi.id
                WHERE dsi.is_delete = 0
                AND dsi.day = '$day'
                AND dsi.hour_start >= '$hour_start' AND dsi.hour_start <= '$hour_end'
                ORDER BY dsi.hour_start ASC
            ")->result();

            $dt = array(
                'group' => $hour_start,
                'data' => $schedule_item,
            );

            if (count($schedule_item) > 0) {
                array_push($data, $dt);
            }
        }

        $output = $data;

        $response_data = $output;
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function get_checkout_monitoring_get($p = 0) {
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
        $query_exit='';
        if ($q_strings['exit_id'] != null && $q_strings['exit_id'] != ""){
            $query_exit = "AND rd.exit_id='".$q_strings['exit_id']."'"; 
        }
        
        $latest_patient = $this->db->query("
        SELECT rd.id as registration_destination_id,p.fullname, p.fullname, hfi.name as health_facility_item_name, ri.sep, rd.exit_reason, ri.statusPulang, prs.date_added as rencana_pulang ,rd.checkout_date as isi_form_pasien_pulang,  prs.date_1 as proses_resep, ti.date_added as billing_selesai, rd.date_task_7 as checkout
        FROM registration_destination rd
        INNER JOIN health_facility_item hfi ON rd.health_facility_item_id = hfi.id
        LEFT JOIN pharmacy_prescription prs ON rd.id = prs.registration_destination_id AND prs.type_prescription_id = 2
        INNER JOIN registration r ON rd.registration_id = r.id
        LEFT JOIN transaction_invoice ti ON rd.registration_id = ti.registration_id AND ti.is_delete = 0
        INNER JOIN registration_insurance ri ON rd.id = ri.registration_destination_id
        INNER JOIN patient p ON r.norm = p.norm
        INNER JOIN helper h ON p.sex = h.code AND h.variable = 'sex'
        WHERE hfi.health_facility_id = '40f06534-07ec-4221-ad9d-dea08196079f'
        AND rd.checkout_date >= '".$q_strings['date_start'].' 00:00:00'."'
        AND rd.checkout_date <= '".$q_strings['date'].' 23:59:59'."' ".$query_exit."
        GROUP BY r.id
        ")->result();

        // $latest_patient = $this->db->query("
        // SELECT p.fullname, p.fullname, hfi.name as health_facility_item_name, ri.sep, ri.statusPulang, rd.checkout_date as isi_form_pasien_pulang, rd.date_task_7 as checkout
        // FROM registration_destination rd
        // INNER JOIN health_facility_item hfi ON rd.health_facility_item_id = hfi.id
        // INNER JOIN registration r ON rd.registration_id = r.id
        // INNER JOIN registration_insurance ri ON rd.id = ri.registration_destination_id
        // INNER JOIN patient p ON r.norm = p.norm
        // INNER JOIN helper h ON p.sex = h.code AND h.variable = 'sex'
        // WHERE hfi.health_facility_id = '40f06534-07ec-4221-ad9d-dea08196079f'
        // AND rd.checkout_date >= '".$q_strings['date_start'].' 00:00:00'."'
        // AND rd.checkout_date <= '".$q_strings['date'].' 23:59:59'."'
        // GROUP BY r.id
        // ")->result();

        $date_start = $q_strings['date_start'];
        $date_end = $q_strings['date'];

        if ($latest_patient) {
            $this->load->library('Vclaim_sep', 'vclaim_sep');

            // Buat objek DateTime dari tanggal awal dan akhir
            $start = new DateTime($date_start);
            $end   = new DateTime($date_end);
        
            // Pastikan loop-nya termasuk bulan terakhir juga
            $end = (clone $end)->modify('first day of next month');
        
            $results = [];
            
            // Loop per bulan
            while ($start < $end) {
                $month = (int) $start->format('m');
                $year  = $start->format('Y');
                
                // {BASE URL}/{Service Name}/Sep/updtglplg/list/bulan/{Parameter 1}/tahun/{Parameter 2}/{Parameter 3}
                // $output = $this->vclaim_sep->request("/Sep/updtglplg/list/bulan/"."$p"."/tahun/".$year."/tahun", "GET");
                $endpoint = "/Sep/updtglplg/list/bulan/{$month}/tahun/{$year}/";
                $output   = $this->vclaim_sep->request($endpoint, "GET");
        
                // Coba decode kalau JSON
                $decoded = json_decode($output, true);
        
                // Simpan hasil
                if (isset($decoded['response']['list']) && is_array($decoded['response']['list'])) {
                    $results = (array) array_merge($results, $decoded['response']['list']);
                }
        
                // Naik ke bulan berikutnya
                $start->modify('first day of next month');
            }
        }
        // var_dump($latest_patient);
        // die();
        for ($i=0; $i < count($latest_patient); $i++) { 
            if ($latest_patient[$i]->sep) {
                for ($z=0; $z < count($results); $z++) { 
                    if ($latest_patient[$i]->sep == $results[$z]['noSep']) {
                        $latest_patient[$i]->statusPulang = $results[$z]['status'];
                        $latest_patient[$i]->dead = $results[$z]['tglMeninggal'].' no : '.$results[$z]['noSurat'];
                    }
                }
            }
        }

        $response_data = $latest_patient;
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function get_balancing_get($p = 0) {
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

        $latest_patient = $this->db->query("
            SELECT ri.sep, rd.register_date, c.name as insurance_name, cre.name as vendor_name, r.norm, ms.desc_inacbg, ms.cost_inacbg, bp.name as period_name, p.name as patient_name, re.no_invoice, re.nfinish, rd.register_date, rd.checkout_date,re.id as receipt_id
            FROM registration_destination rd
            INNER JOIN registration r ON rd.registration_id = r.id
            INNER JOIN patient p ON r.norm = p.norm
            LEFT JOIN medical_services ms ON rd.id = ms.registration_destination_id
            LEFT JOIN b_period bp ON ms.period_id = bp.id
            INNER JOIN receipts re ON rd.id = re.registration_destination_id
            INNER JOIN registration_insurance ri ON rd.id = ri.registration_destination_id
            INNER JOIN company c ON ri.company_id = c.id
            INNER JOIN company cre ON re.supplier_id = cre.id
            AND rd.register_date >= '".$q_strings['date_start'].' 00:00:00'."'
            AND rd.register_date <= '".$q_strings['date'].' 23:59:59'."'
            GROUP BY r.id
            ORDER BY rd.register_date DESC
        ")->result();

        for ($i=0; $i < count($latest_patient); $i++) { 
            if ($latest_patient[$i]->receipt_id) {
                $select = "a.*, 
                b.name as pharmacy_depo_name, 
                c.name as payment_method_name, 
                c.days, 
                ee.fullname as receipt_name, 
                a.nfinish, 
                d.bride_code, 
                h.name as payment_name, 
                d.name as supplier_name, 
                e.username as user_name, 
                f.no_document as no_document_purchase, 
                f.date as date_po, 
                f.ndiscountotal, 
                g.no_document as no_document_planning,
                pc.name as pharmacy_catalog_name,
                ri.batch,
                ri.package_name,
                ri.sum_item,
                ri.sum_package,
                ri.price_item,
                ri.price_package,
                ri.discount_item,
                ri.discount_price,
                ";
                $whereReceipt['a.is_deleted'] = 0;
                $whereReceipt['a.id'] = $latest_patient[$i]->receipt_id;
                $join = array(
                    array('receipt_items ri', 'a.id = ri.receipt_id', 'LEFT'),
                    array('pharmacy_catalog pc', 'ri.pharmacy_catalog_id = pc.id', 'LEFT'),
                    array('pharmacy_depo b','a.pharmacy_depo_id = b.id','LEFT'),
                    array('payment_method c','a.payment_method_id = c.id','LEFT'),
                    array('company d','a.supplier_id = d.id','LEFT'),
                    array('user e','a.user_added = e.id','LEFT'),
                    array('employee ee','e.employee_id = ee.id','LEFT'),
                    array('purchase_order f','a.purchase_order_id = f.id','LEFT'),
                    array('helper h','f.price_type = h.code AND h.variable="type_price"','LEFT'),
                    array('pharmacy_planning g','a.pharmacy_planning_id = g.id','LEFT'),
                );
                $order = array('a.date_added' => 'ASC');
                $latest_patient[$i]->receipt_data = [];
                $latest_patient[$i]->receipt_data = $this->Receipt->select($latest_patient[$i]->receipt_id, $select, $whereReceipt, $join, $order);

            } 
        }

        $response_data = $latest_patient;

    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function margin_get() {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 0); 
        
        // Token Validation
        // $is_valid_token = $this->authorization_token->validateToken();
        // if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
        //     $response_data = 'Invalid Token.';
        //     $this->REST_Return(401, $is_valid_token['message'] , $response_data);
        //     return;
        // }

        if (!isset($q_strings['date_start']) || !isset($q_strings['date_end'])) {
            $this->REST_Return(400, 'date_start & date_end is required');
            return;
        } 

        $date_start = $q_strings['date_start'] . ' 00:00:00';
        $date_end = $q_strings['date_end'] . ' 23:59:59';

        $select = "
            a.no_order,
            a.date_added,
            a.type_prescription_id,
            a.date_0,
            a.date_1,
            h.name AS how_to_pay,
            c.name AS insurance_name,
            h2.name AS how_to_pay2,
            c2.name AS insurance_name2,
            hf.name AS health_facility_name,
            a.norm,
            rd.checkout_date,
            e.fullname AS doctor_name,
            dpjp.fullname AS dpjp_name,
            dpjp2.fullname AS dpjp_name2,
            hfi.name AS health_facility_name_item,
            pd.name AS depo_name,
            pc.name AS catalog_name,
            pi.package_name,
            pi.pharmacy_catalog_id,
            pi.pharmacy_catalog_id_ref,
            pi.qty,
            pi.qty_retur,
            pi.var_1
        ";

        $join = [
            ['pharmacy_prescription_item pi', 'a.id = pi.pharmacy_prescription_id', 'INNER'],
            ['pharmacy_catalog pc', 'pi.pharmacy_catalog_id = pc.id', 'INNER'],
            ['package pkg', 'pc.package_id = pkg.id', 'INNER'],
            ['pharmacy_depo pd', 'a.pharmacy_depo_id = pd.id', 'INNER'],
            ['employee e', 'a.doctor_in_charge_id = e.id', 'LEFT'],
            ['health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'LEFT'],
            ['health_facility hf', 'hfi.health_facility_id = hf.id', 'LEFT'],
            ['registration_destination rd', 'a.registration_destination_id = rd.id', 'LEFT'],
            ['employee dpjp', 'rd.doctor_in_charge_id = dpjp.id', 'LEFT'],
            ['registration_insurance ri', 'rd.id = ri.registration_destination_id AND ri.is_primary = 1', 'LEFT'],
            ['helper h', 'ri.insurance_id = h.code AND h.variable = "insurance"', 'LEFT'],
            ['company c', 'ri.company_id = c.id', 'LEFT'],
            ['soap_p sp', 'a.soap_p_id = sp.id', 'LEFT'],
            ['soap s', 'sp.soap_id = s.id', 'LEFT'],
            ['registration_destination rd2', 's.registration_destination_id = rd2.id', 'LEFT'],
            ['registration_insurance ri2', 'rd2.id = ri2.registration_destination_id AND ri2.is_primary = 1', 'LEFT'],
            ['helper h2', 'ri2.insurance_id = h2.code AND h2.variable = "insurance"', 'LEFT'],
            ['company c2', 'ri2.company_id = c2.id', 'LEFT'],
            ['employee dpjp2', 'rd2.doctor_in_charge_id = dpjp2.id', 'LEFT'],
        ];

        $where = [
            'a.is_delete' => 0,
            'ri.company_id' => '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8',
            'a.flag' => 1,
            'pi.is_delete' => 0,
        ];

        $order = ['a.date_added' => 'ASC'];

        // Build query
        $this->db
            ->select($select)
            ->from('pharmacy_prescription a');

        foreach ($join as $j) {
            $this->db->join($j[0], $j[1], $j[2]);
        }

        $this->db->where('a.is_delete', 0);
        $this->db->where('a.flag', 1);
        $this->db->where('pi.is_delete', 0);
        $this->db->where_not_in('pc.suppliers_id', [
            '29607783-85c9-4c80-bb09-673436abd025',
            '4164b069-15de-4e30-8ec4-40ddb4c70797',
            '4b2b43ce-ddc8-4db9-a9bc-8031c3697e4c',
            '4dbaa98b-13fb-4cd7-a65c-015a3f216e36',
            '52e313cf-ba9b-4eb7-9916-b5d2863139c0',
            '72f7d7df-17b5-4de6-b59b-576b7799723a',
            '7668d7b4-e239-4cc2-8843-c0931c0fed52',
            '7fb91255-7be4-49c7-82e5-370ff86b9b41',
            '923b4d27-2cf4-4503-8769-9922aa0ec4c6',
            '9608f20f-21cb-47d3-87ee-badf8a4a9a51',
            '98175373-29a1-4dcf-b846-cde16d54eba8',
            'ced75311-fe14-4cb2-969a-a4a8b37d6782',
            '8df35771-114f-4b84-8699-e1034fc33138'
        ]);
        if (!empty($q_strings['health_facility_id'])) {
            $this->db->where('hf.id', $q_strings['health_facility_id']);
        }

        // bentuk: (rd.checkout_date BETWEEN ... ) OR (rd.id IS NULL AND a.date_added BETWEEN ...)
        $this->db->group_start();
            $this->db->group_start();
                $this->db->where('rd.checkout_date >=', $date_start);
                $this->db->where('rd.checkout_date <=', $date_end);
            $this->db->group_end();
            $this->db->or_group_start();
                $this->db->where('rd.id IS NULL'); // resep tanpa registration_destination
                $this->db->where('a.date_added >=', $date_start);
                $this->db->where('a.date_added <=', $date_end);
            $this->db->group_end();
        $this->db->group_end();

        $this->db->group_by(['pi.pharmacy_prescription_id','pi.pharmacy_catalog_id']);
        $this->db->order_by('a.date_added','ASC');

        $query = $this->db->get();
        $prescription = $query->result();

        $norms = [];
        for ($i=0; $i < count($prescription); $i++) { 
            $norm = $prescription[$i]->norm;
            if ($norm != null) {
                $norms[] = $norm;
            }
        }

        $param = implode(',',$norms);

        $patient = $this->db->query("
            SELECT norm, fullname
            FROM patient
            WHERE norm in ($param)
        ")->result();

        $dt_patient = [];
        for ($i=0; $i < count($patient); $i++) { 
            $dt_patient[$patient[$i]->norm] = array(
                'fullname' => $patient[$i]->fullname,
            );
        }
        
        Logger::info($this->debug_uniq, 'total rows: ' . count($prescription));


        for ($i=0; $i < count($prescription); $i++) { 
            $receipt = $this->db->query("SELECT 
                    (
                        SELECT MIN(pap2.hna_item)
                        FROM pharmacy_assets_price pap2
                        WHERE pap2.pharmacy_catalog_id = a.pharmacy_catalog_id
                        AND pap2.batch = a.batch
                    ) AS price_item,
                    a.pharmacy_catalog_id, 
                    a.discount_item, 
                    a.package_name AS package_name_receipt
                FROM receipt_items AS a
                INNER JOIN pharmacy_assets_price pap 
                    ON a.pharmacy_catalog_id = pap.pharmacy_catalog_id 
                    AND a.batch = pap.batch
                WHERE a.pharmacy_catalog_id = '{$prescription[$i]->pharmacy_catalog_id}'
                ORDER BY 
                    price_item ASC,
                    a.discount_item DESC
                LIMIT 1")->result()[0];
            

            $no_order = $prescription[$i]->no_order;
            $date_added = $prescription[$i]->date_added;
            $type_prescription_id = $prescription[$i]->type_prescription_id;
            $pharmacy_catalog_id_ref = $prescription[$i]->pharmacy_catalog_id_ref;
            $date_0 = $prescription[$i]->date_0;
            $date_1 = $prescription[$i]->checkout_date;
            $how_to_pay = $prescription[$i]->how_to_pay;
            $insurance_name = $prescription[$i]->insurance_name;
            $how_to_pay2 = $prescription[$i]->how_to_pay2;
            $insurance_name2 = $prescription[$i]->insurance_name2;
            $health_facility_name_item = $prescription[$i]->health_facility_name_item;
            $norm = $prescription[$i]->norm;
            $patient_name = '-';
            $doctor_name = $prescription[$i]->doctor_name;
            $dpjp_name = $prescription[$i]->dpjp_name;
            $dpjp_name2 = $prescription[$i]->dpjp_name2;
            $depo_name = $prescription[$i]->depo_name;
            $catalog_name = $prescription[$i]->catalog_name;
            $package_name = $prescription[$i]->package_name;
            $qty = (float) $prescription[$i]->qty;
            $qty_retur = (float) $prescription[$i]->qty_retur;
            $var_1 = (float) $prescription[$i]->var_1;
            $hna = ($var_1/1.3);
            if (@$receipt->pharmacy_catalog_id == $prescription[$i]->pharmacy_catalog_id) {
                $hpp = (float) ($receipt->price_item-(($receipt->price_item*$receipt->discount_item)/100));
                $package_name_receipt = $receipt->package_name_receipt;
            }

            $patient_name = $dt_patient[$norm]['fullname'];
            
            $total = $var_1 * $qty;

            if ($total > 0) {
                if ($hpp > $hna) {
                    $hpp = $hna;
                }
                $total_hpp = $hpp * $qty;
            } else {
                $hpp = 0;
                $total_hpp = 0;
            }

            if ($pharmacy_catalog_id_ref == null) {
                if ($qty_retur != null) {
                    $total = ($qty - $qty_retur) * $var_1;
                    if ($total > 0) {
                        $total_hpp = ($qty - $qty_retur) * $hpp;
                    } else {
                        $hpp = 0;
                        $total_hpp = 0;
                    }
                }
            }

            $prescription[$i]->no_order                  = $no_order;
            $prescription[$i]->date_added                = $date_added;
            $prescription[$i]->type_prescription_id      = $type_prescription_id;
            $prescription[$i]->date_0                    = $date_0;
            $prescription[$i]->date_1                    = $date_1;
            $prescription[$i]->how_to_pay                = ($how_to_pay != null) ? $how_to_pay : $how_to_pay2;
            $prescription[$i]->type_prescription_id      = $type_prescription_id == 5 ? 'Tunai/Pribadi' : ($insurance_name != null) ? $insurance_name : $insurance_name2;
            $prescription[$i]->health_facility_name_item      = $health_facility_name_item;
            $prescription[$i]->norm                      = $norm;
            $prescription[$i]->patient_name              = $patient_name;
            $prescription[$i]->dpjp_name                 = ($dpjp_name != null) ? $dpjp_name : $dpjp_name2;
            $prescription[$i]->doctor_name               = $doctor_name;
            $prescription[$i]->depo_name                 = $depo_name;
            $prescription[$i]->catalog_name              = $catalog_name;
            $prescription[$i]->qty                       = $qty;
            $prescription[$i]->pharmacy_catalog_id_ref   = $pharmacy_catalog_id_ref == null ? $qty_retur : 0;
            $prescription[$i]->var_1                     = $var_1;
            $prescription[$i]->package_name              = $package_name;
            $prescription[$i]->total                     = $total;
            $prescription[$i]->hpp                       = $hpp;
            $prescription[$i]->package_name_receipt      = $package_name_receipt;
            $prescription[$i]->total_hpp                 = $total_hpp;
        }

        $grouped = [];

        foreach ($prescription as $row) {

            $hf_item = $row->health_facility_name_item ?: 'UNKNOWN';

            if (!isset($grouped[$hf_item])) {
                $grouped[$hf_item] = [
                    'health_facility_name_item' => $hf_item,
                    'total_hpp' => 0,
                ];
            }

            $grouped[$hf_item]['total_hpp'] += $row->total_hpp;
        }

        // Convert to indexed array
        $grouped_result = array_values($grouped);
        
        $this->REST_Return(200, 'SUCCESS', $grouped_result);
        return;

    }

    public function margin_lab_get() {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 0); 
        
        // Token Validation
        // $is_valid_token = $this->authorization_token->validateToken();
        // if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
        //     $response_data = 'Invalid Token.';
        //     $this->REST_Return(401, $is_valid_token['message'] , $response_data);
        //     return;
        // }

        if (!isset($q_strings['date_start']) || !isset($q_strings['date_end'])) {
            $this->REST_Return(400, 'date_start & date_end is required');
            return;
        } 

        $date_start = $q_strings['date_start'] . ' 00:00:00';
        $date_end = $q_strings['date_end'] . ' 23:59:59';

        $select = "
            a.id,
            a.total,
            a.date_added,
            hfi.name AS health_facility_name_item,
        ";

        $join = [
            ['laboratory_request_item lri', 'a.laboratory_request_item_id = lri.id', 'INNER'],
            ['laboratory_request lr', 'lri.laboratory_request_id = lr.id', 'INNER'],
            ['soap s', 'lr.soap_id = s.id', 'LEFT'],
            ['registration_destination rd', 's.registration_destination_id = rd.id', 'LEFT'],
            ['health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'LEFT'],
        ];

        $order = ['a.date_added' => 'ASC'];

        // Build query
        $this->db
            ->select($select)
            ->from('laboratory_request_hpp a');

        foreach ($join as $j) {
            $this->db->join($j[0], $j[1], $j[2]);
        }

        $this->db->where('a.is_delete', 0);
        $this->db->where('rd.register_date >=', $date_start);
        $this->db->where('rd.register_date <=', $date_end);

        $query = $this->db->get();
        $laboratory = $query->result();

        for ($i=0; $i < count($laboratory); $i++) { 
            $total = $laboratory[$i]->total;
            $health_facility_name_item = $laboratory[$i]->health_facility_name_item;
            $laboratory[$i]->health_facility_name_item      = $health_facility_name_item;
            $laboratory[$i]->total_hpp                 = $total;
        }

        $grouped = [];

        foreach ($laboratory as $row) {

            $hf_item = $row->health_facility_name_item ?: 'UNKNOWN';

            if (!isset($grouped[$hf_item])) {
                $grouped[$hf_item] = [
                    'health_facility_name_item' => $hf_item,
                    'total_hpp' => 0,
                ];
            }

            $grouped[$hf_item]['total_hpp'] += $row->total_hpp;
        }

        // Convert to indexed array
        $grouped_result = array_values($grouped);
        
        $this->REST_Return(200, 'SUCCESS', $grouped_result);
        return;

    }

    public function margin_rad_get() {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 0); 
        
        // Token Validation
        // $is_valid_token = $this->authorization_token->validateToken();
        // if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
        //     $response_data = 'Invalid Token.';
        //     $this->REST_Return(401, $is_valid_token['message'] , $response_data);
        //     return;
        // }

        if (!isset($q_strings['date_start']) || !isset($q_strings['date_end'])) {
            $this->REST_Return(400, 'date_start & date_end is required');
            return;
        } 

        $date_start = $q_strings['date_start'] . ' 00:00:00';
        $date_end = $q_strings['date_end'] . ' 23:59:59';

        $select = "
            a.id,
            a.total,
            a.date_added,
            hfi.name AS health_facility_name_item,
        ";

        $join = [
            ['radiology_request_item lri', 'a.radiology_request_item_id = lri.id', 'INNER'],
            ['radiology_request lr', 'lri.radiology_request_id = lr.id', 'INNER'],
            ['soap s', 'lr.soap_id = s.id', 'LEFT'],
            ['registration_destination rd', 's.registration_destination_id = rd.id', 'LEFT'],
            ['health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'LEFT'],
        ];

        $order = ['a.date_added' => 'ASC'];

        // Build query
        $this->db
            ->select($select)
            ->from('radiology_request_hpp a');

        foreach ($join as $j) {
            $this->db->join($j[0], $j[1], $j[2]);
        }

        $this->db->where('a.is_delete', 0);
        $this->db->where('rd.register_date >=', $date_start);
        $this->db->where('rd.register_date <=', $date_end);

        $query = $this->db->get();
        $radiology = $query->result();

        for ($i=0; $i < count($radiology); $i++) { 
            $total = $radiology[$i]->total;
            $health_facility_name_item = $radiology[$i]->health_facility_name_item;
            $radiology[$i]->health_facility_name_item      = $health_facility_name_item;
            $radiology[$i]->total_hpp                 = $total;
        }

        $grouped = [];

        foreach ($radiology as $row) {

            $hf_item = $row->health_facility_name_item ?: 'UNKNOWN';

            if (!isset($grouped[$hf_item])) {
                $grouped[$hf_item] = [
                    'health_facility_name_item' => $hf_item,
                    'total_hpp' => 0,
                ];
            }

            $grouped[$hf_item]['total_hpp'] += $row->total_hpp;
        }

        // Convert to indexed array
        $grouped_result = array_values($grouped);
        
        $this->REST_Return(200, 'SUCCESS', $grouped_result);
        return;

    }

    public function check_margin_get() {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 0); 
        
        // Token Validation
        // $is_valid_token = $this->authorization_token->validateToken();
        // if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
        //     $response_data = 'Invalid Token.';
        //     $this->REST_Return(401, $is_valid_token['message'] , $response_data);
        //     return;
        // }

        // if (!isset($q_strings['registration_destination_id'])) {
        //     $this->REST_Return(400, 'registration_destination_id is required');
        //     return;
        // } 

        // $registration_destination_id = $q_strings['registration_destination_id'];
        if (!isset($q_strings['date_start']) || !isset($q_strings['date_end'])) {
            $this->REST_Return(400, 'date_start & date_end is required');
            return;
        } 

        $date_start = $q_strings['date_start'] . ' 00:00:00';
        $date_end = $q_strings['date_end'] . ' 23:59:59';
        $health_facility_item_id = $q_strings['health_facility_item_id'];

        $output = $this->db->query("SELECT
        rd.id, 
        rd.treatment_done, 
        rd.registration_id, 
        p.norm, 
        br.overhead, 
        p.name as name_patient, 
        hfi.name as health_facility_item_name, 
        hfi.name as health_facility_item_name, 
        rd.register_date,
        rd.checkout_date,
        ms.desc_inacbg,
        ms.cost_inacbg

        FROM registration_destination rd 

        INNER JOIN registration r ON rd.registration_id = r.id
        INNER JOIN registration_insurance ri ON rd.id = ri.registration_destination_id AND ri.is_primary = 1
        INNER JOIN health_facility_item hfi ON rd.health_facility_item_id = hfi.id
        INNER JOIN patient p ON r.norm = p.norm
        INNER JOIN helper h ON p.sex = h.code AND h.variable ='sex'
        INNER JOIN employee e ON rd.doctor_in_charge_id = e.id 
        INNER JOIN bed b ON rd.bed_id = b.id 
        INNER JOIN room br ON b.room_id = br.id 
        LEFT JOIN medical_services ms ON rd.id = ms.registration_destination_id 
        WHERE hfi.id =  '{$health_facility_item_id}'
        -- WHERE hfi.id = '2a2b727f-631e-4d46-86b1-410fb8e40445'
        AND ri.company_id = '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8'
        -- AND rd.is_checkout = 1
        -- AND ms.cost_inacbg >= 2
        AND rd.register_date >= '{$date_start}'
        AND rd.register_date <= '{$date_end}'
        ORDER BY rd.register_date DESC
        
        ")->result();

        
        for ($rd=0; $rd < count($output); $rd++) {

            $select = "
                a.id,
                a.no_order,
                a.date_added,
                pi.pharmacy_catalog_id,
                pi.qty,
                pi.qty_retur,
                pi.var_1,
                pi.var_3
            ";

            $this->db
            ->select($select)
            ->from('pharmacy_prescription a')
            ->join('pharmacy_prescription_item pi', 'a.id = pi.pharmacy_prescription_id')
            ->join('pharmacy_catalog pc', 'pi.pharmacy_catalog_id = pc.id')
            ->join('registration_destination rd', 'a.registration_destination_id = rd.id', 'LEFT')
            ->where('a.is_delete', 0)
            ->where('a.date_added >=', $output[$rd]->register_date)
            ->where('a.date_added <=', $date_end)
            ->where('a.flag', 1)
            ->where('pi.is_delete', 0)
            ->where_not_in('pc.suppliers_id', [
                '29607783-85c9-4c80-bb09-673436abd025',
                '4164b069-15de-4e30-8ec4-40ddb4c70797',
                '4b2b43ce-ddc8-4db9-a9bc-8031c3697e4c',
                '4dbaa98b-13fb-4cd7-a65c-015a3f216e36',
                '52e313cf-ba9b-4eb7-9916-b5d2863139c0',
                '72f7d7df-17b5-4de6-b59b-576b7799723a',
                '7668d7b4-e239-4cc2-8843-c0931c0fed52',
                '7fb91255-7be4-49c7-82e5-370ff86b9b41',
                '923b4d27-2cf4-4503-8769-9922aa0ec4c6',
                '9608f20f-21cb-47d3-87ee-badf8a4a9a51',
                '98175373-29a1-4dcf-b846-cde16d54eba8',
                'ced75311-fe14-4cb2-969a-a4a8b37d6782',
                '8df35771-114f-4b84-8699-e1034fc33138'])
            ->group_start()
                ->where('rd.registration_id', $output[$rd]->registration_id)
                ->or_group_start()
                    ->where('rd.id IS NULL', null, false)
                    ->where('a.registration_destination_id', $output[$rd]->id)
                ->group_end()
            ->group_end()
            ->group_by(['pi.pharmacy_prescription_id','pi.pharmacy_catalog_id'])
            ->order_by('a.date_added', 'ASC');

            $prescription = $this->db->get()->result();


            foreach ($prescription as $i => $row) {

                $qty        = (float)$row->qty;
                $qty_retur  = (float)$row->qty_retur;
                $var_3      = (float)$row->var_3;

                // $hna = $var_1 / 1.3;

                // // min(HPP)
                // $hpp = $row->var_3 ? (float)$row->var_3 : $hna;
                // if ($hpp > $hna) $hpp = $hna;

                $hpp = $row->var_3 ? (float)$row->var_3 : $var_3;

                // qty retur
                $qty_final = $qty_retur ? ($qty - $qty_retur) : $qty;
                if ($qty_final < 0) $qty_final = 0;

                // total_hpp
                $prescription[$i]->total_hpp = $hpp * $qty_final;
            }
    
    
            $select_lab = "
                a.total,
            ";
    
            $join_lab = [
                ['laboratory_request_item lri', 'a.laboratory_request_item_id = lri.id', 'INNER'],
                ['laboratory_request lr', 'lri.laboratory_request_id = lr.id', 'INNER'],
                ['soap s', 'lr.soap_id = s.id', 'LEFT'],
                ['registration_destination rd', 's.registration_destination_id = rd.id', 'LEFT'],
            ];
            
            // Build query
            $this->db
                ->select($select_lab)
                ->from('laboratory_request_hpp a');
    
            foreach ($join_lab as $j) {
                $this->db->join($j[0], $j[1], $j[2]);
            }
    
            $this->db->where('lri.is_delete', 0);
            $this->db->where('lri.hpp', 2);
            $this->db->where('lr.is_delete', 0);
            $this->db->where('rd.registration_id', $output[$rd]->registration_id);
    
            $query_lab = $this->db->get();
            $laboratorium = $query_lab->result();

            
            $select_rad = "
                a.total,
            ";
    
            $join_rad = [
                ['radiology_request_item lri', 'a.radiology_request_item_id = lri.id', 'INNER'],
                ['radiology_request lr', 'lri.radiology_request_id = lr.id', 'INNER'],
                ['soap s', 'lr.soap_id = s.id', 'LEFT'],
                ['registration_destination rd', 's.registration_destination_id = rd.id', 'LEFT'],
            ];
            
            // Build query
            $this->db
                ->select($select_rad)
                ->from('radiology_request_hpp a');
    
            foreach ($join_rad as $j) {
                $this->db->join($j[0], $j[1], $j[2]);
            }
    
            $this->db->where('lri.is_delete', 0);
            $this->db->where('lri.hpp', 2);
            $this->db->where('lr.is_delete', 0);
            $this->db->where('rd.registration_id', $output[$rd]->registration_id);
    
            $query_rad = $this->db->get();
            $radiologi = $query_rad->result();
    
            // $grouped = array('total_hpp_farmasi'=>0,'total_hpp_laboratorium'=>0);
            $output[$rd]->total_hpp_farmasi = 0;
            $output[$rd]->total_hpp_laboratorium = 0;
            $output[$rd]->total_hpp_radiology = 0;
    
            foreach ($prescription as $row_farmasi) {
                $output[$rd]->total_hpp_farmasi += $row_farmasi->total_hpp;
            }
    
            foreach ($laboratorium as $row_lab) {
                $output[$rd]->total_hpp_laboratorium += $row_lab->total;
            }
    
            foreach ($radiologi as $row_rad) {
                $output[$rd]->total_hpp_radiology += $row_rad->total;
            }

            // overhead
            $checkin   = $output[$rd]->register_date;
            $checkout  = $output[$rd]->checkout_date != null ? $output[$rd]->checkout_date  : date('Y-m-d H:i:s');
            $start     = new DateTime($checkin);
            $end       = new DateTime($checkout);

            // Hitung selisih waktu
            $diff = $end->diff($start);

            // Ambil selisih hari dasar
            $totalDays = $diff->days;

            // Jika ada sisa jam/menit, tambahkan 1 hari
            if ($diff->h > 0 || $diff->i > 0) {
                $totalDays += 1;
            }

            // Jika total hari 0, minimal tetap 1 hari
            if ($totalDays == 0) {
                $totalDays = 1;
            }

            // Hitung total harga
            $output[$rd]->overhead = $output[$rd]->overhead * $totalDays;
            $output[$rd]->jm       = ($output[$rd]->cost_inacbg * 20)/100;
            // overhead

            $output[$rd]->total_hpp = $output[$rd]->total_hpp_farmasi + $output[$rd]->total_hpp_laboratorium + $output[$rd]->total_hpp_radiology + $output[$rd]->overhead + $output[$rd]->jm;
    
            // Convert to indexed array
            // $grouped_result =  array($grouped);
        }

        
        $this->REST_Return(200, 'SUCCESS', $output);
        return;

    }

    public function check_margin_prescription_get() {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 0); 
        
        // Token Validation
        // $is_valid_token = $this->authorization_token->validateToken();
        // if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
        //     $response_data = 'Invalid Token.';
        //     $this->REST_Return(401, $is_valid_token['message'] , $response_data);
        //     return;
        // }

        // if (!isset($q_strings['registration_destination_id'])) {
        //     $this->REST_Return(400, 'registration_destination_id is required');
        //     return;
        // } 

        // $registration_destination_id = $q_strings['registration_destination_id'];
        if (!isset($q_strings['registration_id'])) {
            $this->REST_Return(400, 'registration_id is required');
            return;
        } 

        $registration_id = $q_strings['registration_id'];

        $output = $this->db->query("SELECT
        rd.id, 
        rd.registration_id, 
        p.norm, 
        br.overhead, 
        p.name as name_patient, 
        hfi.name as health_facility_item_name, 
        hfi.name as health_facility_item_name, 
        rd.register_date,
        rd.checkout_date,
        ms.desc_inacbg,
        ms.cost_inacbg

        FROM registration_destination rd 

        INNER JOIN registration r ON rd.registration_id = r.id
        INNER JOIN registration_insurance ri ON rd.id = ri.registration_destination_id AND ri.is_primary = 1
        INNER JOIN health_facility_item hfi ON rd.health_facility_item_id = hfi.id
        INNER JOIN patient p ON r.norm = p.norm
        INNER JOIN helper h ON p.sex = h.code AND h.variable ='sex'
        INNER JOIN employee e ON rd.doctor_in_charge_id = e.id 
        INNER JOIN bed b ON rd.bed_id = b.id 
        INNER JOIN room br ON b.room_id = br.id 
        LEFT JOIN medical_services ms ON rd.id = ms.registration_destination_id 
        WHERE rd.registration_id =  '{$registration_id}'
        
        ")->result();

        
        for ($rd=0; $rd < count($output); $rd++) {

            $select = "
                a.id,
                a.no_order,
                a.date_added,
                pi.pharmacy_catalog_id,
                pc.name as pharmacy_catalog_name,
                pi.qty,
                pi.qty_retur,
                pi.var_1,
                pi.var_3
            ";

            $this->db
            ->select($select)
            ->from('pharmacy_prescription a')
            ->join('pharmacy_prescription_item pi', 'a.id = pi.pharmacy_prescription_id')
            ->join('pharmacy_catalog pc', 'pi.pharmacy_catalog_id = pc.id')
            ->join('registration_destination rd', 'a.registration_destination_id = rd.id', 'LEFT')
            ->where('a.is_delete', 0)
            ->where('a.date_added >=', $output[$rd]->register_date)
            ->where('a.date_added <=', $output[$rd]->checkout_date != null ? $output[$rd]->checkout_date  : date('Y-m-d H:i:s'))
            ->where('a.flag', 1)
            ->where('pi.is_delete', 0)
            ->where_not_in('pc.suppliers_id', [
                '29607783-85c9-4c80-bb09-673436abd025',
                '4164b069-15de-4e30-8ec4-40ddb4c70797',
                '4b2b43ce-ddc8-4db9-a9bc-8031c3697e4c',
                '4dbaa98b-13fb-4cd7-a65c-015a3f216e36',
                '52e313cf-ba9b-4eb7-9916-b5d2863139c0',
                '72f7d7df-17b5-4de6-b59b-576b7799723a',
                '7668d7b4-e239-4cc2-8843-c0931c0fed52',
                '7fb91255-7be4-49c7-82e5-370ff86b9b41',
                '923b4d27-2cf4-4503-8769-9922aa0ec4c6',
                '9608f20f-21cb-47d3-87ee-badf8a4a9a51',
                '98175373-29a1-4dcf-b846-cde16d54eba8',
                'ced75311-fe14-4cb2-969a-a4a8b37d6782',
                '8df35771-114f-4b84-8699-e1034fc33138'])
            ->group_start()
                ->where('rd.registration_id', $output[$rd]->registration_id)
                ->or_group_start()
                    ->where('rd.id IS NULL', null, false)
                    ->where('a.registration_destination_id', $output[$rd]->id)
                ->group_end()
            ->group_end()
            ->group_by(['pi.pharmacy_prescription_id','pi.pharmacy_catalog_id'])
            ->order_by('a.date_added', 'ASC');

            $prescription = $this->db->get()->result();


            foreach ($prescription as $i => $row) {

                $qty        = (float)$row->qty;
                $qty_retur  = (float)$row->qty_retur;
                $var_1      = (float)$row->var_1;

                // min(HPP)
                $hpp = $row->var_3 ? (float)$row->var_3 : $hna;

                // qty retur
                $qty_final = $qty_retur ? ($qty - $qty_retur) : $qty;
                if ($qty_final < 0) $qty_final = 0;

                // total_hpp
                $prescription[$i]->qty = $qty_final;
                $prescription[$i]->total_hpp = $hpp * $qty_final;
            }
    
    
           
            // Convert to indexed array
            // $grouped_result =  array($grouped);
        }

        
        $this->REST_Return(200, 'SUCCESS', $prescription);
        return;

    }

    public function check_margin_laboratory_get() {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 0); 
        
        // Token Validation
        // $is_valid_token = $this->authorization_token->validateToken();
        // if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
        //     $response_data = 'Invalid Token.';
        //     $this->REST_Return(401, $is_valid_token['message'] , $response_data);
        //     return;
        // }

        // if (!isset($q_strings['registration_destination_id'])) {
        //     $this->REST_Return(400, 'registration_destination_id is required');
        //     return;
        // } 

        // $registration_destination_id = $q_strings['registration_destination_id'];
        if (!isset($q_strings['registration_id'])) {
            $this->REST_Return(400, 'registration_id is required');
            return;
        } 

        $registration_id = $q_strings['registration_id'];

        $output = $this->db->query("SELECT
        rd.id, 
        rd.registration_id, 
        p.norm, 
        br.overhead, 
        p.name as name_patient, 
        hfi.name as health_facility_item_name, 
        hfi.name as health_facility_item_name, 
        rd.register_date,
        rd.checkout_date,
        ms.desc_inacbg,
        ms.cost_inacbg

        FROM registration_destination rd 

        INNER JOIN registration r ON rd.registration_id = r.id
        INNER JOIN registration_insurance ri ON rd.id = ri.registration_destination_id AND ri.is_primary = 1
        INNER JOIN health_facility_item hfi ON rd.health_facility_item_id = hfi.id
        INNER JOIN patient p ON r.norm = p.norm
        INNER JOIN helper h ON p.sex = h.code AND h.variable ='sex'
        INNER JOIN employee e ON rd.doctor_in_charge_id = e.id 
        INNER JOIN bed b ON rd.bed_id = b.id 
        INNER JOIN room br ON b.room_id = br.id 
        LEFT JOIN medical_services ms ON rd.id = ms.registration_destination_id 
        WHERE rd.registration_id =  '{$registration_id}'
        
        ")->result();

        
        for ($rd=0; $rd < count($output); $rd++) {

             $select_lab = "
                a.total,
                lr.date_request,
                a.qty,
                pc.name as pharmacy_catalog_name,
                lp.name as package_name,
                a.hpp,
            ";
    
            $join_lab = [
                ['laboratory_request_item lri', 'a.laboratory_request_item_id = lri.id', 'INNER'],
                ['laboratory_request lr', 'lri.laboratory_request_id = lr.id', 'INNER'],
                ['pharmacy_catalog pc', 'a.pharmacy_catalog_id = pc.id', 'INNER'],
                ['laboratory_package lp', 'lri.laboratory_package_id = lp.id', 'INNER'],
                ['soap s', 'lr.soap_id = s.id', 'LEFT'],
                ['registration_destination rd', 's.registration_destination_id = rd.id', 'LEFT'],
            ];
            
            // Build query
            $this->db
                ->select($select_lab)
                ->from('laboratory_request_hpp a');
    
            foreach ($join_lab as $j) {
                $this->db->join($j[0], $j[1], $j[2]);
            }
    
            $this->db->where('lri.is_delete', 0);
            $this->db->where('lri.hpp', 2);
            $this->db->where('lr.is_delete', 0);
            $this->db->where('rd.registration_id', $output[$rd]->registration_id);
    
            $query_lab = $this->db->get();
            $laboratorium = $query_lab->result();

           
            // Convert to indexed array
            // $grouped_result =  array($grouped);
        }

        
        $this->REST_Return(200, 'SUCCESS', $laboratorium);
        return;

    }

    public function check_margin_radiology_get() {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 0); 
        
        // Token Validation
        // $is_valid_token = $this->authorization_token->validateToken();
        // if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
        //     $response_data = 'Invalid Token.';
        //     $this->REST_Return(401, $is_valid_token['message'] , $response_data);
        //     return;
        // }

        // if (!isset($q_strings['registration_destination_id'])) {
        //     $this->REST_Return(400, 'registration_destination_id is required');
        //     return;
        // } 

        // $registration_destination_id = $q_strings['registration_destination_id'];
        if (!isset($q_strings['registration_id'])) {
            $this->REST_Return(400, 'registration_id is required');
            return;
        } 

        $registration_id = $q_strings['registration_id'];

        $output = $this->db->query("SELECT
        rd.id, 
        rd.registration_id, 
        p.norm, 
        br.overhead, 
        p.name as name_patient, 
        hfi.name as health_facility_item_name, 
        hfi.name as health_facility_item_name, 
        rd.register_date,
        rd.checkout_date,
        ms.desc_inacbg,
        ms.cost_inacbg

        FROM registration_destination rd 

        INNER JOIN registration r ON rd.registration_id = r.id
        INNER JOIN registration_insurance ri ON rd.id = ri.registration_destination_id AND ri.is_primary = 1
        INNER JOIN health_facility_item hfi ON rd.health_facility_item_id = hfi.id
        INNER JOIN patient p ON r.norm = p.norm
        INNER JOIN helper h ON p.sex = h.code AND h.variable ='sex'
        INNER JOIN employee e ON rd.doctor_in_charge_id = e.id 
        INNER JOIN bed b ON rd.bed_id = b.id 
        INNER JOIN room br ON b.room_id = br.id 
        LEFT JOIN medical_services ms ON rd.id = ms.registration_destination_id 
        WHERE rd.registration_id =  '{$registration_id}'
        
        ")->result();

        
        for ($rd=0; $rd < count($output); $rd++) {

             $select_lab = "
                a.total,
                lr.date_request,
                a.qty,
                pc.name as pharmacy_catalog_name,
                lp.name as package_name,
                a.hpp,
            ";
    
            $join_lab = [
                ['radiology_request_item lri', 'a.radiology_request_item_id = lri.id', 'INNER'],
                ['radiology_request lr', 'lri.radiology_request_id = lr.id', 'INNER'],
                ['pharmacy_catalog pc', 'a.pharmacy_catalog_id = pc.id', 'INNER'],
                ['radiology_package lp', 'lri.radiology_package_id = lp.id', 'INNER'],
                ['soap s', 'lr.soap_id = s.id', 'LEFT'],
                ['registration_destination rd', 's.registration_destination_id = rd.id', 'LEFT'],
            ];
            
            // Build query
            $this->db
                ->select($select_lab)
                ->from('radiology_request_hpp a');
    
            foreach ($join_lab as $j) {
                $this->db->join($j[0], $j[1], $j[2]);
            }
    
            $this->db->where('lri.is_delete', 0);
            $this->db->where('lri.hpp', 2);
            $this->db->where('lr.is_delete', 0);
            $this->db->where('rd.registration_id', $output[$rd]->registration_id);
    
            $query_lab = $this->db->get();
            $radiology = $query_lab->result();

           
            // Convert to indexed array
            // $grouped_result =  array($grouped);
        }

        
        $this->REST_Return(200, 'SUCCESS', $radiology);
        return;

    }

    public function diagnosa_get() {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 0); 
      
        if (!isset($q_strings['registration_id'])) {
            $this->REST_Return(400, 'registration_id is required');
            return;
        } 

        $registration_id = $q_strings['registration_id'];

        $output = $this->db->query("SELECT
        rd.id, 
        rd.registration_id, 
        p.norm, 
        br.overhead, 
        p.name as name_patient, 
        hfi.name as health_facility_item_name, 
        hfi.name as health_facility_item_name, 
        rd.register_date,
        rd.checkout_date,
        ms.desc_inacbg,
        ms.cost_inacbg

        FROM registration_destination rd 

        INNER JOIN registration r ON rd.registration_id = r.id
        INNER JOIN registration_insurance ri ON rd.id = ri.registration_destination_id AND ri.is_primary = 1
        INNER JOIN health_facility_item hfi ON rd.health_facility_item_id = hfi.id
        INNER JOIN patient p ON r.norm = p.norm
        INNER JOIN helper h ON p.sex = h.code AND h.variable ='sex'
        INNER JOIN employee e ON rd.doctor_in_charge_id = e.id 
        INNER JOIN bed b ON rd.bed_id = b.id 
        INNER JOIN room br ON b.room_id = br.id 
        LEFT JOIN medical_services ms ON rd.id = ms.registration_destination_id 
        WHERE rd.registration_id =  '{$registration_id}'
        
        ")->result();

        
        for ($rd=0; $rd < count($output); $rd++) {

            $soap_a = $this->db->query("
                SELECT saa.id,saa.icd10_id,icd.code,icd.name
                FROM soap_a_action as saa
                INNER JOIN soap_a sa ON saa.soap_a_id = sa.id
                INNER JOIN soap s ON sa.soap_id = s.id
                INNER JOIN registration_destination rd ON s.registration_destination_id = rd.id
                INNER JOIN icd10 icd ON saa.icd10_id = icd.id
                WHERE rd.registration_id =  '{$registration_id}'
                GROUP BY icd.code
                ORDER BY saa.index ASC
            ")->result();
            

            $soap_a_coding = $this->db->query("
                SELECT icd.code
                FROM soap_a_action as saa
                INNER JOIN soap_a sa ON saa.soap_a_id = sa.id
                INNER JOIN soap s ON sa.soap_id = s.id
                INNER JOIN registration_destination rd ON s.registration_destination_id = rd.id
                INNER JOIN icd10 icd ON saa.icd10_id = icd.id
                WHERE rd.registration_id =  '{$registration_id}'
                GROUP BY icd.code
                ORDER BY saa.index ASC
            ")->result();

            $soap_p = $this->db->query("
                SELECT spp.id,spp.icd9cm_id,icd.code,icd.name
                FROM soap_p_action as spp
                INNER JOIN soap_p sa ON spp.soap_p_id = sa.id
                INNER JOIN soap s ON sa.soap_id = s.id
                INNER JOIN registration_destination rd ON s.registration_destination_id = rd.id
                INNER JOIN icd9cm icd ON spp.icd9cm_id = icd.id
                WHERE rd.registration_id =  '{$registration_id}'
                GROUP BY icd.code
            ")->result();

            $soap_p_coding = $this->db->query("
                SELECT icd.code
                FROM soap_p_action as spp
                INNER JOIN soap_p sa ON spp.soap_p_id = sa.id
                INNER JOIN soap s ON sa.soap_id = s.id
                INNER JOIN registration_destination rd ON s.registration_destination_id = rd.id
                INNER JOIN icd9cm icd ON spp.icd9cm_id = icd.id
                WHERE rd.registration_id =  '{$registration_id}'
                GROUP BY icd.code
            ")->result();
        }
        

        // 113.212.165.221:17777/drg
        $request = '{
            "diags": "'.$soap_a_coding.'",
            "procs": "'.$soap_p_coding.'",
            "isInpatient": true,
            "isNewborn": false,
            "isYoung": true,
            "isOld": false,
            "isMale": false,
            "los": 43,
            "adl": 40,
            "dds": "6",
            "weight": "70"
        }';
        
        $msg = $this->Request($request);
        
        $this->REST_Return(200, 'SUCCESS', array('icd10' => $soap_a,'icd9cm' => $soap_p,'grouping'=>$msg));
        return;

    }
     
    function Request($request){
        $header = array("content-type: application/json");        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, '113.212.165.221:17777/drg');
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        $response = curl_exec($ch);
        //echo $hasildecrypt;
        $msg = json_decode($response,true);
        return $response;
    }

    public function save_diagnosa_put($p = 0) {
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
                /* Start Transaction */

                $registration_destination_id    = $dt_put['registration_destination_id'];
                $treatment_done                 = $dt_put['treatment_done'];

                 $registration_destination = $this->db->query("
                    SELECT rd.id,rd.registration_id,rd.health_facility_item_id
                    FROM registration_destination rd
                    WHERE rd.id = '{$registration_destination_id}'
                ")->result();

                $this->db->trans_start();

                if ($registration_destination) {
                    

                    // 113.212.165.221:17777/drg
                    $request = '{
                        "diags": "'.$soap_a_coding.'",
                        "procs": "'.$soap_p_coding.'",
                        "isInpatient": true,
                        "isNewborn": false,
                        "isYoung": true,
                        "isOld": false,
                        "isMale": false,
                        "los": 43,
                        "adl": 40,
                        "dds": "6",
                        "weight": "70"
                    }';
                    
                    $msg = $this->Request($request);
                    
                    $this->REST_Return(200, 'SUCCESS', array('icd10' => $soap_a,'icd9cm' => $soap_p,'grouping'=>$msg));
                    return;

                    $this->Registration_destination->replace(
                        array(
                            'id'                        => $registration_destination_id,
                            'registration_id'           => $registration_destination[0]->registration_id,
                            'health_facility_item_id'   => $registration_destination[0]->health_facility_item_id,
                            'treatment_done'            => $treatment_done,
                        )
                    );
                }


                $this->db->trans_complete();
                if ($this->db->trans_status() === false) {
                    $response_data = array(
                        'error'     => $this->db->error(),
                        'data'      => 'data',
                        'query'     => $this->db->last_query()
                    );
                    $this->REST_Return(500, 'error while trying to update', $response_data);
                    return;
                }
                /* End Transaction */
                if ($this->db->trans_status() === false) {
                    $response_data = array();
                    $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                } else {
                    $response_data = $id;
                    $this->REST_Return(200, 'SUCCESS', $registration_destination_id);
                }
                return;     
            // }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }
}