<?php

	
	$def=new XTemplate ("././templates/search.xtpl");
	
	
	$language = $_GET['lang'];
	$def->assign("LANG",$language);
	$def->assign("SPR",$spr);
	
	$def->assign("ACTION",$_GET['a']);
	//$def->assign("")
	$def->assign("ID",$_GET['id']);
	$id=$_GET['id'];
	
	if(auth_level(USER_WORKER)){	
		
		$def->assign("NEU","<a href=\"./?a=aedit&amp;id=new&amp;lang=$language\">[&nbsp;".$spr['neuereintrag']."&nbsp;]</a><br><br>");
		$def->parse("ayax_a");
		$text.=$def->text("ayax_a");
		$def->assign("AJAXBAR", "$text<br />");
	}


	for ($an=ord('A');$an<=ord('Z');$an++){
		$def->assign("anf",chr($an));
		$def->assign('ts',($an==ord('Z')?'':''));
		$def->parse("suchen.abuch");
	}
	
	
	
	$def->assign("anf",'andere');
	$def->parse("suchen.abuch");
	
	$fetch[test]="";
	
	
	subgenformitem($def,'edittext',$spr['volltextsuche'],$fetch[test],'volltext');
	
	subgensubmit($def,'submitfield',$spr['submit']);
	
	$def->parse("suchen");
	$search.=$def->text("suchen");

?>

