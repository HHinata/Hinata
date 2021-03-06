<?php

/**
 * Created by PhpStorm.
 * User: Hinata
 * Date: 2018/5/29
 * Time: 16:18
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
            if(!$this->load->helper(array('common')) || !$this->load->model('Users') || !$this->config->load('errno',true)){
                throw new \Exception($this->config->item('102','errno'), 102);
            }
            $this->check_arguments();
            $uid = get_uid($this->arguments);
            $power = check_power_user_manage($uid);
            if($power == false){
                throw new \Exception($this->config->item('104','errno'), 104);
            }
            $params = array(
                'uid'         => $this->arguments['uid'],
            );
            $uid = $this->Users->delete_user_info($params);

            $this->response['data']['token'] = $uid;
        }catch (Exception $e) {
            $this->response['errno'] = $e->getCode();
            $this->response['errmsg'] = $e->getMessage();
            log_message('error',$this->response['errmsg']);
        }
        echo json_encode($this->response);
    }
    public function check_arguments()
    {
        if(!isset($this->arguments['token']) || !isset($this->arguments['uid'])){
            throw new \Exception($this->config->item('103','errno'), 103);
        }
    }

}