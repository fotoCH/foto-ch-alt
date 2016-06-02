<?php
//if ($_GET ['photos']!="1")
    $query = "SELECT *, '' as dc_right, '' as image_path FROM arbeitsorte WHERE lat<>0";
//else 
    $query2 = "SELECT *, CONCAT(dcterms_spatial,': ', dc_title) as name, 'fotoquery' as swissname FROM fotos WHERE lat<>0";
    
if ($_GET ['land'] || $_GET['kanton']){
$query.=' AND kanton=\''.getClean('kanton').'\' AND land=\''.getClean('land').'\'';
}

$res=getfromselect($query);
if ($_GET['photo']=='0'){
    $res2=array();
} else {
    $res2=getfromselect($query2);
}

jsonout(array_merge($res,$res2));
?>
