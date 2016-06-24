<?php

ini_set('display_errors', 1);
error_reporting( E_WARNING | E_ERROR | E_PARSE );
require("mysql.inc.php");

//session_start();

//set language
/*if($_COOKIE['lang'] == ""){
	$language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
} else {
	$language = $_COOKIE['lang'];
}*/
/*if($_GET['lang']!=""){
	$language = $_GET['lang'];
}
$supported_langs = array("de","fr","it","en","rm");//missing: "en","it",,"rm"
if(!in_array($language ,$supported_langs)) $language = "de";
if($_GET['clang']!=""){   // content_language
	$clanguage = $_GET['clang'];
}
if(!in_array($clanguage ,$supported_langs)) $clanguage = "de";
*/
//für (aktuelle) sprache ein cookie setzen für ca ein halbes jahr:
//setcookie("lang", $language, (time() + (60*60*24*183)));
//put this not before the initial language setting
//define action
include("fotofunc.inc.php");
include("foto-ch.inc.php");

require("lang.inc.php");
require("auth.inc.php");

$action=$_GET['a'];
$actions=array(
    "fotograf",
    "institution",
    "inventory",
    "sprache",
    "login",
    "pages",
    "partner",
    "photographer",
    "exhibition",
    "literature",
    "user",
    "photo",
    "search",
    "orte",
    "perioden", 
    "streamsearch",
    "statistics",
    "filters",
    "request",
    "year");
if ($action=='fotograph') $action='photographer';
if (!in_array($action,$actions)) $action='photographer';  // default Startseite

$glob['LANG']=$language;
//choose content sites
include("items/".$action.".php");

?>
