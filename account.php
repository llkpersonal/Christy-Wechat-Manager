<?php

require_once('include/manage.frame.inc.php');
require_once('include/database.inc.php');
require_once('include/msg.inc.php');

if(!isset($_COOKIE['username'])){
    header("location: login.php");
}

class AccountPage extends ManageFrame{
    public function __construct($title="查看公众平台信息 - Christy微信公众平台管理系统"){
        parent::__construct($title);
        $this->content = file_get_contents('template/account.htm');
    }
    public function fetchInformation(){
        $aDatabase = new Database();
        $result = $aDatabase->get_result("SELECT * FROM `weconfig` WHERE `wid`='".$_GET['wid']."';");
        $arrInfo = mysql_fetch_array($result);
        $this->content = str_replace('{template_wid}',$arrInfo['wid'],$this->content);
        $this->content = str_replace('{template_name}',$arrInfo['name'],$this->content);
        $this->content = str_replace('{template_wechat_id}',$arrInfo['wechat_id'],$this->content);
        $this->content = str_replace('{template_welcomemsg}',stripslashes($arrInfo['welcome_msg']),$this->content);
        $this->content = str_replace('{template_url}',"http://".$_SERVER['HTTP_HOST']."/ai.php?wid=".$arrInfo['wid'],$this->content);
        $this->content = str_replace('{template_token}',$arrInfo['token'],$this->content);
        $this->content = str_replace('{template_email}',$arrInfo['email'],$this->content);
    }
}

if($_GET['action']=='update'){
    $aDatabase = new Database();
    $wid = $_POST['wid'];
    $name = $_POST['name'];
    $wechat_id = $_POST['wechat_id'];
    $email = $_POST['email'];
    $welcome_msg = addslashes($_POST['welcome_msg']);
    $aDatabase->get_result("UPDATE `weconfig` SET `name`='$name',`wechat_id`='$wechat_id',`welcome_msg`='$welcome_msg',`email`='$email' WHERE `wid`=$wid;");
    if($_POST['password']!=''){
        $password = $_POST['password'];
        $aDatabase->get_result("UPDATE `weconfig` SET `password`=md5('$password') WHERE `wid`=$wid;");
    }
    $msgPage = new MsgFrame();
    $msgPage->display_page("公众账号信息修改成功！","weconfig.php");
    exit();
} else if (!isset($_GET['wid'])){
    $msgPage = new MsgFrame();
    $msgPage->display_page("页面错误！","weconfig.php");
    exit();
}

$aAccountPage = new AccountPage();
$aAccountPage->fetchInformation();
$aAccountPage->display_page();

?>