<?php


// load current language in array $spr
$query = "SELECT name, de, array, ".$language." FROM sprache";
$result = mysql_query($query);
while($fetch = mysql_fetch_array($result)){
	if ($fetch['array']>0){
		if ($language!='de'){ // fotographen- und bildgattungen
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
    if ($dbtable=='sprache'){
	return($spr[$value]);
    } else {  // not used
	$query = "SELECT ".$language." FROM $dbtable WHERE name = '$value'";
	$result = mysql_query($query);
	while($fetch = mysql_fetch_array($result)){
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
	$result = mysql_query('SELECT id, bild_'.$language.' as bild, text_'.$language.' as text, link_'.$language.' as link, width FROM logos ORDER BY id');
	while($fetch = mysql_fetch_array($result,MYSQL_ASSOC)){
		$logos[]=$fetch;
	}
	return $logos;
}

?>