<?php

$id=$_GET['id'];
if (auth_level(USER_WORKER)){
	$result=mysql_query("SELECT * FROM bestand WHERE id=$id");
} else {
	$result=mysql_query("SELECT * FROM bestand WHERE (id=$id) AND gesperrt=0");
}

while($fetch=mysql_fetch_array($result, MYSQL_ASSOC)){
    if(auth_level(USER_GUEST_READER)){
        $outl['idd']=$id;
        
    }
    $fetch['bildgattungen']=str_replace(',',', ',$fetch['bildgattungen']);
    $inst=getinsta($fetch['inst_id']);
    $fetch['inst_name']=$inst['name'];
    if (!auth_level(USER_GUEST_READER_PARTNER) && $inst['gesperrt']) exit; // sollte nicht passieren
    if ($_GET['lang']!='de'){
    	$fetch['bildgattungen_set']=setuebersetzungen('bildgattungen_uebersetzungen',$fetch['bildgattungen_set']);
    }
    $fetch['bildgattungen_set']=str_replace(',',', ',$fetch['bildgattungen_set']);
    $fetch['bearbeitungsdatum']=formdatesimp2($fetch['bearbeitungsdatum'],0);
    pushfields($out,$fetch,array('id','name','bearbeitungsdatum','zeitraum','bestandsbeschreibung','link_extern','signatur','copyright','bildgattungen','umfang','weiteres','erschliessungsgrad','inst_id','inst_name'));
    $result6=mysql_query("SELECT * FROM bestand_fotograf WHERE bestand_id=$id");
    
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
    		$outf['name']=$fotogr[$k]['namen'];
    		$outf['fotografen_id']=$fotogr[$k]['fid'];
    		$outf['gesperrt']=$fotogr[$k]['gesperrt'];
    
    		$fotographer[]=$outf;
 
    	}
    	$out['photographer']=$fotographer;
    	
    	$objResult=mysql_query("SELECT id, dc_title AS title, dc_description AS description, image_path FROM fotos WHERE dcterms_ispart_of=$id ORDER BY RAND() LIMIT 0,3");
    	while($result=mysql_fetch_assoc($objResult)){
    		$photo[]=$result;
    	}
    	$out['photos']=$photo;    	
}
    
jsonout($out);
?>
