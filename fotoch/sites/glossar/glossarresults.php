<?php


$def=new XTemplate ("././templates/list_results.xtpl");
$def->assign("ACTION",$_GET['a']);
$def->assign("ID",$_GET['id']);
//$def->assign("SEARCHMODE", "ein");
$lang=$_GET['lang'];
$def->assign("LANG", $lang);
$id=$_GET['id'];
$anf=$_GET['anf'];
$volltext=$_GET['volltext'];

$def->assign("TITLE",getLangContent("sprache",$lang,"glossar"));
$def->assign("BEGRIFF", getLangContent("sprache",$lang,"begriff"));
$def->assign("ZEITRAUM", getLangContent("sprache",$lang,"zeitraum"));
$def->assign("ERLAEUTERUNG", getLangContent("sprache",$lang,"erlaeuterung"));
$neuereintrag =  getLangContent("sprache",$lang,"neuereintrag");
$def->assign("BEARBEITEN", "[&nbsp;".getLangContent("sprache",$lang,"bearbeiten")."&nbsp;]");

if(auth()){
	$def->parse("list.head_admin_glossar");
}else{
	$def->parse("list.head_glossar");	
}


$leerout=" AND LENGTH( `erlaeuterung` ) >0";
if (auth()) $leerout='';

if ($id==''){
	// Select: code
	
	$result=mysql_query("SELECT * FROM glossar WHERE begriff LIKE '$anf%' $leerout ORDER BY begriff Asc");
	if(!$anf) $result=mysql_query("SELECT * FROM glossar WHERE begriff LIKE '%$volltext%' ORDER BY begriff Asc");
	if (auth()){
		$suff='row_admin_glossar';
	}
	else {
		$suff='row_glossar';
	}

	//echo "SELECT * FROM fotografen WHERE nachname LIKE '$anf%' ORDER BY  nachname Asc, vorname Asc";
	while($fetch=mysql_fetch_array($result)){

		$def->assign("FETCH",$fetch);
		//print_r($fetch);
		$def->parse("list.".$suff);
		//$def->parse("list.row_normal");
	}

	if(auth()){
		//$def->assign("NEU"," | <a href=\"".$_SERVER['PHP_SELF']."?a=gedit&amp;id=new&amp;lang=$lang\">$neuereintrag</a>");
	}else{
		$def->assign("NEU","");
	}


	$def->parse("list");
	//$def->out("list");
	$results.=$def->text("list");
}

?>