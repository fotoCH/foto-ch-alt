<?php
require("../mysql.inc.php");
error_reporting(!(E_ALL));
setlocale (LC_ALL, 'de_CH');
$foto=$_REQUEST['suchbegriff'];
$l=strlen($foto);
$sql="SELECT * FROM institution WHERE name LIKE '$foto%' ORDER BY name LIMIT 18";
//$sql="SELECT * FROM namen WHERE SUBSTRING(nachname,1,$l) = '$foto%' ORDER BY nachname LIMIT 8";
//echo($sql);
$result=mysqli_query($sqli, $sql);
$c=0;
//print_r($result);
while(($c<11) && ($fetch=mysqli_fetch_array($result))){
//if (strtolower(substr($fetch['name'],0,$l))==$foto){
	$r=$fetch['id'].'|'.$fetch['name']."\r\n";
	echo ($r);
	$c++;
//	}
}


?>