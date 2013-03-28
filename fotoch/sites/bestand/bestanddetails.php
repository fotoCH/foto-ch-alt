<?php
$def=new XTemplate ("././templates/item_details.xtpl");
$def->assign("ACTION",$_GET['a']);
$def->assign("ID",$_GET['id']);
$def->assign("LANG",$_GET['lang']);
$lang = $_GET['lang'];
$id=$_GET['id'];
$def->assign("SPR",$spr);
$def->assign("TITLE", $spr['bestand']);

if (auth_level(USER_WORKER)){
    $result=mysql_query("SELECT * FROM bestand WHERE id=$id");
} else {
    $result=mysql_query("SELECT * FROM bestand WHERE (id=$id) AND gesperrt=0");
}

$bearbeiten = "&nbsp;&nbsp;[&nbsp;".$spr['bearbeiten']."&nbsp;]";
$det="autodetail";
if ($_GET['style']=='print') $det="detailprint";
while($fetch=mysql_fetch_array($result, MYSQL_ASSOC)){
    $def->assign("ACTION",$_GET['a']);
    $def->assign("ID",$_GET['id']);
    if(auth_level(USER_GUEST_READER)){
        $def->assign("idd",$id);
        $def->parse($det.".idd");
    }

    $fetch['bildgattungen']=str_replace(',',', ',$fetch['bildgattungen']);
    if (auth_level(USER_WORKER)) {
        //$fetch['name'].=" <a href=\"./?a=bedit&amp;id=$id&amp;lang=$lang\">$bearbeiten</a>";
        $def->assign("bearbeiten"," <a href=\"./?a=bedit&amp;id=$id&amp;lang=$lang\">$bearbeiten</a>");
        normfeldg($def,$spr['name'],$fetch['name'],$fetch['gesperrt']);
        $def->assign("bearbeiten","");
    } else {
        normfeld($def,$spr['name'],$fetch['name']);
        //abstand($def);
    }
    $inst=getinsta($fetch['inst_id']);
    if (!auth_level(USER_GUEST_READER_PARTNER) && $inst['gesperrt']) exit;

    normfeldg($def,$spr['institution'],"<a href=\"./?a=institution&amp;id=".$fetch['inst_id']."&amp;lang=".$_GET['lang']."\">".$inst['name']."</a>",$inst['gesperrt']);
    normfeld($def,$spr['zeitraum'],$fetch['zeitraum']);
    normfeld($def,$spr['bestandsbeschreibung'],$fetch['bestandsbeschreibung']);
    $fetch['link_extern']=preg_replace("/http:\/\/(.*)/","<a href=\"http://\$1\" target=\"_new\">\$1</a>",$fetch['link_extern']);
    normfeld($def,$spr['link_extern'],$fetch['link_extern']);
    normfeld($def,$spr['signatur'],$fetch['signatur']);
    normfeld($def,$spr['copy'],$fetch['copyright']);

    normfeld($def,$spr['bildgattungen'],$fetch['bildgattungen']);
    normfeld($def,$spr['umfang'],$fetch['umfang']);
    normfeld($def,$spr['weitere_materialien'],$fetch['weiteres']);
    normfeld($def,$spr['erschliessungsgrad'],$fetch['erschliessungsgrad']);
    if (auth_level(USER_WORKER)) normfeld($def, $spr['fotografen_alt'],$fetch['fotografen']);

    $result6=mysql_query("SELECT * FROM bestand_fotograf WHERE bestand_id=$id");
    $def->assign("fotografIn",$spr['fotographInnen']);
    $fotogr=array();
    while($fetch6=mysql_fetch_array($result6)){
        //print_r($fetch6);
        //if ($fetch6['institution_id']>0){
        if ($fetch6['namen_id']){
            $fo=getfon($fetch6['namen_id']);

        } else {
            $fo=getfo($fetch6['fotografen_id']);
        }
        $fotogr[$fo['sortn']]=$fo;
        //	$fotogr['fid']=$fetch6['fotografen_id'];
    }
    //print_r($fotogr);
    $foton=array_keys($fotogr);
    sort($foton);
    //print_r($foton);
    foreach ($foton as $k){
        //print_r($fo);
        $fetch6['name']=$fotogr[$k]['namen'];
        $fetch6['fotografen_id']=$fotogr[$k]['fid'];
         $def->assign("g","");
        if ($fotogr[$k]['gesperrt']==1){
            if (auth_level(USER_WORKER)){ $def->assign("g","g");
                }  //$fetch6['name']='X '.$fetch6['name'];
        }

        $def->assign("FETCH6",$fetch6);

        if (auth_level(USER_WORKER) || ($fotogr[$k]['gesperrt']==0)) $def->parse("autodetail.z.bestn_2.flink"); else $def->parse("autodetail.z.bestn_2.fnlink");
        $def->parse("autodetail.z.bestn_2");
        $def->parse("autodetail.z");
        $def->assign("fotografIn","");

    }
    if(mysql_num_rows($result6)!=0) abstand($def);
    //if(mysql_result($result6)!=0) abstand($def);
    if (auth_level(USER_WORKER)) normfeld($def, $spr['notiz'],$fetch['notiz']);
    if (auth_level(USER_WORKER)) normfeld($def, $spr['npublizieren'],$fetch['gesperrt']);

    normfeld($def,$spr['bearbeitungsdatum'],$fetch['bearbeitungsdatum']);

    $def->parse("autodetail");
    $results.=$def->text("autodetail");
}
// prepare photograph details
$bestand->assign('panel_headline', $spr['photos_from_stock']);
$bestand->assign("SPR",$spr);
$bestand->assign("view_all_photos",'?a=fotos&lang='.($lang != '' ? $lang : 'de').'&stock='.$id.'&submitbutton='.$spr['submit']);

$objResult=mysql_query("SELECT id, dc_title AS title, dc_description AS description, image_path FROM fotos WHERE dcterms_ispart_of=$id ORDER BY RAND() LIMIT 0,3");
while($result=mysql_fetch_assoc($objResult)){
    $randomPhotos .= '<a href="?a=fotos&id='.$result['id'].'&stock='.$id.'"><img src="'.$result['image_path'].'" alt="'.$result['title'].($result['title']!='' && $result['description']!='' ? ' - ' : '').$result['description'].'"></a>';
}
$bestand->assign('PHOTOS',$randomPhotos);
$bestand->parse('contents.content_detail.photo_panel');
?>
