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
			$result=mysqli_query($sqli, "SELECT *,".$namecase." as name,".$abkcase." as abkuerzung FROM institution WHERE $namecase LIKE '$anf%' ORDER BY ".$namecase);
		} else {
			$result=mysqli_query($sqli, "SELECT *,".$namecase." as name,".$abkcase." as abkuerzung FROM institution WHERE ($namecase LIKE '$anf%') AND (gesperrt=0) ORDER BY ".$namecase);
		}

		while($fetch=mysqli_fetch_array($result)){
			
			if ($fetch['autorin']!=''){
				$outl ['nameclass']='subtitle3bio';
			} else {
				$outl ['nameclass']='subtitle3';
			}
			if(auth_level(USER_GUEST_READER_PARTNER)) if ($fetch['gesperrt']==1) $outl ['nameclass']='subtitle3x';
			if ($fetch['abkuerzung']) $fetch['abkuerzung']='('.$fetch['abkuerzung'].')';
			
			//print_r($fetch);
			pushfields($outl,$fetch,array('name','ort','abkuerzung','nameclass','id','kanton','art_id','art'));
			$out['res'][]=$outl;
			//$def->parse("list.row_normal");
		}
	
	
	//$out ['glob'] = $glob;
	jsonout($out);
}

?>
