<?php
//include("fotofunc.inc.php");
testauth();
$def=new XTemplate ("././templates/list_results.xtpl");
$def->assign("ACTION",$_GET['a']);
$def->assign("ID",$_GET['id']);
$def->assign("LANG", $_GET['lang']);
$lang = $_GET['lang'];

$def->assign("SPR",$spr);

$neuereintrag = $spr['neuereintrag'];
$def->assign("BEARBEITEN", "&nbsp;&nbsp;[&nbsp;".$spr['bearbeiten']."&nbsp;]");
$def->assign("title", $spr['ausstellung']);


$id=$_GET['id'];
$anf=$_GET['anf'];
$volltext = $_GET['volltext'];
testauth();
$def->parse("list.head_ausstellung");


if ($anf=='andere'){
	$result=mysql_query("SELECT * FROM ausstellung WHERE (`titel` <'A') OR (`titel`>'zz') ORDER BY  titel Asc");
}
else {
	if($anf!=''){
		$result=mysql_query("SELECT * FROM ausstellung WHERE titel LIKE '$anf%' ORDER BY  titel Asc");
	}
	elseif($volltext!='') {
		$result=mysql_query("SELECT * FROM ausstellung WHERE titel LIKE '%$volltext%' OR institution LIKE '%$volltext%' OR ort LIKE '%$volltext%' ORDER BY jahr DESC");
	}
	else {
		//echo "fehler";
	}
}
while($fetch=mysql_fetch_array($result)){
	$def->assign("FETCH",$fetch);
	$def->parse("list.row_ausstellung");
}
$def->parse("list");
$results.=$def->text("list");
?>