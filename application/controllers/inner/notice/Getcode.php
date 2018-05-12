<?php

/**
 * Created by PhpStorm.
 * User: Hinata
 * Date: 2018/5/10
 * Time: 17:33
 */
class Getcode extends CI_Controller
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
            if(!$this->load->helper(array('common','download')) || !$this->load->model('Notices') || !$this->config->load('errno',true)){
                throw new \Exception($this->config->item('102','errno'), 102);
            }
            $this->check_arguments();
            $file_name = get_notice_file_name($this->arguments['notice_id']);
            $file_name = $this->config->item('upload_path').$file_name;
            if(!force_download($file_name,NULL)){
                throw new \Exception($this->config->item('100003','errno'),100003);
            }
        }catch (\Exception $e){
            $this->response['errno'] = $e->getCode();
            $this->response['errmsg'] = $e->getMessage();
            log_message('error',$this->response['errmsg']);
        }
        echo json_encode($this->response);
    }
    public function check_arguments()
    {
        if(!isset($this->arguments['notice_id'])){
            throw new \Exception($this->config->item('103','errno'),103);
        }
    }
}