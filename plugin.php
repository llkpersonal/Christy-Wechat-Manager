<?php

require_once(dirname(__FILE__).'/include/extend.frame.inc.php');
require_once(dirname(__FILE__).'/include/database.inc.php');
require_once(dirname(__FILE__).'/include/msg.inc.php');

if(!isset($_COOKIE['username'])){
    header("location: login.php");
}

class PluginFrame extends ExtendFrame{
    public function __construct($title="插件管理 - Christy微信公众平台管理系统"){
        parent::__construct($title);
        $this->content = file_get_contents(dirname(__FILE__).'/template/plugin.htm');
        $this->get_adminfor_toselect();
    }
}

if($_GET['action']=='unins'){
    $pid = $_GET['pid'];
    $aDatabase = new Database();
    $aDatabase->get_result("DELETE FROM `plugin` WHERE `pid`='$pid';");
    $aMsgFrame = new MsgFrame();
    $aMsgFrame->display_page("插件卸载完毕","installplugin.php");
}

$aPluginPage = new PluginFrame();
$aPluginPage->display_page();
    
?>