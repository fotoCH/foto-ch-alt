<?php

include("././fotofunc.inc.php");
include("././foto-ch.inc.php");

$b=getClean('b');
$user=getClean('user');
$password=getClean('password');
$tok=getClean('token');

if ($b=='login'){
	$query = "SELECT * FROM users WHERE username='$user'";
	$result = mysql_query($query);
	$success = false;
	while($fetch = mysql_fetch_array($result)){
		if($fetch['password'] == md5($password)){
			$level = $fetch['level'];
			
		}
		
	}
	if ($success){
		$out['status']='ok';
		$token=getToken($user,$level);
		pushfields($out,$fetch,array('vorname','nachname','email','level'));
	} else {
		$out['status']='nok';
	}
	
	
} elseif ($b=='logout'){
	$out['status']=(logOff($token)?'ok':'nok');
}

jsonout($out);
?>
