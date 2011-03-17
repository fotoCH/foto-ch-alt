<?php
///////////////////////////// testumlaut äöü
function procbestand($def,$array){
	$cufo=getallnam($array['fotografen_id']);
	//print_r($cufo);
	$c=count($cufo); // anzahl namen
	$cuf=$cufo[0]; // 1. Name
	if ($c>1){
		
		$def->assign('olabel','');
		$def->assign('ovalue','');
		$def->parse("bearbeiten.form.fotograf.mname.option");
			foreach ($cufo as $cc){
				if ($cc['nid']==$array['namen_id']){
					$cuf=$cc;
					$def->assign('selected',' selected="selected"');
				} else { 
					$def->assign('selected','');
				}
				$def->assign('olabel',$cc['namen']);
				$def->assign('ovalue',$cc['nid']);
				$def->parse("bearbeiten.form.fotograf.mname.option");
			
		}
	$cuf['fotografen_id']=$cuf['fid'];
	$cuf['bf_id']=$array['id'];
	//print_r($cuf);
	$def->assign("FOTOGRAF", $cuf);
	$def->parse("bearbeiten.form.fotograf.mname");
	}
	$cuf['fotografen_id']=$cuf['fid'];
	$cuf['bf_id']=$array['id'];
	//print_r($cuf);
	$def->assign("FOTOGRAF", $cuf);
	$def->parse("bearbeiten.form.fotograf");
}


include("./fotofunc.inc.php");
include("./backend.inc.php");
//error_reporting((E_ALL));
testauthedit();
$def=new XTemplate ("./templates/edit.xtpl");
$def->assign("ACTION",$_GET['a']);
$def->assign("id",$_GET['id']);
$def->assign("ID", $_GET['id']);
$lang = $_GET['lang'];
$def->assign("LANG", $lang);

$def->assign("SPR",$spr);

$def->assign("BEARBEITEN", "[&nbsp;".$spr['bearbeiten']."&nbsp;]");
$def->assign("LOESCHEN", "[&nbsp;".$spr['loeschen']."&nbsp;]");
$def->assign("EINTRAGLOESCHEN", "[&nbsp;".$spr['eintragloeschen']."&nbsp;]");
$def->assign("EINTRAGNEU", "[&nbsp;".$spr['neuereintrag']."&nbsp;]");


