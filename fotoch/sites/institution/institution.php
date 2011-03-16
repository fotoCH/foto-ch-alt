<?php
$institution = new XTemplate("././templates/contents.xtpl");

include("././search.inc.php");
include("././fotofunc.inc.php");

if($_GET['id']==''){
	//search and 
	include("isearch.php");
	$institution->assign("SEARCH",$search);
	
	//... list
	if($_GET['anf']!="" || $_GET['submitbutton']!=""){
		include("institutionresults.php");
		//$lexi_repe_gloss_hand->assign("RESULTS",$results);
		$institution->assign("LIST",$results);
	}
	$issearch=1;
	$institution->parse("contents.search");
	$out.= $institution->text("contents.search");
} else {
	//show details
	include("institutiondetails.php");
	$institution->assign("CONTENT", $results);
	$institution->parse("contents.home_detail");
	$out.= $institution->text("contents.home_detail");
}
?>

