<?php
//CSS was defined in main.xtpl -> change source in order to
//file choice (navigation)
//$xtpl->assign("CSS","./css/index_footernaviformats.css");

//make new template for needed content with respect to
//the navigation of the user
//template generated here will be added to to the main.xtpl template
$sitemap=new XTemplate ("./templates/contents.xtpl");
//$sitemap->assign("LANG",$language);

//do queries

//assign for content
//$result = mysql_query("SELECT ".$language." FROM sprache WHERE name = 'sitemap'");
//while($fetch = mysql_fetch_array($result)){
	$content = getLangContent("sprache",$_GET['lang'],"sitemap");
	$sitemap->assign("ITEM","<h1>".$content."</h1>");
//}

//$result = mysql_query("SELECT ".$language." FROM sprache WHERE name = 'sitemap_content'");
//while($fetch = mysql_fetch_array($result)){
	$sitemap->assign("DATA",getLangContent("sprache",$_GET['lang'],"sitemap_content"));
//}



//add contents to current template
$sitemap->parse("contents.hand_kon_imp_par_site");
//$out.=$indexFooternaviout->text("indexFooternavi.contentFooternavi");

//include("footer.php");
//$sitemap->assign("FOOTER",$footerout);
//$sitemap->parse("indexFooternavi");

//add footer to current template
$out.=$sitemap->text("contents.hand_kon_imp_par_site");

//add contents and footer to maintemplate $xtpl in index.php
//(which is stored in the $out-variable)
?>