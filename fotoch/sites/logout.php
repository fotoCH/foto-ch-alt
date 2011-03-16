<?php

$_SESSION['s_uid'] == "";
$_SESSION['usr_level'] == 0;
$_SESSION = array();

if (ini_get("session.use_cookies")) {
	$params = session_get_cookie_params();
	setcookie(session_name(), '', time() - 42000,
	$params["path"], $params["domain"],
	$params["secure"], $params["httponly"]
	);
}

session_destroy();
$lang = $_GET['lang'];
$msg= "<h1>Logout</h1>Sie sind ausgeloggt. Es geht gleich weiter... ";
$msg.= "<meta http-equiv=\"refresh\" content=\"1; URL=$_SERVER[PHP_SELF]?a=fotograph&amp;lang=$lang\">";
$xtpl->assign("LOG","login");
//$xtpl->assign("OUT",$msg);
//$xtpl->assign("LOGOUT",$msg);
$logout = new XTemplate("templates/contents.xtpl");
$logout->assign("CONTENT",$msg);
$logout->parse("contents.home_detail");
$out.=$logout->text("contents.home_detail");

?>
