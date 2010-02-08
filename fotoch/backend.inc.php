<?php

function mabstand($def){  // benutzt in edit und iedit
	$def->parse("bearbeiten.form.tr");
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
	$def->assign("label", $label);
	$c=0;
	foreach($list as $key => $value){
		$def->assign("ovalue", $value);
		$def->assign("olabel",$list_tr[$c]);
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
?>