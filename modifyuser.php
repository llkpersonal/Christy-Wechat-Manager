<?php

require_once('include/manage.frame.inc.php');
require_once('include/msg.inc.php');
require_once('include/database.inc.php');

$aMsgFrame = new MsgFrame();

if( !isset($_COOKIE['username']) ){
    header("location :login.php");
} else if (!isAdmin()){
    $aMsgFrame->display_page('权限不够！','weconfig.php');
    exit();
}

class ModifyFrame extends ManageFrame{
    public function __construct($title = "编辑用户 - Christy微信公众平台"){
        parent::__construct($title);
        $this->content = file_get_contents('template/modifyuser.htm');
    }
    
    public function setItems($uid,$username,$adminfor){
        $this->content = str_replace('{template_uid}',$uid,$this->content);
        $this->content = str_replace('{template_username}',$username,$this->content);
        $this->content = str_replace('{template_adminfor}',$adminfor,$this->content);
    }
}

if( $_GET['action']=='update'){
    $uid = $_POST['uid'];
    $aDatabase = new Database();
    $aDatabase->get_result("UPDATE `user` SET `username`='".$_POST['username']."',`admin`='".$_POST['adminfor']."' WHERE `uid`=$uid;");
    if(!empty($_POST['password'])){
        $aDatabase->get_result("UPDATE `user` SET `password`=md5('".$_POST['password']."') WHERE `uid`=$uid;");
    }
    $aMsgFrame->display_page('用户信息修改完成！','users.php');
    exit();
} else if( !isset($_GET['uid']) ){
    $aMsgFrame->display_page('页面错误！','users.php');
    exit();
}


$aModifyFrame = new ModifyFrame();
$aDatabase = new Database();

$uid = $_GET['uid'];
$result = $aDatabase->get_result("SELECT * FROM `user` WHERE `uid`=$uid;");
$array = mysql_fetch_array($result);
$aModifyFrame->setItems($uid,$array['username'],$array['admin']);

$aModifyFrame->display_page();

?>