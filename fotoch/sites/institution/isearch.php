<?php
//include("fotofunc.inc.php");
//include("search.inc.php");
/*
function genformitem(&$def, $template, $label, $value, $name ){
	$def->assign("label",$label);
	$def->assign("name",$name);
	$def->assign("value",$value);
	$def->parse("suchen.form.".$template);
	$def->parse("suchen.form");
}

function gencheckitem(&$def, $label, $value, $name ){
	$def->assign("label",$label);
	$def->assign("name",$name);
	$def->assign("value",1);
	$def->assign("checked",($value>0)?'checked="checked" ':'');
	$def->parse("suchen.form.checkfield");
	$def->parse("suchen.form");
}

function genselectitem(&$def, $label, $value, $name, $list, $m, $ll, $size){
	$def->assign("label",$label);
	$def->assign("name",$name);
	$def->assign("size",$size);
	foreach($list as $v => $l){
		$def->assign("olabel",$l);
		if ($m){
			$def->assign("ovalue",$l);
		} else {
			$def->assign("ovalue",$v);
		}
		if ($m){
			$def->assign("selected",(in_array($v,$value))?'selected="selected" ':'');
			if ($l=="\x85"){
				$def->assign("ovalue",0);
			}
		} else {
			$def->assign("selected",($v==$value)?'selected="selected" ':'');
		}
		$def->assign("multiple",($m)?'multiple="multiple" ':'');
		$def->parse("suchen.form.select.option");
	}
	if ($m) $def->parse("suchen.form.select.plink");
	$def->assign("checked",($value>0)?'checked="checked"':'');
	$def->parse("suchen.form.select");
	$def->parse("suchen.form");
}



function genformitemb(&$def, $template, $label, $value, $b ){
	$def->assign("label",$label);
	$def->assign("value",$value);
	$def->parse("berabeiten.form.".$template);
	$def->parse("suchen.form");
}

function gensubmit(&$def, $template, $value){

	$def->assign("value",$value);
	$def->parse("suchen.form.".$template);
	$def->parse("suchen.form");
}
*/

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


#error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
#ini_set ('error_reporting', E_ALL);
//$def=new XTemplate ("./templates/isearch.xtpl");
$def->assign("ACTION",$_GET['a']);
$def->assign("ID",$_GET['id']);
$id=$_GET['id'];

if(auth()){
	$neuereintrag = getLangContent("sprache",$_GET['lang'],"neuereintrag");
	$def->assign("NEU","<a href=\"./?a=iedit&amp;id=new&amp;lang=$language\">[&nbsp;$neuereintrag&nbsp;]</a><br><br>");
	
	$def->assign("ANZEIGEN",getLangContent("sprache",$_GET['lang'],"anzeigen"));
	$def->parse("ayax_i");
	$text.=$def->text("ayax_i");
	$def->assign("AJAXBAR", "$text<br>");	
	//$search.=$def->text("ayax_f");	
}


for ($an=ord('A');$an<=ord('Z');$an++){
	$def->assign("anf",chr($an));
	$def->assign('ts',($an==ord('Z')?'':''));
	$def->parse("suchen.abuch");
}

	$fetch[test]="";
	
	
	
	$name = getLangContent("sprache",$_GET['lang'],"name");
	$ort = getLangContent("sprache",$_GET['lang'],"ort");
	$volltextsuche = getLangContent("sprache",$_GET['lang'],"volltextsuche");
	$submit = getLangContent("sprache",$_GET['lang'],"submit");
	
	//subgensubmit($def,'submitfield',$submit);
	subgenformitem($def,'textfield',$name,$fetch[test],'name');
	subgenformitem($def,'textfield',$ort,$fetch[test],'ort');


	subgenformitem($def,'edittext',$volltextsuche,$fetch[test],'volltext');


	subgensubmit($def,'submitfield',$submit);
	//
	//$suche = "<a href=\"./?a=repertorium&amp;mod=erw\">erweiterte Suche</a>";

	$def->assign("SUCHE", $suche);
	$def->parse("suchen");
	$search.=$def->text("suchen");
	//echo $search;
//}
?>

