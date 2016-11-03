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
//$result = mysqli_query($sqli, "SELECT ".$language." FROM sprache WHERE name = 'sitemap'");
//while($fetch = mysqli_fetch_array($result)){
	
	$sitemap->assign("ITEM","<h1>".$spr['sitemap']."</h1>");
//}

//$result = mysqli_query($sqli, "SELECT ".$language." FROM sprache WHERE name = 'sitemap_content'");
//while($fetch = mysqli_fetch_array($result)){
	$sitemap->assign("DATA",$spr['sitemap_content']);
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