<?php

/**
 * Created by PhpStorm.
 * User: Hinata
 * Date: 2018/4/13
 * Time: 19:38
 */
class Modify extends CI_Controller
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
            if(!$this->load->helper(array('common')) || !$this->load->model('Problems') || !$this->config->load('errno',true)){
                throw new \Exception($this->config->item('102','errno'), 102);
            }
            $this->check_arguments();
            $this->arguments['uid'] = get_uid($this->arguments);
            $power = check_power_problem_write($this->arguments['uid']);
            if($power == false){
                throw new \Exception($this->config->item('104','errno'), 104);
            }

            $params = array(
                'uid'         => $this->arguments['uid'],
                'pid'         => $this->arguments['pid'],
            );
            isset($this->arguments['title']) && $params['title'] = $this->arguments['title'];
            isset($this->arguments['code']) && $params['code'] = $this->arguments['code'];
            isset($this->arguments['time_limit']) && $params['time_limit'] = $this->arguments['time_limit'];
            isset($this->arguments['mem_limit']) && $params['mem_limit'] = $this->arguments['mem_limit'];
            $pid = $this->Problems->update_problem_info($params);
            $this->response['data']['pid'] = $pid;
        }catch (Exception $e) {
            $this->response['errno'] = $e->getCode();
            $this->response['errmsg'] = $e->getMessage();
            log_message('error',$this->response['errmsg']);
        }
        echo json_encode($this->response);
    }
    public function check_arguments()
    {
        if(!isset($this->arguments['pid']) || !isset($this->arguments['token'])){
            throw new \Exception($this->config->item('103','errno'),103);
        }
    }
}