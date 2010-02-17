<?php

//DEFINE USER_LEVELS FOR DIFFERENT USER-TYPES
global $USER_GUEST_READER;
global $USER_GUEST_READER_PARTNER;
global $USER_WORKER;
global $USER_SUPER_USER;

$USER_GUEST_READER = 2;
$USER_GUEST_READER_PARTNER = 4;
$USER_WORKER = 8;
$USER_SUPER_USER = 9;

function auth_level($level){
	return ($_SESSION['usr_level'] >= $level)? true:false;
}

function testauth(){
	if (empty($_SESSION['s_uid'])){
		header ("Location: ?a=login&error=1");
		exit();
	}
}


?>
