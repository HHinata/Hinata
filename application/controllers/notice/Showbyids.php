<?php

/**
 * Created by PhpStorm.
 * User: Hinata
 * Date: 2018/4/28
 * Time: 17:38
 */
class Showbyids extends CI_Controller
{
    private $response;
    private $arguments;
    private $field = array(
        'code',
        'notice_status',
        'use_memory',
        'use_time',
        'message',
        'pid',
        'uid',
        'notice_id'
    );
    public function __construct()
    {
        parent::__construct();
        $this->arguments = array_merge($_POST,$_GET);
        $this->response = array(
            'errno'  => 0,
            'errmsg' => '',
            'data'   => [],
        );
    }
    public function index()
    {
        try{
            if(!$this->load->helper(array('common')) || !$this->load->model('Notices') || !$this->config->load('errno',true)){
                throw new \Exception($this->config->item('102','errno'), 102);
            }
            $this->check_arguments();
            $this->arguments['uid'] = get_uid($this->arguments);
            $power = check_power_notice_read($this->arguments['uid']);
            if($power == false){
                throw new \Exception($this->config->item('104','errno'), 104);
            }
            $params = array(
                'uid'  => $this->arguments['uid'],
                'notice_ids' => $this->arguments['notice_ids'],
            );
            $notice_infos = $this->Notices->show_notice_info_by_ids($params);
            if($notice_infos == false){
                throw new \Exception($this->config->item('100005','errno'),100005);
            }
            foreach ($notice_infos as $key => $value){
                $notice_infos[$key]['code'] = json_decode($notice_infos[$key]['code'],true);
            }
            $this->response['data']['notice_infos'] = $notice_infos;
        }catch (\Exception $e){
            $this->response['errno'] = $e->getCode();
            $this->response['errmsg'] = $e->getMessage();
            log_message('error',$this->response['errmsg']);
        }
        echo json_encode($this->response);
    }
    public function check_arguments()
    {
        if(!isset($this->arguments['token']) || !isset($this->arguments['notice_ids'])){
            throw new \Exception($this->config->item('103','errno'),103);
        }
        if(!isset($this->arguments['aFields'])){
            return;
        }
        if(!is_array($this->arguments['aFields'])){
            throw new \Exception($this->config->item('103','errno'),103);
        }
        foreach ($this->arguments['aFields'] as $value){
            if(!in_array($value,$this->field)){
                throw new \Exception($this->config->item('103','errno'),103);
            }
        }
    }
}