<?php 

function search_photographer($q){
	$sql="SELECT fotografen.id, fotografen.bearbeitungsdatum, fotografen.geburtsdatum, fotografen.gen_geburtsdatum, fotografen.todesdatum, fotografen.gen_todesdatum, fotografen.autorIn<>'' AS biog, fotografen.showkurzbio, fotografen.unpubliziert, namen.nachname, namen.vorname, namen.namenszusatz, namen.titel, fotografen.pnd  FROM fotografen INNER JOIN namen ON fotografen.id=namen.fotografen_id WHERE namen.nachname LIKE '$q%' ORDER BY namen.nachname Asc, namen.vorname Asc";
	$result = mysql_query ($sql);
	$c=0;
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
		$out[]=$outl;
	}
	return $out;
}

function search_literature($q){
	$count=0;
	return array();
}

function search_institution($q){
	$count=0;
	return array();
}

function search_inventory($q){
	$count=0;
	return array();
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
		$out['res'][]=$outl;
		//$def->parse("list.row_normal");

		$out[]=$outl;
		$c++;
	}
	$count=$c;
	return $out;
}

function search_photo($q){
	$count=0;
	return array();
}



?>