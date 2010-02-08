<?php

$literatur = new XTemplate("././templates/contents.xtpl");

include("././search.inc.php");
include("././fotofunc.inc.php");



if($_GET['id']==''){
	
	include("litsearch.php");
	$literatur->assign("SEARCH",$search);

	if($_GET['submit']!="" || $_GET['anf']!=''){
		include("literaturresults.php");
		$literatur->assign("LIST",$results);
	}
	
	$literatur->parse("contents.search");
	$out.= $literatur->text("contents.search");
}
else {
	//
	include("literaturdetails.php");

	$literatur->assign("CONTENT",$results);
	
	$literatur->parse("contents.home_detail");
	$out.=$literatur->text("contents.home_detail");
}
?>
