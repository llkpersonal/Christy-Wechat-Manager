<?php

require_once('include/reply.frame.inc.php');
require_once('include/database.inc.php');

if(!isset($_COOKIE['username'])){
    header("location: login.php");
}

class CheckReplyFrame extends ReplyFrame{
    private $option_contents;
    public function __construct($title="管理智能回复 - Christy微信公众平台管理系统"){
        parent::__construct($title);
        $this->content = file_get_contents('template/checkreply.htm');
        $aDatabase = new Database();
        $resAdminfor = $aDatabase->get_result("SELECT `admin` FROM `user` WHERE `username`='".$_COOKIE['username']."';");
        $rowAdminfor = mysql_fetch_row($resAdminfor);
        $cmd = "SELECT * FROM `weconfig` WHERE `wid` IN (".$rowAdminfor[0].");";
        if($rowAdminfor[0]==0){
            $cmd = "SELECT * FROM `weconfig`;";
        }
        $resOption = $aDatabase->get_result($cmd);
        while($arrayOption = mysql_fetch_array($resOption)){
            $this->addOption($arrayOption['wid'],$arrayOption['name']);
        }        
        
        $this->content = str_replace('{template_options}',$this->option_contents,$this->content);
    }
    
    private function addOption($wid,$name){
        $this->option_contents .= "<option value=\"$wid\">$name</option>";
    }
}

function delAmsg($mid){
    $aDatabase = new Database();
    $array = mysql_fetch_array($aDatabase->get_result("SELECT * FROM `msgindexes` WHERE `mid`='$mid'"));
    $cmd = "";
    switch($array['type']){
        case "text": $cmd = "DELETE FROM `textreply` WHERE `rid`='".$array['rid']."';";
        case "music": $cmd = "DELETE FROM `musicreply` WHERE `rid`='".$array['rid']."';";
        case "pictext": $cmd = "DELETE FROM `pictextreply` WHERE `rid`='".$array['rid']."';";
    }
    $aDatabase->get_result($cmd);
    $aDatabase->get_result("DELETE FROM `msgindexes` WHERE `mid`='$mid';");
}

if($_GET['action']=='del'){
    $mid = $_GET['mid'];
    delAmsg($mid);
    header("location: checkreply.php");
}

$aCheckReplyFrame = new CheckReplyFrame();
$aCheckReplyFrame->display_page();

?>