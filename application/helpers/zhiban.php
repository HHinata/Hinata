<?php
/**
 * 值班系统，在钉钉群里提醒值班哨兵. xiaoju_debug中配置crontab
 * 唐蕴梦 2018-04-16
 */
$date = $argv[1]=='tomorrow' ? date('Y-m-d',strtotime("+1 day")) : date('Y-m-d');
$dateStr = $argv[1]=='tomorrow' ? "明日" : "今日";

$config = array(
    array(
        'robot' => 'https://oapi.dingtalk.com/robot/send?access_token=3d4f10c5bbfe4a562881fc8d64e65f780a912b28260bc679034c65b70ea9b668',
        'serviceName' => '%E9%A1%BA%E9%A3%8E%E8%BD%A6API%E6%9C%8D%E5%8A%A1%E7%AB%AF',
        'token' => '75c1e07d73ff763a',
        'text' => '值班哨兵：',
    ),
);

$header[] = "Content-Type: application/json";
foreach ($config as $value){
    $url = "http://100.69.178.41:8040/api/duties/getDuties?startDate=".$date."&endDate=".$date."&serviceName=".$value['serviceName']."&token=".$value['token'];
    $who = httpRequest($url);
    $heroName = $value['text'];
    $flag = 0;
    if(isset($who[0]['staffs']) && is_array($who[0]['staffs'])){
        foreach ($who[0]['staffs'] as $hero){
            $heroName.='@'.$hero['name'];
            $flag = 1;
        }
    }
    $data = [
        'msgtype'=>'text',
        'text' => ['content'=>$dateStr.$heroName],
    ];
    if($flag){
        $re = httpRequest($value['robot'], 'POST', $data, $header);
    }
}

function httpRequest($url, $method = 'GET', $data = [], $header = []) {

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    if ($method == 'POST') {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    $ret = curl_exec($ch);
    if(!$ret){
        return false;
    }
    return json_decode($ret, true);
}
