<?php
header("Content-type:text/html;charset=UTF-8");

require_once(dirname(__FILE__).'/../plugin.inc.php');

$aEvent = new event(0);
//echo $GLOBALS["HTTP_RAW_POST_DATA"];

function simsimiHttp($keyword)
{
    $ch = curl_init('http://www.simsimi.com/func/req?lc=ch&ft=0.0&msg='.$keyword);
    $header = array("Accept: text/html, application/xhtml+xml, */*",
                        "X-Requested-With: XMLHttpRequest",
                        "User-Agent: Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; Trident/6.0)",
                        "Content-Type: application/json; charset=utf-8",
                        "Referer: http://www.simsimi.com/talk.htm",
                        "Accept-Encoding: gzip, deflate",
                        "Accept-Language: zh-CN",
			"Host: www.simsimi.com",
			"If-None-Match: \"1703702185\"",
			"DNT: 1",
                        "Cookie: selected_nc=ch; __utma=119922954.792870437.1390618072.1390618072.1390618072.1; __utmb=119922954.98.7.1390620255726; __utmz=119922954.1390618072.1.1.utmcsr=baidu|utmccn=(organic)|utmcmd=organic|utmctr=simsimi; __utmc=119922954; Filtering=0.0; popupCookie=true; selected_nc=ch",
                        "Connection: keep-alive"
                        );
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    $data = curl_exec($ch);
    curl_close($ch);
    
    return $data;
}

$json = simsimiHttp(urlencode($aEvent->content));
$array = json_decode($json);
$aMessage = new TextMessage($aEvent,$array->response);
$aMessage->response();
?>