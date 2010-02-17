<?php
/////////////////////////////
include("./fotofunc.inc.php");
include("./backend.inc.php");
testauth();
$def=new XTemplate ("./templates/edit.xtpl");
$def->assign("ACTION",$_GET['a']);
$def->assign("id",$_GET['id']);
$lang = $_GET['lang'];
$def->assign("LANG",$lang);

$def->assign("SPR",$spr);

$def->assign("EINTRAGLOESCHEN", "[&nbsp;".$spr['eintragloeschen']."&nbsp;]");
$def->assign("EINTRAGNEU", "[&nbsp;".$spr['neuereintrag']."&nbsp;]");

if ($_POST) escposts();

if ($_GET['id']=="new"){
	$sql="INSERT INTO `glossar` ( `id` , `begriff` , `zeitraum` , `erlaeuterung` , `literatur` , `bearbeitungsdatum` ) VALUES (NULL,'','','','','0000-00-00')";
	$result = mysql_query($sql);

	$last_insert_id = mysql_insert_id();

}

$del=$_GET['delete'];

if ($del=="2"){
	$id=$_GET['id'];
	$sql = "DELETE FROM `glossar` WHERE id=$id LIMIT 1";
	echo $sql;
	$result = mysql_query($sql);



	$def->parse("loeschen2");
	$out.=$def->text("loeschen2");
	$fertig=1;
}

if ($del=="1"){

	$def->parse("loeschen1");
	$out.=$def->text("loeschen1");

	$fertig=1;
}


//////////////Bildgattugnen zur Speicherung in DB aufbereiten////////////////////////////
if($_POST['submitbutton']){


	if($_POST['unpubliziert']=="1"){
		$unpubliziert = 1;
	}else{
		$unpubliziert = 0;
	}

	//////////////Formdaten in Tabelle 'fotografen' eintragen bzw aktualisieren////////////////////////////

	$bearbeitungsdatum = date("Y-m-d");
	$sql = "UPDATE `glossar` SET `begriff` = '$_POST[begriff]',
	`zeitraum` = '$_POST[zeitraum]',
	`erlaeuterung` = '$_POST[erlaeuterung]',
	`literatur` = '$_POST[literatur]',
	`bearbeitungsdatum` = '$bearbeitungsdatum'
	WHERE `id` =$_POST[hidden_id] LIMIT 1";
	$result = mysql_query($sql);
}

//////////////Grundsätzliches: Template, assigns ect.////////////////////////////
if ($fertig==1){
} else {
	if($last_insert_id){
		$def->assign("ID",$last_insert_id);
		$id=$last_insert_id;
		//$def->assign("LANG",$_GET['lang']);
		$def->assign("NEW_ENTRY_MSG", $spr['newentrymsg']."<br/>");
	}else{
		$def->assign("ID",$_GET['id']);
		$id=$_GET['id'];
		$lang = $_GET['lang'];
		//$def->assign("LANG",$lang);
	}

	

	//////////////Formdaten aus Tabelle 'fotografen'  holen////////////////////////////
	$sql = "SELECT * FROM `glossar` WHERE id ='$id'";
	$result = mysql_query($sql);
	$array_eintrag = mysql_fetch_array($result);
	
	$def->assign("NAME", $array_eintrag['begriff']);
	$def->parse("bearbeiten.bearbeiten_head_glossar");
	
	$def->assign("LEGEND", "<b>".$spr['glossardetails']."</b><br/>");
	$def->parse("bearbeiten.form.fieldset_start");
	
	//$def->parse("bearbeiten.new_bestand");

	$def->parse("bearbeiten.form.start");


	genformitem($def,'textfield',$spr['begriff'],$array_eintrag['begriff'],'begriff');

	genformitem($def,'textfield',$spr['zeitraum'],$array_eintrag['zeitraum'],'zeitraum');

	genformitem($def,'edittext',$spr['erlaeuterung'],$array_eintrag['erlaeuterung'],'erlaeuterung');
	genformitem($def,'edittext',$spr['literatur'],$array_eintrag['literatur'],'literatur');
	
	//$def->assign("BEARBEITUNGSDATUM", $spr['bearbeitungsdatum']);
	$def->assign("bearbeitungsdatum", $array_eintrag['bearbeitungsdatum']);

	
	/*if(auth_level($USER_WORKER)){
		//$def->assign("NEU"," | <a href=\"./?a=gedit&amp;id=new&amp;lang=$lang\">$neuereintrag</a>");
	}else{
		//$def->assign("NEU","");
	}*/

	$def->parse("bearbeiten.form.fieldset_end");
	$def->parse("bearbeiten");
	$out.=$def->text("bearbeiten");
	//$def->out("bearbeiten");
}
?>