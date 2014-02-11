<?php

/*
 * file: database.inc.php:: a class to operate database
 * author: Lingkun Li
 * date: 25th Jan 2014
 */

require_once('config.php');

class Database{
    private $m_database;
    public function __construct(){
        global $db_host,$db_name,$db_user,$db_pass;        
        $this->m_database = mysql_connect($db_host,$db_user,$db_pass);        
       
        if( !$this->m_database ){
            die('cannot connect to the database!');
        }
        mysql_select_db($db_name,$this->m_database);
        mysql_query("SET NAMES 'UTF8';",$this->m_database);
    }
    
    public function get_result($commond){
        $res = mysql_query($commond,$this->m_database);        
        return $res;
    }
    
    public function __destruct(){
        mysql_close($this->m_database);
    }
}

function isAdminByUSER($username){
    $aDatabase = new Database();
    $groupres = $aDatabase->get_result("SELECT `group` FROM `user` WHERE `username`='$username'");
    $grouparr = mysql_fetch_row($groupres);
    unset($aDatabase);
    if($grouparr[0]=='admin') return true;
    else return false;
}

function isAdminByUID($uid){
    $aDatabase = new Database();
    $groupres = $aDatabase->get_result("SELECT `group` FROM `user` WHERE `uid`='$uid'");
    $grouparr = mysql_fetch_row($groupres);
    unset($aDatabase);
    if($grouparr[0]=='admin') return true;
    else return false;
}

function isAdmin(){
    return isAdminByUSER($_COOKIE['username']);
}

function getPlatformNameByWid($wid){
    $aDatabase = new Database();
    $Nameres = $aDatabase->get_result("SELECT `name` FROM `weconfig` WHERE `wid`='$wid';");
    $Namearr = mysql_fetch_row($Nameres);
    $res = $Namearr[0];
    unset($aDatabase);
    return $res;
}

function insert_protrol($wid,$wechat_user,$protrol,$handle){
    $aDatabase = new Database();
    $aDatabase->get_result("INSERT INTO `intermediate` (`iid`,`wechat_user`,`wid`,`protrol`,`handle`) VALUES (NULL,'$wechat_user','$wid','$protrol','$handle');");
    unset($aDatabase);
}

function get_protrol($wid,$wechat_user){
    $aDatabase = new Database();
    $result = $aDatabase->get_result("SELECT `protrol` FROM `intermediate` WHERE `wid`='$wid' AND `wechat_user`='$wechat_user';");
    if($row = mysql_fetch_row($result)){
        return $row[0];
    } else {
        return false;
    }    
}

function delete_protrol($wid,$wechat_user,$handle){
    $aDatabase = new Database();
    $aDatabase->get_result("DELETE FROM `intermediate` WHERE `wid`='$wid' AND `wechat_user`='$wechat_user' AND `handle`='$handle';");
    unset($aDatabase);
}

function get_handle_page($wid,$wechat_user){
    $aDatabase = new Database();
    $result = $aDatabase->get_result("SELECT `handle` FROM `intermediate` WHERE `wid`='$wid' AND `wechat_user`='$wechat_user';");
    if($row = mysql_fetch_row($result)){
        return $row[0];
    } else {
        return false;
    }
}

function get_before_plugin($wid,$keyword){
    $aDatabase = new Database();
    $result = $aDatabase->get_result("SELECT `folder` FROM `plugin` WHERE `wid`='$wid' AND `keyword`='$keyword';");
    if($row = mysql_fetch_row($result)){
        return $row[0];
    } else {
        return false;
    }
}
?>