<?php

	$def=new XTemplate ("././templates/item_details.xtpl");
	$def->assign("ACTION",$_GET['a']);
	$def->assign("ID",$_GET['id']);
	$def->assign("LANG",$_GET['lang']);
	$lang = $_GET['lang'];
	$id=$_GET['id'];
	$def->assign("TITLE", getLangContent("sprache",$_GET['lang'],"bestand"));
	
	$name = getLangContent("sprache",$_GET['lang'],"name");
	$institution = getLangContent("sprache",$_GET['lang'],"institution");
	$zeitraum = getLangContent("sprache",$_GET['lang'],"zeitraum");
	$bestandsbeschreibung = getLangContent("sprache",$_GET['lang'],"bestandsbeschreibung");
	$link_extern = getLangContent("sprache",$_GET['lang'],"link_extern");
	$signatur = getLangContent("sprache",$_GET['lang'],"signatur");
	$copy = getLangContent("sprache",$_GET['lang'],"copy");
	$bildgattungen = getLangContent("sprache",$_GET['lang'],"bildgattungen");
	$umfang = getLangContent("sprache",$_GET['lang'],"umfang");
	$weitere_materialien = getLangContent("sprache",$_GET['lang'],"weitere_materialien");
	$erschliessungsgrad = getLangContent("sprache",$_GET['lang'],"erschliessungsgrad");
	$fotographInnen = getLangContent("sprache",$_GET['lang'],"fotographInnen");
	
	if (auth()){
		$result=mysql_query("SELECT * FROM bestand WHERE id=$id");
	} else {
		$result=mysql_query("SELECT * FROM bestand WHERE (id=$id) AND gesperrt=0");
	}


	$bearbeiten = "&nbsp;&nbsp;[&nbsp;".getLangContent("sprache",$_GET['lang'],"bearbeiten")."&nbsp;]";
	while($fetch=mysql_fetch_array($result, MYSQL_ASSOC)){
		$def->assign("ACTION",$_GET['a']);
		$def->assign("ID",$_GET['id']);
		$fetch['bildgattungen']=str_replace(',',', ',$fetch['bildgattungen']);
		if (auth()) {
			//$fetch['name'].=" <a href=\"./?a=bedit&amp;id=$id&amp;lang=$lang\">$bearbeiten</a>";
			$def->assign("bearbeiten"," <a href=\"./?a=bedit&amp;id=$id&amp;lang=$lang\">$bearbeiten</a>");
			normfeldg($def,$name,$fetch['name'],$fetch['gesperrt']);
			$def->assign("bearbeiten","");
		} else {
			normfeld($def,$name,$fetch['name']);
			//abstand($def);
		}
		$inst=getinsta($fetch['inst_id']);
		if (!auth() && $inst['gesperrt']) exit;

		normfeldg($def,$institution,"<a href=\"./?a=institution&amp;id=".$fetch['inst_id']."&amp;lang=".$_GET['lang']."\">".$inst['name']."</a>",$inst['gesperrt']);
		normfeld($def,$zeitraum,$fetch['zeitraum']);
		normfeld($def,$bestandsbeschreibung,$fetch['bestandsbeschreibung']);
		normfeld($def,$link_extern,$fetch['link_extern']);
		normfeld($def,$signatur,$fetch['signatur']);
		normfeld($def,$copy,$fetch['copyright']);

		normfeld($def,$bildgattungen,$fetch['bildgattungen']);
		normfeld($def,$umfang,$fetch['umfang']);
		normfeld($def,$weitere_materialien,$fetch['weiteres']);
		normfeld($def,$erschliessungsgrad,$fetch['erschliessungsgrad']);
		if (auth()) normfeld($def, getLangContent("sprache",$_GET['lang'],"fotografen_alt"),$fetch['fotografen']);

		$result6=mysql_query("SELECT * FROM bestand_fotograf WHERE bestand_id=$id");
		$def->assign("Fotograf",$fotographInnen);
		$fotogr=array();
		while($fetch6=mysql_fetch_array($result6)){
			//print_r($fetch6);
			//if ($fetch6['institution_id']>0){
			if ($fetch6['namen_id']){
				$fo=getfon($fetch6['namen_id']);
					
			} else {
				$fo=getfo($fetch6['fotografen_id']);
			}
			$fotogr[$fo['sortn']]=$fo;
			//	$fotogr['fid']=$fetch6['fotografen_id'];
		}
		//print_r($fotogr);
		$foton=array_keys($fotogr);
		sort($foton);
		//print_r($foton);
		foreach ($foton as $k){
			//print_r($fo);
			$fetch6['name']=$fotogr[$k]['namen'];
			$fetch6['fotografen_id']=$fotogr[$k]['fid'];
			 $def->assign("g","");
			if ($fotogr[$k]['gesperrt']==1){
				if (auth()){ $def->assign("g","g");
					}  //$fetch6['name']='X '.$fetch6['name'];
			}

			$def->assign("FETCH6",$fetch6);

			if (auth() || ($fotogr[$k]['gesperrt']==0)) $def->parse("autodetail.z.bestn_2.flink"); else $def->parse("autodetail.z.bestn_2.fnlink");
			$def->parse("autodetail.z.bestn_2");
			$def->parse("autodetail.z");
			$def->assign("Fotograf","");

		}
		if(mysql_num_rows($result6)!=0) abstand($def); 
		//if(mysql_result($result6)!=0) abstand($def);
		if (auth()) normfeld($def, getLangContent("sprache",$_GET['lang'],"notiz"),$fetch['notiz']);
		if (auth()) normfeld($def, getLangContent("sprache",$_GET['lang'],"npublizieren"),$fetch['gesperrt']);
		
		$bearbeitungsdatum = getLangContent("sprache",$_GET['lang'],"bearbeitungsdatum");
		normfeld($def,$bearbeitungsdatum,$fetch['bearbeitungsdatum']);
		
		$def->parse("autodetail");
		$results.=$def->text("autodetail");
	}

	?>
