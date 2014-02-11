<?php

require_once(dirname(__FILE__).'/include/reply.frame.inc.php');
require_once(dirname(__FILE__).'/include/database.inc.php');
require_once(dirname(__FILE__).'/include/msg.inc.php');

class MusicReplyFrame extends ReplyFrame{
    public function __construct($title = "编辑音乐回复"){
        parent::__construct($title);
        if(!isset($_GET['mid'])){
            $this->content = file_get_contents(dirname(__FILE__).'/template/musicreply.htm');
            $this->get_adminfor_toselect();
        } else {
            $mid = $_GET['mid'];
            $this->content = file_get_contents(dirname(__FILE__).'/template/musicreplymod.htm');
            $aDatabase = new Database();
            $array = mysql_fetch_array($aDatabase->get_result("SELECT * FROM `msgindexes` WHERE `mid`=$mid;"));
            $this->content = str_replace('{template_keyword}',$array['keyword'],$this->content);
            $this->content = str_replace('{template_mid}',$array['mid'],$this->content);
            $rid = $array['rid'];
            $this->content = str_replace('{template_rid}',$rid,$this->content);
            $array = mysql_fetch_array($aDatabase->get_result("SELECT * FROM `musicreply` WHERE `rid`=$rid;"));
            $this->content = str_replace('{template_title}',$array['title'],$this->content);
            $this->content = str_replace('{template_description}',$array['description'],$this->content);
            $this->content = str_replace('{template_musicurl}',$array['musicurl'],$this->content);
            $this->content = str_replace('{template_hqurl}',$array['hqurl'],$this->content);
        }
    }
}

function addMusicMsg($wid,$keyword,$title,$description,$musicurl,$hqurl){
    $aDatabase = new Database();
    $result = $aDatabase->get_result("INSERT INTO `musicreply` (`rid`,`title`,`description`,`musicurl`,`hqurl`) VALUES (NULL,'$title','$description','$musicurl','$hqurl');");
    $rid = mysql_insert_id();
    $result = $aDatabase->get_result("INSERT INTO `msgindexes` (`wid`,`keyword`,`type`,`rid`) VALUES ('$wid','$keyword','music','$rid');");
}

if( $_GET['action']=='modify' ){
    $mid = $_POST['mid'];
    $rid = $_POST['rid'];
    $keyword = $_POST['keyword'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $musicurl = $_POST['musicurl'];
    $hqurl = $_POST['hqurl'];
    $aDatabase = new Database();
    $aDatabase->get_result("UPDATE `msgindexes` SET `keyword`='$keyword' WHERE `mid`='$mid';");
    $aDatabase->get_result("UPDATE `musicreply` SET `title`='$title',`description`='$description',`musicurl`='$musicurl',`hqurl`='$hqurl' WHERE `rid`='$rid';");
    $aMsgFrame = new MsgFrame();
    $aMsgFrame->display_page('音乐回复信息修改成功！','checkreply.php');
} else if( $_GET['action']=='add' ) {
    $platid = $_POST['platid'];
    $keyword = $_POST['keyword'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $musicurl = $_POST['musicurl'];
    $hqurl = $_POST['hqurl'];
    foreach($platid as $wid){
        addMusicMsg($wid,$keyword,$title,$description,$musicurl,$hqurl);
    }
    $aMsgFrame = new MsgFrame();
    $aMsgFrame->display_page('音乐回复信息添加成功！','checkreply.php');
}

$aMusicReplyFrame = new MusicReplyFrame();
$aMusicReplyFrame->display_page();

?>