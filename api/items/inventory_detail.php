<?php

$id=$_GET['id'];
if (auth_level(USER_WORKER)){
    $result=mysqli_query($sqli, "SELECT * FROM bestand WHERE id=$id");
} else {
    $result=mysqli_query($sqli, "SELECT * FROM bestand WHERE (id=$id) AND gesperrt=0");
}

while($fetch=@mysqli_fetch_assoc($result)){

    if(auth_level(USER_GUEST_READER)){
        $outl['idd']=$id;
    }
    $fetch['bildgattungen']=str_replace(',',', ',$fetch['bildgattungen']);
    $inst=getinsta($fetch['inst_id']);
    $fetch['inst_name']=$inst['name'];
    if (!auth_level(USER_GUEST_READER_PARTNER) && $inst['gesperrt']) {
        die('Not allowed to watch this.'); // sollte nicht passieren
    }
    if (array_key_exists('lang', $_GET) && $_GET['lang'] != 'de'){
        $fetch['bildgattungen']=setuebersetzungen('bildgattungen_uebersetzungen',$fetch['bildgattungen']);
    }
    $fetch['bildgattungen_set']=str_replace(',',', ',$fetch['bildgattungen_set']);
    $fetch['bearbeitungsdatum']=formdatesimp2($fetch['bearbeitungsdatum'],0);
    pushfields($out,$fetch,array('id','name','bearbeitungsdatum','zeitraum','bestandsbeschreibung','link_extern','signatur','copyright','bildgattungen','umfang','weiteres','erschliessungsgrad','inst_id','inst_name'));
    $result6=mysqli_query($sqli, "SELECT * FROM bestand_fotograf WHERE bestand_id=$id");

    $fotogr=array();

    while($fetch6=mysqli_fetch_array($result6)){
        //if ($fetch6['institution_id']>0){
        if ($fetch6['namen_id']){
            $fo=getfon($fetch6['namen_id']);
    
        } else {
            $fo=getfo($fetch6['fotografen_id']);
        }
        $fotogr[$fo['sortn']]=$fo;
        //    $fotogr['fid']=$fetch6['fotografen_id'];
    }
    $foton=array_keys($fotogr);
    sort($foton);
    foreach ($foton as $k){
            //print_r($fo);
            $outf['name']=$fotogr[$k]['namen'];
            $outf['id']=$fotogr[$k]['fid'];
            $outf['nachname']=$fotogr[$k]['nachname'];
            $outf['vorname']=$fotogr[$k]['vorname'];
            $outf['namenszusatz']=$fotogr[$k]['namenszusatz'];
            $outf['gesperrt']=$fotogr[$k]['gesperrt'];

            $fotographer[]=$outf;
 
    }
    $out['photographer']=$fotographer;
        
    $objResult=mysqli_query($sqli, "SELECT id, dc_title AS title, dc_description AS description, image_path FROM fotos WHERE dcterms_ispart_of=$id ORDER BY RAND() LIMIT 0,3");
    while($result=mysqli_fetch_assoc($objResult)){
        $photo[]=$result;
    }
    $out['photos']=$photo;
}

jsonout($out);
?>
