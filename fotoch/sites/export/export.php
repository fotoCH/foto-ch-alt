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

function getbildgb(&$fetch,$cb,$cbc){
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
				$line.='<td colspan="2">'.(strpos($fetch['bildgattungen'],$bgd[$c])>0?$cbc:$cb).' '.$bgs[$c].'</td>';
				$l++; $l++;
			} else {
				$line.='<td>'.(strpos($fetch['bildgattungen'],$bgd[$c])>0?$cbc:$cb).' '.$bgs[$c].'</td>';
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

$result6=mysql_query("SELECT * FROM bestand WHERE inst_id=$id ORDER BY nachlass DESC, name ASC");


while($fetch6=mysql_fetch_array($result6)){
	if ($fetch6['nachlass']==1){
		$fetch6['nja']=$cbc;
		$fetch6['nnein']=$cb;
	} else {
		$fetch6['nja']=$cb;
		$fetch6['nnein']=$cbc;
	}

	$def->assign('bildgb',getbildgb($fetch6,$cb,$cbc));
	$def->assign("B",$fetch6);

	$def->parse("content.b.b1");
	$def->parse("content.b.b2");
	$def->parse("content.b.b3");
	$def->parse("content.b.b4");
	$def->parse("content.b.b5");
	$def->parse("content.b.b6");
	$def->parse("content.b.b7");
	$def->parse("content.b.b8");
	$def->parse("content.b.b9");

	$fotogr=array();
	$result8=mysql_query("SELECT * FROM bestand_fotograf WHERE bestand_id=".$fetch6['id']);
	while($fetch8=mysql_fetch_array($result8)){
		//print_r($fetch8);
		//if ($fetch6['institution_id']>0){
		if ($fetch8['namen_id']){
			$fo=getfon($fetch8['namen_id']);
				
		} else {
			$fo=getfo($fetch8['fotografen_id']);
		}
		$fotogr[$fo['sortn']]=$fo;
		//	$fotogr['fid']=$fetch6['fotografen_id'];
		}
		//print_r($fotogr);
		$foton=array_keys($fotogr);
		sort($foton);
		//print_r($foton);
		foreach ($foton as $k){
			//print_r($fo);
			$fetch8['name']=$fotogr[$k]['namen'];
			$fetch8['nachname']=$fotogr[$k]['nachname'];
			$fetch8['vorname']=$fotogr[$k]['vorname'];
			$fetch8['lebensdaten']=$fotogr[$k]['lebensdaten'];
			$fetch8['fotografen_id']=$fotogr[$k]['fid'];

			$def->assign("F",$fetch8);

			$def->parse("content.b.b10.row");

		}


		$def->parse("content.b.b10");
		$def->parse("content.b.b11");

		$def->parse("content.b");

	}


	//$def->assign('bildgb',getbildgb("",$cb,$cbc));
	//$def->assign("B",array());

	$def->parse("content.bc.b1");
	$def->parse("content.bc.b2");
	$def->parse("content.bc.b3");
	$def->parse("content.bc.b4");
	$def->parse("content.bc.b5");
	$def->parse("content.bc.b6");
	$def->parse("content.bc.b7");
	$def->parse("content.bc.b8");
	$def->parse("content.bc.b9");


	$def->parse("content.bc.b10");
	$def->parse("content.bc.b11");

	$def->parse("content.bc");


	$def->parse("content");
	$out.=$def->text("content");
	?>