<?php

require_once(dirname(__FILE__).'/feature.frame.inc.php');
require_once(dirname(__FILE__).'/database.inc.php');

class ExtendFrame extends FeatureFrame{
    public function __construct($title = '扩展功能 - Chirsty微信公众平台管理系统'){
        parent::__construct($title);
        $featurelinks = "";
        $featurelinks .= "<li><a href=\"plugin.php\">插件管理</a></li>";
        $featurelinks .= "<li><a href=\"installplugin.php\">安装插件</a></li>";
        $this->header = str_replace("{template_featurelink}",$featurelinks,$this->header);
    }
    
    public function get_adminfor_toselect(){
        $selectoptions = "";
        $aDatabase = new Database();
        $resAdminfor = $aDatabase->get_result("SELECT `admin` FROM `user` WHERE `username`='".$_COOKIE['username']."';");
        $rowAdminfor = mysql_fetch_row($resAdminfor);
        $cmd = "SELECT * FROM `weconfig` WHERE `wid` IN (".$rowAdminfor[0].");";
        if($rowAdminfor[0]==0){
            $cmd = "SELECT * FROM `weconfig`;";
        }
        $resOption = $aDatabase->get_result($cmd);
        $n = 0;
        while($arrayOption = mysql_fetch_array($resOption)){
            if($n==0){
                $this->content = str_replace('{template_default}',$arrayOption['wid'],$this->content); 
                $selectoptions .= "<option value=\"".$arrayOption['wid']."\" selected>".$arrayOption['name']."</option>";
            }
            else
                $selectoptions .= "<option value=\"".$arrayOption['wid']."\">".$arrayOption['name']."</option>";
            $n++;
        }
        
        $this->content = str_replace('{template_options}',$selectoptions,$this->content);
    }
}


?>