<?php
function upload_file($file, $filename, $upload_path = './assets', $allowed_types = 'jpg|jpeg|png|img|pdf', $overwrite = true) {
    $ci=& get_instance();

    $type = explode('/', $file['type'])[1];
    $tmp_name = $file['tmp_name'];
    $error = $file['error'];
    $size = $file['size'];

    if ($type == 'jpg' || $type == 'jpeg') {
        $allowed_types = '*';
    }
    
    $config['upload_path'] = $upload_path;
    $config['allowed_types'] = $allowed_types;
    $config['max_size']      = 4120; // 50 MB dalam KB
    $config['overwrite'] = $overwrite;	
    $config['file_name'] = $filename . '.' . $type;

    $ci->load->library('upload', $config, 'file_upload');
    $ci->file_upload->initialize($config);
    if ($ci->file_upload->do_upload('file') ) {
        $fileData = $ci->file_upload->data();
    } else {    
        $res = array(
            'status' => 'failed',
            'message' => $ci->file_upload->display_errors()
        );

        return $res;
    }

    $res = array(
        'status' => 'ok',
        'message' => '',
        'type' => $type
    );

    return $res;
}