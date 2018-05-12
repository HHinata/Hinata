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
function create_notice_id($uid)
{
    list($msec, $sec) = explode(' ', microtime());
    $msectime =  (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
    $id = config_item('colony_id').$msectime.rand(1,100).rand(1,99).$uid;
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
function get_notice_file_path($notice_id)
{
    $path = './uploads/';

    $flag = (substr(substr($notice_id,-15),0,6)) % 1000000;
    $path1 = $flag % 200;
    $path2 = ($flag / 1000) % 200;
    $path .= 'notices_'.$path1.'/'.$path2.'/';
    return $path;
    if($type == 1) {
        $path .= 'in_'.$path1.'/'.$path2.'/';
    }else{
        $path .= 'out_'.$path1.'/'.$path2.'/';
    }

    return $path;
}