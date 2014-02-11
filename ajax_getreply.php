<?php

require_once('include/database.inc.php');

$aDatabase = new Database();

$wid = $_GET['wid'];

$cmd = "SELECT * FROM `msgindexes` WHERE `wid`=$wid ORDER BY `mid`;";
if($wid==0){
    $resAdminfor = $aDatabase->get_result("SELECT `admin` FROM `user` WHERE `username`='".$_COOKIE['username']."'");
    $rowAdminfor = mysql_fetch_row($resAdminfor);
    if($rowAdminfor[0]==0)
        $cmd = "SELECT * FROM `msgindexes` ORDER BY `mid`;";
    else
        $cmd = "SELECT * FROM `msgindexes` WHERE `wid` IN (".$rowAdminfor[0].") ORDER BY `mid`;";
}

$res = "";

$resMsgindex = $aDatabase->get_result($cmd);
while($arrMsgindex = mysql_fetch_array($resMsgindex)){
    $platformname = getPlatformNameByWid($arrMsgindex['wid']);
    if($arrMsgindex['wid']==0) $platformname='全部';
    $type = "";
    $editurl = "";
    switch($arrMsgindex['type']){
        case "text":$type="文本回复"; $editurl="textreply.php?mid=".$arrMsgindex['mid'];break;
        case "pictext": $type="图文回复"; $editurl="pictextreply.php?mid=".$arrMsgindex['mid']; break;
        case "music": $type="音乐回复"; $editurl="musicreply.php?mid=".$arrMsgindex['mid']; break;
    }
    $res .="<tr><td>".$arrMsgindex['mid']."</td><td>$platformname</td><td>".$arrMsgindex['keyword']."</td><td>$type</td><td><a href=\"$editurl\">编辑</a>&nbsp;&nbsp;<a href=\"checkreply.php?action=del&mid=".$arrMsgindex['mid']."\">删除</a></td></tr>";
}

echo        '<table>
                <tr>
                    <th style="width:5%;">mid</th>
                    <th style="width:10%;">公众平台</th>
                    <th style="width:10%;">关键词</th>
                    <th style="width:5%;">回复类型</th>
                    <th style="width:5%;">操作</th>
                </tr>';
echo $res;
echo '</table>';

?>