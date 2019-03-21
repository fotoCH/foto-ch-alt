<?php

$glob ['ID'] = $_GET ['id'];
$id = $_GET ['id'];
$anf = $_GET ['anf'];
$lang = $_GET ['lang'];
$mod = $_GET ['mod'];

$glob ['SPR'] = $spr;

$glob ['title'] = $spr ['fotographInnen'];

$glob['LANG']=$_GET['lang'];
$namecase="CASE `territoriumszugegoerigkeit` WHEN 'de' THEN name WHEN 'fr' THEN name_fr WHEN 'it' THEN name_it WHEN 'rm' THEN name_rm END";
$abkcase ="CASE `territoriumszugegoerigkeit` WHEN 'de' THEN abkuerzung WHEN 'fr' THEN abkuerzung_fr WHEN 'it' THEN abkuerzung_it WHEN 'rm' THEN abkuerzung_rm END";

// alph. suche
// if submit is empty -> listenansicht
if ($id==''){
		$issearch=2;
		// Select: code
		if(auth_level(USER_GUEST_READER_PARTNER)){  
			$sql="SELECT * FROM bestand WHERE name LIKE '$anf%' ORDER BY  name Asc";
		} else {
			$sql="SELECT * FROM bestand WHERE name LIKE '$anf%' ORDER BY  name Asc";
		}
		if (!$anf){
                    if (!$_GET['nocache']){
                            jsonfile('cache/inventory.json');
                            exit;
                        } else {
                            $sql="SELECT * FROM bestand ORDER BY  name Asc";
                        }
                }
		$result=mysqli_query($sqli, $sql);
		while($fetch=mysqli_fetch_array($result)){
			
			if ($fetch['gesperrt']==1) $fetch['nameclass']='subtitle3x'; else $fetch['nameclass']='subtitle3';
				
			if(auth_level(USER_GUEST_READER_PARTNER)) if ($fetch['gesperrt']==1) $outl ['nameclass']='subtitle3x';
			
			//print_r($fetch);
			$inst=getinsta($fetch['inst_id']);
		        $fetch['institution']=$inst['name'];
			pushfields($outl,$fetch,array('name','institution','inst_id','nameclass','id','gesperrt'));
			
			
			$result6=mysqli_query($sqli, "SELECT * FROM bestand_fotograf WHERE bestand_id=".$fetch['id']);
    
		        $fotogr=array();
		        $fotographer=array();

		        while($fetch6=mysqli_fetch_array($result6)){
		        if ($fetch6['namen_id']){
            		    $fo=getfon($fetch6['namen_id']);
		        } else {
            		    $fo=getfo($fetch6['fotografen_id']);
		        }
		        $fotogr[$fo['sortn']]=$fo;
		        }
		        $foton=array_keys($fotogr);
		        sort($foton);
		        foreach ($foton as $k){
	                    $outf['name']=$fotogr[$k]['namen'];
        		    $outf['id']=$fotogr[$k]['fid'];
	                    $outf['nachname']=$fotogr[$k]['nachname'];
	                    $outf['vorname']=$fotogr[$k]['vorname'];
	                    $outf['namenszusatz']=$fotogr[$k]['namenszusatz'];    
	                    $outf['gesperrt']=$fotogr[$k]['gesperrt'];
	                    $outf['bildgattungen']=explode( ',', $fotogr[$k]['bildgattungen_set']);
	                    $fotographer[]=$outf;
 
    			}
		        $outl['photographer']=$fotographer;

			
			$out['res'][]=$outl;
			//$def->parse("list.row_normal");
		}
	
	
	//$out ['glob'] = $glob;
	jsonout($out);
}

?>
