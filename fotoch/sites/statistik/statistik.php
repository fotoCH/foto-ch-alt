<?php

include("./fotofunc.inc.php");
include("./backend.inc.php");

function getheaderrow($ar,$first,$last){
	$res="<tr><td align=\"right\" class=\"th\">$first</td>";
	foreach ($ar as $a => $l){
		$res.="<td align=\"right\" class=\"thr\">$l</td>";
	}
	//$res.="<td class=\"thr\">$last</td></tr>";
	return($res);
}

function getvaluerow($t,$ar,$first,$last){
	$res="<tr><td align=\"right\" class=\"th\">$first</td>";
	foreach ($t as $a => $l){
		if (is_array($ar[$a])){
			$res.="<td align=\"right\" class=\"tv\">".$ar[$a]['total'].'&nbsp;'."</td>";
		} else {
			$res.="<td align=\"right\" class=\"tv\">".$ar[$a].'&nbsp;'."</td>";
		}
	}
	//$res.="<td class=\"tv\">".$last."</td></tr>";
	return($res);
}

$logcookie=$_COOKIE['nolog'];

if ($_GET['nolog']==1){
	setcookie('nolog','1',time()+60*60*24*365*5,'/'); // 5 years!
	$logcookie=1;
}

if ($_GET['nolog']==2){
	setcookie('nolog','1',time()-60*60,'/'); // delete cookie
	unset($logcookie);
}


$page=new XTemplate ("./templates/contents.xtpl");

