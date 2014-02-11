<?php

require_once(dirname(__FILE__).'/include/extend.frame.inc.php');
require_once(dirname(__FILE__).'/include/database.inc.php');
require_once(dirname(__FILE__).'/include/msg.inc.php');

if(!isset($_COOKIE['username'])){
    header("location: login.php");
}

class InstallPluginFrame extends ExtendFrame{
    public function __construct($title="安装插件 - Christy微信公众平台管理系统"){
        parent::__construct($title);
        $this->content = file_get_contents(dirname(__FILE__).'/template/installplugin.htm');
        $this->get_adminfor_toselect();
    }
}

if($_GET['action']=='install'){
    $wid = $_GET['wid'];
    $folder = $_GET['folder'];
    $obj = simplexml_load_file('plugin/'.$folder.'/config.xml');
    $aDatabase = new Database();
    $aDatabase->get_result("INSERT INTO `plugin` (`pid`,`name`,`version`,`folder`,`protrol`,`keyword`,`hasconfigpage`,`wid`) VALUES (NULL,'$obj->name','$obj->version','$folder','$obj->protrol','$obj->keyword','$obj->hasconfigpage','$wid')");
    $aMsgFrame = new MsgFrame();
    $aMsgFrame->display_page("插件安装完毕","installplugin.php");
}

$aInstallPluginPage = new InstallPluginFrame();
$aInstallPluginPage->display_page();
    
?>