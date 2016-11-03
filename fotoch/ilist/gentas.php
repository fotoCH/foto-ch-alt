<?php
require("../mysql.inc.php");
setlocale (LC_ALL, 'de_CH');
error_reporting(!(E_ALL));

$suchfeld=$_REQUEST['suchfeld'];
$suchbegriff=$_REQUEST['suchbegriff'];
$table=$_REQUEST['table'];
$retfeld=$_REQUEST['retfeld'];
if(get_magic_quotes_gpc()){
	$retfeld=stripslashes($retfeld);
}
$ergfeld=$_REQUEST['ergfeld'];
$erg=$_REQUEST['erg'];
$erg2=$_REQUEST['erg2'];
$sql="SELECT id, $suchfeld, $retfeld".($ergfeld?', '.$ergfeld:'')." FROM $table WHERE LOWER($suchfeld) LIKE '$suchbegriff%' ORDER BY $suchfeld LIMIT 18";
//echo $sql;
//$sql="SELECT * FROM namen WHERE SUBSTRING(nachname,1,$l) = '$foto%' ORDER BY nachname LIMIT 8";
//echo($sql);
$result=mysqli_query($sqli, $sql);
$c=0;
//print_r($result);
while(($c<12) && ($fetch=mysqli_fetch_array($result))){
//while($c<11){
//if (strtolower(substr($fetch['nachname'],0,$l))==$foto){
if (true){
	$r=$fetch['id'].'|'.$fetch[$suchfeld].($erg?$erg.$fetch[$retfeld]:'').($erg2?$erg2.$fetch[$ergfeld]:'').'|'.$fetch[$retfeld];
	$r.="\r\n";
	echo ($r);
	$c++;
	}
}

// beiospiele
//litsuche: gentas.php?table=literatur&retfeld=CONCAT(verfasser_name,', ',verfasser_vorname)&suchfeld=titel&suchbegriff=be
//institutionssuche: gentas.php?table=institution&retfeld=ort&suchfeld=name&suchbegriff=be
//ausstellung: gentas.php?table=ausstellung&retfeld=concat(ort,',%20',institution)&suchfeld=titel&suchbegriff=be

?>
