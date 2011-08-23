<?php
$def=new XTemplate ("././templates/list_results.xtpl");
$def->assign("ACTION",$_GET['a']);
$def->assign("ID",$_GET['id']);
$id=$_GET['id'];
$anf=$_GET['anf'];
$lang=$_GET['lang'];
$mod=$_GET['mod'];

$def->assign("SPR",$spr);

$def->assign("title",$spr['fotographInnen']);

$def->assign("BEARBEITEN", "[&nbsp;".$spr['bearbeiten']."&nbsp;]");

//$result = "";
$def->assign("LANG",$_GET['lang']);

//if mod != alph..
if($_GET['submitbutton'] != ""){
	$issearch=3;
	$vars=array();
	$vars=$_GET;
	unset($vars['a']);
	unset($vars['submitbutton']);
	unset($vars['mod']);
	unset($vars['lang']);
	//print_r($vars);
		
	$fgt="";
	foreach ($vars['fotografengattungen'] as $fg){
		if (!empty($fgt)) $fgt.='%';
		$fgt.=$fg;
	}
	if (!empty($fgt)) {
		$vars['fotografengattungen_set']=$fgt;
	}
	unset($vars['fotografengattungen']);

	$fgt="";
	foreach ($vars['kanton'] as $fg){
		if (!empty($fgt)) $fgt.='%';
		$fgt.=$fg;
	}
	if (!empty($fgt)) {
		$vars['kanton']=$fgt;
	}
	
	$fgt="";
	
	foreach ($vars['bildgattungen'] as $fg){
		if (!empty($fgt)) $fgt.='%';
		$fgt.=$fg;
	}
	if (!empty($fgt)) {
		$vars['bildgattungen_set']=$fgt;
	}
	unset($vars['bildgattungen']);
	$where="";
	$arbeitsjahr="";
	if (!empty($vars['arbeitsjahr'])){
		$arbeitsjahr=$vars['arbeitsjahr'];
	}
	
	//if(!auth_level(USER_WORKER)){
	unset($vars['arbeitsjahr']);  // no LIKE search for arbeitsjahr
	//}
	
	foreach ($vars as $key=>$value){
		if (!empty($vars[$key])){
			if (!empty($where)){
				$where.=" AND ";
			}
			if ($key=='nachname') $key='namen.nachname';
			if ($key=='vorname') $key='namen.vorname';
		 
			if( $key == 'ALL_offene_arbeiten') {
				foreach( array('werdegang','schaffensbeschrieb','uebersetzung_de','uebersetzung_fr','uebersetzung_it','uebersetzung_rm','uebersetzung_en') as $field ) {
					$where.= "({$field}_l1_user = '$value' AND {$field}_l2_user = '0') OR ";
					$where.= "({$field}_l3_user = '$value' AND {$field}_l4_user = '0') OR ";
				}
				$where.= "false ";
			} elseif( $value === "0" ) {
				$where.="$key = '' ";
			} elseif ( $value === "1" ) {
				$where.="$key <> '' ";
			} else {
				$where.="$key LIKE '%$value%' ";
			}
		}
	}

	$arb= (!empty($vars['arbeitsort'])) || (!empty($arbeitsjahr));
	
	if (!empty($arbeitsjahr)){
		if (!empty($where)){
			$where.=" AND ";
		}
		$where.="((NOT( arbeitsperioden.bis<='".$arbeitsjahr."')) OR (NOT( arbeitsperioden.bis<='".($arbeitsjahr-5)."') AND (arbeitsperioden.um_bis=1)) OR (arbeitsperioden.bis='') ) AND ((NOT( arbeitsperioden.von>='".$arbeitsjahr."')) OR (NOT( arbeitsperioden.von>='".($arbeitsjahr+5)."') AND (arbeitsperioden.um_von=1)) OR (arbeitsperioden.von=''))";
	}

	$full= (!empty($vars['volltext']));
	
	if (!auth_level(USER_GUEST_READER)) $where='(fotografen.unpubliziert=0) AND ('.$where.')';
	$query="SELECT fotografen.id, fotografen.nachname, fotografen.geburtsdatum, fotografen.gen_geburtsdatum, fotografen.todesdatum, fotografen.gen_todesdatum, fotografen.werdegang<>'' AS biog, fotografen.unpubliziert, namen.nachname, namen.vorname, namen.namenszusatz  FROM fotografen INNER JOIN namen ON fotografen.id=namen.fotografen_id WHERE ".$where." ORDER BY namen.nachname Asc, namen.vorname Asc";

	if ($arb){
		$query="SELECT fotografen.id, fotografen.nachname, fotografen.geburtsdatum, fotografen.gen_geburtsdatum, fotografen.todesdatum, fotografen.gen_todesdatum, fotografen.werdegang<>'' AS biog, fotografen.unpubliziert, namen.nachname, namen.vorname, namen.namenszusatz, arbeitsperioden.arbeitsort, arbeitsperioden.von, arbeitsperioden.um_von, arbeitsperioden.bis, arbeitsperioden.um_bis, CONCAT(arbeitsperioden.von, arbeitsperioden.bis)<>'' AS arbp FROM (fotografen INNER JOIN namen ON fotografen.id=namen.fotografen_id) INNER JOIN arbeitsperioden ON fotografen.id=arbeitsperioden.fotografen_id WHERE ".$where." ORDER BY arbp DESC, arbeitsperioden.arbeitsort ASC, arbeitsperioden.von ASC, arbeitsperioden.bis ASC";
	}

	if ($full){
		$query="SELECT fotografen.id, fotografen.nachname, fotografen.geburtsdatum, fotografen.gen_geburtsdatum, fotografen.todesdatum, fotografen.gen_todesdatum, fotografen.werdegang<>'' AS biog, fotografen.unpubliziert, namen.nachname, namen.vorname, namen.namenszusatz  FROM fotografen INNER JOIN namen ON fotografen.id=namen.fotografen_id WHERE (fotografen.unpubliziert=0) AND MATCH ( heimatort, geburtsort, todesort, beruf ,werdegang ,schaffensbeschrieb ,auszeichnungen ,kurzbio ,beruf_fr ,beruf_it ,beruf_en ,werdegang_fr ,werdegang_it ,werdegang_en ,schaffensbeschrieb_fr ,schaffensbeschrieb_it ) AGAINST ('".$vars['volltext']."')";
	}

	if(auth_level(USER_WORKER)){
		if( $mod='df' ) {
			$def->parse("list.listhead_admin_df");
		} elseif ($arb){
			$def->parse("list.listhead_admin_arb");
		}else{
			$def->parse("list.listhead_admin");			
		}

	}else{
		if ($arb){
			$def->parse("list.listhead_normal_arb");
			//$results.=$def->text("list.listhead_normal_arb");
		}else{
			$def->parse("list.listhead_normal");
			//$results.=$def->text("list.row_admin_arb");
		}
	}
//echo $query;
	$result=mysql_query($query);
	
	while($fetch=mysql_fetch_array($result)){
	
		$fetch['fgeburtsdatum']=formdatesimp($fetch['geburtsdatum'],$fetch['gen_geburtsdatum']);
		$fetch['fldatum']=formldatesimp($fetch['geburtsdatum'],$fetch['gen_geburtsdatum'],$fetch['todesdatum'],$fetch['gen_todesdatum']);
		$vonbis= ($fetch['um_von']==0?'':'um ').$fetch['von'].'-'.($fetch['um_bis']==0?'':' um ').$fetch['bis'];
		if ($vonbis=='-') $vonbis='';
		if (!empty($vonbis)) $fetch['arbeitsort'].=' '.$vonbis;
		if ($fetch['biog']==1){
			$fetch['bioclass']='subtitle3bio';
		} else {
			$fetch['bioclass']='subtitle3';
		}
		if($fetch['showkurzbio'] == 1) {
			$fetch['bioclass']='subtitle3kbio';
		}		
		
		if(auth_level(USER_GUEST_READER) && $fetch['unpubliziert']==1) $fetch['bioclass'].='x';
		
		$def->assign("FETCH",$fetch);
		if(auth_level(USER_WORKER)){
			if( $mod='df' ) {
				$def->parse("list.row_admin_df");
			} elseif ($arb) {
				$def->parse("list.row_admin_arb");
			}else{
				$def->parse("list.row_admin");
			}
		} else {
			if ($arb){
				$def->parse("list.row_normal_arb");				
			} else {
				$def->parse("list.row_normal");
			}
		}
	}
	if(auth_level(USER_WORKER)){
		$def->assign("NEU"," | <a href=\"./?a=edit&amp;id=new&amp;lang=$lang\">".$spr['neuereintrag']."</a>");
	}else{
		$def->assign("NEU","");
	}
	$def->parse("list");
	$results.=$def->text("list");
	
} else {
	//alph. suche
	//if submit is empty -> listenansicht
	if ($_GET['id'] =='' && !$ayax){
	
		//do query
		$issearch=2;
		if(auth_level(USER_GUEST_READER)){
			$result=mysql_query("SELECT fotografen.id, fotografen.geburtsdatum, fotografen.gen_geburtsdatum, fotografen.todesdatum, fotografen.gen_todesdatum, fotografen.autorIn<>'' AS biog, fotografen.showkurzbio, fotografen.unpubliziert, namen.nachname, namen.vorname, namen.namenszusatz, namen.titel, fotografen.pnd  FROM fotografen INNER JOIN namen ON fotografen.id=namen.fotografen_id WHERE namen.nachname LIKE '$anf%' ORDER BY namen.nachname Asc, namen.vorname Asc");
		}else{
			$result=mysql_query("SELECT fotografen.id, fotografen.geburtsdatum, fotografen.gen_geburtsdatum, fotografen.todesdatum, fotografen.gen_todesdatum, fotografen.autorIn<>'' AS biog, fotografen.showkurzbio, fotografen.unpubliziert, namen.nachname, namen.vorname, namen.namenszusatz, namen.titel, fotografen.pnd  FROM fotografen INNER JOIN namen ON fotografen.id=namen.fotografen_id WHERE (fotografen.unpubliziert=0) AND (namen.nachname LIKE '$anf%') ORDER BY namen.nachname Asc, namen.vorname Asc");		
		}	
		
		//assign title template:
		(auth_level(USER_WORKER)) ? $def->parse("list.listhead_admin") : $def->parse("list.listhead_normal");
		
		while($fetch=mysql_fetch_array($result)){
			if ($fetch['biog']==1){
				$fetch['bioclass']='subtitle3bio';
			} else {
				$fetch['bioclass']='subtitle3';
			}
			if($fetch['showkurzbio'] == 1) {
				$fetch['bioclass']='subtitle3kbio';
				//echo $fetch['namen'].' '.$fetch['vornamen'];
			}
			$fetch['fgeburtsdatum']=formdatesimp($fetch['geburtsdatum'],$fetch['gen_geburtsdatum']);
			$fetch['fldatum']=formldatesimp($fetch['geburtsdatum'],$fetch['gen_geburtsdatum'],$fetch['todesdatum'],$fetch['gen_todesdatum']);
			if(auth_level(USER_GUEST_READER)) if ($fetch['unpubliziert']==1){
				$fetch['bioclass'].='x';
			}
	
			$def->assign("FETCH",$fetch);
	
			if(auth_level(USER_WORKER)){
				$def->parse("list.row_admin");
				//$results.=$def->text("list.row_admin");
			}else{
				$def->parse("list.row_normal");
				//$results.=$def->text("list.row_normal");
			}
		}
	
		$def->parse("list");
		$results.=$def->text("list");
	
	} 
	else {
	
		if ($ayax){
			$def->assign("NEUO","");
			$def->parse("list.ayax");
			$def->parse("list");
			$results.=$def->text("list");
		}
	}
}
?>
