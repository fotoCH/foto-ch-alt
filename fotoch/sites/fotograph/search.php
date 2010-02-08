<?php
//include("./fotofunc.inc.php");
//include("./search.inc.php");

$searchmodes = array("ein","erw");
$searchmode=$_GET['mod'];
if(! in_array($searchmode,$searchmodes)) {
	$searchmode="ein";
}

//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
//ini_set ('error_reporting', E_ALL);
$def=new XTemplate ("././templates/search.xtpl");


$language = $_GET['lang'];
$def->assign("LANG",$language);
$def->assign("SEARCHMODE",$searchmode);

$erw_search = getLangContent("sprache",$_GET['lang'],"erw_search");
$einf_search = getLangContent("sprache",$_GET['lang'],"einf_search");
$submit = getLangContent("sprache",$_GET['lang'],"submit");
$nachname = getLangContent("sprache",$_GET['lang'],"nachname");
$vorname = getLangContent("sprache",$_GET['lang'],"vorname");
$arbeitsort = getLangContent("sprache",$_GET['lang'],"arbeitsort");
$arbeitsjahr1 = getLangContent("sprache",$_GET['lang'],"arbeitsjahr");
$volltextsuche =getLangContent("sprache",$_GET['lang'],"volltextsuche");

$def->assign("ACTION",$_GET['a']);
$def->assign("ID",$_GET['id']);
$id=$_GET['id'];

/*
if(auth()){
		$def->assign("NEU"," | <a href=\"./?a=edit&amp;id=new&amp;lang=$lang\">neuer Eintrag</a>");
	}else{
		$def->assign("NEU","");
	}

*/

if(auth()){
	$neuereintrag = getLangContent("sprache",$_GET['lang'],"neuereintrag");
	$def->assign("NEU","<a href=\"./?a=edit&amp;id=new&amp;lang=$language\">[&nbsp;$neuereintrag&nbsp;]</a><br /><br />");
	
	$def->assign("ANZEIGEN",getLangContent("sprache",$_GET['lang'],"anzeigen"));
	$def->parse("ayax_f");
	$text.=$def->text("ayax_f");
	$def->assign("AJAXBAR", "$text<br />");	
	//$search.=$def->text("ayax_f");	
}

for ($an=ord('A');$an<=ord('Z');$an++){
	$def->assign("anf",chr($an));
	$def->assign('ts',($an==ord('Z')?'':''));
	$def->parse("suchen.abuch");
	$def->parse("abuch");
	//$search.=$def->text("suchen.abuch");
}


$fetch[test]="";
	


