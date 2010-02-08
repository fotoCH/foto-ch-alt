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
$def->assign("EINTRAGLOESCHEN", "[&nbsp;".getLangContent("sprache", $lang, "eintragloeschen")."&nbsp;]");
$def->assign("SPEICHERN", getLangContent("sprache", $lang, "speichern"));
$def->assign("LITERATURBEARBEITEN", getLangContent("sprache", $lang, "literaturbearbeiten"));
$def->assign("VERKNUEPFUNGEN", getLangContent("sprache", $lang, "verknuepfungen"));
$def->assign("ANSEHEN", getLangContent("sprache", $lang, "ansehen"));
$def->assign("JA", getLangContent("sprache", $lang, "ja"));
$def->assign("NEIN", getLangContent("sprache", $lang, "nein"));
if ($_POST) escposts();
if ($_GET[id]=="new"){
	$sql = "INSERT INTO `literatur` ( `id` , `titel` , `verfasser_name` , `verfasser_vorname` , `jahr` , `ort` , `in` , `nummer` , `seite` , `code` , `text` ) VALUES (NULL , '', NULL , NULL , NULL , NULL , NULL , NULL , NULL , NULL , NULL)";
	$result = mysql_query($sql);
	$last_insert_id = mysql_insert_id();
}
$del=$_GET['delete'];
if ($del=="2"){
	$id=$_GET['id'];
	$sql = "DELETE FROM `literatur` WHERE id=$id LIMIT 1";
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
if($_POST[submit]){
	//////////////Formdaten in Tabelle 'fotografen' eintragen bzw aktualisieren////////////////////////////
	$bearbeitungsdatum = date("Y-m-d");
	$sql="UPDATE `literatur` SET `titel` = '$_POST[titel]',
	`verfasser_name` = '$_POST[verfasser_name]',
	`verfasser_vorname` = '$_POST[verfasser_vorname]',
	`jahr` = '$_POST[jahr]',
	`ort` = '$_POST[ort]',
	`in` = '$_POST[in]',
	`nummer` = '$_POST[nummer]',
	`seite` = '$_POST[seite]',
	`code` = '$_POST[code]',
	`url` = '$_POST[url]',
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
		$def->assign("LANG", $_GET['lang']);
		$newentrymsg = getLangContent("sprache",$_GET['lang'],"newentrymsg");
		$def->assign("NEW_ENTRY_MSG", "$newentrymsg<br/>");
	}else{
		$def->assign("ID",$_GET['id']);
		$def->assign("LANG", $_GET['lang']);
		$id=$_GET['id'];
	}
	//////////////Formdaten aus Tabelle 'fotografen'  holen////////////////////////////
	$sql = "SELECT * FROM literatur WHERE id ='$id'";
	$result = mysql_query($sql);
	$array_eintrag = mysql_fetch_array($result);
	
	$def->assign("TITEL",$array_eintrag['titel']);
	$def->parse("bearbeiten.bearbeiten_head_literatur");
	$literaturdetails = getLangContent("sprache", $lang,"literatur_details");
	$def->assign("LEGEND","<b>$literaturdetails</b><br/>");
	$def->parse("bearbeiten.form.fieldset_start");
	$def->parse("bearbeiten.form.start");
	genformitem($def,'textfield',getLangContent("sprache", $lang, "verfasser_name"),$array_eintrag['verfasser_name'],'verfasser_name');
	genformitem($def,'textfield',getLangContent("sprache", $lang, "verfasser_vorname"),$array_eintrag['verfasser_vorname'],'verfasser_vorname');
	genformitem($def,'textfield',getLangContent("sprache", $lang, "titel"),$array_eintrag['titel'],'titel');
	genformitem($def,'textfield',getLangContent("sprache", $lang, "ort"),$array_eintrag['ort'],'ort');
	genformitem($def,'textfield',getLangContent("sprache", $lang, "jahr"),$array_eintrag['jahr'],'jahr');
	genformitem($def,'textfield',getLangContent("sprache", $lang, "in"),$array_eintrag['in'],'in');
	genformitem($def,'textfield',getLangContent("sprache", $lang, "nummer"),$array_eintrag['nummer'],'nummer');
	genformitem($def,'textfield',getLangContent("sprache", $lang, "seite"),$array_eintrag['seite'],'seite');
	genformitem($def,'textfield',getLangContent("sprache", $lang, "url"),$array_eintrag['url'],'url');
	
	$arr_code=array("" => "", "U" =>"Url", "P" =>"Periodika", "T" =>"Tageszeitung", "H" =>"Herausgeber", "Z" =>"Zitiert in", "V" =>"Verlag statt Ort");   //Array füllen für Select
	genselectitem($def, getLangContent("sprache", $lang, "code"), $array_eintrag['code'], "code", $arr_code, "", "", "");
	genformitem($def,'textfield',getLangContent("sprache", $lang, "notiz"),$array_eintrag['notiz'],'notiz');
	if(auth()){  //  ??
		//$def->assign("NEU"," | <a href=\"./?a=ledit&amp;id=new\">neuer Eintrag</a>");
	}else{
		//$def->assign("NEU","");
	}
	$def->assign("bearbeitungsdatum", $array_eintrag['bearbeitungsdatum']);
	$def->assign("BEARBEITUNGSDATUM", getLangContent("sprache", $lang, "bearbeitungsdatum"));
	
	$def->parse("bearbeiten.form.fieldset_start");
	$def->parse("bearbeiten");
	$out.=$def->text("bearbeiten");
	//$def->out("bearbeiten");
}
?>
