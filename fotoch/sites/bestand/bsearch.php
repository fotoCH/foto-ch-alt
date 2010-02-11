<?php

$def=new XTemplate ("././templates/search.xtpl");
$def->assign("ACTION",$_GET['a']);
$def->assign("ID",$_GET['id']);
$def->assign("LANG", $_GET['lang']);
$def->assign("SEARCHMODE", "ein");
$language = $_GET['lang'];

if(auth()){
	$neuereintrag = getLangContent("sprache",$_GET['lang'],"neuereintrag");
	$def->assign("NEU","<a href=\"./?a=bedit&amp;id=new&amp;lang=$language\">[&nbsp;$neuereintrag&nbsp;]</a><br /><br />");
	
	$def->assign("ANZEIGEN",getLangContent("sprache",$_GET['lang'],"anzeigen"));
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
$volltextsuche =getLangContent("sprache",$_GET['lang'],"volltextsuche");
subgenformitem($def,'edittext',$volltextsuche,$fetch[test],'volltext');

//such button
$submit = getLangContent("sprache",$_GET['lang'],"submit");
subgensubmit($def,'submitfield',$submit);

//write to template
$def->parse("suchen");

//write to $out.
$search.=$def->text("suchen");
?>