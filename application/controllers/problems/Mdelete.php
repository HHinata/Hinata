<?php

/**
 * Created by PhpStorm.
 * User: Hinata
 * Date: 2018/4/28
 * Time: 17:14
 */
class Mdelete extends CI_Controller
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
                'pids'  => $this->arguments['pids'],
            );
            $this->Problems->mdelete_problem_info($params);
            $this->response['data']['pids'] = $this->arguments['pids'];
        }catch (\Exception $e){
            $this->response['errno'] = $e->getCode();
            $this->response['errmsg'] = $e->getMessage();
            log_message('error',$this->response['errmsg']);
        }
        echo json_encode($this->response);
    }
    public function check_arguments()
    {
        if(!isset($this->arguments['uid']) || !isset($this->arguments['pids']) || !is_array($this->arguments['pids'])){
            throw new \Exception($this->config->item('103','errno'),103);
        }
    }
}