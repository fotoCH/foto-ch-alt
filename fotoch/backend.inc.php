<?php
require_once("./users.inc.php");

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

function genstempel1(&$def, $label,$name,&$fetch,$disabled=false){
	$e['label']=$label;
	$e['name']=$name;

	if ($fetch[$name.'_date']!='0000-00-00' && $fetch[$name.'_date']!=''){
		$e['date']=formdatesimp($fetch[$name.'_date']);
		$e['checked']='checked="checked"';
		$e['user']=$fetch[$name.'_user'];
		$e['usern']=getusername($fetch[$name.'_user']);
	} else {
		$e['date']='';
		$e['checked']='';
		$e['user']='';
		$e['usern']='';
	}
	if( $disabled ) {
		$def->assign('DISABLED','disabled="1"');
	} else {
		$def->assign('DISABLED','');
	}
	$def->assign('E',$e);
	$def->parse("bearbeiten.form.stempel1");
	$def->parse("bearbeiten.form");


}

function genstempel_multi(&$def, $label,$name,&$fetch){
	$newfetch = unserialize($fetch[$name]);
	$size = sizeof($newfetch)/2;
	for( $i = 0; $i <= $size; $i++ ) {
		genstempel1($def,$label,$name."_".$i,$newfetch,$i < $size-1);
	}
}


function genstempel2(&$def, $label,$name,&$fetch,$start){
	global $spr;
	$e['label']=$label;
	$e['name']=$name;
	$e['start']=$start;
	$e['level']=$start-1;
	$e['olevel']=$start-1;
	for ($l=$start;$l<=6;$l++){
		$e['num']=$l;
		$e['olabel']=$spr['wf_ref_'.$l];
		if ($fetch[$name.'_l'.$l.'_date']!='0000-00-00' && $fetch[$name.'_l'.$l.'_date']!=''){
			$e['date']=formdatesimp($fetch[$name.'_l'.$l.'_date']);
			$e['checked']='checked="checked"';
			$e['user']=$fetch[$name.'_l'.$l.'_user'];
			$e['usern']=getusername($fetch[$name.'_l'.$l.'_user']);
			$e['level']=$l;
			$e['olevel']=$l;
		} else {
			$e['date']='';
			$e['checked']='';
			$e['user']='';
			$e['usern']='';
		}
		if (($l==1) || ($l==3)){
			$ub=getuserbox($name.'_'.$l.'_userbox',$e['usern']);
		}
		$def->assign('userbox',$ub);
		$def->assign('E',$e);
		
		$def->parse("bearbeiten.form.stempel2.rowr.row".(($l==1) || ($l==3)?'2':''));
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

function genradioarrayitem(&$def, $label, $list, $name, $list2){
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
		$def->parse("bearbeiten.form.radio.box");
	}
	$def->parse("bearbeiten.form.radio");
	$def->parse("bearbeiten.form");
}


function gencheckarrayitemKv(&$def, $label, $list, $name, $list2){  // werteliste mit key/value
	$def->assign("label", $label);
	foreach($list as $key => $value){
		$def->assign("ovalue", $key);
		$def->assign("olabel",$value);
		$def->assign("inputname", $name);
		if(in_array($key, $list2)){
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

function getHistEntry($wo, $was, $werte){
	return date("Y-m-d ").$_SESSION['s_uid']." [".$wo."] ".$was.": ".$werte;
}

?>