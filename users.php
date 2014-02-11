<?php

require_once('include/manage.frame.inc.php');
require_once('include/database.inc.php');
require_once('include/msg.inc.php');

if(!isset($_COOKIE['username'])){
    header("location: login.php");
}

class UsersFrame extends ManageFrame{
    private $table_content;
    public function __construct($title = "用户设置 - Christy微信公众平台管理系统"){
        parent::__construct($title);
        $this->content = file_get_contents('template/usersself.htm');
        if( isAdmin() ){
            $adminuserspage = file_get_contents('template/usersadmin.htm');
            $table_content = "";
            $this->content.=$adminuserspage;
        }
    }
    
    public function addItems($uid,$username,$group,$admin){
        $this->table_content .= "<tr><td>$uid</td><td>$username</td><td>$group</td><td>$admin</td><td><a href=\"modifyuser.php?uid=$uid\">编辑
        </a>&nbsp;&nbsp;<a href=\"users.php?action=del&uid=$uid\">删除</a></td></tr>";
    }
    
    public function display_page(){
        $this->content = str_replace('{template_contents}',$this->table_content,$this->content);
        parent::display_page();
    }
}

if($_GET['action']=='modifykey'){
    $aMsgFrame = new MsgFrame();
    $aDatabase = new Database();
    $result = $aDatabase->get_result("SELECT COUNT(*) FROM `user` WHERE `username`='".$_COOKIE['username']."' AND `password`=md5('".$_POST['oldpass']."');");
    $row = mysql_fetch_row($result);
    if( $row[0]==0 ){
        $aMsgFrame->display_page("您输入的旧密码不正确，请重新输入！","users.php");
        exit();
    } else if( $_POST['newpass']==''){
        $aMsgFrame->display_page("新密码不能为空，请重新输入！","users.php");
        exit();
    } else if( $_POST['newpass']!=$_POST['confirmpass'] ){
        $aMsgFrame->display_page("新密码与确认密码不一致，请重新输入！","users.php");
        exit();
    }
    
    $aDatabase->get_result("UPDATE `user` SET `password`=md5('".$_POST['newpass']."') WHERE `username`='".$_COOKIE['username']."';");
    $aMsgFrame->display_page("密码修改成功，您需要重新登录！","login.php");
    setcookie("username","",time()-3600);
    exit();
} else if ( $_GET['action']=='add' ){
    if(!isAdmin()){
        $aMsgFrame = new MsgFrame();
        $aMsgFrame->display_page('没有权限!','users.php');
        exit();
    }
    if($_POST['username']==''||$_POST['password']==''||$_POST['adminfor']==''){
        $aMsgFrame = new MsgFrame();
        $aMsgFrame->display_page('请将表单填写完整！','users.php');
        exit();
    }
    $aDatabase = new Database();
    $aDatabase->get_result("INSERT INTO `user` (`username`,`password`,`group`,`admin`) VALUES ('".$_POST['username']."',md5('".$_POST['password']."'),'manager','".$_POST['adminfor']."')");
    header('location: users.php');
} else if ($_GET['action']=='del'){
    if(!isAdmin()){
        $aMsgFrame = new MsgFrame();
        $aMsgFrame->display_page('没有权限!','users.php');
        exit();
    } else if ( isAdminByUID($_GET['uid']) ){
        $aMsgFrame = new MsgFrame();
        $aMsgFrame->display_page('不可以删除用户组是admin的账户!','users.php');
        exit();
    }
    $aDatabase = new Database();
    $aDatabase->get_result("DELETE FROM `user` WHERE uid=".$_GET['uid']);
    header('location: users.php');
}

$aUsersFrame = new UsersFrame();
$aDatabase = new Database();
$result = $aDatabase->get_result("SELECT * FROM `user`;");
while($array = mysql_fetch_array($result)){
    $aUsersFrame->addItems($array['uid'],$array['username'],$array['group'],$array['admin']);
}
$aUsersFrame->display_page();

?>