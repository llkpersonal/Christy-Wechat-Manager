<?php

require_once(dirname(__FILE__).'/include/reply.frame.inc.php');
require_once(dirname(__FILE__).'/include/database.inc.php');
require_once(dirname(__FILE__).'/include/msg.inc.php');

if(!isset($_COOKIE['username'])){
    header("location: login.php");
}

class PicTextReplyFrame extends ReplyFrame{
    private $table_content;
    private $table_item_number;
    public function __construct($title="图文回复管理 - Christy微信公众平台管理系统"){
        parent::__construct($title);
        if(isset($_GET['mid'])){
            $mid = $_GET['mid'];
            $this->content = file_get_contents(dirname(__FILE__).'/template/pictextreplymod.htm');
            $aDatabase = new Database();
            $result = mysql_fetch_array($aDatabase->get_result("SELECT * FROM `msgindexes` WHERE `mid`=$mid;"));
            $this->content = str_replace('{template_keyword}',$result['keyword'],$this->content);
            $this->content = str_replace('{template_mid}',$mid,$this->content);
            $rid = $result['rid'];
            $this->content = str_replace('{template_rid}',$result['rid'],$this->content);
            $result = $aDatabase->get_result("SELECT * FROM `pictextreply` WHERE `rid`=$rid;");
            $iNum = 1;
            while( $array = mysql_fetch_array($result) ){
                $pid = $array['pid'];
                $title = $array['title'];
                $description = $array['description'];
                $picurl = $array['picurl'];
                $url = $array['url'];
                $secquence = $array['secquence'];
                $this->table_content .= "<tr>
                <td><input type=\"hidden\" name=\"pid$iNum\" value=\"$pid\"/> <input type=\"text\" name=\"sec$iNum\" value=\"$secquence\" style=\"width:90%;\"/></td>
                <td><input type=\"text\" name=\"tit$iNum\" value=\"$title\" style=\"width:90%;\"/></td>
                <td><input type=\"text\" name=\"des$iNum\" value=\"$description\" style=\"width:90%;\"/></td>
                <td><input type=\"text\" name=\"pic$iNum\" value=\"$picurl\" style=\"width:90%;\"/></td>
                <td><input type=\"text\" name=\"url$iNum\" value=\"$url\" style=\"width:90%;\"/></td>
                <td><a href=\"pictextreply.php?action=del&mid=$mid&pid=$pid\">删除</a></td>
                </tr>";
                $iNum++;
            }
            $this->table_item_number = $iNum-1;
            $this->content = str_replace('{template_contents}',$this->table_content,$this->content);
            $this->content = str_replace('{template_number}',$this->table_item_number,$this->content);
        } else {
            $this->content = file_get_contents(dirname(__FILE__).'/template/pictextreply.htm');
            $this->get_adminfor_toselect();
        }
    }
}

if($_GET['action']=='modify'){
    $aDatabase = new Database();
    $mid = $_POST['mid'];
    $keyword = $_POST['keyword'];
    $aDatabase->get_result("UPDATE `msgindexes` SET `keyword`='$keyword' WHERE `mid`='$mid';");
    $numOfItems = $_POST['numOfItems'];
    for($i=1;$i<=$numOfItems;$i++){
        $pid = $_POST["pid$i"];
        $secquence = $_POST["sec$i"];
        $title = $_POST["tit$i"];
        $description = $_POST["des$i"];
        $picurl = $_POST["pic$i"];
        $url = $_POST["url$i"];
        $aDatabase->get_result("UPDATE `pictextreply` SET `title`='$title',`description`='$description',`picurl`='$picurl',`url`='$url',`secquence`='$secquence' WHERE `pid`='$pid';");
    }
    $aMsgFrame = new MsgFrame();
    $aMsgFrame->display_page("图文信息修改成功！",'checkreply.php');
} else if ( $_GET['action']=='additem' ) {
    $aDatabase = new Database();
    $rid = $_POST['rid'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $picurl = $_POST['picurl'];
    $url = $_POST['url'];
    $secquence = $_POST['secquence'];
    $aDatabase->get_result("INSERT INTO `pictextreply` (`pid`,`rid`,`title`,`description`,`picurl`,`url`,`secquence`) VALUES (NULL,'$rid','$title','$description','$picurl','$url','$secquence');");
    $aMsgFrame = new MsgFrame();
    $aMsgFrame->display_page("图文信息项目添加成功！",'pictextreply.php?mid='.$_POST['mid']);
} else if ( $_GET['action']=='del' ) {
    $aDatabase = new Database();
    $pid = $_GET['pid'];
    $aDatabase->get_result("DELETE FROM `pictextreply` WHERE `pid`='$pid';");
    $aMsgFrame = new MsgFrame();
    $aMsgFrame->display_page("图文信息项目删除成功！",'pictextreply.php?mid='.$_GET['mid']);
} else if ( $_GET['action']=='add') {
    $keyword = $_POST['keyword'];
    $wid = $_POST['platid'];
    $aDatabase = new Database();
    $result = $aDatabase->get_result("INSERT INTO `msgindexes` (`mid`,`wid`,`keyword`,`type`) VALUES (NULL,'$wid','$keyword','pictext');");
    $rid = mysql_insert_id();
    $result = $aDatabase->get_result("UPDATE `msgindexes` SET `rid`='$rid' WHERE `mid`='$rid';");
    $aMsgFrame = new MsgFrame();
    $aMsgFrame->display_page("图文信息添加成功,接下来将进入条目编辑页面！",'pictextreply.php?mid='.$rid);
}


$aPicTextReplyFrame = new PicTextReplyFrame();
$aPicTextReplyFrame->display_page();

?>