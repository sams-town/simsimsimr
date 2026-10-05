<?php
function get_variable($variable_name) {
    $ci=& get_instance();
    
    $result = [];

    $type_register = $ci->db->query("
        SELECT name, code
        FROM helper
        WHERE variable = '$variable_name'
        AND is_delete = 0
    ")->result();


    for ($i=0; $i < count($type_register); $i++) { 
        $name = $type_register[$i]->name;
        $code = $type_register[$i]->code;

        $result[$code] = $name;
    }

    return $result;
}
function get_variable_assesment($variable_name) {
    $ci=& get_instance();
    
    $result = [];

    $type = array(
        array('variable' => 'yesno', 'code' => '0', 'name' => 'Tidak'),
        array('variable' => 'yesno', 'code' => '1', 'name' => 'Ya'),
        array('variable' => 'immunization_status_type', 'code' => '0', 'name' => 'Tidak Lengkap'),
        array('variable' => 'immunization_status_type', 'code' => '1', 'name' => 'Lengkap'),
        array('variable' => 'drug_allergy_type', 'code' => '0', 'name' => 'Tidak'),
        array('variable' => 'drug_allergy_type', 'code' => '1', 'name' => 'Ya'),
        array('variable' => 'type_of_cases_type', 'code' => '1', 'name' => 'Visum'),
        array('variable' => 'type_of_cases_type', 'code' => '2', 'name' => 'DOA'),
        array('variable' => 'type_of_cases_type', 'code' => '3', 'name' => 'Non Trauma'),
        array('variable' => 'type_of_cases_type', 'code' => '4', 'name' => 'Trauma'),
        array('variable' => 'level_of_consciousness_type', 'code' => '1', 'name' => 'Alert'),
        array('variable' => 'level_of_consciousness_type', 'code' => '2', 'name' => 'Verbal'),
        array('variable' => 'level_of_consciousness_type', 'code' => '3', 'name' => 'Painful'),
        array('variable' => 'level_of_consciousness_type', 'code' => '4', 'name' => 'Unresponsive'),
        array('variable' => 'breathing_path_arry', 'code' => 'A1', 'name' => 'Ada Sumbatan Jalan Nafas'),
        array('variable' => 'breathing_path_arry', 'code' => 'B1', 'name' => 'Bebas Atau Ada Ancaman Sumbatan'),
        array('variable' => 'breathing_path_arry', 'code' => 'C1', 'name' => 'Bebas'),
        array('variable' => 'breathing_path_arry', 'code' => 'D1', 'name' => 'Bebas'),
        array('variable' => 'breathing_path_arry', 'code' => 'E1', 'name' => 'Bebas'),
        array('variable' => 'breathing_arry', 'code' => 'A1', 'name' => 'Henti Nafas/Apnoe'),
        array('variable' => 'breathing_arry', 'code' => 'A2', 'name' => 'RR &lt; 10x/menit'),
        array('variable' => 'breathing_arry', 'code' => 'A3', 'name' => 'Sianosis'),
        array('variable' => 'breathing_arry', 'code' => 'A4', 'name' => 'SPO2 &lt; 90%'),
        array('variable' => 'breathing_arry', 'code' => 'B1', 'name' => 'RR &ge; 32x/menit'),
        array('variable' => 'breathing_arry', 'code' => 'B2', 'name' => 'SPO2 &lt; 92%'),
        array('variable' => 'breathing_arry', 'code' => 'C1', 'name' => 'RR 25-32x/menit'),
        array('variable' => 'breathing_arry', 'code' => 'C2', 'name' => 'SPO2 92-95%'),
        array('variable' => 'breathing_arry', 'code' => 'D1', 'name' => 'RR 20-24x/menit'),
        array('variable' => 'breathing_arry', 'code' => 'D2', 'name' => 'SPO2 96-100%'),
        array('variable' => 'breathing_arry', 'code' => 'E1', 'name' => 'RR 16-20 x/mnt'),
        array('variable' => 'breathing_arry', 'code' => 'E2', 'name' => 'SPO2 96-100%'),
    );
    for ($i=0; $i < count($type); $i++) { 
        if ($variable_name == $type[$i]['variable']) {
            $name = $type[$i]['name'];
            $code = $type[$i]['code'];
    
            $result[$code] = $name;
        }
    }

    return $result;
}
function get_variable_assesment_arry($variable_name,$input_codes) {
    $ci=& get_instance();
    
    $result = [];

    $type = array(
        array('variable' => 'breathing_path_arry', 'code' => 'A1', 'name' => 'Ada Sumbatan Jalan Nafas'),
        array('variable' => 'breathing_path_arry', 'code' => 'B1', 'name' => 'Bebas Atau Ada Ancaman Sumbatan'),
        array('variable' => 'breathing_path_arry', 'code' => 'C1', 'name' => 'Bebas'),
        array('variable' => 'breathing_path_arry', 'code' => 'D1', 'name' => 'Bebas'),
        array('variable' => 'breathing_path_arry', 'code' => 'E1', 'name' => 'Bebas'),
        array('variable' => 'breathing_arry', 'code' => 'A1', 'name' => 'Henti Nafas/Apnoe'),
        array('variable' => 'breathing_arry', 'code' => 'A2', 'name' => 'RR &lt; 10x/menit'),
        array('variable' => 'breathing_arry', 'code' => 'A3', 'name' => 'Sianosis'),
        array('variable' => 'breathing_arry', 'code' => 'A4', 'name' => 'SPO2 &lt; 90%'),
        array('variable' => 'breathing_arry', 'code' => 'B1', 'name' => 'RR &ge; 32x/menit'),
        array('variable' => 'breathing_arry', 'code' => 'B2', 'name' => 'SPO2 &lt; 92%'),
        array('variable' => 'breathing_arry', 'code' => 'C1', 'name' => 'RR 25-32x/menit'),
        array('variable' => 'breathing_arry', 'code' => 'C2', 'name' => 'SPO2 92-95%'),
        array('variable' => 'breathing_arry', 'code' => 'D1', 'name' => 'RR 20-24x/menit'),
        array('variable' => 'breathing_arry', 'code' => 'D2', 'name' => 'SPO2 96-100%'),
        array('variable' => 'breathing_arry', 'code' => 'E1', 'name' => 'RR 16-20 x/mnt'),
        array('variable' => 'breathing_arry', 'code' => 'E2', 'name' => 'SPO2 96-100%'),
        array('variable' => 'circulation_arry', 'code' => 'B1', 'name' => 'Henti Jantung / arrest'),
        array('variable' => 'circulation_arry', 'code' => 'B2', 'name' => 'Nadi Tidak Teraba'),
        array('variable' => 'circulation_arry', 'code' => 'B3', 'name' => 'Akral Dingin'),
        array('variable' => 'circulation_arry', 'code' => 'B4', 'name' => 'Nadi Lemah'),
        array('variable' => 'circulation_arry', 'code' => 'B5', 'name' => 'HR &lt; 50 x/mnt atau &gt; 150 x/mnt'),
        array('variable' => 'circulation_arry', 'code' => 'C1', 'name' => 'TD &lt; 100/&lt; 60 mmHg'),
        array('variable' => 'circulation_arry', 'code' => 'C2', 'name' => 'HR 121-150x/menit'),
        array('variable' => 'circulation_arry', 'code' => 'C3', 'name' => 'TD &gt; 160/ &gt; 100 mmHg'),
        array('variable' => 'circulation_arry', 'code' => 'C4', 'name' => 'CRT &lt; 2 Detik'),
        array('variable' => 'circulation_arry', 'code' => 'D1', 'name' => 'HR 101-120x/menit'),
        array('variable' => 'circulation_arry', 'code' => 'D2', 'name' => 'TD 121 - 140/80-100 mmHg'),
        array('variable' => 'circulation_arry', 'code' => 'D3', 'name' => 'CRT &lt; 2 Detik'),
        array('variable' => 'circulation_arry', 'code' => 'E1', 'name' => 'HR 80-100x/menit'),
        array('variable' => 'circulation_arry', 'code' => 'E2', 'name' => 'TD 120/80 mmHg'),
        array('variable' => 'circulation_arry', 'code' => 'E3', 'name' => 'CRT &lt; 2 Detik'),
        array('variable' => 'consciousness_arry', 'code' => 'A1', 'name' => 'GCS &lt; 9'),
        array('variable' => 'consciousness_arry', 'code' => 'A2', 'name' => 'Kejang'),
        array('variable' => 'consciousness_arry', 'code' => 'B1', 'name' => 'GCS 9-12'),
        array('variable' => 'consciousness_arry', 'code' => 'B2', 'name' => 'Gelisah'),
        array('variable' => 'consciousness_arry', 'code' => 'B3', 'name' => 'Nyeri Dada'),
        array('variable' => 'consciousness_arry', 'code' => 'C1', 'name' => 'GCS 13-14'),
        array('variable' => 'consciousness_arry', 'code' => 'C2', 'name' => 'Apatis / Somnolen'),
        array('variable' => 'consciousness_arry', 'code' => 'D1', 'name' => 'GCS 15'),
        array('variable' => 'consciousness_arry', 'code' => 'E1', 'name' => 'GCS 15'),
        array('variable' => 'resource_arry', 'code' => 'C1', 'name' => 'Menggunakan &ge; 2 sumber daya'),
        // array('variable' => 'resource_arry', 'code' => 'C1', 'name' => 'Menggunakan &gt; 2 sumber daya'),
        array('variable' => 'resource_arry', 'code' => 'D1', 'name' => 'Menggunakan 1 sumber daya'),
        array('variable' => 'resource_arry', 'code' => 'E1', 'name' => 'Tidak menggunakan sumber daya'),
    );
    $codes = explode(',', $input_codes);
    $result = [];

    foreach ($type as $item) {
        if ($item['variable'] == $variable_name && in_array($item['code'], $codes)) {
            $result[] = html_entity_decode($item['name']); // decode &lt; &ge; dsb
        }
    }

    $output = implode(', ', $result);
    return $output;
}