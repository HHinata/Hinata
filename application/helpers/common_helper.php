<?php
/**
 * Created by PhpStorm.
 * User: Hinata
 * Date: 2018/4/16
 * Time: 14:43
 */
function create_case_id()
{
    $id = config_item('colony_id').'1'.time();
    return $id;
}
function create_notice_id()
{
    $id = config_item('colony_id').'2'.time();
    return $id;
}
function get_uid($arguments)
{
    return $arguments['uid'];
}
function get_notice_file_name($notice_id)
{
    return 'note_'.$notice_id.'.code';
}
function get_case_file_name($type,$case_id)
{
    if($type == 1) {
        $file_name = 'case_in_' . $case_id . '.txt';
    }else{
        $file_name = 'case_out_' . $case_id . '.txt';
    }
    return $file_name;
}
