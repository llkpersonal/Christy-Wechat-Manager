<?php

/*
 * file: reply.frame.inc.php:: the reply page frame
 * author: Lingkun Li
 * date: 27th.Jan.2014
 */

require_once(dirname(__FILE__).'/feature.frame.inc.php');
require_once(dirname(__FILE__).'/database.inc.php');

class ReplyFrame extends FeatureFrame{
    public function __construct($title = '消息管理 - Chirsty微信公众平台管理系统'){
        parent::__construct($title);
        $featurelinks = "";
        $featurelinks .= "<li><a href=\"checkreply.php\">消息回复管理</a></li>";
        $featurelinks .= "<li><a href=\"textreply.php\">添加文本回复</a></li>";
        $featurelinks .= "<li><a href=\"pictextreply.php\">添加图文回复</a></li>";
        $featurelinks .= "<li><a href=\"musicreply.php\">添加音乐回复</a></li>";
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
            if($n==0)
                $selectoptions .= "<option value=\"".$arrayOption['wid']."\" selected>".$arrayOption['name']."</option>";
            else
                $selectoptions .= "<option value=\"".$arrayOption['wid']."\">".$arrayOption['name']."</option>";
            $n++;
        }
        
        $this->content = str_replace('{template_options}',$selectoptions,$this->content);
    }
}

?>