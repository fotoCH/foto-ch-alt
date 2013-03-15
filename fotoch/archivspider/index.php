<?php

header('Content-type: text/html; charset=utf-8');

ini_set('display_errors', 1);
error_reporting(E_ALL && ~E_NOTICE);
require("templates/xtemplate.class.php");
require("mysql.inc.php");

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

$action=$_GET['a'];
$actions=array("editpages","fotograph","repertorium","partner","links","kontakt","impressum","institution","ausstellung","bestand","glossar","handbuch","home","literatur","login","logout","aedit","bedit","edit","gedit","iedit","ledit","user","uedit","dfedit","pndtest","statistik","export","ablauf");
if (!in_array($action,$actions)) $action='home';  // default Startseite
//functions
//chose main template
//print_r($spr);
//echo($spr['fotoch']);
($_SESSION['usr_level'] != "") ? $xtpl->assign("LOG","logout"):$xtpl->assign("LOG","login");
$xtpl->assign("USER",' '.$_SESSION['s_uid']);

$xtpl->assign("SPR",$spr);  // load all languagecontent!

$xtpl->assign("LANG",$language);
$xtpl->assign("URLDE", changeurl("de"));
$xtpl->assign("URLFR", changeurl("fr"));
$xtpl->assign("URLIT", changeurl("it"));


//assign action
$xtpl->assign("ACTION",$action);
//choose content sites 
$adminActions = array("ausstellung", "bestand", "fotograph", "glossar", "institution", "literatur","user","statistik","export","ablauf");
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

$xtpl->assign("OUT", $out);
$xtpl->parse("main");
$xtpl->out("main");
?>
