<?php

function count_age($birth_date) {
    $age = (date('Y') - date('Y', strtotime($birth_date))); // Get the current year and the birth year
    if (date('md', strtotime($birth_date)) > date('md')) { 
        $age--; 
    } // Check if the birthday has occurred this year or not (to adjust the age)
    return $age;
}

function bride_lis($url = null,$param = null) {
    // url : 192.168.100.153/api_lis/index.php
    $url_acc   = '192.168.100.153';
    $ch = curl_init($url_acc.$url);
    $authorization   = array();
    // $authorization[] = "Authorization:Bearer ".$token; 
    // $authorization[] = "X-Session-ID:".$SESSIONID; 
    if($param !== null){
        $authorization[] = "Content-Type: application/json;charset=utf-8"; 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $authorization ); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch); 
    return json_decode($result,true); 
}


function jakartaTimestamp($time = '00:00:00', $dayOffset = 0) {
    $date = new DateTime("+$dayOffset days", new DateTimeZone('Asia/Jakarta'));
    $date->setTime(...explode(':', $time));
    return $date->getTimestamp();
}

function get_timestamp($dateString = 'now', $time = '00:00:00', $dayOffset = 0) {
    // Create DateTime object with given date in Asia/Jakarta timezone
    $date = new DateTime($dateString, new DateTimeZone('Asia/Jakarta'));
    
    // Apply day offset if needed
    if ($dayOffset != 0) {
        $date->modify("$dayOffset days");
    }

    // Set the time
    $date->setTime(...explode(':', $time));

    // Return UNIX timestamp
    return $date->getTimestamp();
}

/**
 * Nilai asli dari satuan 
 * 
 * Mencari nilai asli dari satuan yang bukan unit
 * cth. 1 C (Sendok Makan) = 15 ML
 * 1 Tablet = 300 MG
 * 
 * @param float $dose dosis yang diberikan pada resep dokter
 * @param string $unit_id satuan yang diberikan pada resep dokter
 * @param string $catalog_id id katalog obat untuk mengeluarkan nilai asli kemasan
 * @return float nilai asli dari katalog obat
 */
