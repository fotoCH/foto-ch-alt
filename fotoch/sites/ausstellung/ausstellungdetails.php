<?php

$def=new XTemplate ("././templates/item_details.xtpl");
$def->assign("ACTION",$_GET['a']);
$def->assign("ID",$_GET['id']);
$id = $_GET['id'];
$lang = $_GET['lang'];
$def->assign("LANG", $_GET['lang']);
$bearbeiten = "[&nbsp;".$spr['bearbeiten']."&nbsp;]";
$def->assign("TITLE", $spr['ausstellung']);
$def->assign("SPR",$spr);

$id=$_GET['id'];
$anf=$_GET['anf'];
$result=mysqli_query($sqli, "SELECT * FROM ausstellung WHERE id=$id");
while($fetch=mysqli_fetch_assoc($result)){
	$fetch=formaus($fetch);
	normfeld($def,'',$fetch['text'].'.');
	unset ($fetch['titel']);
	unset ($fetch['ort']);
	unset ($fetch['jahr']);
	unset ($fetch['text']);
	unset ($fetch['institution']);

	if(auth_level(USER_WORKER)){////////////////////////////// Admincode
		foreach ($fetch as $key=>$value){

			if($value!=''){
				$def->assign("key",$spr[$key]);
				$def->assign("value",$value);
				$def->parse("autodetail.z.autorow");
				$def->parse("autodetail.z");
				abstand($def);
			}
		}

	}else{////////////////////////////// nicht-Admincode

			
		foreach ($fetch as $key=>$value){
			//print "$key=>$value<br>";
			if($key=="id" || $key=="typ" || $key=="bearbeitungsdatum" || $key=="notiz"){
			}else{
				//$key=ucfirst($key);
				if($value!=''){
					$def->assign("key",$spr[$key]);
					$def->assign("value",$value);
					$def->parse("autodetail.z.autorow");
					$def->parse("autodetail.z");
					abstand($def);
				}
			}
		}
	}
}

$result6=mysqli_query($sqli, "SELECT * FROM ausstellung_fotograf WHERE ausstellung_id=$id");

$def->assign("fotografIn",$spr['fotografIn']);
$fotogr=array();
while($fetch6=mysqli_fetch_array($result6)){

	$fo=getfo($fetch6['fotograf_id']);
	$fotogr[$fo['sortn']]=$fo;
}
$foton=array_keys($fotogr);
sort($foton);
//print_r($foton);
foreach ($foton as $k){
	//print_r($fo);
	$fetch6['name']=$fotogr[$k]['namen'];
	$fetch6['fotografen_id']=$fotogr[$k]['fid'];
	//if ($fotogr[$k]['gesperrt']==1) if (auth()) $fetch6['name']='X '.$fetch6['name'];
	$def->assign("g",(auth_level(USER_WORKER) && $fotogr[$k]['gesperrt']==1?'g':''));
	$def->assign("FETCH6",$fetch6);
	if (auth_level(USER_WORKER) || ($fotogr[$k]['gesperrt']==0)) $def->parse("autodetail.z.bestn_2.flink"); else $def->parse("autodetail.z.bestn_2.fnlink");
	$def->parse("autodetail.z.bestn_2");
	$def->parse("autodetail.z");
	$def->assign("fotografIn","");

}
//if(auth_level(USER_WORKER)) $def->assign("BEARBEITEN","rrr<a href=\"./?a=aedit&amp;id=$id&amp;lang=$lang\">sdgsg$bearbeiten</a>");
// wirkungslos
if(auth_level(USER_WORKER)){
	$def->parse("autodetail.z.bearbeiten_ausstellung");
	$def->parse("autodetail.z");
}
$def->parse("autodetail");
$results.=$def->text("autodetail");
?>
