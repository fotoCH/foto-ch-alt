<?php
$page=new XTemplate ("./templates/contents.xtpl");
	
$page->assign("ITEM","<h1>".$spr['statistik']."</h1>");
$page->assign("DATA",$spr['kontakt_content']);
$page->parse("contents.hand_kon_imp_par_site");

$out.=$page->text("contents.hand_kon_imp_par_site");
$sql = "SELECT COUNT(id),useragent,level,isbot FROM `log_sessions` WHERE 1 GROUP BY useragent,level ORDER BY isbot,level,useragent LIMIT 0, 30 ";
?>