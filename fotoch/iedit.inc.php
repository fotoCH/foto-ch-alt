<?

function bestaende($def,$id){
	$sql = "SELECT * FROM `bestand` WHERE inst_id = $id ORDER BY bestand.nachlass DESC, bestand.name ASC"; // Weitere Formdaten aus Tabelle 'bestaende' holen
	//echo $sql;
	$result = mysql_query($sql);

	if(mysql_num_rows($result)>0){
		$num=1;
		while($array=mysql_fetch_array($result)){			
			$def->assign("NUM", $num);
			$def->assign("BESTAND", $array);
			$def->parse("bearbeiten.form.bestand_institution");
			$def->parse("bearbeiten.form");
			$num++;
		}
	}
	$def->parse("bearbeiten.form.new_bestand_institution");
	$def->parse("bearbeiten.form");
}

function literatur($def,$id){
	// literatur
	$sql = "SELECT literatur_institution.institution_id, literatur_institution.id AS if_id, literatur.*
	FROM literatur_institution INNER JOIN literatur ON literatur_institution.literatur_id = literatur.id
	WHERE literatur_institution.institution_id=$id ORDER BY if_id"; // Weitere Formdaten aus Tabelle 'bestaende' holen
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
	$def->parse("bearbeiten.form.new_literatur_institution");
	$def->parse("bearbeiten.form");
}

function ausstellungen($def,$id){
	// ausstellungen
	$sql = "SELECT ausstellung_institution.institution_id, ausstellung_institution.id AS af_id, ausstellung.*
	FROM ausstellung_institution INNER JOIN ausstellung ON ausstellung_institution.ausstellung_id = ausstellung.id
	WHERE ausstellung_institution.institution_id=$id ORDER BY ausstellung.typ, af_id"; // Weitere Formdaten aus Tabelle 'bestaende' holen
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
	$def->parse("bearbeiten.form.new_ausstellung_institution");
	$def->parse("bearbeiten.form");
}

?>