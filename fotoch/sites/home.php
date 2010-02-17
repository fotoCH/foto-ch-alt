<?php

$home=new XTemplate ("./templates/contents.xtpl");
$home->assign("CONTENT",$spr['home_content']);

$home->assign("LOGOS", $spr['home_logos']);
$home->parse("contents.home_detail");

//add indexfooternavi to the main-template
$out.=$home->text("contents.home_detail");
?>