<?php
//CSS was defined in main.xtpl -> change source in order to
//file choice (navigation)
//$ueberuns->assign("CSS","./css/index_footernaviformats.css");

//make new template for needed content with respect to
//the navigation of the user
//template generated here will be added to to the main.xtpl template
$ueberuns=new XTemplate("./templates/contents.xtpl");
//$indexFooternaviout->assign("LANG",$language);

//do queries

//assign for content
//$result = mysql_query("SELECT ".$language." FROM sprache WHERE name = 'kontakt'");
//while($fetch = mysql_fetch_array($result)){
	
//	$ueberuns->assign("ITEM","<h1>".$spr['ueberuns']."</h1>");
//}

$ueberuns->assign("ITEM",$spr['ueberuns_index']);

//$result = mysql_query("SELECT ".$language." FROM sprache WHERE name = 'kontakt_content'");
//while($fetch = mysql_fetch_array($result)){
	$ueberuns->assign("DATA",$spr['ueberuns_content']);
//}

//add contents to current template
$ueberuns->parse("contents.hand_kon_imp_par_site");
//$out.=$indexFooternaviout->text("indexFooternavi.contentFooternavi");

//get footer template
//include("footer.php");

//finish the indexfooternavi-template
//$indexFooternaviout->assign("FOOTER",$footerout);
//$indexFooternaviout->parse("indexFooternavi");

//add indexfooternavi to the main-template
$out.=$ueberuns->text("contents.hand_kon_imp_par_site");

$ueberuns->assign('SPR',$spr);
$logos=getLogos();
//print_r($logos);
foreach ($logos as $logo){
    $e['text']='<b>'.$logo['text'].'</b>';
    if ($logo['bild']){
        $e['img']='<img alt="'.$logo['text'].'" width="'.$logo['width'].'" border="0" src="Logos/'.$logo['bild'].'" />';
    } else {
        $e['img']=' &nbsp; ';
    }
    $ueberuns->assign("e", $e);
    $ueberuns->parse("contents.partner_content.row");
}

$ueberuns->parse("contents.partner_content");
$out.=$ueberuns->text("contents.partner_content");

// impressum
$ueberuns->assign("DATA",$spr['impressum_content']);
$ueberuns->parse("contents.imprint_content");
//add indexfooternavi to the main-template
$out.=$ueberuns->text("contents.imprint_content");
?>