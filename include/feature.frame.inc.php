<?php

/*
 * file: feature.frame.inc.php:: the frame page of the feature
 * author: Lingkun Li
 * date: 26th.Jan.2014
 */

class FeatureFrame{
    protected $header;
    protected $footer;
    protected $content;
    
    public function __construct($title = '欢迎访问Christy微信公众平台管理系统'){
        $this->header = file_get_contents(dirname(__FILE__).'/../template/feature.header.htm');
        $this->header = str_replace('{template_title}',$title,$this->header);
        if(isset($_COOKIE['username'])){
            $this->header = str_replace("{template_login}","<li id=\"login\"><a href=\"logout.php\">退出 ".$_COOKIE['username']."</a></li>",$this->header);
        }else{
            $this->header = str_replace("{template_login}","<li id=\"login\"><a href=\"login.php\">登录系统</a></li>",$this->header);
        }
        $this->footer = file_get_contents(dirname(__FILE__).'/../template/common.footer.htm');
        
        $this->content = "";
    }
    
    public function display_page(){
        echo $this->header.$this->content.$this->footer;
    }
}



?>