<?php

$def=new XTemplate ("././templates/search.xtpl");
$def->assign("ACTION",$_GET['a']);
$def->assign("ID",$_GET['id']);
$def->assign("LANG", $_GET['lang']);
$def->assign("SEARCHMODE", "ein");
$def->assign("SPR",$spr);
$language = $_GET['lang'];

if(auth_level($USER_WORKER)){
	
	$def->assign("NEU","<a href=\"./?a=bedit&amp;id=new&amp;lang=$language\">[&nbsp;".$spr['neuereintrag']."&nbsp;]</a><br /><br />");
	
	$def->parse("ayax_b");
	$text.=$def->text("ayax_b");
	$def->assign("AJAXBAR", "$text<br />");	
	//$search.=$def->text("ayax_f");	
}

//alphabetische suche
for ($an=ord('A');$an<=ord('Z');$an++){
	$def->assign("anf",chr($an));
	$def->parse("suchen.abuch");
	//$def->out("list");
}

//volltextsuche
subgenformitem($def,'edittext',$spr['volltextsuche'],$fetch[test],'volltext');

//such button
subgensubmit($def,'submitfield',$spr['submit']);

//write to template
$def->parse("suchen");

//write to $out.
$search.=$def->text("suchen");
?>