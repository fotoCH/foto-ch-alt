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
			$sql="SELECT * FROM ausstellung WHERE titel LIKE '$anf%' ORDER BY titel Asc";
		} else {
			$sql="SELECT * FROM ausstellung WHERE titel LIKE '$anf%' ORDER BY titel Asc";
		}
		if (!$anf){
		    if (!$_GET['nocache']){
    		    //$sql="SELECT * FROM ausstellung ORDER BY titel Asc";
			    jsonfile('cache/exhibition.json');
			    exit;
			} else {
			    $sql="SELECT * FROM ausstellung ORDER BY titel Asc";
			}
		} 
		$result=mysql_query($sql);
		while($fetch=mysql_fetch_array($result)){
			
			if ($fetch['gesperrt']==1) $fetch['nameclass']='subtitle3x'; else $fetch['nameclass']='subtitle3';
				
			if(auth_level(USER_GUEST_READER_PARTNER)) if ($fetch['gesperrt']==1) $outl ['nameclass']='subtitle3x';
			
			//print_r($fetch);
			pushfields($outl,$fetch,array('titel','jahr','ort','typ','institution','inst_id','nameclass','id','gesperrt'));
			
			
			$result6=mysql_query("SELECT * FROM ausstellung_fotograf WHERE ausstellung_id=".$fetch['id']);
    
		        $fotogr=array();
//			$outl=array();
			$outf=array();
			$fotographer=array();
		        while($fetch6=mysql_fetch_array($result6)){
		                $fo=getfo($fetch6['fotograf_id']);
    			        $fotogr[$fo['sortn']]=$fo;
    			}
    			mysql_free_result($result6);
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
//	                    $fotographer[]=$fotogr[$k]['fid'];
		        }
		        $outl['photographer']=$fotographer;

			
			
			$out['res'][]=$outl;
			//$def->parse("list.row_normal");
		}
	
	
	//$out ['glob'] = $glob;
	jsonout($out);
}

?>
