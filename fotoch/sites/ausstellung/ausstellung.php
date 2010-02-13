<?php

$ausstellung = new XTemplate("././templates/contents.xtpl");

include("././search.inc.php");
include("././fotofunc.inc.php");



if($_GET['id']==''){
	
	include("asearch.php");
	$ausstellung->assign("SEARCH",$search);

	if($_GET['submitbutton']!="" || $_GET['anf']!=''){
		include("ausstellungresults.php");
		$ausstellung->assign("LIST",$results);
	}
	
	$ausstellung->parse("contents.search");
	$out.= $ausstellung->text("contents.search");
}
else {
	//
	include("ausstellungdetails.php");

	$ausstellung->assign("CONTENT",$results);
	
	$ausstellung->parse("contents.home_detail");
	$out.=$ausstellung->text("contents.home_detail");
}
?>
