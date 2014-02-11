<?php

require_once('include/manage.frame.inc.php');
require_once('include/database.inc.php');
require_once('include/msg.inc.php');

if( !isset($_COOKIE['username']) ){
    header( "location: login.php" );
} else {
    $aDatabase = new Database();
    $result = $aDatabase->get_result("SELECT `group` FROM `user` WHERE `username`='".$_COOKIE['username']."';");
    $row = mysql_fetch_row($result);
    if( $row[0] != 'admin' ){
        $aMsgPage = new MsgFrame();
        $aMsgPage->display_page("对不起，您所在的用户组不支持该功能！","weconfig.php");
        exit();
    }
    unset($aDatabase);
}

class LinksPage extends ManageFrame{
    public function __construct($title = '友情链接设置 - Chirsty微信公众平台管理系统'){
        parent::__construct($title);
        $this->content = file_get_contents('template/links.htm');
        
        // get links information
        $tablecontent = "";
        $aDatabase = new Database();
        $linksinfor = $aDatabase->get_result("SELECT * FROM `links`;");
        
        $linkNum = 1;
        while( $linksArr = mysql_fetch_array($linksinfor) ){
            $iLid = $linksArr['lid'];
            $tablecontent .= "<tr>
                        <td><input type=\"hidden\" name=\"lid$linkNum\" value=\"$iLid\"/>$iLid</td>
                        <td><input type=\"text\" name=\"link$linkNum\" value=\"".$linksArr['link']."\" name= /></td>
                        <td><input type=\"text\" name=\"name$linkNum\" value=\"".$linksArr['name']."\" /></td>";
            if( $linksArr['display']=='yes' )
                $tablecontent .= "<td><input type=\"checkbox\" name=\"dis$linkNum"."[]"."\" value=\"1\" checked /></td>";
            else
                $tablecontent .= "<td><input type=\"checkbox\" name=\"dis$linkNum"."[]"."\" value=\"1\" /></td>";
            $tablecontent.="<td><input type=\"text\" name=\"sec$linkNum\" value=\"".$linksArr['secquence']."\" style=\"width:20px;\"/></td>
                            <td><a href=\"links.php?action=del&lid=$iLid\">删除</a></td>
                            </tr>";
            $linkNum++;
        }
        $linkNum--;
        $this->content = str_replace('{template_number}',$linkNum,$this->content);
        $this->content = str_replace('{template_contents}',$tablecontent,$this->content);
    }
    
    public function display_page($tips){
        $this->content = str_replace('{template_tips}',$tips,$this->content);
        echo $this->header.$this->content.$this->footer;
    }
}

$tips = "您可以在此修改友情链接相关信息";
if( $_GET['action']=='manage' ){
    $aDatabase = new Database();
    $numOfItems = $_POST["numOfItems"];
    for($i=1;$i<=$numOfItems;$i++){
        $cmd = "UPDATE `links` SET `link`='".$_POST["link$i"]."',`name`='".$_POST["name$i"]."',`secquence`='".$_POST["sec$i"]."'";
        $arrChecked = $_POST["dis$i"];
        if( $arrChecked[0]==1 )
            $cmd .= ",`display`='yes'";
        else
            $cmd .= ",`display`='no'";
        $cmd .= " WHERE `lid`=".$_POST["lid$i"];
        $aDatabase->get_result($cmd);
    }
    $tips = "友情链接修改完成";
} else if( $_GET['action']=='add' ){
    $aDatabase = new Database();
    $aDatabase->get_result("INSERT INTO `links` (`link`,`name`,`display`,`secquence`) VALUES ('".$_POST['link']."','".$_POST['name']."','yes','".$_POST['secquence']."');");
} else if( $_GET['action']=='del' ){
    $aDatabase = new Database();
    $aDatabase->get_result("DELETE FROM `links` WHERE `lid` = ".$_GET['lid'].";");
}



$aLinksPage = new LinksPage();
$aLinksPage->display_page($tips);

?>