if($_GET[mod]=="erw"){
	$f = getLangContent("sprache",$_GET['lang'],"f");
	$m = getLangContent("sprache",$_GET['lang'],"m");
	$heimatort = getLangContent("sprache",$_GET['lang'],"heimatort");
	$geburtsdatum = getLangContent("sprache",$_GET['lang'],"geburtsdatum");
	$geburtsort = getLangContent("sprache",$_GET['lang'],"geburtsort");
	$todesdatum = getLangContent("sprache",$_GET['lang'],"todesdatum");
	$todesort = getLangContent("sprache",$_GET['lang'],"todesort");
	
	subgensubmit($def,'submitfield',$submit);
	
	subgenformitem($def,'textfield',$nachname,$fetch[test],'nachname');
	subgenformitem($def,'textfield',$vorname,$fetch[test],'vorname');
	$arr_geschlecht=array(''=>'', 'm' =>$m, 'f' =>$f);
	
	$geschlecht = getLangContent("sprache",$_GET['lang'],"geschlecht");
	
	subgenselectitem($def, $geschlecht, "", "geschlecht", $arr_geschlecht, "", "", "");
	subgenformitem($def,'textfield',$heimatort,$fetch[test],'heimatort');
	subgenformitem($def,'textfield',$geburtsdatum,$fetch[test],'geburtsdatum');
	subgenformitem($def,'textfield',$geburtsort,$fetch[test],'geburtsort');
	subgenformitem($def,'textfield',$todesdatum,$fetch[test],'todesdatum');
	subgenformitem($def,'textfield',$todesort,$fetch[test],'todesort');

	$sql ="DESCRIBE fotografen kanton";
	$result = mysql_query($sql);
	$fetch = mysql_fetch_array($result);
	$set_list = $fetch['Type'];
	$set_list = substr($set_list, 5, strlen($set_list)-7);
	$array_set_list = explode ("','", $set_list);


	//$set= $array_eintrag['kanton'];
	//$array_set = explode (",", $set);
	$kantone = getLangContent("sprache",$_GET['lang'],"kantone");
	subgenselectitem($def, $kantone, 5, "kanton[]", $array_set_list, "true", "", "8");

	if($_SESSION['s_uid']=="fotobe"){
		$notiz = getLangContent("sprache",$_GET['lang'],"notiz");
		subgenformitem($def,'edittext',$notiz,$fetch[test],'notiz');
		
		
		$autorIn = getLangContent("sprache",$_GET['lang'],"autorIn");
		subgenformitem($def,'textfield',$autorIn,$fetch[test],'autorIn');
	}

	$beruf = getLangContent("sprache",$_GET['lang'],"beruf");
	subgenformitem($def,'textfield',$beruf,$fetch[test],'beruf');

	$werdegang = getLangContent("sprache",$_GET['lang'],"werdegang");
	
	subgenformitem($def,'edittext',$werdegang,$fetch[test],'werdegang');
	$schaffensbeschrieb = getLangContent("sprache",$_GET['lang'],"schaffensbeschrieb");
	
	subgenformitem($def,'edittext',$schaffensbeschrieb,$fetch[test],'schaffensbeschrieb');

	$sql ="DESCRIBE fotografen fotografengattungen_set";
	$result = mysql_query($sql);
	$fetch = mysql_fetch_array($result);
	$set_list = $fetch['Type'];
	$set_list = substr($set_list, 5, strlen($set_list)-7);
	$array_set_list = explode ("','", $set_list);


	//$set= $array_eintrag['fotografengattungen_set'];
	//$array_set = explode (",", $set);
	$fotografengattungen = getLangContent("sprache",$_GET['lang'],"fotografengattungen");

	if ($_GET['lang']!='de'){
		subgenselectitemtr($def, $fotografengattungen, 5, "fotografengattungen[]", $array_set_list, $spatr['fotografengattungen_uebersetzungen'], "true", "", "8");
	} else {
		subgenselectitem($def, $fotografengattungen, 5, "fotografengattungen[]", $array_set_list, "true", "", "8");
	}
	$sql ="DESCRIBE fotografen bildgattungen_set";
	$result = mysql_query($sql);
	$fetch = mysql_fetch_array($result);
	$set_list = $fetch['Type'];
	$set_list = substr($set_list, 5, strlen($set_list)-7);
	$array_set_list = explode ("','", $set_list);


	//$set= $array_eintrag['bildgattungen_set'];
	//$array_set = explode (",", $set);
	$bildgattungen = getLangContent("sprache",$_GET['lang'],"bildgattungen");
	if ($_GET['lang']!='de'){
		subgenselectitemtr($def, $bildgattungen, 5, "bildgattungen[]", $array_set_list, $spatr['bildgattungen_uebersetzungen'], "true", "", "8");
	} else {
		subgenselectitem($def, $bildgattungen, 5, "bildgattungen[]", $array_set_list, "true", "", "8");
	}


	subgenformitem($def,'textfield',$arbeitsort,$fetch[''],'arbeitsort');

	subgenformitem($def,'textfield',$arbeitsjahr1,$fetch[''],'arbeitsjahr');

	subgenformitem($def,'edittext',$volltextsuche,$fetch[''],'volltext');


	subgensubmit($def,'submitfield',$submit);
	$suche = "<a href=\"".$_SERVER['PHP_SELF']."?a=fotograph&amp;mod=ein&amp;lang=".$language."\">".$einf_search."</a>";
	$mehrfachauswahl = getLangContent("sprache",$_GET['lang'],"mehrfachauswahl");

} else {
	//subgensubmit($def,'submitfield',$submit);
	subgenformitem($def,'textfield',$nachname,$fetch[''],'nachname');
	subgenformitem($def,'textfield',$vorname,$fetch[''],'vorname');

	subgenformitem($def,'textfield',$arbeitsort,$fetch[''],'arbeitsort');

	subgenformitem($def,'textfield',$arbeitsjahr1,$fetch[''],'arbeitsjahr');

	subgenformitem($def,'edittext',$volltextsuche,$fetch[''],'volltext');

	subgensubmit($def,'submitfield',$submit);
	$suche = "<a href=\"".$_SERVER['PHP_SELF']."?a=fotograph&amp;mod=erw&amp;lang=".$language."\">".$erw_search."</a>";

}
$def->assign("SUCHE", $suche);
$def->assign("MEHRF",$mehrfachauswahl);
$def->parse("suchen");

$search.=$def->text("suchen");
?>

