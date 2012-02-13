<?php


error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set ('error_reporting', E_ALL);
/////////////////////////////
include("./fotofunc.inc.php");
include("./backend.inc.php");
include("./dfedit.inc.php");
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


//////////////Bildgattugnen zur Speicherung in DB aufbereiten////////////////////////////
//////////////Grundsätzliches: Template, assigns ect.////////////////////////////
if ($fertig==1){
} else {
	$def->assign("ID",$_GET['id']);
	$def->assign("heute",date('d.m.Y'));
	$def->assign("user",$_SESSION['s_uid']);
	$def->assign("userid",$_SESSION['s_id']);
	$id=$_GET['id'];

	if ($type == 'fotografen'){
		$gen = new DokufichenFotografFormBuilder($def);
	} else {  // institution
		$gen = new DokufichenInstitutionFormBuilder($def);
	}
	$gen->init($id);

	if($_POST['submitbutton']){
		//////////////Formdaten aus Tabelle 'fotografen'  holen////////////////////////////
		$array_eintrag = $gen->formData;
		//print_r($_POST);
		foreach ($_REQUEST['dokumentation'] as $t){
			$dokumentation .=$t;
			$dokumentation .=",";
		}
		$dokumentation = substr($dokumentation, 0, strlen($dokumentation)-1);
		$_POST['dokumentation']=$dokumentation;

		$fs=array("projektname", "territoriumszugegoerigkeit", "bearbeitungstiefe","dokumentation","dokumentation_text","notiz_fiche");
		$refs=array("biografie", "ausstellungen", "auszeichnungen_stipendien", "bestaende", "interview_vorgesehen", "interview_fertiggestellt","dokumentation");
		$wrefs=array("werdegang", "schaffensbeschrieb", "uebersetzung_de", "uebersetzung_fr", "uebersetzung_it", "uebersetzung_rm", "uebersetzung_en");
		$multirefs=array("aktualisierung");
		$sql="UPDATE $type SET ";
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
			for ($ln=-1;$ln<=6;$ln++){  // neu l7: geschrieben
				$l=($ln==-1?7:$ln); 
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
		foreach ($multirefs as $t){
			$update = unserialize($array_eintrag[$t]);
			//last editable position
			$first_l = max(0,(sizeof($update)/2)-1);
			$changed = false;
			for($l=$first_l;$l<$first_l+2;$l++) {
				$pdate=rformdate($_POST[$t.'_'.$l.'_date']);
				if( $pdate!=$update[$t.'_'.$l.'_date'] ) {
					if(empty($pdate)){
						unset($update[$t.'_'.$l.'_date']);
					} else {
						$update[$t.'_'.$l.'_date'] = mysql_real_escape_string($pdate);
					}
					$s2.=($s2?', ':'').getHChanged($t.'_'.$l.'_date',$pdate,$array_eintrag[$t.'_'.$l.'_date']);
					$changed = true;
				}
				if( $_POST[$t.'_'.$l.'_user']!=$update[$t.'_'.$l.'_user'] ) {
					if(empty($_POST[$t.'_'.$l.'_user'])){
						unset($update[$t.'_'.$l.'_user']);	
					} else {
						$update[$t.'_'.$l.'_user'] = mysql_real_escape_string($_POST[$t.'_'.$l.'_user']);
					}
					$s2.=($s2?', ':'').getHChanged($t.'_'.$l.'_user',getusername($_POST[$t.'_'.$l.'_user']),getusername($array_eintrag[$t.'_'.$l.'_user']));
					$changed = true;
				}
			}
			if( $changed ) {
				$s .= " `$t`='".mysql_real_escape_string(serialize($update))."' ";
			}
		}

		$sql.=$s." WHERE id ='$id'";

		$bearbeitungsdatum = date("Y-m-d");
			
		$gen->edit->writeHistory($id, getHistEntry("DF", "edit", $s2));

		$result = mysql_query($sql);
	}

	//////////////Form generieren////////////////////////////
	$gen->init($id);
	$gen->generate();
	$out .= $gen->out(); 
}
?>

