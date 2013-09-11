<?php
// header("Content-type: text/html; charset=utf-8");
// include_once('../templates/xtemplate.class.php');
// include_once 'mysql.inc.php';



// include("../fotofunc.inc.php");
// include("../backend.inc.php");

$def = new XTemplate('./templates/segedit.xtpl');

$def->assign("LANG", $lang);
$def->assign("SPR",$spr);


$def->parse('segedit');

$out.=$def->text("segedit");

?>