<?php
//global stuff
if (stristr($_SERVER["SERVER_NAME"],'fotobe')){
    header("Location: /fotobe/index.php?action=home");
}
header('Content-type: text/html; charset=utf-8');

ini_set('display_errors', 0);
error_reporting(E_ERROR | E_WARNING | E_PARSE);
require('config.inc.php');
require("templates/xtemplate.class.php");
require("mysql.inc.php");
require("nav.inc.php");
session_start();

$_SESSION['lastactions'];
$_SESSION['url'];
//set language
if($_COOKIE['lang'] == ""){
	$language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
} else {
	$language = $_COOKIE['lang'];
}
if($_GET['lang']!=""){
	$language = $_GET['lang'];
}
$supported_langs = array("de","fr","it","en","rm");//missing: "en","it",,"rm"
if(!in_array($language ,$supported_langs)) $language = "de";
if($_GET['clang']!=""){   // content_language
	$clanguage = $_GET['clang'];
}
if(!in_array($clanguage ,$supported_langs)) $clanguage = "de";

//für (aktuelle) sprache ein cookie setzen für ca ein halbes jahr:
setcookie("lang", $language, (time() + (60*60*24*183)));
//put this not before the initial language setting
//define action
require("lang.inc.php");
require("auth.inc.php");
require("log.inc.php");

log_session();

$action=$_GET['a'];
$actions=array("newsearch","editpages","fotograph","repertorium","partner","links","ueberuns","impressum","institution","ausstellung","fotos","bestand","glossar","hilfe","home", "literatur","login","logout","aedit","bedit","edit","gedit","iedit","ledit","user","uedit","dfedit","pndtest","statistik","export","ablauf");
if (!in_array($action,$actions)) $action='home';  // default Startseite
//functions
//chose main template
//print_r($spr);
//echo($spr['fotoch']);
if(auth_level(USER_WORKER)){
	if ($action=='export')
		$xtpl = new XTemplate("templates/main_export.xtpl");
	else
		$xtpl = new XTemplate("templates/main_intern.xtpl");
}
else {
	if($action == "fotograph" || $action == "bestand" || $action == "institution"|| $action == "ausstellung" || $action == "fotos" || $action == "newsearch"){
        $xtpl = new XTemplate("templates/main_lexirepi.xtpl");
		include("sites/navhistory.php");
		$xtpl->assign("SUBNAVI", $navigationhistory);
	}
	else{
		$xtpl=new XTemplate ("templates/main.xtpl");
		//template = main.xtpl, the default was not overwritten.
		include("img.inc.php");
		$placeholders = shakeImages();
		//print_r($placeholders);
		$xtpl->assign("IMGhref",$placeholders);
	}
    $xtpl->assign($action, " class='selected'");
}
($_SESSION['usr_level'] != "") ? $xtpl->assign("LOG","logout"):$xtpl->assign("LOG","login");
$xtpl->assign("USER",' '.$_SESSION['s_uid']);

$xtpl->assign("SPR",$spr);  // load all languagecontent!

$xtpl->assign("LANG",$language);
$langSwitch.= $language != 'de' ? (' <a href="?'.changeurl("de").'">de</a>') : '';
$langSwitch.= $language != 'fr' ? (' <a href="?'.changeurl("fr").'">fr</a>') : '';
$langSwitch.= $language != 'it' ? (' <a href="?'.changeurl("it").'">it</a>') : '';
$xtpl->assign("LANGSWITCH", $langSwitch);

//assign action
$xtpl->assign("ACTION",$action);
//choose content sites
$adminActions = array("newsearch", "ausstellung", "fotos", "bestand", "fotograph", "glossar", "institution", "literatur","user","statistik","export","ablauf");
$editActions = array("edit", "aedit", "bedit","gedit", "iedit", "ledit","uedit","dfedit");
if(in_array($action,$adminActions)){
	if((($action == "glossar") || ($action == "literatur") || ($action == "user") || ($action == "statistik")) && empty($_SESSION['s_uid'])){
		$action = "login";
		include("sites/".$action.".php");
	} else {
		include("sites/".$action."/".$action.".php");
	}
} else {
	if(in_array($action,$editActions)) {
		if(auth_level(USER_WORKER)){
			include("edit/".$action.".php");
		} else {
			$action = "login";
			include("sites/".$action.".php");
		}
	} else {
		include("sites/".$action.".php");
	}
}
$kategorie='A';
if ($action=='fotograph') $kategorie='L';
if ($action=='institution' || $action=='bestand') $kategorie='R';
$search= 0 + $issearch;

log_page($kategorie,$search,$action,$language,$_SESSION['usr_level'],$_SERVER['REQUEST_URI']);
//the def variable contains the specific template-entities defined
//in the file included in $action.php
$xtpl->assign("OUT", $out);
if(auth_level(USER_GUEST_FOTOS)){
	$xtpl->parse("main.fotos");
}
$xtpl->parse("main");
$xtpl->out("main");
?>
