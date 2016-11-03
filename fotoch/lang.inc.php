<?php

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