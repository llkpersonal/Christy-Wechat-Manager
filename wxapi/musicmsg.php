<?php

require_once(dirname(__FILE__).'/msg.php');
require_once(dirname(__FILE__).'/../include/database.inc.php');

class MusicMessage extends Message{
    private $title;
    private $description;
    private $musicurl;
    private $hqurl;
    
    public function __construct($event,$title,$description,$musicurl,$hqurl){
        parent::__construct($event);
        $this->xmlstr = file_get_contents(dirname(__FILE__).'/../xml/music.xml');
        $this->title = $title;
        $this->description = $description;
        $this->musicurl = $musicurl;
        $this->hqurl = $hqurl;
        $this->msgType = "music";
    }
    
    public function response(){
        $resultstr = sprintf($this->xmlstr,$this->toUserName,$this->fromUserName,$this->createTime,$this->msgType,
                            $this->title,$this->description,$this->musicurl,$this->hqurl);
        echo $resultstr;
    }
}

function fetch_musicmsg_by_rid($event,$rid){
    $aDatabase = new Database();
    $result = $aDatabase->get_result("SELECT * FROM `musicreply` WHERE `rid`=$rid;");
    $array = mysql_fetch_array($result);
    $aMusicMessage = new MusicMessage($event,$array['title'],$array['description'],$array['musicurl'],$array['hqurl']);
    return $aMusicMessage;
}

?>