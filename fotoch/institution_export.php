<?php

function getfo($id){
 $result=mysql_query("SELECT *  FROM (fotografen INNER JOIN namen ON fotografen.id=namen.fotografen_id) INNER JOIN bestand_fotograf ON fotografen.id=bestand_fotograf.fotografen_id WHERE bestand_fotograf.bestand_id=$id ORDER BY namen.id Asc");
 while($fetch=mysql_fetch_array($result)){

 $r.=$fetch['namenszusatz'].' '.$fetch['nachname'].', '.$fetch['vorname'].' ('.$fetch['id'].'); ';
}
return($r);
}


require("mysql.inc.php");
header('Content-type: text/plain');

$id=$_REQUEST['id'];
$sql="SELECT * FROM institution WHERE id=$id";
//echo($sql);
$result=mysql_query($sql);
//print_r($result);
while($fetch=mysql_fetch_array($result, MYSQL_ASSOC)){
foreach ($fetch as $key=>$value){
	$value=str_replace("\n",'',$value);
	$value=str_replace("\r",'',$value);
	$l1.=$key."\t";
	$l2.=$value."\t";
	}
echo $l1."\n";
echo $l2."\n";
}
$sql="SELECT * FROM bestand WHERE inst_id=$id ORDER BY nachlass DESC, name ASC";
//echo($sql);
$result=mysql_query($sql);
//print_r($result);
$ll=1;
while($fetch=mysql_fetch_array($result, MYSQL_ASSOC)){
unset ($fetch['fotografen']);
unset ($fetch['inst_id']);
unset ($fetch['fotografen_ref']);
unset ($fetch['convert_result']);

$l1='';
$l2='';
foreach ($fetch as $key=>$value){

	$l1.=$key."\t";
	$l2.=$value."\t";
	}
	$l1.="fotografen";
	$l2.=getfo($fetch['id']);
	if ($ll==1) echo $l1."\n";
	echo $l2."\n";
	$ll=0;
}
$result=mysql_query("SELECT literatur_institution.institution_id, literatur_institution.id AS if_id, literatur.*
FROM literatur_institution INNER JOIN literatur ON literatur_institution.literatur_id = literatur.id
WHERE literatur_institution.institution_id=$id ORDER BY if_id");
$ll=1;
	while($fetch=mysql_fetch_array($result, MYSQL_ASSOC)){

$l1='';
$l2='';
unset ($fetch['institution_id']);
unset ($fetch['if_id']);

foreach ($fetch as $key=>$value){

	$l1.=$key."\t";
	$l2.=$value."\t";
	}
	if ($ll==1) echo $l1."\n";
	echo $l2."\n";
	$ll=0;
	}
	
$result=mysql_query("SELECT ausstellung_institution.institution_id, ausstellung_institution.id AS af_id, ausstellung.*
FROM ausstellung_institution INNER JOIN ausstellung ON ausstellung_institution.ausstellung_id = ausstellung.id
WHERE ausstellung_institution.institution_id=$id ORDER BY ausstellung.typ, af_id");
$ll=1;
	while($fetch=mysql_fetch_array($result, MYSQL_ASSOC)){
$l1='';
$l2='';


unset ($fetch['instidution_id']);
foreach ($fetch as $key=>$value){

	$l1.=$key."\t";
	$l2.=$value."\t";
	if ($ll==1) echo $l1."\n\r";
	echo $l2."\n\r";
	$ll=0;
	}

}


?>

