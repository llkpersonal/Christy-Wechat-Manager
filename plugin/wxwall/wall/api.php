<?php

@header("Content-type: text/html; charset=utf-8");
 
require_once(dirname(__FILE__).'/../../plugin.inc.php');

$lastid=$_REQUEST['lastid'];
$wid = $_REQUEST['wid'];
$aDatabase = new Database();
$num=$lastid+1;
//$sql1="SELECT * FROM  `msg` order by `mid` desc limit 3";
$sql1="SELECT * FROM  `wxwall_msg` where `num` = '$num' and `wid`='$wid' limit 1 ";
$query1 = $aDatabase->get_result($sql1);
$q=mysql_fetch_row($query1);


$mid=$q[0];
$num=$q[2];
$content=$q[3];
$nickname=$q[4];
$avatar=$q[5];
if($q[3]){
@$msg=array(data=>array(array("id"=>$mid,"num"=>$num,"content"=>$content,"nickname"=>$nickname,"avatar"=>$avatar)),ret=>1);
echo $msg=json_encode($msg);
}
if(!$q[3]){
@$msg=array(data=>array(),ret=>0);
echo $msg=json_encode($msg);
}


?>