<?php

/**
 * Created by PhpStorm.
 * User: Hinata
 * Date: 2018/4/16
 * Time: 15:06
 */
class Authorization extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('common'));
    }
    public function index()
    {
        $arguments = array_merge($_POST,$_GET);
        $uid = get_uid($arguments);
    }

}