<?php

/**
 * Created by PhpStorm.
 * User: Hinata
 * Date: 2018/4/16
 * Time: 15:11
 */
class Cases extends  CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    public function create_cases($params)
    {
        if(!isset($params['case_id']) || !is_numeric($params['case_id']) || !isset($params['pid']) || !isset($params['uid']) || !isset($params['type'])){
            throw new \Exception($this->config->item('103','errno'),103);
        }
        $pid       = $params['pid'];
        $status    = isset($params['status']) ? $params['status'] : 1;
        $uid       = $params['uid'];
        $case_id   = $params['case_id'];
        $type      = $params['type'];
        $condition = array(
            'pid'         => $pid,
            'status'      => $status,
            'uid'         => $uid,
            'type'        => $type,
            'case_id'     => $case_id,
            'colony_id'   => $this->config->item('colony_id'),
            'create_time' => date("Y-m-d H:i:s", time()),
            'update_time' => date("Y-m-d H:i:s", time()),
        );
        if(!$this->db->insert('cases', $condition)){
            $error = $this->db->error();
            throw new \Exception($error['message'],$error['code']);
        }
        return $case_id;
    }
    public function delete_case_info($params)
    {
        if(!isset($params['case_id']) || !is_numeric($params['case_id']) || !isset($params['uid']) || !isset($params['type'])){
            throw new \Exception($this->config->item('103','errno'),103);
        }
        $condition = array(
            'status'      => 0,
            'update_time' => date("Y-m-d H:i:s", time()),
        );
        $where = array(
            'uid' => $params['uid'],
            'case_id' => $params['case_id'],
            'type'  => $params['type'],
        );
        if(!$this->db->update('cases',$condition,$where)){
            $error = $this->db->error();
            throw new \Exception($error['message'],$error['code']);
        }
        return true;
    }
    public function show_case_info($params)
    {
        if(!isset($params['case_id']) || !is_numeric($params['case_id']) || !isset($params['uid']) || !isset($params['type'])){
            throw new \Exception($this->config->item('103','errno'),103);
        }
        $where = array(
            'uid' => $params['uid'],
            'case_id' => $params['case_id'],
            'type'  => $params['type'],
            'status' => 1,
        );
        $info = $this->db->get_where('cases',$where);
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
    public function show_case_id_by_pid($params)
    {
        if(!isset($params['pid'])){
            throw new \Exception($this->config->item('103','errno'),103);
        }
        $where = array(
            'pid' => $params['pid'],
            'status' => 1,
        );
        $infos = $this->db->get_where('cases',$where);
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
    public function filter_info($info)
    {
        return $info;
    }
    public function filter_infos($infos)
    {
        return $infos;
    }

}