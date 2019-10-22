<?php

function parse_query($query){
    $squery=explode(' ',$query);
    $ret=array();
    $ov='';
    $err='';
    foreach ($squery as $q){
	switch (substr_count($q, '"')){
	    case 0:if ($ov==''){
		    $ret[]=$q;
		} else {
		    $ov.=' '.$q;
		}
		break;
	    case 1:	if ($ov==''){
		    $ov=$q;
		} else {
		    $ret[]=$ov.' '.$q;
		    $ov='';
		}
		break;
	    case 2:	if ($ov!=''){
		    $err='illegal 2';
		} else {
		    $ret[]=$q;
		}
		break;
	    default: $err='more than 2';
	}
    }
    if ($err!='') sruError("parse_Error: $err");
    return $ret;
}

function parse_value($s){
    $s=str_replace('"', "", $s);
    return(explode(' ',$s));
}

function compact_query($query){
    $ret=array(array(),array());
    $date=false;
    foreach($query as $q){
	switch ($q){
	    case 'Serverchoice':
	    case 'all':
	    case 'AND':
	    case 'isad.date':
		break;
	    case 'WITHIN':
		$date=true;
		break;
	    default: if (!$date){
		    $ret[0]=parse_value($q);
		} else {
		    $ret[1]=parse_value($q);
		}
	    }
	}
    return $ret;
    }
include("fotofunc.inc.php");
include("foto-ch.inc.php");
include("fotocache.inc.php");
include("lang.inc.php");

include('streamsearch.php');

$request=compact_query(parse_query($query));

$results=getStreamResults($request[0],$request[1],$maxrecords);

//print_r(compact_query(parse_query($query)));
//outputXML(sruNoResults());
outputXML($results);
?>
