<?php

/**
 * Created by PhpStorm.
 * User: Hinata
 * Date: 2018/4/13
 * Time: 19:53
 */
class Modify extends CI_Controller
{

    private $response;
    private $arguments;

    public function __construct()
    {
        parent::__construct();
        $this->arguments = array_merge($_POST, $_GET);
        $this->response = array(
            'errno' => 0,
            'errmsg' => '',
            'data' => [],
        );
    }

    public function index()
    {
        try {
            if (!$this->load->helper(array('common')) || !$this->load->model('Notices') || !$this->config->load('errno', true)) {
                throw new \Exception($this->config->item('102', 'errno'), 102);
            }
            $this->check_arguments();
            $this->arguments['uid'] = get_uid($this->arguments);
            $power = check_power_notice_write($this->arguments['uid']);
            if($power == false){
                throw new \Exception($this->config->item('104','errno'), 104);
            }
            $params = array(
                'uid' => $this->arguments['uid'],
                'notice_id' => $this->arguments['notice_id'],
            );
            isset($this->arguments['pid']) && $params['pid'] = $this->arguments['pid'];
            isset($this->arguments['notice_status']) && $params['notice_status'] = $this->arguments['notice_status'];
            $notice_id = $this->Notices->update_notice_info($params);
            $this->response['data']['notice_id'] = $notice_id;
        } catch (Exception $e) {
            $this->response['errno'] = $e->getCode();
            $this->response['errmsg'] = $e->getMessage();
            log_message('error', $this->response['errmsg']);
        }
        echo json_encode($this->response);
    }

    public function check_arguments()
    {
        if (!isset($this->arguments['notice_id']) || !isset($this->arguments['token'])) {
            throw new \Exception($this->config->item('103', 'errno'), 103);
        }
    }
}