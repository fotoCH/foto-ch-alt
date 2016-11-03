<?php

global $debug;

function getnames(&$names){
	$result=mysqli_query($sqli, "SELECT fotografen.id, namen.nachname, namen.vorname, namen.namenszusatz  FROM fotografen INNER JOIN namen ON fotografen.id=namen.fotografen_id ORDER BY namen.nachname Asc, namen.vorname Asc");
	$names=array();
	while($fetch=mysqli_fetch_array($result)){
		$id=$fetch['id'];
		$names[$id]=$fetch;
	}

}

function mysqli_real_escape_string_callback($a)
{
	global $sqli;
	return mysqli_real_escape_string($sqli, $a);
}

$def=new XTemplate ("././templates/list_results.xtpl");
$def->assign("ACTION",$_GET['a']);
$def->assign("ID",$_GET['id']);
$def->assign("LANG",$_GET['lang']);
$def->assign("SPR",$spr);

$lang = $_GET['lang'];
$id=$_GET['id'];
$anf=$_GET['anf'];
$volltext = mysqli_real_escape_string($sqli, $_GET['volltext']);



$def->assign("title",$spr['bestand']);

$def->assign("BEARBEITEN","[&nbsp;".$spr['bearbeiten']."&nbsp;]");
	
	if ($anf!=''){
	
// 			testauth();
			$def->parse("list.head_bestand");
			// Select: code
			$result=mysqli_query($sqli, "SELECT * FROM bestand WHERE name LIKE '$anf%' ORDER BY  name Asc");
			$issearch=2;
			//echo "SELECT * FROM fotografen WHERE nachname LIKE '$anf%' ORDER BY  nachname Asc, vorname Asc";
			while($fetch=mysqli_fetch_array($result)){
		
				if ($fetch['gesperrt']==1) $fetch['nameclass']='subtitle3x'; else $fetch['nameclass']='subtitle3';
				$def->assign("FETCH",$fetch);
				$def->parse("list.row".((auth_level(USER_WORKER))?'_admin_bestand':'_normal_bestand'));
			}
		
			$def->parse("list");
			//$def->out("list");
			$results.=$def->text("list");
		
	} else {
	
		if ($_GET['submitbutton']=="suchen") {	//&& !$ayax){
			$params['volltext'] = $volltext;
			$params['name'] = mysqli_real_escape_string($sqli, $_GET['name']);
			$params['bestandsbeschreibung'] = mysqli_real_escape_string($sqli, $_GET['bestandsbeschreibung']);
			$params['bildgattungen'] = $_GET['bildgattungen'];
			array_walk($params['bildgattungen'], 'mysqli_real_escape_string_callback');
// 			var_dump($params);
			
// 			testauth();
			$def->parse("list.head_bestand");
			// Select: code
			//$volltext = $_GET['volltext'];
			$sql="SELECT * FROM bestand WHERE
				( name LIKE '%$volltext%' OR 
				`zeitraum` LIKE '%$volltext%' OR 
				`umfang` LIKE '%$volltext%' OR 
				`erschliessungsgrad` LIKE '%$volltext%' OR 
				`weiteres` LIKE '%$volltext%' OR 
				`bildgattungen` LIKE '%$volltext%' OR 
				`bestandsbeschreibung` LIKE '%$volltext%' OR 
				`link_extern` LIKE '%$volltext%' OR 
				`signatur` LIKE '%$volltext%' OR 
				`copyright` LIKE '%$volltext%'
				";
			if(auth_level(USER_WORKER))
				$sql .= " OR `notiz` LIKE '%$volltext%'";
			
				$sql.=" ) AND
				`name` LIKE '%$params[name]%' AND 
				`bestandsbeschreibung` LIKE '%$params[bestandsbeschreibung]%' "; 
				foreach($params['bildgattungen'] as $gattung) {
					$sql .= " AND find_in_set('$gattung',`bildgattungen`) ";
				}
			
			$sql .= "ORDER BY name Asc";
// 			echo $sql;
			$result = mysqli_query($sqli, $sql);
			$issearch=3;
			//echo "SELECT * FROM fotografen WHERE nachname LIKE '$anf%' ORDER BY  nachname Asc, vorname Asc";
			while($fetch=mysqli_fetch_array($result)){
				if ($fetch['gesperrt']==1) $fetch['nameclass']='subtitle3x'; else $fetch['nameclass']='subtitle3';
				$def->assign("FETCH",$fetch);
				$def->parse("list.row".((auth_level(USER_WORKER))?'_admin_bestand':'_normal_bestand'));
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