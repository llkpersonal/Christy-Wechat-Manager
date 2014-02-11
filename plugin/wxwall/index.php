<?php

require_once(dirname(__FILE__).'/../plugin.inc.php');
require_once(dirname(__FILE__).'/../plugin.frame.inc.php');

$xmlstr = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<weixinwall>
    <needexamine>%s</needexamine>
</weixinwall>
XML;

class WeixinWallFrame extends PluginFrame{
    public function __construct($title="微信墙设置"){
        parent::__construct($title);
        $this->content = file_get_contents('page.htm');
        global $xmlstr;
        if(!file_exists('config/'.$_GET['wid'].'.xml')){
            $resultstr = sprintf($xmlstr,'no');
            file_put_contents('config/'.$_GET['wid'].'.xml',$resultstr);
            $this->content = str_replace('{template_no}','checked',$this->content);
            $this->content = str_replace('{template_yes}','',$this->content);
        } else {
            $xml_obj = simplexml_load_file(dirname(__FILE__).'/config/'.$_GET['wid'].'.xml');
            if($xml_obj->needexamine=='yes'){
                $this->content = str_replace('{template_yes}','checked',$this->content);
                $this->content = str_replace('{template_no}','',$this->content);
            } else {
                $this->content = str_replace('{template_no}','checked',$this->content);
                $this->content = str_replace('{template_yes}','',$this->content);
            }
        }
        $aDatabase = new Database();
        $wid = $_GET['wid'];
        $result = $aDatabase->get_result("SELECT * FROM `wxwall_msg` WHERE `wid`='$wid' AND `num`='0' ORDER BY `mid`;");
        $table_contents = "";
        $number = 1;
        while($array = mysql_fetch_array($result)){
            $table_contents .= '<tr><td><input type="hidden" name="exam'.$number.'" value="'.$array['mid'].'" />';
            $table_contents .= $array['nickname'].'</td><td>'.$array['content'].'</td><td><a href="index.php?action=up&&mid='.$array['mid'].'&wid='.$wid.'">上墙</a>';
            $table_contents .= '&nbsp;&nbsp;<a href="index.php?action=del&mid='.$array['mid'].'&wid='.$wid.'">删除</a></td></tr>';
            $number++;
        }
        $this->content = str_replace('{template_numbers}',$number-1,$this->content);
        $this->content = str_replace('{template_contents}',$table_contents,$this->content);
        $this->content = str_replace('{template_wid}',$wid,$this->content);
        
        $featurelink = '<li><a href="?wid='.$_GET['wid'].'">插件设置</a></li>';
        $featurelink .= '<li><a href="?action=delall&wid='.$wid.'">清空微信墙</a></li>';
        $this->header = str_replace('{template_featurelink}',$featurelink,$this->header);
    }
}

if($_GET['action']=='config'){
    $needexamine = $_POST['needexamine'];
    $resultstr = sprintf($xmlstr,$needexamine);
    file_put_contents('config/'.$_GET['wid'].'.xml',$resultstr);
    header("location: index.php?wid=".$_GET['wid']);
} else if($_GET['action']=='up'){
    $aDatabase = new Database();
    $result = $aDatabase->get_result("SELECT MAX(`num`) FROM `wxwall_msg` WHERE `wid`='".$_GET['wid']."'");
    $array = mysql_fetch_row($result);
    if($array[0]==0){
        $aDatabase->get_result("UPDATE `wxwall_msg` SET `num`='1' WHERE `mid`=".$_GET['mid']);
    } else {
        $newnum = $array[0]+1;
        $aDatabase->get_result("UPDATE `wxwall_msg` SET `num`='$newnum' WHERE `mid`=".$_GET['mid']);
    }
    header("location: index.php?wid=".$_GET['wid']);
} else if($_GET['action']=='del'){
    $aDatabase = new Database();
    $aDatabase->get_result("DELETE FROM `wxwall_msg` WHERE `mid`=".$_GET['mid']);
    header("location: index.php?wid=".$_GET['wid']);
} else if($_GET['action']=='all'){
    $numbers = $_POST['numbers'];
    $aDatabase = new Database();
    $wid = $_GET['wid'];
    $result = $aDatabase->get_result("SELECT MAX(`num`) FROM `wxwall_msg` WHERE `wid`='$wid'");
    $array = mysql_fetch_row($result);
    $lastid = $array[0];
    for($i=1;$i<=$numbers;$i++){
        $newnum = $lastid+$i;
        $mid = $_POST["exam$i"];
        $aDatabase->get_result("UPDATE `wxwall_msg` SET `num`='$newnum' WHERE `mid`=$mid");
    }
    header("location: index.php?wid=".$_GET['wid']);
} else if($_GET['action']=='delall'){
    $aDatabase = new Database();
    $wid = $_GET['wid'];
    $aDatabase->get_result("DELETE FROM `wxwall_msg` WHERE `wid`=".$_GET['wid']);
    header("location: index.php?wid=".$_GET['wid']);
}

$aWeixinWallFrame = new WeixinWallFrame();
$aWeixinWallFrame->display_page();

?>