<?php
/**
 * Created by PhpStorm.
 * User: Hinata
 * Date: 2018/4/16
 * Time: 14:43
 */
function create_user_id()
{
    $id = config_item('colony_id').'2'.time();
    return $id;
}
function create_token($uid)
{
    $token = base64_encode($uid.config_item('colony_id').time());
    $redis = new Redis();
    $redis->connect('10.96.88.20', 6379);
    $redis->set($token,$uid);
    return $token;
}
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
    $token = $arguments['token'];
    $redis = new Redis();
    $redis->connect('10.96.88.20', 6379);
    $uid = $redis->get($token);
    return $uid;
}
function get_notice_file_name($notice_id)
{
    return 'note_'.$notice_id.'.txt';
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
}


function get_user_info($uid)
{
    $CI=&get_instance();
    $CI->load->model('Users');
    $info = $CI->Users->show_user_info($uid);
    return $info;
}

function check_power_case_read($uid)
{
    $user_info = get_user_info($uid);
    if(!isset($user_info['power'])){
        return false;
    }
    $power = $user_info['power'];
    $flag = substr($power,0,1);
    if($flag){
        return true;
    }
    return false;
}

function check_power_case_write($uid)
{
    $user_info = get_user_info($uid);
    if(!isset($user_info['power'])){
        return false;
    }
    $power = $user_info['power'];
    $flag = substr($power,1,1);
    if($flag){
        return true;
    }
    return false;
}

function check_power_problem_read($uid)
{
    $user_info = get_user_info($uid);
    if(!isset($user_info['power'])){
        return false;
    }
    $power = $user_info['power'];
    $flag = substr($power,2,1);
    if($flag){
        return true;
    }
    return false;
}

function check_power_problem_write($uid)
{
    $user_info = get_user_info($uid);
    if(!isset($user_info['power'])){
        return false;
    }
    $power = $user_info['power'];
    $flag = substr($power,3,1);
    if($flag){
        return true;
    }
    return false;
}

function check_power_notice_read($uid)
{
    $user_info = get_user_info($uid);
    if(!isset($user_info['power'])){
        return false;
    }
    $power = $user_info['power'];
    $flag = substr($power,4,1);
    if($flag){
        return true;
    }
    return false;
}

function check_power_notice_write($uid)
{
    $user_info = get_user_info($uid);
    if(!isset($user_info['power'])){
        return false;
    }
    $power = $user_info['power'];
    $flag = substr($power,5,1);
    if($flag){
        return true;
    }
    return false;
}

function check_power_inner($uid)
{
    $user_info = get_user_info($uid);
    if(!isset($user_info['power'])){
        return false;
    }
    $power = $user_info['power'];
    $flag = substr($power,6,1);
    if($flag){
        return true;
    }
    return false;
}

function check_power_user_manage($uid)
{
    $user_info = get_user_info($uid);
    if(!isset($user_info['power'])){
        return false;
    }
    $power = $user_info['power'];
    $flag = substr($power,7,1);
    if($flag){
        return true;
    }
    return false;
}