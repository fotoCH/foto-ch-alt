<?php
$fotografien = new XTemplate("././templates/contents.xtpl");

include("././search.inc.php");
include("././fotofunc.inc.php");

if($_GET['id']==''){
    //search and
    include("fsearch.php");
    $fotografien->assign("SEARCH",$search);
    $issearch=1;
    //... list
    if($_GET['anf']!="" || $_GET['submitbutton']!=""){
        include("fotoresults.php");
        //$lexi_repe_gloss_hand->assign("RESULTS",$results);
        $fotografien->assign("LIST",$results);
    }

    $fotografien->parse("contents.search");
    $out.= $fotografien->text("contents.search");
} else {
    //show details
    include("fotodetails.php");
    $fotografien->assign("CONTENT", $results);
    $fotografien->parse("contents.home_detail");
    $out.= $fotografien->text("contents.home_detail");
}
?>