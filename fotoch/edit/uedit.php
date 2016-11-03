<?php
/////////////////////////////
include("./fotofunc.inc.php");
include("./backend.inc.php");
testauthedit();
$def=new XTemplate ("./templates/edit.xtpl");
$def->assign("ACTION",$_GET['a']);
$def->assign("id",$_GET['id']);
$lang = $_GET['lang'];
$def->assign("LANG",$lang);

$def->assign("SPR",$spr);

if ($_POST) escposts();

if ($_GET['id']=="new"){
	$sql="INSERT INTO `users` ( `id` , `username` ) VALUES (NULL,'')";
	$result = mysqli_query($sqli, $sql);

	$last_insert_id = mysqli_insert_id($sqli);

}

$del=$_GET['delete'];

if ($del=="2"){
	$id=$_GET['id'];
	$sql = "DELETE FROM `users` WHERE id=$id LIMIT 1";
	
	$result = mysqli_query($sqli, $sql);



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
if($_POST['submitbutton']){



	//////////////Formdaten in Tabelle 'fotografen' eintragen bzw aktualisieren////////////////////////////

//	$bearbeitungsdatum = date("Y-m-d");
	$sql = "UPDATE `users` SET `username` = '$_POST[username]',
	`vorname` = '$_POST[vorname]',
	`nachname` = '$_POST[nachname]',
	`email` = '$_POST[email]',
	`password` = '".($_POST['password']?md5($_POST['password']):'').
	"', `level` = $_POST[level]
	WHERE `id` =$_POST[hidden_id] LIMIT 1";
	echo $sql;
	$result = mysqli_query($sqli, $sql);
}

//////////////Grundsätzliches: Template, assigns ect.////////////////////////////
if ($fertig==1){
} else {
	if($last_insert_id){
		$def->assign("ID",$last_insert_id);
		$id=$last_insert_id;
		//$def->assign("LANG",$_GET['lang']);
		$def->assign("NEW_ENTRY_MSG", $spr['newentrymsg']."<br/>");
	}else{
		$def->assign("ID",$_GET['id']);
		$id=$_GET['id'];
		$lang = $_GET['lang'];
		//$def->assign("LANG",$lang);
	}

	

	//////////////Formdaten aus Tabelle 'fotografen'  holen////////////////////////////
	$sql = "SELECT * FROM `users` WHERE id ='$id'";
	$result = mysqli_query($sqli, $sql);
	$array_eintrag = mysqli_fetch_array($result);
	
	$def->assign("NAME", $array_eintrag['username']);
	$def->parse("bearbeiten.bearbeiten_head_user");
	
	$def->assign("LEGEND", "<b>".'Userdetails'."</b><br/>");
	$def->parse("bearbeiten.form.fieldset_start");
	
	//$def->parse("bearbeiten.new_bestand");

	$def->parse("bearbeiten.form.start");


	genformitem($def,'textfield',$spr['username'],$array_eintrag['username'],'username');

	genformitem($def,'textfield',$spr['vorname'],$array_eintrag['vorname'],'vorname');
	genformitem($def,'textfield',$spr['nachname'],$array_eintrag['nachname'],'nachname');
	
	genformitem($def,'textfield',$spr['email'],$array_eintrag['email'],'email');
	genformitem($def,'textfield','passwort neu setzen','','password');
	genformitem($def,'textfield','level (2|4|8)',$array_eintrag['level'],'level');
	
	//$def->assign("SPR.bearbeitungsdatum", '');  // funktioniert nicht
	$def->assign("bearbeitungsdatum", '');

	
	/*if(auth_level($USER_WORKER)){
		//$def->assign("NEU"," | <a href=\"./?a=gedit&amp;id=new&amp;lang=$lang\">$neuereintrag</a>");
	}else{
		//$def->assign("NEU","");
	}*/

	$def->parse("bearbeiten.form.fieldset_end");
	$def->parse("bearbeiten.bearbeitungsdatum");
	$def->parse("bearbeiten.speichern.neuloeschen");
	$def->parse("bearbeiten.speichern");
	$def->parse("bearbeiten");
		$out.=$def->text("bearbeiten");
	//$def->out("bearbeiten");
}
?>