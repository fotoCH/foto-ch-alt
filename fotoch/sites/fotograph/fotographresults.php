<?php
$def=new XTemplate ("././templates/list_results.xtpl");
$def->assign("ACTION",$_GET['a']);
$def->assign("ID",$_GET['id']);
$id=$_GET['id'];
$anf=$_GET['anf'];
$lang=$_GET['lang'];


$def->assign("TITLE",getLangContent("sprache",$_GET['lang'],"fotographInnen"));
$def->assign("NACHNAME",getLangContent("sprache",$_GET['lang'],"nachname"));
$def->assign("VORNAME",getLangContent("sprache",$_GET['lang'],"vorname"));
$def->assign("LEBENSDATEN",getLangContent("sprache",$_GET['lang'],"lebensdaten"));
$def->assign("ARBEITSORT",getLangContent("sprache",$_GET['lang'],"arbeitsort"));
$def->assign("ID",getLangContent("sprache",$_GET['lang'],"id"));
$def->assign("BEARBEITEN", "[&nbsp;".getLangContent("sprache",$_GET['lang'],"bearbeiten")."&nbsp;]");

$result = "";
$def->assign("LANG",$_GET['lang']);
//if mod != alph..
if($_GET['submitbutton'] != ""){

	$vars=array();
	$vars=$_GET;
	unset($vars['a']);
	unset($vars['submitbutton']);
	unset($vars['mod']);
	unset($vars['lang']);
		
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
	//unset($vars['fotografengattungen']);


	$fgt="";
	//print_r($vars['bildgattungen']);
	foreach ($vars['bildgattungen'] as $fg){
		if (!empty($fgt)) $fgt.='%';
		$fgt.=$fg;
	}
	if (!empty($fgt)) {
		$vars['bildgattungen_set']=$fgt;
	}
	unset($vars['bildgattungen']);
	$where="";
	if (!empty($vars['arbeitsjahr'])){
		$arbeitsjahr=$vars['arbeitsjahr'];
		//unset($vars['arbeitsjahr']);
	}
	if(!auth()){
		unset($vars['arbeitsjahr']);
	}
	
	foreach ($vars as $key=>$value){
		if (!empty($vars[$key])){
			if (!empty($where)){
				$where.=" AND ";
			}
			if ($key=='nachname') $key='namen.nachname';
			if ($key=='vorname') $key='namen.vorname';

			$where.="$key LIKE '%$value%' ";
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
	
	if (!auth()) $where='(fotografen.unpubliziert=0) AND ('.$where.')';
	$query="SELECT fotografen.id, fotografen.nachname, fotografen.geburtsdatum, fotografen.gen_geburtsdatum, fotografen.todesdatum, fotografen.gen_todesdatum, fotografen.werdegang<>'' AS biog, fotografen.unpubliziert, namen.nachname, namen.vorname, namen.namenszusatz  FROM fotografen INNER JOIN namen ON fotografen.id=namen.fotografen_id WHERE ".$where." ORDER BY namen.nachname Asc, namen.vorname Asc";

	if ($arb){
		$query="SELECT fotografen.id, fotografen.nachname, fotografen.geburtsdatum, fotografen.gen_geburtsdatum, fotografen.todesdatum, fotografen.gen_todesdatum, fotografen.werdegang<>'' AS biog, fotografen.unpubliziert, namen.nachname, namen.vorname, namen.namenszusatz, arbeitsperioden.arbeitsort, arbeitsperioden.von, arbeitsperioden.um_von, arbeitsperioden.bis, arbeitsperioden.um_bis, CONCAT(arbeitsperioden.von, arbeitsperioden.bis)<>'' AS arbp FROM (fotografen INNER JOIN namen ON fotografen.id=namen.fotografen_id) INNER JOIN arbeitsperioden ON fotografen.id=arbeitsperioden.fotografen_id WHERE $where ORDER BY arbp DESC, arbeitsperioden.arbeitsort ASC, arbeitsperioden.von ASC, arbeitsperioden.bis ASC";
	}

	if ($full){
		$query="SELECT fotografen.id, fotografen.nachname, fotografen.geburtsdatum, fotografen.gen_geburtsdatum, fotografen.todesdatum, fotografen.gen_todesdatum, fotografen.werdegang<>'' AS biog, fotografen.unpubliziert, namen.nachname, namen.vorname, namen.namenszusatz  FROM fotografen INNER JOIN namen ON fotografen.id=namen.fotografen_id WHERE (fotografen.unpubliziert=0) AND MATCH ( heimatort,geburtsort,todesort,beruf,schaffensbeschrieb,werdegang,auszeichnungen) AGAINST ('".$vars['volltext']."')";
	}

	if(auth()){
		if ($arb){
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
	//print_r($vars);
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
		if(auth()) if ($fetch['unpubliziert']==1) $fetch['bioclass']='subtitle3x';
		
		//echo "fetch:";
		//print_r($fetch);
		$def->assign("FETCH",$fetch);
		if(auth()){
			if ($arb){
				$def->parse("list.row_admin_arb");
				//$results.=$def->text("list.row_admin_arb");
			}else{
				$def->parse("list.row_admin");
				//$results.=$def->text("list.row_admin");
			}
		}else{
			if ($arb){
				$def->parse("list.row_normal_arb");
				//$results.=$def->text("list.row_normal_arb");
			}else{
				$def->parse("list.row_normal");
				//$results.=$def->text("list.row_normal");
			}
			//;
		}
	}
	if(auth()){
		$def->assign("NEU"," | <a href=\"./?a=edit&amp;id=new&amp;lang=$lang\">neuer Eintrag</a>");
	}else{
		$def->assign("NEU","");
	}
	$def->parse("list");
	$results.=$def->text("list");
	
} else {
	//alph. suche
	//if submit is empty -> listenansicht
	if ($_GET['id'] =='' && !$ayax){
	
		if(auth()){
			$result=mysql_query("SELECT fotografen.id, fotografen.geburtsdatum, fotografen.gen_geburtsdatum, fotografen.todesdatum, fotografen.gen_todesdatum, fotografen.autorIn<>'' AS biog, fotografen.unpubliziert, namen.nachname, namen.vorname, namen.namenszusatz, namen.titel  FROM fotografen INNER JOIN namen ON fotografen.id=namen.fotografen_id WHERE namen.nachname LIKE '$anf%' ORDER BY namen.nachname Asc, namen.vorname Asc");
			$def->parse("list.listhead_admin");//?
		}else{
			$result=mysql_query("SELECT fotografen.id, fotografen.geburtsdatum, fotografen.gen_geburtsdatum, fotografen.todesdatum, fotografen.gen_todesdatum, fotografen.autorIn<>'' AS biog, fotografen.unpubliziert, namen.nachname, namen.vorname, namen.namenszusatz, namen.titel  FROM fotografen INNER JOIN namen ON fotografen.id=namen.fotografen_id WHERE (fotografen.unpubliziert=0) AND (namen.nachname LIKE '$anf%') ORDER BY namen.nachname Asc, namen.vorname Asc");
			$def->parse("list.listhead_normal");//?
		}	
		
		//$results.=$def->text("list.listhead_normal");
		while($fetch=mysql_fetch_array($result)){
			if ($fetch['biog']==1){
				$fetch['bioclass']='subtitle3bio';
			} else {
				$fetch['bioclass']='subtitle3';
			}
			$fetch['fgeburtsdatum']=formdatesimp($fetch['geburtsdatum'],$fetch['gen_geburtsdatum']);
			$fetch['fldatum']=formldatesimp($fetch['geburtsdatum'],$fetch['gen_geburtsdatum'],$fetch['todesdatum'],$fetch['gen_todesdatum']);
			if(auth()) if ($fetch['unpubliziert']==1) $fetch['bioclass']='subtitle3x';
	
			$def->assign("FETCH",$fetch);
	
			if(auth()){
				$def->parse("list.row_admin");
				//$results.=$def->text("list.row_admin");
			}else{
				$def->parse("list.row_normal");
				//$results.=$def->text("list.row_normal");
			}
		}
	
		$def->parse("list");
		$results.=$def->text("list");
		//echo $results." bla list";
		//$out.=$def->text("list");
	
	} 
	else {
	
		if ($ayax){
			$def->assign("NEUO","");
			$def->parse("list.ayax");
			$def->parse("list");
			$results.=$def->text("list");
			//echo $results." bla list 2";
			//$out.=$def->text("list");
		}
	}
}
?>