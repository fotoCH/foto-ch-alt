<?php


define("USER_GUEST_READER",2);
define("USER_GUEST_READER_PARTNER", 4);
define("USER_WORKER", 8);
define("USER_SUPER_USER", 9);

function auth_level($level){
	return ($_SESSION['usr_level'] >= $level);
}

function testauth(){
	if (empty($_SESSION['s_uid'])){
		header ("Location: ?a=login&error=1");
		exit();
	}
}

function testauthedit(){   // sind editierrechte vorhanden?
	if (empty($_SESSION['s_uid']) || ($_SESSION['usr_level'] < USER_WORKER)){
		header ("Location: ?a=login&error=1");
		exit();
	}
}



?>
