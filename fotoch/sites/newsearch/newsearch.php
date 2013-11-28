<?php

include("././fotofunc.inc.php");
include("././search.inc.php");
//include("./fotofunc.inc.php");
testauth();
//echo "include";
if($_GET['id']!="") {
	$bestand = new XTemplate("././templates/contents.xtpl");
	include("searchdetails.php");
	//echo " included";
	$bestand->assign("CONTENT",$results);
	$bestand->parse("contents.content_detail");
	//parse lexikon
	//$fotograph->parse("lexi_repe_gloss_hand");
	$out.= $bestand->text("contents.content_detail");
}
else {
	//include("./lang.inc.php");
	//volltext und alf. suche	
	$bestand = new XTemplate("././templates/contents.xtpl");
	include("nsearch.php");
	$bestand->assign("SEARCH", $search);
	//$bestand->parse("contents.search");
	$issearch=1;
	include("searchresults.php");
	$bestand->assign("LIST", $results);
	if(!isset($_GET[seg]) && !isset($_GET['volltext'])) {
		$bestand->parse("contents.search.map");
	}
	$bestand->parse("contents.search");

	$out.=$bestand->text("contents.search");
}

?>
