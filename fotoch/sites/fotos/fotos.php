<?php
$xtpl_fotos = new XTemplate("././templates/contents.xtpl");

include("././search.inc.php");
include("././fotofunc.inc.php");

testauth();

if($_GET['id']==''){
    //search and search result list
    include("fsearch.php");
    $xtpl_fotos->assign("SEARCH",$search);

    if($_GET['anf']!="" || $_GET['submitbutton']!=""){
        include("fotoresults.php");
        $xtpl_fotos->assign("LIST",$results);
    }

    $xtpl_fotos->parse("contents.search");
    $out.= $xtpl_fotos->text("contents.search");
} else {
    //show details
    include("fotodetails.php");
    $xtpl_fotos->assign("CONTENT", $results);
    $xtpl_fotos->parse("contents.content_detail");
    $out.= $xtpl_fotos->text("contents.content_detail");
}
?>