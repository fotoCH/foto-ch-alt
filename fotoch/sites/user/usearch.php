<?php

$searchmodes = array("ein","erw");
$searchmode=$_GET['mod'];
if(! in_array($searchmode,$searchmodes)) {
	$searchmode="ein";
}

#error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
//ini_set ('error_reporting', E_ALL);
$def=new XTemplate ("././templates/search.xtpl");


$language = $_GET['lang'];
$def->assign("LANG",$language);
$def->assign("SEARCHMODE",$searchmode);
$def->assign("SPR",$spr);

#error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
#ini_set ('error_reporting', E_ALL);
//$def=new XTemplate ("./templates/isearch.xtpl");
$def->assign("ACTION",$_GET['a']);
$def->assign("ID",$_GET['id']);
$id=$_GET['id'];

if(auth_level(USER_WORKER)){

	$def->assign("NEU","<a href=\"./?a=uedit&amp;id=new&amp;lang=$language\">[&nbsp;".$spr['neuereintrag']."&nbsp;]</a><br><br>");
	
	$def->parse("ayax_u");
	$text.=$def->text("ayax_u");
	$def->assign("AJAXBAR", "$text<br />");	
}


for ($an=ord('A');$an<=ord('Z');$an++){
	$def->assign("anf",chr($an));
	$def->assign('ts',($an==ord('Z')?'':''));
	$def->parse("suchen.abuch");
}

	$fetch['test']="";
	
	subgenformitem($def,'textfield',$spr['username'],$fetch['test'],'username');


	subgensubmit($def,'submitfield',$spr['submit']);
	$def->parse("suchen");
	$search.=$def->text("suchen");
?>

