<?php

require_once(dirname(__FILE__).'/../include/database.inc.php');
//require_once('include/database.inc.php');
class MsgIdx{
    private $mid;
    private $wid;
    private $keyword;
    private $type;
    private $rid;
    
    public function __get($valname){
        return $this->$valname;
    }
    
    public function __set($valname,$val){
        $this->$valname = $val;
    }
}

function fetch_accurate_msgidx($event,$keyword){
    $aDatabase = new Database();
    $aMsgIdx = new MsgIdx();
    $result = $aDatabase->get_result("SELECT * FROM `msgindexes` WHERE `wid`='".$event->wid."' AND `keyword`='$keyword';");
    if($row = mysql_fetch_array($result)){
        $aMsgIdx->mid = $row['mid'];
        $aMsgIdx->wid = $row['wid'];
        $aMsgIdx->keyword = $row['keyword'];
        $aMsgIdx->type = $row['type'];
        $aMsgIdx->rid = $row['rid'];
        return $aMsgIdx;
    } else {
        return false;
    }    
}

function fetch_blur_msgidx($event,$keyword){
    $aDatabase = new Database();
    $aMsgIdx = new MsgIdx();
    $result = $aDatabase->get_result("SELECT * FROM `msgindexes` WHERE `wid`='".$event->wid."';");
    while($row = mysql_fetch_array($result)){
        $preg = str_replace('*','.*',$row['keyword']);
        $preg = '/^'.$preg.'$/';
        if(preg_match($preg,$keyword)){
            $aMsgIdx->mid = $row['mid'];
            $aMsgIdx->wid = $row['wid'];
            $aMsgIdx->keyword = $row['keyword'];
            $aMsgIdx->type = $row['type'];
            $aMsgIdx->rid = $row['rid'];
            return $aMsgIdx;
        }        
    }
    return false;
}

?>