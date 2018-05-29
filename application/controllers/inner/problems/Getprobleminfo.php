<?php

/**
 * Created by PhpStorm.
 * User: Hinata
 * Date: 2018/4/23
 * Time: 23:41
 */
class Getprobleminfo extends CI_Controller
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
            if(!$this->load->helper(array('common')) || !$this->load->model('Problems') || !$this->load->model('Cases') || !$this->config->load('errno',true)){
                throw new \Exception($this->config->item('102','errno'), 102);
            }
            $this->check_arguments();
            $uid = '121527580551';
            $power = check_power_inner($uid);
            if($power == false){
                throw new \Exception($this->config->item('104','errno'), 104);
            }
            $params = array(
                'pid'  => $this->arguments['pid'],
            );
            $case_infos = $this->Cases->show_case_id_by_pid($params);
            $pro_info = $this->Problems->show_problem_info($params);
            //echo json_encode($pro_info);exit(0);
            foreach ($case_infos as $key => $value){
                if($value['type'] == 1){
                    $pro_info['case_in_ids'] = $value['case_id'];
                }else{
                    $pro_info['case_out_ids'] = $value['case_id'];
                }
            }
            if($pro_info == false){
                throw new \Exception($this->config->item('100004','errno'),100004);
            }
            $this->response['data']['problem_info'] = $pro_info;
        }catch (\Exception $e){
            $this->response['errno'] = $e->getCode();
            $this->response['errmsg'] = $e->getMessage();
            log_message('error',$this->response['errmsg']);
        }
        echo json_encode($this->response);
    }
    public function check_arguments()
    {
        if(!isset($this->arguments['pid'])){
            throw new \Exception($this->config->item('103','errno'),103);
        }
    }
}