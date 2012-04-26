<?php

include_once 'fotofunc.inc.php';

function getbildg(&$fetch,$cb,$cbc){
	global $spade;
	global $spatr;
	$bgs=$spatr['bildgattungen_uebersetzungen2'];
	$bgd=$spade['bildgattungen_uebersetzungen2']; 
	$c=0;
	$l=0;
	$out='';
	$line='';
	while ($c<=count($bgs)+3){
		if ($c>=count($bgs)){
			$line.='<td></td>';
			$l++;
		} else {
		if (in_array($c,array(33,39,40))){
			$line.='<td colspan="2">'.(strpos($fetch['bildgattungen_set'],$bgd[$c])>0?$cbc:$cb).' '.$bgs[$c].'</td>';
			$l++; $l++;
		} else {
			$line.='<td>'.(strpos($fetch['bildgattungen_set'],$bgd[$c])>0?$cbc:$cb).' '.$bgs[$c].'</td>';
			$l++; 
		}
		}
		if ($l % 5 == 0){
			$out.='<tr>'.$line.'</tr>';
			$line='';
		}
		$c++;
	}
	if ($_GET['lang']!='de'){
		//$fetch['bildgattungen_set']=setuebersetzungen('bildgattungen_uebersetzungen',$fetch['bildgattungen_set']);
	}
	return($out);
}

testauthedit();
$def=new XTemplate ("./templates/export.html");
$id=$_GET['id'];
$result=mysql_query("SELECT * FROM institution WHERE institution.id=".$id);
$i=mysql_fetch_array($result);
foreach (array('de','fr','it','rm','en') as $l){
	if ($i['territoriumszugegoerigkeit']==$l){
		$i['t_'.$l]=$cbc='☒';
	} else {
	$i['t_'.$l]=$cbc='☐';
	}
}
$def->assign('I',$i);
$cb='<input type="checkbox" checked="checked"/>';
//$cbc='☑☒';
$cbc='☒';
$cb='☐';
$def->assign('checkbox',$cb);
$def->assign("SPR", $spr);
$def->parse("content.i1");
$def->parse("content.i2");
$def->parse("content.i3");
$def->parse("content.i4");
$def->parse("content.i5");
$def->parse("content.i6");
$def->parse("content.i7");
$def->parse("content.i8");
$def->parse("content.i9");
$def->assign('bildg',getbildg($i,$cb,$cbc));
$def->parse("content.i10");
$def->parse("content.i11");
$result7=mysql_query("SELECT literatur_institution.institution_id, literatur_institution.id AS if_id, literatur.*
		FROM literatur_institution INNER JOIN literatur ON literatur_institution.literatur_id = literatur.id
		WHERE literatur_institution.institution_id=$id ORDER BY literatur.verfasser_name");
while($fetch7=mysql_fetch_array($result7)){
	$fetch7['if_typ']=$lit;
	formlite($fetch7);
	$fetch7['Literatur']=$lit;
	$def->assign("l",$fetch7);
	$def->parse("content.i12.row");
	$lit='';
}

$def->parse("content.i12");


$aus='';
$result8=mysql_query("SELECT ausstellung_institution.institution_id, ausstellung_institution.id AS af_id, ausstellung.*
		FROM ausstellung_institution INNER JOIN ausstellung ON ausstellung_institution.ausstellung_id = ausstellung.id
		WHERE ausstellung_institution.institution_id=$id ORDER BY ausstellung.typ, af_id");
while($fetch8=mysql_fetch_array($result8)){
	$typeHasChanged = false;
	if ($fetch8['typ']!=$aus){
		if($aus!=''){
			$typeHasChanged = true;
		}
		$aus=$fetch8['typ'];
		$fetch8['Ausstellung']=($aus=='E'?$spr['einzelaustellungen']:$spr['gruppenaustellungen']);
		//if ($aus=='G') abstand($def);
	} else {
		$fetch8['Ausstellung']='';
	}
	
	$fetch8=formaus($fetch8);
	$def->assign("a",$fetch8);
	$def->parse("content.i13.row");
}


$def->parse("content.i13");
$def->parse("content");
$out.=$def->text("content");
?>