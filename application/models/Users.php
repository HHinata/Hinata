<?php

/**
 * Created by PhpStorm.
 * User: Hinata
 * Date: 2018/5/29
 * Time: 15:43
 */
class Users extends  CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    public function create_users($params)
    {
        if(!isset($params['uid']) || !is_numeric($params['uid']) || !isset($params['password']) || !isset($params['uname'])){
            throw new \Exception($this->config->item('103','errno'),103);
        }
        $status    = isset($params['status']) ? $params['status'] : 1;
        $uid       = $params['uid'];
        $uname     = $params['uname'];
        $password  = $params['password'];
        $power     = '0000000000000000001';
        $condition = array(
            'status'      => $status,
            'uid'         => $uid,
            'uname'       => $uname,
            'password'    => $password,
            'power'       => $power,
            'create_time' => date("Y-m-d H:i:s", time()),
            'update_time' => date("Y-m-d H:i:s", time()),
        );
        if(!$this->db->insert('users', $condition)){
            $error = $this->db->error();
            throw new \Exception($error['message'],$error['code']);
        }
        return $condition;
    }
    public function delete_user_info($params)
    {
        if(!isset($params['uid']) || !is_numeric($params['uid'])){
            throw new \Exception($this->config->item('103','errno'),103);
        }
        $condition = array(
            'status'      => 0,
            'update_time' => date("Y-m-d H:i:s", time()),
        );
        $where = array(
            'uid' => $params['uid'],
        );
        if(!$this->db->update('users',$condition,$where)){
            $error = $this->db->error();
            throw new \Exception($error['message'],$error['code']);
        }
        return true;
    }
    public function update_user_info($params)
    {
        if(!isset($params['uid']) || !is_numeric($params['uid'])){
            throw new \Exception($this->config->item('103','errno'),103);
        }
        $condition = array(
            'update_time' => date("Y-m-d H:i:s", time()),
        );
        isset($params['password']) && $condition['password'] = $params['password'];
        isset($params['power']) && $condition['power'] = $params['power'];
        $where = array(
            'uid' => $params['uid'],
        );
        if(!$this->db->update('users',$condition,$where)){
            $error = $this->db->error();
            throw new \Exception($error['message'],$error['code']);
        }
        return $params['uid'];
    }
    public function show_user_info($params)
    {
        if(!isset($params['uid']) || !is_numeric($params['uid'])){
            throw new \Exception($this->config->item('103','errno'),103);
        }
        $where = array(
            'uid'    => $params['uid'],
            'status' => 1,
        );
        $info = $this->db->get_where('users',$where);
        if($info == false){
            $error = $this->db->error();
            throw new \Exception($error['message'],$error['code']);
        }
        $num = $info->num_rows();
        if($num == 0){
            return false;
        }
        $info = $info->row_array();
        return $this->filter_info($info);
    }
    public function login($params)
    {
        if(!isset($params['uname']) || !is_numeric($params['password'])){
            throw new \Exception($this->config->item('103','errno'),103);
        }
        $where = array(
            'uname'    => $params['uname'],
            'password' => $params['password'],
            'status'   => 1,
        );
        $info = $this->db->get_where('users',$where);
        if($info == false){
            $error = $this->db->error();
            throw new \Exception($error['message'],$error['code']);
        }
        $num = $info->num_rows();
        if($num == 0){
            return false;
        }
        $info = $info->row_array();
        return $this->filter_info($info);
    }
    public function filter_info($info)
    {
        return $info;
    }
    public function filter_infos($infos)
    {
        return $infos;
    }

}