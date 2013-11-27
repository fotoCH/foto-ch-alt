<?php
include("mysql.inc.php");
header('Content-type: text/html; charset=utf-8');
$lang = $_COOKIE['lang'];
$def=new XTemplate ("./templates/search.xtpl");
$id=mysql_real_escape_string($GET['id']);
$sql = "SELECT id, name_".$lang." as name FROM provsubk WHERE id=$id";
$rsd = mysql_query($sql);
while($rs = mysql_fetch_array($rsd)) {
	$def->assign('subk',$rs);
	$def->parse("seg_map.poly");
	}
$def->parse("seg_map");
$def->out("seg_map");
?>
