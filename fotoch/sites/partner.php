<?php
//CSS was defined in main.xtpl -> change source in order to
//file choice (navigation)
//$xtpl->assign("CSS","./css/index_footernaviformats.css");

//make new template for needed content with respect to
//the navigation of the user
//template generated here will be added to to the main.xtpl template
$partner=new XTemplate ("./templates/contents.xtpl");
//$indexFooternaviout->assign("LANG",$language);

//do queries

//assign for content
//$result = mysql_query("SELECT ".$language." FROM sprache WHERE name = 'partner'");
//while($fetch = mysql_fetch_array($result)){
	$partner->assign("ITEM","<h1>".$spr['partner']."</h1>");
//}

//$result = mysql_query("SELECT ".$language." FROM sprache WHERE name = 'partner_content'");
//while($fetch = mysql_fetch_array($result)){
	$partner->assign("DATA",$spr['partner_content']);
//}

//add contents to current template
$partner->parse("contents.hand_kon_imp_par_site");
//$out.=$indexFooternaviout->text("indexFooternavi.contentFooternavi");

//get footer template
//include("footer.php");

//finish the indexfooternavi-template
//$indexFooternaviout->assign("FOOTER",$footerout);
//$indexFooternaviout->parse("indexFooternavi");

//add indexfooternavi to the main-template
$out.=$partner->text("contents.hand_kon_imp_par_site");
?>