function clean_dose_unit($dose, $unit_id, $catalog_id) {    
    $ci=& get_instance();

    $catalog = $ci->db->query("
        SELECT dose, dose_unit_id, packaging_value, packaging_unit_id
        FROM pharmacy_catalog
        WHERE id = '$catalog_id'
    ")->result();

    $catalog_dose = $catalog[0]->dose;
    $catalog_dose_unit_id = $catalog[0]->dose_unit_id;
    $packaging_value = $catalog[0]->packaging_value;
    $packaging_unit_id = $catalog[0]->packaging_unit_id;

    if ($unit_id == '654a4992-3ac2-476d-be22-2e7218ddefcb') { // C (sendok makan)
        $result = $dose * 15;
    } elseif (
        $unit_id == '1e051835-cd6c-4d45-8f6b-2fed8aa56d02' // TABLET
        || $unit_id == 'b0ae0afd-acc4-4654-86ce-e9417f28253a' // KAPSUL
        || $unit_id == '7208793f-f854-4e16-9167-95b166c4405e' // OVULA
        || $unit_id == 'acdd1748-d15a-4a7c-8d34-3fe5578e76ac' // SACHET
        || $unit_id == 'bd5eab5b-ea81-4b2b-aa00-554c67ca84a4' // SUPP
        || $unit_id == 'c61397ea-0a25-4a90-848d-3d0e824d784c' // SYRINGE
        || $unit_id == '1787de8f-9974-4717-ba15-f6f3a2a8e8fe' // TUBE
        || $unit_id == 'ab06daef-5f29-4323-8844-e13a52f1c990' // PCS
    ) {
        // $result = $dose;
        
        // if ($packaging_value == $catalog_dose && $packaging_unit_id == $catalog_dose_unit_id) {
        //     $result = $dose * $catalog_dose;
        // }

        $result = $dose * $packaging_value;
    } elseif (
        $unit_id == '18c95f16-604d-4b17-80e3-4efe7b8aca40' // VIAL
        || $unit_id == '4dfd0607-ab07-459d-a6ba-0b133e3827b1' // AMPUL
        || $unit_id == 'ebf6382a-934d-485c-8430-de3a48604cbb' // BOTOL
    ) {
        $result = $dose * $packaging_value;
    } else {
        $result = $dose;
    }

    return (float) $result;
}

/**
 * Menukar satuan 
 * 
 * Satuan uncountable menjadi satuan yang countable
 * cth. AMPUL = ML
 * TABLET = MG
 * 
 * @param string $unit_id id satuan
 * @return string id satuan yang countable
 */
function clean_unit($unit_id) {
    if ($unit_id == '654a4992-3ac2-476d-be22-2e7218ddefcb') { // C (sendok makan)
        $result = 'ab328f03-5088-49e3-9f0d-c3ea95d0747d'; // ML
    } elseif ($unit_id == 'b2654738-ced3-431c-9503-da4e4b7f9562') { // TPM
        $result = 'ab328f03-5088-49e3-9f0d-c3ea95d0747d'; // ML
    } elseif ($unit_id == 'ebf6382a-934d-485c-8430-de3a48604cbb') { // BOTOL
        $result = 'ab328f03-5088-49e3-9f0d-c3ea95d0747d'; // ML
    } elseif ($unit_id == '4dfd0607-ab07-459d-a6ba-0b133e3827b1') { // AMPUL
        $result = 'ab328f03-5088-49e3-9f0d-c3ea95d0747d'; // ML
    } elseif ($unit_id == '18c95f16-604d-4b17-80e3-4efe7b8aca40') { // VIAL
        $result = 'ab328f03-5088-49e3-9f0d-c3ea95d0747d'; // ML
    } elseif ($unit_id == '1e051835-cd6c-4d45-8f6b-2fed8aa56d02') { // TABLET
        $result = '61a257e9-2e4e-4e47-8387-0bc0fe90aeec'; // MG
    } elseif ($unit_id == 'b0ae0afd-acc4-4654-86ce-e9417f28253a') { // KAPSUL
        $result = '61a257e9-2e4e-4e47-8387-0bc0fe90aeec'; // MG
    } elseif ($unit_id == 'b0ae0afd-acc4-4654-86ce-e9417f28253a') { // SUPP
        $result = '61a257e9-2e4e-4e47-8387-0bc0fe90aeec'; // MG
    } elseif ($unit_id == '7208793f-f854-4e16-9167-95b166c4405e') { // OVULA
        $result = '61a257e9-2e4e-4e47-8387-0bc0fe90aeec'; // MG
    } elseif ($unit_id == 'c61397ea-0a25-4a90-848d-3d0e824d784c') { // SYRINGE
        $result = 'ab328f03-5088-49e3-9f0d-c3ea95d0747d'; // ML
    } else {
        $result = $unit_id;
    }

    return $result;
}

/**
 * Menukar nama satuan 
 * 
 * Nama satuan uncountable menjadi nama satuan yang countable
 * 
 * @param string $unit_name nama satuan
 * @return string nama satuan yang countable
 */
function clean_unit_name($unit_name) {
    if ($unit_name == 'C') {
        $result = 'ML';
    } elseif ($unit_name == 'TAB') {
        $result = 'MG';
    } elseif ($unit_name == 'KAPSUL') {
        $result = 'MG';
    } elseif ($unit_name == 'AMPUL') {
        $result = 'ML';
    } elseif ($unit_name == 'VIAL') {
        $result = 'ML';
    } else {
        $result = $unit_name;
    }

    return $result;
}

function options_unit_package($package_id) {
    $ci=& get_instance();

    $ci->load->model('v1/setting/Unit_model', 'Unit');

    $options = [
        '357310dd-904f-4b24-92a2-7987a75cdfc5' => [ // TAB
            '1e051835-cd6c-4d45-8f6b-2fed8aa56d02', // TAB
            '61a257e9-2e4e-4e47-8387-0bc0fe90aeec', // MG
            // '963fb7d8-9141-4f16-bcc7-727d0392ac02', // GR
        ],
        'c5bc8af2-c4eb-4d74-9566-39048ba2f00a' => [ // KAPSUL
            'b0ae0afd-acc4-4654-86ce-e9417f28253a', // KAPSUL
            '61a257e9-2e4e-4e47-8387-0bc0fe90aeec', // MG
            // '963fb7d8-9141-4f16-bcc7-727d0392ac02', // GR
        ],
        'a0c282f5-6ee2-4577-b3c8-bf0779e515f5' => [ // SUPP
            'bd5eab5b-ea81-4b2b-aa00-554c67ca84a4', // SUPP
            '7208793f-f854-4e16-9167-95b166c4405e', // OVULA
            '61a257e9-2e4e-4e47-8387-0bc0fe90aeec', // MG
            '963fb7d8-9141-4f16-bcc7-727d0392ac02', // GR
        ],
        'd5df7392-0cc5-496b-a9ce-1c5b7426af3e' => [ // VIAL
            '18c95f16-604d-4b17-80e3-4efe7b8aca40', // VIAL
            '61a257e9-2e4e-4e47-8387-0bc0fe90aeec', // MG
            '02e218ab-81d6-4217-82d1-46b686d272a5', // MCG
            'ab328f03-5088-49e3-9f0d-c3ea95d0747d', // ML
            '963fb7d8-9141-4f16-bcc7-727d0392ac02', // GR
        ],
        '2340f991-643c-4409-af29-7072b594d1ef' => [ // AMPUL
            '4dfd0607-ab07-459d-a6ba-0b133e3827b1', // AMPUL
            '61a257e9-2e4e-4e47-8387-0bc0fe90aeec', // MG
            '02e218ab-81d6-4217-82d1-46b686d272a5', // MCG
            'ab328f03-5088-49e3-9f0d-c3ea95d0747d', // ML
            '963fb7d8-9141-4f16-bcc7-727d0392ac02', // GR
        ],
        'c37c026c-f4e5-4f32-a4ff-1525bbe74803' => [ // TUBE
            'e72b6358-8d74-447c-b5be-9623a1903470', // OLES
            'a6c14c74-d3c3-49ff-8ca9-45cc1682dbd5', // TABUR
        ],
        '7afbf19d-209c-407f-adbf-17ce9c82728d' => [ // BOTOL
            'ebf6382a-934d-485c-8430-de3a48604cbb', // BOTOL
            '8ff1ce10-45cd-47fb-93bc-c6449946a099', // TETES
            '5fd3d1f5-7e92-49b9-84a2-e6a01474d55a', // TETES MIKRO
            'f68098fa-f6b4-4df2-9a4f-bc8298663574', // TETES MAKRO
            'ab328f03-5088-49e3-9f0d-c3ea95d0747d', // ML
            '61a257e9-2e4e-4e47-8387-0bc0fe90aeec', // MG
            '654a4992-3ac2-476d-be22-2e7218ddefcb', // C
            // 'f4d6a5a3-cc89-46c8-895d-c31417b27a78', // SPRAY
            '0ea77797-c210-43de-a72a-da2344d513e8', // PUFF
        ],
        '54f6e791-283d-479d-ba82-9a728cc5ce7b' => [ // SACHET
            'acdd1748-d15a-4a7c-8d34-3fe5578e76ac', // SACHET
        ],
        '7b430ad7-7462-47d3-bed7-7fb649369771' => [ // SYRINGE
            'c61397ea-0a25-4a90-848d-3d0e824d784c', // SYRINGE
            'ab328f03-5088-49e3-9f0d-c3ea95d0747d', // ML
            'e1f18f3e-c563-4c51-a6b1-b529326f9447', // UNIT
        ],
    ];

    $select = 'a.id, a.name';
    $where = array(
        'a.is_delete' => 0,
    );
    if (count($options[$package_id]) > 0) {
        $where_unit = 'id IN ("'.join('","', $options[$package_id]).'")';
        $ci->db->where($where_unit);
    }
    $unit = $ci->Unit->select(0, $select, $where, [], ['a.name' => 'ASC']);

    return $unit;
}

function count_administer_times_now($signatura_times_id, $catalog_date) {
    $now = strtotime($catalog_date);
    
    $morning           = get_timestamp(date('Y-m-d', $now) ,'06:00:00');
    $after_morning     = get_timestamp(date('Y-m-d', $now) ,'10:00:00');
    $noon              = get_timestamp(date('Y-m-d', $now) ,'14:00:00');
    $afternoon         = get_timestamp(date('Y-m-d', $now) ,'18:00:00');
    $evening           = get_timestamp(date('Y-m-d', $now) ,'22:00:00');

    if ($now > $morning && $now < $noon) {
        if ($signatura_times_id == '95727030-63ba-4e4d-9b82-2dfb064eaa74') { // 4 JAM
            if ($now < $after_morning) {
                $times = 6;
            } else {
                $times = 5;
            }
        }
        if ($signatura_times_id == '8f20d2ac-df6f-42e5-a804-009343c0d6f8') { // 6 JAM
            $times = 4;
        } elseif ($signatura_times_id == '26e33f75-a6bc-4624-b75a-64f545485c56') { // 8 JAM
            $times = 3;
        } elseif ($signatura_times_id == '20446fea-c42d-432e-9a1a-15ffad0c9dd4') { // 12 JAM
            $times = 2;
        } elseif ($signatura_times_id == 'a061feed-36d3-458e-9c5f-a1ac63e92075') { // 18 JAM
            $times = 2;
        } elseif ($signatura_times_id == 'b69d3707-ec6e-4f58-8aa2-e874d97c97e9') { // 24 JAM
            $times = 1;
        } elseif ($signatura_times_id == '7783211c-4d01-4a1a-921c-252b504e6a38') { // 36 JAM
            $times = 1;
        }
        // echo 'pagi menjelang siang';
    } elseif ($now > $noon && $now < $afternoon) {
        if ($signatura_times_id == '95727030-63ba-4e4d-9b82-2dfb064eaa74') { // 4 JAM
            $times = 4;
        } elseif ($signatura_times_id == '8f20d2ac-df6f-42e5-a804-009343c0d6f8') { // 6 JAM
            $times = 3;
        } elseif ($signatura_times_id == '26e33f75-a6bc-4624-b75a-64f545485c56') { // 8 JAM
            $times = 2;
        } elseif ($signatura_times_id == '20446fea-c42d-432e-9a1a-15ffad0c9dd4') { // 12 JAM
            $times = 2;
        } elseif ($signatura_times_id == 'a061feed-36d3-458e-9c5f-a1ac63e92075') { // 18 JAM
            $times = 2;
        } elseif ($signatura_times_id == 'b69d3707-ec6e-4f58-8aa2-e874d97c97e9') { // 24 JAM
            $times = 1;
        } elseif ($signatura_times_id == '7783211c-4d01-4a1a-921c-252b504e6a38') { // 36 JAM
            $times = 1;
        }
        // echo 'siang menjelang sore';
    } elseif ($now > $afternoon && $now < $evening) {
        if ($signatura_times_id == '95727030-63ba-4e4d-9b82-2dfb064eaa74') { // 4 JAM
            $times = 4;
        } elseif ($signatura_times_id == '8f20d2ac-df6f-42e5-a804-009343c0d6f8') { // 6 JAM
            $times = 2;
        } elseif ($signatura_times_id == '26e33f75-a6bc-4624-b75a-64f545485c56') { // 8 JAM
            $times = 2;
        } elseif ($signatura_times_id == '20446fea-c42d-432e-9a1a-15ffad0c9dd4') { // 12 JAM
            $times = 2;
        } elseif ($signatura_times_id == 'a061feed-36d3-458e-9c5f-a1ac63e92075') { // 18 JAM
            $times = 2;
        } elseif ($signatura_times_id == 'b69d3707-ec6e-4f58-8aa2-e874d97c97e9') { // 24 JAM
            $times = 1;
        } elseif ($signatura_times_id == '7783211c-4d01-4a1a-921c-252b504e6a38') { // 36 JAM
            $times = 1;
        }
        // echo 'sore menjelang malam';
    } elseif ($now > $evening) {
        if ($signatura_times_id == '95727030-63ba-4e4d-9b82-2dfb064eaa74') { // 4 JAM
            $times = 6;
        } elseif ($signatura_times_id == '8f20d2ac-df6f-42e5-a804-009343c0d6f8') { // 6 JAM
            $times = 4;
        } elseif ($signatura_times_id == '26e33f75-a6bc-4624-b75a-64f545485c56') { // 8 JAM
            $times = 4;
        } elseif ($signatura_times_id == '20446fea-c42d-432e-9a1a-15ffad0c9dd4') { // 12 JAM
            $times = 3;
        } elseif ($signatura_times_id == 'a061feed-36d3-458e-9c5f-a1ac63e92075') { // 18 JAM
            $times = 2;
        } elseif ($signatura_times_id == 'b69d3707-ec6e-4f58-8aa2-e874d97c97e9') { // 24 JAM
            $times = 1;
        } elseif ($signatura_times_id == '7783211c-4d01-4a1a-921c-252b504e6a38') { // 36 JAM
            $times = 1;
        }
        // echo 'malam menjelang pagi';
    } elseif ($now < $morning) {
        if ($signatura_times_id == '95727030-63ba-4e4d-9b82-2dfb064eaa74') { // 4 JAM
            $times = 5;
        } elseif ($signatura_times_id == '8f20d2ac-df6f-42e5-a804-009343c0d6f8') { // 6 JAM
            $times = 4;
        } elseif ($signatura_times_id == '26e33f75-a6bc-4624-b75a-64f545485c56') { // 8 JAM
            $times = 3;
        } elseif ($signatura_times_id == '20446fea-c42d-432e-9a1a-15ffad0c9dd4') { // 12 JAM
            $times = 2;
        } elseif ($signatura_times_id == 'a061feed-36d3-458e-9c5f-a1ac63e92075') { // 18 JAM
            $times = 2;
        } elseif ($signatura_times_id == 'b69d3707-ec6e-4f58-8aa2-e874d97c97e9') { // 24 JAM
            $times = 1;
        } elseif ($signatura_times_id == '7783211c-4d01-4a1a-921c-252b504e6a38') { // 36 JAM
            $times = 1;
        }
    } else {
        if ($signatura_times_id == '95727030-63ba-4e4d-9b82-2dfb064eaa74') { // 4 JAM
            $times = 5;
        } elseif ($signatura_times_id == '8f20d2ac-df6f-42e5-a804-009343c0d6f8') { // 6 JAM
            $times = 4;
        } elseif ($signatura_times_id == '26e33f75-a6bc-4624-b75a-64f545485c56') { // 8 JAM
            $times = 3;
        } elseif ($signatura_times_id == '20446fea-c42d-432e-9a1a-15ffad0c9dd4') { // 12 JAM
            $times = 2;
        } elseif ($signatura_times_id == 'a061feed-36d3-458e-9c5f-a1ac63e92075') { // 18 JAM
            $times = 2;
        } elseif ($signatura_times_id == 'b69d3707-ec6e-4f58-8aa2-e874d97c97e9') { // 24 JAM
            $times = 1;
        } elseif ($signatura_times_id == '7783211c-4d01-4a1a-921c-252b504e6a38') { // 36 JAM
            $times = 1;
        }
    }

    if ($signatura_times_id == 'ce7206d4-fb67-49d2-9a3a-3115a8c833f9') { // 1 JAM
        // Current time
        $now = new DateTime(date('Y-m-d H:i:s', $now));
        // Midnight (start of tomorrow)
        // $midnight = new DateTime('tomorrow');
        $midnight = (clone $now)->modify('tomorrow midnight');
        // Difference
        $diff = $now->diff($midnight);
        // Get hours left (including minutes converted to fraction if you want)
        $hoursLeft = ceil($diff->h + ($diff->i / 60));
        $times = $hoursLeft;
    }

    if ($signatura_times_id == 'fc91b35a-9ac8-4009-bf43-531764dbeeec') { // 1 MENIT
        // Current time
        $now = new DateTime(date('Y-m-d H:i:s', $now));
        // Midnight (start of tomorrow)
        // $midnight = new DateTime('tomorrow');
        $midnight = (clone $now)->modify('tomorrow midnight');
        // Difference in seconds
        $secondsLeft = $midnight->getTimestamp() - $now->getTimestamp();
        // Convert to minutes (round up to include partial minutes)
        $minutesLeft = ceil($secondsLeft / 60);
        $times = $minutesLeft;
    }

    return $times;
}

function clean_postput($field) {
    if ($field === "") {
        $field = null;
    }
    return $field;
}