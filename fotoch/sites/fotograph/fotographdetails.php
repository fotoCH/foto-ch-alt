<?php
include(config.inc.php);
$def=new XTemplate ("././templates/item_details.xtpl");
$def->assign("ACTION",$_GET['a']);
$def->assign("ID",$_GET['id']);
$def->assign("LANG",$_GET['lang']);
$id=$_GET['id'];
$lang=$_GET['lang'];
$def->assign("TITLE", $spr['fotographIn']);
$bearbeiten = "[&nbsp;".$spr['bearbeiten']."&nbsp;]";

$def->assign("SPR", $spr);

$det="autodetail";
if ($_GET['style']=='print') $det="detailprint";


$def->assign("KOMMA1", ", "); //assign komma for name, vorname in header

if(auth_level(USER_GUEST_READER)) {
	$result = mysql_query("SELECT * FROM fotografen WHERE (id=$id)");
}
else {
	$result=mysql_query("SELECT * FROM fotografen WHERE (id=$id) AND (unpubliziert=0)");
}

while($fetch=mysql_fetch_array($result)){
	if ($fetch['originalsprache']=='fr' && $_GET['clang']=='') $clanguage='fr';
	$def->assign('sprachanzeige',checklangsf($fetch,array('beruf','umfeld','werdegang','schaffensbeschrieb'),"<a href=\"./?a=fotograph&amp;id=$id&amp;lang=$lang"));
	if (auth_level(USER_WORKER)){
		//$def->assign('id','$id');
		$def->assign('bearb',"<a href=\"./?a=edit&amp;id=$id&amp;lang=$lang\">$bearbeiten</a>");

	} else {
		$fetch['id']='';
	}
	if(auth_level(USER_GUEST_READER)){
		$def->assign("idd",$id);
		$def->parse($det.".idd");
	}
	if(auth_level(USER_GUEST_READER_PARTNER)){
		if ($fetch['pnd']){
			$def->assign("pnd",'<a target="_new" href="http://d-nb.info/gnd/'.$fetch['pnd'].'">'.$fetch['pnd'].'</a>');
			$def->parse($det.".pnd");
		}
		//normfelda($def,'PND',($fetch['pnd']?'<a target="_new" href="http://d-nb.info/gnd/'.$fetch['pnd'].'">'.$fetch['pnd'].'</a>':''));
	} else {
		if ($fetch['pnd'] && $fetch['pnd_status']==1){
			$def->assign("pnd",'<a target="_new" href="http://d-nb.info/gnd/'.$fetch['pnd'].'">'.$fetch['pnd'].'</a>');
			$def->parse($det.".pnd2");
		}
		
	}

	$fetch['fbearbeitungsdatum']=formdatesimp($fetch['bearbeitungsdatum'],0);
	$fetch['fldatum']=formldatesimp2($fetch['geburtsdatum'],$fetch['gen_geburtsdatum'],$fetch['todesdatum'],$fetch['gen_todesdatum'],$fetch['geburtsort'],$fetch['todesort']);
	$fetch['fumfeld']=formumfeld(clangcont($fetch,'umfeld'));
	if ($_GET['lang']!='de'){
		$fetch['fotografengattungen_set']=setuebersetzungen('fotografengattungen_uebersetzungen',$fetch['fotografengattungen_set']);
	}
	$fetch['fotografengattungen_set']=str_replace(',',', ',$fetch['fotografengattungen_set']);
	if ($fetch['geschlecht']=='f'){  // nur in deutscher Sprache
		$fetch['fotografengattungen_set']=str_replace('otograf','otografin',$fetch['fotografengattungen_set']);
		$fetch['fotografengattungen_set']=str_replace('lehrer','lehrerin',$fetch['fotografengattungen_set']);
		$fetch['fotografengattungen_set']=str_replace('reporter','reporterin',$fetch['fotografengattungen_set']);
		$fetch['fotografengattungen_set']=str_replace('abrikant','abrikantin',$fetch['fotografengattungen_set']);
		$fetch['fotografengattungen_set']=str_replace('issenschaftler','issenschaftlerin',$fetch['fotografengattungen_set']);	
		$fetch['fotografengattungen_set']=str_replace('ammler','ammlerin',$fetch['fotografengattungen_set']);
	}


	if ($_GET['lang']!='de'){
		$fetch['bildgattungen_set']=setuebersetzungen('bildgattungen_uebersetzungen',$fetch['bildgattungen_set']);
	}
	$fetch['bildgattungen_set']=str_replace(',',', ',$fetch['bildgattungen_set']);
	$def->assign("FETCH",$fetch);

	$result4=mysql_query("SELECT * FROM namen WHERE fotografen_id=$id ORDER BY  id");
	//echo "SELECT * FROM arbeitsperioden WHERE fotografen_id=$id ORDER BY  id";
	$def->assign("SPR1",$spr);
	while($fetch4=mysql_fetch_array($result4)){
		if($fetch4[vorname]!=""){
			$def->assign("KOMMA",",");
		}else{
			$def->assign("KOMMA","");
		}
		if (auth_level(USER_GUEST_READER)) $fetch4['idf']="(id=$fetch4[id])";
		if (auth_level(USER_GUEST_READER)){
			$def->assign('g',$fetch['unpubliziert']==1?'g':'');
		}
		$def->assign("FETCH4",$fetch4);
		$def->parse($det.".namen");
		$def->assign("SPR1", ""); //delete table header
		//$def->assign("SPR1.NACHNAME", ""); //delete table header
		$def->assign("KOMMA1", "");
		//$results.=$def->text($det.".namen");
	}
	//just before leaving the namensvarianten, attach the lebensdaten:
	$def->assign("FETCH4",$fetch4);
	$def->parse($det.".fldatum");
	abstand($def);

	normfelda($def,$spr['heimatort'],trim($fetch['heimatort']));

	normfelda($def,$spr['beruf'],trim(clangcont($fetch,'beruf')));

	normfelda($def,$spr['fotografengattungen'],trim($fetch['fotografengattungen_set']));

	normfelda($def,$spr['bildgattungen'],trim($fetch['bildgattungen_set']));

	//$def->assign("Arbeitsort",$spr['arbeitsort']);

	$result2=mysql_query("SELECT * FROM arbeitsperioden WHERE fotografen_id=$id ORDER BY  id");
	$def->assign("SPR2",$spr);
	while($fetch2=mysql_fetch_array($result2)){
		if ($fetch2['von'].$fetch2['bis']!=''){
			$fetch2['um_vonf']=$fetch2['um_von']==0?'':$spr['um'].' ';
			$fetch2['um_bisf']=$fetch2['um_bis']==0?'':$spr['um'].' ';
			$def->assign("FETCH2",$fetch2);

			$def->parse($det.".z.arb.vonbis");
			//$results.=$def->text($det.".z.arb.vonbis");
		} else {

			$def->assign("FETCH2",$fetch2);
		}
		$def->parse($det.".z.arb");
		$def->parse($det.".z");
		$def->assign("SPR2", ""); //delete table-header
		//$results.=$def->text($det.".z");
	}
	if(mysql_num_rows($result2)!=0) abstand($def);

	normfelda($def,$spr['umfeld'],clean_entry($fetch['fumfeld']));

	if(auth_level(USER_GUEST_READER)){
		normfelda($def,$spr['biografie'],clean_entry($fetch['kurzbio']));
		normfelda($def,$spr['werdegang'],clean_entry(clangcont($fetch,'werdegang')));
		normfelda($def,$spr['schaffensbeschrieb'],clean_entry(clangcont($fetch,'schaffensbeschrieb')));
	} else {
		if($fetch['showkurzbio'] == 1) {
			normfelda($def,$spr['biografie'],clean_entry($fetch['kurzbio']));
		}
		else {
			normfelda($def,$spr['werdegang'],clean_entry(clangcont($fetch,'werdegang')));
			normfelda($def,$spr['schaffensbeschrieb'],clean_entry(clangcont($fetch,'schaffensbeschrieb')));
		}
	}
	normfelda($def,$spr['auszeichnungen_und_stipendien'],clean_entry($fetch['auszeichnungen']));

	if(auth_level(USER_GUEST_READER_PARTNER)){
		$result6=mysql_query("SELECT bestand_fotograf.fotografen_id, bestand_fotograf.id AS bf_id, CASE institution.`territoriumszugegoerigkeit` WHEN 'de' THEN institution.name WHEN 'fr' THEN institution.name_fr WHEN 'it' THEN institution.name_it WHEN 'rm' THEN institution.name_rm END AS inst_name, institution.id AS inst_id, institution.gesperrt as instgesp, bestand.*
				FROM bestand_fotograf INNER JOIN (bestand INNER JOIN institution ON bestand.inst_id = institution.id) ON bestand_fotograf.bestand_id = bestand.id
				WHERE bestand_fotograf.fotografen_id=$id ORDER BY bestand.nachlass DESC, bestand.name ASC");
	} else {
		$result6=mysql_query("SELECT bestand_fotograf.fotografen_id, bestand_fotograf.id AS bf_id, CASE institution.`territoriumszugegoerigkeit` WHEN 'de' THEN institution.name WHEN 'fr' THEN institution.name_fr WHEN 'it' THEN institution.name_it WHEN 'rm' THEN institution.name_rm END AS inst_name, institution.id AS inst_id, institution.gesperrt as instgesp, bestand.*
				FROM bestand_fotograf INNER JOIN (bestand INNER JOIN institution ON bestand.inst_id = institution.id) ON bestand_fotograf.bestand_id = bestand.id
				WHERE (bestand_fotograf.fotografen_id=$id) AND (bestand.gesperrt=0) AND (institution.gesperrt=0) ORDER BY bestand.nachlass DESC, bestand.name ASC");
	}

	$bes=$spr['bestaende'];
	while($fetch6=mysql_fetch_array($result6)){
		if (auth_level(USER_GUEST_READER_PARTNER) || $fetch6['instgesp']==0){
			$fetch6['institution']="<a href=\"./?a=institution&amp;id=".$fetch6['institution_id']."&amp;lang=$lang\">".$fetch6['institution_id']."</a>";
		} else {
			$fetch6['institution']="<a href=\"./?a=institution&amp;id=".$fetch6['institution_id']."&amp;lang=$lang\">".$fetch6['institution_id']."</a>";
		}
		if (auth_level(USER_GUEST_READER_PARTNER)){
			$def->assign("gb",($fetch6['gesperrt']==0?'':'g'));
			$def->assign("gi",($fetch6['instgesp']==0?'':'g'));
		}
		$fetch6['Bestand']=$bes;
		$def->assign("FETCH6",$fetch6);
		$def->parse($det.".z.bestn");
		$def->parse($det.".z");
		$bes='';
		//$results.=$def->text($det.".z");
	}
	if(mysql_num_rows($result6)!=0) abstand($def);

	if (auth_level(USER_GUEST_READER_PARTNER)){ // alte bestaende
		$result3=mysql_query("SELECT * FROM bestaende WHERE fotografen_id=$id ORDER BY  id");
		$def->assign("alt_best", "Alt. Best.");
		while($fetch3=mysql_fetch_array($result3)){
			if ($fetch3['institution_id']>0){
				$fetch3['institution']="<a href=\"./?a=institution&amp;id=".$fetch3['institution_id']."&amp;lang=$lang\">".$fetch3['institution']."</a>";
			}

			$def->assign("FETCH3",$fetch3);
			$def->parse($det.".z.best");
			$def->parse($det.".z");
			$def->assign("alt_best", "");
		}
		//
	}
	if(mysql_num_rows($result3)>0) abstand($def);
	$lit='';

	$result7=mysql_query("SELECT literatur_fotograf.fotografen_id, literatur_fotograf.id AS if_id, literatur_fotograf.typ AS if_typ, IF(literatur_fotograf.typ='P',literatur.jahr,literatur.verfasser_name) AS sortsp, literatur.*
			FROM literatur_fotograf INNER JOIN literatur ON literatur_fotograf.literatur_id = literatur.id
			WHERE literatur_fotograf.fotografen_id=$id ORDER BY if_typ, sortsp");

	while($fetch7=mysql_fetch_array($result7)){
		//$tmpfetch7 = $fetch7;
		$litHasChanged = false;
		if ($fetch7['if_typ']!=$lit){
			if($lit!=''){
				$litHasChanged = true;
			}
			$lit=$fetch7['if_typ'];
			$fetch7['Literatur']=($lit=='P'?$spr['primaerliteratur']:$spr['sekundaerliteratur']);
			//if ($lit=='S') abstand($def);
		} else {
			$fetch7['Literatur']='';
		}
		if($litHasChanged) abstand($def);
		$fetch7=formlit($fetch7);
		$def->assign("FETCH7",$fetch7);
		$def->parse($det.".z.lit");
		$def->parse($det.".z");
	}
	if(mysql_num_rows($result7)!=0) abstand($def);
	if (auth_level(USER_GUEST_READER)){  //alte Literatur
		normfelda($def,$spr['primaerliteratur_alt'],$fetch['primaerliteratur']);
		normfelda($def,$spr['sekundaerliteratur_alt'],$fetch['sekundaerliteratur']);
	}

	$aus='';

	$result8=mysql_query("SELECT ausstellung_fotograf.fotograf_id, ausstellung_fotograf.id AS af_id, ausstellung.*
			FROM ausstellung_fotograf INNER JOIN ausstellung ON ausstellung_fotograf.ausstellung_id = ausstellung.id
			WHERE ausstellung_fotograf.fotograf_id=$id ORDER BY ausstellung.typ, ausstellung.jahr, ausstellung.ort, af_id");
	//if(mysql_num_rows($result8)!=0) abstand($def);
	while($fetch8=mysql_fetch_array($result8)){
		$typeHasChanged=false;
		if ($fetch8['typ']!=$aus){
			if($aus!=''){
				$typeHasChanged=true;
			}
			$aus=$fetch8['typ'];
			$fetch8['Ausstellung']=($aus=='E'?$spr['einzelaustellungen']:$spr['gruppenaustellungen']);
			//if ($aus=='E') abstand($def);
			//abstand($def);
		} else {
			$fetch8['Ausstellung']='';
		}
		if($typeHasChanged) abstand($def);
		$fetch8=formaus($fetch8);
		$def->assign("FETCH8",$fetch8);
		$def->parse($det.".z.aus");
		$def->parse($det.".z");
	}
	if(mysql_num_rows($result8)!=0) abstand($def);
	if (auth_level(USER_GUEST_READER)){  //alte Literatur
		normfelda($def,$spr['einzelausstellung_alt'],$fetch['einzelausstellungen']);
		normfelda($def,$spr['gruppenausstellung_alt'],$fetch['gruppenausstellungen']);
		if(!empty($fetch['kanton'])){
			//add whitespace after every kanton (after ",") in order to allow a table to add <br />. otherwise the layout
			//will be broken. especially schweizerischer berufsfotografen verband..
			$kantone = str_replace(",", ", ",$fetch['kanton']);
			//echo $kantone;
		}
		normfelda($def,$spr['kantone'],$kantone);
		normfelda($def,'GND',($fetch['pnd']?'<a target="_new" href="http://d-nb.info/gnd/'.$fetch['pnd'].'">'.$fetch['pnd'].'</a>':''));
		if(auth_level(USER_GUEST_READER_PARTNER)) normfelda($def,$spr['notiz'],$fetch['notiz']);
		normfeld($def,$spr['npublizieren'],$fetch['unpubliziert']);
	}
	normfeld($def,$spr['autorIn'],$fetch['autorIn']);
	normfeld($def,$spr['bearbeitungsdatum'],$fetch['fbearbeitungsdatum']);
	if (auth_level(USER_GUEST_READER)){
		$def->assign('g',$fetch['unpubliziert']==1?'g':'');
	}
}//while

$def->parse($det);
$results.=$def->text($det);

// prepare photograph details
$objResult=mysql_query("SELECT vorname, nachname FROM namen WHERE fotografen_id=$id");
while($result=mysql_fetch_assoc($objResult)){
    $fotograph->assign('FOTOGRAPH', $result['vorname'].' '.$result['nachname']);
}
$fotograph->assign("SPR",$spr);
$fotograph->assign("ALLPHOTOS",'?a=fotos&lang='.($lang != '' ? $lang : 'de').'&fotograph='.$result['vorname'].'+'.$result['nachname'].'paul+senn&submitbutton=suchen');


$objResult=mysql_query("SELECT id, dc_title AS title, dc_description AS description FROM fotos WHERE dc_creator=$id ORDER BY RAND() LIMIT 0,3");
while($result=mysql_fetch_assoc($objResult)){
    $randomPhotos .= '<a href="?a=fotos&id='.$result['id'].'"><img src="'.PHOTO_PATH.$result['id'].'.jpg" alt="'.$result['title'].($result['title']!='' && $result['description']!='' ? ' - ' : '').$result['description'].'"></a>';
}
$fotograph->assign('PHOTOS',$randomPhotos);
$fotograph->parse('contents.content_detail.photo_panel');

?>