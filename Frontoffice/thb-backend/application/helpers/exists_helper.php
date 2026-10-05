<?php
function is_exist($model,$id) {
    $ci=& get_instance();
    $ci->load->model($model);
    $result = array(); 

    $select_before = $ci->$model->selectFields;
    $where_before = $ci->$model->whereFields;
    $limit_before = $ci->$model->limit;

    $ci->$model->selectFields = "a.id";
    $ci->$model->whereFields['a.id'] = $id;
    $ci->$model->limit = 1;
    $result = $ci->$model->read(0,false);

    $ci->$model->selectFields = $select_before;
    $ci->$model->whereFields = $where_before;
    $ci->$model->limit = $limit_before;
    return $result;
}