<?php
/////////////////////////////
include("./fotofunc.inc.php");
include("./backend.inc.php");
testauthedit();
$def=new XTemplate ("./templates/edit.xtpl");
$def->assign("ACTION",$_GET['a']);
$def->assign("id",$_GET['id']);
$lang = $_GET['lang'];
$def->assign("LANG", $lang);

$def->assign("SPR",$spr);

$def->assign("EINTRAGLOESCHEN", "[&nbsp;".$spr['eintragloeschen']."&nbsp;]");
$def->assign("EINTRAGNEU", "|&nbsp;&nbsp;&nbsp;[&nbsp;".$spr['neuereintrag']."&nbsp;]");


if ($_POST) escposts();
if ($_GET['id']=="new"){
	$sql = "INSERT INTO `ausstellung` ( `id` , `titel` , `jahr` , `ort` , `institution` , `typ` ) VALUES (NULL , '', NULL , NULL , NULL , 'E')";
	$result = mysql_query($sql);
	$last_insert_id = mysql_insert_id();
}
$del=$_GET['delete'];
if ($del=="2"){
	$id=$_GET['id'];
	$sql = "DELETE FROM `ausstellung` WHERE id=$id LIMIT 1";
	//echo $sql;
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
	$sql = "UPDATE `ausstellung` SET `titel` = '$_POST[titel]',
	`jahr` = '$_POST[jahr]',
	`ort` = '$_POST[ort]',
	`institution` = '$_POST[institution]',
	`typ` = '$_POST[typ]',
	`notiz` = '$_POST[notiz]',
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
		$def->assign("NEW_ENTRY_MSG", "<h3>".$spr['newentrymsg']."</h3><br/>");
		$lang = $_GET['lang'];
		$def->assign("LANG", $lang);
	}else{
		$def->assign("ID",$_GET['id']);
		$id=$_GET['id'];
		$lang = $_GET['lang'];
		$def->assign("LANG", $lang);
	}
	
	//////////////Formdaten aus Tabelle 'fotografen'  holen////////////////////////////
	$sql = "SELECT * FROM ausstellung WHERE id ='$id'";
	$result = mysql_query($sql);
	$array_eintrag = mysql_fetch_array($result);
	
	$def->assign("TITEL", $array_eintrag['titel']);
	$def->parse("bearbeiten.bearbeiten_head_ausstellung");
	$def->assign("LEGEND", "<b>".$spr['ausstellung_details']."</b><br/>");
	$def->parse("bearbeiten.form.fieldset_start");
	$def->parse("bearbeiten.form.start");
	
	genformitem($def,'textfield',$spr['jahr'],$array_eintrag['jahr'],'jahr');
	genformitem($def,'textfield',$spr['ort'],$array_eintrag['ort'],'ort');
	genformitem($def,'textfield',$spr['institution'],$array_eintrag['institution'],'institution');
	genformitem($def,'textfield',$spr['titel'],$array_eintrag['titel'],'titel');
	$arr_typ=array("E" =>"E", "G" =>"G");   //Array füllen für Select
	genselectitem($def, $spr['typ'], $array_eintrag['typ'], "typ", $arr_typ, "", "", "");
	genformitem($def,'textfield',$spr['notiz'],$array_eintrag['notiz'],'notiz');	
	$def->assign("BEARBEITUNGSDATUM", $spr['bearbeitungsdatum']);
	$def->assign("bearbeitungsdatum", $array_eintrag['bearbeitungsdatum']);
	$def->assign("NEU"," | <a href=\"./?a=aedit&amp;id=new\">".$spr['neuereintrag']."</a>");
	
	$def->parse("bearbeiten.form.fieldset_end");
	$def->parse("bearbeiten.bearbeitungsdatum");
	$def->parse("bearbeiten.speichern.neuloeschen");
	$def->parse("bearbeiten.speichern");
	$def->parse("bearbeiten");
	$out.=$def->text("bearbeiten");
}

?>
