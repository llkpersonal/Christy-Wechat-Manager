<?php

require_once(dirname(__FILE__).'/../plugin.inc.php');
require_once(dirname(__FILE__).'/functional.php');
require_once(dirname(__FILE__).'/engine.php');

$aEvent = new event($_GET['wid']);
$handlepage = "/plugin/wxwall/handle.php";

if($aEvent->msgType!='text'){
    $aMessage = new TextMessage($aEvent,"暂时只支持文字内容上墙哟~\n您可以输入“退出”退出微信墙");
    $aMessage->response();
    exit();
}

switch($aEvent->content){
    case "微信墙": handle_wxwall($_GET['wid'],$aEvent); break;
    case "退出":  exit_plugin($aEvent,$_GET['wid']); break;
    default: handle_default($_GET['wid'],$aEvent);    
}

function exit_plugin($event,$wid){
    global $handlepage;
    $protrol = get_protrol($wid,$event->fromUserName);
    delete_protrol($_GET['wid'],$event->fromUserName,$handlepage);    
    if($protrol=='1'){
        $aDatabase = new Database();
        $aDatabase->get_result("DELETE FROM `wxwall_user` WHERE `wid`='$wid' AND `openid`='".$event->fromUserName."';");
    }
    $aMessage = new TextMessage($event,"已退出微信墙!");
    $aMessage->response();
    exit();
}

function handle_wxwall($wid,$event){
    global $handlepage;
    $str = "handle_wxwall";
    if(hasOpenid($wid,$event->fromUserName)){
        $str = "你已经进入了微信墙模式，发送任何信息即代表向微信墙上投放内容！\n输入“退出”即可退出微信墙模式";
        insert_protrol($wid,$event->fromUserName,2,$handlepage);
    } else {
        $str = "你已经进入了微信墙模式，由于您是第一次使用，请您输入验证码：".substr($event->fromUserName,10,6)."\n(不区分大小写)\n或者输入“退出”退出微信墙！";
        insert_protrol($wid,$event->fromUserName,1,$handlepage);
        addUser($wid,$event->fromUserName);
    }
    $aMessage = new TextMessage($event,$str);
    $aMessage->response();
}

function handle_default($wid,$event){
    global $handlepage;
    $protrol = get_protrol($wid,$event->fromUserName);
    if($protrol=='1'){
        if(strtolower($event->content)==strtolower(substr($event->fromUserName,10,6))){
            $aEngine = new Engine();
            if($aEngine->get_information($_GET['wid'],$event->fromUserName,$event->content,$event->createTime)){
                $aDatabase = new Database();
                $aDatabase->get_result("UPDATE `intermediate` SET `protrol`='2' WHERE `wechat_user`='".$event->fromUserName."' AND `handle`='".$handlepage."' AND `wid`='".$wid."'");
                unset($aDatabase);
                $fakeid = $aEngine->get_head_img($_GET['wid'],$event->fromUserName);
                $aEngine->send($fakeid,"验证成功！您现在回复的任何消息都将作为投放至微信墙上的消息！\n您可以输入“退出”退出微信墙");
            }
        } else {
            $aMessage = new TextMessage($event,"尚未验证成功，请重新输入刚才的验证码！\n您可以输入“退出”退出微信墙");
            $aMessage->response();
        }
    } else if($protrol=='2'&&strtolower($event->content)!=strtolower(substr($event->fromUserName,10,6))){
        global $handlepage;
        $aDatabase = new Database();
        $wid = $_GET['wid'];
        $xml_obj = simplexml_load_file("config/$wid.xml");
        $result = $aDatabase->get_result("SELECT MAX(`num`) FROM `wxwall_msg` WHERE `wid`='".$_GET['wid']."'");
        if($row = mysql_fetch_row($result)) $num = $row[0]+1;
        else $num=1;
        if($xml_obj->needexamine=='yes') $num=0;
        $result = $aDatabase->get_result("SELECT * FROM `wxwall_user` WHERE `openid`='$event->fromUserName' AND `wid`=".$_GET['wid']);
        $array = mysql_fetch_array($result);
        $nick_name = $array['username'];
        $uid = $array['uid'];
        $avatar = "http://".$_SERVER['HTTP_HOST']."/plugin/wxwall/headimg/$uid.jpg";
        $aDatabase->get_result("INSERT INTO `wxwall_msg` (`mid`,`wid`,`num`,`content`,`nickname`,`avatar`) VALUES (NULL,'".$_GET['wid']."','$num','$event->content','$nick_name','$avatar');");
        $aMessage = new TextMessage($event,"感谢您参与微信墙的互动，您的消息将在工作人员审核后上墙显示！\n您可以输入“退出”退出微信墙");
        $aMessage->response();
    } else {
        $aMessage = new TextMessage($event,"");
        $aMessage->response();
    }
}

?>