<?php
//global stuff
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
$supported_langs = array("de","fr");//missing: "en","it",,"rm"
if(!in_array($language ,$supported_langs)) $language = "de";	
//für (aktuelle) sprache ein cookie setzen für ca ein halbes jahr:
setcookie("lang", $language, (time() + (60*60*24*183)));
//put this not before the initial language setting
//define action
require("lang.inc.php");

$action=$_GET['a'];
$actions=array("editpages","fotograph","repertorium","partner","links","kontakt","impressum","institution","ausstellung","bestand","glossar","handbuch","home","literatur","login","logout","aedit","bedit","edit","gedit","iedit","ledit");
if (!in_array($action,$actions)) $action='home';  // default Startseite
//functions
//chose main template
//print_r($spr);
//echo($spr['fotoch']);
if(!empty($_SESSION['s_uid'])){
	$xtpl = new XTemplate("templates/main_intern.xtpl");
	$xtpl->assign("LOG","logout");
//	$xtpl->assign("BESTAND", getLangContent("sprache", $language, "bestand"));
//	$xtpl->assign("LITERATUR", getLangContent("sprache", $language, "literatur"));
//	$xtpl->assign("AUSSTELLUNG", getLangContent("sprache", $language, "ausstellung"));
//	xtpl->assign("GLOSSAR", getLangContent("sprache", $language, "glossar"));
//	$xtpl->assign("EDITPAGES", getLangContent("sprache", $language, "editpages"));
	
} else {
	if($action == "fotograph" || $action == "bestand" || $action == "institution"){
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
		$xtpl->assign("IMG0href", "images/".$placeholders[0]);
		$xtpl->assign("IMG1href", "images/".$placeholders[1]);
		$xtpl->assign("IMG2href", "images/".$placeholders[2]);
		$xtpl->assign("IMG3href", "images/".$placeholders[3]);
		$xtpl->assign("IMG4href", "images/".$placeholders[4]);
		$xtpl->assign("IMG5href", "images/".$placeholders[5]);		
	}
	$xtpl->assign("LOG","login");
}

$xtpl->assign("SPR",$spr);  // load all languagecontent!
//$xtpl->assign("FOTOCH", getLangContent("sprache", $language, "fotoch"));
$xtpl->assign("LANG",$language);
$xtpl->assign("URLDE", changeurl("de"));
$xtpl->assign("URLFR", changeurl("fr"));
//$xtpl->assign("URLIT", changeurl("it"));
//$xtpl->assign("URLRO", changeurl("ro"));
//$xtpl->assign("HEADER", getLangContent("sprache", $language, "fotoch"));
//$xtpl->assign("HOME", getLangContent("sprache", $language, "home"));
//$xtpl->assign("LEXIKON", getLangContent("sprache", $language, "lexikon"));
//$xtpl->assign("REPERTORIUM", getLangContent("sprache", $language, "repertorium"));
//$xtpl->assign("GLOSSAR", getLangContent("sprache", $language, "glossar"));
//$xtpl->assign("HANDBUCH", getLangContent("sprache", $language, "handbuch"));
//$xtpl->assign("KONTAKT", getLangContent("sprache", $language, "kontakt"));
//$xtpl->assign("SITEMAP", getLangContent("sprache", $language, "sitemap"));
//$xtpl->assign("PARTNER", getLangContent("sprache", $language, "partner"));
//$xtpl->assign("IMPRESSUM", getLangContent("sprache", $language, "impressum"));

//probably not used anymore
//$xtpl->assign("SESSION_UID", $_SESSION['s_uid']);
//assign action
$xtpl->assign("ACTION",$action);
//choose content sites
$adminActions = array("ausstellung", "bestand", "fotograph", "glossar", "institution", "literatur");
$editActions = array("edit", "aedit", "bedit","gedit", "iedit", "ledit");
if(in_array($action,$adminActions)){
	$tempbool = ($action == "ausstellung");
	$tempbool1 = ($action == "glossar");
	$tempbool2 = ($action == "literatur");
	$tempbool3 = empty($_SESSION['s_uid']);
	if(($tempbool || $tempbool1 || $tempbool2) && $tempbool3){
		$action = "login";
		include("sites/".$action.".php");
	} else {
		include("sites/".$action."/".$action.".php");
	}
} else {
	if(in_array($action,$editActions)) { 
		if(!empty($_SESSION['s_uid'])){
			include("edit/".$action.".php");
		} else { 
			$action = "login";
			include("sites/".$action.".php");
		}
	} else {
		include("sites/".$action.".php");
	}
}
//the def variable contains the specific template-entities defined
//in the file included in $action.php
$xtpl->assign("OUT", $out);
$xtpl->parse("main");
$xtpl->out("main");
?>