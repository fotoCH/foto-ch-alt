<?php
function namen($def,$id){ //nur fotograf
	$sql = "SELECT * FROM namen WHERE fotografen_id=$id ORDER BY id"; // Weitere Formdaten aus Tabelle 'Namen' holen
	$result = mysql_query($sql);
	if(mysql_num_rows($result)>0){
		$num=1;
		while($array=mysql_fetch_array($result)){
			if ($num==1){
				$def->assign("DELETE", "");
				$def->assign("STANDARD", "(Standard)");				
			}else{
				$lang = $_GET['lang'];	
				$loeschen = getLangContent("sprache", $_GET['lang'], "loeschen");	//seems to have no other solution...		
				$def->assign("DELETE", "<a href=\"./?a=edit&amp;n=del&n_id=$array[id]&amp;id=$id&amp;lang=$lang\">[&nbsp;".$loeschen."&nbsp;]</a>");
				//$def->assign("DELETE", "<a href=\"./?a=edit&amp;n=del&n_id=$array[id]&amp;id=$id&amp;lang=$lang\">[&nbsp;".$spr['loeschen']."&nbsp;]</a>");// does not work ??				
				$def->assign("STANDARD", "");	
				$def->assign("BR","<br />")	;		
			}
			$def->assign("NUM", $num);
			$def->assign("NAMEN", $array);			
			$def->parse("bearbeiten.form.namen");
			$def->parse("bearbeiten.form");
			$num++;
		}
		$def->parse("bearbeiten.form.new_namen");
		$def->parse("bearbeiten.form");
	}else{
		$def->parse("bearbeiten.form.new_namen");
		$def->parse("bearbeiten.form");
	}
}
function arbeitsperioden($def,$id){//nur fotograf
	$sql = "SELECT * FROM `arbeitsperioden` WHERE fotografen_id = $id ORDER BY id asc"; // Weitere Formdaten aus Tabelle 'arbeitsperioden' holen
	$result = mysql_query($sql);
	if(mysql_num_rows($result)>0){
		$num=1;
		while($array=mysql_fetch_array($result)){
			if($array['um_von']=="1"){
				$def->assign("check_von", "checked=\"checked\"");
			}else{
				$def->assign("check_von", "");
			}
			if($array['um_bis']=="1"){
				$def->assign("check_bis", "checked=\"checked\"");
			}else{
				$def->assign("check_bis", "");
			}
			$def->assign("NUM", $num);
			$def->assign("ARBEITSORT", $array);
			$def->parse("bearbeiten.form.arbeitsort");
			$def->parse("bearbeiten.form");
			$num++;
		}
		$def->parse("bearbeiten.form.new_arbeitsort");
		$def->parse("bearbeiten.form");
		
	} else {
		$def->parse("bearbeiten.form.new_arbeitsort");
		$def->parse("bearbeiten.form");
	}
}
function bestand($def,$id){
	$sql = "SELECT bestand_fotograf.fotografen_id, bestand_fotograf.id AS bf_id, institution.name AS inst_name, institution.id AS inst_id, bestand.*
	FROM bestand_fotograf INNER JOIN (bestand INNER JOIN institution ON bestand.inst_id = institution.id) ON bestand_fotograf.bestand_id = bestand.id
	WHERE bestand_fotograf.fotografen_id=$id ORDER BY bestand.nachlass DESC, bestand.name ASC"; // Weitere Formdaten aus Tabelle 'bestaende' holen
	$result = mysql_query($sql);
	if(mysql_num_rows($result)>0){
		$num=1;
		while($array=mysql_fetch_array($result)){
			$def->assign("NUM", $num);
			$def->assign("BESTAND", $array);
			$def->parse("bearbeiten.form.bestand");
			$def->parse("bearbeiten.form");
			$num++;
		}
	}
	$def->parse("bearbeiten.form.new_bestand");
	$def->parse("bearbeiten.form");
}
function literatur($def,$id){
	$sql = "SELECT literatur_fotograf.fotografen_id, literatur_fotograf.id AS if_id, literatur_fotograf.typ AS if_typ, literatur.*
	FROM literatur_fotograf INNER JOIN literatur ON literatur_fotograf.literatur_id = literatur.id
	WHERE literatur_fotograf.fotografen_id=$id ORDER BY if_id"; // Weitere Formdaten aus Tabelle 'bestaende' holen
	$result = mysql_query($sql);
	if(mysql_num_rows($result)>0){
		$num=1;
		while($array=mysql_fetch_array($result)){	
			$def->assign("NUM", $num);
			$array=formlit($array);
			$def->assign("LITERATUR", $array);
			$def->parse("bearbeiten.form.literatur");
			$def->parse("bearbeiten.form");
			$num++;
		}
	}
	$def->parse("bearbeiten.form.new_literatur");
	$def->parse("bearbeiten.form");
}
function ausstellungen($def,$id){
	// ausstellungen
	$sql = "SELECT ausstellung_fotograf.fotograf_id, ausstellung_fotograf.id AS af_id, ausstellung.*
	FROM ausstellung_fotograf INNER JOIN ausstellung ON ausstellung_fotograf.ausstellung_id = ausstellung.id
	WHERE ausstellung_fotograf.fotograf_id=$id ORDER BY ausstellung.typ, af_id"; // Weitere Formdaten aus Tabelle 'bestaende' holen
	$result = mysql_query($sql);
	if(mysql_num_rows($result)>0){
		$num=1;
		while($array=mysql_fetch_array($result)){
			$def->assign("NUM", $num);
			$def->assign("AUSSTELLUNG", $array);			
			$def->parse("bearbeiten.form.ausstellung");
			$def->parse("bearbeiten.form");
			$num++;
		}
	}
	$def->parse("bearbeiten.form.new_ausstellung");	
	$def->parse("bearbeiten.form");
}
?>