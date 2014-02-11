<?php

require_once('include/manage.frame.inc.php');
require_once('include/database.inc.php');
require_once('include/msg.inc.php');

if( !isset($_COOKIE['username']) ){
    header("location: login.php");
}

class WeconfigPage extends ManageFrame{
    private $table_content;
    private $group;
    public function __construct($title = "公众平台设置 - Christy微信公众平台管理系统"){
        parent::__construct($title);
        $this->content = file_get_contents('template/weconfig.htm');
        $this->table_content = "";
        $aDatabase = new Database();
        $groupres = $aDatabase->get_result("SELECT `group` FROM `user` WHERE `username`='".$_COOKIE['username']."'");
        $grouparr = mysql_fetch_row($groupres);
        $this->group = $grouparr[0];
    }
    
    public function addItem($id,$name,$account,$fansnum){
        $this->table_content .= "<tr><td>$id</td><td>$name</td><td>$account</td><td>$fansnum</td><td><a href=\"account.php?wid=$id\">查看</a>";
        if($this->group=='admin')
            $this->table_content .= "&nbsp;&nbsp;<a href=\"weconfig.php?action=del&wid=$id\">删除</a></td>";
    }
    
    public function display_page(){
        $this->content = str_replace('{template_contents}',$this->table_content,$this->content);
        
        if($this->group=='admin'){
            $addcontent = file_get_contents('template/weconfigadd.htm');
            $this->content .= $addcontent;
        }
        echo $this->header.$this->content.$this->footer;
    }
    
}

if($_GET['action']=='del'){
    $aDatabase = new Database();
    $groupres = $aDatabase->get_result("SELECT `group` FROM `user` WHERE `username`='".$_COOKIE['username']."'");
    $grouparr = mysql_fetch_row($groupres);
    if($grouparr[0]!='admin'){
        $aMsgPage = new MsgFrame();
        $aMsgPage->display_page("对不起，您的权限不足，不可以执行删除操作！","weconfig.php");
        exit();
    }
    $aDatabase->get_result("DELETE FROM `weconfig` WHERE `wid`=".$_GET['wid']);
    header("location: weconfig.php");
} else if($_GET['action']=='add'){
    $aDatabase = new Database();
    $token = sha1($_POST['wechat_id']);
    $token = substr($token,0,6);
    $aDatabase->get_result("INSERT INTO `weconfig` (`name`,`wechat_id`,`welcome_msg`,`token`) VALUES('".$_POST['name']."','".$_POST['wechat_id']."','".addslashes($_POST['welcome_msg'])."','".$token."')");
    header("location: weconfig.php");
}

$aWeconfigPage = new WeconfigPage();
$aDatabase = new Database();
$adminResult = $aDatabase->get_result("SELECT `admin` FROM `user` WHERE `username`='".$_COOKIE['username']."';");
$adminArr = mysql_fetch_row($adminResult);
$CtrlArr = explode(',',$adminArr[0]);
foreach( $CtrlArr as $wid ){
    $cmd = "SELECT * FROM `weconfig` WHERE `wid`=$wid;";
    if($wid==0)
        $cmd = "SELECT * FROM `weconfig`;";
    $result = $aDatabase->get_result($cmd);
    while( $arr = mysql_fetch_array($result) ){
        $aWeconfigPage->addItem($arr['wid'],$arr['name'],$arr['wechat_id'],$arr['fans_num']);
    }
}

$aWeconfigPage->display_page();

?>