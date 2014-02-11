<?php
require_once(dirname(__FILE__).'/../../plugin.inc.php');

$page = file_get_contents(dirname(__FILE__).'/template/index.html');
$aDatabase = new Database();
$result = $aDatabase->get_result("SELECT `wechat_id` FROM `weconfig` WHERE `wid`=".$_GET['wid']);
$row = mysql_fetch_row($result);
$page = str_replace('{template_wxid}',$row[0],$page);
$page = str_replace('{template_wid}',$_GET['wid'],$page);
echo $page;

?>