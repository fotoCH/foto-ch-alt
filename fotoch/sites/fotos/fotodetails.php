<?php
$xtpl_fotodetails=new XTemplate ("././templates/item_details.xtpl");
$xtpl_fotodetails->assign("ACTION",$_GET['a']);
$xtpl_fotodetails->assign("ID",$_GET['id']);
$id = $_GET['id'];
$lang = $_GET['lang'];
$xtpl_fotodetails->assign("LANG", $_GET['lang']);
$xtpl_fotodetails->assign("TITLE", $spr['photos']);
$xtpl_fotodetails->assign("SPR",$spr);

$select = 'i.id AS institution_id, b.id AS stock_id, n.fotografen_id AS photograph_id, f.dc_title AS titel, f.dc_description AS description, CONCAT(n.vorname, " ", n.nachname) AS fotograph, f.dc_created AS zeitraum, f.dc_coverage AS coverage, b.name AS bestand, i.name AS institution, f.dc_right AS copy, f.dcterms_medium AS medium, f.dc_identifier AS img_url, f.image_path';

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
    $photoPath = $arrResult['image_path'];
    foreach ($arrResult as $key=>$value){
        if ($value==''){
            $value=$spr['not_available'];
        }
        $isOutput = true;
        switch ($key) {
            case 'titel':
                $value = $value.(($value!='' && $arrResult['description']!='') ? ' / ' : '').$arrResult['description'];
                $img_alt = $value;
                break;
            case 'description':
            case 'institution_id':
            case 'stock_id':
            case 'photograph_id':
                $isOutput = false;
                break;
            case 'fotograph':
                if ($value == $spr['not_available']){
                    break;
                }
                $value = '<a href="?a=fotograph&id='.$photographID.'&lang='.($lang? $lang : 'de').'">'.$value.'</a>';
                break;
            case 'institution':
                if ($value == $spr['not_available']){
                    break;
                }
                $value = '<a href="?a=institution&id='.$institutionID.'&lang='.($lang? $lang : 'de').'">'.$value.'</a>';
                break;
            case 'bestand':
                if ($value == $spr['not_available']){
                    break;
                }
                $value = '<a href="?a=bestand&id='.$stockID.'&lang='.($lang? $lang : 'de').'">'.$value.'</a>';
                break;
            case 'img_url':
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
$xtpl_fotodetails->assign("photo_src", $photoPath);
$xtpl_fotodetails->assign("photo_alt", $img_alt);
$xtpl_fotodetails->parse('autodetail.photo');

$xtpl_fotodetails->parse('autodetail');
$results.=$xtpl_fotodetails->text("autodetail");

// generate the links to the previous and next photo according to the url
foreach($_GET as $key=>$value) {
    if ($value==''){
        break;
    }
    switch ($key) {
        case 'fotograph':
            if (!is_numeric($value)){
                // get the id of the photographer from the passed name
                $arrName = explode(' ', $value);
                if (count($arrName) > 1){
                    $query = "SELECT id FROM namen WHERE ((nachname LIKE '%$arrName[0]%' AND vorname LIKE '%$arrName[1]%') OR (vorname LIKE '%$arrName[0]%' AND nachname LIKE '%$arrName[1]%')) LIMIT 0,1";
                } else {
                    $query = "SELECT id FROM namen WHERE (nachname LIKE '%$arrName[0]%' OR vorname LIKE '%$arrName[0]%') LIMIT 0,1";
                }

                $objResult = mysql_query($query);
                $value = mysql_fetch_assoc($objResult);
                $value = $value['id'];
            }
            $where = ($where!='' ? ' AND ' : '')."dc_creator=$value";
            break;
        case 'period_start':
            $period = "$value-01-01";
            $where .= ($where!='' ? ' AND ' : '')."dc_created >= '$period'";
            break;
        case 'period_end':
            $period = "$value-01-01";
            $where .= ($where!='' ? ' AND ' : '')."dc_created >= '$period'";
            break;
        case 'title':
            $where .= ($where!='' ? ' AND ' : '')."(dc_title LIKE '%$value%' OR dc_description LIKE '%$value%' OR dc_coverage LIKE '%$value%')";
            break;
        case 'institution':
            $where .= ($where!='' ? ' AND ' : '')."edm_dataprovider='$value'";
            break;
        case 'bestand':
            $where = ($where!='' ? ' AND ' : '')."dcterms_ispart_of=$value";
            break;
    }
}

$query = "SELECT id FROM fotos".($where!='' ? ' WHERE '.$where : '');
$nextQuery = $query.($where!='' ? ' AND ' : ' WHERE ')."id>$id LIMIT 0,1";
$objNextResult = mysql_query($nextQuery);
if (mysql_num_rows($objNextResult)>0){
    $result = mysql_fetch_assoc($objNextResult);
    $url = str_replace('&id='.$id, '&id='.$result['id'], $_SERVER['REQUEST_URI']);
    $next = '<a href="'.$url.'">'.$spr['next_photo'].'</a>';
    $xtpl_fotos->assign("next_link", $next);
}
$previousQuery = $query.($where!='' ? ' AND ' : ' WHERE ')."id<$id ORDER BY id DESC LIMIT 0,1";
$objPreviousResult = mysql_query($previousQuery);
if (mysql_num_rows($objPreviousResult)>0){
    $result = mysql_fetch_assoc($objPreviousResult);
    $url = str_replace('&id='.$id, '&id='.$result['id'], $_SERVER['REQUEST_URI']);
    $xtpl_fotos->assign("previous_link", '<a href="'.$url.'">'.$spr['previous_photo'].'</a>'.($next!='' ? ' | ' : ''));
}
$xtpl_fotos->parse('contents.content_detail.nav_panel');
?>
