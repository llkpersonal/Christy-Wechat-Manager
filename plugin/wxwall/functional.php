<?php

function addUser($wid,$openid){
    $aDatabase = new Database();
    $result = $aDatabase->get_result("INSERT INTO `wxwall_user` (`uid`,`wid`,`openid`) VALUES (NULL,'$wid','$openid');");
    $uid = mysql_insert_id();
    return $uid;
}

function hasOpenid($wid,$openid){
    $aDatabase = new Database();
    $result = $aDatabase->get_result("SELECT COUNT(*) FROM `wxwall_user` WHERE `wid`='$wid' AND `openid`='$openid';");
    $row = mysql_fetch_row($result);
    if($row[0]>0) return true;
    else return false;
}

?>