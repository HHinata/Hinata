<?php

/**
 * Created by PhpStorm.
 * User: Hinata
 * Date: 2018/5/10
 * Time: 17:23
 */
class Upload extends CI_Controller
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
            $this->do_upload();
        }catch (Exception $e) {
            $this->response['errno'] = $e->getCode();
            $this->response['errmsg'] = $e->getMessage();
            log_message('error',$this->response['errmsg']);
        }
        echo json_encode($this->response);
    }
    public function do_upload()
    {
        $notice_id = $this->arguments['notice_id'];
        $config['file_name'] = get_notice_file_name($notice_id);
        if(!$this->upload->initialize($config,false)){
            throw new \Exception($this->config->item('102','errno'), 102);
        }
        if (!$this->upload->do_upload('notice_code')) {
            $error = array('error' => $this->upload->display_errors());
            throw new \Exception($error['error'], 100001);
        }
        //$code = json_encode(file_get_contents($_FILES['notice_code']['tmp_name']));
        //echo json_encode($code);exit(0);
        $code = '';
        $params = array(
            'uid'         => $this->arguments['uid'],
            'notice_id'   => $notice_id,
            'code'        => $code,
        );
        $this->Notices->update_notice_info($params);
        $this->response['data']['notice_id'] = $notice_id;
    }
    public function check_arguments()
    {
        if(!isset($this->arguments['notice_id']) || !isset($this->arguments['uid']) || !isset($_FILES['notice_code'])){
            throw new \Exception($this->config->item('103','errno'), 103);
        }
    }
}