<?php 
include("searches.php");

$q = getClean('query');

$parts=array('photographer','literature','institution','inventory','exhibition','photo');
$res=array();
foreach ($parts as $p){
	$res[$p.'_results']=call_user_func ('search_'.$p, $q, &$count);
	$res[$p.'_result_count']=$count;
}

jsonout($res);
?>