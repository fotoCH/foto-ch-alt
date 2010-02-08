<?php
/////////////////////////////
include("./fotofunc.inc.php");
include("./backend.inc.php");
testauth();
$def=new XTemplate ("./templates/edit.xtpl");
$def->assign("ACTION",$_GET['a']);
$def->assign("id",$_GET['id']);
$lang = $_GET['lang'];
$def->assign("LANG", $lang);
$def->assign("EINTRAGLOESCHEN", "[&nbsp;".getLangContent("sprache", $lang, "eintragloeschen")."&nbsp;]");
$def->assign("SPEICHERN", getLangContent("sprache", $lang, "speichern"));
$def->assign("VERKNUEPFUNGEN", getLangContent("sprache", $lang, "verknuepfungen"));
//$def->assign("VERKNUEPFUNG", getLangContent("sprache", $lang, "verknuepfung"));
$def->assign("ANSEHEN", getLangContent("sprache", $lang, "ansehen"));
$def->assign("AUSSTELLUNGBEARBEITEN", getLangContent("sprache", $lang, "ausstellungbearbeiten"));
$def->assign("JA", getLangContent("sprache", $lang, "ja"));
$def->assign("NEIN", getLangContent("sprache", $lang, "nein"));
if ($_POST) escposts();
if ($_GET['id']=="new"){
	$sql = "INSERT INTO `ausstellung` ( `id` , `titel` , `jahr` , `ort` , `institution` , `typ` , `code` , `text_alt` ) VALUES (NULL , '', NULL , NULL , NULL , 'E', NULL , '')";
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
if($_POST['submit']){
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
	`code` = '$_POST[code]',
	`typ` = '$_POST[typ]',
	`notiz` = '$_POST[notiz]',
	`bearbeitungsdatum` = '$bearbeitungsdatum',
	`text_alt` = '$_POST[text_alt]' WHERE `id` =$_POST[hidden_id] LIMIT 1";
	$result = mysql_query($sql);
}
//////////////Grundsätzliches: Template, assigns ect.////////////////////////////
if ($fertig==1){
} else {
	if($last_insert_id){
		$def->assign("ID",$last_insert_id);
		$id=$last_insert_id;
		$newentrymsg = getLangContent("sprache", $lang, "newentrymsg");
		$def->assign("NEW_ENTRY_MSG", "<h3>$newentrymsg</h3><br/>");
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
	$ausstellungsdetails = getLangContent("sprache", $lang,"ausstellung_details");
	$def->assign("LEGEND", "<b>$ausstellungsdetails</b><br/>");
	$def->parse("bearbeiten.form.fieldset_start");
	$def->parse("bearbeiten.form.start");
	
	genformitem($def,'textfield',getLangContent("sprache", $lang, "jahr"),$array_eintrag['jahr'],'jahr');
	genformitem($def,'textfield',getLangContent("sprache", $lang, "ort"),$array_eintrag['ort'],'ort');
	genformitem($def,'textfield',getLangContent("sprache", $lang, "institution"),$array_eintrag['institution'],'institution');
	genformitem($def,'textfield',getLangContent("sprache", $lang, "titel"),$array_eintrag['titel'],'titel');
	$arr_typ=array("E" =>"E", "G" =>"G");   //Array füllen für Select
	genselectitem($def, getLangContent("sprache", $lang, "typ"), $array_eintrag['typ'], "typ", $arr_typ, "", "", "");
	genformitem($def,'textfield',getLangContent("sprache", $lang, "notiz"),$array_eintrag['notiz'],'notiz');	
	$def->assign("BEARBEITUNGSDATUM", getLangContent("sprache", $lang, "bearbeitungsdatum"));
	$def->assign("bearbeitungsdatum", $array_eintrag['bearbeitungsdatum']);
	if(auth()){
		$neuereintrag = "[&nbsp;".getLangContent("sprache",$_GET['lang'], "neuereintrag")."&nbsp;]";
		$def->assign("NEU"," | <a href=\"./?a=aedit&amp;id=new\">$neuereintrag</a>");
	}else{
		$def->assign("NEU","");
	}
	$def->parse("bearbeiten.form.fieldset_end");
	$def->parse("bearbeiten");
	$out.=$def->text("bearbeiten");
	//$def->out("bearbeiten");
}
?>
