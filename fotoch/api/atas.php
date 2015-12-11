<?php
require("./mysql.inc.php");
require("./foto-ch.inc.php");
error_reporting(!(E_ALL));
setlocale (LC_ALL, 'de_CH');
$foto=($_REQUEST['s']);
$l=strlen($foto);

$sql="SELECT * FROM ausstellung WHERE titel LIKE '$foto%' ORDER BY titel LIMIT 5";

//$sql="SELECT * FROM ausstellung WHERE titel LIKE '$foto%' AND gesperrt=0 ORDER BY titel LIMIT 18";
$result=mysql_query($sql);
$c=0;
$res=array();
//print_r($result);
while(($c<12) && ($fetch=mysql_fetch_array($result))){
//while($c<11){
//if (strtolower(substr($fetch['name'],0,$l))==$foto){
	$r=array('id'=>$fetch['id'],'name'=>$fetch['titel'], 'ort'=>$fetch['ort'], 'jahr'=>$fetch['jahr']);
	$c++;
	$res[]=$r;
	}
//}
jsonout($res);


?>
