<?php

/**
 * Created by PhpStorm.
 * User: Hinata
 * Date: 2018/4/18
 * Time: 20:37
 */
class Problems extends  CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    public function create_problems($params)
    {
        if(!isset($params['time_limit']) || !isset($params['uid']) || !isset($params['type']) || !isset($params['mem_limit'])){
            throw new \Exception($this->config->item('103','errno'),103);
        }
        $pid        = $this->create_problem_id($params['uid']);
        $status     = isset($params['status']) ? $params['status'] : 1;
        $uid        = $params['uid'];
        $title      = isset($params['title']) ? $params['title'] : '';
        $time_limit = $params['time_limit'];
        $mem_limit  = $params['mem_limit'];
        $code       = isset($params['code']) ? $params['code'] : '';
        $type       = $params['type'];
        $condition  = array(
            'pid'         => $pid,
            'status'      => $status,
            'uid'         => $uid,
            'type'        => $type,
            'code'        => $code,
            'time_limit'  => $time_limit,
            'mem_limit'   => $mem_limit,
            'title'       => $title,
            'create_time' => date("Y-m-d H:i:s", time()),
            'update_time' => date("Y-m-d H:i:s", time()),
        );
        if(!$this->db->insert('problems', $condition)){
            $error = $this->db->error();
            throw new \Exception($error['message'],$error['code']);
        }
        return $pid;
    }
    public function update_problem_info($params)
    {
        if(!isset($params['pid']) || !is_numeric($params['pid']) || !isset($params['uid'])){
            throw new \Exception($this->config->item('103','errno'),103);
        }
        $condition = array(
            'update_time' => date("Y-m-d H:i:s", time()),
        );
        isset($params['title']) && $condition['title'] = $params['title'];
        isset($params['code']) && $condition['code'] = $params['code'];
        isset($params['time_limit']) && $condition['time_limit'] = $params['time_limit'];
        isset($params['mem_limit']) && $condition['mem_limit'] = $params['mem_limit'];
        $where = array(
            'uid' => $params['uid'],
            'pid' => $params['pid'],
        );
        if(!$this->db->update('problems',$condition,$where)){
            $error = $this->db->error();
            throw new \Exception($error['message'],$error['code']);
        }
        return $params['pid'];
    }
    public function delete_problem_info($params)
    {
        if(!isset($params['pid']) || !is_numeric($params['pid']) || !isset($params['uid'])){
            throw new \Exception($this->config->item('103','errno'),103);
        }
        $condition = array(
            'status'      => 0,
            'update_time' => date("Y-m-d H:i:s", time()),
        );
        $where = array(
            'uid' => $params['uid'],
            'pid' => $params['pid'],
        );
        if(!$this->db->update('problems',$condition,$where)){
            $error = $this->db->error();
            throw new \Exception($error['message'],$error['code']);
        }
        return true;
    }
    public function mdelete_problem_info($params)
    {
        if(empty($params['pids'])){
            return true;
        }
        if(!isset($params['pids']) || !is_array($params['pids']) || !isset($params['uid'])){
            throw new \Exception($this->config->item('103','errno'),103);
        }
        foreach ($params['pids'] as $key => $pid){
            if(!is_numeric($pid)){
                throw new \Exception($this->config->item('103','errno'),103);
            }
        }
        $condition = array(
            'status'      => 0,
            'update_time' => date("Y-m-d H:i:s", time()),
        );
        $this->db->where_in('pid',$params['pids']);
        $this->db->where('uid',$params['uid']);
        if(!$this->db->update('problems',$condition)){
            $error = $this->db->error();
            throw new \Exception($error['message'],$error['code']);
        }
        return true;
    }
    public function show_problem_info($params)
    {
        if(!isset($params['pid']) || !is_numeric($params['pid']) || !isset($params['uid'])){
            throw new \Exception($this->config->item('103','errno'),103);
        }
        $where = array(
            'uid'    => $params['uid'],
            'pid'    => $params['pid'],
            'status' => 1,
        );
        $info = $this->db->get_where('problems',$where);
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
    public function show_problem_info_by_ids($params)
    {
        if(!isset($params['pids']) || !is_array($params['pids']) || !isset($params['uid'])){
            throw new \Exception($this->config->item('103','errno'),103);
        }
        foreach ($params['pids'] as $key => $pid){
            if(!is_numeric($pid)){
                throw new \Exception($this->config->item('103','errno'),103);
            }
        }
        if(empty($params['pids'])){
            return array();
        }
        $this->db->where_in('pid',$params['pids']);
        $this->db->where('uid',$params['uid']);
        $this->db->where('status',1);
        $infos = $this->db->get('problems');
        if($infos == false){
            $error = $this->db->error();
            throw new \Exception($error['message'],$error['code']);
        }
        $num = $infos->num_rows();
        if($num == 0){
            return array();
        }
        $infos = $infos->result_array();
        return $this->filter_infos($infos);
    }
    public function show_problem_info_by_user($params)
    {
        if(!isset($params['uid'])){
            throw new \Exception($this->config->item('103','errno'),103);
        }
        $where = array(
            'uid'    => $params['uid'],
            'status' => 1,
        );
        $infos = $this->db->get_where('problems',$where);
        if($infos == false){
            $error = $this->db->error();
            throw new \Exception($error['message'],$error['code']);
        }
        $num = $infos->num_rows();
        if($num == 0){
            return false;
        }
        $infos = $infos->result_array();
        return $this->filter_infos($infos);
    }
    public function filter_info($info)
    {
        return $info;
    }
    public function filter_infos($infos)
    {
        return $infos;
    }
    public function create_problem_id($uid)
    {
        list($msec, $sec) = explode(' ', microtime());
        $msectime =  (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        $id = config_item('colony_id').$uid.$msectime.rand(1,1000);
        return $id;
    }

}