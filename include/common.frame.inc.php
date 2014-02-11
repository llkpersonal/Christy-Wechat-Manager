<?php

/*
 * file: common.inc.php:: Common Framework of web application
 * author: Lingkun Li
 * date: 25th Jan 2014
 */

require_once('database.inc.php');

class CommonFrame{
    private $header;
    private $footer;
    protected $content;
    public function __construct( $title="欢迎使用Christy微信公众平台管理系统" ){
        $this->header = file_get_contents("template/common.header.htm");
        $this->footer = file_get_contents("template/common.footer.htm");
        $this->header = str_replace('{template_title}',$title,$this->header);
        if(isset($_COOKIE['username'])){
            $this->header = str_replace("{template_login}","<li id=\"login\"><a href=\"logout.php\">退出 ".$_COOKIE['username']."</a></li>",$this->header);
        }else{
            $this->header = str_replace("{template_login}","<li id=\"login\"><a href=\"login.php\">登录系统</a></li>",$this->header);
        }
        $strlink = "";
        $aDatabase = new Database();
        $linkResult = $aDatabase->get_result("SELECT * FROM `links` ORDER BY `secquence`;");
        while( $linkArr = mysql_fetch_array($linkResult) ){
            if( $linkArr['display']=='yes' )
                $strlink .= "<li><a target=\"_blank\" href=\"".$linkArr['link']."\">".$linkArr['name']."</a></li>";
        }
        $this->header = str_replace('{template_links}',$strlink,$this->header);
    }
    
    public function display_page($content=""){
        $this->content = $content;
        echo $this->header.$this->content.$this->footer;
    }
}

?>