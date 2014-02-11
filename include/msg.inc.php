<?php

require_once('common.frame.inc.php');

class MsgFrame extends CommonFrame{
    public function __construct($title = "提示信息 - Christy微信公众平台管理系统"){
        parent::__construct($title);
        $this->content = file_get_contents('template/msg.htm');
    }
    
    public function display_page($message="",$link="#fakelink"){
        $this->content = str_replace('{template_message}',$message,$this->content);
        $this->content = str_replace('{template_link}',$link,$this->content);
        parent::display_page($this->content);
        header("refresh:2;url=$link");
    }
}

?>