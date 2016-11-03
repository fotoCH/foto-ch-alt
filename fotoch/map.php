<?php
require("templates/xtemplate.class.php");
include("mysql.inc.php");
header('Content-type: text/html; charset=utf-8');
$lang = mysqli_real_escape_string($sqli, $_COOKIE['lang']);
$def=new XTemplate ("./templates/contents.xtpl");
if(isset($_GET['id'])) {
	$id=mysqli_real_escape_string($sqli, $_GET['id']);
	$sql = "SELECT id, name_".$lang." as name, poly_coords FROM provsubk WHERE FIND_IN_SET(super_r, ".$id.") ORDER BY id";
}
else {
	$sql = "SELECT id, name_".$lang." as name, poly_coords FROM kontinent ORDER BY id";
}
$rsd = mysqli_query($sqli, $sql);
while($rs = mysqli_fetch_array($rsd)) {
	$def->assign('subk',$rs);
	$def->parse("contents.search.map.seg_map.poly");
	}
$def->parse("contents.search.map.seg_map");
$def->out("contents.search.map.seg_map");
?>
