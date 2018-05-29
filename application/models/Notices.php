<?php

/**
 * Created by PhpStorm.
 * User: Hinata
 * Date: 2018/4/20
 * Time: 14:38
 */
class Notices extends  CI_Model
{
    static $redis_write_ip = array(
        '10.96.88.20',
    );
    static $redis_read_ip = array(
        '10.96.91.199',
    );
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    public function create_notices($params)
    {
        if(!isset($params['notice_id']) || !is_numeric($params['notice_id']) || !isset($params['pid']) || !isset($params['uid'])){
            throw new \Exception($this->config->item('103','errno'),103);
        }
        $pid        = $params['pid'];
        $status     = isset($params['status']) ? $params['status'] : 1;
        $uid        = $params['uid'];
        $notice_id  = $params['notice_id'];
        $use_time   = '';
        $use_memory = '';
        $message    = '';
        //$code       = isset($params['code']) ? $params['code'] : '';
        $code       = '';
        $notice_status = 1;
        $colony_id = $params['colony_id'];
        $condition = array(
            'pid'           => $pid,
            'status'        => $status,
            'notice_status' => $notice_status,
            'uid'           => $uid,
            'use_time'      => $use_time,
            'notice_id'     => $notice_id,
            'use_memory'    => $use_memory,
            'message'       => $message,
            'code'          => $code,
            'colony_id'     => $colony_id,
            'create_time'   => date("Y-m-d H:i:s", time()),
            'update_time'   => date("Y-m-d H:i:s", time()),
        );
        $db_name = self::getDBname($params['notice_id']);
        if(!$this->db->insert($db_name, $condition)){
            $error = $this->db->error();
            throw new \Exception($error['message'],$error['code']);
        }
        self::set_notice_cache($notice_id,$condition);
        return $notice_id;
    }
    public function delete_notice_info($params)
    {
        if(!isset($params['notice_id']) || !is_numeric($params['notice_id']) || !isset($params['uid'])){
            throw new \Exception($this->config->item('103','errno'),103);
        }
        $condition = array(
            'status'      => 0,
            'update_time' => date("Y-m-d H:i:s", time()),
        );
        $where = array(
            'uid' => $params['uid'],
            'notice_id' => $params['notice_id'],
        );
        $db_name = self::getDBname($params['notice_id']);
        if(!$this->db->update($db_name,$condition,$where)){
            $error = $this->db->error();
            throw new \Exception($error['message'],$error['code']);
        }
        self::delete_notice_cache($params['notice_id']);
        return true;
    }
    public function show_notice_info($params)
    {
        if(!isset($params['notice_id']) || !is_numeric($params['notice_id'])){
            throw new \Exception($this->config->item('103','errno'),103);
        }
        $info = self::get_notice_cache($params['notice_id']);
        if($info !== false){
            return $this->filter_info($info);
        }
        $where = array(
            'notice_id' => $params['notice_id'],
            'status' => 1,
        );
        $db_name = self::getDBname($params['notice_id']);
        $info = $this->db->get_where($db_name,$where);
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
    public function show_notice_info_by_ids($params)
    {
        if(!isset($params['notice_ids']) || !is_array($params['notice_ids']) || !isset($params['uid'])){
            throw new \Exception($this->config->item('103','errno'),103);
        }
        $this->db->where_in('notice_id',$params['notice_ids']);
        $this->db->where('status',1);
        $this->db->where('uid',$params['uid']);
        $db_name = self::getDBname($params['notice_id']);
        $infos = $this->db->get($db_name);
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
    public function update_notice_info($params)
    {
        if(!isset($params['notice_id']) || !is_numeric($params['notice_id']) || !isset($params['uid'])){
            throw new \Exception($this->config->item('103','errno'),103);
        }
        $condition = array(
            'update_time' => date("Y-m-d H:i:s", time()),
        );
        isset($params['pid']) && $condition['pid'] = $params['pid'];
        isset($params['notice_status']) && $condition['notice_status'] = $params['notice_status'];
        $where = array(
            'uid' => $params['uid'],
            'notice_id' => $params['notice_id'],
        );
        $db_name = self::getDBname($params['notice_id']);
        if(!$this->db->update($db_name,$condition,$where)){
            $error = $this->db->error();
            throw new \Exception($error['message'],$error['code']);
        }
        $info = self::get_notice_cache($params['notice_id']);
        if($info !== false){
            $info = array_merge($info,$condition);
            self::set_notice_cache($params['notice_id'],$info);
        }
        return $params['notice_id'];
    }
    public function notice_back($params)
    {
        if(!isset($params['notice_id']) || !is_numeric($params['notice_id'])){
            throw new \Exception($this->config->item('103','errno'),103);
        }
        $condition = array(
            'update_time'   => date("Y-m-d H:i:s", time()),
            'use_time'      => $params['use_time'],
            'use_memory'    => $params['use_memory'],
            'message'       => $params['message'],
            'notice_status' => $params['notice_status'],
        );
        $where = array(
            'notice_id' => $params['notice_id'],
        );
        $db_name = self::getDBname($params['notice_id']);
        if(!$this->db->update($db_name,$condition,$where)){
            $error = $this->db->error();
            throw new \Exception($error['message'],$error['code']);
        }
        return $params['notice_id'];
    }
    public function pop_notice()
    {
        $id = self::pop_notice_cache();
        if($id != false){
            $params['notice_id'] = $id;
            return self::show_notice_info($params);
        }else{
            self::ergodic_notice();
            return [];
        }
    }
    public function set_judgeing($id)
    {
        $redis = new Redis();
        $redis->connect(self::$redis_write_ip[0], 6379);
        return $redis->setnx($id,1);
    }
    public function notice_pop($notice_id = '') //todo
    {
        $where = array(
            'notice_status' => 1,
            'status' => 1,
        );
        for ($i = 0;$i < 107 ;$i++){
            $db_name = self::getDBname($i);
            $this->db->limit(1);
            $info = $this->db->get_where($db_name,$where);
            if($info == false){
                $error = $this->db->error();
                throw new \Exception($error['message'],$error['code']);
            }
            $num = $info->num_rows();
            if($num == 0){
                continue;
            }
            $info = $info->row_array();
            return $this->filter_info($info);
        }
        return [];
    }
    public function filter_info($info)
    {
        return $info;
    }
    public function filter_infos($infos)
    {
        return $infos;
    }
    public function getDBname($notice_id){
        return 'notices_'.((substr($notice_id,-4)) % 107);
    }

    public function get_notice_cache($notice_id)
    {
        //return false;
        $redis = new Redis();
        $redis->connect(self::$redis_read_ip[0], 6379);
        $notice_info = $redis->get($notice_id);
        if($notice_info === false){
            return false;
        }
        $notice_info = json_decode($notice_info,true);
        return $notice_info;
    }
    public function set_notice_cache($notice_id,$notice_info)
    {
        $notice_info = json_encode($notice_info);
        $redis = new Redis();
        $redis->connect(self::$redis_write_ip[0], 6379);
        return $redis->set($notice_id,$notice_info,60*60*24);
    }
    public function delete_notice_cache($notice_id)
    {
        $redis = new Redis();
        $redis->connect(self::$redis_write_ip[0], 6379);
        $redis->delete($notice_id);
    }
    public function get_ergodic_flag()
    {
        $redis = new Redis();
        $redis->connect(self::$redis_read_ip[0], 6379);
        $flag = $redis->get('ergodic_flag');
        if($flag == 1){
            return true;
        }
        return false;
    }
    public function set_ergodic_flag()
    {
        $redis = new Redis();
        $redis->connect(self::$redis_write_ip[0], 6379);
        return $redis->setnx('ergodic_flag',1);
    }
    public function delete_ergodic_flag()
    {
        $redis = new Redis();
        $redis->connect(self::$redis_write_ip[0], 6379);
        $redis->delete('ergodic_flag');
    }
    public function push_notice_cache($notice_id)
    {
        $redis = new Redis();
        $redis->connect(self::$redis_write_ip[0], 6379);
        $redis->lpush('notice_id',$notice_id);
    }
    public function pop_notice_cache()
    {
        $redis = new Redis();
        $redis->connect(self::$redis_write_ip[0], 6379);
        $id = false;
        while(true){
            $id = $redis->rPop('notice_id');
            $judeging = self::set_judgeing($id);
            if($judeging == true){
                return $id;
            }
        }
        return false;
    }
    public function ergodic_notice()
    {
        if(self::get_ergodic_flag() == true){
            return;
        }
        if(self::set_ergodic_flag() != true){
            return;
        }
        $where = array(
            'notice_status' => 1,
            'status' => 1,
        );
        for ($i = 0;$i < 107 ;$i++){
            $db_name = self::getDBname($i);
            $info = $this->db->get_where($db_name,$where);
            if($info == false){
                $error = $this->db->error();
                throw new \Exception($error['message'],$error['code']);
            }
            $num = $info->num_rows();
            if($num == 0){
                continue;
            }
            $infos = $info->result_array();
            foreach ($infos as $key => $value){
                if(isset($value['notice_id'])) {
                    self::push_notice_cache($value['notice_id']);
                }
            }
        }
        self::delete_ergodic_flag();
        return;
    }

}