<?php
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

function subgenselectitem(&$def, $label, $value, $name, $list, $m, $ll, $size, $onchange=''){
	if($name!="geschlecht"){
		$label.="*";
	}
	$def->assign("label",$label."");
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
	if ($m) $def->parse("suchen.form.select.plink");
	$def->assign("checked",($value>0)?'checked="checked"':'');
    $def->assign("onchange",($onchange)?" onchange=$onchange":'');
    $def->parse("suchen.form.select");
	$def->parse("suchen.form");
}

function subgenselectitemtr(&$def, $label, $value, $name, $list, $list_tr, $m, $ll, $size){  // mit uebersetzung
	global $spade;
	global $spatr;
	$def->assign("label",$label);
	$def->assign("name",$name);
	$def->assign("size",$size);
	$c=0;
	foreach($list as $v => $l){
		$def->assign("olabel",$list_tr[$c]);
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
		$c++;
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