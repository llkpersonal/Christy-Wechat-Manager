<?php
header("Content-type:text/html;charset=UTF-8");
require_once(dirname(__FILE__).'/include/database.inc.php');

$table_header = <<<TABLE_HEADER
<table>
<tr>
    <th style="width:20%;">名称</th>
    <th style="width:20%">目录</th>
    <th style="width:18%">版本</th>
    <th style="width:18%">类型</th>
    <th style="width:18%">关键词</th>
    <th>安装</th>
</tr>
TABLE_HEADER;

$table_content = "";

$table_footer = "</table>";

function is_installed($folder){
    $aDatabase = new Database();
    $wid = $_GET['wid'];
    $row = mysql_fetch_row($aDatabase->get_result("SELECT COUNT(*) FROM `plugin` WHERE `wid`='$wid' AND `folder`='$folder';"));
    if($row[0]>0){
        return true;
    } else {
        return false;
    }
}


$root_folder = "plugin";
$dir = opendir($root_folder);

while($file = readdir($dir)){
    if($file=='..'||$file=='.') continue;
    if(is_dir($root_folder.'/'.$file)&&!is_installed($file)){
        $str = file_get_contents($root_folder.'/'.$file.'/config.xml');
        $obj = simplexml_load_string($str);
        $table_content .= "<tr><td>$obj->name</td><td>$file</td><td>$obj->version</td><td>$obj->protrol</td><td>$obj->keyword</td>
                            <td><a href=\"installplugin.php?action=install&wid=".$_GET['wid']."&folder=$file\">安装</a></td></tr>";
    }
}

echo $table_header.$table_content.$table_footer;

?>
