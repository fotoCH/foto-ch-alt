<?php

$log=new XTemplate ("templates/contents.xtpl"); // neus Instanz des Login-Temaplte
$log->assign("BENUTZERNAME",$spr['benutzername']);
$log->assign("PASSWORT",$spr['passwort']);
$log->assign("SUBMIT", $spr['senden']);
$log->assign("LANG",$_GET['lang']);
$xtpl->assign("LANG", $_GET['lang']);

if ($_POST['usr_uid'] !="" && $_POST['usr_pw']!=""){
	
	$query = "SELECT * FROM users";
	$result = mysql_query($query);
	$success = false;
	while($fetch = mysql_fetch_array($result)){
		if($fetch['username'] == $_POST['usr_uid'] && $fetch['password'] == md5($_POST['usr_pw'])){
			$_SESSION['usr_level'] = $fetch['level'];
			$_SESSION['s_uid'] = $_POST['usr_uid'];
			$log->parse("contents.log.ok");
			$out=$log->text("contents.log.ok");
			$success = true;
		}
		if(!$success){
			$out = print_error($log);			
		}		
	}
	
} else {
	$out = print_error($log);
}




function print_error($log){
	//$log=new XTemplate ("templates/contents.xtpl"); 
	$log->assign("ACTION", $_GET['a']);
	$log->assign("ID", $_GET['id']);
	$log->parse("contents.log.form");
	$log->parse("contents.log");
	return $log->text("contents.log");	
}
?>
