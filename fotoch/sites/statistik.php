<?php

include("./fotofunc.inc.php");
include("./backend.inc.php");
include("./php-user-agent/phpUserAgent.php");

$userAgent = new phpUserAgent();
print_r($userAgent);
$userAgent->getBrowserName();    // firefox
$userAgent->getBrowserVersion();   // 3.6
$userAgent->getOperatingSystem();  // linux
$userAgent->getEngine();           // gecko

function getheaderrow($ar,$first,$last){
	$res="<tr><td class=\"th\">$first</td>";
	foreach ($ar as $a){
		$res.="<td class=\"thr\">$a</td>";
	}
	$res.="<td class=\"thr\">$last</td></tr>";
	return($res);
}

function getvaluerow($t,$ar,$first,$last){
	$res="<tr><td class=\"th\">$first</td>";
	foreach ($t as $t){
		$res.="<td class=\"tv\">".($ar[$t]>0?formstunden($ar[$t]):'&nbsp;')."</td>";
	}
	$res.="<td class=\"tv\">".formstunden($last)."</td></tr>";
	return($res);
}

$page=new XTemplate ("./templates/contents.xtpl");

		datumtest();
		if ($_REQUEST['vondatum']){
			$w=' AND datum>=\''.rformday($_REQUEST['vondatum']).'\'';
			$krit2=' '.$_REQUEST['vondatum'].' -';
		}
		if ($_REQUEST['bisdatum']){
			$w.=' AND datum<=\''.rformday($_REQUEST['bisdatum']).'\'';
			if (!$krit2) $krit2.=' -';
			$krit2.=' '.$_REQUEST['bisdatum'];
		}


$page->assign("ITEM","<h1>".$spr['statistik']."</h1>");
$page->assign("DATA",$spr['kontakt_content']);
$page->parse("contents.hand_kon_imp_par_site");

$out.=$page->text("contents.hand_kon_imp_par_site");
$sql = "SELECT COUNT(id),useragent,level,isbot FROM `log_sessions` WHERE 1 GROUP BY useragent,level ORDER BY isbot,level,useragent LIMIT 0, 30 ";
?>