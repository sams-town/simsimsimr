<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Unit extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
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

        $limit = $q_strings['limit'] ?? 100;
        $offset = $q_strings['offset'] ?? 0;

        $this->$model->limit = $limit;
        $this->$model->offset = $offset;

        if(isset($q_strings['limit']) || isset($q_strings['offset'])) {
            unset($q_strings['limit']);
            unset($q_strings['offset']);
        }

        $package_id = $q_strings['package_id'] ?? null;

        $unit_ids = [];

        if ($package_id != null) {
            if ($package_id == '357310dd-904f-4b24-92a2-7987a75cdfc5') { // TAB
                array_push($unit_ids, '1e051835-cd6c-4d45-8f6b-2fed8aa56d02'); // TAB
                array_push($unit_ids, '61a257e9-2e4e-4e47-8387-0bc0fe90aeec'); // MG
                // array_push($unit_ids, '963fb7d8-9141-4f16-bcc7-727d0392ac02'); // GR
            }

            if ($package_id == 'c5bc8af2-c4eb-4d74-9566-39048ba2f00a') { // KAPSUL
                array_push($unit_ids, 'b0ae0afd-acc4-4654-86ce-e9417f28253a'); // KAPSUL
                array_push($unit_ids, '61a257e9-2e4e-4e47-8387-0bc0fe90aeec'); // MG
                // array_push($unit_ids, '963fb7d8-9141-4f16-bcc7-727d0392ac02'); // GR
            }

            if ($package_id == 'a0c282f5-6ee2-4577-b3c8-bf0779e515f5') { // SUPP
                array_push($unit_ids, 'bd5eab5b-ea81-4b2b-aa00-554c67ca84a4'); // SUPP
                array_push($unit_ids, '7208793f-f854-4e16-9167-95b166c4405e'); // OVULA
                array_push($unit_ids, '61a257e9-2e4e-4e47-8387-0bc0fe90aeec'); // MG
                array_push($unit_ids, '963fb7d8-9141-4f16-bcc7-727d0392ac02'); // GR
            }

            if ($package_id == 'd5df7392-0cc5-496b-a9ce-1c5b7426af3e') { // VIAL
                array_push($unit_ids, '18c95f16-604d-4b17-80e3-4efe7b8aca40'); // VIAL
                array_push($unit_ids, '61a257e9-2e4e-4e47-8387-0bc0fe90aeec'); // MG
                array_push($unit_ids, '02e218ab-81d6-4217-82d1-46b686d272a5'); // MCG
                array_push($unit_ids, 'ab328f03-5088-49e3-9f0d-c3ea95d0747d'); // ML
                array_push($unit_ids, '963fb7d8-9141-4f16-bcc7-727d0392ac02'); // GR
            }

            if ($package_id == '2340f991-643c-4409-af29-7072b594d1ef') { // AMPUL
                array_push($unit_ids, '4dfd0607-ab07-459d-a6ba-0b133e3827b1'); // AMPUL
                array_push($unit_ids, '61a257e9-2e4e-4e47-8387-0bc0fe90aeec'); // MG
                array_push($unit_ids, '02e218ab-81d6-4217-82d1-46b686d272a5'); // MCG
                array_push($unit_ids, 'ab328f03-5088-49e3-9f0d-c3ea95d0747d'); // ML
                array_push($unit_ids, '963fb7d8-9141-4f16-bcc7-727d0392ac02'); // GR
            }

            if ($package_id == 'c37c026c-f4e5-4f32-a4ff-1525bbe74803') { // TUBE
                array_push($unit_ids, 'e72b6358-8d74-447c-b5be-9623a1903470'); // OLES
                array_push($unit_ids, 'a6c14c74-d3c3-49ff-8ca9-45cc1682dbd5'); // TABUR
            }

            if ($package_id == '7afbf19d-209c-407f-adbf-17ce9c82728d') { // BOTOL
                array_push($unit_ids, 'ebf6382a-934d-485c-8430-de3a48604cbb'); // BOTOL
                array_push($unit_ids, '8ff1ce10-45cd-47fb-93bc-c6449946a099'); // TETES
                array_push($unit_ids, '5fd3d1f5-7e92-49b9-84a2-e6a01474d55a'); // TETES MIKRO
                array_push($unit_ids, 'f68098fa-f6b4-4df2-9a4f-bc8298663574'); // TETES MAKRO
                array_push($unit_ids, 'ab328f03-5088-49e3-9f0d-c3ea95d0747d'); // ML
                array_push($unit_ids, '61a257e9-2e4e-4e47-8387-0bc0fe90aeec'); // MG
                array_push($unit_ids, '654a4992-3ac2-476d-be22-2e7218ddefcb'); // C
                // array_push($unit_ids, 'f4d6a5a3-cc89-46c8-895d-c31417b27a78'); // SPRAY
                array_push($unit_ids, '0ea77797-c210-43de-a72a-da2344d513e8'); // PUFF
            }

            if ($package_id == '54f6e791-283d-479d-ba82-9a728cc5ce7b') { // SACHET
                array_push($unit_ids, 'acdd1748-d15a-4a7c-8d34-3fe5578e76ac'); // SACHET
            }

            if ($package_id == '7b430ad7-7462-47d3-bed7-7fb649369771') { // SYRINGE
                array_push($unit_ids, 'c61397ea-0a25-4a90-848d-3d0e824d784c'); // SYRINGE
                array_push($unit_ids, 'ab328f03-5088-49e3-9f0d-c3ea95d0747d'); // ML
                array_push($unit_ids, 'e1f18f3e-c563-4c51-a6b1-b529326f9447'); // UNIT
            }
        }

        $select = 'a.*';
        $where = array(
            'a.is_delete' => 0,
        );

        if (count($unit_ids) > 0) {
            $where_unit = 'id IN ("'.join('","', $unit_ids).'")';
            $this->db->where($where_unit);
        }

        $output = $this->$model->select(0, $select, $where);

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
}