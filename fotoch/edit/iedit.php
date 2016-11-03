<?php
/////////////////////////////
include("./fotofunc.inc.php");
include("./backend.inc.php");
include("./iedit.inc.php");
include("./edit.inc.php");

//error_reporting((E_ALL));
testauthedit();
$def=new XTemplate ("./templates/edit.xtpl");
$def->assign("ACTION",$_GET['a']);

$def->assign("id",$_GET['id']);
$lang = $_GET['lang'];
$def->assign("LANG",$lang);

$def->assign("SPR",$spr);

$edit = new Edit( $def, 'institution' );

//if ($_POST) escposts();
if ($_GET[id]=="new"){
	$sql = "INSERT INTO `institution` (`name`,`gesperrt`) VALUES ( 'neue Institution','1')";
	$result = mysqli_query($sqli, $sql);
	$last_insert_id = mysqli_insert_id($sqli);
	$edit->writeHistory($last_insert_id, getHistEntry("IN", "add", mysqli_insert_id($sqli)));
}
$del=$_GET['delete'];
if ($del=="2"){
	$id=$_GET['id'];
	$sql = "DELETE FROM `institution` WHERE id=$id LIMIT 1";
	//echo $sql;
	$result = mysqli_query($sqli, $sql);
	$edit->writeHistory($last_insert_id, getHistEntry("IN", "deleted", ''));
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
	$result = mysqli_query($sqli, $sql);
	$bearbeitungsdatum = date("Y-m-d");
	$sql = "UPDATE `institution` SET `bearbeitungsdatum` = '$bearbeitungsdatum' WHERE `id` ='$_GET[id]' LIMIT 1";
	$result = mysqli_query($sqli, $sql);
	$edit->writeHistory($_GET['id'], getHistEntry("IN", "del literatur: ",$_GET['logid'] ));
}
if($_GET['au']=="del"){
	$sql = "DELETE FROM `ausstellung_institution` WHERE id='$_GET[a_id]' LIMIT 1";
	$result = mysqli_query($sqli, $sql);
	$bearbeitungsdatum = date("Y-m-d");
	$sql = "UPDATE `institution` SET `bearbeitungsdatum` = '$bearbeitungsdatum' WHERE `id` ='$_GET[id]' LIMIT 1";
	$result = mysqli_query($sqli, $sql);
	$edit->writeHistory($_GET['id'], getHistEntry("IN", "del ausstellung: ",$_GET['logid'] ));
}
//////////////Bestand bearbeiten->speichern////////////////////////////
if($_REQUEST['new_literatur']){
	
	$sql="INSERT INTO `literatur_institution` (`literatur_id`, `institution_id`) VALUES ($_REQUEST[literatur_id],$_REQUEST[id])";
	$result = mysqli_query($sqli, $sql);
	$bearbeitungsdatum = date("Y-m-d");
	$sql = "UPDATE `institution` SET `bearbeitungsdatum` = '$bearbeitungsdatum' WHERE `id` ='$_REQUEST[id]' LIMIT 1";
	$result = mysqli_query($sqli, $sql);
	$edit->writeHistory($_GET['id'], getHistEntry("IN", "add literatur: ",$_GET['literatur_id'] ));
}
if($_REQUEST['new_ausstellung']){
	
	$sql="INSERT INTO `ausstellung_institution` (`ausstellung_id`, `institution_id`) VALUES ($_REQUEST[ausstellung_id],$_REQUEST[id])";
	$result = mysqli_query($sqli, $sql);
	//echo($sql);
	$bearbeitungsdatum = date("Y-m-d");
	$sql = "UPDATE `institution` SET `bearbeitungsdatum` = '$bearbeitungsdatum' WHERE `id` ='$_REQUEST[id]' LIMIT 1";
	$result = mysqli_query($sqli, $sql);
	$edit->writeHistory($_GET['id'], getHistEntry("IN", "add ausstellung: ",$_GET['ausstellung_id'] ));
	
}
//////////////Bildgattugnen zur Speicherung in DB aufbereiten////////////////////////////
if($_POST['submitbutton']){
	$id=$_POST['id'];
	foreach ($_POST['bildgattungen'] as $t){
		$bildgattungen_set .=$t;
		$bildgattungen_set .=",";
	}
	$bildgattungen_set = substr($bildgattungen_set, 0, strlen($bildgattungen_set)-1);
	if($_POST['unpubliziert']=="1"){
		$gesperrt = 1;
	}else{
		$gesperrt = 0;
	}
	
	$spezfs=array();  // felder bei denen der SB-name nicht dem formelement-name entspricht
	$langfs=array(); // felder mit sprachversionen
	$textfs=array('abkuerzung','abkuerzung_fr','abkuerzung_it','abkuerzung_rm','abkuerzung_en','art','isil','kanton','adresse','ort','name','name_fr','name_it','name_rm','name_en','plz','kontaktperson','telefon','fax','email','homepage','zugang_zur_sammlung','sammlungszeit_von','sammlungszeit_bis','sammlungsbeschreibung','sammlungsgeschichte','literatur','notiz','autorin'); //"normale" felder

	$varfields=array('bildgattungen_set','gesperrt');  // felder die aus variablen gelesen werden.
	
	//////////////Formdaten in Tabelle 'fotografen' eintragen bzw aktualisieren////////////////////////////
	$bearbeitungsdatum = date("Y-m-d");
	$sql = "SELECT * FROM institution WHERE id =$id";
	$result = mysqli_query($sqli, $sql);
	$array_eintrag = mysqli_fetch_array($result);
	
	$sql="UPDATE institution SET ";
	$s='';
	$s2=''; // für history
	foreach ($langfs as $t){
		$u=($_POST[$t]==$array_eintrag[$t.clangex()]?'':'`'.$t.clangex().'`=\''.mysqli_real_escape_string($sqli, $_POST[$t])."'");
		if ($u){
			$s.=($s?', ':'').$u;
			$s2.=($s2?', ':'').getHChanged($t.clangex(),$_POST[$t],$array_eintrag[$t.clangex()]);
		}
	}

	foreach ($textfs as $t){
		$u=($_POST[$t]==$array_eintrag[$t]?'':'`'.$t.'`=\''.mysqli_real_escape_string($sqli, $_POST[$t])."'");
		if ($u){
			$s.=($s?', ':'').$u;
			$s2.=($s2?', ':'').getHChanged($t,$_POST[$t],$array_eintrag[$t]);
		}
	}
	
	foreach ($spezfs as $t=>$v){
		$u=($_POST[$v]==$array_eintrag[$t]?'':'`'.$t.'`=\''.mysqli_real_escape_string($sqli, $_POST[$v])."'");
		if ($u){
			$s.=($s?', ':'').$u;
			$s2.=($s2?', ':'').getHChanged($t,$_POST[$v],$array_eintrag[$t]);
		}
	}
	foreach ($varfields as $t){
		//echo "$t: ".$$t."<br />";
		$u=($$t==$array_eintrag[$t]?'':'`'.$t.'`=\''.mysqli_real_escape_string($sqli, $$t)."'");
		if ($u){
			$s.=($s?', ':'').$u;
			$s2.=($s2?', ':'').getHChanged($t,$$t,$array_eintrag[$t]);
		}
	}
	
	$sql.=$s.", `bearbeitungsdatum`='".date("Y-m-d")."' WHERE id =$_POST[hidden_id] LIMIT 1";

	
	$edit->writeHistory($id, getHistEntry("IN", "edit", $s2));
	//echo $sql;
	
/*	$sql = "UPDATE `institution` SET `name` = '$_POST[name]',
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
	`gesperrt` = $gesperrt WHERE `id` =$_POST[hidden_id] LIMIT 1"; */
	$result = mysqli_query($sqli, $sql); 
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
	$result = mysqli_query($sqli, $sql);
	$array_eintrag = mysqli_fetch_array($result);
	
	$def->assign("LEGEND", "<b>".$spr['institution_details']."</b>");
	$def->parse("bearbeiten.form.fieldset_start");	
	//$def->parse("bearbeiten.form");	
	$def->parse("bearbeiten.form.start");
	$def->parse("bearbeiten.form");
	
	genformitem($def,'textfield',$spr['name'],$array_eintrag['name'],'name');
	$def->assign('NAME',$array_eintrag['name']);
	$def->assign('g',($array_eintrag['gesperrt']==1?'g':''));
	$def->parse("bearbeiten.bearbeiten_head_institution");
	genformitem($def,'textfield',$spr['name'].'_fr',$array_eintrag['name_fr'],'name_fr');
	genformitem($def,'textfield',$spr['name'].'_it',$array_eintrag['name_it'],'name_it');
	genformitem($def,'textfield',$spr['name'].'_rm',$array_eintrag['name_rm'],'name_rm');
	genformitem($def,'textfield',$spr['name'].'_en',$array_eintrag['name_en'],'name_en');
	genformitem($def,'textfield',$spr['abkuerzung'],$array_eintrag['abkuerzung'],'abkuerzung');
	genformitem($def,'textfield',$spr['abkuerzung'].'_fr',$array_eintrag['abkuerzung_fr'],'abkuerzung_fr');
	genformitem($def,'textfield',$spr['abkuerzung'].'_it',$array_eintrag['abkuerzung_it'],'abkuerzung_it');
	genformitem($def,'textfield',$spr['abkuerzung'].'_rm',$array_eintrag['abkuerzung_rm'],'abkuerzung_rm');
	genformitem($def,'textfield',$spr['abkuerzung'].'_en',$array_eintrag['abkuerzung_en'],'abkuerzung_en');
	genformitem($def,'textfield',$spr['art'],$array_eintrag['art'],'art');
	genformitem($def,'textfield',$spr['isil'],$array_eintrag['isil'],'isil');	
	genformitem($def,'textfield',$spr['adresse'],$array_eintrag['adresse'],'adresse');
	genformitem($def,'textfield',$spr['plz'],$array_eintrag['plz'],'plz');
	genformitem($def,'textfield',$spr['ort'],$array_eintrag['ort'],'ort');
	$sql ="DESCRIBE institution kanton";//Beschreibung des Sets bekommen
	$result = mysqli_query($sqli, $sql);
	$fetch = mysqli_fetch_array($result);
	$set_list = $fetch[Type];
	$set_list = substr($set_list, 5, strlen($set_list)-7);
	$array_set_list = explode ("','", $set_list); $array_set_list[0]='';
	$set= $array_eintrag['kanton'];
	$array_set = explode (",", $set);
	///
	genradioarrayitem($def, $spr['kanton'], $array_set_list, "kanton", $array_set);
	
	genformitem($def,'textfield',$spr['kontaktperson'],$array_eintrag['kontaktperson'],'kontaktperson');
	genformitem($def,'textfield',$spr['telefon'],$array_eintrag['telefon'],'telefon');
	genformitem($def,'textfield',$spr['fax'],$array_eintrag['fax'],'fax');
	genformitem($def,'textfield',$spr['email'],$array_eintrag['email'],'email');
	genformitem($def,'textfield',$spr['homepage'],$array_eintrag['homepage'],'homepage');
	genformitem($def,'editstext',$spr['zugang_zur_sammlung'],$array_eintrag['zugang_zur_sammlung'],'zugang_zur_sammlung');
	genformitem($def,'textfield',$spr['sammlungszeit']." ".$spr['von'],$array_eintrag['sammlungszeit_von'],'sammlungszeit_von');
	genformitem($def,'textfield',$spr['sammlungszeit']." ".$spr['bis'],$array_eintrag['sammlungszeit_bis'],'sammlungszeit_bis');
	$sql ="DESCRIBE institution bildgattungen_set";//Beschreibung des Sets bekommen
	$result = mysqli_query($sqli, $sql);
	$fetch = mysqli_fetch_array($result);
	$set_list = $fetch[Type];
	$set_list = substr($set_list, 5, strlen($set_list)-7);
	$array_set_list = explode ("','", $set_list);
	$set= $array_eintrag[bildgattungen_set];
	$array_set = explode (",", $set);
	
	gencheckarrayitemtr($def, $spr['bildgattungen'], $array_set_list, $spatr['bildgattungen_uebersetzungen2'], "bildgattungen[]", $array_set);

	genformitem($def,'edittext',$spr['sammlungsgeschichte'],$array_eintrag['sammlungsgeschichte'],'sammlungsgeschichte');
	genformitem($def,'edittext',$spr['sammlungsbeschreibung'],$array_eintrag['sammlungsbeschreibung'],'sammlungsbeschreibung');
	$def->parse("bearbeiten.form.tend");
	$def->parse("bearbeiten.form");
	$def->parse("bearbeiten.form.fieldset_end");	
	mabstand($def);
	
	
	$def->assign("LEGEND","<b>".$spr['bestaende']."</b>");
	$def->parse("bearbeiten.form.fieldset_start");
	$edit->bestand($id);
	$def->parse("bearbeiten.form.fieldset_end");
	
	$def->parse("bearbeiten.form");
	mabstand($def);
	
	$def->assign("LEGEND","<b>".$spr['literatur']."</b>");
	$def->parse("bearbeiten.form.fieldset_start");	
	$edit->literatur($id);
	$def->parse("bearbeiten.form.fieldset_end");
	
	$def->parse("bearbeiten.form");
	mabstand($def);
	
	$def->assign("LEGEND","<b>".$spr['ausstellungen']."</b>");
	$def->parse("bearbeiten.form.fieldset_start");	
	$edit->ausstellungen($id);
	$def->parse("bearbeiten.form.fieldset_end");	
	$def->parse("bearbeiten.form");
	mabstand($def);
	
	$def->assign("LEGEND", "<b>".$spr['institution_zusatz']."</b>");
	$def->parse("bearbeiten.form.fieldset_start");	
	$def->parse("bearbeiten.form.start");
	//$def->parse("bearbeiten.form");
	
	genformitem($def,'edittext',$spr['notiz'],$array_eintrag['notiz'],'notiz');
	genformitem($def,'textfield',$spr['autorIn'],$array_eintrag['autorin'],'autorin');
	if(auth_level(USER_WORKER)) gencheckitem($def,$spr['npublizieren'],$array_eintrag['gesperrt'],'unpubliziert');	
	$def->assign("bearbeitungsdatum", $array_eintrag['bearbeitungsdatum']);
	$def->parse("bearbeiten.form.fieldset_end");
	$def->parse("bearbeiten.bearbeitungsdatum");
	$def->parse("bearbeiten.speichern.neuloeschen");
	$def->parse("bearbeiten.speichern");
	$def->parse("bearbeiten");
	$out.=$def->text("bearbeiten");
}
?>
