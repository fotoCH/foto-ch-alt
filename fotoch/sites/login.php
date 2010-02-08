<?

$log=new XTemplate ("templates/contents.xtpl"); // neus Instanz des Login-Temaplte
$log->assign("BENUTZERNAME",getLangContent("sprache",$_GET['lang'],"benutzername"));
$log->assign("PASSWORT",getLangContent("sprache",$_GET['lang'],"passwort"));
$log->assign("SUBMIT", getLangContent("sprache",$_GET['lang'],"senden"));
$log->assign("LANG",$_GET['lang']);
$xtpl->assign("LANG", $_GET['lang']);

if ($_POST['usr_uid']=="fotobe" && $_POST['usr_pw']=="jrgntlspch"){
	$_SESSION['s_uid']=$_POST['usr_uid'];
	$_SESSION['s_pw']=$_POST['usr_pw'];
	$log->parse("contents.log.ok");
	$xtpl->assign("LOG","logout");
 
  
} else {
	$log->assign("ACTION", $_GET['a']);
	$log->assign("ID", $_GET['id']);
	$log->parse("contents.log.form");
}
$log->parse("contents.log");
$out=$log->text("contents.log");
?>
