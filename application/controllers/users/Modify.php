<?php

/**
 * Created by PhpStorm.
 * User: Hinata
 * Date: 2018/5/10
 * Time: 17:22
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
        $arguments = array_merge($_POST,$_GET);
        try{
            if(!$this->load->helper(array('common')) || !$this->load->model('Users') || !$this->config->load('errno',true)){
                throw new \Exception($this->config->item('102','errno'), 102);
            }
            $this->check_arguments();
            $this->arguments['uid'] = get_uid($this->arguments);
            $params = array(
                'uid'         => $this->arguments['uid'],
                'password'    => $this->arguments['password'],
            );
            $uid = $this->Users->update_user_info($params);
            $token = $this->arguments['token'];

            $this->response['data']['token'] = $token;
        }catch (Exception $e) {
            $this->response['errno'] = $e->getCode();
            $this->response['errmsg'] = $e->getMessage();
            log_message('error',$this->response['errmsg']);
        }
        echo json_encode($this->response);
    }
    public function check_arguments()
    {
        if(!isset($this->arguments['token']) || !isset($this->arguments['password'])){
            throw new \Exception($this->config->item('103','errno'), 103);
        }
    }

}