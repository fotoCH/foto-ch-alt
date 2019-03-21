<?php

$anf = getClean('anf');
// alph. suche
// if submit is empty -> listenansicht
if ($id == '') {
	// do query
	$issearch = 2;
	if (auth_level ( USER_GUEST_READER )) {
		$sql="SELECT fotografen.id, fotografen.bearbeitungsdatum, fotografen.geburtsdatum, fotografen.gen_geburtsdatum, fotografen.todesdatum, fotografen.gen_todesdatum, fotografen.autorIn<>'' AS biog, fotografen.showkurzbio, fotografen.unpubliziert, namen.nachname, namen.vorname, namen.namenszusatz, namen.titel, fotografen.pnd, fotografen.fotografengattungen_set, fotografen.bildgattungen_set, fotografen.kanton FROM fotografen INNER JOIN namen ON fotografen.id=namen.fotografen_id WHERE namen.nachname LIKE '$anf%' ORDER BY namen.nachname Asc, namen.vorname Asc";
	} else {
		$sql="SELECT fotografen.id, fotografen.bearbeitungsdatum, fotografen.geburtsdatum, fotografen.gen_geburtsdatum, fotografen.todesdatum, fotografen.gen_todesdatum, fotografen.autorIn<>'' AS biog, fotografen.showkurzbio, fotografen.unpubliziert, namen.nachname, namen.vorname, namen.namenszusatz, namen.titel, fotografen.pnd, fotografen.fotografengattungen_set, fotografen.bildgattungen_set, fotografen.kanton FROM fotografen INNER JOIN namen ON fotografen.id=namen.fotografen_id WHERE (fotografen.unpubliziert=0) AND (namen.nachname LIKE '$anf%') ORDER BY namen.nachname Asc, namen.vorname Asc";
	}
	if (!$anf){
	    if (!$_GET['nocache']){
    		jsonfile('cache/photographer.json');
    		exit;
	    } else {
		  $sql="SELECT fotografen.id, fotografen.bearbeitungsdatum, fotografen.geburtsdatum, fotografen.gen_geburtsdatum, fotografen.todesdatum, fotografen.gen_todesdatum, fotografen.autorIn<>'' AS biog, fotografen.showkurzbio, fotografen.unpubliziert, namen.nachname, namen.vorname, namen.namenszusatz, namen.titel, fotografen.pnd, fotografen.fotografengattungen_set, fotografen.bildgattungen_set, fotografen.kanton FROM fotografen INNER JOIN namen ON fotografen.id=namen.fotografen_id WHERE (fotografen.unpubliziert=0) AND (namen.nachname < 'Z') ORDER BY namen.nachname Asc, namen.vorname Asc";
	    }
	    
	}
	if ($_GET['cache']){
	//echo("abc");
	    jsonfile('cache/photographer.json');
	    exit;
	}
	if ($_GET['recent']){
	    $sql="SELECT fotografen.id, fotografen.geschlecht, fotografen.bearbeitungsdatum, fotografen.geburtsdatum, fotografen.gen_geburtsdatum, fotografen.todesdatum, fotografen.gen_todesdatum, fotografen.autorIn<>'' AS biog, fotografen.showkurzbio, fotografen.unpubliziert, namen.nachname, namen.vorname, namen.namenszusatz, namen.titel, fotografen.pnd, bearbeitungsdatum FROM fotografen INNER JOIN namen ON fotografen.id=namen.fotografen_id WHERE (fotografen.unpubliziert=0) ORDER BY bearbeitungsdatum DESC LIMIT ".mysqli_real_escape_string($sqli, $_GET['recent']);

	}
    if ($_GET['mostviewed']) {
        $sql="SELECT fotografen.id, fotografen.geschlecht, fotografen.bearbeitungsdatum, fotografen.geburtsdatum, fotografen.gen_geburtsdatum, fotografen.todesdatum, fotografen.gen_todesdatum, fotografen.autorIn<>'' AS biog, fotografen.showkurzbio, fotografen.unpubliziert, namen.nachname, namen.vorname, namen.namenszusatz, namen.titel, fotografen.pnd, bearbeitungsdatum FROM fotografen INNER JOIN namen ON fotografen.id=namen.fotografen_id WHERE (fotografen.unpubliziert=0) ORDER BY visits DESC LIMIT ".mysqli_real_escape_string($sqli, $_GET['mostviewed']);
    }
	//$out['sql']=$sql;  // debug
	$result = mysqli_query ($sqli, $sql);
	while ( $fetch = mysqli_fetch_array ( $result ) ) {
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
		//$outl['fotografengattungen']=explode( ',', $fetch['fotografengattungen_set']);
		$outl['fotografengattungen']=$fetch['fotografengattungen_set'];
		//$outl['bildgattungen']=explode( ',', $fetch['bildgattungen_set']);
		$outl['bildgattungen']=$fetch['bildgattungen_set'];
		//$outl['kanton']=explode( ',', $fetch['kanton']);
		$outl['kanton']=$fetch['kanton'];
        $outl['geschlecht']=$fetch['geschlecht'];
		pushfields($outl,$fetch,array('nachname','vorname','namenszusatz','id'));
		$outl['bearbeitungsdatum']=$fetch['fbearbeitungsdatum'];
		$result2=mysqli_query($sqli, "SELECT * FROM arbeitsperioden WHERE fotografen_id=".$fetch['id']." ORDER BY  id");
        //$def->assign("SPR2",$spr);
		//$arbeitsperioden=array();
		$arbeitsperioden="";
	        while($fetch2=mysqli_fetch_array($result2)){
                if ($fetch2['von'].$fetch2['bis']!=''){
                        $fetch2['um_vonf']=$fetch2['um_von']==0?'':$spr['um'].' ';
                        $fetch2['um_bisf']=$fetch2['um_bis']==0?'':$spr['um'].' ';
                        //$def->assign("FETCH2",$fetch2);
                        
                        //$def->parse($det.".z.arb.vonbis");
                        //$results.=$def->text($det.".z.arb.vonbis");
                } else {
                        $fetch2['um_vonf']='';
                        $fetch2['um_bisf']='';
                        //$def->assign("FETCH2",$fetch2);
                }
                //$def->parse($det.".z.arb");
                //$def->parse($det.".z");
                //$def->assign("SPR2", ""); //delete table-header
                //$results.=$def->text($det.".z");
                //pushfields($arb,$fetch2,array('arbeitsort','um_vonf','von','um_bisf','bis'));
                //$arbeitsperioden[]=$arb;
                if ($arbeitsperioden!='') $arbeitsperioden.=', ';
                $arbeitsperioden.=$fetch2['arbeitsort'];
	        }
	        mysqli_free_result($result2);
    		$outl['arbeitsperioden']=$arbeitsperioden;
		$out['res'][]=$outl;
	}
	//$out ['glob'] = $glob;
	jsonout($out);
}

?>
