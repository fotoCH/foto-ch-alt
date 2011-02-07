<?php

function mabstand($def){  // benutzt in edit und iedit
	$def->parse("bearbeiten.form.tr");
	$def->parse("bearbeiten.form");
}

function gennoedit(&$def, $label, $value){
	genformitem($def,'noedit', $label, $value, '' );
}

function gendatnoedit(&$def, $label, $value){
	genformitem($def,'noedit', $label, formdatesimp($value,0), '' );
}

function genstempel1(&$def, $label,$name,$refid){
	$sql="SELECT * FROM doku_ref JOIN users on `dokuref`.`userid`=`users`.`id` WHERE `dokuref`.id=$refid";
	$result = mysql_query($sql);
	$ref = mysql_fetch_array($result);
	$e['label']=$label;
	$e['name']=$name;

	if ($ref['date']!='0000-00-00' && $ref['date']!=''){
		$e['date']=formdatesimp($ref['date']);
		$e['checked']='checked="checked"';
		$e['user']=$ref['username'];
	} else {
		$e['date']='';
		$e['checked']='';
		$e['user']='';
	}
	$def->assign('E',$e);
	$def->parse("bearbeiten.form.stempel1");
	$def->parse("bearbeiten.form");


}

function genstempel2(&$def, $label,$name,$refid){
	global $spr;
	$sql="SELECT * FROM doku_workflow_ref JOIN users on `doku_workflow_ref`.`userid`=`users`.`id` WHERE `dokuref`.id=$refid";
	$result = mysql_query($sql);
	$ref = mysql_fetch_array($result);
	$e['label']=$label;
	$e['name']=$name;
	$e['level']=1;
	$e['olevel']=1;
	for ($l=1;$l<=6;$l++){
		$e['num']=$l;
		$e['olabel']=$spr['wf_ref_'.$l];
		if ($ref['l'.$l.'date']!='0000-00-00' && $ref['l'.$l.'date']!=''){
			$e['date']=formdatesimp($ref['l'.$l.'date']);
			$e['checked']='checked="checked"';
			$e['user']=$ref['l'.$l.'user'];
		} else {
			$e['date']='';
			$e['checked']='';
			$e['user']='';
		}
		if (($l==2) || ($l==4)){
			$ub=getuserbox($name.'_'.$l.'_userbox',$e['user']);
		}
		$def->assign('userbox',$ub);
		$def->assign('E',$e);
		
		$def->parse("bearbeiten.form.stempel2.rowr.row".(($l==2) || ($l==4)?'2':''));
		$def->parse("bearbeiten.form.stempel2.rowr");

	}
	$def->parse("bearbeiten.form.stempel2");
	$def->parse("bearbeiten.form");


}



function genformitem(&$def, $template, $label, $value, $name ){
	$def->assign("label",$label);
	$def->assign("name",$name);
	$def->assign("value",$value);
	$def->parse("bearbeiten.form.".$template);
	$def->parse("bearbeiten.form");
}
function gencheckitem(&$def, $label, $value, $name ){
	$def->assign("label",$label);
	$def->assign("name",$name);
	$def->assign("value",1);
	$def->assign("checked",($value>0)?'checked="checked" ':'');
	$def->parse("bearbeiten.form.checkfield");
	$def->parse("bearbeiten.form");
}

function gencheckarrayitem(&$def, $label, $list, $name, $list2){
	$def->assign("label", $label);
	foreach($list as $key => $value){
		$def->assign("ovalue", $value);
		$def->assign("olabel",$value);
		$def->assign("inputname", $name);
		if(in_array($value, $list2)){
			//-> checked
			$def->assign("checked","checked");
		}
		else {
			$def->assign("checked","");
		}
		$def->parse("bearbeiten.form.check.box");
	}
	$def->parse("bearbeiten.form.check");
	$def->parse("bearbeiten.form");
}

function gencheckarrayitemtr(&$def, $label, $list, $list_tr, $name, $list2){
	global $language;
	$def->assign("label", $label);
	$c=0;
	foreach($list as $key => $value){
		$def->assign("ovalue", $value);
		if ($language!='de')
		$def->assign("olabel",$list_tr[$c]);
		else
		$def->assign("olabel",$value);
		$def->assign("inputname", $name);
		if(in_array($value, $list2)){
			//-> checked
			$def->assign("checked","checked");
		}
		else {
			$def->assign("checked","");
		}
		$def->parse("bearbeiten.form.check.box");
		$c++;
	}
	$def->parse("bearbeiten.form.check");
	$def->parse("bearbeiten.form");
}


