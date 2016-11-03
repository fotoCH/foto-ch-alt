<?php
require("../mysql.inc.php");
error_reporting(!(E_ALL));
setlocale (LC_ALL, 'de_CH');
$foto=strtolower($_REQUEST['suchbegriff']);
$l=strlen($foto);
//$sql="SELECT * FROM namen WHERE LOWER(nachname) LIKE '$foto%' COLLATE latin1_bin ORDER BY nachname LIMIT 18";
//$sql="SELECT * FROM namen WHERE LOWER(nachname) LIKE '$foto%' COLLATE latin1_bin ORDER BY nachname LIMIT 18";
$sql="SELECT * FROM namen WHERE LOWER(nachname) LIKE '$foto%' ORDER BY nachname LIMIT 18";
//$sql="SELECT * FROM namen WHERE SUBSTRING(nachname,1,$l) = '$foto%' ORDER BY nachname LIMIT 8";
//echo($sql);
$result=mysqli_query($sqli, $sql);
$c=0;
//print_r($result);
while(($c<12) && ($fetch=mysqli_fetch_array($result))){
//while($c<11){
if (strtolower(substr($fetch['nachname'],0,$l))==$foto){
	$r=$fetch['fotografen_id'].'|'.$fetch['nachname'].', '.$fetch['vorname'];
	if ($fetch['zusatz']) $r.=' '.$fetch['zusatz'];
	$r.="\r\n";
	echo ($r);
	$c++;
	}
}

?>