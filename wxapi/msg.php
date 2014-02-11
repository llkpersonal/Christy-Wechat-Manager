<?php

class Message{
    protected $toUserName;
    protected $fromUserName;
    protected $createTime;
    protected $msgType;
    protected $xmlstr;
    public function __construct($event){
        $this->toUserName = $event->fromUserName;
        $this->fromUserName = $event->toUserName;
        $this->createTime = time();
    }
    public function response(){
        echo $this->xmlstr;
    }
}

?>