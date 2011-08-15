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
$type=getParam('type','fotografen');
$def->assign("TYPE", $type);

$edit = new Edit( $def, $type );

//////////////Bildgattugnen zur Speicherung in DB aufbereiten////////////////////////////
//////////////Grundsätzliches: Template, assigns ect.////////////////////////////
if ($fertig==1){
} else {
	$def->assign("ID",$_GET['id']);
	$def->assign("heute",date('d.m.Y'));
	$def->assign("user",$_SESSION['s_uid']);
	$def->assign("userid",$_SESSION['s_id']);
	$id=$_GET['id'];



	if($_POST['submitbutton']){
		//////////////Formdaten aus Tabelle 'fotografen'  holen////////////////////////////
		$sql = "SELECT * FROM $type WHERE id ='$id'";
		$result = mysql_query($sql);
		$array_eintrag = mysql_fetch_array($result);
		//print_r($_POST);
		if ($type=='fotografen'){
			foreach ($_REQUEST['dokumentation'] as $t){
				$dokumentation .=$t;
				$dokumentation .=",";
			}
			$dokumentation = substr($dokumentation, 0, strlen($dokumentation)-1);
			$_POST['dokumentation']=$dokumentation;

			$fs=array("projektname", "territoriumszugegoerigkeit", "bearbeitungstiefe","dokumentation","dokumentation_text","notiz_fiche");
			$refs=array("biografie", "ausstellungen", "auszeichnungen_stipendien", "bestaende", "interview_vorgesehen", "interview_fertiggestellt","dokumentation");
			$wrefs=array("werdegang", "schaffensbeschrieb", "uebersetzung_de", "uebersetzung_fr", "uebersetzung_it", "uebersetzung_rm", "uebersetzung_en");
			$sql="UPDATE  $type SET ";
			$s='';
			$s2=''; // für history
			foreach ($fs as $t){
				$u=($_POST[$t]==$array_eintrag[$t]?'':'`'.$t.'`=\''.mysql_real_escape_string($_POST[$t])."'");
				if ($u){
					$s.=($s?', ':'').$u;
					$s2.=($s2?', ':'').getHChanged($t,$_POST[$t],$array_eintrag[$t]);
				}
			}
			foreach ($refs as $t){
				$pdate=rformdate($_POST[$t.'_date']);
				if ($_POST[$t.'_date']){
					$u=($pdate==$array_eintrag[$t.'_date']?'':'`'.$t.'_date'.'`=\''.mysql_real_escape_string($pdate)."'");
					if ($u){
						$s.=($s?', ':'').$u;
						$s2.=($s2?', ':'').getHChanged($t.'_date',$pdate,$array_eintrag[$t.'_date']);
					}

					$u=($_POST[$t.'_user']==$array_eintrag[$t.'_user']?'':'`'.$t.'_user'.'`=\''.mysql_real_escape_string($_POST[$t.'_user'])."'");
					if ($u){
						$s.=($s?', ':'').$u;
						$s2.=($s2?', ':'').getHChanged($t.'_user',getusername($_POST[$t.'_user']),getusername($array_eintrag[$t.'_user']));
					}
				} else {
					if ($array_eintrag[$t.'_date']!='' && $array_eintrag[$t.'_date']!='0000-00-00'){ //Eintrag gelöscht
						//echo "old: ".$array_eintrag[$t.'_date']."<br />";
						$u='`'.$t.'_date'.'`=\''."'";
						if ($u){
							$s.=($s?', ':'').$u;
							$s2.=($s2?', ':'').$t.' deleted';
						}
					}
				}
			}
			foreach ($wrefs as $t){
				for ($l=0;$l<=6;$l++){
					$pdate=rformdate($_POST[$t.'_'.$l.'_date']);
					if ($_POST[$t.'_'.$l.'_date']){
						$u=($pdate==$array_eintrag[$t.'_l'.$l.'_date']?'':'`'.$t.'_l'.$l.'_date'.'`=\''.mysql_real_escape_string($pdate)."'");
						if ($u){
							$s.=($s?', ':'').$u;
							$s2.=($s2?', ':'').getHChanged($t.'_l'.$l.'_date',$pdate,$array_eintrag[$t.'_l'.$l.'_date']);
						}
						if ($_POST[$t.'_'.$l.'_userbox']!=0){
							$u=($_POST[$t.'_'.$l.'_userbox']==$array_eintrag[$t.'_l'.$l.'_user']?'':'`'.$t.'_l'.$l.'_user'.'`=\''.mysql_real_escape_string($_POST[$t.'_'.$l.'_userbox'])."'");
							if ($u){
								$s.=($s?', ':'').$u;
								$s2.=($s2?', ':'').getHChanged($t.'_l'.$l.'_user',getusername($_POST[$t.'_'.$l.'_userbox']),getusername($array_eintrag[$t.'_l'.$l.'_user']));
							}
						}
					}
				}
			}
		} else {  // type== institution
				
		}


		$sql.=$s." WHERE id ='$id'";

		$bearbeitungsdatum = date("Y-m-d");
		$edit->writeHistory($id, getHistEntry("DF", "edit", $s2));
		//echo $sql;
		/*	foreach ($_POST as $k=>$v){
		echo "\"$k\", ";
		}*/
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
		 `bildgattungen_set` = '$bildgattungen_set' WHERE `id` ='$_POST[hidden_id]' LIMIT 1"; */
		$result = mysql_query($sql);
	}

	//////////////Formdaten aus Tabelle 'fotografen'  holen////////////////////////////
	$sql = "SELECT * FROM $type WHERE id ='$id'";
	$result = mysql_query($sql);
	$array_eintrag = mysql_fetch_array($result);
	//////////////Formdaten aus Tabelle 'fotografen'  holen////////////////////////////
	if ($type == 'fotografen'){

		$def->assign("LEGEND", "<b>".$spr['fotografennamen']."</b>");
		$def->parse("bearbeiten.form.fieldset_start");
		$def->parse("bearbeiten.form");
		$edit->namendf($id);
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

		gendatnoedit($def,$spr['erstellungsdatum'],$array_eintrag['erstellungsdatum']);

		gendatnoedit($def,$spr['letzte_aktualisierung'],$array_eintrag['bearbeitungsdatum']);


		//$arr_bearbeitungstiefe=array(0=>'',1=>'Lokal',2=>'Regional',3=>'Kantonal',4=>'National (Übersetzungen D/F/I)',5=>'International (Übersetzungen D/F/I)');
		$arr_bearbeitungstiefe=array(0=>'0: L&auml;rm',1=>'1: K U Be Bi A',2=>'2: K U Be Bi A W',3=>'3: K U Be Bi A W S I',4=>'4: K U Be Bi A W S I Ue (I/F/D)',5=>'5: K U Be Bi A W S I Ue (I/F/D/E)');
		genselectitem($def, $spr['bearbeitungstiefe'], $array_eintrag['bearbeitungstiefe'], "bearbeitungstiefe", $arr_bearbeitungstiefe, "", "", "");
		//genformitem($def,'edittext',$spr['biografie'],$array_eintrag['kurzbio'],'kurzbio');

		genstempel1($def, $spr['bibliografie'].': '.$spr['fertig_gestellt'],'biografie',$array_eintrag);
		genstempel1($def, $spr['ausstellungen'].': '.$spr['fertig_gestellt'],'ausstellungen',$array_eintrag);
		genstempel1($def, $spr['auszeichnungen_stipendien'].': ','auszeichnungen_stipendien',$array_eintrag);
		genstempel1($def, $spr['bestaende2'].': '.$spr['fertig_gestellt'],'bestaende',$array_eintrag);
		genstempel1($def, $spr['interview_vorgesehen'],'interview_vorgesehen',$array_eintrag);
		genstempel1($def, $spr['interview_fertiggestellt'],'interview_fertiggestellt',$array_eintrag);

		genformitem($def,'edittext','Aenderungsverfolgung',$array_eintrag['history'],'history_b');


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

		gennoedit($def, $spr['originalsprache'],$array_eintrag['originalsprache']);


		genstempel2($def, $spr['werdegang'],'werdegang',$array_eintrag,0);

		genstempel2($def, $spr['schaffensbeschrieb'],'schaffensbeschrieb',$array_eintrag,0);

		genstempel2($def, $spr['uebersetzung'].' de','uebersetzung_de',$array_eintrag,1);

		genstempel2($def, $spr['uebersetzung'].' fr','uebersetzung_fr',$array_eintrag,1);

		genstempel2($def, $spr['uebersetzung'].' it','uebersetzung_it',$array_eintrag,1);

		genstempel2($def, $spr['uebersetzung'].' rm','uebersetzung_rm',$array_eintrag,1);

		genstempel2($def, $spr['uebersetzung'].' en','uebersetzung_en',$array_eintrag,1);

		$def->parse("bearbeiten.form.tend");
		$def->parse("bearbeiten.form");

		$def->parse("bearbeiten.form.fieldset_end");
		$def->parse("bearbeiten.form");
		mabstand($def);

		$def->assign("LEGEND", "<b>".$spr['dokumentation']."</b>");
		$def->parse("bearbeiten.form.fieldset_start");
		$def->parse("bearbeiten.form");
		$def->parse("bearbeiten.form.start");
		$def->parse("bearbeiten.form");

		///
		//gencheckarrayitem($def, $spr['kanton'], $array_set_list, "kanton[]", $array_set);


		$arr_dokumentation=array('Haengemappen'=>'H&auml;ngemappen','Archivschachteln'=>'Archivschachteln','Elektronisch'=>'Elektronisch'); //Array füllen für Select
		$set= $array_eintrag['dokumentation'];
		$array_set = explode (",", $set);
		gencheckarrayitemKv($def, $spr['dokumentation'], $arr_dokumentation, "dokumentation[]",$array_set);

		genformitem($def,'textfield','Dokumentation_Beschreibung',$array_eintrag['dokumentation_text'],'dokumentation_text');
		genstempel1($def, 'dokumentation_erfasst','dokumentation',$array_eintrag);

		genformitem($def,'edittext',$spr['notiz'],$array_eintrag['notiz_fiche'],'notiz_fiche');

		$def->parse("bearbeiten.form.fieldset_end");
		//$def->parse("bearbeiten.form");
		$def->parse("bearbeiten.bearbeitungsdatum");
		$def->parse("bearbeiten.speichern");
		$def->parse("bearbeiten");
		//$def->parse("bearbeiten");
		$out.=$def->text("bearbeiten");
		//$def->out("bearbeiten");
	} else {  // institution
		$def->assign("LEGEND", "<b>".$spr['institution']."</b>");
		$def->parse("bearbeiten.form.fieldset_start");
		$def->parse("bearbeiten.form");
		$edit->namendf($id,$array_eintrag['name']);
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

		gendatnoedit($def,$spr['erstellungsdatum'],$array_eintrag['erstellungsdatum']);

		gendatnoedit($def,$spr['letzte_aktualisierung'],$array_eintrag['bearbeitungsdatum']);


		$arr_bearbeitungstiefe=array(0=>'0:',1=>'1: Lokal',2=>'2: Regional',3=>'3: Kantonal',4=>'4: National (&Uuml;bersetzungen D/F/I)',5=>'5: International (&Uuml;bersetzungen D/F/I/E)');
		//$arr_bearbeitungstiefe=array(0=>'0: L&auml;rm',1=>'1: K U Be Bi A',2=>'2: K U Be Bi A W',3=>'3: K U Be Bi A W S I',4=>'4: K U Be Bi A W S I Ue (I/F/D)',5=>'5: K U Be Bi A W S I Ue (I/F/D/E)');
		genselectitem($def, $spr['bearbeitungstiefe'], $array_eintrag['bearbeitungstiefe'], "bearbeitungstiefe", $arr_bearbeitungstiefe, "", "", "");
		//genformitem($def,'edittext',$spr['biografie'],$array_eintrag['kurzbio'],'kurzbio');

/*		genstempel1($def, $spr['bibliografie'].': '.$spr['fertig_gestellt'],'biografie',$array_eintrag);
		genstempel1($def, $spr['ausstellungen'].': '.$spr['fertig_gestellt'],'ausstellungen',$array_eintrag);
		genstempel1($def, $spr['auszeichnungen_stipendien'].': ','auszeichnungen_stipendien',$array_eintrag);
		genstempel1($def, $spr['bestaende2'].': '.$spr['fertig_gestellt'],'bestaende',$array_eintrag);
		genstempel1($def, $spr['interview_vorgesehen'],'interview_vorgesehen',$array_eintrag);
		genstempel1($def, $spr['interview_fertiggestellt'],'interview_fertiggestellt',$array_eintrag); */

		genformitem($def,'edittext','Aenderungsverfolgung',$array_eintrag['history'],'history_b');


		$def->parse("bearbeiten.form.tend");
		$def->parse("bearbeiten.form");

		$def->parse("bearbeiten.form.fieldset_end");
		$def->parse("bearbeiten.form");
		mabstand($def);

		$def->assign("LEGEND", "<b>".$spr['vorgeseheneArbeiten']."</b>");
		$def->parse("bearbeiten.form.fieldset_start");
		$def->parse("bearbeiten.form");
		$def->parse("bearbeiten.form.start");
		$def->parse("bearbeiten.form");

		gennoedit($def, $spr['originalsprache'],$array_eintrag['originalsprache']);


		genstempel2($def, $spr['werdegang'],'werdegang',$array_eintrag,0);

		genstempel2($def, $spr['schaffensbeschrieb'],'schaffensbeschrieb',$array_eintrag,0);


		$def->parse("bearbeiten.form.tend");
		$def->parse("bearbeiten.form");

		$def->parse("bearbeiten.form.fieldset_end");
		$def->parse("bearbeiten.form");
		mabstand($def);

		$def->assign("LEGEND", "<b>".$spr['aktualisierung']."</b>");
		$def->parse("bearbeiten.form.fieldset_start");
		$def->parse("bearbeiten.form");
		$def->parse("bearbeiten.form.start");
		$def->parse("bearbeiten.form");


		//genstempel2($def, $spr['aktualisierung'],'aktualisierung',$array_eintrag,1);
		genstempel1($def, $spr['aktualisierung'],'dokumentation',$array_eintrag);

		$def->parse("bearbeiten.form.tend");
		$def->parse("bearbeiten.form");

		$def->parse("bearbeiten.form.fieldset_end");
		$def->parse("bearbeiten.form");
		mabstand($def);		

		$def->assign("LEGEND", "<b>".$spr['uebersetzung']."</b>");
		$def->parse("bearbeiten.form.fieldset_start");
		$def->parse("bearbeiten.form");
		$def->parse("bearbeiten.form.start");
		$def->parse("bearbeiten.form");


		genstempel2($def, $spr['uebersetzung'].' de','uebersetzung_de',$array_eintrag,1);

		genstempel2($def, $spr['uebersetzung'].' fr','uebersetzung_fr',$array_eintrag,1);

		genstempel2($def, $spr['uebersetzung'].' it','uebersetzung_it',$array_eintrag,1);

		genstempel2($def, $spr['uebersetzung'].' rm','uebersetzung_rm',$array_eintrag,1);

		genstempel2($def, $spr['uebersetzung'].' en','uebersetzung_en',$array_eintrag,1);

		$def->parse("bearbeiten.form.tend");
		$def->parse("bearbeiten.form");

		$def->parse("bearbeiten.form.fieldset_end");
		$def->parse("bearbeiten.form");
		mabstand($def);		
		
		
		$def->assign("LEGEND", "<b>".$spr['dokumentation']."</b>");
		$def->parse("bearbeiten.form.fieldset_start");
		$def->parse("bearbeiten.form");
		$def->parse("bearbeiten.form.start");
		$def->parse("bearbeiten.form");

		///
		//gencheckarrayitem($def, $spr['kanton'], $array_set_list, "kanton[]", $array_set);


		$arr_dokumentation=array('Haengemappen'=>'H&auml;ngemappen','Archivschachteln'=>'Archivschachteln','Elektronisch'=>'Elektronisch'); //Array füllen für Select
		$set= $array_eintrag['dokumentation'];
		$array_set = explode (",", $set);
		gencheckarrayitemKv($def, $spr['dokumentation'], $arr_dokumentation, "dokumentation[]",$array_set);

		genformitem($def,'textfield','Dokumentation_Beschreibung',$array_eintrag['dokumentation_text'],'dokumentation_text');
		genstempel1($def, 'dokumentation_erfasst','dokumentation',$array_eintrag);

		genformitem($def,'edittext',$spr['notiz'],$array_eintrag['notiz_fiche'],'notiz_fiche');

		$def->parse("bearbeiten.form.fieldset_end");
		//$def->parse("bearbeiten.form");
		$def->parse("bearbeiten.bearbeitungsdatum");
		$def->parse("bearbeiten.speichern");
		$def->parse("bearbeiten");
		//$def->parse("bearbeiten");
		$out.=$def->text("bearbeiten");
		//$def->out("bearbeiten");
		
	}
}
?>

