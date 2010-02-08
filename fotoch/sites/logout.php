<?

	$_SESSION[s_uid]=="";											
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
