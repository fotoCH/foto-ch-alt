<?php

function parseZeitraum($z){
    $zahlen=array("","");
    $cz=0;
    for ($i=0; $i<strlen($z); $i++){
	$czei=$z[$i];
	if (is_numeric($czei)){
	    $zahlen[$cz].=$czei;
	} else {
	    if ((int)$zahlen[$cz]>0){
		$cz++;
	    }
	}
    }
    if ($zahlen[1]=="") $zahlen[1]=$zahlen[0];
//    writeToLog("zr: ".$z." -> ".$zahlen[0].'-'.$zahlen[1]);
    return $zahlen;
}

function photographerMatchesDateQuery($r,$d){
    if (!$d) return true;
    $start=(int)$d[0]+0;
    $ende=(int)$d[1]+0;
    $fstart=(int)substr($r['geburtsdatum'],0,4)+0;
    $fende=(int)substr($r['todesdatum'],0,4)+0;
    if ($fstart>$ende) return false;
    if ($fende>0 && $fende<$start) return false;
    return true;
}

function stockMatchesDateQuery($r,$d){
    if (!$d) return true;
    $start=(int)$d[0]+0;
    $ende=(int)$d[1]+0;
    $zr=parseZeitraum($r['zeitraum']);
    $fstart=(int)substr($zr[0],0,4)+0;
    $fende=(int)substr($zr[0],0,4)+0;
    if ($fstart>0 && $fstart>$ende) return false;
    if ($fende>0 && $fende<$start) return false;
    return true;
}

function exhibitionMatchesDateQuery($r,$d){
    if (!$d) return true;
    $start=(int)$d[0]+0;
    $ende=(int)$d[1]+0;
    $jahr=(int)substr($r['jahr'],0,4)+0;
    if ($jahr>$ende) return false;
    if ($jahr<$start) return false;
    return true;
}

function photoMatchesDateQuery($r,$d){
    if (!$d) return true;
    $start=(int)$d[0]+0;
    $ende=(int)$d[1]+0;
    $zr=parseZeitraum($r['zeitraum']);
    $fstart=(int)substr($zr[0],0,4)+0;
    $fende=(int)substr($zr[0],0,4)+0;
    if ($fstart>0 && $fstart>$ende) return false;
    if ($fende>0 && $fende<$start) return false;
    return true;
}

?>
