<?php

/**
 * Created by PhpStorm.
 * User: Hinata
 * Date: 2018/4/13
 * Time: 19:38
 */
class Delete extends CI_Controller
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
                'uid'  => $this->arguments['uid'],
                'pid'  => $this->arguments['pid'],
            );
            $this->Problems->delete_problem_info($params);
            $this->response['data']['pid'] = $this->arguments['pid'];
        }catch (\Exception $e){
            $this->response['errno'] = $e->getCode();
            $this->response['errmsg'] = $e->getMessage();
            log_message('error',$this->response['errmsg']);
        }
        echo json_encode($this->response);
    }
    public function check_arguments()
    {
        if(!isset($this->arguments['uid']) || !isset($this->arguments['pid'])){
            throw new \Exception($this->config->item('103','errno'),103);
        }
    }
}