function genselectitem(&$def, $label, $value, $name, $list, $m, $ll, $size){
	$def->assign("label",$label);
	//$def->assign("name",$name);
	$def->assign("name",($m)?$name."[]":$name);
	$def->assign("size",$size);
	if($m){
		foreach($list as $v => $l){
			$def->assign("ovalue",$l);
			$def->assign("olabel",$l);
			if ($m){
				$def->assign("selected",(in_array($l,$value))?'selected="selected" ':'');
				if ($l=="\x85"){
					$def->assign("ovalue",0);
				}
			} else {
				$def->assign("selected",($v==$value)?'selected="selected" ':'');
			}
			$def->assign("multiple",($m)?'multiple="multiple" ':'');
			$def->parse("bearbeiten.form.select.option");
		}
	}else{
		foreach($list as $v => $l){
			$def->assign("ovalue",$v);
			$def->assign("olabel",$l);
			if ($m){
				$def->assign("selected",(in_array($v,$value))?'selected="selected" ':'');
				if ($l=="\x85"){
					$def->assign("ovalue",0);
				}
			} else {
				$def->assign("selected",($v==$value)?'selected="selected" ':'');
			}
			$def->assign("multiple",($m)?'multiple="multiple" ':'');
			$def->parse("bearbeiten.form.select.option");
		}
	}
	if ($ll) $def->parse("bearbeiten.form.select.plink");
	$def->assign("checked",($value>0)?'checked="checked"':'');
	$def->parse("bearbeiten.form.select");
	$def->parse("bearbeiten.form");
}

function genformitemb(&$def, $template, $label, $value, $b ){
	$def->assign("label",$label);
	$def->assign("value",$value);
	$def->parse("berabeiten.form.".$template);
	$def->parse("bearbeiten.form");
}

function subgenformitem(&$def, $template, $label, $value, $name ){
	$def->assign("label",$label);
	$def->assign("name",$name);
	$def->assign("value",$value);
	$def->parse("suchen.form.".$template);
	$def->parse("suchen.form");
}

function subgencheckitem(&$def, $label, $value, $name ){
	$def->assign("label",$label);
	$def->assign("name",$name);
	$def->assign("value",1);
	$def->assign("checked",($value>0)?'checked="checked" ':'');
	$def->parse("suchen.form.checkfield");
	$def->parse("suchen.form");
}

function subgenselectitem(&$def, $label, $value, $name, $list, $m, $ll, $size){
	$def->assign("label",$label);
	$def->assign("name",$name);
	$def->assign("size",$size);
	foreach($list as $v => $l){
		$def->assign("olabel",$l);
		if ($m){
			$def->assign("ovalue",$l);
		} else {
			$def->assign("ovalue",$v);
		}
		if ($m){
			$def->assign("selected",(in_array($v,$value))?'selected="selected" ':'');
			if ($l=="\x85"){
				$def->assign("ovalue",0);
			}
		} else {
			$def->assign("selected",($v==$value)?'selected="selected" ':'');
		}
		$def->assign("multiple",($m)?'multiple="multiple" ':'');
		$def->parse("suchen.form.select.option");
	}
	//if ($m) $def->parse("suchen.form.select.plink");
	$def->assign("checked",($value>0)?'checked="checked"':'');
	$def->parse("suchen.form.select");
	$def->parse("suchen.form");
}




function subgenformitemb(&$def, $template, $label, $value, $b ){
	$def->assign("label",$label);
	$def->assign("value",$value);
	$def->parse("berabeiten.form.".$template);
	$def->parse("suchen.form");
}

function subgensubmit(&$def, $template, $value){
	$def->assign("value",$value);
	$def->parse("suchen.form.".$template);
	$def->parse("suchen.form");
}

function getParam($name,$default=''){
	$p=$_REQUEST[$name];
	return ($p?$p:$default);
}

$wusers=array();

function initwar(){
	global $wusers;
	if (count($wusers)>0) return;
	$wusers[0]='';
	$sql="SELECT * FROM `users` WHERE `level` >= 8 ORDER BY username";
	$result = mysql_query($sql);
	while ($fetch = mysql_fetch_array($result)){
		$wusers[$fetch['id']]=$fetch['username'];
	}
	//print_r($wusers);
	return;
}

function getuserbox($name, $u){ // selectbox mit ausgewähletem user
	initwar();
	global $wusers;
	$o='<select name="'.$name.'" id="'.$name.'" class="edit" >';
	foreach ($wusers as $k=>$v){
           $o.='<option '.($u==$v?'selcted="selscted" ':'').'value="'.$k.'" class="edit">'.$v.'</option>';
      }
     $o.='</select>'; 
	return($o);
}

?>