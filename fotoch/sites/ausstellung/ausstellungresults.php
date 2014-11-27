<?php
//include("fotofunc.inc.php");

$def=new XTemplate ("././templates/list_results.xtpl");
$def->assign("ACTION",$_GET['a']);
$def->assign("ID",$_GET['id']);
$def->assign("LANG", $_GET['lang']);
$lang = $_GET['lang'];

$def->assign("SPR",$spr);

$neuereintrag = $spr['neuereintrag'];
$def->assign("BEARBEITEN", "&nbsp;&nbsp;[&nbsp;".$spr['bearbeiten']."&nbsp;]");
$def->assign("title", $spr['ausstellung']);


$id=$_GET['id'];
$anf=$_GET['anf'];
$volltext = $_GET['volltext'];

$admin=(auth_level(USER_WORKER)?'_admin':'');

$def->parse("list.head_ausstellung".$admin);


if ($anf=='andere'){
	$result=mysql_query("SELECT * FROM ausstellung WHERE (`titel` <'A') OR (`titel`>'zz') ORDER BY  titel Asc");
}
else {
	if($anf!=''){
		$result=mysql_query("SELECT * FROM ausstellung WHERE titel LIKE '$anf%' ORDER BY  titel Asc");
		$issearch=2;
	}
	else {
		if($volltext!='') {
			if (auth_level(USER_GUEST_READER)){
				$w1="(titel LIKE '%$volltext%' OR institution LIKE '%$volltext%' OR ort LIKE '%$volltext%' OR `jahr`  LIKE '%$volltext%' OR `institution`  LIKE '%$volltext%' OR `notiz` LIKE '%$volltext%')";
				$sql = "SELECT * FROM ausstellung WHERE  ORDER BY jahr DESC";
			}
			else {
				$w1="titel LIKE '%$volltext%' OR institution LIKE '%$volltext%' OR ort LIKE '%$volltext%' OR `jahr`  LIKE '%$volltext%' OR `institution`  LIKE '%$volltext%'";
				
			}
		}
		else {
			//echo "fehler";
		}
		$where='';
		foreach (array('institution','ort','jahr') as $key){
			if (!empty($_GET[$key])){
				if (!empty($where)){
					$where.=" AND ";
				}
				$where.="$key LIKE '%".$_GET[$key]."%' ";
			}
		}
		
		if ($w1){
			if (!empty($where)){
				$where.=" AND ";
			}
			$where.=$w1;
		}
		$sql = "SELECT * FROM ausstellung WHERE $where ORDER BY jahr DESC";
		$issearch=3;
		$result=mysql_query($sql);
	}
}
while($fetch=mysql_fetch_array($result)){

	
	if (trim($fetch['titel'])==''){
		$fetch['titel']='&ndash;';  // platzhalter für link
	}
	$def->assign("FETCH",$fetch);
	$def->parse("list.row_ausstellung".$admin);
}
$def->parse("list");
$results.=$def->text("list");
?>