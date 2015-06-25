<?php 

function search_photographer($q){
	$sql="SELECT fotografen.id, fotografen.bearbeitungsdatum, fotografen.geburtsdatum, fotografen.gen_geburtsdatum, fotografen.todesdatum, fotografen.gen_todesdatum, fotografen.autorIn<>'' AS biog, fotografen.showkurzbio, fotografen.unpubliziert, namen.nachname, namen.vorname, namen.namenszusatz, namen.titel, fotografen.pnd  FROM fotografen INNER JOIN namen ON fotografen.id=namen.fotografen_id WHERE namen.nachname LIKE '$q%' ORDER BY namen.nachname Asc, namen.vorname Asc";
	$result = mysql_query ($sql);

	while ( $fetch = mysql_fetch_assoc ( $result ) ) {
		$fetch['fbearbeitungsdatum']=formdatesimp2($fetch['bearbeitungsdatum'],0);
		if ($fetch ['biog'] == 1) {
			$outl ['bioclass'] = 'subtitle3bio';
		} else {
			$outl ['bioclass'] = 'subtitle3';
		}
		if ($fetch ['showkurzbio'] == 1) {
			$outl ['bioclass'] = 'subtitle3kbio';
			// echo $fetch['namen'].' '.$fetch['vornamen'];
		}
		$outl ['fgeburtsdatum'] = formdatesimp ( $fetch ['geburtsdatum'], $fetch ['gen_geburtsdatum'] );
		$outl ['fldatum'] = formldatesimp ( $fetch ['geburtsdatum'], $fetch ['gen_geburtsdatum'], $fetch ['todesdatum'], $fetch ['gen_todesdatum'] );

		pushfields($outl,$fetch,array('nachname','vorname','namenszusatz','id'));
		$outl['bearbeitungsdatum']=$fetch['fbearbeitungsdatum'];
		if (($fetch['gesperrt']!=1) || auth_level(USER_GUEST_READER_PARTNER)) $out[]=$outl;
	}
	return $out;
}

function search_literature($q){
		if(auth_level(USER_GUEST_READER_PARTNER)){  
			$result=mysql_query("SELECT * FROM bestand WHERE name LIKE '$q%' ORDER BY  name Asc");
		} else {
			$result=mysql_query("SELECT * FROM bestand WHERE name LIKE '$q%' ORDER BY  name Asc");
		}

		while($fetch=mysql_fetch_array($result)){
			
			if ($fetch['gesperrt']==1) $fetch['nameclass']='subtitle3x'; else $fetch['nameclass']='subtitle3';
				
			if(auth_level(USER_GUEST_READER_PARTNER)) if ($fetch['gesperrt']==1) $outl ['nameclass']='subtitle3x';
			
			//print_r($fetch);
			pushfields($outl,$fetch,array('name','institution','inst_id','nameclass','id','gesperrt'));
			if (($fetch['gesperrt']!=1) || auth_level(USER_GUEST_READER_PARTNER)) $out[]=$outl;
			//$def->parse("list.row_normal");
		}
		return $out;		
	}

function search_institution($q){
	$namecase="CASE `territoriumszugegoerigkeit` WHEN 'de' THEN name WHEN 'fr' THEN name_fr WHEN 'it' THEN name_it WHEN 'rm' THEN name_rm END";
	$abkcase ="CASE `territoriumszugegoerigkeit` WHEN 'de' THEN abkuerzung WHEN 'fr' THEN abkuerzung_fr WHEN 'it' THEN abkuerzung_it WHEN 'rm' THEN abkuerzung_rm END";
	
		if(auth_level(USER_GUEST_READER_PARTNER)){
			$result=mysql_query("SELECT id, gesperrt, ".$namecase." as name,".$abkcase." as abkuerzung FROM institution WHERE $namecase LIKE '$q%' ORDER BY ".$namecase);
		} else {
			$result=mysql_query("SELECT id, gesperrt,".$namecase." as name,".$abkcase." as abkuerzung FROM institution WHERE ($namecase LIKE '$q%') AND (gesperrt=0) ORDER BY ".$namecase);
		} 

		while($fetch=mysql_fetch_assoc($result)){
			print_r($fetch);
			if ($fetch['autorin']!=''){
				$outl ['nameclass']='subtitle3bio';
			} else {
				$outl ['nameclass']='subtitle3';
			}
			if(auth_level(USER_GUEST_READER_PARTNER)) if ($fetch['gesperrt']==1) $outl ['nameclass']='subtitle3x';
			if ($fetch['abkuerzung']) $fetch['abkuerzung']='('.$fetch['abkuerzung'].')';
			
			//print_r($fetch);
			pushfields($outl,$fetch,array('name','abkuerzung','nameclass','id'));

			if (($fetch['gesperrt']!=1) || auth_level(USER_GUEST_READER_PARTNER)) $out[]=$outl;
			//$def->parse("list.row_normal");
		}
		return $out;		
	}

function search_inventory($q){
		if(auth_level(USER_GUEST_READER_PARTNER)){  
			$result=mysql_query("SELECT * FROM bestand WHERE name LIKE '$q%' ORDER BY  name Asc");
		} else {
			$result=mysql_query("SELECT * FROM bestand WHERE name LIKE '$q%' ORDER BY  name Asc");
		}

		while($fetch=mysql_fetch_array($result)){
			
			if ($fetch['gesperrt']==1) $fetch['nameclass']='subtitle3x'; else $fetch['nameclass']='subtitle3';
				
			if(auth_level(USER_GUEST_READER_PARTNER)) if ($fetch['gesperrt']==1) $outl ['nameclass']='subtitle3x';
			
			//print_r($fetch);
			pushfields($outl,$fetch,array('name','institution','inst_id','nameclass','id','gesperrt'));
			if (($fetch['gesperrt']!=1) || auth_level(USER_GUEST_READER_PARTNER)) $out[]=$outl;
			//$def->parse("list.row_normal");
		}
	
	return $out;
}

function search_exhibition($q){
	if(auth_level(USER_GUEST_READER_PARTNER)){
		$result=mysql_query("SELECT * FROM ausstellung WHERE titel LIKE '$q%' ORDER BY titel Asc");
	} else {
		$result=mysql_query("SELECT * FROM ausstellung WHERE titel LIKE '$q%' ORDER BY titel Asc");
	}
	$c=0;
	while($fetch=mysql_fetch_array($result)){
			
		if ($fetch['gesperrt']==1) $fetch['nameclass']='subtitle3x'; else $fetch['nameclass']='subtitle3';
	
		if(auth_level(USER_GUEST_READER_PARTNER)) if ($fetch['gesperrt']==1) $outl ['nameclass']='subtitle3x';
			
		//print_r($fetch);
		pushfields($outl,$fetch,array('titel','jahr','ort','typ','institution','inst_id','nameclass','id','gesperrt'));
		//$def->parse("list.row_normal");

		$out[]=$outl;
	}
	return $out;
}

function search_photo($q){
	$count=0;
	return array();
}



?>