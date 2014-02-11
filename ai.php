<?php
header("Content-type:text/html;charset=utf-8");

require_once('wxapi/event.php');
require_once('wxapi/msg.php');
require_once('wxapi/textmsg.php');
require_once('include/database.inc.php');
require_once('wxapi/msgidx.php');
require_once('wxapi/pictextmsg.php');
require_once('wxapi/musicmsg.php');
require_once('include/post.inc.php');

$wid = $_GET['wid'];

$aEvent = new event($wid);

if($aEvent->checkSignature()){
    $echoStr = $aEvent->echoStr;
    echo $echoStr;
}

if($interpage = get_handle_page($wid,$aEvent->fromUserName)){
    $res = post_xml_url($_SERVER['SERVER_NAME'].'/'.$interpage.'?wid='.$wid,$GLOBALS["HTTP_RAW_POST_DATA"]);
    echo $res;
} else {
    switch($aEvent->msgType){
        case "event":HandleEvent($aEvent); break;
        case "text": HandleTextMsg($aEvent); break;
    }
}


function HandleEvent($event){
    $aDatabase = new Database();
    $aMessage = new Message($event);
    switch($event->event){
        case "subscribe":        
        $result = $aDatabase->get_result("SELECT `welcome_msg` FROM `weconfig` WHERE `wid`='".$event->wid."'");
        $row = mysql_fetch_row($result);
        $aMessage = new TextMessage($event,stripslashes($row[0]));
        $aDatabase->get_result("UPDATE `weconfig` SET `fans_num`=`fans_num`+1 WHERE `wid`='".$event->wid."'");
        break;
        case "unsubscribe":
        $aDatabase->get_result("UPDATE `weconfig` SET `fans_num`=`fans_num`-1 WHERE `wid`='".$event->wid."'");
        break;
    }
    $aMessage->response();
}

function HandleTextMsg($event){
    $aMessage = new Message($event);
    $keyword = $event->content;
    if($plugin_folder = get_before_plugin($_GET['wid'],$keyword)){
        $res = post_xml_url($_SERVER['SERVER_NAME'].'/plugin/'.$plugin_folder.'/handle.php?wid='.$event->wid,$GLOBALS["HTTP_RAW_POST_DATA"]);
        echo $res;
    } else if(($aMsgIdx = fetch_accurate_msgidx($event,$keyword))||($aMsgIdx = fetch_blur_msgidx($event,$keyword))){
        if($aMsgIdx->type=="text"){
            $aMessage = new TextMessage($event,get_text_content($aMsgIdx->rid));
        } else if($aMsgIdx->type=="pictext"){
            //$aMessage = new TextMessage($event,$aMsgIdx->rid);
            $aMessage = fetch_pictextmsg_by_rid($event,$aMsgIdx->rid);
        } else if($aMsgIdx->type="music"){
            $aMessage = fetch_musicmsg_by_rid($event,$aMsgIdx->rid);
        } 
    } else {
        $aDatabase = new Database();
        global $wid;
        $result = $aDatabase->get_result("SELECT `folder` FROM `plugin` WHERE `wid`='$wid' AND `protrol`='after';");
        if($array = mysql_fetch_row($result)){
            $res = post_xml_url($_SERVER['SERVER_NAME'].'/plugin/'.$array[0].'/handle.php?wid='.$event->wid,$GLOBALS["HTTP_RAW_POST_DATA"]);
            echo $res;
        } // if array
    } // else
   
    $aMessage->response();
}

?>