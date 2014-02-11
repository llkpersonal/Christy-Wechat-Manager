<?php

require_once(dirname(__FILE__).'/msg.php');
require_once(dirname(__FILE__).'/../include/database.inc.php');

class TextMessage extends Message{
    private $content;
    public function __construct($event,$content){
        parent::__construct($event);
        $this->content = $content;
        $this->setXmlStr();
        $this->msgType="text";
    }
    
    public function setXmlStr(){
        $this->xmlstr = file_get_contents(dirname(__FILE__).'/../xml/textmsg.xml');
    }
    
    public function response(){
        $resultstr = sprintf($this->xmlstr,$this->toUserName,$this->fromUserName,$this->createTime,$this->msgType,$this->content);
        echo $resultstr;
    }
}

function get_text_content($rid){
    $aDatabase = new Database();
    $result = $aDatabase->get_result("SELECT `content` FROM `textreply` WHERE `rid`=$rid;");
    $row = mysql_fetch_row($result);
    return stripslashes($row[0]);
}

?>