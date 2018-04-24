<?php

/**
 * Created by PhpStorm.
 * User: Hinata
 * Date: 2018/4/13
 * Time: 19:53
 */
class Show extends CI_Controller
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
            $params = array(
                'uid'  => $this->arguments['uid'],
                'notice_id' => $this->arguments['notice_id'],
            );
            $notice_info = $this->Notices->show_notice_info($params);
            if($notice_info == false){
                throw new \Exception($this->config->item('100005','errno'),100005);
            }
            $notice_info['code'] = json_decode($notice_info['code'],true);
            $this->response['data']['notice_info'] = $notice_info;
        }catch (\Exception $e){
            $this->response['errno'] = $e->getCode();
            $this->response['errmsg'] = $e->getMessage();
            log_message('error',$this->response['errmsg']);
        }
        echo json_encode($this->response);
    }
    public function check_arguments()
    {
        if(!isset($this->arguments['uid']) || !isset($this->arguments['notice_id'])){
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