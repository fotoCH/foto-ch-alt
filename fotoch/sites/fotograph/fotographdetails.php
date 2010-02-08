<?php
	$def=new XTemplate ("././templates/item_details.xtpl");
	$def->assign("ACTION",$_GET['a']);
	$def->assign("ID",$_GET['id']);
	$def->assign("LANG",$_GET['lang']);
	$id=$_GET['id'];
	$lang=$_GET['lang'];
	$bearbeiten = "[&nbsp;".getLangContent("sprache",$_GET['lang'],"bearbeiten")."&nbsp;]";

		
	$fotographIn = getLangContent("sprache",$_GET['lang'],"fotographIn");
	$def->assign("TITLE",$fotographIn);
	if(auth()) $def->assign("idd",$id);
	
	$nachname = getLangContent("sprache",$_GET['lang'],"nachname");
	$def->assign("NACHNAME",$nachname);
	
	$vorname = getLangContent("sprache",$_GET['lang'],"vorname");
	$def->assign("VORNAME",$vorname);
	
	$def->assign("KOMMA1", ", "); //assign komma for name, vorname in header
	
	$lebensdaten = getLangContent("sprache",$_GET['lang'],"lebensdaten");
	$heimatort = getLangContent("sprache",$_GET['lang'],"heimatort");
	$beruf = getLangContent("sprache",$_GET['lang'],"beruf");
	$fotografengattungen = getLangContent("sprache",$_GET['lang'],"fotografengattungen");
	$bildgattungen = getLangContent("sprache",$_GET['lang'],"bildgattungen");
	$arbeitsort = getLangContent("sprache",$_GET['lang'],"arbeitsort");
		
		if(auth()) {
			$result = mysql_query("SELECT * FROM fotografen WHERE (id=$id)");
		}
		else {
			$result=mysql_query("SELECT * FROM fotografen WHERE (id=$id) AND (unpubliziert=0)");
		}	
		$det="autodetail";
		if ($_GET['style']=='print') $det="detailprint";
		while($fetch=mysql_fetch_array($result)){
			if (auth()){
				//$def->assign('id','$id');
				$def->assign('bearb',"<a href=\"./?a=edit&amp;id=$id&amp;lang=$lang\">$bearbeiten</a>");
				
			} else {
				$fetch['id']='';
			}
	
			$fetch['fbearbeitungsdatum']=formdatesimp($fetch['bearbeitungsdatum'],0);
			$fetch['fldatum']=formldatesimp2($fetch['geburtsdatum'],$fetch['gen_geburtsdatum'],$fetch['todesdatum'],$fetch['gen_todesdatum'],$fetch['geburtsort'],$fetch['todesort']);
			$fetch['fumfeld']=formumfeld($fetch['umfeld']);
			if ($_GET['lang']!='de'){
				$fetch['fotografengattungen_set']=setuebersetzungen('fotografengattungen_uebersetzungen',$fetch['fotografengattungen_set']);
			}
			$fetch['fotografengattungen_set']=str_replace(',',', ',$fetch['fotografengattungen_set']);
			if ($fetch['geschlecht']=='f'){  // nur in deutscher Sprache
				$fetch['fotografengattungen_set']=str_replace('otograf','otografin',$fetch['fotografengattungen_set']);
				$fetch['fotografengattungen_set']=str_replace('lehrer','lehrerin',$fetch['fotografengattungen_set']);
				$fetch['fotografengattungen_set']=str_replace('reporter','reporterin',$fetch['fotografengattungen_set']);
				$fetch['fotografengattungen_set']=str_replace('fabrikant','fabrikantin',$fetch['fotografengattungen_set']);
				$fetch['fotografengattungen_set']=str_replace('wissenschaftler','wissenschaftlerin',$fetch['fotografengattungen_set']);	$fetch['fotografengattungen_set']=str_replace('sammler','sammlerin',$fetch['fotografengattungen_set']);
			}
	
			
			if ($_GET['lang']!='de'){
				$fetch['bildgattungen_set']=setuebersetzungen('bildgattungen_uebersetzungen',$fetch['bildgattungen_set']);
			}
			$fetch['bildgattungen_set']=str_replace(',',', ',$fetch['bildgattungen_set']);
			$def->assign("FETCH",$fetch);
			$result4=mysql_query("SELECT * FROM namen WHERE fotografen_id=$id ORDER BY  id");
			//echo "SELECT * FROM arbeitsperioden WHERE fotografen_id=$id ORDER BY  id";
			while($fetch4=mysql_fetch_array($result4)){
				if($fetch4[vorname]!=""){
				 $def->assign("KOMMA",",");
				}else{
				 $def->assign("KOMMA","");
				}
				if (auth()) $fetch4['idf']="(id=$fetch4[id])";
				if (auth()){
					$def->assign('g',$fetch['unpubliziert']==1?'g':'');
				}
				$def->assign("FETCH4",$fetch4);
				$def->parse($det.".namen");
				$def->assign("VORNAME", ""); //delete table header
				$def->assign("NACHNAME", ""); //delete table header
				$def->assign("KOMMA1", "");				
				//$results.=$def->text($det.".namen");
			}
			//just before leaving the namensvarianten, attach the lebensdaten:
			$def->assign("FETCH4",$fetch4);
			$def->parse($det.".fldatum");
			abstand($def);
			
			normfelda($def,$heimatort,trim($fetch['heimatort']));
			
			normfelda($def,$beruf,trim($fetch['beruf']));
			
			normfelda($def,$fotografengattungen,trim($fetch['fotografengattungen_set']));
			
			normfelda($def,$bildgattungen,trim($fetch['bildgattungen_set']));
						
			$def->assign("Arbeitsort",$arbeitsort);
			
			$result2=mysql_query("SELECT * FROM arbeitsperioden WHERE fotografen_id=$id ORDER BY  id");
			
			while($fetch2=mysql_fetch_array($result2)){
				if ($fetch2['von'].$fetch2['bis']!=''){
					$fetch2['um_vonf']=$fetch2['um_von']==0?'':'um ';
					$fetch2['um_bisf']=$fetch2['um_bis']==0?'':'um ';
					$def->assign("FETCH2",$fetch2);
					 
					$def->parse($det.".z.arb.vonbis");
					//$results.=$def->text($det.".z.arb.vonbis");
				} else {
	
					$def->assign("FETCH2",$fetch2);
				}
				$def->parse($det.".z.arb");
				$def->parse($det.".z");
				$def->assign("Arbeitsort", ""); //delete table-header
				//$results.=$def->text($det.".z");
			}
			if(mysql_num_rows($result2)!=0) abstand($def); 
			
			$umfeld = getLangContent("sprache",$_GET['lang'],"umfeld");
			$werdegang = getLangContent("sprache",$_GET['lang'],"werdegang");
			$schaffensbeschrieb = getLangContent("sprache",$_GET['lang'],"schaffensbeschrieb");
			$auszeichnungen_und_stipendien = getLangContent("sprache",$_GET['lang'],"auszeichnungen_und_stipendien");
			normfelda($def,$umfeld,clean_entry($fetch['fumfeld']));
			
			normfelda($def,$werdegang,clean_entry($fetch['werdegang']));
			normfelda($def,$schaffensbeschrieb,clean_entry($fetch['schaffensbeschrieb']));
			normfelda($def,$auszeichnungen_und_stipendien,clean_entry($fetch['auszeichnungen']));
			
			if(auth()){
				$result6=mysql_query("SELECT bestand_fotograf.fotografen_id, bestand_fotograf.id AS bf_id, institution.name AS inst_name, institution.id AS inst_id, institution.gesperrt as instgesp, bestand.*
				FROM bestand_fotograf INNER JOIN (bestand INNER JOIN institution ON bestand.inst_id = institution.id) ON bestand_fotograf.bestand_id = bestand.id
				WHERE bestand_fotograf.fotografen_id=$id ORDER BY bestand.nachlass DESC, bestand.name ASC");
			} else {
				$result6=mysql_query("SELECT bestand_fotograf.fotografen_id, bestand_fotograf.id AS bf_id, institution.name AS inst_name, institution.id AS inst_id, institution.gesperrt as instgesp, bestand.*
				FROM bestand_fotograf INNER JOIN (bestand INNER JOIN institution ON bestand.inst_id = institution.id) ON bestand_fotograf.bestand_id = bestand.id
				WHERE (bestand_fotograf.fotografen_id=$id) AND (bestand.gesperrt=0) AND (institution.gesperrt=0) ORDER BY bestand.nachlass DESC, bestand.name ASC");
			}
			
			$bestaende = getLangContent("sprache",$_GET['lang'],"bestaende");
			
			$bes=$bestaende;
			while($fetch6=mysql_fetch_array($result6)){
				if (auth() || $fetch6['instgesp']==0){ 
					$fetch6['institution']="<a href=\"./?a=institution&amp;id=".$fetch6['institution_id']."&amp;lang=$lang\">".$fetch6['institution_id']."</a>";
				} else {
					$fetch6['institution']="<a href=\"./?a=institution&amp;id=".$fetch6['institution_id']."&amp;lang=$lang\">".$fetch6['institution_id']."</a>";
				}
				if (auth()){
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
			
			if (auth()){ // alte bestaende
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
	
			$primaerliteratur = getLangContent("sprache",$_GET['lang'],"primaerliteratur");
			$sekundaerliteratur = getLangContent("sprache",$_GET['lang'],"sekundaerliteratur");
			
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
					$fetch7['Literatur']=($lit=='P'?$primaerliteratur:$sekundaerliteratur);
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
			if (auth()){  //alte Literatur
				normfelda($def,getLangContent("sprache",$_GET['lang'],"primaerliteratur_alt"),$fetch['primaerliteratur']);
				normfelda($def,getLangContent("sprache",$_GET['lang'],"sekundaerliteratur_alt"),$fetch['sekundaerliteratur']);
	
			}
	
			$aus='';
			$einzelaustellungen = getLangContent("sprache",$_GET['lang'],"einzelaustellungen");
			$gruppenaustellungen = getLangContent("sprache",$_GET['lang'],"gruppenaustellungen");
			
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
					$fetch8['Ausstellung']=($aus=='E'?$einzelaustellungen:$gruppenaustellungen);
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
			if (auth()){  //alte Literatur
				normfelda($def,getLangContent("sprache",$_GET['lang'],"einzelausstellung_alt"),$fetch['einzelausstellungen']);
				normfelda($def,getLangContent("sprache",$_GET['lang'],"gruppenausstellung_alt"),$fetch['gruppenausstellungen']);
				if(!empty($fetch['kanton'])){
					//add whitespace after every kanton (after ",") in order to allow a table to add <br />. otherwise the layout
					//will be broken. especially schweizerischer berufsfotografen verband..
					$kantone = str_replace(",", ", ",$fetch['kanton']);
					//echo $kantone;
				}
				normfelda($def,getLangContent("sprache",$_GET['lang'],"kantone"),$kantone);
				normfelda($def,getLangContent("sprache",$_GET['lang'],"notiz"),$fetch['notiz']);
				normfeld($def,getLangContent("sprache",$_GET['lang'],"npublizieren"),$fetch['unpubliziert']);
			}
			$autorIn= getLangContent("sprache",$_GET['lang'],"autorIn");
			$bearbeitungsdatum= getLangContent("sprache",$_GET['lang'],"bearbeitungsdatum");
			normfeld($def,$autorIn,$fetch['autorIn']);
			normfeld($def,$bearbeitungsdatum,$fetch['fbearbeitungsdatum']);
			if (auth()){
				$def->assign('g',$fetch['unpubliziert']==1?'g':'');
			}	
		}//while	
		
		$def->parse($det);
		$results.=$def->text($det);
?>