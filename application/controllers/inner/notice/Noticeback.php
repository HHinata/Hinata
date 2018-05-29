<?php

/**
 * Created by PhpStorm.
 * User: Hinata
 * Date: 2018/4/23
 * Time: 23:30
 */
class  Noticeback extends CI_Controller
{
    private $response;
    private $arguments;

    public function __construct()
    {
        parent::__construct();
        $this->arguments = array_merge($_POST, $_GET);
        $this->response = array(
            'errno' => 0,
            'errmsg' => '',
            'data' => [],
        );
    }
    public function index()
    {
        try {
            if (!$this->load->helper(array('common')) || !$this->load->model('Notices') || !$this->config->load('errno', true)) {
                throw new \Exception($this->config->item('102', 'errno'), 102);
            }
            $this->check_arguments();
            $uid = '121527580551';
            $power = check_power_inner($uid);
            if($power == false){
                throw new \Exception($this->config->item('104','errno'), 104);
            }
            $params = array(
                'notice_id' => $this->arguments['notice_id'],
                'use_time'  => $this->arguments['use_time'],
                'use_memory'=> $this->arguments['use_memory'],
                'message'   => $this->arguments['message'],
                'notice_status' => $this->arguments['notice_status'],
            );
            $notice_id = $this->Notices->notice_back($params);
            $this->response['data']['notice_id'] = $notice_id;
        } catch (Exception $e) {
            $this->response['errno'] = $e->getCode();
            $this->response['errmsg'] = $e->getMessage();
            log_message('error', $this->response['errmsg']);
        }
        echo json_encode($this->response);
    }

    public function check_arguments()
    {
        if (!isset($this->arguments['notice_id']) || !isset($this->arguments['use_time'])
        || !isset($this->arguments['use_memory']) || !isset($this->arguments['message'])
        || !isset($this->arguments['notice_status'])) {
            throw new \Exception($this->config->item('103', 'errno'), 103);
        }
    }
}