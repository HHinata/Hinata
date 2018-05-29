<?php

/**
 * Created by PhpStorm.
 * User: Hinata
 * Date: 2018/4/13
 * Time: 19:53
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
            if(!$this->load->helper(array('common')) || !$this->load->model('Notices') || !$this->load->library('upload') || !$this->config->load('errno',true)){
                throw new \Exception($this->config->item('102','errno'), 102);
            }
            $this->check_arguments();
            $this->arguments['uid'] = get_uid($this->arguments);
            $power = check_power_notice_write($this->arguments['uid']);
            if($power == false){
                throw new \Exception($this->config->item('104','errno'), 104);
            }
            $this->do_upload();
        }catch (Exception $e) {
            $this->response['errno'] = $e->getCode();
            $this->response['errmsg'] = $e->getMessage();
            log_message('error',$this->response['errmsg']."    ".json_encode($this->arguments));
        }
        echo json_encode($this->response);
    }
    public function do_upload()
    {
        $notice_id = create_notice_id($this->arguments['uid']);
        $code = '';
        $params = array(
            'pid'         => $this->arguments['pid'],
            'uid'         => $this->arguments['uid'],
            'notice_id'   => $notice_id,
            'code'        => $code,
        );
        if($this->arguments['need_upload']){
            $params['colony_id'] = $this->config->item('colony_id');
            $config['file_name'] = get_notice_file_name($notice_id);
            $config['upload_path']   = get_notice_file_path($notice_id);
            log_message('error',$config['upload_path'].'    '.$this->arguments['uid']);
            if(!$this->upload->initialize($config,false)){
                throw new \Exception($this->config->item('102','errno'), 102);
            }
            if (!$this->upload->do_upload('notice_code')) {
                $error = array('error' => $this->upload->display_errors());
                throw new \Exception($error['error'], 100001);
            }
            //send_file($config['upload_path'],'127.0.0.1/',);
        }
        $this->Notices->create_notices($params);
        $this->response['data']['notice_id'] = $notice_id;
    }
    public function check_arguments()
    {
        if(!isset($this->arguments['pid']) || !isset($this->arguments['token'])){
            throw new \Exception($this->config->item('103','errno'), 103);
        }
    }
}