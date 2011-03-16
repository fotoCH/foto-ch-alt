<?php
$user = new XTemplate("././templates/contents.xtpl");

include("././search.inc.php");
include("././fotofunc.inc.php");
testauthedit();
if($_GET['id']==''){
	//search and 
	include("usearch.php");
	$user->assign("SEARCH",$search);
	
	//... list all
	$issearch=1;
		include("userresults.php");
		//$lexi_repe_gloss_hand->assign("RESULTS",$results);
		$user->assign("LIST",$results);
	
	$user->parse("contents.search");
	$out.= $user->text("contents.search");
} else {
	//show details
	include("userdetails.php");
	$user->assign("CONTENT", $results);
	$user->parse("contents.home_detail");
	$out.= $user->text("contents.home_detail");
}
?>

