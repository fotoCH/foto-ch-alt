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
			$result=mysql_query("SELECT * FROM ausstellung WHERE titel LIKE '$anf%' ORDER BY titel Asc");
		} else {
			$result=mysql_query("SELECT * FROM ausstellung WHERE titel LIKE '$anf%' ORDER BY titel Asc");
		}

		while($fetch=mysql_fetch_array($result)){
			
			if ($fetch['gesperrt']==1) $fetch['nameclass']='subtitle3x'; else $fetch['nameclass']='subtitle3';
				
			if(auth_level(USER_GUEST_READER_PARTNER)) if ($fetch['gesperrt']==1) $outl ['nameclass']='subtitle3x';
			
			//print_r($fetch);
			pushfields($outl,$fetch,array('titel','jahr','ort','typ','institution','inst_id','nameclass','id','gesperrt'));
			$out['res'][]=$outl;
			//$def->parse("list.row_normal");
		}
	
	
	//$out ['glob'] = $glob;
	jsonout($out);
}

?>
