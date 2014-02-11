<?php

require_once(dirname(__FILE__).'/msg.php');
require_once(dirname(__FILE__).'/../include/database.inc.php');

class PicTextMessage extends Message{
    private $items;
    private $tmp_items;
    private $items_num;
    public function __construct($event){
        parent::__construct($event);
        $this->msgType="news";
        $this->tmp_items = file_get_contents(dirname(__FILE__).'/../xml/pictextitem.xml');
        $this->items_num = 0;
        $this->xmlstr = file_get_contents(dirname(__FILE__).'/../xml/pictextmsg.xml');
    }
    
    public function add_item($title,$description,$picurl,$url){
        if($items_num>=10) return;
        $this->items .= sprintf($this->tmp_items,$title,$description,$picurl,$url);
        $this->items_num++;
    }
    
    public function response(){
        $resultstr = sprintf($this->xmlstr,$this->toUserName,$this->fromUserName,$this->createTime,$this->msgType,$this->items_num,$this->items);
        echo $resultstr;
    }
}

function fetch_pictextmsg_by_rid($event,$rid){
    $aPicTextMsg = new PicTextMessage($event);
    $aDatabase = new Database();
    $result = $aDatabase->get_result("SELECT * FROM `pictextreply` WHERE `rid`=$rid ORDER BY `secquence`;");
    while( $array = mysql_fetch_array($result) ){
        $aPicTextMsg->add_item($array['title'],$array['description'],$array['picurl'],$array['url']);
    }
    return $aPicTextMsg;
}



?>