<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set ('error_reporting', E_ALL);
/////////////////////////////
include("./fotofunc.inc.php");
include("./backend.inc.php");
include("./edit.inc.php");
testauthedit();

$def=new XTemplate ("./templates/edit.xtpl");
$def->assign("ACTION",$_GET['a']);
$def->assign("id",$_GET['id']);
$def->assign("LANG", $_GET['lang']);
$def->assign("CLANG", $clanguage);
$lang = $_GET['lang'];

$def->assign("SPR", $spr);

$edit = new EditFotograf($def);

//if ($_POST && !$_POST['submitbutton']) escposts();  // nur noch für nebentabellen
if ($_GET['id']=="new"){
	$sql = "INSERT INTO `fotografen` ( `id` , `nachname` , `vorname` , `namenszusatz` , `zweitname` , `art` , `geschlecht` , `heimatort` , 			`gen_geburtsdatum` , `geburtsdatum` , `geburtsort` , `gen_todesdatum` , `todesdatum` , `todesort` , `umfeld` , `notiz` , `primaerliteratur` , 	`sekundaerliteratur` , `beruf` , `einzelausstellungen` , `gruppenausstellungen` , `werdegang` , `kurzbio` , `schaffensbeschrieb` , `autorIn` , `bearbeitungsdatum` , `erstellungsdatum` , `fotografengattungen_set` , `bildgattungen_set` )
	VALUES ('', '', '', '', '', 'P', '', '', '0', '0000-00-00', '', '0', '0000-00-00', '', '', '', '', '', '', '', '', '', '', '', '', '0000-00-00', NOW(), '', '')";
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
	$sql = "DELETE FROM `namen` WHERE fotografen_id=$id LIMIT 1";   //lässt unverknüpfte namen und df zurück!
	$result = mysql_query($sql);
	$edit->writeHistory($last_insert_id, getHistEntry("FG", "deleted", ''));
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
	$edit->writeHistory($id, getHistEntry("FG", "delnamen", $_GET['n_id']));
}
//////////////Bezeichnung(Namen)erstellen////////////////////////////
if($_GET['n']=="new"){
	$sql = "INSERT INTO `namen` ( `id` , `fotografen_id` , `nachname` , `vorname` , `namenszusatz` , `titel` )
	VALUES ('', '$_GET[id]', '', '', '', '')";
	$result = mysql_query($sql);
	$edit->writeHistory($last_insert_id, getHistEntry("FG", "add name", mysql_insert_id()));
}
//////////////Bezeichnung(Namen) bearbeiten->speichern////////////////////////////
if($_POST['submit_namen']){
	$sql="UPDATE `namen` SET `nachname` = '".mysql_real_escape_string($_POST['nachname'])."',
	`vorname` = '".mysql_real_escape_string($_POST['vorname'])."',
	`namenszusatz` = '".mysql_real_escape_string($_POST[zusatz])."',
	`titel` = '".mysql_real_escape_string($_POST['titel'])."' WHERE `id` ='$_REQUEST[namen_id]' LIMIT 1";
	$result = mysql_query($sql);
	$bearbeitungsdatum = date("Y-m-d");
	$sql = "UPDATE `fotografen` SET `bearbeitungsdatum` = '$bearbeitungsdatum' WHERE `id` ='$_REQUEST[fotografen_id]' LIMIT 1";
	$result = mysql_query($sql);
	$edit->writeHistory($_REQUEST['fotografen_id'], getHistEntry("FG", "edit name", $_REQUEST['namen_id'].': '.getname($_REQUEST['namen_id'])));
}
//////////////Bestand löschen////////////////////////////
if($_GET['b']=="del"){
	$sql = "DELETE FROM `bestand_fotograf` WHERE id='$_GET[b_id]' LIMIT 1";
	$result = mysql_query($sql);
	$bearbeitungsdatum = date("Y-m-d");
	$sql = "UPDATE `fotografen` SET `bearbeitungsdatum` = '$bearbeitungsdatum' WHERE `id` ='$_GET[id]' LIMIT 1";
	$result = mysql_query($sql);
	$edit->writeHistory($_GET['id'], getHistEntry("FG", "del bestand: ",$_GET['logid'] ));
}
//////////////Literatur löschen////////////////////////////
if($_GET['l']=="del"){
	$sql = "DELETE FROM `literatur_fotograf` WHERE id='$_GET[l_id]' LIMIT 1";
	$result = mysql_query($sql);
	//echo($sql);
	$bearbeitungsdatum = date("Y-m-d");
	$sql = "UPDATE `fotografen` SET `bearbeitungsdatum` = '$bearbeitungsdatum' WHERE `id` ='$_GET[id]' LIMIT 1";
	$result = mysql_query($sql);
	$edit->writeHistory($_GET['id'], getHistEntry("FG", "del literatur: ",$_GET['logid'] ));
}
if($_GET['au']=="del"){
	$sql = "DELETE FROM `ausstellung_fotograf` WHERE id='$_GET[a_id]' LIMIT 1";
	$result = mysql_query($sql);
	//echo($sql);
	$bearbeitungsdatum = date("Y-m-d");
	$sql = "UPDATE `fotografen` SET `bearbeitungsdatum` = '$bearbeitungsdatum' WHERE `id` ='$_GET[id]' LIMIT 1";
	$result = mysql_query($sql);
	$edit->writeHistory($_GET['id'], getHistEntry("FG", "del ausstellung: ",$_GET['logid'] ));
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
	$edit->writeHistory($_GET['id'], getHistEntry("FG", "add bestand: ",$_GET['bestand_id'] ));
}
if($_REQUEST['new_literatur']){
	$sql="INSERT INTO `literatur_fotograf` (`literatur_id`, `fotografen_id`, `typ`) VALUES ($_REQUEST[literatur_id],$_REQUEST[id],'$_REQUEST[typ]')";
	$result = mysql_query($sql);
	$bearbeitungsdatum = date("Y-m-d");
	//echo $sql;
	$sql = "UPDATE `fotografen` SET `bearbeitungsdatum` = '$bearbeitungsdatum' WHERE `id` ='$_REQUEST[id]' LIMIT 1";
	$result = mysql_query($sql);
	$edit->writeHistory($_GET['id'], getHistEntry("FG", "add literatur: ",$_GET['literatur_id'] ));
}
if($_REQUEST['new_ausstellung']){
	$sql="INSERT INTO `ausstellung_fotograf` (`ausstellung_id`, `fotograf_id`) VALUES ($_REQUEST[ausstellung_id],$_REQUEST[id])";
	$result = mysql_query($sql);
	//echo $sql;
	$bearbeitungsdatum = date("Y-m-d");
	$sql = "UPDATE `fotografen` SET `bearbeitungsdatum` = '$bearbeitungsdatum' WHERE `id` ='$_REQUEST[id]' LIMIT 1";
	$result = mysql_query($sql);
	$edit->writeHistory($_GET['id'], getHistEntry("FG", "add ausstellung: ",$_GET['ausstellung_id'] ));
}
//////////////Arbeitsperiode löschen////////////////////////////
if($_GET['ap']=="del"){
	$sql = "DELETE FROM `arbeitsperioden` WHERE id='$_GET[a_id]'";
	$result = mysql_query($sql);
	$bearbeitungsdatum = date("Y-m-d");
	$sql = "UPDATE `fotografen` SET `bearbeitungsdatum` = '$bearbeitungsdatum' WHERE `id` ='$_GET[id]' LIMIT 1";
	$result = mysql_query($sql);
	$edit->writeHistory($_GET['id'], getHistEntry("FG", "del arbeitsperiode", $_GET['a_id']), 'fotografen');
}
//////////////Arbeitsperiode erstellen////////////////////////////
if($_GET['ap']=="new"){
	$sql = "INSERT INTO `arbeitsperioden` ( `id` , `fotografen_id` , `arbeitsort` , `von` , `um_von` , `bis` , `um_bis` )
	VALUES ('', '$_GET[id]', '', '', '0', '', '0')";
	$result = mysql_query($sql);
	$edit->writeHistory($_GET['id'], getHistEntry("FG", "add arbeitsperiode", mysql_insert_id()));
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
	$sql="UPDATE `arbeitsperioden` SET `arbeitsort` = '".mysql_real_escape_string($_POST['arbeitsort'])."',
	`von` = '$_REQUEST[von]',
	`um_von` = '$um_von',
	`bis` = '$_REQUEST[bis]',
	`um_bis` = '$um_bis' WHERE `id` = '$_REQUEST[arbeitsort_id]'";
	//echo($sql);
	$result = mysql_query($sql);
	$bearbeitungsdatum = date("Y-m-d");
	$sql = "UPDATE `fotografen` SET `bearbeitungsdatum` = '$bearbeitungsdatum' WHERE `id` ='$_REQUEST[fotografen_id]' LIMIT 1";
	$result = mysql_query($sql);
	$edit->writeHistory($_GET['id'], getHistEntry("FG", "edit arbeitsperiode", $_REQUEST['arbeitsort_id'].': '.($um_von==1?'um ':'').$_REQUEST['von'].'-'.($um_bis==1?'um ':'').$_REQUEST['bis'].' '.$_POST['arbeitsort']));
}
//////////////Bildgattugnen zur Speicherung in DB aufbereiten////////////////////////////
if($_POST['submitbutton']){
	$id=$_POST['hidden_id'];
	$spezfs=array('gen_geburtsdatum'=>'geburtscode','gen_todesdatum'=>'todescode','primaerliteratur'=>'prim_literatur',
	'sekundaerliteratur'=>'sek_literatur','autorIn' => 'autor');  // felder bei denen der SB-name nicht dem formelement-name entspricht
	$langfs=array('umfeld','beruf','werdegang','schaffensbeschrieb'); // felder mit sprachversionen
	$textfs=array('art','geschlecht','heimatort','geburtsdatum','geburtsort','originalsprache','notiz','prim_literatur',
		'todesdatum','todesort','einzelausstellungen','gruppenausstellungen','kurzbio','auszeichnungen','pnd','pnd_status','pnd_answer'); //"normale" felder

	$varfields=array('showkurzbio','unpubliziert','fotografengattungen_set','bildgattungen_set','kanton');  // felder die aus variablen gelesen werden.

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
	if($_POST['showkurzbio']=="1"){
		$showkurzbio = 1;
	}else{
		$showkurzbio = 0;
	}
	
	//////////////Formdaten in Tabelle 'fotografen' eintragen bzw aktualisieren////////////////////////////
	$fotografengattungen_set = substr($fotografengattungen_set, 0, strlen($fotografengattungen_set)-1);
	$bearbeitungsdatum = date("Y-m-d");

	$sql = "SELECT * FROM fotografen WHERE id =$id";
	$result = mysql_query($sql);
	$array_eintrag = mysql_fetch_array($result);
	
	$sql="UPDATE fotografen SET ";
	$s='';
	$s2=''; // für history
	foreach ($langfs as $t){
		$u=($_POST[$t]==$array_eintrag[$t.clangex()]?'':'`'.$t.clangex().'`=\''.mysql_real_escape_string($_POST[$t])."'");
		if ($u){
			$s.=($s?', ':'').$u;
			$s2.=($s2?', ':'').getHChanged($t.clangex(),$_POST[$t],$array_eintrag[$t.clangex()]);
		}
	}

	foreach ($textfs as $t){
		$u=($_POST[$t]==$array_eintrag[$t]?'':'`'.$t.'`=\''.mysql_real_escape_string($_POST[$t])."'");
		if ($u){
			$s.=($s?', ':'').$u;
			$s2.=($s2?', ':'').getHChanged($t,$_POST[$t],$array_eintrag[$t]);
		}
	}
	
	foreach ($spezfs as $t=>$v){
		$u=($_POST[$v]==$array_eintrag[$t]?'':'`'.$t.'`=\''.mysql_real_escape_string($_POST[$v])."'");
		if ($u){
			$s.=($s?', ':'').$u;
			$s2.=($s2?', ':'').getHChanged($t,$_POST[$v],$array_eintrag[$t]);
		}
	}
	foreach ($varfields as $t){
		//echo "$t: ".$$t."<br />";
		$u=($$t==$array_eintrag[$t]?'':'`'.$t.'`=\''.mysql_real_escape_string($$t)."'");
		if ($u){
			$s.=($s?', ':'').$u;
			$s2.=($s2?', ':'').getHChanged($t,$$t,$array_eintrag[$t]);
		}
	}
	
	$sql.=$s.", `bearbeitungsdatum`='".date("Y-m-d")."' WHERE id ='$id'";

	$bearbeitungsdatum = date("Y-m-d");
	$edit->writeHistory($id, getHistEntry("FG", "edit", $s2));
	//echo $sql;



//	$sql = "UPDATE `fotografen` SET `art` = '$_POST[art]',
//	`geschlecht` = '$_POST[geschlecht]',
//	`heimatort` = '$_POST[heimatort]',
//	`gen_geburtsdatum` = '$_POST[geburtscode]',
//	`geburtsdatum` = '$_POST[geburtsdatum]',
//	`geburtsort` = '$_POST[geburtsort]',
//	`gen_todesdatum` = '$_POST[todescode]',
//	`todesdatum` = '$_POST[todesdatum]',
//	`todesort` = '$_POST[todesort]',
//	`umfeld".clangex()."` = '$_POST[umfeld]',
//	`originalsprache` = '$_POST[originalsprache]',
//	`notiz` = '$_POST[notiz]',
//	`primaerliteratur` = '$_POST[prim_literatur]',
//	`sekundaerliteratur` = '$_POST[sek_literatur]',
//	`beruf".clangex()."` = '$_POST[beruf]',
//	`einzelausstellungen` = '$_POST[einzelausstellungen]',
//	`gruppenausstellungen` = '$_POST[gruppenausstellungen]',
//	`werdegang".clangex()."` = '$_POST[werdegang]',
//	`kurzbio` = '$_POST[kurzbio]',
//	`showkurzbio` = '$_POST[showkurzbio]',		
//	`schaffensbeschrieb".clangex()."` = '$_POST[schaffensbeschrieb]',
//	`auszeichnungen`= '$_POST[auszeichnungen]',
//	`pnd` = '$_POST[pnd]',
//	`autorIn` = '$_POST[autor]',
//	`bearbeitungsdatum` = '$bearbeitungsdatum',
//	`unpubliziert` = '$unpubliziert',
//	`fotografengattungen_set` = '$fotografengattungen_set',
//	`kanton` = '$kanton',
//	`bildgattungen_set` = '$bildgattungen_set' WHERE `id` ='$_POST[hidden_id]' LIMIT 1";
	$result = mysql_query($sql);
}
//////////////Grundsätzliches: Template, assigns ect.////////////////////////////
if ($fertig==1){
} else {
	if($last_insert_id){
		$def->assign("ID",$last_insert_id);
		$id=$last_insert_id;
		//$def->assign("LANG", $_GET['lang']);
		$entrymsg = $spr['newentrymsg'];
		$def->assign("NEW_ENTRY_MSG", $entrymsg."<br /><br />");
	}else{
		$def->assign("ID",$_GET['id']);
		$id=$_GET['id'];
		//$def->assign("LANG", $_GET['lang']);
		//$lang = $_GET['lang'];
	}

	//////////////Formdaten aus Tabelle 'fotografen'  holen////////////////////////////
	$sql = "SELECT * FROM fotografen WHERE id ='$id'";
	$result = mysql_query($sql);
	$array_eintrag = mysql_fetch_array($result);
	$def->assign('g',$array_eintrag['unpubliziert']==1?'g':'');

	$def->assign("LEGEND", "<b>".$spr['fotografennamen']."</b>");
	$def->parse("bearbeiten.form.fieldset_start");
	$def->parse("bearbeiten.form");
	$edit->namen($id);
	$def->parse("bearbeiten.form.fieldset_end");
	$def->parse("bearbeiten.form");

	$def->parse("bearbeiten.bearbeiten_head_fotograf");
	mabstand($def);


	$def->assign("LEGEND", "<b>".$spr['fotographdetails']."</b>");
	$def->parse("bearbeiten.form.fieldset_start");
	$def->parse("bearbeiten.form.start");
	$def->parse("bearbeiten.form");
	genformitem($def,'textfield','PND',$array_eintrag['pnd'],'pnd');
	$arr_art=array("P" =>"P", "G" =>"G");   //Array füllen für Select
	genselectitem($def, $spr['art'], "$array_eintrag[art]", "art", $arr_art, "", "", "");
	$arr_geschlecht=array(""=> "", "m" =>"Mann", "f" =>"Frau"); //Array füllen für Select
	genselectitem($def, $spr['geschlecht'], $array_eintrag['geschlecht'], "geschlecht", $arr_geschlecht, "", "", "");
	genformitem($def,'textfield',$spr['heimatort'],$array_eintrag['heimatort'],'heimatort');
	genformitem($def,'textfield',$spr['geburtsdatum'],$array_eintrag['geburtsdatum'],'geburtsdatum');
	$arr_geb_code=array(0 =>"0", 1 =>"1", 2 =>"2"); //Array füllen für Select

	genselectitem($def, $spr['geburtscode'], $array_eintrag['gen_geburtsdatum'], "geburtscode", $arr_geb_code, "", "", "");
	genformitem($def,'textfield',$spr['geburtsort'],$array_eintrag['geburtsort'],'geburtsort');
	genformitem($def,'textfield',$spr['todesdatum'],$array_eintrag['todesdatum'],'todesdatum');
	$arr_tod_code=array(0 =>"0", 1 =>"1", 2 =>"2", 8=> "8", 9=>"9"); //Array füllen für Select
	genselectitem($def, $spr['todescode'], $array_eintrag['gen_todesdatum'], "todescode", $arr_tod_code, "", "", "");
	genformitem($def,'textfield', $spr['todesort'],$array_eintrag['todesort'],'todesort');
	genformitem($def,'submitfield','','','');
	$def->parse("bearbeiten.form.tend");
	$def->parse("bearbeiten.form");
	$def->parse("bearbeiten.form.fieldset_end");
	$def->parse("bearbeiten.form");
	mabstand($def);


	$def->assign("LEGEND","<b>".$spr['arbeitsorte']."</b>");
	$def->parse("bearbeiten.form.fieldset_start");
	$def->parse("bearbeiten.form");
	$edit->arbeitsperioden($id);
	$def->parse("bearbeiten.form.fieldset_end");
	$def->parse("bearbeiten.form");
	mabstand($def);


	$def->assign("LEGEND","<b>".$spr['bestaende']."</b>");
	$def->parse("bearbeiten.form.fieldset_start");
	$def->parse("bearbeiten.form");
	$edit->bestand($id);
	$def->parse("bearbeiten.form.fieldset_end");
	$def->parse("bearbeiten.form");
	mabstand($def);


	$def->assign("LEGEND","<b>".$spr['literatur']."</b>");
	$def->parse("bearbeiten.form.fieldset_start");
	$def->parse("bearbeiten.form");
	$edit->literatur($id);
	$def->parse("bearbeiten.form.fieldset_end");
	$def->parse("bearbeiten.form");
	mabstand($def);

	$def->assign("LEGEND","<b>".$spr['ausstellungen']."</b>");
	$def->parse("bearbeiten.form.fieldset_start");
	$def->parse("bearbeiten.form");
	$edit->ausstellungen($id);
	$def->parse("bearbeiten.form.fieldset_end");
	$def->parse("bearbeiten.form");
	mabstand($def);


	$def->assign("LEGEND", "<b>".$spr['fotografen_zusatz']."</b>");
	$def->parse("bearbeiten.form.fieldset_start");
	$def->parse("bearbeiten.form");
	$def->parse("bearbeiten.form.start");
	$def->parse("bearbeiten.form");
	$arr_originalsprache=array('de'=>'de','fr'=>'fr','it'=>'it','rm'=>'rm','en'=>'en'); //Array füllen für Select
	$def->assign('sprachauswahl',gensprachaus("<a href=\"./?a=edit&amp;id=$id&amp;lang=$lang"));
	genselectitem($def, $spr['originalsprache'], $array_eintrag['originalsprache'], "originalsprache", $arr_originalsprache, "", "", "");
	//genformitem($def,'textfield','Zweitname',$array_eintrag[zweitname],'zweitname');
	genformitem($def,'edittext',$spr['umfeld'].' ('.$clanguage.')',$array_eintrag['umfeld'.clangex()],'umfeld');
	//genformitem($def,'edittext','Prim&auml;rliteratur',$array_eintrag[primaerliteratur],'prim_literatur');
	//genformitem($def,'edittext','Sekund&auml;rliteratur',$array_eintrag[sekundaerliteratur],'sek_literatur');
	genformitem($def,'submitfield','','','');
	genformitem($def,'textfield',$spr['beruf'].' ('.$clanguage.')',$array_eintrag['beruf'.clangex()],'beruf');
	genformitem($def,'edittext',$spr['biografie'],$array_eintrag['kurzbio'],'kurzbio');
	gencheckitem($def,$spr['biografie_anzeigen'], $array_eintrag['showkurzbio'], 'showkurzbio');


	genformitem($def,'submitfield','','','');
	//genformitem($def,'edittext','Einzelausstellungen alt',$array_eintrag[einzelausstellungen],'einzelausstellungen');
	//genformitem($def,'edittext','Gruppenausstellungen alt',$array_eintrag[gruppenausstellungen],'gruppenausstellungen');
	genformitem($def,'edittext',$spr['werdegang'].' ('.$clanguage.')',$array_eintrag['werdegang'.clangex()],'werdegang');
	genformitem($def,'submitfield','','','');
	genformitem($def,'edittext',$spr['schaffensbeschrieb'].' ('.$clanguage.')',$array_eintrag['schaffensbeschrieb'.clangex()],'schaffensbeschrieb');
	genformitem($def,'edittext',$spr['auszeichnungen_und_stipendien'],$array_eintrag['auszeichnungen'],'auszeichnungen');
	$sql ="DESCRIBE fotografen fotografengattungen_set";//Beschreibung des Sets bekommen
	$result = mysql_query($sql);
	$fetch = mysql_fetch_array($result);
	$set_list = $fetch[Type];
	$set_list = substr($set_list, 5, strlen($set_list)-7);
	$array_set_list = explode ("','", $set_list);
	$set= $array_eintrag[fotografengattungen_set];
	$array_set = explode (",", $set);
	///
	gencheckarrayitemtr($def, $spr['fotographengattungen'], $array_set_list, $spatr['fotografengattungen_uebersetzungen'], "fotografengattungen[]", $array_set);

	$sql ="DESCRIBE fotografen bildgattungen_set";//Beschreibung des Sets bekommen
	$result = mysql_query($sql);
	$fetch = mysql_fetch_array($result);
	$set_list = $fetch[Type];
	$set_list = substr($set_list, 5, strlen($set_list)-7);
	$array_set_list = explode ("','", $set_list);
	$set= $array_eintrag[bildgattungen_set];
	$array_set = explode (",", $set);

	gencheckarrayitemtr($def, $spr['bildgattungen'], $array_set_list, $spatr['bildgattungen_uebersetzungen2'], "bildgattungen[]", $array_set);

	genformitem($def,'submitfield','','','');
	$sql ="DESCRIBE fotografen kanton";//Beschreibung des Sets bekommen
	$result = mysql_query($sql);
	$fetch = mysql_fetch_array($result);
	$set_list = $fetch[Type];
	$set_list = substr($set_list, 5, strlen($set_list)-7);
	$array_set_list = explode ("','", $set_list);
	$set= $array_eintrag['kanton'];
	$array_set = explode (",", $set);
	///
	gencheckarrayitem($def, $spr['kanton'], $array_set_list, "kanton[]", $array_set);
	genformitem($def,'edittext',$spr['notiz'],$array_eintrag[notiz],'notiz');
	genformitem($def,'textfield',$spr['autorIn'],$array_eintrag[autorIn],'autor');
	gencheckitem($def,$spr['npublizieren'],$array_eintrag[unpubliziert],'unpubliziert');
	genformitem($def,'textfield','pnd_status',$array_eintrag['pnd_status'],'pnd_status');
	genformitem($def,'edittext','pnd_answer',$array_eintrag['pnd_answer'],'pnd_answer');
	//$def->assign("BEARBEITUNGSDATUM", $spr['bearbeitungsdatum']);
	$def->assign("bearbeitungsdatum", $array_eintrag[bearbeitungsdatum]);

	$def->parse("bearbeiten.form.fieldset_end");
	$def->parse("bearbeiten.bearbeitungsdatum");
	$def->parse("bearbeiten.speichern.neuloeschen");
	$def->parse("bearbeiten.speichern");
	$def->parse("bearbeiten");
	$out.=$def->text("bearbeiten");
	//$def->out("bearbeiten");
}
?>
