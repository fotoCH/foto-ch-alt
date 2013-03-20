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

$select = 'i.id AS institution_id, n.fotografen_id AS photograph_id, CONCAT(f.dc_title, " ", f.dc_description) AS titel, CONCAT(n.vorname, " ", n.nachname) AS fotograph, f.dc_created AS zeitraum, f.dc_coverage AS coverage, i.name AS institution, b.name AS bestand, f.dc_right AS copy, f.dcterms_medium AS medium, f.dc_identifier AS url';

$join .= "LEFT JOIN namen AS n ON f.dc_creator=n.fotografen_id ";
$join .= "LEFT JOIN institution AS i ON f.edm_dataprovider=i.id ";
$join .= "LEFT JOIN bestand AS b ON f.dcterms_ispart_of=b.inst_id ";

$query = "SELECT $select FROM fotos AS f $join WHERE f.id=$id";


$objResult=mysql_query($query);

// prepare the metadata
while($arrResult=mysql_fetch_assoc($objResult)){
    foreach ($arrResult as $key=>$value){
        if($value!=''){
            $isOutput = true;
            switch ($key) {
                case 'institution_id':
                case 'photograph_id':
                    $isOutput = false;
                    break;
                case 'fotograph':
                    $xtpl_fotodetails->assign("value",'<a href="?a=fotograph&id='.$arrResult['photograph_id'].'&lang='.($lang? $lang : 'de').'">'.$value.'</a>');
                    break;
                case 'institution':
                    $xtpl_fotodetails->assign("value",'<a href="?a=institution&id='.$arrResult['institution_id'].'&lang='.($lang? $lang : 'de').'">'.$value.'</a>');
                    break;
                case 'bestand':
                    $xtpl_fotodetails->assign("value",'<a href="?a=bestand&id='.$arrResult['institution_id'].'&lang='.($lang? $lang : 'de').'">'.$value.'</a>');
                    break;
                case 'url':
                    $xtpl_fotodetails->assign("value",'<a href="'.$value.'" target="_blank">'.$value.'</a>');
                    break;
                case 'zeitraum':
                    $xtpl_fotodetails->assign("value",date('Y', mktime(0,0,0,1,1,$value)));
                    break;
                default:
                    $xtpl_fotodetails->assign("value",$value);
            }
            if($isOutput) {
                $xtpl_fotodetails->assign("key",$spr[$key]);
                $xtpl_fotodetails->parse("autodetail.z.autorow");
                $xtpl_fotodetails->parse("autodetail.z");
                abstand($xtpl_fotodetails);
            }
        }
    }
}

// render the photo
$xtpl_fotodetails->assign("photo_src", PHOTO_PATH.$id.'.jpg');
$xtpl_fotodetails->assign("photo_alt", 'test');
$xtpl_fotodetails->parse('autodetail.photo');

$xtpl_fotodetails->parse('autodetail');
$results.=$xtpl_fotodetails->text("autodetail");
?>
