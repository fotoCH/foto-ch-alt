<?php

ini_set('display_errors', 1);
error_reporting( E_ALL ^ E_NOTICE ^ E_DEPRECATED );
require("mysql.inc.php");
include("fotofunc.inc.php");
include("foto-ch.inc.php");

function doCode($q){
//$q='Selzach';
$searchURL='https://api3.geo.admin.ch/rest/services/api/SearchServer?type=locations&origins=gg25,sn25&searchText=';
$response = file_get_contents($searchURL.urlencode($q));
$response = json_decode($response);
$res1=$response->results[0];
//print_r($res1);
if ($res1->weight>=10){
	//print_r($res1->attrs);
	return($res1->attrs);
} else {
	return NULL;
}
}

$sql='SELECT * FROM arbeitsorte WHERE name >=\'B\'';
//$sql='SELECT * FROM arbeitsorte WHERE name LIKE \'%geri%\'';
$res=getfromselect($sql);
foreach ($res as $r){
	echo ($r['name']."\r\n");
	$a=doCode($r['name']);
	if ($a<>NULL){
		//print_r($a);
		$sql=("UPDATE arbeitsorte set lat='$a->lat', lon='$a->lon', swissname='".mysqli_real_escape_string($sqli, $a->label)."' WHERE id=$r[id]");
		echo $sql; echo "<br />\r\n";
		mysqli_query($sqli, $sql);
		flush();
	}
}


?>