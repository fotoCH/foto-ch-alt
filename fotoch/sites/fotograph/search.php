<?php

$searchmodes = array("ein","erw");
$searchmode=$_GET['mod'];
if(! in_array($searchmode,$searchmodes)) {
	$searchmode="ein";
}

//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
//ini_set ('error_reporting', E_ALL);
$def=new XTemplate ("././templates/search.xtpl");

$def->assign("SPR",$spr);

$language = $_GET['lang'];
$def->assign("LANG",$language);
//$def->assign("SPR",$spr);
$def->assign("SEARCHMODE",$searchmode);

$def->assign("ACTION",$_GET['a']);
$def->assign("ID",$_GET['id']);
$id=$_GET['id'];


if(auth_level(USER_WORKER)){
	
	$def->assign("NEU","<a href=\"./?a=edit&amp;id=new&amp;lang=$language\">[&nbsp;".$spr['neuereintrag']."&nbsp;]</a><br /><br />");
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


$fetch['empty']="";
	


if($_GET[mod]=="erw"){
	subgensubmit($def,'submitfield',$spr['submit']);
	
	subgenformitem($def,'textfield',$spr['nachname'],$fetch['empty'],'nachname');
	subgenformitem($def,'textfield',$spr['vorname'],$fetch['empty'],'vorname');
	$arr_geschlecht=array(''=>'', 'm' =>$spr['m'], 'f' =>$spr['f']);
	

	subgenselectitem($def, $spr['geschlecht'], "", "geschlecht", $arr_geschlecht, "", "", "");
	subgenformitem($def,'textfield',$spr['heimatort'],$fetch['empty'],'heimatort');
	subgenformitem($def,'textfield',$spr['geburtsdatum'],$fetch['empty'],'geburtsdatum');
	subgenformitem($def,'textfield',$spr['geburtsort'],$fetch['empty'],'geburtsort');
	subgenformitem($def,'textfield',$spr['todesdatum'],$fetch['empty'],'todesdatum');
	subgenformitem($def,'textfield',$spr['todesort'],$fetch['empty'],'todesort');

	$sql ="DESCRIBE fotografen kanton";
	$result = mysql_query($sql);
	$fetch = mysql_fetch_array($result);
	$set_list = $fetch['Type'];
	$set_list = substr($set_list, 5, strlen($set_list)-7);
	$array_set_list = explode ("','", $set_list);

	subgenselectitem($def, $spr['kantone'], 5, "kanton[]", $array_set_list, "true", "", "8");

	if(auth_level(USER_GUEST_READER_PARTNER)){
		subgenformitem($def,'edittext',$spr['notiz'],$fetch['empty'],'notiz');
		subgenformitem($def,'textfield',$spr['autorIn'],$fetch['empty'],'autorIn');
	}

	subgenformitem($def,'textfield',$spr['beruf'],$fetch['empty'],'beruf');
	subgenformitem($def,'edittext',$spr['biografie'],$fetch['empty'],'kurzbio');
	subgenformitem($def,'edittext',$spr['werdegang'],$fetch['empty'],'werdegang');
	subgenformitem($def,'edittext',$spr['schaffensbeschrieb'],$fetch['empty'],'schaffensbeschrieb');

	$sql ="DESCRIBE fotografen fotografengattungen_set";
	$result = mysql_query($sql);
	$fetch = mysql_fetch_array($result);
	$set_list = $fetch['Type'];
	$set_list = substr($set_list, 5, strlen($set_list)-7);
	$array_set_list = explode ("','", $set_list);

	if ($_GET['lang']!='de'){
		subgenselectitemtr($def, $spr['fotografengattungen'], 5, "fotografengattungen[]", $array_set_list, $spatr['fotografengattungen_uebersetzungen'], "true", "", "8");
	} else {
		subgenselectitem($def, $spr['fotografengattungen'], 5, "fotografengattungen[]", $array_set_list, "true", "", "8");
	}
	$sql ="DESCRIBE fotografen bildgattungen_set";
	$result = mysql_query($sql);
	$fetch = mysql_fetch_array($result);
	$set_list = $fetch['Type'];
	$set_list = substr($set_list, 5, strlen($set_list)-7);
	$array_set_list = explode ("','", $set_list);

	if ($_GET['lang']!='de'){
		subgenselectitemtr($def, $spr['bildgattungen'], 5, "bildgattungen[]", $array_set_list, $spatr['bildgattungen_uebersetzungen'], "true", "", "8");
	} else {
		subgenselectitem($def, $spr['bildgattungen'], 5, "bildgattungen[]", $array_set_list, "true", "", "8");
	}


	subgenformitem($def,'textfield',$spr['arbeitsort'],$fetch[''],'arbeitsort');

	subgenformitem($def,'textfield',$spr['arbeitsjahr'],$fetch[''],'arbeitsjahr');

	subgenformitem($def,'edittext',$spr['volltextsuche'],$fetch[''],'volltext');

	subgensubmit($def,'submitfield',$spr['submit']);
	$suche = "<a href=\"".$_SERVER['PHP_SELF']."?a=fotograph&amp;mod=ein&amp;lang=".$language."\">".$spr['einf_search']."</a>";
	$mehrfachauswahl = $spr['mehrfachauswahl'];
	
} else {
	
	subgenformitem($def,'textfield',$spr['nachname'],$fetch[''],'nachname');
	subgenformitem($def,'textfield',$spr['vorname'],$fetch[''],'vorname');

	subgenformitem($def,'textfield',$spr['arbeitsort'],$fetch[''],'arbeitsort');

	subgenformitem($def,'textfield',$spr['arbeitsjahr'],$fetch[''],'arbeitsjahr');

	subgenformitem($def,'edittext',$spr['volltextsuche'],$fetch[''],'volltext');

	subgensubmit($def,'submitfield',$spr['submit']);
	$suche = "<a href=\"".$_SERVER['PHP_SELF']."?a=fotograph&amp;mod=erw&amp;lang=".$language."\">".$spr['erw_search']."</a>";

}
$def->assign("SUCHE", $suche);
$def->assign("MEHRF",$mehrfachauswahl);
$def->parse("suchen");

$search.=$def->text("suchen");
?>

