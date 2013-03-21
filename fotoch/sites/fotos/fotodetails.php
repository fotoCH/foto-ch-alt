<?php

ini_set('display_errors', 1);
error_reporting(E_ALL && ~E_NOTICE);

$xtpl_fotodetails=new XTemplate ("././templates/item_details.xtpl");
$xtpl_fotodetails->assign("ACTION",$_GET['a']);
$xtpl_fotodetails->assign("ID",$_GET['id']);
$id = $_GET['id'];
$lang = $_GET['lang'];
$xtpl_fotodetails->assign("LANG", $_GET['lang']);
$xtpl_fotodetails->assign("TITLE", $spr['photos']);
$xtpl_fotodetails->assign("SPR",$spr);

$select = 'i.id AS institution_id, b.id AS stock_id, n.fotografen_id AS photograph_id, f.dc_title AS titel, f.dc_description AS description, CONCAT(n.vorname, " ", n.nachname) AS fotograph, f.dc_created AS zeitraum, f.dc_coverage AS coverage, i.name AS institution, b.name AS bestand, f.dc_right AS copy, f.dcterms_medium AS medium, f.dc_identifier AS url';

$join .= "LEFT JOIN namen AS n ON f.dc_creator=n.fotografen_id ";
$join .= "LEFT JOIN institution AS i ON f.edm_dataprovider=i.id ";
$join .= "LEFT JOIN bestand AS b ON f.dcterms_ispart_of=b.id ";

$query = "SELECT $select FROM fotos AS f $join WHERE f.id=$id";
$objResult=mysql_query($query);

// prepare the metadata
while($arrResult=mysql_fetch_assoc($objResult)){
    $photographID = $arrResult['photograph_id'];
    $institutionID = $arrResult['institution_id'];
    $stockID = $arrResult['stock_id'];
    foreach ($arrResult as $key=>$value){
        $isOutput = true;
        switch ($key) {
            case 'titel':
                $value = $value.(($value!='' && $arrResult['description']!='') ? ' / ' : '').$arrResult['description'];
                break;
            case 'description':
            case 'institution_id':
            case 'stock_id':
            case 'photograph_id':
                $isOutput = false;
                break;
            case 'fotograph':
                $value = '<a href="?a=fotograph&id='.$photographID.'&lang='.($lang? $lang : 'de').'">'.$value.'</a>';
                break;
            case 'institution':
                $value = '<a href="?a=institution&id='.$institutionID.'&lang='.($lang? $lang : 'de').'">'.$value.'</a>';
                break;
            case 'bestand':
                $value = '<a href="?a=bestand&id='.$stockID.'&lang='.($lang? $lang : 'de').'">'.$value.'</a>';
                break;
            case 'url':
                $value = '<a href="'.$value.'" target="_blank">'.$value.'</a>';
                break;
            case 'zeitraum':
                $value = date('Y', mktime(0,0,0,1,1,$value));
                break;
        }
        if($isOutput && !empty($value)) {
            $xtpl_fotodetails->assign("key",$spr[$key]);
            $xtpl_fotodetails->assign("value", $value);
            $xtpl_fotodetails->parse("autodetail.z.autorow");
            $xtpl_fotodetails->parse("autodetail.z");
            abstand($xtpl_fotodetails);
        }
    }
}

// render the photo
$xtpl_fotodetails->assign("photo_src", PHOTO_PATH.$id.'.jpg');
$xtpl_fotodetails->assign("photo_alt", 'test');
$xtpl_fotodetails->parse('autodetail.photo');

$xtpl_fotodetails->parse('autodetail');
$results.=$xtpl_fotodetails->text("autodetail");

// generate the links to the previous and next photo according to the referral
switch($_SESSION['referral']) {
    case 'institution':
        $query = "SELECT id FROM fotos WHERE edm_dataprovider=$institutionID ";
        break;
    case 'bestand':
        $query = "SELECT id FROM fotos WHERE dcterms_ispart_of=$stockID ";
        break;
    default:
        $query = "SELECT id FROM fotos WHERE dc_creator=$photographID ";
}
$objResult = mysql_query($query."AND id>$id LIMIT 0,1");
if (mysql_num_rows($objResult)>0){
    $xtpl_fotos->assign("next_link", '<a href="?a=fotos&id='.mysql_fetch_assoc($objResult)['id'].'">'.$spr['next_photo'].'</a>');
}

// generate the link to the previous photo
$objResult = mysql_query($query."AND id<$id LIMIT 0,1");
if (mysql_num_rows($objResult)>0){
    $xtpl_fotos->assign("previous_link", '<a href="?a=fotos&id='.mysql_fetch_assoc($objResult)['id'].'">'.$spr['previous_photo'].'</a>');
}
$xtpl_fotos->parse('contents.content_detail.nav_panel');
?>
