<?php
header('Content-type: text/plain; charset=utf-8');

ini_set('display_errors', 1);
error_reporting(E_ALL && ~E_NOTICE);
//require("templates/xtemplate.class.php");
require("../mysql.inc.php");
ob_start();
mysqli_select_db($sqli, "foto-ch_test");
$sql="SELECT * FROM bildarchiv"; 
$result=mysqli_query($sqli, $sql); echo mysqli_error($sqli);
while ($fetch=mysqli_fetch_assoc($result)){
	$rec=array();
	$res['dc_title']=$fetch['Titel'];
	$res['dc_creator']=$fetch['fotograph'];
	$res['dcterms_ispart_of']=$fetch['bestand'];
	$res['dc_right']=$fetch['Copyright'];
	$created=parseZeitraum($fetch['Zeitraum']);
	$res['dc_created']=$created[0];
	$res['dc_created_bis']=$created[1];
	$res['dcterms_medium']=$fetch['Art des Negativs'];
	$res['dcterms_subject']=$fetch['Thema (Fotos/Bilder)'];
	$res['dc_identifier']='http://www.query.sta.be.ch/detail.aspx?ID='.$fetch['id'];
	$res['dc_description']=$fetch['Inhalt'];
	$res['dcterms_spatial']=$fetch['Ort (Fotos/Bilder)'];
	$res['image_path']='bsaimage/'.$fetch['id'].'.jpg';
	$res['edm_dataprovider']='2';
	$res['all']=$fetch['all'];
	$res['supplier_id']=$fetch['id'];
	$res['zeitraum']=$fetch['Zeitraum'];
	//print_r($res);
	$u='';
	foreach ($res as $k => $v){
		//echo "$k => $v\r\n";
		$u.="`".$k."`='".mysqli_real_escape_string ($sqli, $v)."', ";
	}
	//echo "xxx".$u."vvv";
	$sql="INSERT INTO fotos_bsa SET ".substr($u,0,-2);
	mysqli_query($sqli, $sql);
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