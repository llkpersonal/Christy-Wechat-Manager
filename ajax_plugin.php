<?php
header("Content-type:text/html;charset=UTF-8");
require_once(dirname(__FILE__).'/include/database.inc.php');

$table_header = <<<TABLE_HEADER
<table>
<tr>
    <th style="width:20%;">名称</th>
    <th style="width:20%">目录</th>
    <th style="width:15%">版本</th>
    <th style="width:15%">类型</th>
    <th style="width:18%">关键词</th>
    <th>操作</th>
</tr>
TABLE_HEADER;

$table_content = "";

$table_footer = "</table>";

$root_folder = "plugin";
$dir = opendir($root_folder);
$aDatabase = new Database();
$wid = $_GET['wid'];
$result = $aDatabase->get_result("SELECT * FROM `plugin` WHERE `wid`='$wid';");

while($array = mysql_fetch_array($result)){
    $table_content .= "<tr><td>".$array['name']."</td><td>".$array['folder']."</td><td>".$array['version']."</td><td>".$array['protrol']."</td><td>".$array['keyword']."</td><td>";
    if($array['hasconfigpage']=='yes'){
        $table_content .= "<a href=\"plugin/".$array['folder']."/?wid=".$wid."\">配置</a>&nbsp;";
    }
    $table_content .= "<a href=\"plugin.php?action=unins&pid=".$array['pid']."\">卸载</a></td></tr>";
}

echo $table_header.$table_content.$table_footer;

?>
