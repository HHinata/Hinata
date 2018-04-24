<?php

/**
 * Created by PhpStorm.
 * User: Hinata
 * Date: 2018/4/20
 * Time: 14:38
 */
class Notices extends  CI_Model
{
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
        $code       = isset($params['code']) ? $params['code'] : '';
        $notice_status = 1;
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
            'create_time'   => date("Y-m-d H:i:s", time()),
            'update_time'   => date("Y-m-d H:i:s", time()),
        );
        if(!$this->db->insert('notices', $condition)){
            $error = $this->db->error();
            throw new \Exception($error['message'],$error['code']);
        }
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
        if(!$this->db->update('notices',$condition,$where)){
            $error = $this->db->error();
            throw new \Exception($error['message'],$error['code']);
        }
        return true;
    }
    public function show_notice_info($params)
    {
        if(!isset($params['notice_id']) || !is_numeric($params['notice_id']) || !isset($params['uid'])){
            throw new \Exception($this->config->item('103','errno'),103);
        }
        $where = array(
            'uid' => $params['uid'],
            'notice_id' => $params['notice_id'],
            'status' => 1,
        );
        $info = $this->db->get_where('notices',$where);
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
        if(!$this->db->update('notices',$condition,$where)){
            $error = $this->db->error();
            throw new \Exception($error['message'],$error['code']);
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
        if(!$this->db->update('notices',$condition,$where)){
            $error = $this->db->error();
            throw new \Exception($error['message'],$error['code']);
        }
        return $params['notice_id'];
    }
    public function notice_pop()
    {
        $where = array(
            'notice_status' => 1,
            'status' => 1,
        );
        $info = $this->db->get_where('notices',$where);
        if($info == false){
            $error = $this->db->error();
            throw new \Exception($error['message'],$error['code']);
        }
        $num = $info->num_rows();
        if($num == 0){
            return [];
        }
        $info = $info->row_array();
        return $this->filter_info($info);
    }
    public function filter_info($info)
    {
        return $info;
    }

}