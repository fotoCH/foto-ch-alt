<?php
//CSS was defined in main.xtpl -> change source in order to
//file choice (navigation)
//$xtpl->assign("CSS","./css/index_footernaviformats.css");

//make new template for needed content with respect to
//the navigation of the user
//template generated here will be added to to the main.xtpl template
$impressum=new XTemplate ("./templates/contents.xtpl");
//$indexFooternaviout->assign("LANG",$language);

//do queries

//assign for content
//$result = mysql_query("SELECT ".$language." FROM sprache WHERE name = 'impressum'");
//while($fetch = mysql_fetch_array($result)){
	
	$impressum->assign("ITEM","<h1>".$spr['impressum']."</h1>");
//}

//$result = mysql_query("SELECT ".$language." FROM sprache WHERE name = 'impressum_content'");
//while($fetch = mysql_fetch_array($result)){
	$impressum->assign("DATA",$spr['impressum_content']);
//}

$impressum->parse("contents.hand_kon_imp_par_site");
 //get footer template
//include("footer.php");

//finish the indexfooternavi-template
//$indexFooternaviout->assign("FOOTER",$footerout);
//$indexFooternaviout->parse("indexFooternavi");

//add indexfooternavi to the main-template
$out.=$impressum->text("contents.hand_kon_imp_par_site");
?>