<?php
//$xtpl->assign("CSS","css/handbuchformats.css");

$handbuch = new XTemplate("./templates/contents.xtpl");

//assign the search field (left)
//in this case it is an index
//get the database entry
//$result = mysqli_query($sqli, "SELECT ".$language." FROM sprache WHERE name='handbuch_index'");
//while($fetch = mysqli_fetch_array($result)){
//	$search = $fetch["".$language.""];
//}
$handbuch->assign("ITEM",$spr['handbuch_index']);
//$lexi_repe_gloss_hand->parse("lexi_repe_gloss_hand.search_results");
//$out.= $lexi_repe_gloss_hand->text("lexi_repe_gloss_hand.search_results");

//assign the searchresults
//$result = mysqli_query($sqli, "SELECT ".$language." FROM sprache WHERE name='handbuch_content'");
//while($fetch = mysqli_fetch_array($result)){
//	$content = $fetch["".$language.""];
//}
$handbuch->assign("DATA",$spr['handbuch_content']);
$handbuch->parse("contents.hand_kon_imp_par_site");
$out.= $handbuch->text("contents.hand_kon_imp_par_site");

?>
