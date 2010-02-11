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

$lang = $_GET['lang'];
$id=$_GET['id'];
$anf=$_GET['anf'];
$volltext = $_GET['volltext'];

$def->assign("TITLE",getLangContent("sprache",$_GET['lang'],"bestand"));
$def->assign("NAME", getLangContent("sprache",$_GET['lang'],"name"));
$def->assign("ID", getLangContent("sprache",$_GET['lang'],"id"));
$def->assign("INSTITUTION", getLangContent("sprache", $_GET['lang'], "institution"));
$def->assign("BEARBEITEN","[&nbsp;".getLangContent("sprache", $_GET['lang'], "bearbeiten")."&nbsp;]");
	
	if ($anf!=''){
	
			testauth();
			$def->parse("list.head_bestand");
			// Select: code
			$result=mysql_query("SELECT * FROM bestand WHERE name LIKE '$anf%' ORDER BY  name Asc");
		
			//echo "SELECT * FROM fotografen WHERE nachname LIKE '$anf%' ORDER BY  nachname Asc, vorname Asc";
			while($fetch=mysql_fetch_array($result)){
		
				if ($fetch['gesperrt']==1) $fetch['nameclass']='subtitle3x'; else $fetch['nameclass']='subtitle3';
				$def->assign("FETCH",$fetch);
				$def->parse("list.row".(($_SESSION['s_uid']=="fotobe")?'_admin_bestand':'_normal_bestand'));
			}
		
			$def->parse("list");
			//$def->out("list");
			$results.=$def->text("list");
		
	} else {
	
		if ($volltext !='') {	//&& !$ayax){
			
			testauth();
			$def->parse("list.head_bestand");
			// Select: code
			//$volltext = $_GET['volltext'];
			$result=mysql_query("SELECT * FROM bestand WHERE name LIKE '%$volltext%' ORDER BY name Asc");
		
			//echo "SELECT * FROM fotografen WHERE nachname LIKE '$anf%' ORDER BY  nachname Asc, vorname Asc";
			while($fetch=mysql_fetch_array($result)){
		
				if ($fetch['gesperrt']==1) $fetch['nameclass']='subtitle3x'; else $fetch['nameclass']='subtitle3';
				$def->assign("FETCH",$fetch);
				$def->parse("list.row".(($_SESSION['s_uid']=="fotobe")?'_admin_bestand':'_normal_bestand'));
			}
		
			$def->parse("list");
			//$def->out("list");
			$results.=$def->text("list");
			
		} else {
		
			$def->parse("list.head_bestand");
			$results.=$def->text("list");
		}	
	}

?>