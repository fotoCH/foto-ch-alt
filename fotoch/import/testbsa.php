<?php
header('Content-type: text/plain; charset=utf-8');

ini_set('display_errors', 1);
error_reporting(E_ALL && ~E_NOTICE);
//require("templates/xtemplate.class.php");
require("../mysql.inc.php");
mysql_select_db("foto-ch_test");
$sql="SELECT * FROM bildarchiv"; 
$result=mysql_query($sql); echo mysql_error();
while ($fetch=mysql_fetch_assoc($result)){
	$rec=array();
	$res['dc_title']=$fetch['Titel'];
	$res['dc_creator']=$fetch['fotograph'];
	$res['dcterms_ispart_of']=$fetch['bestand'];
	$created=parseZeitraum($fetch['Zeitraum']);
	$res['dc_created']=$created[0];
	$res['dc_created_bis']=$created[1];
	$res['dcterms_medium']=$fetch['Art des Negativs'];
	$res['dcterms_subject']=$fetch['Thema (Fotos/Bilder)'];
	$res['dcterms_identifier']='http://www.query.sta.be.ch/detail.aspx?ID='.$fetch['id'];
	$res['dc_description']=$fetch['Inhalt'];
	$res['dcterms_spatial']=$fetch['Ort (Fotos/Bilder)'];
	$res['bildlink']='bsaimages/'.$fetch['id'].'.jpg';
	$res['edm_dataprovider']='2';
	$res['all']=$fetch['all'];
	//print_r($res);
	$u='';
	foreach ($res as $k => $v){
		//echo "$k => $v\r\n";
		$u.="`".$k."`='".mysql_escape_string($v)."', ";
	}
	//echo "xxx".$u."vvv";
	$sql="INSERT INTO fotos_bsa SET ".substr($u,0,-2);
	mysql_query($sql);
	echo $sql;
}

function parseDate($s){
 $e=explode("/",$s);
 if (count($e)>3){
 	echo "error: parsing $s";
 	return "";
 }
 if (count($e)==1){
 	if ($e[0]) return $e[0]."-01-01";
 	else return "";
 }

 if (count($e)==2){
 	return $e[0]."-".$e[1]."-01";
 } 
 if (count($e)==1){
 	return $e[0]."-".$e[1]."-"-$e[2];
 } else return "";
 
 
 
}

function parseZeitraum($s){
	$d=explode("-",$s);
	$res=array();
	$res[0]=parseDate($d[0]);
	$res[1]=parseDate($d[1]);
	return($res);
}

?>