<?php
require("./mysql.inc.php");
require("./foto-ch.inc.php");
error_reporting(!(E_ALL));
setlocale (LC_ALL, 'de_CH');
$foto=strtolower($_REQUEST['s']);
$l=strlen($foto);

$sql="SELECT * FROM namen WHERE CANCAT(LOWER(nachname),LOWER(vorname)) LIKE '$foto%' ORDER BY nachname, vorname LIMIT 18";

$sql="SELECT  fotografen.id, fotografen.geburtsdatum, fotografen.gen_geburtsdatum, fotografen.todesdatum, fotografen.gen_todesdatum, namen.nachname, namen.vorname, namen.namenszusatz, namen.titel  FROM fotografen INNER JOIN namen ON fotografen.id=namen.fotografen_id WHERE (fotografen.unpubliziert=0) AND CONCAT(LOWER(nachname),' ',LOWER(vorname)) LIKE '$foto%' ORDER BY nachname, vorname LIMIT 18";

$result=mysql_query($sql);
$c=0;
$res=array();
//print_r($result);
while(($c<12) && ($fetch=mysql_fetch_array($result))){
//while($c<11){
//if (strtolower(substr($fetch['nachname'],0,$l))==$foto){
	if ($fetch['zusatz']) $fetch['vorname'].=' '.$fetch['zusatz'];
	$fldatum = formlebensdaten ( $fetch ['geburtsdatum'], $fetch ['gen_geburtsdatum'], $fetch ['todesdatum'], $fetch ['gen_todesdatum'] );
	$r=array('id'=>$fetch['id'],'name'=>$fetch['nachname'],'vorname'=>$fetch['vorname'],'fldatum'=>$fldatum);
	$c++;
	$res[]=$r;
//	}
}
jsonout($res);

?>