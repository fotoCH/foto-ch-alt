<?php

$partner=new XTemplate ("./templates/contents.xtpl");

$partner->assign('SPR',$spr);
$logos=getLogos();
//print_r($logos);
foreach ($logos as $logo){

	if ($logo['link']){
		$e['text']='<a href="'.$logo['link'].'" target="_new">'.$logo['text'].'</a>';
		if ($logo['bild']){
			$e['img']='<a href="'.$logo['link'].'" target="_new"><img width="'.$logo['width'].'" border="0" src="Logos/'.$logo['bild'].'" /></a>';
		} else {
			$e['img']=' &nbsp; ';
		}
	} else {
		$e['text']='<b>'.$logo['text'].'</b>';
		if ($logo['bild']){
			$e['img']='<img alt="'.$logo['text'].'" width=""'.$logo['width'].'" border="0" src="Logos/'.$logo['bild'].'" />';
		} else {
			$e['img']=' &nbsp; ';
		}
	}
	$partner->assign("e", $e);
	$partner->parse("contents.partner_content.row");
}

$partner->parse("contents.partner_content");
$out.=$partner->text("contents.partner_content");

?>