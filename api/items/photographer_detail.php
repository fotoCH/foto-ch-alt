<?php
//$id=$_GET['id'];
if(auth_level(USER_GUEST_READER)) {
	$result = mysqli_query($sqli, "SELECT * FROM fotografen WHERE (id=$id)");
}
else {
	$result=mysqli_query($sqli, "SELECT * FROM fotografen WHERE (id=$id) AND (unpubliziert=0)");
}
//echo(mysqli_error());
while($fetch=@mysqli_fetch_assoc($result)){ 
	if ($fetch['originalsprache']=='fr' && $_GET['clang']=='') $clanguage='fr';
	$fetch['sprachanzeige']=checklangsf($fetch,array('beruf','umfeld','werdegang','schaffensbeschrieb'),"<a href=\"./?a=fotograph&amp;id=$id&amp;lang=$lang");
	if (auth_level(USER_WORKER)){

	} else {
		$fetch['id']='';
	}
	if ($fetch['pnd'] && ($fetch['pnd_status']==1 || auth_level(USER_GUEST_READER_PARTNER))){
		$fetch['pnd_f']='<a target="_new" href="http://d-nb.info/gnd/'.$fetch['pnd'].'">'.$fetch['pnd'].'</a>';
	}

	$fetch['fbearbeitungsdatum']=formdatesimp2($fetch['bearbeitungsdatum'],0);
	$fetch['fldatum']=trim(formldatesimp2($fetch['geburtsdatum'],$fetch['gen_geburtsdatum'],$fetch['todesdatum'],$fetch['gen_todesdatum'],$fetch['geburtsort'],$fetch['todesort']));
	$fetch['fumfeld']=formumfeldn(clangcont($fetch,'umfeld'));
	$out['umfeld_s']=clangues($fetch,'umfeld');
	$out['werdegang_s']=clangues($fetch,'werdegang');
	$out['schaffensbeschrieb_s']=clangues($fetch,'schaffensbeschrieb');
	$out['beruf_s']=clangues($fetch,'beruf');

	// check available languages for content language switcher
	$fetch['availableLanguages'] = array();
	if(is_array($out['umfeld_s'])){
		$fetch['availableLanguages'] = $fetch['availableLanguages'] + array_keys($out['umfeld_s']);
	}
	if(is_array($out['werdegang_s'])){
		$fetch['availableLanguages'] = $fetch['availableLanguages'] + array_keys($out['werdegang_s']);
	}
	if(is_array($out['schaffensbeschrieb_s'])){
		$fetch['availableLanguages'] = $fetch['availableLanguages'] + array_keys($out['schaffensbeschrieb_s']);
	}
	if(is_array($out['beruf_s'])){
		$fetch['availableLanguages'] = $fetch['availableLanguages'] + array_keys($out['beruf_s']);
	}


	$fetch['fotografengattungen_s'] = array();
	$fetch['bildgattungen_s'] = array();

	$translationsGattungen = getTranslationsGattungen();

	// CHANGED BY Silas Mächler > 03.07.2017 since $glob['LANG'] is the current Language. Base Lang is now fixed to "de".
	$baseLang = 'de';

	foreach ($fetch['availableLanguages'] as $lang){
		$fetch['fotografengattungen_s'][$lang] = implode(', ', 
			explode(',', 
				str_replace($translationsGattungen['fotografengattungen_uebersetzungen'][$baseLang],
					$translationsGattungen['fotografengattungen_uebersetzungen'][$lang],
					$fetch['fotografengattungen_set']
		)));
		$fetch['bildgattungen_s'][$lang] = implode(', ', explode(',',str_replace($translationsGattungen['bildgattungen_uebersetzungen'][$baseLang],$translationsGattungen['bildgattungen_uebersetzungen'][$lang],$fetch['bildgattungen_set'])));
	}

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

	$result4=mysqli_query($sqli, "SELECT * FROM namen WHERE fotografen_id=$id ORDER BY  id");
	//echo "SELECT * FROM arbeitsperioden WHERE fotografen_id=$id ORDER BY  id";
	//$def->assign("SPR1",$spr);

	while($fetch4=mysqli_fetch_array($result4)){
		if($fetch4[vorname]!=""){
			//$def->assign("KOMMA",",");
		}else{
			//$def->assign("KOMMA","");
		}
		if (auth_level(USER_GUEST_READER)) $fetch4['idf']="(id=$fetch4[id])";
		if (auth_level(USER_GUEST_READER)){
			$out['g']=$fetch['unpubliziert']==1?'g':'';
		}
		//$def->assign("FETCH4",$fetch4);
		//$def->parse($det.".namen");
		//$def->assign("SPR1", ""); //delete table header
		//$def->assign("SPR1.NACHNAME", ""); //delete table header
		//$def->assign("KOMMA1", "");
		//$results.=$def->text($det.".namen");
		pushfields($nam,$fetch4,array('titel','nachname','vorname','namenszusatz','idf'));
		$namen[]=$nam;
	}
	//just before leaving the namensvarianten, attach the lebensdaten:
	//$def->assign("FETCH4",$fetch4);
	//$def->parse($det.".fldatum");
	//abstand($def);
	$out['namen']=$namen;
	pushfields($out,$fetch,array('originalsprache','sprachanzeige','availableLanguages', 'id','pnd','pnd_status','fbearbeitungsdatum','fldatum','fumfeld','fotografengattungen_set','bildgattungen_set'));

	$out['heimatort']=trim($fetch['heimatort']);

	$out['beruf']=trim(clangcont($fetch,'beruf'));

	$out['fotografengattungen']=trim($fetch['fotografengattungen_set']);

	$out['bildgattungen']=trim($fetch['bildgattungen_set']);
	$out['fotografengattungen_s']=$fetch['fotografengattungen_s'];
	$out['bildgattungen_s']=$fetch['bildgattungen_s'];


	//$def->assign("Arbeitsort",$spr['arbeitsort']);

	$result2=mysqli_query($sqli, "SELECT * FROM arbeitsperioden WHERE fotografen_id=$id ORDER BY  id");
	//$def->assign("SPR2",$spr);

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
		pushfields($arb,$fetch2,array('arbeitsort','um_vonf','von','um_bisf','bis'));
		$arbeitsperioden[]=$arb;
	}
	$out['arbeitsperioden']=$arbeitsperioden;
	//if(mysqli_num_rows($result2)!=0) abstand($def);

	$out['umfeld']=clean_entry($fetch['fumfeld']);

	if(auth_level(USER_GUEST_READER)){
		$out['biografie']=clean_entry($fetch['kurzbio']);
		$out['werdegang']=clean_entry(clangcont($fetch,'werdegang'));
		$out['schaffensbeschrieb']=clean_entry(clangcont($fetch,'schaffensbeschrieb'));
	} else {
		if($fetch['showkurzbio'] == 1) {
			$out['biografie']=clean_entry($fetch['kurzbio']);
		}
		else {
			$out['werdegang']=clean_entry(clangcont($fetch,'werdegang'));
			$out['schaffensbeschrieb']=clean_entry(clangcont($fetch,'schaffensbeschrieb'));
		}
	}
	$out['auszeichnungen_und_stipendien']=clean_entry($fetch['auszeichnungen']);

	if(auth_level(USER_WORKER)){
		$result6=mysqli_query($sqli, "SELECT bestand_fotograf.fotografen_id, bestand_fotograf.id AS bf_id, CASE institution.`territoriumszugegoerigkeit` WHEN 'de' THEN institution.name WHEN 'fr' THEN institution.name_fr WHEN 'it' THEN institution.name_it WHEN 'rm' THEN institution.name_rm END AS inst_name, institution.id AS inst_id, institution.gesperrt as instgesp, bestand.*
				FROM bestand_fotograf INNER JOIN (bestand INNER JOIN institution ON bestand.inst_id = institution.id) ON bestand_fotograf.bestand_id = bestand.id
				WHERE bestand_fotograf.fotografen_id=$id ORDER BY bestand.nachlass DESC, bestand.name ASC");
	} else {
		$result6=mysqli_query($sqli, "SELECT bestand_fotograf.fotografen_id, bestand_fotograf.id AS bf_id, CASE institution.`territoriumszugegoerigkeit` WHEN 'de' THEN institution.name WHEN 'fr' THEN institution.name_fr WHEN 'it' THEN institution.name_it WHEN 'rm' THEN institution.name_rm END AS inst_name, institution.id AS inst_id, institution.gesperrt as instgesp, bestand.*
				FROM bestand_fotograf INNER JOIN (bestand INNER JOIN institution ON bestand.inst_id = institution.id) ON bestand_fotograf.bestand_id = bestand.id
				WHERE (bestand_fotograf.fotografen_id=$id) AND (bestand.gesperrt=0) AND (institution.gesperrt=0) ORDER BY bestand.nachlass DESC, bestand.name ASC");
	}

	$bes=$spr['bestaende'];
	while($fetch6=mysqli_fetch_array($result6)){
		if (auth_level(USER_WORKER) || $fetch6['instgesp']==0){
			$fetch6['institution']="<a href=\"./?a=institution&amp;id=".$fetch6['institution_id']."&amp;lang=$lang\">".$fetch6['institution_id']."</a>";
		} else {
			$fetch6['institution']="<a href=\"./?a=institution&amp;id=".$fetch6['institution_id']."&amp;lang=$lang\">".$fetch6['institution_id']."</a>";
		}
		if (auth_level(USER_WORKER)){
			//$def->assign("gb",($fetch6['gesperrt']==0?'':'g'));
			//$def->assign("gi",($fetch6['instgesp']==0?'':'g'));
		}
		$fetch6['Bestand']=$bes;
		//$def->assign("FETCH6",$fetch6);
		if(auth_level(USER_GUEST_READER)) {
			//$def->parse($det.".z.bestn.adm");
		}
		//$def->parse($det.".z.bestn");
		//$def->parse($det.".z");
		$bes='';
		//$results.=$def->text($det.".z");
		pushfields($best,$fetch6,array('id','inst_id','gi','name','inst_name','zeitraum','Bestand'));
		$bestaende[]=$best;
	}
	$out['bestaende']=$bestaende;
	//if(mysqli_num_rows($result6)!=0) abstand($def);

	/*if (auth_level(USER_GUEST_READER_PARTNER)){ // alte bestaende
		$result3=mysqli_query("SELECT * FROM bestaende WHERE fotografen_id=$id ORDER BY  id");
		//$def->assign("alt_best", "Alt. Best.");
		while($fetch3=mysqli_fetch_array($result3)){
			if ($fetch3['institution_id']>0){
				$fetch3['institution']="<a href=\"./?a=institution&amp;id=".$fetch3['institution_id']."&amp;lang=$lang\">".$fetch3['institution']."</a>";
			}

			//$def->assign("FETCH3",$fetch3);
			//$def->parse($det.".z.best");
			//$def->parse($det.".z");
			//$def->assign("alt_best", "");
		}
		//
	}*/
	//if(mysqli_num_rows($result3)>0) abstand($def);
	$lit='';

	$result7=mysqli_query($sqli, "SELECT literatur_fotograf.fotografen_id, literatur_fotograf.id AS if_id, literatur_fotograf.typ AS if_typ, IF(literatur_fotograf.typ='P',literatur.jahr,literatur.verfasser_name) AS sortsp, literatur.*
			FROM literatur_fotograf INNER JOIN literatur ON literatur_fotograf.literatur_id = literatur.id
			WHERE literatur_fotograf.fotografen_id=$id ORDER BY if_typ, sortsp");

	while($fetch7=mysqli_fetch_array($result7)){
		//$tmpfetch7 = $fetch7;
		$lit=$fetch7['if_typ'];
		//if($litHasChanged) abstand($def);
		$fetch7=formlit($fetch7, false);
		//$def->assign("FETCH7",$fetch7);
		if(auth_level(USER_GUEST_READER)) {
			//$def->parse($det.".z.lit.adm");
		}
		$l=array('id'=>$fetch7['id'],'text'=>$fetch7['text']);
		if ($lit=='P'){
			$plit[]=$l;
		} else {
			$slit[]=$l;
		}
		//$def->parse($det.".z.lit");
		//$def->parse($det.".z");
	}
	$out['primaerliteratur']=$plit;
	$out['sekundaerliteratur']=$slit;
	//if(mysqli_num_rows($result7)!=0) abstand($def);
	if (auth_level(USER_GUEST_READER)){  //alte Literatur
		$out['primaerliteratur_alt']=$fetch['primaerliteratur'];
		$out['sekundaerliteratur_alt']=$fetch['sekundaerliteratur'];
	}

	$aus='';
	$query ="SELECT 
			ausstellung_fotograf.fotograf_id, 
			ausstellung_fotograf.id AS af_id, 
			ausstellung.*,
			institution.gesperrt as 'gesperrt'
			FROM ausstellung_fotograf 
			INNER JOIN ausstellung ON ausstellung_fotograf.ausstellung_id = ausstellung.id
			LEFT JOIN ausstellung_institution ON ausstellung.id = ausstellung_institution.ausstellung_id
			LEFT JOIN institution ON ausstellung_institution.institution_id = institution.id
			WHERE ausstellung_fotograf.fotograf_id=$id 
			ORDER BY ausstellung.typ, ausstellung.jahr, ausstellung.ort, af_id";
	$result8=mysqli_query($sqli, $query);
	//if(mysqli_num_rows($result8)!=0) abstand($def);
	while($fetch8=mysqli_fetch_array($result8)){
		$aus=$fetch8['typ'];
		//if($typeHasChanged) abstand($def);
		$fetch8=formaus($fetch8);
		//$def->assign("FETCH8",$fetch8);
		if(auth_level(USER_GUEST_READER)) {
			//$def->parse($det.".z.aus.adm");
		}
		//$def->parse($det.".z.aus");
		//$def->parse($det.".z");
		$a=array(
			'id'=>$fetch8['id'],
			'text'=>$fetch8['text'],
			'titel'=>$fetch8['titel'],
			'ort'=>$fetch8['ort'],
			'jahr'=>$fetch8['jahr'],
			'institution'=>$fetch8['institution'],
			'gesperrt' => $fetch8['gesperrt']
		);
		if ($aus=='E'){
			$eaus[]=$a;
		} else {
			$gaus[]=$a;
		}
		
	}
	$out['einzelausstellungen']=$eaus;
	$out['gruppenausstellungen']=$gaus;
	//if(mysqli_num_rows($result8)!=0) abstand($def);
	if (auth_level(USER_GUEST_READER)){  //alte Literatur
		$out['einzelausstellung_alt']=$fetch['einzelausstellungen'];
		$out['gruppenausstellung_alt']=$fetch['gruppenausstellungen'];
		if(!empty($fetch['kanton'])){
			//add whitespace after every kanton (after ",") in order to allow a table to add <br />. otherwise the layout
			//will be broken. especially schweizerischer berufsfotografen verband..
			$kantone = str_replace(",", ", ",$fetch['kanton']);
			//echo $kantone;
		}
		$out['kantone']=$kantone;
		$out['GND']=$fetch['pnd']?'<a target="_new" href="http://d-nb.info/gnd/'.$fetch['pnd'].'">'.$fetch['pnd'].'</a>':'';
		$out['notiz']=$fetch['notiz'];
		$out['npublizieren']=$fetch['unpubliziert'];
	}
	$out['autorIn']=$fetch['autorIn'];
	$out['bearbeitungsdatum']=$fetch['fbearbeitungsdatum'];

	$objResult=mysqli_query($sqli, "SELECT id, dc_title AS title, dc_description AS description, image_path FROM fotos WHERE dc_creator=$id ORDER BY RAND() LIMIT 0,3");
	while($result=mysqli_fetch_assoc($objResult)){
		$photo[]=$result;
	}
	$out['photos']=$photo;

	// update visits
	if(is_numeric($id)) {
		$updateSQL = "UPDATE fotografen SET visits = visits + 1 WHERE id=".$id;
		// suppress errors from this update.
		@mysqli_query($sqli, $updateSQL);
	}

}//while
	jsonout($out);
/*
if(auth_level(USER_GUEST_FOTOS)){
	// prepare photo details
	$objResult=mysqli_query("SELECT vorname, nachname FROM namen WHERE fotografen_id=$id LIMIT 0,1");
	while($result=mysqli_fetch_assoc($objResult)){
		$firstname = $result['vorname'];
		$name = $result['nachname'];
		$fotograph->assign('panel_headline', $spr['photos_from'].' '.$firstname.' '.$name);
	}

	$fotograph->assign("SPR",$spr);
	$fotograph->assign("view_all_photos",'?a=fotos&lang='.($lang != '' ? $lang : 'de').'&photograph='.$firstname.(($firstname!='' && $name!='') ? '+' : '').$name.'&submitbutton='.$spr['submit']);

	$objResult=mysqli_query("SELECT id, dc_title AS title, dc_description AS description, image_path FROM fotos WHERE dc_creator=$id ORDER BY RAND() LIMIT 0,3");
	while($result=mysqli_fetch_assoc($objResult)){
		$randomPhotos .= '<a href="?a=fotos&id='.$result['id'].'&photograph='.$firstname.(($firstname!='' && $name!='') ? '+' : '').$name.'"><img src="'.$result['image_path'].'" alt="'.$result['title'].($result['title']!='' && $result['description']!='' ? ' - ' : '').$result['description'].'"></a>';
	}
	$fotograph->assign('PHOTOS',$randomPhotos);
	$fotograph->parse('contents.content_detail.photo_panel');
} */

function getTranslationsGattungen(){
	global $sqli;
	$translationsResult = mysqli_query($sqli, "Select * from sprache WHERE array>0");
	$translations = array();
	while($row=@mysqli_fetch_assoc($translationsResult)) {
		$translations[$row['name']]['de'] = explode(',', $row['de']);
		$translations[$row['name']]['fr'] = explode(',', $row['fr']);
		$translations[$row['name']]['it'] = explode(',', $row['it']);
		$translations[$row['name']]['rm'] = explode(',', $row['rm']);
		$translations[$row['name']]['en'] = explode(',', $row['en']);
	}
	return $translations;
}
?>
