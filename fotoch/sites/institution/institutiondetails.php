<?php
include(config.inc.php);
//include("fotofunc.inc.php");
	
$def=new XTemplate ("././templates/item_details.xtpl");
$def->assign("ACTION",$_GET['a']);
$def->assign("ID",$_GET['id']);
$def->assign("LANG",$_GET['lang']);
$lang= $_GET['lang'];
//$def->assign("DN",$dn);
$id=$_GET['id'];
$anf=$_GET['anf'];
if (!$anf){
    if (auth_level(USER_WORKER) && !$id){
        $ayax=1;
    } else {
        $anf='A';
    }
}
$def->assign("TITLE", $spr['institution']);
$def->assign("SPR", $spr);
$bearbeiten = "&nbsp;&nbsp;[&nbsp;".$spr['bearbeiten']."&nbsp;]";
$det="autodetail";
if ($_GET['style']=='print') $det="detailprint";

if (auth_level(USER_GUEST_READER_PARTNER))
$result=mysql_query("SELECT * FROM institution WHERE institution.id=$id");
else  $result=mysql_query("SELECT * FROM institution WHERE (institution.id=$id) AND (gesperrt=0)");

while($fetch=mysql_fetch_array($result, MYSQL_ASSOC)){
    //print_r($fetch);
    $def->assign("ACTION",$_GET['a']);
    $def->assign("ID",$_GET['id']);
    if(auth_level(USER_GUEST_READER)){
        $def->assign("idd",$id);
        $def->parse($det.".idd");
    }
    $fetch['name']=clean_entry(clangcont($fetch,'name'));
    $fetch['abkuerzung']=clean_entry(clangcont($fetch,'abkuerzung'));
    if ($fetch['abkuerzung']) $fetch['name'].=' ('.$fetch['abkuerzung'].')';
    unset($fetch['abkuerzung']);
    $fetch['ort']=$fetch['plz'].' '.$fetch['ort'];
    unset($fetch['plz']);
    $fetch['homepage']=preg_replace("/http:\/\/(.*)/","<a href=\"http://\$1\" target=\"_new\">\$1</a>",$fetch['homepage']);
    $fetch['email']=preg_replace("/(.*@.*)/","<a href=\"mailto:\$1\">\$1</a>",$fetch['email']);
    if ($_GET['lang']!='de'){
        $fetch['bildgattungen_set']=setuebersetzungen('bildgattungen_uebersetzungen',$fetch['bildgattungen_set']);
    }
    $fetch['bildgattungen_set']=str_replace(',',', ',$fetch['bildgattungen_set']);

    if ($fetch['sammlungszeit_von'].$fetch['sammlungszeit_bis']!=''){
        $fetch['sammlungszeit']=$fetch['sammlungszeit_von'].' - '.$fetch['sammlungszeit_bis'];
    } else { $fetch['sammlungszeit']=''; }
    if (auth_level(USER_WORKER)) {
        //$fetch['name'].=" <a href=\"./?a=iedit&amp;id=$id&amp;lang=$lang\">$bearbeiten</a>";
        $def->assign("bearbeiten"," <a href=\"./?a=iedit&amp;id=$id&amp;lang=$lang\">$bearbeiten</a>");
        normfeldg($def,$spr['name'],$fetch['name'],$fetch['gesperrt']);
        $def->assign("bearbeiten","");
    } else {
        if (auth_level(USER_GUEST_READER_PARTNER)){
            normfeldg($def,$spr['name'],$fetch['name'],$fetch['gesperrt']);
        } else {
            normfeld($def,$spr['name'],$fetch['name']);
        }
    }
    normfeld($def,$spr['art'],$fetch['art']);
    normfeld($def,$spr['isil'],$fetch['isil']);
    normfeld($def,$spr['adresse'],$fetch['adresse']);
    normfeld($def,$spr['ort'],$fetch['ort']);
    //abstand($def);
    if (auth_level(USER_WORKER)){
        normfeld($def,'FAX',$fetch['fax']);
        normfeld($def,'Email',$fetch['email']);
        normfeld($def, $spr['kontaktperson'],$fetch['kontaktperson']);
        //abstand($def);
    }
    normfeld($def,$spr['homepage'],$fetch['homepage']);
    //abstand($def);

    normfelda($def,$spr['zugang_zur_sammlung'],$fetch['zugang_zur_sammlung']);
    normfelda($def,$spr['sammlungszeit'],$fetch['sammlungszeit']);
    normfelda($def,$spr['bildgattungen'],$fetch['bildgattungen_set']);
    if (auth_level(USER_GUEST_READER_PARTNER)) normfelda($def, $spr['bildgattungen_alt'],$fetch['bildgattungen']);
    normfelda($def,$spr['sammlungsgeschichte'],$fetch['sammlungsgeschichte']);
    normfelda($def,$spr['sammlungsbeschreibung'],$fetch['sammlungsbeschreibung']);

    if (auth_level(USER_GUEST_READER_PARTNER)) normfelda($def,'Literatur alt',$fetch['literatur']);


    $result6=mysql_query("SELECT * FROM bestand WHERE inst_id=$id ORDER BY nachlass DESC, name ASC");

    $def->assign("Bestand",$spr['bestaende']);
    while($fetch6=mysql_fetch_array($result6)){

        $def->assign("FETCH6",$fetch6);
        if (auth_level(USER_GUEST_READER_PARTNER) || ($fetch6['gesperrt']==0)){
            $def->assign('g',(auth_level(USER_GUEST_READER_PARTNER) && $fetch6['gesperrt']==1?'g':''));
            $def->parse("autodetail.z.bestn_3");
            $def->parse("autodetail.z");
            $def->assign("Bestand","");
        }
    }

    if(mysql_num_rows($result6)!=0) abstand($def);

    $lit=$spr['literatur'];

    $result7=mysql_query("SELECT literatur_institution.institution_id, literatur_institution.id AS if_id, literatur.*
    FROM literatur_institution INNER JOIN literatur ON literatur_institution.literatur_id = literatur.id
    WHERE literatur_institution.institution_id=$id ORDER BY literatur.verfasser_name");
    while($fetch7=mysql_fetch_array($result7)){
        $fetch7['if_typ']=$lit;
        $fetch7=formlit($fetch7);
        $fetch7['Literatur']=$lit;
        $def->assign("FETCH7",$fetch7);
        $def->parse("autodetail.z.lit");
        $def->parse("autodetail.z");
        $lit='';
    }
    if(mysql_num_rows($result7)!=0) abstand($def);

    $aus='';
    $result8=mysql_query("SELECT ausstellung_institution.institution_id, ausstellung_institution.id AS af_id, ausstellung.*
    FROM ausstellung_institution INNER JOIN ausstellung ON ausstellung_institution.ausstellung_id = ausstellung.id
    WHERE ausstellung_institution.institution_id=$id ORDER BY ausstellung.typ, af_id");
    while($fetch8=mysql_fetch_array($result8)){
        $typeHasChanged = false;
        if ($fetch8['typ']!=$aus){
            if($aus!=''){
                $typeHasChanged = true;
            }
            $aus=$fetch8['typ'];
            $fetch8['Ausstellung']=($aus=='E'?$spr['einzelaustellungen']:$spr['gruppenaustellungen']);
            //if ($aus=='G') abstand($def);
        } else {
            $fetch8['Ausstellung']='';
        }
        if($typeHasChanged) abstand($def);
        $fetch8=formaus($fetch8);
        $def->assign("FETCH8",$fetch8);
        $def->parse("autodetail.z.aus");
        $def->parse("autodetail.z");
    }
    if(mysql_num_rows($result8)!=0) abstand($def);

    if (auth_level(USER_WORKER)) normfeld($def, $spr['notiz'],$fetch['notiz']);
    if (auth_level(USER_WORKER)) normfeld($def, $spr['npublizieren'],$fetch['gesperrt']);
    normfeld($def,$spr['bearbeitungsdatum'],$fetch['bearbeitungsdatum']);
    normfeld($def,$spr['autorIn'],$fetch['autorin']);


    $def->parse("autodetail");
    $results.=$def->text("autodetail");
}

// prepare photograph details
$institution->assign('panel_headline', $spr['photos_from_institution']);
$institution->assign("SPR",$spr);
$institution->assign("view_all_photos",'?a=fotos&lang='.($lang != '' ? $lang : 'de').'&institution='.$id.'&submitbutton=suchen');


$objResult=mysql_query("SELECT id, dc_title AS title, dc_description AS description FROM fotos WHERE edm_dataprovider=$id ORDER BY RAND() LIMIT 0,3");
while($result=mysql_fetch_assoc($objResult)){
    $randomPhotos .= '<a href="?a=fotos&id='.$result['id'].'&institution='.$id.'"><img src="'.PHOTO_PATH.$result['id'].'.jpg" alt="'.$result['title'].($result['title']!='' && $result['description']!='' ? ' - ' : '').$result['description'].'"></a>';
}
$institution->assign('PHOTOS',$randomPhotos);
$institution->parse('contents.content_detail.photo_panel');
?>
