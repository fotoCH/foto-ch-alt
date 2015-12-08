<?php

$query = "SELECT *, namen.fotografen_id as fid FROM arbeitsperioden JOIN namen ON arbeitsperioden.fotografen_id=namen.fotografen_id WHERE arbeitsort_id=".$_GET['id']. ' ORDER BY von ASC';
$res2=array();
$res=getfromselect($query);
foreach ($res as $f){
	

if ($f['von'].$f['bis']!=''){
	$f['um_vonf']=$f['um_von']==0?'':$spr['um'].' ';
	$f['um_bisf']=$f['um_bis']==0?'':$spr['um'].' ';
} else {
	$f['um_vonf']='';
	$f['um_bisf']='';
}

$res2[]=array('id'=>$f['fid'], 'name'=>$f['vorname'].' '.$f['nachname'],'periode'=>$f['um_vonf'].$f['von'].' - '.$f['um_bisf'].$f['bis']);
}
jsonout($res2);
?>
