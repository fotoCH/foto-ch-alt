<?php 
include("searches.php");

$q = getClean('query');

$parts=array('photographer','literature','institution','inventory','exhibition','photo');
$res=array();
foreach ($parts as $p){
	$results=call_user_func ('search_'.$p, $q);
	$res[$p.'_results']=$results;
	$res[$p.'_result_count']=count($results);
}

jsonout($res);
?>