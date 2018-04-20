<?php

/**
 * Created by PhpStorm.
 * User: Hinata
 * Date: 2018/4/13
 * Time: 19:38
 */
class Show extends CI_Controller
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
            $pro_info = $this->Problems->show_problem_info($params);
            if($pro_info == false){
                throw new \Exception($this->config->item('100004','errno'),100004);
            }
            $this->response['data']['problem_info'] = $pro_info;
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