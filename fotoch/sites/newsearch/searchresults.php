<?php

global $debug;

function getnames(&$names){
	$result=mysql_query("SELECT fotografen.id, namen.nachname, namen.vorname, namen.namenszusatz  FROM fotografen INNER JOIN namen ON fotografen.id=namen.fotografen_id ORDER BY namen.nachname Asc, namen.vorname Asc");
	$names=array();
	while($fetch=mysql_fetch_array($result)){
		$id=$fetch['id'];
		$names[$id]=$fetch;
	}

}

$def=new XTemplate ("././templates/list_results.xtpl");
$def->assign("ACTION",$_GET['a']);
$def->assign("ID",$_GET['id']);
$def->assign("LANG",$_GET['lang']);
$def->assign("SPR",$spr);

$lang = $_GET['lang'];
$id=$_GET['id'];
$anf=$_GET['anf'];
$volltext = mysql_real_escape_string($_GET['volltext']);



$def->assign("title","<span id=\"bestandsliste\">".$spr['bestand']."</span><a href=\"".$_SERVER['REQUEST_URI']."#top\">&uarr;</a>");

$def->assign("BEARBEITEN","[&nbsp;".$spr['bearbeiten']."&nbsp;]");

// volltextsuche in bestand und institution
if ($volltext !='') {
	
	testauth();
	//tabllenkopf
	$def->parse("list.head_bestand");
	
	// Bestände auslesen
	$result=mysql_query("SELECT * FROM bestand WHERE name LIKE '%$volltext%' OR `bestandsbeschreibung` LIKE '%$volltext%' ORDER BY name Asc");
	$issearch=3;

	while($fetch=mysql_fetch_array($result)){

		if ($fetch['gesperrt']==1) $fetch['nameclass']='subtitle3x'; else $fetch['nameclass']='subtitle3';
		$def->assign("FETCH",$fetch);
		$def->parse("list.row".((auth_level(USER_WORKER))?'_admin_bestand':'_normal_bestand'));
	}
	// bestände in liste(tabelle) ausgeben
	$def->parse("list");

	// überschrift für institution mit ankerlink
	$def->assign("title","<span id=\"institutionsliste\">".$spr['institution']."</span><a href=\"".$_SERVER['REQUEST_URI']."#top\">&uarr;</a>");
	$def->parse("list.listhead_normal_institution");
	// Select: code
	//$volltext = $_GET['volltext'];
	$result=mysql_query("SELECT * FROM institution WHERE name LIKE '%$volltext%' OR `name_fr` LIKE '%$volltext%' OR `name_it` LIKE '%$volltext%' OR `name_rm` LIKE '%$volltext%' OR `name_en` LIKE '%$volltext%' OR `abkuerzung` LIKE '%$volltext%' OR `abkuerzung_fr` LIKE '%$volltext%' OR `abkuerzung_it` LIKE '%$volltext%' OR `abkuerzung_rm` LIKE '%$volltext%' OR `abkuerzung_en` LIKE '%$volltext%' OR `ort` LIKE '%$volltext%' OR `sammlungsgeschichte` LIKE '%$volltext%' OR `sammlungsgeschichte_fr` LIKE '%$volltext%' OR `sammlungsgeschichte_it` LIKE '%$volltext%' OR `sammlungsgeschichte_rm` LIKE '%$volltext%' OR `sammlungsgeschichte_en` LIKE '%$volltext%' OR `sammlungsbeschreibung` LIKE '%$volltext%' OR `sammlungsbeschreibung_rm` LIKE '%$volltext%' OR `sammlungsbeschreibung_fr` LIKE '%$volltext%' OR `sammlungsbeschreibung_it` LIKE '%$volltext%' OR `sammlungsbeschreibung_en` LIKE '%$volltext%' ORDER BY name Asc");
	$issearch=3;
	//echo "SELECT * FROM fotografen WHERE nachname LIKE '$anf%' ORDER BY  nachname Asc, vorname Asc";
	while($fetch=mysql_fetch_array($result)){
	
		if ($fetch['gesperrt']==1) $fetch['nameclass']='subtitle3x'; else $fetch['nameclass']='subtitle3';
		$def->assign("FETCH",$fetch);
		$def->parse("list.row".((auth_level(USER_WORKER))?'_admin_institution':'_normal_institution'));
	}
	$def->parse("list");
	//$def->out("list");
	$results.=$def->text("list");
	
	
} elseif (isset($_GET["seg"])) {//Suche nach SEG
	testauth();
	$seg = $_GET['seg'];
	$seg = array_map('mysql_real_escape_string', $seg);
 	
	// construct query
	$query = "SELECT * FROM bestand WHERE id IN (SELECT DISTINCT bestand_id FROM bestand_segref WHERE ";
	
	// find out, which fields are filled and construct sql parts
	$q = array();
	(!empty($seg['kont'])? array_push($q, " kontinent_id='".$seg[kont]."'"):NULL);
	(!empty($seg['subk'])? array_push($q, " subk_id='".$seg[subk]."'"):NULL);
	(!empty($seg['region'])? array_push($q, " regionort_id='".$seg[region]."'"):NULL);
	(!empty($seg['ort'])? array_push($q, " regionort_id='".$seg[ort]."'"):NULL);
	(!empty($seg['prov'])? array_push($q, " prov_id='".$seg[prov]."'"):NULL);
	(!empty($seg['ethnie'])? array_push($q, " ethnien_id='".$seg[ethnie]."'"):NULL);
	
	
	// add where clauses to query
	foreach($q as $p) {
		$query .= $p;
		// last element
		($p===end($q)?$query .= ");":$query .= " AND");
	}
	

// 	tabllenkopf
	$def->parse("list.head_bestand");

	$result = mysql_query($query);
 	
	// bestände in liste packen
	while($fetch=mysql_fetch_array($result)){
		
		if ($fetch['gesperrt']==1) $fetch['nameclass']='subtitle3x'; else $fetch['nameclass']='subtitle3';
		$def->assign("FETCH",$fetch);
		$def->parse("list.row".((auth_level(USER_WORKER))?'_admin_bestand':'_normal_bestand'));
		
	}
	$def->parse("list");
	
	$results.=$def->text("list");
	
} else {

	$def->parse("list.head_bestand");
	$results.=$def->text("list");
}	

?>