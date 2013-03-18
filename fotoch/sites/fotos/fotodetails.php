<?php

ini_set('display_errors', 1);
error_reporting(E_ALL && ~E_NOTICE);

$xtpl_fotodetails=new XTemplate ("././templates/item_details.xtpl");
$xtpl_fotodetails->assign("ACTION",$_GET['a']);
$xtpl_fotodetails->assign("ID",$_GET['id']);
$id = $_GET['id'];
$lang = $_GET['lang'];
$xtpl_fotodetails->assign("LANG", $_GET['lang']);
$xtpl_fotodetails->assign("TITLE", $spr['fotos']);
$xtpl_fotodetails->assign("SPR",$spr);

$query = "SELECT 'f.dc_created AS created, f.dc_title AS title, f.dc_description AS description'";


/*
SELECT b.name AS stock, i.name AS institution, n.nachname AS name, n.vorname AS firstname
FROM fotos AS f
JOIN namen AS n
ON f.dc_creator=n.fotografen_id
JOIN institution AS i
ON f.edm_dataprovider=i.id
JOIN bestand AS b
ON b.inst_id=i.id
WHERE n.vorname LIKE 'Paul'
*/


$objResult=mysql_query("SELECT * FROM fotos WHERE id=$id");

while($arrResult=mysql_fetch_assoc($objResult)){
    foreach ($arrResult as $key=>$value){
        if($value!=''){
            $xtpl_fotodetails->assign("key",$key);
            $xtpl_fotodetails->assign("value",$value);
            $xtpl_fotodetails->parse("autodetail.z.autorow");
            $xtpl_fotodetails->parse("autodetail.z");
            abstand($xtpl_fotodetails);
        }
    }
}

$xtpl_fotodetails->parse('autodetail');
$results.=$xtpl_fotodetails->text("autodetail");
?>
