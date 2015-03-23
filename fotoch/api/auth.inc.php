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

function testauthedit(){   // sind editierrechte vorhanden?
	global $userlevel;
	if ($userlevel < USER_WORKER){
		header ("Location: ./?a=login&error=1");
		exit();
	}
}

function getAuthFromHeader(){
	global $userlevel;
	$headers = apache_request_headers(); 
	if ($headers['X-AuthToken']){
	    $at=$headers['X-AuthToken'];
	    $userlevel=testToken($at);
	}
}

getAuthFromHeader();

?>
