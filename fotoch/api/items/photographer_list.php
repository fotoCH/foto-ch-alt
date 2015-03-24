<?php

$anf = getClean('anf');
// alph. suche
// if submit is empty -> listenansicht
if ($id == '') {
	
	// do query
	$issearch = 2;
	if (auth_level ( USER_GUEST_READER )) {
		$sql="SELECT fotografen.id, fotografen.bearbeitungsdatum, fotografen.geburtsdatum, fotografen.gen_geburtsdatum, fotografen.todesdatum, fotografen.gen_todesdatum, fotografen.autorIn<>'' AS biog, fotografen.showkurzbio, fotografen.unpubliziert, namen.nachname, namen.vorname, namen.namenszusatz, namen.titel, fotografen.pnd  FROM fotografen INNER JOIN namen ON fotografen.id=namen.fotografen_id WHERE namen.nachname LIKE '$anf%' ORDER BY namen.nachname Asc, namen.vorname Asc";
	} else {
		$sql="SELECT fotografen.id, fotografen.bearbeitungsdatum, fotografen.geburtsdatum, fotografen.gen_geburtsdatum, fotografen.todesdatum, fotografen.gen_todesdatum, fotografen.autorIn<>'' AS biog, fotografen.showkurzbio, fotografen.unpubliziert, namen.nachname, namen.vorname, namen.namenszusatz, namen.titel, fotografen.pnd  FROM fotografen INNER JOIN namen ON fotografen.id=namen.fotografen_id WHERE (fotografen.unpubliziert=0) AND (namen.nachname LIKE '$anf%') ORDER BY namen.nachname Asc, namen.vorname Asc";
	}
	if ($_GET['recent']){
	$sql="SELECT fotografen.id, fotografen.bearbeitungsdatum, fotografen.geburtsdatum, fotografen.gen_geburtsdatum, fotografen.todesdatum, fotografen.gen_todesdatum, fotografen.autorIn<>'' AS biog, fotografen.showkurzbio, fotografen.unpubliziert, namen.nachname, namen.vorname, namen.namenszusatz, namen.titel, fotografen.pnd, bearbeitungsdatum FROM fotografen INNER JOIN namen ON fotografen.id=namen.fotografen_id WHERE (fotografen.unpubliziert=0) ORDER BY bearbeitungsdatum DESC LIMIT ".mysql_real_escape_string($_GET['recent']);
	}
	//$out['sql']=$sql;  // debug
	$result = mysql_query ($sql);
	while ( $fetch = mysql_fetch_array ( $result ) ) {
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
		if (auth_level ( USER_GUEST_READER ))
			if ($fetch ['unpubliziert'] == 1) {
				$outl ['bioclass'] .= 'x';
			}
		//print_r($fetch);
		pushfields($outl,$fetch,array('nachname','vorname','namenszusatz','id'));
		$outl['bearbeitungsdatum']=$fetch['fbearbeitungsdatum'];
		$out['res'][]=$outl;
	}
	//$out ['glob'] = $glob;
	jsonout($out);
}

?>
