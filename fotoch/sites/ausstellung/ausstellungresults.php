<?php
//include("fotofunc.inc.php");
testauth();
$def=new XTemplate ("././templates/list_results.xtpl");
$def->assign("ACTION",$_GET['a']);
$def->assign("ID",$_GET['id']);
$def->assign("LANG", $_GET['lang']);
$lang = $_GET['lang'];
$neuereintrag = getLangContent("sprache",$_GET['lang'], "neuereintrag");
$def->assign("LITERATUR", getLangContent("sprache",$_GET['lang'], "literatur"));
$def->assign("Id", getLangContent("sprache",$_GET['lang'], "id"));
$def->assign("BEARBEITEN", "&nbsp;&nbsp;[&nbsp;".getLangContent("sprache",$_GET['lang'], "bearbeiten")."&nbsp;]");
$def->assign("INSTITUTION", getLangContent("sprache",$_GET['lang'], "institution"));
$def->assign("TITEL", getLangContent("sprache",$_GET['lang'], "titel"));
$def->assign("ORT", getLangContent("sprache",$_GET['lang'], "ort"));
$def->assign("JAHR", getLangContent("sprache",$_GET['lang'], "jahr"));
$def->assign("TITLE", getLangContent("sprache",$_GET['lang'], "ausstellung"));
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