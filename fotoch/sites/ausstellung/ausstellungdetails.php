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
	$result=mysql_query("SELECT * FROM ausstellung WHERE id=$id");
	//print_r($result);
	if(auth()){////////////////////////////// Admincode
		while($fetch=mysql_fetch_array($result, MYSQL_ASSOC)){
			
			foreach ($fetch as $key=>$value){
				
				if($value!=''){
					$def->assign("key",$spr[$key]);
					$def->assign("value",$value);
					$def->parse("autodetail.z.autorow");
					$def->parse("autodetail.z");
					abstand($def);
				}
			}
		}
	}else{////////////////////////////// nicht-Admincode
		while($fetch=mysql_fetch_array($result, MYSQL_ASSOC)){
			
			foreach ($fetch as $key=>$value){
				//print "$key=>$value<br>";
				if($key=="id" || $key=="lexikon" || $key=="kontaktperson" || $key=="xtelefon" || $key=="xfax" || $key=="xemail" || $key=="notiz"){
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
	
	$result6=mysql_query("SELECT * FROM ausstellung_fotograf WHERE ausstellung_id=$id");
	
	$def->assign("fotografIn",$spr['fotografIn']);
	$fotogr=array();
	while($fetch6=mysql_fetch_array($result6)){
		
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
		$def->assign("g",(auth() && $fotogr[$k]['gesperrt']==1?'g':''));
		$def->assign("FETCH6",$fetch6);
		if (auth() || ($fotogr[$k]['gesperrt']==0)) $def->parse("autodetail.z.bestn_2.flink"); else 		$def->parse("autodetail.z.bestn_2.fnlink");
		$def->parse("autodetail.z.bestn_2");
		$def->parse("autodetail.z");
		$def->assign("fotografIn","");
	
	}
	if(auth()) $def->assign("BEARBEITEN","<a href=\"./?a=aedit&amp;id=$id&amp;lang=$lang\">$bearbeiten</a>");
	//abstand($def);
	$def->parse("autodetail.z.bearbeiten_ausstellung");
	$def->parse("autodetail.z");
	$def->parse("autodetail");
	$results.=$def->text("autodetail");
?>
