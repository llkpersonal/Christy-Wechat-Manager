<?php

require_once(dirname(__FILE__).'/include/reply.frame.inc.php');
require_once(dirname(__FILE__).'/include/database.inc.php');
require_once(dirname(__FILE__).'/include/msg.inc.php');

if(!isset($_COOKIE['username'])){
    header("location: login.php");
}

class TextReplyFrame extends ReplyFrame{
    public function __construct($title = "编辑文本回复 - Christy微信公众平台管理系统"){
        parent::__construct($title);
        
        if(!isset($_GET['mid'])){
            $this->content = file_get_contents('template/textreply.htm');
            $this->get_adminfor_toselect();
        } else {
            $mid = $_GET['mid'];
            $this->content = file_get_contents('template/textreplymod.htm');
            $this->content = str_replace('{template_mid}',$mid,$this->content);
            $aDatabase = new Database();
            $array = mysql_fetch_array($aDatabase->get_result("SELECT * FROM `msgindexes` WHERE mid='$mid';"));
            $this->content = str_replace('{template_keyword}',$array['keyword'],$this->content);
            $this->content = str_replace('{template_rid}',$array['rid'],$this->content);
            $row = mysql_fetch_row($aDatabase->get_result("SELECT `content` FROM `textreply` WHERE rid='".$array['rid']."';"));
            $this->content = str_replace('{template_textreply}',$row[0],$this->content);
        }
    }
}

function addTextMsg($wid,$keyword,$content){
    $aDatabase = new Database();
    $result = $aDatabase->get_result("INSERT INTO `textreply` (`rid`,`content`) VALUES (NULL,'$content');");
    $rid = mysql_insert_id();
    $result = $aDatabase->get_result("INSERT INTO `msgindexes` (`wid`,`keyword`,`type`,`rid`) VALUES ('$wid','$keyword','text','$rid');");
}

if( $_GET['action']=='modify' ){
    $mid = $_POST['mid'];
    $rid = $_POST['rid'];
    $keyword = $_POST['keyword'];
    $replytext = addslashes($_POST['replytext']);
    $aDatabase = new Database();
    $aDatabase->get_result("UPDATE `msgindexes` SET `keyword`='$keyword' WHERE `mid`='$mid';");
    $aDatabase->get_result("UPDATE `textreply` SET `content`='$replytext' WHERE `rid`='$rid';");
    $aMsgFrame = new MsgFrame();
    $aMsgFrame->display_page('文本回复信息修改成功！','checkreply.php');
} else if( $_GET['action']=='add' ){
    $platid = $_POST['platid'];
    $keyword = $_POST['keyword'];
    $content = addslashes($_POST['replytext']);
    foreach($platid as $wid){
        addTextMsg($wid,$keyword,$content);
    }
    $aMsgFrame = new MsgFrame();
    $aMsgFrame->display_page('文本回复信息添加成功！','checkreply.php');
}

$aTextReplyFrame = new TextReplyFrame();

$aTextReplyFrame->display_page();

?>