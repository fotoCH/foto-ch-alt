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

if(auth_level($USER_WORKER)){

	$def->assign("NEU","<a href=\"./?a=iedit&amp;id=new&amp;lang=$language\">[&nbsp;".$spr['neuereintrag']."&nbsp;]</a><br><br>");
	
	$def->parse("ayax_i");
	$text.=$def->text("ayax_i");
	$def->assign("AJAXBAR", "$text<br />");	
	//$search.=$def->text("ayax_f");	
}


for ($an=ord('A');$an<=ord('Z');$an++){
	$def->assign("anf",chr($an));
	$def->assign('ts',($an==ord('Z')?'':''));
	$def->parse("suchen.abuch");
}

	$fetch[test]="";
	
	//subgensubmit($def,'submitfield',$submit);
	subgenformitem($def,'textfield',$spr['name'],$fetch[test],'name');
	subgenformitem($def,'textfield',$spr['ort'],$fetch[test],'ort');


	subgenformitem($def,'edittext',$spr['volltextsuche'],$fetch[test],'volltext');


	subgensubmit($def,'submitfield',$spr['submit']);
	//
	//$suche = "<a href=\"./?a=repertorium&amp;mod=erw\">erweiterte Suche</a>";

	//$def->assign("SUCHE", $suche);
	$def->parse("suchen");
	$search.=$def->text("suchen");
	//echo $search;
//}
?>

