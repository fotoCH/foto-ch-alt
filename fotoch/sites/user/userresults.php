<?php

$def=new XTemplate ("././templates/list_results.xtpl");
$def->assign("ACTION",$_GET['a']);
$id=$_GET['id'];
$anf=$_GET['anf'];

if (!$anf){
	if (auth_level(USER_WORKER) && !$id){
		$ayax=1;
		$anf='%';
	} else {
		$anf='%';
	}
}

$def->assign("LANG",$_GET['lang']);
$def->assign("SPR",$spr);
$def->assign("title",$spr['useren']);
$def->assign("BEARBEITEN", "[&nbsp;".$spr['bearbeiten']."&nbsp]");
$def->assign("NEU","");

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
			$where.="$key LIKE '%$value%' ";
		}
	}

	$full= (!empty($vars['volltext']));

	$query="SELECT * FROM users WHERE $where ORDER BY name Asc";

	//print_r($vars);

	$result=mysqli_query($sqli, $query);

	if(mysqli_num_rows($result) > 0){
		if(auth_level(USER_WORKER)){
			$def->parse("list.listhead_user");
		}else{
			$def->parse("list.listhead_user");
		}
	}

	while($fetch=mysqli_fetch_array($result)){
		//
		$fetch['nameclass']='subtitle3';

		$def->assign("FETCH",$fetch);
		$def->parse("list.row_user");
	}
	$def->parse("list");
	$results.=$def->text("list");

} else {

	if ($id==''){
		// Select: code

		$result=mysqli_query($sqli, "SELECT * FROM users WHERE username LIKE '$anf%' ORDER BY  username Asc");

		if(auth_level(USER_WORKER)){
			$def->parse("list.listhead_user");
		}else{
			$def->parse("list.listhead_user");
		}

		while($fetch=mysqli_fetch_array($result)){
			$fetch['nameclass']='subtitle3';
				
			$def->assign("FETCH",$fetch);
			//print_r($fetch);
			$def->parse("list.row_user");
			//
		}

		$def->parse("list");
		$results.=$def->text("list");

	} else {


		if ($ayax){
			$def->assign("NEUO","");
			//$def->parse("list.ayax_u");
			$def->parse("list");
			$results.=$def->text("list");
		}


	}
}
?>