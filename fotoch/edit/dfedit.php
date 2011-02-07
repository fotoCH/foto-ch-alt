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

//if ($_POST) escposts();
$type=getParam('type','fotograf');
$def->assign("TYPE", $type);

if ($_GET['new']=="1"){
	$id=$_GET['id'];
	$sql = "INSERT INTO `doku_fiche_$type` (`id`) VALUES ('$id');";
	$result = mysql_query($sql);
}
$del=$_GET['delete'];
if ($del=="2"){
	$id=$_GET['id'];
	$sql = "DELETE FROM `doku_fiche_$type` WHERE id=$id LIMIT 1";
	//echo $sql;
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
//////////////Bildgattugnen zur Speicherung in DB aufbereiten////////////////////////////
if($_POST['submitbutton']){
	if($_POST['unpubliziert']=="1"){
		$unpubliziert = 1;
	}else{
		$unpubliziert = 0;
	}
	//////////////Formdaten in Tabelle 'fotografen' eintragen bzw aktualisieren////////////////////////////
	$fotografengattungen_set = substr($fotografengattungen_set, 0, strlen($fotografengattungen_set)-1);
	$bearbeitungsdatum = date("Y-m-d");
/*	$sql = "UPDATE `fotografen` SET `art` = '$_POST[art]',
	`geschlecht` = '$_POST[geschlecht]',
	`heimatort` = '$_POST[heimatort]',
	`gen_geburtsdatum` = '$_POST[geburtscode]',
	`geburtsdatum` = '$_POST[geburtsdatum]',
	`geburtsort` = '$_POST[geburtsort]',
	`gen_todesdatum` = '$_POST[todescode]',
	`todesdatum` = '$_POST[todesdatum]',
	`todesort` = '$_POST[todesort]',
	`umfeld".clangex()."` = '$_POST[umfeld]',
	`originalsprache` = '$_POST[originalsprache]',
	`notiz` = '$_POST[notiz]',
	`primaerliteratur` = '$_POST[prim_literatur]',
	`sekundaerliteratur` = '$_POST[sek_literatur]',
	`beruf".clangex()."` = '$_POST[beruf]',
	`einzelausstellungen` = '$_POST[einzelausstellungen]',
	`gruppenausstellungen` = '$_POST[gruppenausstellungen]',
	`werdegang".clangex()."` = '$_POST[werdegang]',
	`kurzbio` = '$_POST[kurzbio]',
	`showkurzbio` = '$_POST[showkurzbio]',		
	`schaffensbeschrieb".clangex()."` = '$_POST[schaffensbeschrieb]',
	`auszeichnungen`= '$_POST[auszeichnungen]',
	`pnd` = '$_POST[pnd]',
	`autorIn` = '$_POST[autor]',
	`bearbeitungsdatum` = '$bearbeitungsdatum',
	`unpubliziert` = '$unpubliziert',
	`fotografengattungen_set` = '$fotografengattungen_set',
	`kanton` = '$kanton',
	`bildgattungen_set` = '$bildgattungen_set' WHERE `id` ='$_POST[hidden_id]' LIMIT 1";
	$result = mysql_query($sql); */
}
//////////////Grundsätzliches: Template, assigns ect.////////////////////////////
if ($fertig==1){
} else {
	$def->assign("ID",$_GET['id']);
	$def->assign("heute",date('d.m.Y'));
	$def->assign("user",$_SESSION['s_uid']);
	$def->assign("userid",$_SESSION['s_id']);
	$id=$_GET['id'];
	
	//////////////Formdaten aus Tabelle 'fotografen'  holen////////////////////////////
	$sql = "SELECT * FROM doku_fiche_$type WHERE id ='$id'";
	$result = mysql_query($sql);
	$array_eintrag = mysql_fetch_array($result);
	//////////////Formdaten aus Tabelle 'fotografen'  holen////////////////////////////
	$sql = "SELECT * FROM ".($type=='fotograf'?'fotografen':'institution')." WHERE id ='$id'";
	$result = mysql_query($sql);
	$array_eintrag2 = mysql_fetch_array($result);
	
	$def->assign('g',$array_eintrag['unpubliziert']==1?'g':'');
	
	
	$def->assign("LEGEND", "<b>".$spr['fotografennamen']."</b>");
	$def->parse("bearbeiten.form.fieldset_start");	
	$def->parse("bearbeiten.form");	
	namendf($def,$id);
	$def->parse("bearbeiten.form.fieldset_end");	
	$def->parse("bearbeiten.form");	
	
	$def->parse("bearbeiten.bearbeiten_head_dokufiche_".$type);
	mabstand($def);	

	$def->assign("LEGEND", "<b>".$spr['allgemeine_informationen']."</b>");
	$def->parse("bearbeiten.form.fieldset_start");		
	$def->parse("bearbeiten.form");	
	$def->parse("bearbeiten.form.start");
	$def->parse("bearbeiten.form");
	genformitem($def,'textfield',$spr['projektname'],$array_eintrag['projektname'],'projektname');
	
	$arr_territoriumszugegoerigkeit=array('de'=>'de','fr'=>'fr','it'=>'it','rm'=>'rm','en'=>'en'); //Array füllen für Select
	genselectitem($def, $spr['territoriumszugegoerigkeit'], $array_eintrag['territoriumszugegoerigkeit'], "territoriumszugegoerigkeit", $arr_territoriumszugegoerigkeit, "", "", "");

	gendatnoedit($def,$spr['erstellungsdatum'],$array_eintrag2['erstellungsdatum']);

	gendatnoedit($def,$spr['letzte_aktualisierung'],$array_eintrag['bearbeitungsdatum']);
	
	
	//$arr_bearbeitungstiefe=array(0=>'',1=>'Lokal',2=>'Regional',3=>'Kantonal',4=>'National (Übersetzungen D/F/I)',5=>'International (Übersetzungen D/F/I)');
	$arr_bearbeitungstiefe=array(0=>'0: L&auml;rm',1=>'1: K U Be Bi A',2=>'2: K U Be Bi A W',3=>'3: K U Be Bi A W S I',4=>'4: K U Be Bi A W S I Ue (I/F/D)',5=>'5: K U Be Bi A W S I Ue (I/F/D/E)');
	genselectitem($def, $spr['bearbeitungstiefe'], $array_eintrag['bearbeitungstiefe'], "bearbeitungstiefe", $arr_bearbeitungstiefe, "", "", "");
	//genformitem($def,'edittext',$spr['biografie'],$array_eintrag['kurzbio'],'kurzbio');
	
	genstempel1($def, $spr['bibliografie'].': '.$spr['fertig_gestellt'],'biografie_ref',$array_eintrag['biografie_ref']);
	genstempel1($def, $spr['ausstellungen'].': '.$spr['fertig_gestellt'],'ausstellungen_ref',$array_eintrag['ausstellungen_ref']);
	genstempel1($def, $spr['auszeichnungen_stipendien'].': ','auszeichnungen_stipendien_ref',$array_eintrag['auszeichnungen_stipendien_ref']);
	genstempel1($def, $spr['bestaende2'].': '.$spr['fertig_gestellt'],'bestaende_ref',$array_eintrag['bestaende_ref']);
	genstempel1($def, $spr['interview_vorgesehen'],'interview_vorgesehen_ref',$array_eintrag['interview_vorgesehen_ref']);
	genstempel1($def, $spr['interview_fertiggestellt'],'interview_fertiggestellt_ref',$array_eintrag['interview_fertiggestellt_ref']);
	$def->parse("bearbeiten.form.tend");
	$def->parse("bearbeiten.form");	
	
	$def->parse("bearbeiten.form.fieldset_end");
	$def->parse("bearbeiten.form");	
	mabstand($def);
	
	$def->assign("LEGEND", "<b>".$spr['vita']."</b>");
	$def->parse("bearbeiten.form.fieldset_start");		
	$def->parse("bearbeiten.form");	
	$def->parse("bearbeiten.form.start");
	$def->parse("bearbeiten.form");
	
	gennoedit($def, $spr['originalsprache'],$array_eintrag2['originalsprache']);
	
	
	genstempel2($def, $spr['werdegang'],'werdegang_workflow_ref',$array_eintrag['werdegang_workflow_ref']);

	genstempel2($def, $spr['schaffensbeschrieb'],'schaffensbeschrie_workflow_ref',$array_eintrag['schaffensbeschrie_workflow_ref']);
	
	genstempel2($def, $spr['uebersetzung'].' de','uebersetzung_de_wref',$array_eintrag['uebersetzung_de_wref']);
	
	genstempel2($def, $spr['uebersetzung'].' fr','uebersetzung_fr_wref',$array_eintrag['uebersetzung_fr_wref']);

	genstempel2($def, $spr['uebersetzung'].' it','uebersetzung_it_wref',$array_eintrag['uebersetzung_it_wref']);
	
	genstempel2($def, $spr['uebersetzung'].' rm','uebersetzung_rm_wref',$array_eintrag['uebersetzung_rm_wref']);
	
	genstempel2($def, $spr['uebersetzung'].' en','uebersetzung_en_wref',$array_eintrag['uebersetzung_en_wref']);
	
	$def->parse("bearbeiten.form.fieldset_end");
	//$def->parse("bearbeiten.form");	
	$def->parse("bearbeiten");
	//$def->parse("bearbeiten");
	$out.=$def->text("bearbeiten");
	//$def->out("bearbeiten");
}
?>

