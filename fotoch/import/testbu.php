<?php
header('Content-type: text/plain; charset=utf-8');

ini_set('display_errors', 1);
error_reporting(E_ALL && ~E_NOTICE);
//require("templates/xtemplate.class.php");
require("../mysql.inc.php");
ob_start();
mysql_select_db("foto-ch_test");
$sql="SELECT * FROM bildarchivbu"; 
$result=mysql_query($sql); echo mysql_error();
while ($fetch=mysql_fetch_assoc($result)){
	$rec=array();
	$res['dc_title']=$fetch['Titel'];
	//$res['dc_creator']=$fetch['fotograph'];
	$res['dcterms_ispart_of']=$fetch['bestand'];
	$res['dc_right']='<a href="http://www.burgerbib.ch/dokumente/benutzung/angebot_und_preise_reproduktionen.pdf">Reproduktionen / Bildrechte</a>';
	$created=parseZeitraum($fetch['Entstehungszeitraum']);
	$res['dc_created']=$created[0];
	$res['dc_created_bis']=$created[1];
	$res['dcterms_medium']=$fetch['Material / Beschreibstoffe'].' '.$fetch['Technik'];
	$res['dcterms_subject']=$fetch['Dargestelltes Objekt'];
	$res['dc_identifier']=$fetch['URL'];
	$res['dc_description']=$fetch['Inhalt (Darin)'].' '.$fetch['Bildinhalt'];
	$res['dcterms_spatial']=$fetch['Ort'];
	$res['image_path']='buimage/'.$fetch['id'].'.jpg';
	$res['edm_dataprovider']='13';
	$res['all']=$fetch['all'];
	$res['supplier_id']=$fetch['id'];
	$res['zeitraum']=$fetch['Entstehungszeitraum'];
	//print_r($res);
	$u='';
	foreach ($res as $k => $v){
		//echo "$k => $v\r\n";
		$u.="`".$k."`='".mysql_escape_string($v)."', ";
	}
	//echo "xxx".$u."vvv";
	$sql="INSERT INTO fotos SET ".substr($u,0,-2);
	mysql_query($sql);
	//echo $sql;
}

function parseDate($s){
 $rchars=array(" ","ca","approx",".","between");
 
 $s=str_replace($rchars,"",$s);
 $e=explode("/",$s);
 //echo("$s		=> ".count($e).": ".$e[0]." | ".$e[1]." | ".$e[2]."\r\n");
 if (count($e)>3){
 	echo "error: parsing $s";
 	return "";
 }
 if (count($e)==1){
 	if ($e[0]) return $e[0]."-01-01";
 	else return "";
 }

 if (count($e)==2){
 	return $e[1]."-".$e[0]."-01";
 } 
 if (count($e)==3){
 	return $e[2]."-".$e[0]."-".$e[1];
 } else return "";
 
 
 
}

function parseZeitraum($s){
	$s=str_replace("and","-",$s);
	$d=explode("-",$s);
	$res=array();
	$res[0]=parseDate($d[0]);
	$res[1]=parseDate($d[1]);
	echo("$s		-> ".$res[0]." | ".$res[1]."\r\n");
	ob_flush();
	flush();
	return($res);
}

?>