<?php

require_once('include/common.frame.inc.php');

$content = file_get_contents("template/index.htm");

$INDEXPAGE = new CommonFrame();
$INDEXPAGE->display_page($content);

?>