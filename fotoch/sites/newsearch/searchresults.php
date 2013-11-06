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
$volltext = $_GET['volltext'];



$def->assign("title","<span id=\"bestandsliste\">".$spr['bestand']."</span><a href=\"".$_SERVER['REQUEST_URI']."#top\">&uarr;</a>");

$def->assign("BEARBEITEN","[&nbsp;".$spr['bearbeiten']."&nbsp;]");

// volltextsuche in bestand und institution
if ($volltext !='') {
	
	testauth();
	//tabllenkopf
	$def->parse("list.head_bestand");
	
	// Bestände auslesen
	$result=mysql_query("SELECT * FROM bestand WHERE name LIKE '%$volltext%' OR `zeitraum` LIKE '%$volltext%' OR `umfang` LIKE '%$volltext%' OR `erschliessungsgrad` LIKE '%$volltext%' OR `weiteres` LIKE '%$volltext%' OR `bildgattungen` LIKE '%$volltext%' OR `bestandsbeschreibung` LIKE '%$volltext%' OR `link_extern` LIKE '%$volltext%' OR `signatur` LIKE '%$volltext%' OR `copyright` LIKE '%$volltext%' OR `notiz` LIKE '%$volltext%' ORDER BY name Asc");
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
	$result=mysql_query("SELECT * FROM institution WHERE name LIKE '%$volltext%' OR `name_fr` LIKE '%$volltext%' OR `name_it` LIKE '%$volltext%' ORDER BY name Asc");
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
	
	
} else {

	$def->parse("list.head_bestand");
	$results.=$def->text("list");
}	

?>