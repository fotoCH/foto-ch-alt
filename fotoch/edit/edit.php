<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set ('error_reporting', E_ALL);
/////////////////////////////
include("./fotofunc.inc.php");
include("./backend.inc.php");
include("./edit.inc.php");
testauth();
//include("../templates/xtpl.p");
$def=new XTemplate ("./templates/edit.xtpl");
$def->assign("ACTION",$_GET['a']);
$def->assign("id",$_GET['id']);
$def->assign("LANG", $_GET['lang']);
$lang = $_GET['lang'];
$def->assign("FOTOGRAPHBEARBEITEN", getLangContent("sprache", $lang, "fotographbearbeiten"));
$def->assign("NACHNAME", getLangContent("sprache", $lang, "name"));
$def->assign("VORNAME", getLangContent("sprache", $lang, "vorname"));
$def->assign("TITEL", getLangContent("sprache", $lang, "titel"));
$def->assign("ZUSATZ", getLangContent("sprache", $lang, "zusatz"));
$def->assign("SPEICHERN", getLangContent("sprache", $lang, "speichern"));
$def->assign("LOESCHEN", "[&nbsp;".getLangContent("sprache", $lang, "loeschen")."&nbsp;]");
$def->assign("BEARBEITEN", "[&nbsp;".getLangContent("sprache", $lang, "bearbeiten")."&nbsp;]");
$def->assign("ANSEHEN", getLangContent("sprache", $lang, "ansehen"));
$def->assign("VERKNUEPFUNGEN", getLangContent("sprache", $lang, "verknuepfungen"));
$def->assign("VERKNUEPFEN", getLangContent("sprache", $lang, "verknuepfen"));
$def->assign("ORT", getLangContent("sprache", $lang, "ort"));
$def->assign("JAHR", getLangContent("sprache", $lang, "jahr"));
$def->assign("ZEITRAUM", getLangContent("sprache", $lang, "zeitraum"));
$def->assign("BESTAND", getLangContent("sprache", $lang, "bestand"));
$def->assign("NACHTITEL", getLangContent("sprache", $lang, "nach_titel"));
$def->assign("NACHVERFASSER", getLangContent("sprache", $lang, "nach_verfasser"));
$def->assign("NEUELITERATUR", getLangContent("sprache", $lang, "neue_literatur"));
$def->assign("NEUEAUSSTELLUNGEN", getLangContent("sprache", $lang, "neue_ausstellungen"));
$def->assign("NEUERBESTAND", getLangContent("sprache", $lang, "neuer_bestand"));
$def->assign("NEUERARBEITSORT", getLangContent("sprache", $lang, "neuer_arbeitsort"));
$def->assign("ARBEITSORTNAME", getLangContent("sprache", $lang, "arbeitsort"));
$def->assign("INSTITUTIONNAME", getLangContent("sprache", $lang, "institution"));
$def->assign("INSTITUTIONBEARBEITEN", "[&nbsp;".getLangContent("sprache", $lang, "institution_bearbeiten")."&nbsp;]");
$def->assign("UMFANG", getLangContent("sprache", $lang, "umfang"));
$def->assign("TYP", getLangContent("sprache", $lang, "typ"));
$def->assign("BEZEICHNUNGEN", getLangContent("sprache", $lang, "bezeichnungen"));
$def->assign("BEZEICHNUNG", getLangContent("sprache", $lang, "bezeichnung"));
$def->assign("VON", getLangContent("sprache", $lang, "von"));
$def->assign("BIS", getLangContent("sprache", $lang, "bis"));
$def->assign("ARBEITSORTHINZUFUEGEN",  "[&nbsp;".getLangContent("sprache", $lang, "arbeitsort_hinzufuegen")."&nbsp;]");
$def->assign("bestand", getLangContent("sprache", $lang, "bestand"));
$def->assign("NEUEAUSSTELLUNG", getLangContent("sprache", $lang, "neue_ausstellung"));
$def->assign("EINTRAGLOESCHEN", "[&nbsp;".getLangContent("sprache", $lang, "eintragloeschen")."&nbsp;]");
$def->assign("BEZEICHNUNGHINZUFUEGEN", "[&nbsp;".getLangContent("sprache", $lang, "bezeichnung_hinzufuegen")."&nbsp;]");
$def->assign("JA",getLangContent("sprache", $lang, "ja"));
$def->assign("NEIN",getLangContent("sprache", $lang, "nein"));
$def->assign("ausstellung",getLangContent("sprache", $_GET['lang'], "ausstellung"));
$def->assign("literatur",getLangContent("sprache", $_GET['lang'], "literatur"));
if ($_POST) escposts();
if ($_GET['id']=="new"){
	$sql = "INSERT INTO `fotografen` ( `id` , `nachname` , `vorname` , `namenszusatz` , `zweitname` , `art` , `geschlecht` , `heimatort` , 			`gen_geburtsdatum` , `geburtsdatum` , `geburtsort` , `gen_todesdatum` , `todesdatum` , `todesort` , `umfeld` , `notiz` , `primaerliteratur` , 	`sekundaerliteratur` , `beruf` , `einzelausstellungen` , `gruppenausstellungen` , `werdegang` , `schaffensbeschrieb` , `autorIn` , 		`bearbeitungsdatum` , `fotografengattungen_set` , `bildgattungen_set` )
	VALUES ('', '', '', '', '', 'P', '', '', '0', '0000-00-00', '', '0', '0000-00-00', '', '', '', '', '', '', '', '', '', '', '', '0000-00-00', '', '')";
	$result = mysql_query($sql);
	$last_insert_id = mysql_insert_id();
	$sql = "INSERT INTO `namen` ( `id` , `fotografen_id` , `nachname` , `vorname` , `namenszusatz` , `titel`  )
	VALUES ('', '$last_insert_id', 'Neueintrag', '', '', '')";
	$result = mysql_query($sql);
}
$del=$_GET['delete'];
if ($del=="2"){
	$id=$_GET['id'];
	$sql = "DELETE FROM `fotografen` WHERE id=$id LIMIT 1";
	//echo $sql;
	$result = mysql_query($sql);
	$sql = "DELETE FROM `namen` WHERE fotografen_id=$id LIMIT 1";
	$result = mysql_query($sql);
	$def->parse("loeschen2");
	$out.=$def->text("loeschen2");
	$fertig=1;
}
if ($del=="1"){
	$def->parse("loeschen1");
	$out.=$def->text("loeschen1");
	//echo("bbcc");
	$fertig=1;
}
//////////////Namen löschen////////////////////////////
if($_GET['n']=="del"){
	$sql = "DELETE FROM `namen` WHERE id='$_GET[n_id]'";
	$result = mysql_query($sql);
	$bearbeitungsdatum = date("Y-m-d");
	$sql = "UPDATE `fotografen` SET `bearbeitungsdatum` = '$bearbeitungsdatum' WHERE `id` ='$_GET[id]' LIMIT 1";
	$result = mysql_query($sql);
}
//////////////Bezeichnung(Namen)erstellen////////////////////////////
if($_GET['n']=="new"){
	$sql = "INSERT INTO `namen` ( `id` , `fotografen_id` , `nachname` , `vorname` , `namenszusatz` , `titel` )
	VALUES ('', '$_GET[id]', '', '', '', '')";
	$result = mysql_query($sql);
}
//////////////Bezeichnung(Namen) bearbeiten->speichern////////////////////////////
if($_REQUEST['submit_namen']){
	$sql="UPDATE `namen` SET `nachname` = '$_REQUEST[nachname]',
	`vorname` = '$_REQUEST[vorname]',
	`namenszusatz` = '$_REQUEST[zusatz]',
	`titel` = '$_REQUEST[titel]' WHERE `id` ='$_REQUEST[namen_id]' LIMIT 1";
	$result = mysql_query($sql);
	$bearbeitungsdatum = date("Y-m-d");
	$sql = "UPDATE `fotografen` SET `bearbeitungsdatum` = '$bearbeitungsdatum' WHERE `id` ='$_REQUEST[fotografen_id]' LIMIT 1";
	$result = mysql_query($sql);
}
//////////////Bestand löschen////////////////////////////
if($_GET['b']=="del"){
	$sql = "DELETE FROM `bestand_fotograf` WHERE id='$_GET[b_id]' LIMIT 1";
	$result = mysql_query($sql);
	$bearbeitungsdatum = date("Y-m-d");
	$sql = "UPDATE `fotografen` SET `bearbeitungsdatum` = '$bearbeitungsdatum' WHERE `id` ='$_GET[id]' LIMIT 1";
	$result = mysql_query($sql);
}
//////////////Literatur löschen////////////////////////////
if($_GET['l']=="del"){
	$sql = "DELETE FROM `literatur_fotograf` WHERE id='$_GET[l_id]' LIMIT 1";
	$result = mysql_query($sql);
	//echo($sql);
	$bearbeitungsdatum = date("Y-m-d");
	$sql = "UPDATE `fotografen` SET `bearbeitungsdatum` = '$bearbeitungsdatum' WHERE `id` ='$_GET[id]' LIMIT 1";
	$result = mysql_query($sql);
}
if($_GET['au']=="del"){
	$sql = "DELETE FROM `ausstellung_fotograf` WHERE id='$_GET[a_id]' LIMIT 1";
	$result = mysql_query($sql);
	//echo($sql);
	$bearbeitungsdatum = date("Y-m-d");
	$sql = "UPDATE `fotografen` SET `bearbeitungsdatum` = '$bearbeitungsdatum' WHERE `id` ='$_GET[id]' LIMIT 1";
	$result = mysql_query($sql);
}
//////////////Bestand erstellen////////////////////////////
//////////////neuer Bestand einfügen////////////////////////////
if($_REQUEST['new_bestand']){
	$sql="INSERT INTO `bestand_fotograf` (`bestand_id`, `fotografen_id`) VALUES ($_REQUEST[bestand_id],$_REQUEST[id])";
	$result = mysql_query($sql);
	$bearbeitungsdatum = date("Y-m-d");
	$sql = "UPDATE `fotografen` SET `bearbeitungsdatum` = '$bearbeitungsdatum' WHERE `id` ='$_REQUEST[id]' LIMIT 1";
	$result = mysql_query($sql);
	$sql = "UPDATE `bestand` SET `bearbeitungsdatum` = '$bearbeitungsdatum' WHERE `id` ='$_REQUEST[bestand_id]' LIMIT 1";
	$result = mysql_query($sql);
}
if($_REQUEST['new_literatur']){
	$sql="INSERT INTO `literatur_fotograf` (`literatur_id`, `fotografen_id`, `typ`) VALUES ($_REQUEST[literatur_id],$_REQUEST[id],'$_REQUEST[typ]')";
	$result = mysql_query($sql);
	$bearbeitungsdatum = date("Y-m-d");
	//echo $sql;
	$sql = "UPDATE `fotografen` SET `bearbeitungsdatum` = '$bearbeitungsdatum' WHERE `id` ='$_REQUEST[id]' LIMIT 1";
	$result = mysql_query($sql);
}
if($_REQUEST['new_ausstellung']){
	$sql="INSERT INTO `ausstellung_fotograf` (`ausstellung_id`, `fotograf_id`) VALUES ($_REQUEST[ausstellung_id],$_REQUEST[id])";
	$result = mysql_query($sql);
	$bearbeitungsdatum = date("Y-m-d");
	$sql = "UPDATE `fotografen` SET `bearbeitungsdatum` = '$bearbeitungsdatum' WHERE `id` ='$_REQUEST[id]' LIMIT 1";
	$result = mysql_query($sql);
}
//////////////Arbeitsperiode löschen////////////////////////////
if($_GET['ap']=="del"){
	$sql = "DELETE FROM `arbeitsperioden` WHERE id='$_GET[a_id]'";
	$result = mysql_query($sql);
	$bearbeitungsdatum = date("Y-m-d");
	$sql = "UPDATE `fotografen` SET `bearbeitungsdatum` = '$bearbeitungsdatum' WHERE `id` ='$_GET[id]' LIMIT 1";
	$result = mysql_query($sql);
}
//////////////Arbeitsperiode erstellen////////////////////////////
if($_GET['ap']=="new"){
	$sql = "INSERT INTO `arbeitsperioden` ( `id` , `fotografen_id` , `arbeitsort` , `von` , `um_von` , `bis` , `um_bis` )
	VALUES ('', '$_GET[id]', '', '', '0', '', '0')";
	$result = mysql_query($sql);
}
//////////////Arbeitsperiode bearbeiten->speichern////////////////////////////
if($_REQUEST['submit_arbeitsort']){
	if($_REQUEST['umvon']=="1"){
		$um_von = 1;
	}else{
		$um_von = 0;
	}
	if($_REQUEST['umbis']=="1"){
		$um_bis = 1;
	}else{
		$um_bis = 0;
	}
	$sql="UPDATE `arbeitsperioden` SET `arbeitsort` = '$_REQUEST[arbeitsort]',
	`von` = '$_REQUEST[von]',
	`um_von` = '$um_von',
	`bis` = '$_REQUEST[bis]',
	`um_bis` = '$um_bis' WHERE `id` = '$_REQUEST[arbeitsort_id]'";
	//echo($sql);
	$result = mysql_query($sql);
	$bearbeitungsdatum = date("Y-m-d");
	$sql = "UPDATE `fotografen` SET `bearbeitungsdatum` = '$bearbeitungsdatum' WHERE `id` ='$_REQUEST[fotografen_id]' LIMIT 1";
	$result = mysql_query($sql);
}
//////////////Bildgattugnen zur Speicherung in DB aufbereiten////////////////////////////
if($_POST['submit']){
	foreach ($_REQUEST['bildgattungen'] as $t){
		$bildgattungen_set .=$t;
		$bildgattungen_set .=",";
	}
	$bildgattungen_set = substr($bildgattungen_set, 0, strlen($bildgattungen_set)-1);
	//////////////Fotografengattugnen zur Speicherung in DB aufbereiten////////////////////////////
	foreach ($_REQUEST['fotografengattungen'] as $t){
		$fotografengattungen_set .=$t;
		$fotografengattungen_set .=",";
	}
	foreach ($_REQUEST['kanton'] as $t){
		$kanton .=$t;
		$kanton .=",";
	}
	$kanton = substr($kanton, 0, strlen($kanton)-1);
	if($_POST['unpubliziert']=="1"){
		$unpubliziert = 1;
	}else{
		$unpubliziert = 0;
	}
	//////////////Formdaten in Tabelle 'fotografen' eintragen bzw aktualisieren////////////////////////////
	$fotografengattungen_set = substr($fotografengattungen_set, 0, strlen($fotografengattungen_set)-1);
	$bearbeitungsdatum = date("Y-m-d");
	$sql = "UPDATE `fotografen` SET `art` = '$_POST[art]',
	`geschlecht` = '$_POST[geschlecht]',
	`heimatort` = '$_POST[heimatort]',
	`gen_geburtsdatum` = '$_POST[geburtscode]',
	`geburtsdatum` = '$_POST[geburtsdatum]',
	`geburtsort` = '$_POST[geburtsort]',
	`gen_todesdatum` = '$_POST[todescode]',
	`todesdatum` = '$_POST[todesdatum]',
	`todesort` = '$_POST[todesort]',
	`umfeld` = '$_POST[umfeld]',
	`notiz` = '$_POST[notiz]',
	`primaerliteratur` = '$_POST[prim_literatur]',
	`sekundaerliteratur` = '$_POST[sek_literatur]',
	`beruf` = '$_POST[beruf]',
	`einzelausstellungen` = '$_POST[einzelausstellungen]',
	`gruppenausstellungen` = '$_POST[gruppenausstellungen]',
	`werdegang` = '$_POST[werdegang]',
	`schaffensbeschrieb` = '$_POST[schaffensbeschrieb]',
	`auszeichnungen`= '$_POST[auszeichnungen]',
	`autorIn` = '$_POST[autor]',
	`bearbeitungsdatum` = '$bearbeitungsdatum',
	`unpubliziert` = '$unpubliziert',
	`fotografengattungen_set` = '$fotografengattungen_set',
	`kanton` = '$kanton',
	`bildgattungen_set` = '$bildgattungen_set' WHERE `id` ='$_POST[hidden_id]' LIMIT 1";
	$result = mysql_query($sql);
}
//////////////Grundsätzliches: Template, assigns ect.////////////////////////////
if ($fertig==1){
} else {
	if($last_insert_id){
		$def->assign("ID",$last_insert_id);
		$id=$last_insert_id;
		$def->assign("LANG", $_GET['lang']);
		$entrymsg = getLangContent("sprache", $_GET['lang'], "newentrymsg");
		$def->assign("NEW_ENTRY_MSG", $entrymsg."<br /><br />");
	}else{
		$def->assign("ID",$_GET['id']);
		$id=$_GET['id'];
		$def->assign("LANG", $_GET['lang']);
		$lang = $_GET['lang'];
	}
	//echo "abc";
	//////////////Formdaten aus Tabelle 'fotografen'  holen////////////////////////////
	$sql = "SELECT * FROM fotografen WHERE id ='$id'";
	$result = mysql_query($sql);
	$array_eintrag = mysql_fetch_array($result);
	$def->assign('g',$array_eintrag['unpubliziert']==1?'g':'');
	
	
	
	$namen = getLangContent("sprache", $_GET['lang'], "fotografennamen");		
	$def->assign("LEGEND", "<b>$namen</b>");
	$def->parse("bearbeiten.form.fieldset_start");	
	$def->parse("bearbeiten.form");	
	namen($def,$id);
	$def->parse("bearbeiten.form.fieldset_end");	
	$def->parse("bearbeiten.form");	
	
	$def->parse("bearbeiten.bearbeiten_head_fotograf");
	mabstand($def);	
	
	 
	$fotographdetails = getLangContent("sprache", $_GET['lang'], "fotographdetails"); ///////??????
	$def->assign("LEGEND", "<b>$fotographdetails</b>");
	$def->parse("bearbeiten.form.fieldset_start");	
	$def->parse("bearbeiten.form.start");
	$def->parse("bearbeiten.form");	
	$arr_art=array("P" =>"P", "G" =>"G");   //Array füllen für Select
	genselectitem($def, getLangContent("sprache", $lang, "art"), "$array_eintrag[art]", "art", $arr_art, "", "", "");
	$arr_geschlecht=array(""=> "", "m" =>"Mann", "f" =>"Frau"); //Array füllen für Select
	genselectitem($def, getLangContent("sprache", $lang, "geschlecht"), $array_eintrag[geschlecht], "geschlecht", $arr_geschlecht, "", "", "");
	genformitem($def,'textfield',getLangContent("sprache", $lang, "heimatort"),$array_eintrag[heimatort],'heimatort');
	genformitem($def,'textfield',getLangContent("sprache", $lang, "geburtsdatum"),$array_eintrag[geburtsdatum],'geburtsdatum');
	$arr_geb_code=array(0 =>"0", 1 =>"1", 2 =>"2"); //Array füllen für Select
	
	genselectitem($def, getLangContent("sprache", $lang, "geburtscode"), $array_eintrag[gen_geburtsdatum], "geburtscode", $arr_geb_code, "", "", "");
	genformitem($def,'textfield',getLangContent("sprache", $lang, "geburtsort"),$array_eintrag[geburtsort],'geburtsort');
	genformitem($def,'textfield',getLangContent("sprache", $lang, "todesdatum"),$array_eintrag[todesdatum],'todesdatum');
	$arr_tod_code=array(0 =>"0", 1 =>"1", 2 =>"2", 8=> "8", 9=>"9"); //Array füllen für Select
	genselectitem($def, getLangContent("sprache", $lang, "todescode"), $array_eintrag[gen_todesdatum], "todescode", $arr_tod_code, "", "", "");
	genformitem($def,'textfield',getLangContent("sprache", $lang, "todesort"),$array_eintrag[todesort],'todesort');
	genformitem($def,'submitfield','','','');
	$def->parse("bearbeiten.form.tend");
	$def->parse("bearbeiten.form");	
	$def->parse("bearbeiten.form.fieldset_end");
	$def->parse("bearbeiten.form");	
	mabstand($def);
	
	
	$arbeit = getLangContent("sprache", $_GET['lang'], "arbeitsorte");
	$def->assign("LEGEND","<b>$arbeit</b>");
	$def->parse("bearbeiten.form.fieldset_start");	
	$def->parse("bearbeiten.form");	
	arbeitsperioden($def,$id);
	$def->parse("bearbeiten.form.fieldset_end");
	$def->parse("bearbeiten.form");	
	mabstand($def);	
	
	
	$bestand = getLangContent("sprache", $_GET['lang'], "bestaende");
	$def->assign("LEGEND","<b>$bestand</b>");
	$def->parse("bearbeiten.form.fieldset_start");	
	$def->parse("bearbeiten.form");	
	bestand($def,$id);
	$def->parse("bearbeiten.form.fieldset_end");
	$def->parse("bearbeiten.form");	
	mabstand($def);
	
	
	$literatur = getLangContent("sprache", $_GET['lang'], "literatur");
	$def->assign("LEGEND","<b>$literatur</b>");
	$def->parse("bearbeiten.form.fieldset_start");
	$def->parse("bearbeiten.form");		
	literatur($def,$id);
	$def->parse("bearbeiten.form.fieldset_end");
	$def->parse("bearbeiten.form");	
	mabstand($def);
	
	
	$ausstellung = getLangContent("sprache", $_GET['lang'], "ausstellungen");
	$def->assign("LEGEND","<b>$ausstellung</b>");
	$def->parse("bearbeiten.form.fieldset_start");	
	$def->parse("bearbeiten.form");	
	ausstellungen($def,$id);
	$def->parse("bearbeiten.form.fieldset_end");
	$def->parse("bearbeiten.form");	
	mabstand($def);
	
	
	$fotografenzusatz = getLangContent("sprache", $lang,"fotografen_zusatz");
	$def->assign("LEGEND", "<b>$fotografenzusatz</b>");
	$def->parse("bearbeiten.form.fieldset_start");		
	$def->parse("bearbeiten.form");	
	$def->parse("bearbeiten.form.start");
	$def->parse("bearbeiten.form");
	//genformitem($def,'textfield','Zweitname',$array_eintrag[zweitname],'zweitname');
	genformitem($def,'edittext',getLangContent("sprache", $lang, "umfeld"),$array_eintrag[umfeld],'umfeld');
	//genformitem($def,'edittext','Prim&auml;rliteratur',$array_eintrag[primaerliteratur],'prim_literatur');
	//genformitem($def,'edittext','Sekund&auml;rliteratur',$array_eintrag[sekundaerliteratur],'sek_literatur');
	genformitem($def,'submitfield','','','');
	genformitem($def,'textfield',getLangContent("sprache", $lang, "beruf"),$array_eintrag[beruf],'beruf');
	//genformitem($def,'edittext','Einzelausstellungen alt',$array_eintrag[einzelausstellungen],'einzelausstellungen');
	//genformitem($def,'edittext','Gruppenausstellungen alt',$array_eintrag[gruppenausstellungen],'gruppenausstellungen');
	genformitem($def,'edittext',getLangContent("sprache", $lang, "werdegang"),$array_eintrag[werdegang],'werdegang');
	genformitem($def,'submitfield','','','');
	genformitem($def,'edittext',getLangContent("sprache", $lang, "schaffensbeschrieb"),$array_eintrag[schaffensbeschrieb],'schaffensbeschrieb');
	genformitem($def,'edittext',getLangContent("sprache", $lang, "auszeichnungen_und_stipendien"),$array_eintrag[auszeichnungen],'auszeichnungen');
	$sql ="DESCRIBE fotografen fotografengattungen_set";//Beschreibung des Sets bekommen
	$result = mysql_query($sql);
	$fetch = mysql_fetch_array($result);
	$set_list = $fetch[Type];
	$set_list = substr($set_list, 5, strlen($set_list)-7);
	$array_set_list = explode ("','", $set_list);
	$set= $array_eintrag[fotografengattungen_set];
	$array_set = explode (",", $set);		
	///
	if ($_GET['lang']!='de'){
		gencheckarrayitemtr($def, getLangContent("sprache", $lang, "fotographengattungen"), $array_set_list, $spatr['fotografengattungen_uebersetzungen'], "fotografengattungen[]", $array_set); 
	} else {
		gencheckarrayitem($def, getLangContent("sprache", $lang, "fotographengattungen"), $array_set_list, "fotografengattungen[]", $array_set);
	}

	$sql ="DESCRIBE fotografen bildgattungen_set";//Beschreibung des Sets bekommen
	$result = mysql_query($sql);
	$fetch = mysql_fetch_array($result);
	$set_list = $fetch[Type];
	$set_list = substr($set_list, 5, strlen($set_list)-7);
	$array_set_list = explode ("','", $set_list);
	$set= $array_eintrag[bildgattungen_set];
	$array_set = explode (",", $set);
	
	
	///

	if ($_GET['lang']!='de'){
		gencheckarrayitemtr($def, getLangContent("sprache", $lang, "bildgattungen"), $array_set_list, $spatr['bildgattungen_uebersetzungen'], "bildgattungen[]", $array_set);
	} else {
		gencheckarrayitem($def, getLangContent("sprache", $lang, "bildgattungen"), $array_set_list, "bildgattungen[]", $array_set);
	}
	
	genformitem($def,'submitfield','','','');
	$sql ="DESCRIBE fotografen kanton";//Beschreibung des Sets bekommen
	$result = mysql_query($sql);
	$fetch = mysql_fetch_array($result);
	$set_list = $fetch[Type];
	$set_list = substr($set_list, 5, strlen($set_list)-7);
	$array_set_list = explode ("','", $set_list);
	$set= $array_eintrag[kanton];
	$array_set = explode (",", $set);
	///
	gencheckarrayitem($def, getLangContent("sprache", $lang, "kanton"), $array_set_list, "kanton[]", $array_set);
	genformitem($def,'edittext',getLangContent("sprache", $lang, "notiz"),$array_eintrag[notiz],'notiz');
	genformitem($def,'textfield',getLangContent("sprache", $lang, "autorIn"),$array_eintrag[autorIn],'autor');
	gencheckitem($def,getLangContent("sprache", $lang, "npublizieren"),$array_eintrag[unpubliziert],'unpubliziert');
	$def->assign("BEARBEITUNGSDATUM", getLangContent("sprache", $lang, "bearbeitungsdatum"));
	$def->assign("bearbeitungsdatum", $array_eintrag[bearbeitungsdatum]);
	
	$def->parse("bearbeiten.form.fieldset_end");
	//$def->parse("bearbeiten.form");	
	$def->parse("bearbeiten");
	//$def->parse("bearbeiten");
	$out.=$def->text("bearbeiten");
	//$def->out("bearbeiten");
}
?>
