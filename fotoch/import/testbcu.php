<?php
header('Content-type: text/plain; charset=utf-8');

ini_set('display_errors', 1);
error_reporting(E_ALL && ~E_NOTICE);
//require("templates/xtemplate.class.php");
require("../mysql.inc.php");
ob_start();
mysql_select_db("foto-ch_test");
$sql="SELECT * FROM bildarchivbcu"; 
$result=mysql_query($sql); echo mysql_error();
while ($fetch=mysql_fetch_assoc($result)){
	$rec=array();
	if ($fetch['245_00_a']) $f245='00';
	if ($fetch['245_02_a']) $f245='02';
	if ($fetch['245_04_a']) $f245='04';
	if ($fetch['245_05_a']) $f245='05';
	//if ($fetch['245_03_a']) $f245='03';
	$res['dc_title']=$fetch['245_'.$f245.'_a'].": ".$fetch['245_'.$f245.'_b'];
	//$res['dc_creator']=$fetch['100_a'];
	//$res['dcterms_ispart_of']=$fetch['bestand'];
	$res['dc_right']='BCU';
	$created=parseZeitraum($fetch['260_c']);
	$res['dc_created']=$created[0];
	$res['dc_created_bis']=$created[1];
	$res['dcterms_medium']=$fetch['300_a'].' '.$fetch['300_b'].' '.$fetch['300_c'];
	//$res['dcterms_subject']=$fetch['Thema (Fotos/Bilder)'];
	//$res['dc_identifier']=$fetch['856_4_u'];
	$res['dc_description']=$fetch['500_a'];
	$res['dcterms_spatial']=$fetch['981_a'];
	//$res['image_path']='bcu/'.substr($fetch['856_4_u'],42).'.jpg';
	$path=explode("\r\n",$fetch['856_4_u']);
	$res['edm_dataprovider']='118';
	$res['all']=$fetch['all'];
	$res['supplier_id']=$fetch['id'];
	$res['zeitraum']=$fetch['260_c'];
	//print_r($res);
	$u='';
	foreach ($res as $k => $v){
		//echo "$k => $v\r\n";
		$u.="`".$k."`='".mysql_escape_string($v)."', ";
	}
	foreach ($path as $p){
		$ip='bcu/'.substr($p,42);
		$nu="`image_path`='".mysql_escape_string($ip)."', ";
		$nu2="`dc_identifier`='".mysql_escape_string($p)."'";
		$sql="INSERT INTO fotos SET ".$u.$nu.$nu2;
		mysql_query($sql);
		echo $sql;
		echo ("\r\n");
	}
	//echo "xxx".$u."vvv";
	//$sql="INSERT INTO fotos_bcu SET ".substr($u,0,-2);
	//mysql_query($sql);
	//echo $sql;
}

function parseDate($s){
 $rchars=array(" ","ca","approx",".","between","Entre","entre","vers",'[',']');
 
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
	$s=str_replace("et","-",$s);
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