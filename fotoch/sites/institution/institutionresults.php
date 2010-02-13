<?php

$def=new XTemplate ("././templates/list_results.xtpl");
$def->assign("ACTION",$_GET['a']);
$def->assign("ID",$_GET['id']);
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
$def->assign("LANG",$_GET['lang']);
$def->assign("TITLE",getLangContent("sprache",$_GET['lang'],"institutionen"));
$def->assign("NAME",getLangContent("sprache",$_GET['lang'],"name"));
$def->assign("ORT",getLangContent("sprache",$_GET['lang'],"ort"));
$def->assign("ID",getLangContent("sprache",$_GET['lang'],"id"));
$def->assign("BEARBEITEN", "[&nbsp;".getLangContent("sprache",$_GET['lang'],"bearbeiten")."&nbsp]");
$def->assign("NEU","");

if ($_GET['submitbutton']!=""){
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
			if($key == 'name'){
				$where .= "$key LIKE '%$value%' OR abkuerzung LIKE '%$value%'";
			}
			else{
				$where.="$key LIKE '%$value%' ";
			}
		}
	}
	
	$full= (!empty($vars['volltext']));
	
	$query="SELECT * FROM institution WHERE $where ORDER BY name Asc";
	//print_r($vars);

	if ($full){
		$query="SELECT * FROM institution WHERE MATCH (zugang_zur_sammlung,sammlungsbeschreibung,sammlungsgeschichte,abkuerzung) AGAINST ('".$vars['volltext']."') OR name LIKE '%".$vars['volltext']."%' OR abkuerzung LIKE '%".$vars['volltext']."%' OR ort LIKE '%".$vars['volltext']."%' ORDER BY name";
	}
	
	$result=mysql_query($query);
	//echo $query;
	if(mysql_num_rows($result) > 0){
		if(auth()){
			$def->parse("list.listhead_admin_institution");
		}else{
			$def->parse("list.listhead_normal_institution");
		}
	}
	
	while($fetch=mysql_fetch_array($result)){
		//
		$fetch['nameclass']='subtitle3';
		if ($fetch['autorin']!=''){
			$fetch['nameclass']='subtitle3bio';
		} else {
			$fetch['nameclass']='subtitle3';
		}
		if(auth()) if ($fetch['gesperrt']==1) $fetch['nameclass']='subtitle3x';
		if ($fetch['abkuerzung']) $fetch['abkuerzung']='('.$fetch['abkuerzung'].')';
		//
		
		$def->assign("FETCH",$fetch);
		if(auth()){
			$def->parse("list.row_admin_institution");
		}else{
			if ($fetch['gesperrt']==0) $def->parse("list.row_normal_institution");
		}
	}
	$def->parse("list");
	$results.=$def->text("list");
	
} 

else { 
	if ($id=='' && !$ayax){
		// Select: code
		if(auth()){
			$result=mysql_query("SELECT * FROM institution WHERE name LIKE '$anf%' ORDER BY  name Asc");
		} else {
			$result=mysql_query("SELECT DISTINCT * FROM institution WHERE (name LIKE '$anf%') AND (gesperrt=0) ORDER BY  name Asc");
		}
		
		if(auth()){
			$def->parse("list.listhead_admin_institution");
		}else{
			$def->parse("list.listhead_normal_institution");
		}
	
		while($fetch=mysql_fetch_array($result)){
			$fetch['nameclass']='subtitle3';
			if ($fetch['autorin']!=''){
				$fetch['nameclass']='subtitle3bio';
			} else {
				$fetch['nameclass']='subtitle3';
			}
			if(auth()) if ($fetch['gesperrt']==1) $fetch['nameclass']='subtitle3x';
			if ($fetch['abkuerzung']) $fetch['abkuerzung']='('.$fetch['abkuerzung'].')';
			$def->assign("FETCH",$fetch);
			//print_r($fetch);
			$def->parse("list.row".(($_SESSION['s_uid']=="fotobe")?'_admin_institution':'_normal_institution'));
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