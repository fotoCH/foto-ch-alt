<?php
require("./mysql.inc.php");
require("./foto-ch.inc.php");
error_reporting(!(E_ALL));
setlocale (LC_ALL, 'de_CH');
$foto=($_REQUEST['s']);
$l=strlen($foto);

$sql="SELECT * FROM institution WHERE name LIKE '$foto%' ORDER BY name LIMIT 18";

$sql="SELECT * FROM institution WHERE name LIKE '$foto%' AND gesperrt=0 ORDER BY name LIMIT 18";

$result=mysql_query($sql);
$c=0;
$res=array();
//print_r($result);
while(($c<12) && ($fetch=mysql_fetch_array($result))){
//while($c<11){
//if (strtolower(substr($fetch['name'],0,$l))==$foto){
	$r=array('id'=>$fetch['id'],'name'=>$fetch['name'], 'ort'=>$fetch['ort']);
	$c++;
	$res[]=$r;
	}
//}
jsonout($res);


?>
