<?php

include("././fotofunc.inc.php");
include("././search.inc.php");
//include("./fotofunc.inc.php");

//echo "include";
if($_GET['id']!="") {
	$bestand = new XTemplate("././templates/contents.xtpl");
	include("bestanddetails.php");	
	//echo " included";
	$bestand->assign("CONTENT",$results);
	$bestand->parse("contents.home_detail");
	//parse lexikon
	//$fotograph->parse("lexi_repe_gloss_hand");
	$out.= $bestand->text("contents.home_detail");
} 
else {
	//include("./lang.inc.php");
	//volltext und alf. suche
	
	$bestand = new XTemplate("././templates/contents.xtpl");
	include("bsearch.php");
	$bestand->assign("SEARCH", $search);
	//$bestand->parse("contents.search");
	$issearch=1;
	include("bestandresults.php");
	$bestand->assign("LIST", $results);
	$bestand->parse("contents.search");
	
	$out.=$bestand->text("contents.search");
} 

?>