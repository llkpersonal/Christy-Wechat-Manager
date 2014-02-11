<?php

require_once(dirname(__FILE__).'/../include/database.inc.php');

class event{
    protected $toUserName;
    protected $fromUserName;
    protected $createTime;
    protected $msgType;
    protected $event;
    protected $signature;
    protected $timestamp;
    protected $echoStr;
    protected $content;
    protected $msgId;
    protected $nonce;
    protected $wid;
    protected $token;
    protected $location_x,$location_y;
    protected $scale;
    protected $label;
        
    public function __construct($wid){
        $this->wid = $wid;
        $this->getToken($wid);
        $this->signature = $_GET['signature'];
        $this->timestamp = $_GET['timestamp'];
        $this->echoStr = $_GET['echostr'];
        $this->nonce = $_GET['nonce'];
        
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if(!empty($postStr)){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $this->toUserName = $postObj->ToUserName;
            $this->fromUserName = $postObj->FromUserName;
            $this->createTime = $postObj->CreateTime;
            $this->msgType = $postObj->MsgType;
            $this->event = $postObj->Event;
            $this->msgId = $postObj->MsgId;
            $this->content = $postObj->Content;
            $this->location_x = $postObj->Location_X;
            $this->location_y = $postObj->Location_Y;
            $this->scale = $postObj->Scale;
            $this->label = $postObj->Label;
        }
    }
    
    public function __get($valName){
        return $this->$valName;
    }
    
    private function getToken($wid){
        $aDatabase = new Database();
        $result = $aDatabase->get_result("SELECT `token` FROM `weconfig` WHERE `wid`=$wid;");
        $array = mysql_fetch_row($result);
        $this->token = $array[0];
        unset($aDatabase);
        return $this->token;
    }
    
    public function checkSignature(){
        $tmpArr = array($this->token,$this->timestamp,$this->nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if($tmpStr == $this->signature){
            return true;
        } else {
            return false;
        }
    }
}    

?>