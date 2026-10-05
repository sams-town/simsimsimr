<?php

function get_request($url, $params = []) {
    if (count($params) > 0) {
        $url = $url . '?' . http_build_query($params);
    }
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        show_error("Curl GET error: " . curl_error($ch), 500);
    }

    curl_close($ch);

    $decoded = json_decode($response, true);  // true = associative array

    if (json_last_error() !== JSON_ERROR_NONE) {
        show_error("Invalid JSON response", 500);
    }

    return $decoded;
}

function post_request($url, $payload, $header = null) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        show_error('Curl POST errora: ' . $error_msg);
    }

    curl_close($ch);

    $decoded = json_decode($response, true);  // true = associative array

    if (json_last_error() !== JSON_ERROR_NONE) {
        show_error("Invalid JSON response", 500);
    }

    return $decoded;
}

function put_request() {
    $url = $url . '/123'; // Assume 123 is the resource ID

    $data = [
        'email' => 'newemail@example.com'
    ];

    $payload = json_encode($data);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($payload)
    ]);

    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        show_error("Curl PUT error: " . curl_error($ch), 500);
    }

    curl_close($ch);
    echo "PUT Response ($httpcode):<br><pre>$response</pre>";
}

function make_curl_file() {
    $ci=& get_instance();

    // Load CI's file upload library
    $config['upload_path'] = 'assets/temp';
    $config['allowed_types'] = '*'; // Limit as needed
    $config['max_size'] = 4120; // 50 MB in KB
    $config['encrypt_name'] = TRUE;
    // $config['overwrite'] = $overwrite;	
    // $config['file_name'] = $filename . '.' . $type;

    $ci->load->library('upload', $config);

    if (!$ci->upload->do_upload('file')) {
        show_error($ci->upload->display_errors());
    }

    $fileData = $ci->upload->data();
    $filePath = 'assets/temp/' . $fileData['file_name'];

    // Prepare curl for multipart POST
    $curlFile = new CURLFile($filePath, $fileData['file_type'], $fileData['file_name']);

    return $curlFile;
}

function upload_file() {
    

    $postFields = ['file' => $curlFile];

    $ch = curl_init('http://pythonapi:5000/api/upload');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        show_error("Curl error: " . curl_error($ch));
    }

    curl_close($ch);
    unlink($filePath); // Optional: delete uploaded file from PHP server

    echo "<h3>Response ($httpcode):</h3><pre>$response</pre>";
}