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
	$def->assign("TITLE", $spr['institution']);
	$def->assign("SPR", $spr);
	$bearbeiten = "&nbsp;&nbsp;[&nbsp;".$spr['bearbeiten']."&nbsp;]";
	
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
			normfeldg($def,$spr['name'],$fetch['name'],$fetch['gesperrt']);
			$def->assign("bearbeiten","");
		} else {
			normfeld($def,$spr['name'],$fetch['name']);
		}
		normfeld($def,$spr['art'],$fetch['art']);
		normfeld($def,$spr['adresse'],$fetch['adresse']);
		normfeld($def,$spr['ort'],$fetch['ort']);
		//abstand($def);
		if (auth()){
			normfeld($def,'FAX',$fetch['fax']);
			normfeld($def,'Email',$fetch['email']);
			normfeld($def, $spr['kontaktperson'],$fetch['kontaktperson']);
			//abstand($def);
		}
		normfeld($def,$spr['homepage'],$fetch['homepage']);
		//abstand($def);

		normfelda($def,$spr['zugang_zur_sammlung'],$fetch['zugang_zur_sammlung']);
		normfelda($def,$spr['sammlungszeit'],$fetch['sammlungszeit']);
		normfelda($def,$spr['bildgattungen'],$fetch['bildgattungen_set']);
		if (auth()) normfelda($def, $spr['bildgattungen_alt'],$fetch['bildgattungen']);
		normfelda($def,$spr['sammlungsgeschichte'],$fetch['sammlungsgeschichte']);
		normfelda($def,$spr['sammlungsbeschreibung'],$fetch['sammlungsbeschreibung']);

		if (auth()) normfelda($def,'Literatur alt',$fetch['literatur']);


		$result6=mysql_query("SELECT * FROM bestand WHERE inst_id=$id ORDER BY nachlass DESC, name ASC");
		
		$def->assign("Bestand",$spr['bestaende']);
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
		
		$lit=$spr['literatur'];

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
				$fetch8['Ausstellung']=($aus=='E'?$spr['einzelaustellungen']:$spr['gruppenaustellungen']);
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
			
		if (auth()) normfeld($def, $spr['notiz'],$fetch['notiz']);
		if (auth()) normfeld($def, $spr['npublizieren'],$fetch['gesperrt']);
		normfeld($def,$spr['bearbeitungsdatum'],$fetch['bearbeitungsdatum']);
		normfeld($def,$spr['autorIn'],$fetch['autorin']);


		$def->parse("autodetail");
		$results.=$def->text("autodetail");
	}
?>
