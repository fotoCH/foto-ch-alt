<?php

$id=$_GET['id'];

if (auth_level(USER_WORKER)){
	$result=mysql_query("SELECT * FROM literatur WHERE id=$id");
	$afields=array("id","typ","notiz");
	
} else {
	$result=mysql_query("SELECT * FROM literatur WHERE id=$id");
	$afields=array();
}



while($fetch=mysql_fetch_array($result, MYSQL_ASSOC)){
    $fetch['bearbeitungsdatum']=formdatesimp2($fetch['bearbeitungsdatum'],0);
    $fetch=formaus($fetch);
    pushfields($out,$fetch,array_merge(array('text','bearbeitungsdatum'),$afields));
    $result6=mysql_query("SELECT * FROM ausstellung_fotograf WHERE ausstellung_id=$id");
    
    $fotogr=array();
    while($fetch6=mysql_fetch_array($result6)){
    	//print_r($fetch6);
    	//if ($fetch6['institution_id']>0){
    	if ($fetch6['namen_id']){
    		$fo=getfon($fetch6['namen_id']);
    
    	} else {
    		$fo=getfo($fetch6['fotograf_id']);
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
}
    
jsonout($out);
?>
