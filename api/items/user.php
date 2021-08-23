<?php

function getStocks($user_id){
	global $sqli;
	$query = "SELECT bu.bestand_id, bestand.name FROM bestand_users as bu INNER JOIN bestand on bu.bestand_id=bestand.id WHERE bu.user_id=" . $user_id;
	$result = mysqli_query($sqli, $query);
	$stocks = array();
	while ($fetch = mysqli_fetch_array($result)){
		$stocks[$fetch['bestand_id']] = $fetch['name'];
	}
	return $stocks;

}

$b=getClean('b');
$user=getClean('user');
$password=getClean('password');
$tok=getClean('token');

if ($b=='login'){
	$query = "SELECT * FROM users WHERE username='$user'";
	$result = mysqli_query($sqli, $query);
	$success = false;
	while($fetch = mysqli_fetch_array($result)){
		if($fetch['password'] == md5($password)){
			$level = $fetch['level'];
			$inst_comment=$fetch['inst_comment'];
			$success = true;
			$userId = $fetch['id'];
			$res=$fetch;
			// break after user has been found
			break;
		}
		
	}
	if ($success){
		$out['status']='ok';
		$out['token']=getToken($user,$level,$inst_comment);
		$out['stocks'] = getStocks($userId);
		pushfields($out,$res,array('vorname','nachname','email','level','inst_comment'));
	} else {
		$out['status']='nok';
	}
	
	
} elseif ($b=='logout') {
	
	$query = "SELECT * FROM auth WHERE token=?";
	$stmt = mysqli_prepare($sqli, $query);
	mysqli_stmt_bind_param($stmt, "s", $tok);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $result);
	mysqli_stmt_fetch($stmt);

	$out['result'] = $result;

	$out['status']=(logOff($tok)?'ok':'nok');
} elseif ($b=='info'){
	$out=getTokenInfo($tok);
}

jsonout($out);
?>
