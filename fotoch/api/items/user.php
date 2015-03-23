<?php


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
			$success = true;
			$res=$fetch;
		}
		
	}
	if ($success){
		$out['status']='ok'; print_r($fetch);
		$out['token']=getToken($user,$level);
		pushfields($out,$res,array('vorname','nachname','email','level'));
	} else {
		$out['status']='nok';
	}
	
	
} elseif ($b=='logout'){
	$out['status']=(logOff($token)?'ok':'nok');
}

jsonout($out);
?>
