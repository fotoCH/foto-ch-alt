<?php

function log_session(){
	$sid = session_id();
	$ua=mysql_real_escape_string($_SERVER['HTTP_USER_AGENT']);
	$req=mysql_real_escape_string($_SERVER['REQUEST_URI']);
	$sql="INSERT INTO log_sessions (`session_id`, `start`, `last`,`useragent`,`firstpage`) VALUES ('".$sid."',NOW(),NOW(),'".$ua."','".$req."')";
	mysql_query($sql);
	$sql="UPDATE log_sessions SET `last`=NOW(), `count`=`count`+1, `seconds`=TIMESTAMPDIFF(SECOND,`start`,`last`)  WHERE `session_id`='".$sid."'";
	//echo $sql;	
	mysql_query($sql);
}

function log_setLevel(){
	$sid = session_id();
	$sql="UPDATE log_sessions SET `level`=".$_SESSION['usr_level']."  WHERE `session_id`='".$sid."'";
	//echo $sql;	
	mysql_query($sql);
}


?>