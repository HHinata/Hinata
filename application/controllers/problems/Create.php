<?php

/**
 * Created by PhpStorm.
 * User: Hinata
 * Date: 2018/4/13
 * Time: 19:37
 */
class Create extends CI_Controller
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

            $params = array(
                'uid'         => $this->arguments['uid'],
                'type'        => $this->arguments['type'],
                'time_limit'  => $this->arguments['time_limit'],
                'mem_limit'   => $this->arguments['mem_limit'],
            );
            isset($this->arguments['title']) && $params['title'] = $this->arguments['title'];
            isset($this->arguments['code']) && $params['code'] = $this->arguments['code'];
            $pid = $this->Problems->create_problems($params);
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
        if(!isset($this->arguments['time_limit']) || !isset($this->arguments['uid']) || !isset($this->arguments['type']) || !isset($this->arguments['mem_limit'])){
            throw new \Exception($this->config->item('103','errno'),103);
        }
    }
}