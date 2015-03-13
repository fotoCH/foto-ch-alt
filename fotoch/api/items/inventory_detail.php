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
    
    pushfields($out,$fetch,array('id','zeitraum','bestandsbeschreibung','link_extern','signatur','copyright','bildgattungen','umfang','weiteres','erschliessungsgrad','inst_id','inst_name'));
}
    
jsonout($out);
?>
