<?php

$def=new XTemplate ("././templates/list_results.xtpl");
$def->assign("ACTION",$_GET['a']);
$id=$_GET['id'];
$anf=$_GET['anf'];

if (!$anf){
	if (auth_level(USER_WORKER) && !$id){
		$ayax=1;
	} else {
		$anf='A';
	}
}

$def->assign("LANG",$_GET['lang']);
$def->assign("SPR",$spr);
$def->assign("title",$spr['institutionen']);
$def->assign("BEARBEITEN", "[&nbsp;".$spr['bearbeiten']."&nbsp]");
$def->assign("NEU","");
//SELECT `id`, `territoriumszugegoerigkeit`, CASE `territoriumszugegoerigkeit` WHEN 'de' THEN name WHEN 'fr' THEN name_fr WHEN 'it' THEN name_it WHEN 'rm' THEN name_rm END as uname FROM `institution` WHERE CASE `territoriumszugegoerigkeit` WHEN 'de' THEN name WHEN 'fr' THEN name_fr WHEN 'it' THEN name_it WHEN 'rm' THEN name_rm END LIKE 'E%' ORDER BY uname	
$namecase="CASE `territoriumszugegoerigkeit` WHEN 'de' THEN name WHEN 'fr' THEN name_fr WHEN 'it' THEN name_it WHEN 'rm' THEN name_rm END";
$abkcase ="CASE `territoriumszugegoerigkeit` WHEN 'de' THEN abkuerzung WHEN 'fr' THEN abkuerzung_fr WHEN 'it' THEN abkuerzung_it WHEN 'rm' THEN abkuerzung_rm END";
if ($_GET['submitbutton']!=""){
	$issearch=3;

	$vars=array();
	$vars=$_GET;
	unset($vars['a']);
	unset($vars['submitbutton']);
	unset($vars['lang']);
	unset($vars['mod']);
	
	foreach ($vars as $key=>$value){
		if (!empty($vars[$key])){
			if (!empty($where)){
				$where.=" AND ";
			}
			
			if($key == 'sammlungsgeschichte'){
				$where .= "sammlungsgeschichte LIKE '%$value%' OR sammlungsbeschreibung LIKE '%$value%' OR ";
				$where .= "sammlungsgeschichte_fr LIKE '%$value%' OR sammlungsbeschreibung_fr LIKE '%$value%' OR ";
				$where .= "sammlungsgeschichte_it LIKE '%$value%' OR sammlungsbeschreibung_it LIKE '%$value%' OR ";
				$where .= "sammlungsgeschichte_rm LIKE '%$value%' OR sammlungsbeschreibung_rm LIKE '%$value%' OR ";
				$where .= "sammlungsgeschichte_en LIKE '%$value%' OR sammlungsbeschreibung_en LIKE '%$value%'";
			}
			elseif($key == 'bildgattungen'){
				$where .= "(";
				foreach($value as $bildgattung) {
					$where .= "find_in_set('$bildgattung',`bildgattungen_set`) AND ";
				}
				$where = substr($where, 0 , -4);
				$where .= ")";
// 				var_dump($where);
			}
			elseif($key == 'name'){
				$where .= "name LIKE '%$value%' OR abkuerzung LIKE '%$value%' OR ";
				$where .= "name_fr LIKE '%$value%' OR abkuerzung_fr LIKE '%$value%' OR ";
				$where .= "name_it LIKE '%$value%' OR abkuerzung_it LIKE '%$value%' OR ";
				$where .= "name_rm LIKE '%$value%' OR abkuerzung_rm LIKE '%$value%' OR ";
				$where .= "name_en LIKE '%$value%' OR abkuerzung_en LIKE '%$value%'";
			}
			elseif($key == 'kanton') {
				$where .= "kanton LIKE '%" . $value . "%'";
			}
			else{
				$where.="$key LIKE '%$value%' ";
			}
		}
	}
	
	$full= (!empty($vars['volltext']));
	//print_r($vars);
	$query="SELECT *,".$namecase." as name,".$abkcase." as abkuerzung FROM institution WHERE $where ORDER BY ".$namecase;
// 	var_dump($query);
	
	if ($full){
		$query="SELECT *,".$namecase." as name,".$abkcase." as abkuerzung FROM institution WHERE MATCH (zugang_zur_sammlung,sammlungsbeschreibung,sammlungsgeschichte,abkuerzung) AGAINST ('".$vars['volltext']."') OR name LIKE '%".$vars['volltext']."%' OR name_fr LIKE '%".$vars['volltext']."%' OR name_it LIKE '%".$vars['volltext']."%' OR name_rm LIKE '%".$vars['volltext']."%' OR name_en LIKE '%".$vars['volltext']."%' OR abkuerzung LIKE '%".$vars['volltext']."%' OR abkuerzung_fr LIKE '%".$vars['volltext']."%' OR abkuerzung_it LIKE '%".$vars['volltext']."%' OR abkuerzung_rm LIKE '%".$vars['volltext']."%' OR abkuerzung_en LIKE '%".$vars['volltext']."%' OR ort LIKE '%".$vars['volltext']."%' ORDER BY ".$namecase;
	}
	//echo $query;
	$result=mysqli_query($sqli, $query);
	
	if(mysqli_num_rows($result) > 0){
		if(auth_level(USER_WORKER)){
			$def->parse("list.listhead_admin_institution");
		}else{
			$def->parse("list.listhead_normal_institution");
		}
	}
	
	while($fetch=mysqli_fetch_array($result)){
		//
		$fetch['nameclass']='subtitle3';
		if ($fetch['autorin']!=''){
			$fetch['nameclass']='subtitle3bio';
		} else {
			$fetch['nameclass']='subtitle3';
		}
		if(auth_level(USER_GUEST_READER_PARTNER) && $fetch['gesperrt']==1) $fetch['nameclass']='subtitle3x';
		if ($fetch['abkuerzung']) $fetch['abkuerzung']='('.$fetch['abkuerzung'].')';
		
		
		$def->assign("FETCH",$fetch);
		if(auth_level(USER_WORKER)){
			$def->parse("list.row_admin_institution");
		}else{
			if ($fetch['gesperrt']==0) $def->parse("list.row_normal_institution");
		}
	}
	$def->parse("list");
	$results.=$def->text("list");	

} else { 
	
	if ($id=='' && !$ayax){
		$issearch=2;
		// Select: code
		if(auth_level(USER_GUEST_READER_PARTNER)){
			$result=mysqli_query($sqli, "SELECT *,".$namecase." as name,".$abkcase." as abkuerzung FROM institution WHERE $namecase LIKE '$anf%' ORDER BY ".$namecase);
		} else {
			$result=mysqli_query($sqli, "SELECT *,".$namecase." as name,".$abkcase." as abkuerzung FROM institution WHERE ($namecase LIKE '$anf%') AND (gesperrt=0) ORDER BY ".$namecase);
		}
		//echo $mysqli_error($sqli);
		if(auth_level(USER_WORKER)){
			$def->parse("list.listhead_admin_institution");
		}else{
			$def->parse("list.listhead_normal_institution");
		}
//	echo $mysqli_error($sqli);
		while($fetch=mysqli_fetch_array($result)){
			$fetch['nameclass']='subtitle3';
			if ($fetch['autorin']!=''){
				$fetch['nameclass']='subtitle3bio';
			} else {
				$fetch['nameclass']='subtitle3';
			}
			if(auth_level(USER_GUEST_READER_PARTNER)) if ($fetch['gesperrt']==1) $fetch['nameclass']='subtitle3x';
			if ($fetch['abkuerzung']) $fetch['abkuerzung']='('.$fetch['abkuerzung'].')';
			$def->assign("FETCH",$fetch);
			//print_r($fetch);
			$def->parse("list.row".((auth_level(USER_WORKER))?'_admin_institution':'_normal_institution'));
			//$def->parse("list.row_normal");
		}
	
		$def->parse("list");
		$results.=$def->text("list");
	
	} else {
	
		
		if ($ayax){
			$def->assign("NEUO","");
			$def->parse("list.ayax");
			$def->parse("list");
			$results.=$def->text("list");
		} 
	
	
	}
}
?>