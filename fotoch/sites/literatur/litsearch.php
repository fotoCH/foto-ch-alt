<?php

	
	$def=new XTemplate ("././templates/search.xtpl");
	
	
	$language = $_GET['lang'];
	$def->assign("LANG",$language);
	
	$def->assign("ACTION",$_GET['a']);
	//$def->assign("")
	$def->assign("ID",$_GET['id']);
	$id=$_GET['id'];
	
	if(auth()){
		
	
		$neuereintrag = getLangContent("sprache",$_GET['lang'],"neuereintrag");
		$def->assign("NEU","<a href=\"./?a=ledit&amp;id=new&amp;lang=$language\">[&nbsp;$neuereintrag&nbsp;]</a><br /><br />");
		
		$def->assign("ANZEIGEN",getLangContent("sprache",$_GET['lang'],"anzeigen"));
		$def->assign("NACHTITEL",getLangContent("sprache",$_GET['lang'],"nach_titel"));
		$def->assign("NACHVERFASSER",getLangContent("sprache",$_GET['lang'],"nach_verfasser"));
		$def->parse("ayax_l");
		$text.=$def->text("ayax_l");
		$def->assign("AJAXBAR", "$text<br />");	
		//$search.=$def->text("ayax_f");	
	}


	for ($an=ord('A');$an<=ord('Z');$an++){
		$def->assign("anf",chr($an));
		$def->assign('ts',($an==ord('Z')?'':''));
		$def->parse("suchen.abuch");
	}
	
	
	
	$def->assign("anf",'keiner');
	$def->parse("suchen.abuch");
	$def->assign("anf",'in');
	$def->parse("suchen.abuch");
	$def->assign("anf",'zeitschrift');
	$def->parse("suchen.abuch");
	$def->assign("anf",'url');
	$def->assign('ts','');
	$def->parse("suchen.abuch");

	
	$fetch[test]="";
	
	$volltextsuche = getLangContent("sprache", $language, "volltextsuche");
	subgenformitem($def,'edittext',$volltextsuche,$fetch[test],'volltext');
	$submit = getLangContent("sprache", $language, "submit");
	subgensubmit($def,'submitfield',$submit);
	//
	//$suche = "<a href=\"./?a=repertorium&amp;mod=erw\">erweiterte Suche</a>";

	//$def->assign("SUCHE", $suche);
	$def->parse("suchen");
	$search.=$def->text("suchen");

?>

