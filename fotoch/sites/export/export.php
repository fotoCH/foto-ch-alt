<?php
testauthedit();
$def=new XTemplate ("./templates/export.xtpl");
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
$cbc='☑☒';
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
$def->parse("content.i10");
$def->parse("content");
$out.=$def->text("content");
?>