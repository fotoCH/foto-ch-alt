<?
$fotograph = new XTemplate("././templates/contents.xtpl");

include("././search.inc.php");
include("././fotofunc.inc.php");


if($_GET['id']==''){
	//search and 
	include("search.php");
	$fotograph->assign("SEARCH",$search);
	
	//... list
	if(($_GET['mod']=="alph" && $_GET['anf']!="") || ($_GET['mod']=="erw" && $_GET['submit']!="") || 
	($_GET['mod']=="ein" && $_GET['submit']!="")){
		include("fotographresults.php");
		//$lexi_repe_gloss_hand->assign("RESULTS",$results);
		$fotograph->assign("LIST",$results);
	}
	$fotograph->parse("contents.search");
	$out.= $fotograph->text("contents.search");
} else {
	//show details
	include("fotographdetails.php");
	$fotograph->assign("CONTENT", $results);
	$fotograph->parse("contents.home_detail");
	$out.= $fotograph->text("contents.home_detail");
}
?>
