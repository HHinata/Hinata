<?php

/**
 * Created by PhpStorm.
 * User: Hinata
 * Date: 2018/4/28
 * Time: 17:27
 */
class Showbyuser extends CI_Controller
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
            $power = check_power_problem_read($this->arguments['uid']);
            if($power == false){
                throw new \Exception($this->config->item('104','errno'), 104);
            }
            $params = array(
                'uid'  => $this->arguments['uid'],
            );
            $pro_infos = $this->Problems->show_problem_info_by_user($params);
            if($pro_infos == false){
                throw new \Exception($this->config->item('100004','errno'),100004);
            }
            $this->response['data']['problem_infos'] = $pro_infos;
        }catch (\Exception $e){
            $this->response['errno'] = $e->getCode();
            $this->response['errmsg'] = $e->getMessage();
            log_message('error',$this->response['errmsg']);
        }
        echo json_encode($this->response);
    }
    public function check_arguments()
    {
        if(!isset($this->arguments['token'])){
            throw new \Exception($this->config->item('103','errno'),103);
        }
    }
}