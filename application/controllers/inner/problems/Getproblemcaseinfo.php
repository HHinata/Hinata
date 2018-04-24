<?php

/**
 * Created by PhpStorm.
 * User: Hinata
 * Date: 2018/4/23
 * Time: 23:41
 */
class Getproblemcaseinfo extends CI_Controller
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
            if(!$this->load->helper(array('common','download')) || !$this->load->model('Cases') || !$this->config->load('errno',true)){
                throw new \Exception($this->config->item('102','errno'), 102);
            }
            $this->check_arguments();
            $params = array(
                'case_id' => $this->arguments['case_id'],
                'type'  => $this->arguments['type'],
            );
            $case_info = $this->Cases->show_case_info($params);
            if($case_info == false){
                throw new \Exception($this->config->item('100002','errno'),100002);
            }
            $file_name = get_case_file_name($this->arguments['type'],$this->arguments['case_id']);
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
        if(!isset($this->arguments['case_id']) || !isset($this->arguments['type'])){
            throw new \Exception($this->config->item('103','errno'),103);
        }
    }
}