if ($_POST) escposts();
if ($_GET['id']=="new"){
	$sql = "INSERT INTO `bestand` ( `id` , `inst_id` , `name` , `fotografen` , `zeitraum` , `umfang` , `erschliessungsgrad` , `weiteres` , `fotografen_ref` , `convert_result` , `bildgattungen` , `bearbeitungsdatum` , `notiz` , `gesperrt` ) VALUES (NULL , '0', '', NULL , NULL , NULL , '', NULL , NULL , NULL , '', '0000-01-01', '', '0')";
	$result = mysql_query($sql);
	$last_insert_id = mysql_insert_id();
}
$del=$_GET['delete'];
if ($del=="2"){
	$id=$_GET['id'];
	$sql = "DELETE FROM `bestand` WHERE id=$id LIMIT 1";
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
if($_REQUEST['new_fotograf']){
	$sql="INSERT INTO `bestand_fotograf` (`bestand_id`, `fotografen_id`) VALUES ($_REQUEST[id],$_REQUEST[fotograf_id])";
	//echo $sql;
	$result = mysql_query($sql);
	$bearbeitungsdatum = date("Y-m-d");
	$sql = "UPDATE `fotografen` SET `bearbeitungsdatum` = '$bearbeitungsdatum' WHERE `id` ='$_REQUEST[fotograf_id]' LIMIT 1";
	$result = mysql_query($sql);
	$sql = "UPDATE `bestand` SET `bearbeitungsdatum` = '$bearbeitungsdatum' WHERE `id` ='$_REQUEST[id]' LIMIT 1";
	$result = mysql_query($sql);
}
if($_REQUEST['new_inst']){
	$sql="UPDATE `bestand` SET `inst_id`=$_REQUEST[institution_id] WHERE `id`=$_REQUEST[id]";
	//echo $sql;
	$result = mysql_query($sql);
	$bearbeitungsdatum = date("Y-m-d");
	$sql = "UPDATE `bestand` SET `bearbeitungsdatum` = '$bearbeitungsdatum' WHERE `id` ='$_REQUEST[id]' LIMIT 1";
	$result = mysql_query($sql);
}
//////////////Fotograf löschen////////////////////////////
if($_GET['f']=="del"){
	$sql = "DELETE FROM `bestand_fotograf` WHERE id='$_GET[f_id]' LIMIT 1";
	$result = mysql_query($sql);
	$bearbeitungsdatum = date("Y-m-d");
	$sql = "UPDATE `bestand` SET `bearbeitungsdatum` = '$bearbeitungsdatum' WHERE `id` ='$_GET[id]' LIMIT 1";
	$result = mysql_query($sql);
}
//////////////Fotograf namen bearbeiten////////////////////////////
if($_GET['f']=="edit"){
	$sql = "UPDATE `bestand_fotograf` set `namen_id`=$_GET[nid] WHERE id='$_GET[f_id]' LIMIT 1";
	$result = mysql_query($sql);
	$bearbeitungsdatum = date("Y-m-d");
	$sql = "UPDATE `bestand` SET `bearbeitungsdatum` = '$bearbeitungsdatum' WHERE `id` ='$_GET[id]' LIMIT 1";
	$result = mysql_query($sql);
}
//////////////Bildgattugnen zur Speicherung in DB aufbereiten////////////////////////////
if($_POST['submitbutton']){
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
	if($_POST['nachlass']=="1"){
		$nachlass = 1;
	}else{
		$nachlass = 0;
	}
	//////////////Formdaten in Tabelle 'fotografen' eintragen bzw aktualisieren////////////////////////////
	$bearbeitungsdatum = date("Y-m-d");
	$sql = "UPDATE `bestand` SET `name` = '$_POST[name]',
	
	`zeitraum` = '$_POST[zeitraum]',
	`umfang` = '$_POST[umfang]',
	`erschliessungsgrad` = '$_POST[erschliessungsgrad]',
	`weiteres` = '$_POST[weiteres]',
	`bildgattungen` = '$bildgattungen_set',
	`bearbeitungsdatum` = '$bearbeitungsdatum',
	`bestandsbeschreibung` = '$_POST[bestandsbeschreibung]',
	`link_extern` = '$_POST[link_extern]',
	`signatur` = '$_POST[signatur]',
	`copyright` = '$_POST[copyright]',
	`notiz` = '$_POST[notiz]',
	`nachlass` = $nachlass,
	`gesperrt` = $unpubliziert WHERE `id` =$_POST[hidden_id] LIMIT 1";
	$result = mysql_query($sql);
}
//////////////Grundsätzliches: Template, assigns ect.////////////////////////////
if ($fertig==1){
} else {
	if($last_insert_id){
		$def->assign("ID",$last_insert_id);
		$id=$last_insert_id;
		$def->assign("NEW_ENTRY_MSG", "<h3>".$spr['newentrymsg']."</h3><br/>");
		//$def->assign("LANG",$_GET['lang']);
	}else{
		$def->assign("ID",$_GET['id']);
		$id=$_GET['id'];
		//$def->assign("LANG",$_GET['lang']);
	}
	//////////////Formdaten aus Tabelle 'fotografen'  holen////////////////////////////
	$sql = "SELECT * FROM bestand WHERE id ='$id'";
	$result = mysql_query($sql);
	$array_eintrag = mysql_fetch_array($result);
	$def->assign('g',$array_eintrag['gesperrt']==1?'g':'');
//	$sql = "SELECT bestand_fotograf.fotografen_id,  bestand_fotograf.id AS bf_id, namen.nachname, namen.vorname, namen.namenszusatz FROM bestand_fotograf INNER JOIN (fotografen INNER JOIN namen ON fotografen.id=namen.fotografen_id) ON bestand_fotograf.fotografen_id=fotografen.id WHERE bestand_fotograf.bestand_id=$id ORDER BY namen.nachname ASC"; // Weitere Formdaten  // Weitere Formdaten aus Tabelle 'bestaende' holen
	$sql = "SELECT * FROM bestand_fotograf WHERE bestand_id=$id"; // Weitere Formdaten  // Weitere Formdaten aus Tabelle 'bestaende' holen
	$result = mysql_query($sql);
	if(mysql_num_rows($result)>0){
		$num=1;
		while($array=mysql_fetch_array($result)){
			$def->assign("NUM", $num);
			procbestand($def,$array);
			$num++;
		}
	}
	$def->assign('NAME',$array_eintrag['name']);
	$def->parse("bearbeiten.bearbeiten_head_bestand");
	
	
	$def->assign("LEGEND","<b>".$spr['fotographen']."</b>");
	$def->parse("bearbeiten.form.fieldset_start");
		
	$def->parse("bearbeiten.form.new_fotograf");
	$def->parse("bearbeiten.form");
	$def->parse("bearbeiten.form.fieldset_end");
	$def->parse("bearbeiten.form.tr");
	$def->parse("bearbeiten.form");
	
	$def->assign("LEGEND","<b>".$spr['bestand_details']."</b>");
	$def->parse("bearbeiten.form.fieldset_start");	
	$def->parse("bearbeiten.form.start");
	
	genformitem($def,'textfield',$spr['name'],$array_eintrag['name'],'name');
	//$def->assign('NAME',$array_eintrag['name']);
	gencheckitem($def,$spr['nachlass'],$array_eintrag['nachlass'],'nachlass');
	$sql = 'SELECT id , name FROM `institution` WHERE id='.$array_eintrag['inst_id'].' ORDER BY name';
	$result = mysql_query($sql);
	$fetch = mysql_fetch_array($result);
	
	$iname=$fetch['name'];
	
	
	//genselectitem($def, "Institution", $array_eintrag['inst_id'], "inst_id", $arr_inst, "", "", "");
	genformitem($def, 'noedit', $spr['Institution'], $iname, "inst_name");
	//$def->assign("LEGEND","<b>".$spr['intitution']."</b>");
	//$def->parse("bearbeiten.form.fieldset_start");
		
	$def->parse("bearbeiten.form.new_institution_bestand");
//	$def->parse("bearbeiten.form");
//	$def->parse("bearbeiten.form.fieldset_end");
//	$def->parse("bearbeiten.form.tr");
//	$def->parse("bearbeiten.form");
	
	genformitem($def,'instlink','',$array_eintrag['inst_id'],'');
	//genformitem($def,'textfield','Institution (id)',$array_eintrag['inst_id'],'inst_id');
	genformitem($def,'textfield',$spr['zeitraum'],$array_eintrag['zeitraum'],'zeitraum');
	genformitem($def,'edittext',$spr['bestandsbeschreibung'],$array_eintrag['bestandsbeschreibung'],'bestandsbeschreibung');
	genformitem($def,'textfield',$spr['link_extern'],$array_eintrag['link_extern'],'link_extern');
	genformitem($def,'textfield',$spr['signatur'],$array_eintrag['signatur'],'signatur');
	genformitem($def,'textfield',$spr['copy'],$array_eintrag['copyright'],'copyright');
	genformitem($def,'textfield',$spr['umfang'],$array_eintrag['umfang'],'umfang');
	genformitem($def,'textfield',$spr['erschliessungsgrad'],$array_eintrag['erschliessungsgrad'],'erschliessungsgrad');
	genformitem($def,'textfield',$spr['weitere_materialien'],$array_eintrag['weiteres'],'weiteres');
	$sql ="DESCRIBE bestand bildgattungen";//Beschreibung des Sets bekommen
	$result = mysql_query($sql);
	$fetch = mysql_fetch_array($result);
	$set_list = $fetch[Type];
	$set_list = substr($set_list, 5, strlen($set_list)-7);
	$array_set_list = explode ("','", $set_list);
	$set= $array_eintrag['bildgattungen'];
	$array_set = explode (",", $set);
	//genselectitem($def, $spr['bildgattungen'], $array_set, "bildgattungen", $array_set_list, "true", "", "");
	gencheckarrayitemtr($def, $spr['bildgattungen'], $array_set_list, $spatr['bildgattungen_uebersetzungen'], "bildgattungen[]", $array_set);
	genformitem($def,'edittext',$spr['notiz'],$array_eintrag['notiz'],'notiz');
	gencheckitem($def,$spr['npublizieren'],$array_eintrag['gesperrt'],'unpubliziert');
	$def->assign("bearbeitungsdatum", $array_eintrag['bearbeitungsdatum']);
	$def->parse("bearbeiten.form.fieldset_end");
	$def->parse("bearbeiten.neuloeschen");
	$def->parse("bearbeiten");
	$out.=$def->text("bearbeiten");
	//$def->out("bearbeiten");
}
?>
