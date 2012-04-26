<?php
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
$result=mysql_query("SELECT * FROM institution WHERE institution.id=".$_GET['id']);
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
$def->parse("content.i12");
$def->parse("content.i13");
$def->parse("content");
$out.=$def->text("content");
?>