<?php

/**
 * Created by PhpStorm.
 * User: Hinata
 * Date: 2018/4/23
 * Time: 23:13
 */
class Pop extends CI_Controller
{
    private $response;
    private $arguments;
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
            $uid = '121527580551';
            $power = check_power_inner($uid);
            if($power == false){
                throw new \Exception($this->config->item('104','errno'), 104);
            }
            $notice_info = $this->Notices->pop_notice();
            if($notice_info === false){
                throw new \Exception($this->config->item('100006','errno'),100006);
            }
            if(!empty($notice_info)){
                $notice_info['code'] = json_decode($notice_info['code'],true);
            }
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
    }
}