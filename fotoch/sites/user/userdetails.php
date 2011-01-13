<?php

//include("fotofunc.inc.php");
	testauthedit();
	$def=new XTemplate ("././templates/item_details.xtpl");
	$def->assign("ACTION",$_GET['a']);
	$def->assign("ID",$_GET['id']);
	$def->assign("LANG",$_GET['lang']);
	$lang= $_GET['lang'];
	//$def->assign("DN",$dn);
	$id=$_GET['id'];
	$anf=$_GET['anf'];
	if (!$anf){
		if (auth_level(USER_WORKER) && !$id){
			$ayax=1;
		} else {
			$anf='A';
		}
	}
	$def->assign("TITLE", $spr['user']);
	$def->assign("SPR", $spr);
	$bearbeiten = "&nbsp;&nbsp;[&nbsp;".$spr['bearbeiten']."&nbsp;]";
	
	if (auth_level(USER_GUEST_READER_PARTNER))
	$result=mysql_query("SELECT * FROM users WHERE users.id=$id");
	else  $result=mysql_query("SELECT * FROM users WHERE (users.id=$id)");

	while($fetch=mysql_fetch_array($result, MYSQL_ASSOC)){
		//print_r($fetch);
		$def->assign("ACTION",$_GET['a']);
		$def->assign("ID",$_GET['id']);
		
			//$fetch['name'].=" <a href=\"./?a=iedit&amp;id=$id&amp;lang=$lang\">$bearbeiten</a>";
		$def->assign("bearbeiten"," <a href=\"./?a=uedit&amp;id=$id&amp;lang=$lang\">$bearbeiten</a>");
		normfeld($def,'username',$fetch['username']);
		$def->assign("bearbeiten","");
		
		normfeld($def,$spr['vorname'],$fetch['vorname']);
		normfeld($def,$spr['nachname'],$fetch['nachname']);
		normfeld($def,$spr['email'],$fetch['email']);
		abstand($def);
		normfeld($def,'level',$fetch['level']);
		//abstand($def);
		

		$def->parse("autodetail");
		$results.=$def->text("autodetail");
	}
?>
