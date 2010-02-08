<?php

//include("fotofunc.inc.php");
	
	$def=new XTemplate ("././templates/item_details.xtpl");
	$def->assign("ACTION",$_GET['a']);
	$def->assign("ID",$_GET['id']);
	$def->assign("LANG",$_GET['lang']);
	$lang= $_GET['lang'];
	//$def->assign("DN",$dn);
	$id=$_GET['id'];
	$anf=$_GET['anf'];
	if (!$anf){
		if (auth() && !$id){
			$ayax=1;
		} else {
			$anf='A';
		}
	}
	
	$def->assign("TITLE", getLangContent("sprache",$_GET['lang'],"institution"));
	$bearbeiten = "&nbsp;&nbsp;[&nbsp;".getLangContent("sprache",$_GET['lang'],"bearbeiten")."&nbsp;]";
	

	$name = getLangContent("sprache",$_GET['lang'],"name");
	$art = getLangContent("sprache",$_GET['lang'],"art");
	$adresse = getLangContent("sprache",$_GET['lang'],"adresse");
	$ort = getLangContent("sprache",$_GET['lang'],"ort");
	$homepage = getLangContent("sprache",$_GET['lang'],"homepage");
	$zugang_zur_sammlung = getLangContent("sprache",$_GET['lang'],"zugang_zur_sammlung");
	$sammlungszeit = getLangContent("sprache",$_GET['lang'],"sammlungszeit");
	$bildgattungen = getLangContent("sprache",$_GET['lang'],"bildgattungen");
	$sammlungsgeschichte = getLangContent("sprache",$_GET['lang'],"sammlungszeit");
	$sammlungsbeschreibung = getLangContent("sprache",$_GET['lang'],"sammlungsbeschreibung");
	
	
	if (auth())
	$result=mysql_query("SELECT * FROM institution WHERE institution.id=$id");
	else  $result=mysql_query("SELECT * FROM institution WHERE (institution.id=$id) AND (gesperrt=0)");

	while($fetch=mysql_fetch_array($result, MYSQL_ASSOC)){
		//print_r($fetch);
		$def->assign("ACTION",$_GET['a']);
		$def->assign("ID",$_GET['id']);
		if ($fetch['abkuerzung']) $fetch['name'].=' ('.$fetch['abkuerzung'].')';
		unset($fetch['abkuerzung']);
		$fetch['ort']=$fetch['plz'].' '.$fetch['ort'];
		unset($fetch['plz']);
		$fetch['homepage']=preg_replace("/http:\/\/(.*)/","<a href=\"http://\$1\" target=\"_new\">\$1</a>",$fetch['homepage']);
		$fetch['email']=preg_replace("/(.*@.*)/","<a href=\"mailto:\$1\">\$1</a>",$fetch['email']);
		$fetch['bildgattungen_set']=str_replace(',',', ',$fetch['bildgattungen_set']);

		if ($fetch['sammlungszeit_von'].$fetch['sammlungszeit_bis']!=''){
			$fetch['sammlungszeit']=$fetch['sammlungszeit_von'].' - '.$fetch['sammlungszeit_bis'];
		} else { $fetch['sammlungszeit']=''; }
		if (auth()) {
			//$fetch['name'].=" <a href=\"./?a=iedit&amp;id=$id&amp;lang=$lang\">$bearbeiten</a>";
			$def->assign("bearbeiten"," <a href=\"./?a=iedit&amp;id=$id&amp;lang=$lang\">$bearbeiten</a>");
			normfeldg($def,$name,$fetch['name'],$fetch['gesperrt']);
			$def->assign("bearbeiten","");
		} else {
			normfeld($def,$name,$fetch['name']);
		}
		normfeld($def,$art,$fetch['art']);
		normfeld($def,$adresse,$fetch['adresse']);
		normfeld($def,$ort,$fetch['ort']);
		//abstand($def);
		if (auth()){
			normfeld($def,'FAX',$fetch['fax']);
			normfeld($def,'Email',$fetch['email']);
			normfeld($def, getLangContent("sprache",$_GET['lang'],"kontaktperson"),$fetch['kontaktperson']);
			//abstand($def);
		}
		normfeld($def,$homepage,$fetch['homepage']);
		//abstand($def);

		normfelda($def,$zugang_zur_ammlung,$fetch['zugang_zur_sammlung']);
		normfelda($def,$sammlungszeit,$fetch['sammlungszeit']);
		normfelda($def,$bildgattungen,$fetch['bildgattungen_set']);
		if (auth()) normfelda($def, getLangContent("sprache",$_GET['lang'],"bildgattungen_alt"),$fetch['bildgattungen']);
		normfelda($def,$sammlungsgeschichte,$fetch['sammlungsgeschichte']);
		normfelda($def,$sammlungsbeschreibung,$fetch['sammlungsbeschreibung']);

		if (auth()) normfelda($def,'Literatur alt',$fetch['literatur']);


		$result6=mysql_query("SELECT * FROM bestand WHERE inst_id=$id ORDER BY nachlass DESC, name ASC");
		
		$bestaende = getLangContent("sprache",$_GET['lang'],"bestaende");
		
		$def->assign("Bestand",$bestaende);
		while($fetch6=mysql_fetch_array($result6)){

			$def->assign("FETCH6",$fetch6);
			if (auth() || ($fetch6['gesperrt']==0)){
				$def->assign('g',(auth() && $fetch6['gesperrt']==1?'g':''));
				$def->parse("autodetail.z.bestn_3");
				$def->parse("autodetail.z");
				$def->assign("Bestand","");
			}
		}

		if(mysql_num_rows($result6)!=0) abstand($def); 
		
		$literatur = getLangContent("sprache",$_GET['lang'],"literatur");
		
		$lit=$literatur;

		$result7=mysql_query("SELECT literatur_institution.institution_id, literatur_institution.id AS if_id, literatur.*
		FROM literatur_institution INNER JOIN literatur ON literatur_institution.literatur_id = literatur.id
		WHERE literatur_institution.institution_id=$id ORDER BY literatur.verfasser_name");
		while($fetch7=mysql_fetch_array($result7)){
			$fetch7['if_typ']=$lit;
			$fetch7=formlit($fetch7);
			$fetch7['Literatur']=$lit;
			$def->assign("FETCH7",$fetch7);
			$def->parse("autodetail.z.lit");
			$def->parse("autodetail.z");
			$lit='';
		}
		if(mysql_num_rows($result7)!=0) abstand($def); 

		$aus='';
		$einzelaustellungen = getLangContent("sprache",$_GET['lang'],"einzelaustellungen");
		$gruppenaustellungen = getLangContent("sprache",$_GET['lang'],"gruppenaustellungen");
		$result8=mysql_query("SELECT ausstellung_institution.institution_id, ausstellung_institution.id AS af_id, ausstellung.*
		FROM ausstellung_institution INNER JOIN ausstellung ON ausstellung_institution.ausstellung_id = ausstellung.id
		WHERE ausstellung_institution.institution_id=$id ORDER BY ausstellung.typ, af_id");
		while($fetch8=mysql_fetch_array($result8)){
			$typeHasChanged = false;
			if ($fetch8['typ']!=$aus){
				if($aus!=''){
					$typeHasChanged = true;
				}
				$aus=$fetch8['typ'];
				$fetch8['Ausstellung']=($aus=='E'?$einzelaustellungen:$gruppenaustellungen);
				//if ($aus=='G') abstand($def);
			} else {
				$fetch8['Ausstellung']='';
			}
			if($typeHasChanged) abstand($def);
			$fetch8=formaus($fetch8);
			$def->assign("FETCH8",$fetch8);
			$def->parse("autodetail.z.aus");
			$def->parse("autodetail.z");
		}
		if(mysql_num_rows($result8)!=0) abstand($def); 
		
		$bearbeitungsdatum = getLangContent("sprache",$_GET['lang'],"bearbeitungsdatum");
		$autorIn = getLangContent("sprache",$_GET['lang'],"autorIn");
		
		
		if (auth()) normfeld($def, getLangContent("sprache",$_GET['lang'],"notiz"),$fetch['notiz']);
		if (auth()) normfeld($def, getLangContent("sprache",$_GET['lang'],"npublizieren"),$fetch['gesperrt']);
		normfeld($def,$bearbeitungsdatum,$fetch['bearbeitungsdatum']);
		normfeld($def,$autorIn,$fetch['autorin']);


		$def->parse("autodetail");
		$results.=$def->text("autodetail");
	}
?>
