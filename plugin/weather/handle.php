<?php

require_once(dirname(__FILE__).'/../plugin.inc.php');

$wid = $_GET['wid'];
$aEvent = new event($wid);
$aMessage = new Message($wid);

if($aEvent->msgType=='text'){
    if($aEvent->content=='天气'){
        $str = "进入天气模式，请开启定位服务，点击‘+’号发送位置！\n发送‘Q’退出天气模式！";
        $aMessage = new TextMessage($aEvent,$str);
        insert_protrol($_GET['wid'],$aEvent->fromUserName,1,'plugin/weather/handle.php');
    } else if($aEvent->content=='Q'||$aEvent->content=='退出'){
        $str = "已退出天气模式！";
        delete_protrol($_GET['wid'],$aEvent->fromUserName,'plugin/weather/handle.php');
        $aMessage = new TextMessage($aEvent,$str);
    } else {
        $str = "输入错误，请重新输入！\n发送‘Q’退出天气模式！";
        $aMessage = new TextMessage($aEvent,$str);
    }
} else if($aEvent->msgType=='location'){
    $url = "http://api.map.baidu.com/geocoder/v2/?ak=jusURyzNZbMxGRnExePLpIpi&callback=renderReverse&location=$aEvent->location_x,$aEvent->location_y&output=xml&pois=1";
    $xml_str = file_get_contents($url);
    preg_match_all("/\<district\>(.*?)\<\/district\>/",$xml_str,$array);
    $district = $array[1][0];
    $xml_str = file_get_contents("http://api.map.baidu.com/telematics/v2/weather?location=".urlencode($district)."&ak=jusURyzNZbMxGRnExePLpIpi");
    preg_match_all("/\<date\>(.*?)\<\/date\>/",$xml_str,$date);
    preg_match_all("/\<weather\>(.*?)\<\/weather\>/",$xml_str,$weather);
    preg_match_all("/\<wind\>(.*?)\<\/wind\>/",$xml_str,$wind);
    preg_match_all("/\<temperature\>(.*?)\<\/temperature\>/",$xml_str,$temperature);
    $arr = array($district."天气",
            $date[1][0],
            "天气状况：".$weather[1][0],
            "风力：".$wind[1][0],
            "气温：".$temperature[1][0]);
    $str = implode("\n",$arr);
    $aMessage = new TextMessage($aEvent,$str);
    delete_protrol($_GET['wid'],$aEvent->fromUserName,'plugin/weather/handle.php');
}
$aMessage->response();


?>