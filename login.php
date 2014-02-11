<?php

require_once('include/common.frame.inc.php');
require_once('include/database.inc.php');

class LoginPage extends CommonFrame{
    public function __construct($title="登录 - Christy微信公众平台管理系统"){
        parent::__construct($title);
        $this->content = file_get_contents('template/login.htm');
    }
    public function display_page($message){
        $this->content = str_replace('{template_tips}',$message,$this->content);
        parent::display_page($this->content);
    }
}

$LOGINPAGE = new LoginPage();

if( $_GET['action']=='login' ){
    $username = $_POST['username'];
    $password = $_POST['password'];
    $mydatabase = new Database();
    $result = $mydatabase->get_result("SELECT COUNT(*) FROM `user` WHERE `username`='$username' AND `password`=md5('$password');");
    
    $row = mysql_fetch_row($result);
    if( $row[0]>0 ){
        setcookie("username",$username);
        header("location: index.php");
    } else {
        $LOGINPAGE->display_page('对不起，您的用户名或密码输入错误，请重新输入！');
        exit();
    }
}


$LOGINPAGE->display_page('要开始，请先登录！');

?>