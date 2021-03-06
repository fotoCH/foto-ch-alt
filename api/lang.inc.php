<?php
// declare variables, to avoid warnings
$language = '';
$clanguage = '';

$lang = getClean('lang');
$clang = getClean('clang');
if(!$clang) {
	$clang = $lang;
}
//set language
if($lang!=""){
    $language = $lang;
} /*else {
    if($_COOKIE['lang'] == ""){
	$language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    } else {
	$language = $_COOKIE['lang'];
    }
}*/
$supported_langs = array("de","fr","it","en","rm");//missing: "en","it",,"rm"
if(!in_array($language ,$supported_langs)) {
	$language = "de";
}
if($clang!=""){   // content_language
    $clanguage = $clang;
}
if(!in_array($clanguage ,$supported_langs)) {
	$clanguage = "de";
}

//für (aktuelle) sprache ein cookie setzen für ca ein halbes jahr:
//setcookie("lang", $language, (time() + (60*60*24*183)));
//put this not before the initial language setting
//define action

// load current language in array $spr
$query = "SELECT name, de, array, ".$language." FROM sprache";
$result = mysqli_query($sqli, $query);
while($fetch = mysqli_fetch_array($result)){
	if ($fetch['array']>0){
		if ($language!='dede'){ // fotographen- und bildgattungen // immer einsetzen für export
			$de=explode(',',$fetch['de']);
			$tr=explode(',',$fetch[$language]);
			$spade[$fetch['name']]=$de;
			$spatr[$fetch['name']]=$tr;
		}
	} else {
	  $spr[$fetch['name']]=$fetch[$language];
	}
}


function getLangContent($dbtable, $language, $value){
global $spr;
global $sqli;
    if ($dbtable=='sprache'){
	return($spr[$value]);
    } else {  // not used
	$query = "SELECT ".$language." FROM $dbtable WHERE name = '$value'";
	$result = mysqli_query($sqli, $query);
	while($fetch = mysqli_fetch_array($result)){
		$fetchresult = $fetch["".$language.""];
	}
	return $fetchresult;
	}
}

function setuebersetzungen($f,$text){
	global $spade;
	global $spatr;
	//print_r($spade[$f]);
	return(str_replace($spade[$f],$spatr[$f],$text));
}

function getLogos(){
	global $language;
	global $sqli;
	$result = mysqli_query($sqli, 'SELECT id, bild_'.$language.' as bild, text_'.$language.' as text, link_'.$language.' as link, width FROM logos ORDER BY id');
	while($fetch = mysqli_fetch_assoc($result)){
		$logos[]=$fetch;
	}
	return $logos;
}

?>
