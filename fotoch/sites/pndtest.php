<?php

include ("./mysql.inc.php");
include ("./fotofunc.inc.php");
echo "abc";
	$result=mysql_query("SELECT namen.*, fotografen.*, namen.id as nid, `fotografen`.id as fid  FROM namen JOIN fotografen ON `fotografen`.id=`namen`.fotografen_id WHERE namen.nachname LIKE '%' AND pnd_status<1 AND (pnd='' OR ISNULL(pnd)) ORDER BY namen.id Asc");
	while ($fetch=mysql_fetch_array($result)){
		$co++;
		echo $fetch['nid'];
		$a=getPNDfromName($fetch['nid']);
		$b=count($a). " Ergebnisse:\n";
		$c=0;
		$maxr=-100;
		$maxc=0;
		$maxp=-1;
		$ranks=array();
		$pnds=array();
		foreach( $a as $e){
			foreach ( $e as $k=>$v)
			$b.=$k."=>";
			$b.=print_r($v,TRUE)."\n";
			$c++;
			$ranks[]=$v['rank'];
			if ($v['rank']>$maxr){
				$maxr=$v['rank'];
				$maxc=1;
				$maxp=$k;
			} else if ($v['rank']==$maxr){
				$maxc++;
			}
			$pnds[]=$k;
		}
		$pnd='';
		if ($c==0){
			$status=99;   // kein ergebnis
		}
		if ($c==1){ // genau ein ergebnis
			$status=89;
			if ($maxr>=10){
				$status=81;
				$pnd=$pnds[0];
			}
			if ($maxr>=5){
				$status=82;
				$pnd=$pnds[0];
			}
			if ($maxr>=0){
				$status=88;
				$pnd=$pnds[0];
			}
		}
		if ($c>=10){
			$status=79;
		} else	if (($c>1) && ($maxc==1)){
			$status=69;
			if ($maxr>=10){
				$status=61;
				$pnd=$maxp;
			}
			if ($maxr>=5){
				$status=62;
				$pnd=$maxp;
			}
			if ($maxr>=0){
				$status=68;
				$pnd=$maxp;
			}
			
		}
		
		if ($pnd){
			$b='gesetzt: '.$pnd."\n".$b;
			$gesetzt++;
		}
		$sql="UPDATE fotografen SET pnd_status=$status, pnd_answer='".mysql_real_escape_string(utf8_decode($b)).'\''.($pnd?', pnd=\''.$pnd.'\'':'')." WHERE id=".$fetch['fid']. " LIMIT 1";
		echo $sql;
		mysql_query($sql);
	}

echo ("neu gesetzt: ".$gesetzt. " von ".$co);

?>