if ($_REQUEST['submitseiten'] || $_REQUEST['submitbesucher']){

	$s='COUNT(id) AS count';
	$sp[]='count';
	$g='';
	$o='';
	if ($_REQUEST['submitbesucher']){
		//print_r($_REQUEST);
		if ($_REQUEST['zeitauswahl']){
			//$sp[]='time';
			$zp[]='time';
			$s.=($s?',':'');
			if ($_REQUEST['zeitauswahl']!='MONTH'){
				$s.=$_REQUEST['zeitauswahl']."(start) AS time";
				$g.=($g?',':'');
				$g.=$_REQUEST['zeitauswahl']."(start)";
				$o.=($o?',':'');
				$o.=$_REQUEST['zeitauswahl']."(start)";
			} else {
				$s.="EXTRACT(YEAR_MONTH FROM start) AS time";
				$g.=($g?',':'');
				$g.="EXTRACT(YEAR_MONTH FROM start)";
				$o.=($o?',':'');
				$o.="EXTRACT(YEAR_MONTH FROM start)";
			}

		}
		if ($_REQUEST['browserwahl']){
			$s.=($s?',':'');
			$s.=$_REQUEST['browserwahl'];
			$g.=($g?',':'');
			$g.=$_REQUEST['browserwahl'];
			$o.=($o?',':'');
			$o.=$_REQUEST['browserwahl'];
			$zp[]=$_REQUEST['browserwahl'];
		}

		$s.=($s?',':'');
		$s.="AVG(count) as acount";
		$sp[]='acount';
		$s.=($s?',':'');
		$s.="AVG(seconds) AS aseconds";
		$sp[]='acseconds';
		if ($_REQUEST['botsa']){
			$w.=($w?' AND ':'');
			$w.='isbot=0';
		}
		if ($_REQUEST['level8a']){
			$w.=($w?' AND ':'');
			$w.='level<8';
		}
		datumtest();
		if ($_REQUEST['vondatum']){
			$w.=($w?' AND ':'');
			$w.='start>=\''.rformday($_REQUEST['vondatum']).'\'';
			$krit2=' '.$_REQUEST['vondatum'].' -';
		}
		if ($_REQUEST['bisdatum']){
			$w.=($w?' AND ':'');
			$w.='datum<=\''.rformday($_REQUEST['bisdatum']).'\'';
			if (!$krit2) $krit2.=' -';
			$krit2.=' '.$_REQUEST['bisdatum'];
		}

		if ($g){
			$g='GROUP BY '.$g;
		}

		if ($o){
			$o='ORDER BY '.$o;
		}
		if ($w){
			$w='WHERE '.$w;
		}
		$ot=-1;
		$sql="SELECT $s FROM log_sessions $w $g $o";
		$res=mysql_query($sql);
		$result=array();
		
		while ($f=mysql_fetch_array($res)){
			$time=$f['time'];
			if ($ot!=$time){
				$times[]=$time;
				$ot=$time;
			}
			$c=$f['count'];
			if (!$result[$time]){
				$result[$time]=array();
			}
			
			$bws=split(',',$_REQUEST['browserwahl']);
			
			$browser='';
			
			foreach ($bws as $b){
				$browser.=($browser?' ':'');
				$browser.=$f[trim($b)];
			}
			if ($_REQUEST['browserwahl'] && $browser==''){
				$browser='unbekannt';
			}
			if (!$result[$time][$browser]){
				$result[$time][$browser]=array('count'=>0,'aseconds'=>0,'acount'=>0);
				if (!in_array($browser, $zeil)){
					$zeil[]=$browser;
				}
			}
			
			
			$result[$time][$browser]['count']=$c;
			$result[$time][$browser]['aseconds']=$f['aseconds'];
			$result[$time][$browser]['acount']=$f['acount'];
			$result[$time]['total']['count']+=$c;
		}

		if (count($zeil)==1) $zeil=array();
		if (!$_REQUEST['browserwahl']){
			$zeil[]='t';
			
		} else {
			$zeil[]='total';
		}
		$sp=array('count'=>'anzahl','aseconds'=>'durchschn. Zeit [s]','acount'=>'durchschn. Anzahl Seiten');
		//print_r($result);
		
	} else { //pages
		//print_r($_REQUEST);
		if ($_REQUEST['zeitauswahl']){
			//$sp[]='time';
			$zp[]='time';
			$s.=($s?',':'');
			if ($_REQUEST['zeitauswahl']!='MONTH'){
				$s.=$_REQUEST['zeitauswahl']."(timestamp) AS time";
				$g.=($g?',':'');
				$g.=$_REQUEST['zeitauswahl']."(timestamp)";
				$o.=($o?',':'');
				$o.=$_REQUEST['zeitauswahl']."(timestamp)";
			} else {
				$s.="EXTRACT(YEAR_MONTH FROM timestamp) AS time";
				$g.=($g?',':'');
				$g.="EXTRACT(YEAR_MONTH FROM timestamp)";
				$o.=($o?',':'');
				$o.="EXTRACT(YEAR_MONTH FROM timestamp)";
			}

		}
		if (true){  // nach Kategorie
			$s.=($s?',':'');
			$s.='kategorie';
			$g.=($g?',':'');
			$g.='kategorie';
			$o.=($o?',':'');
			$o.='kategorie';
			$zp[]='kategorie';
		}


		if ($_REQUEST['seplevel']){
			$s.=($s?',':'');
			$s.='level';
			$g.=($g?',':'');
			$g.='level';
			$o.=($o?',':'');
			$o.='level';
			$zp[]=$_REQUEST['level'];
		}

		if ($_REQUEST['bots']){
			$w.=($w?' AND ':'');
			$w.='isbot=0';
		}
		if ($_REQUEST['level8']){
			$w.=($w?' AND ':'');
			$w.='level<8';
		}
		datumtest();
		if ($_REQUEST['vondatum']){
			$w.=($w?' AND ':'');
			$w.='start>=\''.rformday($_REQUEST['vondatum']).'\'';
			$krit2=' '.$_REQUEST['vondatum'].' -';
		}
		if ($_REQUEST['bisdatum']){
			$w.=($w?' AND ':'');
			$w.='datum<=\''.rformday($_REQUEST['bisdatum']).'\'';
			if (!$krit2) $krit2.=' -';
			$krit2.=' '.$_REQUEST['bisdatum'];
		}

		if ($g){
			$g='GROUP BY '.$g;
		}

		if ($o){
			$o='ORDER BY '.$o;
		}
		if ($w){
			$w='WHERE '.$w;
		}

		$sql="SELECT $s FROM log_pages $w $g $o";
		//echo $sql;
		$res=mysql_query($sql);

		$result=array();
		$ot=-1;
		while ($f=mysql_fetch_array($res)){
			$time=$f['time'];
			if ($ot!=$time){
				$times[]=$time;
				$ot=$time;
			}
			
			$c=$f['count'];
			if (!$result[$time]){
				$result[$time]=array('L'=>array(),'R'=>array(),'A'=>array(),'total'=>array());
			}
			$kat=$f['kategorie'];
				
			$level=$f['level'];
			if (!$result[$time][$kat][$level]){
				$result[$time][$kat][$level]=0;
			}
			if (!$result[$time][$kat]['total']){
				$result[$time][$kat]['total']=0;
			}
			if (!$result[$time]['total']['total']){
				$result[$time]['total']['total']=0;
			}
			
			$result[$time][$kat][$level]+=$c;
			$result[$time][$kat]['total']+=$c;
			$result[$time]['total'][$level]+=$c;
			$result[$time]['total']['total']+=$c;
		}
		$sp=array('0','1','2','4','8','total');
		if ($_REQUEST['seplevel']){
			$zeil=array('L','R','A','total');
			$sp=array('0'=>'0','1'=>'1','2'=>'2','4'=>'4','8'=>'8','total'=>'total');
		} else {
			$zeil=array('');
			$sp=array('L'=>'L','R'=>'R','A'=>'A','total'=>'total');
		}
			
	}
	
	$table="";
	
	foreach ($times as $t){
		$table.=$t.'<br /><table border="1">';
		$table.=getheaderrow($sp, '', '');
		
		foreach ($zeil as $z){
			if ($z){
				if ($z=='t'){
					$table.=getvaluerow($sp, $result[$t][''], 'total', '');
				} else {
					$table.=getvaluerow($sp, $result[$t][$z], $z, '');
				}
			} else {
				$table.=getvaluerow($sp, $result[$t], 'total', '');
			}
		}
		$table.='</table><br />';
	}
	$out=$table;
	//if ()

} else {

	$zeitauswahl=array('DATE'=>'pro Tag','MONTH' =>'pro Monat','YEAR'=>'pro Jahr',''=>'total');
	$def=new XTemplate ("./templates/edit.xtpl");
	$def->assign("TITEL", 'statistik');
	$def->assign("SPR", $spr);
	$def->parse("bearbeiten.bearbeiten_head_statistik");
	$def->assign("LEGEND", "<b>Zeitoptionen</b><br/>");
	$def->parse("bearbeiten.form.fieldset_start");
	$def->parse("bearbeiten.form");
	$def->parse("bearbeiten.form.start");
	$def->parse("bearbeiten.form");



	genselectitem($def, 'Zeitgruppierung', '', 'zeitauswahl', $zeitauswahl, "", "", "");
	genformitem($def, 'textfield', 'von', '', 'vondatum');
	genformitem($def, 'textfield', 'bis', '', 'bisdatum');


	$def->parse("bearbeiten.form.tend");
	$def->parse("bearbeiten.form");

	$def->parse("bearbeiten.form.fieldset_end");
	$def->parse("bearbeiten.form");

	mabstand($def);

	$def->assign("LEGEND", "<b>Besuche</b><br/>");
	$def->parse("bearbeiten.form.fieldset_start");
	$def->parse("bearbeiten.form");
	$def->parse("bearbeiten.form.start");
	$def->parse("bearbeiten.form");
	$browserwahl=array('browser'=>'nach Browser','browser, version' =>'nach Browserversion','os'=>'nach Betriebssystem',''=>'total');
	genselectitem($def, 'Browser', '', 'browserwahl', $browserwahl, "", "", "");

	gencheckitem($def, 'level >8 ausblenden', 1, 'level8a');
	gencheckitem($def, 'Bots ausblenden', 1, 'botsa');
	genformitem($def, 'submit', 'Besucherbericht', 'senden', 'submitbesucher');
	$def->parse("bearbeiten.form.tend");
	$def->parse("bearbeiten.form");

	$def->parse("bearbeiten.form.fieldset_end");
	$def->parse("bearbeiten.form");

	mabstand($def);

	$def->assign("LEGEND", "<b>Seitenaufrufe</b><br/>");
	$def->parse("bearbeiten.form.fieldset_start");
	$def->parse("bearbeiten.form");
	$def->parse("bearbeiten.form.start");
	$def->parse("bearbeiten.form");
	gencheckitem($def, 'nach level trennen', 0, 'seplevel');

	gencheckitem($def, 'level >8 ausblenden', 1, 'level8');
	gencheckitem($def, 'Bots ausblenden', 1, 'bots');
	genformitem($def, 'submit', 'Seitenbericht', 'senden', 'submitseiten');
	$def->parse("bearbeiten.form.tend");
	$def->parse("bearbeiten.form");

	$def->parse("bearbeiten.form.fieldset_end");
	$def->parse("bearbeiten.form");



	$def->parse("bearbeiten");
	$out.=$def->text("bearbeiten")."<br />";


	$page->parse('contents.statistik.log'.($logcookie?2:1));
	$page->parse('contents.statistik');'
	$out.=$page->text("contents.statistik");
	$sql = "SELECT COUNT(id),useragent,level,isbot FROM `log_sessions` WHERE 1 GROUP BY useragent,level ORDER BY isbot,level,useragent LIMIT 0, 30 ";
	$sql = "SELECT COUNT(id),browser,level,isbot FROM `log_sessions` WHERE 1 GROUP BY browser,level ORDER BY isbot,level,useragent LIMIT 0, 30 ";
	$sql = "SELECT COUNT(id),browser,version,level,isbot FROM `log_sessions` WHERE 1 GROUP BY browser,version,level ORDER BY isbot,level,browser,version";
}
?>