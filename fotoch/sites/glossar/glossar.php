<?php
//include("fotofunc.inc.php");
$glossar = new XTemplate("././templates/contents.xtpl");

//set search-params: language and mode 
//set the mod = ein. because we havent set mod, a first search would be mod == "". -> no proper results

include("././search.inc.php");
include("././fotofunc.inc.php");


if($_GET['id']==''){
	include("gsearch.php");
	$glossar->assign("SEARCH",$search);
	$issearch=1;
	//echo "include";
	if($_GET['submitbutton']!="" || $_GET['anf']!=''){
		include("glossarresults.php");
		$glossar->assign("LIST",$results);
	}
	
	//parse lexikon
	
	$glossar->parse("contents.search");
	$out.= $glossar->text("contents.search");
}
else {
	//show glossar detail
	include("glossardetails.php");
	//$glossar = new XTemplate("./templates/contents.xtpl");
	$glossar->assign("CONTENT",$results);
	$glossar->parse("contents.home_detail");
	$out.=$glossar->text("contents.home_detail");

}
?>
