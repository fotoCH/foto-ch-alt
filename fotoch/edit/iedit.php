<?php
/////////////////////////////
include("./fotofunc.inc.php");
include("./backend.inc.php");
include("./iedit.inc.php");
//error_reporting((E_ALL));
testauth();
$def=new XTemplate ("./templates/edit.xtpl");
$def->assign("ACTION",$_GET['a']);
$def->assign("id",$_GET['id']);
$lang = $_GET['lang'];
$def->assign("LANG",$lang);

$def->assign("SPR",$spr);

// assign the [ ] functions
$def->assign("EINTRAGLOESCHEN", "[&nbsp;".$spr['eintragloeschen']."&nbsp;]");
$def->assign("BEARBEITEN", "[&nbsp;".$spr['bearbeiten']."&nbsp;]");
$def->assign("LINKLOESCHEN", "[&nbsp;".$spr['linkloeschen']."&nbsp;]");
$def->assign("LOESCHEN", "[&nbsp;".$spr['loeschen']."&nbsp;]");
$def->assign("EINTRAGNEU", "[&nbsp;".$spr['neuereintrag']."&nbsp;]");

if ($_POST) escposts();
if ($_GET[id]=="new"){
	$sql = "INSERT INTO `institution` (`name`,`gesperrt`) VALUES ( 'neue Institution','1')";
	$result = mysql_query($sql);
	$last_insert_id = mysql_insert_id();
}
$del=$_GET['delete'];
if ($del=="2"){
	$id=$_GET['id'];
	$sql = "DELETE FROM `institution` WHERE id=$id LIMIT 1";
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
//////////////Literatur löschen////////////////////////////
if($_GET['l']=="del"){
	$sql = "DELETE FROM `literatur_institution` WHERE id='$_GET[l_id]' LIMIT 1";
	$result = mysql_query($sql);
	$bearbeitungsdatum = date("Y-m-d");
	$sql = "UPDATE `institution` SET `bearbeitungsdatum` = '$bearbeitungsdatum' WHERE `id` ='$_GET[id]' LIMIT 1";
	$result = mysql_query($sql);
}
if($_GET['au']=="del"){
	$sql = "DELETE FROM `ausstellung_institution` WHERE id='$_GET[a_id]' LIMIT 1";
	$result = mysql_query($sql);
	$bearbeitungsdatum = date("Y-m-d");
	$sql = "UPDATE `institution` SET `bearbeitungsdatum` = '$bearbeitungsdatum' WHERE `id` ='$_GET[id]' LIMIT 1";
	$result = mysql_query($sql);
}
//////////////Bestand bearbeiten->speichern////////////////////////////
if($_REQUEST['new_literatur']){
	
	$sql="INSERT INTO `literatur_institution` (`literatur_id`, `institution_id`) VALUES ($_REQUEST[literatur_id],$_REQUEST[id])";
	$result = mysql_query($sql);
	$bearbeitungsdatum = date("Y-m-d");
	$sql = "UPDATE `institution` SET `bearbeitungsdatum` = '$bearbeitungsdatum' WHERE `id` ='$_REQUEST[id]' LIMIT 1";
	$result = mysql_query($sql);
}
if($_REQUEST['new_ausstellung']){
	
	$sql="INSERT INTO `ausstellung_institution` (`ausstellung_id`, `institution_id`) VALUES ($_REQUEST[ausstellung_id],$_REQUEST[id])";
	$result = mysql_query($sql);
	//echo($sql);
	$bearbeitungsdatum = date("Y-m-d");
	$sql = "UPDATE `institution` SET `bearbeitungsdatum` = '$bearbeitungsdatum' WHERE `id` ='$_REQUEST[id]' LIMIT 1";
	$result = mysql_query($sql);
}
//////////////Bildgattugnen zur Speicherung in DB aufbereiten////////////////////////////
if($_POST[submit]){
	foreach ($_POST['bildgattungen'] as $t){
		$bildgattungen_set .=$t;
		$bildgattungen_set .=",";
	}
	$bildgattungen_set = substr($bildgattungen_set, 0, strlen($bildgattungen_set)-1);
	if($_POST['unpubliziert']=="1"){
		$unpubliziert = 1;
	}else{
		$unpubliziert = 0;
	}
	//////////////Formdaten in Tabelle 'fotografen' eintragen bzw aktualisieren////////////////////////////
	$bearbeitungsdatum = date("Y-m-d");
	$sql = "UPDATE `institution` SET `name` = '$_POST[name]',
	`abkuerzung` = '$_POST[abkuerzung]',
	`art` = '$_POST[art]',
	`adresse` = '$_POST[adresse]',
	`plz` = '$_POST[plz]',
	`ort` = '$_POST[ort]',
	`kontaktperson` = '$_POST[kontaktperson]',
	`telefon` = '$_POST[telefon]',
	`fax` = '$_POST[fax]',
	`email` = '$_POST[email]',
	`homepage` = '$_POST[homepage]',
	`zugang_zur_sammlung` = '$_POST[zugang_zur_sammlung]',
	`sammlungszeit_von` = '$_POST[sammlungszeit_von]',
	`sammlungszeit_bis` = '$_POST[sammlungszeit_bis]',
	`bildgattungen_set` = '$bildgattungen_set',
	`sammlungsbeschreibung` = '$_POST[sammlungsbeschreibung]',
	`sammlungsgeschichte` = '$_POST[sammlungsgeschichte]',
	`literatur` = '$_POST[literatur]',
	`bearbeitungsdatum` = '$bearbeitungsdatum',
	`notiz` = '$_POST[notiz]',
	`autorin` = '$_POST[autorin]',
	`gesperrt` = $unpubliziert WHERE `id` =$_POST[hidden_id] LIMIT 1";
	$result = mysql_query($sql);
}
//////////////Grundsätzliches: Template, assigns ect.////////////////////////////
if ($fertig==1){
} else {
	if($last_insert_id){
		$def->assign("ID",$last_insert_id);
		$id=$last_insert_id;
		$def->assign("NEW_ENTRY_MSG", $spr['newentrymsg']."<br/>");
		//$def->assign("LANG", $_GET['lang']);
	}else{
		$def->assign("ID",$_GET['id']);
		$id=$_GET['id'];
		//$def->assign("LANG", $_GET['lang']);
	}
	//////////////Formdaten aus Tabelle 'fotografen'  holen////////////////////////////
	$sql = "SELECT * FROM institution WHERE id ='$id'";
	$result = mysql_query($sql);
	$array_eintrag = mysql_fetch_array($result);
	
	$def->assign("LEGEND", "<b>".$spr['institution_details']."</b>");
	$def->parse("bearbeiten.form.fieldset_start");	
	//$def->parse("bearbeiten.form");	
	$def->parse("bearbeiten.form.start");
	$def->parse("bearbeiten.form");
	
	genformitem($def,'textfield',$spr['name'],$array_eintrag['name'],'name');
	$def->assign('NAME',$array_eintrag['name']);
	$def->assign('g',($array_eintrag['gesperrt']==1?'g':''));
	$def->parse("bearbeiten.bearbeiten_head_institution");
	genformitem($def,'textfield',$spr['abkuerzung'],$array_eintrag['abkuerzung'],'abkuerzung');
	genformitem($def,'textfield',$spr['art'],$array_eintrag['art'],'art');
	genformitem($def,'textfield',$spr['adresse'],$array_eintrag['adresse'],'adresse');
	genformitem($def,'textfield',$spr['plz'],$array_eintrag['plz'],'plz');
	genformitem($def,'textfield',$spr['ort'],$array_eintrag['ort'],'ort');
	genformitem($def,'textfield',$spr['kontaktperson'],$array_eintrag['kontaktperson'],'kontaktperson');
	genformitem($def,'textfield',$spr['telefon'],$array_eintrag['telefon'],'telefon');
	genformitem($def,'textfield',$spr['fax'],$array_eintrag['fax'],'fax');
	genformitem($def,'textfield',$spr['email'],$array_eintrag['email'],'email');
	genformitem($def,'textfield',$spr['homepage'],$array_eintrag['homepage'],'homepage');
	genformitem($def,'editstext',$spr['zugang_zur_sammlung'],$array_eintrag['zugang_zur_sammlung'],'zugang_zur_sammlung');
	genformitem($def,'textfield',$spr['sammlungszeit']." ".$spr['von'],$array_eintrag['sammlungszeit_von'],'sammlungszeit_von');
	genformitem($def,'textfield',$spr['sammlungszeit']." ".$spr['bis'],$array_eintrag['sammlungszeit_bis'],'sammlungszeit_bis');
	$sql ="DESCRIBE institution bildgattungen_set";//Beschreibung des Sets bekommen
	$result = mysql_query($sql);
	$fetch = mysql_fetch_array($result);
	$set_list = $fetch[Type];
	$set_list = substr($set_list, 5, strlen($set_list)-7);
	$array_set_list = explode ("','", $set_list);
	$set= $array_eintrag[bildgattungen_set];
	$array_set = explode (",", $set);
	genselectitem($def, $spr['bildgattungen'], $array_set, "bildgattungen", $array_set_list, "true", "", "");
	genformitem($def,'edittext',$spr['sammlungsgeschichte'],$array_eintrag['sammlungsgeschichte'],'sammlungsgeschichte');
	genformitem($def,'edittext',$spr['sammlungsbeschreibung'],$array_eintrag['sammlungsbeschreibung'],'sammlungsbeschreibung');
	$def->parse("bearbeiten.form.tend");
	$def->parse("bearbeiten.form");
	$def->parse("bearbeiten.form.fieldset_end");	
	mabstand($def);
	
	
	$def->assign("LEGEND","<b>".$spr['bestaende']."</b>");
	$def->parse("bearbeiten.form.fieldset_start");
	bestaende($def,$id);
	$def->parse("bearbeiten.form.fieldset_end");
	
	$def->parse("bearbeiten.form");
	mabstand($def);
	
	$def->assign("LEGEND","<b>".$spr['literatur']."</b>");
	$def->parse("bearbeiten.form.fieldset_start");	
	literatur($def,$id);
	$def->parse("bearbeiten.form.fieldset_end");
	
	$def->parse("bearbeiten.form");
	mabstand($def);
	
	$def->assign("LEGEND","<b>".$spr['ausstellungen']."</b>");
	$def->parse("bearbeiten.form.fieldset_start");	
	ausstellungen($def,$id);
	$def->parse("bearbeiten.form.fieldset_end");	
	$def->parse("bearbeiten.form");
	mabstand($def);
	
	$def->assign("LEGEND", "<b>".$spr['institution_zusatz']."</b>");
	$def->parse("bearbeiten.form.fieldset_start");	
	$def->parse("bearbeiten.form.start");
	//$def->parse("bearbeiten.form");
	
	genformitem($def,'edittext',$spr['notiz'],$array_eintrag['notiz'],'notiz');
	genformitem($def,'textfield',$spr['autorIn'],$array_eintrag['autorin'],'autorin');
	gencheckitem($def,$spr['npublizieren'],$array_eintrag['gesperrt'],'unpubliziert');	
	$def->assign("bearbeitungsdatum", $array_eintrag['bearbeitungsdatum']);
	$def->parse("bearbeiten.form.fieldset_end");
	//$def->parse("bearbeiten.form");	
	$def->parse("bearbeiten");
	$out.=$def->text("bearbeiten");
}
?>