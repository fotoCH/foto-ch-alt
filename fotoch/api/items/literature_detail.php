<?php
$id=$_GET['id'];

if (auth_level(USER_WORKER)){
	$result=mysqli_query($sqli, "SELECT * FROM literatur WHERE id=$id");
	$afields=array("id","lexikon","kontaktperson","telefon","fax","email","notiz");
	
} else {
	$result=mysqli_query($sqli, "SELECT * FROM literatur WHERE id=$id");
	$afields=array();
}

while($fetch=mysqli_fetch_assoc($result)){
    $fetch['bearbeitungsdatum']=formdatesimp2($fetch['bearbeitungsdatum'],0);
	switch ($fetch['code']){
		case 'H':
			$fetch['herausgeber_name'] = $fetch['verfasser_name'];
			$fetch['herausgeber_vorname'] = $fetch['verfasser_vorname'];
			$fetch['herausgeber_code'] = '(Hg.)';
			pushfields($out,$fetch,array_merge(array('titel','herausgeber_name', 'herausgeber_vorname','herausgeber_code','bearbeitungsdatum','jahr','ort','in','nummer','seite','text','url','verlag', 'code'),$afields));
			break;
		case 'Z':
			$fetch['intext'] = implode(', ', array_filter(array($fetch['in'], $fetch['nummer'], $fetch['seite'])));
			pushfields($out,$fetch,array_merge(array('titel','verfasser_name','verfasser_vorname','intext','bearbeitungsdatum','jahr','ort','in','nummer','seite','text','url','verlag', 'code'),$afields));
			break;
		case 'P':
			$fetch['intext'] = implode(', ', array_filter(array($fetch['in'], $fetch['jahr'], $fetch['nummer'], $fetch['seite'])));
			pushfields($out,$fetch,array_merge(array('titel','verfasser_name','verfasser_vorname','intext','bearbeitungsdatum','in','nummer','seite','text','url','verlag', 'code'),$afields));
			break;
		case 'T':
			$fetch['intext'] = implode(', ', array_filter(array($fetch['in'], $fetch['jahr'], $fetch['nummer'], $fetch['seite'])));
			pushfields($out,$fetch,array_merge(array('titel','verfasser_name','verfasser_vorname','intext','bearbeitungsdatum','in','nummer','seite','text','url','verlag', 'code'),$afields));
			break;
		default:
			pushfields($out,$fetch,array_merge(array('titel','verfasser_name','verfasser_vorname','bearbeitungsdatum','jahr','ort','in','nummer','seite','text','url','verlag', 'code'),$afields));
			break;
	}
    $result6=mysqli_query($sqli, "SELECT * FROM literatur_fotograf WHERE literatur_id=$id");
    
    $fotogr=array();
    while($fetch6=mysqli_fetch_array($result6)){
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
    		$outf['nachname']=$fotogr[$k]['nachname'];
		$outf['vorname']=$fotogr[$k]['vorname'];
		$outf['namenszusatz']=$fotogr[$k]['namenszusatz'];
    		$outf['id']=$fotogr[$k]['fid'];
    		$outf['gesperrt']=$fotogr[$k]['gesperrt'];
    
    		$fotographer[]=$outf;
 
    	}
    	$out['photographer']=$fotographer;
}
    
jsonout($out);
?>
