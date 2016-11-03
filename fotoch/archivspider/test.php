<?php
header('Content-type: text/html; charset=utf-8');

ini_set('display_errors', 1);
error_reporting(E_ALL && ~E_NOTICE);
//require("templates/xtemplate.class.php");
require("../mysql.inc.php");

function getDetailScopeWeb($id){
	$query="http://www.query.sta.be.ch/detail.aspx?Id=".$id;
	echo $query; ob_flush();
	$ans=file_get_contents($query);
	$dtable='<table id="ctl00_cphMainArea_tblDetails" class="veDetailTable" cellspacing="0" cellpadding="2" border="0">';
	$p1=strstr($ans,$dtable);
	$p1=substr($p1,strlen($dtable)+1);
	$p1=strstr($p1,'</table>',true);
	putData($p1);
	//$p1=substr($p1,strlen($dtable)+1);	
	//echo "<body><table>".$p1.'</table></body>';
	//$ans=substr($ans,$p1+strlen($dtable)+1);
	//echo $ans; exit
	//return $ret;
}

function putData($t){
	$regex='|<td class="veDetailAttributLabel"[^>]*>(.*)</td><td class="veDetailAttributValue"[^>]*>(.*)</td>|';
	preg_match_all($regex,$t,$m);
	$res=array();
	foreach ($m[1] as $k=>$v){
		$key=$m[1][$k];
		$value=$m[2][$k];
		
		if (substr($key,-1)==':') $key=substr($key,0,-1);
		$res[]=array($key,$value);
		if ($key=='Ansichtsbild'){
			$reg2='/GetImage.aspx\?VEID=(.*)&amp;DE/';
			preg_match($reg2,$value,$m2);
			//print_r($m2);
			$res[]=array('id',$m2[1]);
		}
		//echo("Key: $key Value: $value\r\n");
	}
	//print_r($res);
	putToDB($res);
}

function putToDB($r){
	global $sqli;
	$s='Staatsarchiv des Kantons Bern';
	$q="REPLACE INTO bildarchiv SET ";
	foreach ($r as $k => $v){
		$q.="`$v[0]`='".mysqli_real_escape_string ($sqli, $v[1])."', ";
		$all.="$v[0]=".mysqli_real_escape_string ($sqli, $v[1])."\r\n";
	}
	$q.="`source`='$s', `all`='".$all."'";
	$res=mysqli_query($sqli, $q);
	if ($e=mysqli_error($sqli)){
		echo($e);

		addMissingColums($r);
	}
}

function addMissingColums($r){
	global $sqli;
	$res=mysqli_query($sqli, 'DESCRIBE bildarchiv');
	$fields=array();
	while ($fetch=mysqli_fetch_assoc($res)){
		$fields[]=$fetch['Field'];
	}
	foreach ($r as $k => $v){
		$k=$v[0];
		if (!in_array($k,$fields)){
			echo"missing: $k\r\n";
			$q='ALTER TABLE  `bildarchiv` ADD  `'.$k.'` VARCHAR( 255 ) NOT NULL';
			mysqli_query($sqli, $q);
		}
	}
	//print_r($res);
}

function getSRUSig($ref, &$fetch){
	
	$query="http://www.query.sta.be.ch/sru/?operation=searchRetrieve&version=1.2&query=isad.reference=\"".rawurlencode($ref)."\"";
	echo $query;
	$ans=file_get_contents($query);
	echo $ans; exit;
	$dom = new DomDocument();
	$dom->load($query);
	$nr=$dom->getElementsByTagName("record");
	$c=0;
	echo ($nr->length. "Ergebnisse: \n");
	foreach($nr as $r){
		$a=parseRDF($r);
		foreach ($a as $k=>$v){
			$a[$k]['rank']=rank($v,$fetch);
		}
		print_r($a);
		$ret[]=$a;
	}

	//echo dom_dump($dom);
	//echo dom_dump($nr);

	//echo $ans;
	//$a=parseRDF($ans);
	return $ret;
}

function getBild($id){
$odir="/home/chrigu/bsaimage/";
$url='http://www.query.sta.be.ch/GetImage.aspx?VEID='.$id.'&DEID=10&sqnznr=1&wSIZE=100';
file_put_contents($odir.$id.'.jpg', file_get_contents($url));
}

//getBild(90913);

function getBilder(){
global $sqli;
$q="SELECT id FROM bildarchiv WHERE id>0 ORDER BY id";
$res=mysqli_query($sqli, $q);
while ($fetch=mysqli_fetch_assoc($res)){
 getBild($fetch['id']);
 echo $fetch['id']."\r\n";
 flush();
 ob_flush();
}
}
//getSRUsig("FN Fo");
/*for ($x=90913;$x<100000;$x++){
getDetailScopeWeb($x);
} */
getBilder();
?>