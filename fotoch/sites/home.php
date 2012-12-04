<?php
$home=new XTemplate ("./templates/contents.xtpl");

$home->assign("CONTENT",$spr['home_content']);
$home->assign('SPR',$spr);
$logos=getLogos();

foreach ($logos as $logo){

	if (false){ //if ($logo['link']){
		if ($logo['bild']){
			$e['img']='<a href="'.$logo['link'].'" target="_new"><img width="'.$logo['width'].'" border="0" src="Logos/'.$logo['bild'].'" /></a>';
		} else {
			$e['img']='<a href="'.$logo['link'].'" target="_new">'.$logo['text'].'</a>';
		}
	} else {
		if ($logo['bild']){
			$e['img']='<img alt="'.$logo['text'].'" width="'.$logo['width'].'" border="0" src="Logos/'.$logo['bild'].'" />';
		} else {
			$e['img']='<b>'.$logo['text'].'</b>';
		}
	}
	$home->assign("e", $e);
	$home->parse("contents.home_detail.logo");
}

$home->parse("contents.home_detail");
$out.=$home->text("contents.home_detail");
?>