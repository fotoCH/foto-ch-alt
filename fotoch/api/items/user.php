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
			$inst_comment=$fetch['inst_comment'];
			$success = true;
			$res=$fetch;
			// break after user has been found
			break;
		}
		
	}
	if ($success){
		$out['status']='ok';
		$out['token']=getToken($user,$level,$inst_comment);
		pushfields($out,$res,array('vorname','nachname','email','level','inst_comment'));
	} else {
		$out['status']='nok';
	}
	
	
} elseif ($b=='logout'){
	$out['status']=(logOff($authToken)?'ok':'nok');
} elseif ($b=='info'){
	$out=getTokenInfo($tok);
}

jsonout($out);
?>
