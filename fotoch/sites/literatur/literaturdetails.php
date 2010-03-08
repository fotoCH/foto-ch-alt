<?php
	$def=new XTemplate ("././templates/item_details.xtpl");
	$def->assign("PHP_SELF",$_SERVER['PHP_SELF']);
	$def->assign("ACTION",$_GET['a']);
	$def->assign("ID",$_GET['id']);
	$id = $_GET['id'];
	$lang = $_GET['lang'];
	$def->assign("LANG", $_GET['lang']);
	$def->assign("TITLE", $spr['literatur']);
	$def->assign("SPR",$spr);
	//$bearbeiten = "[&nbsp;".$spr['bearbeiten']."&nbsp;]";
	
	$result=mysql_query("SELECT * FROM literatur WHERE id=$id");
	//if(mysql_num_rows($result)>0){ echo "es" ;} else {echo "no";}
	if(auth_level(USER_WORKER)){////////////////////////////// Admincode
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
				//print "$key=>$value<br />";
				if($key=="ida" || $key=="lexikon" || $key=="kontaktperson" || $key=="telefon" || $key=="fax" || $key=="email" || $key=="notiz"){
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
	$result6=mysql_query("SELECT * FROM literatur_fotograf WHERE literatur_id=$id");
	
	$def->assign("fotografIn",$spr['fotografIn']);
	$fotogr=array();
	while($fetch6=mysql_fetch_array($result6)){
		
		$fo=getfo($fetch6['fotografen_id']);
		$fotogr[$fo['sortn']]=$fo;
	}
	
	$foton=array_keys($fotogr);
	sort($foton);
	
	foreach ($foton as $k){
		//print_r($fo);
		$fetch6['name']=$fotogr[$k]['namen'];
		$fetch6['fotografen_id']=$fotogr[$k]['fid'];
		//if ($fotogr[$k]['gesperrt']==1) if (auth()) $fetch6['name']='X '.$fetch6['name'];
		$def->assign("g",(auth_level(USER_WORKER) && $fotogr[$k]['gesperrt']==1?'g':''));
		$def->assign("FETCH6",$fetch6);
		if (auth_level(USER_WORKER) || ($fotogr[$k]['gesperrt']==0)) $def->parse("autodetail.z.bestn_2.flink"); else 		$def->parse("autodetail.z.bestn_2.fnlink");
		$def->parse("autodetail.z.bestn_2");
		$def->parse("autodetail.z");
		$def->assign("Fotograf","");
	}
	//$def->assign("BEARBEITEN","<a href=\"?a=ledit&amp;id=$id&amp;lang=$lang\">$bearbeiten</a>");
	$def->parse("autodetail.z.bearbeiten_literatur");
	$def->parse("autodetail.z");
	$def->parse("autodetail");
	$results.=$def->text("autodetail");
?>
