<?php
require("./mysql.inc.php");
require("./foto-ch.inc.php");
error_reporting(!(E_ALL));
setlocale (LC_ALL, 'de_CH');
$foto=strtolower($_REQUEST['s']);
$l=strlen($foto);

$sql="SELECT * FROM namen WHERE LOWER(nachname) LIKE '$foto%' ORDER BY nachname LIMIT 18";

$result=mysql_query($sql);
$c=0;
$res=array();
//print_r($result);
while(($c<12) && ($fetch=mysql_fetch_array($result))){
//while($c<11){
if (strtolower(substr($fetch['nachname'],0,$l))==$foto){
	if ($fetch['zusatz']) $fetch['vorname'].=' '.$fetch['zusatz'];
	$r=array('id'=>$fetch['fotografen_id'],'name'=>$fetch['nachname'],'vorname'=>$fetch['vorname']);
	$c++;
	$res[]=$r;
	}
}
jsonout($res);

?>