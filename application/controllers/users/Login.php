<?php

/**
 * Created by PhpStorm.
 * User: Hinata
 * Date: 2018/5/29
 * Time: 15:36
 */
class Login extends CI_Controller
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
            $params = array(
                'uname'       => $this->arguments['uname'],
                'password'    => $this->arguments['password'],
            );
            $user_info = $this->Users->login($params);
            if(empty($user_info)){
                $this->response['data'] = '';
            }else{
                $uid = $user_info['uid'];
                $token = create_token($uid);
                $this->response['data']['token'] = $token;
            }
        }catch (Exception $e) {
            $this->response['errno'] = $e->getCode();
            $this->response['errmsg'] = $e->getMessage();
            log_message('error',$this->response['errmsg']);
        }
        echo json_encode($this->response);
    }
    public function check_arguments()
    {
        if(!isset($this->arguments['password']) || !isset($this->arguments['uname'])){
            throw new \Exception($this->config->item('103','errno'), 103);
        }
    }

}