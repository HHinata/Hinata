<?php

/**
 * Created by PhpStorm.
 * User: Hinata
 * Date: 2018/4/13
 * Time: 19:38
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
            if(!$this->load->helper(array('common')) || !$this->load->model('Cases') || !$this->load->library('upload') || !$this->config->load('errno',true)){
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
        $case_id = create_case_id();
        $config['file_name'] = get_case_file_name($this->arguments['type'],$case_id);
        if(!$this->upload->initialize($config,false)){
            throw new \Exception($this->config->item('102','errno'), 102);
        }
        if (!$this->upload->do_upload('case_file')) {
            $error = array('error' => $this->upload->display_errors());
            throw new \Exception($error, 100001);
        }
        else {
            $data = array('upload_data' => $this->upload->data());
            $params = array(
                'pid'         => $this->arguments['pid'],
                'uid'         => $this->arguments['uid'],
                'type'        => $this->arguments['type'],
                'case_id'     => $case_id,
            );
            $this->Cases->create_cases($params);
            $this->response['data']['case_id'] = $case_id;
        }
    }
    public function check_arguments()
    {
        if(!isset($this->arguments['pid']) || !isset($this->arguments['type']) || !isset($this->arguments['uid'])){
            throw new \Exception($this->config->item('103','errno'), 103);
        }
    }
}