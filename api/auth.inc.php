<?php


define("USER_GUEST_READER",2);
define("USER_GUEST_FOTOS", 3);
define("USER_GUEST_READER_PARTNER", 4);
define("USER_WORKER", 8);
define("USER_SUPER_USER", 9);

function auth_level($level){
	global $userlevel;
	return ($userlevel >= $level);
}

function testauth_level($level){
	global $userlevel;
	if ($userlevel < $level){
		header ("Location: ./?a=login&error=1");
		exit();
	}
}

function testauth(){
	global $userlevel;
	if ($userlevel<=0){
		header ("Location: ./?a=login&error=1");
		exit();
	}
}

function testInstitution($toTest) {
	global $inst_comment;
	if($toTest == $inst_comment) {
		return true;
	}
	return false;
}

function auth_user($field = false) {
	global $authToken;
	$info = getTokenInfo($authToken);
	if(! $field) {
		return $info;
	} else {
		return $info[$field];
	}
}

function testauthedit(){   // sind editierrechte vorhanden?
	global $userlevel;
	if ($userlevel < USER_WORKER){
		header ("Location: ./?a=login&error=1");
		exit();
	}
}

function getAuthFromHeader(){
	global $userlevel;
	global $inst_comment;
	global $authToken;
	if (array_key_exists('X-AuthToken', $_GET)){
	    $headers['X-AuthToken']=$_GET['X-AuthToken'];
	} else {
		$headers = apache_request_headers(); 
	}
	if (array_key_exists('X-AuthToken', $headers) && $headers['X-AuthToken']){
		$authToken = $headers['X-AuthToken'];
	    $at=$headers['X-AuthToken'];
	    $tinfo=getTokenInfo($at);
	    //$userlevel=testToken($at);
	    $userlevel=$tinfo['level'];
	    $inst_comment=$tinfo['inst_comment'];
	}
}
getAuthFromHeader();

?>
