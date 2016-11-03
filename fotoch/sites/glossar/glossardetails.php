<?php
//include("fotofunc.inc.php");

$def=new XTemplate ("././templates/item_details.xtpl");
$def->assign("ACTION",$_GET['a']);
$def->assign("ID",$_GET['id']);
$def->assign("LANG", $_GET['lang']);

$def->assign("SPR",$spr);

$id=$_GET['id'];
$anf=$_GET['anf'];
$def->assign("TITLE", $spr['glossar']);
$def->assign("BEARBEITEN", "[&nbsp;".$spr['bearbeiten']."&nbsp;]");

	$result=mysqli_query($sqli, "SELECT * FROM glossar WHERE id=$id");


	if($_SESSION[s_uid]=="fotobe"){////////////////////////////// Admincode
		while($fetch=mysqli_fetch_assoc($result)){
			$def->assign("PHP_SELF",$_SERVER['PHP_SELF']);
			$def->assign("ACTION",$_GET['a']);
			$def->assign("ID",$_GET['id']);

			foreach ($fetch as $key=>$value){
				//print "$key=>$value<br />";
				//$key=ucfirst($key);
				if($value!=''){
					$def->assign("key",$spr[$key]);
					$def->assign("value",$value);
					$def->parse("autodetail.z.autorow");
					$def->parse("autodetail.z");
				}
			}
		}
		$def->parse("autodetail.z.bearbeiten_glossar");
		$def->parse("autodetail.z");
	}else{////////////////////////////// nicht-Admincode
		while($fetch=mysqli_fetch_assoc($result)){
			//print_r($fetch);
			$def->assign("PHP_SELF",$_SERVER['PHP_SELF']);
			$def->assign("ACTION",$_GET['a']);
			$def->assign("ID",$_GET['id']);

			foreach ($fetch as $key=>$value){
				//print "$key=>$value<br />";
				if($key=="id" || $key=="notiz"){
				}else{
					//$key=ucfirst($key);
					if($value!=''){
						$def->assign("key",$spr[$key]);
						$def->assign("value",$value);
						$def->parse("autodetail.z.autorow");
						$def->parse("autodetail.z");
					}
				}
			}
		}
	}

	$def->parse("autodetail");
	$results.=$def->text("autodetail");
?>
