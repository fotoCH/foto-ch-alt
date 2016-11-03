<?php
// gemäss Mail Jocelyne 21.3.2013
$techniken=array('(Licht-?)','(Stereo-)','Ambrotypie','Bromsilberdruck','Daguerreotypie','Druck (Bromure)','Fotografie','Heliogravüre','Lichtdruck','Stereofotografie');


header('Content-type: text/plain; charset=utf-8');

ini_set('display_errors', 1);
error_reporting(E_ALL && ~E_NOTICE);
//require("templates/xtemplate.class.php");
require("../mysql.inc.php");

function getDetailScopeWeb($id){
	$query="http://katalog.burgerbib.ch/detail.aspx?Id=".$id;
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
	$oldv='';
	foreach ($m[1] as $k=>$v){
		$key=$m[1][$k];
		$value=$m[2][$k];

		if (substr($key,-1)==':') $key=substr($key,0,-1);
		if (($key=='') && ($oldv=='Ort')){ 
			
			$c=count($res);
			$res[$c-1][1]=$res[$c-1][1]."\r\n".$value;
			
		} else {
			$res[]=array($key,$value);
			$oldv=$key;
		}
		
		
		if (substr($key,0,12)=='Ansichtsbild'){
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

function checktechnik($t){
	$techniken=array('(Licht-?)','(Stereo-)','Ambrotypie','Bromsilberdruck','Daguerreotypie','Druck (Bromure)','Fotografie','Heliogravüre','Lichtdruck','Stereofotografie');
	foreach ($techniken as $tt){
		if (substr($t,0,strlen($tt))==$tt) return true;
	}
	return false;
}

function putToDB($r){
	global $sqli;
	$s='Burgerbibliothek der Stadt Bern';
	$q="REPLACE INTO bildarchivbu SET ";
	$ok=false;
	$tech='';
	foreach ($r as $k => $v){
		//echo("v0: ".$v[0]." v1: ".$v[1]."vold: ".$oldv."<br />\r\n");
		if ($v[0]=='id') $ok=true;
		
		if ($v[0]!=''){
			$q.="`$v[0]`='".mysqli_real_escape_string ($sqli, $v[1])."', ";
			$all.="$v[0]=".mysqli_real_escape_string ($sqli, $v[1])."\r\n";
		}
		if ($v[0]=='Technik') $tech=$v[1];
		
	}
	$q.="`source`='$s', `all`='".$all."'";
	//echo $q;
	if ($ok && checktechnik($tech)){
		$res=mysqli_query($sqli, $q);
		if ($e=mysqli_error($sqli)){
			echo($e);

			addMissingColums($r);
			$res=mysqli_query($sqli, $q);
		}
	}
}

function addMissingColums($r){
	global $sqli;
	$res=mysqli_query($sqli, 'DESCRIBE bildarchivbu');
	$fields=array();
	while ($fetch=mysqli_fetch_assoc($res)){
		$fields[]=$fetch['Field'];
	}
	foreach ($r as $k => $v){
		$k=$v[0];
		if (!in_array($k,$fields)){
			echo"missing: $k\r\n";
			$q='ALTER TABLE  `bildarchivbu` ADD  `'.$k.'` VARCHAR( 255 ) NOT NULL';
			mysqli_query($sqli, $q);
		}
	}
	//print_r($res);
}

function getSRUSig($ref, &$fetch){

	$query="http://katalog.burgerbib.ch/sru/?operation=searchRetrieve&version=1.2&query=isad.reference=\"".rawurlencode($ref)."\"";
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
	$odir="/home/chrigu/buimage/";
	$url='http://katalog.burgerbib.ch/getimage.aspx?VEID='.$id.'&DEID=10&sqnznr=1&wSIZE=100';
	file_put_contents($odir.$id.'.jpg', file_get_contents($url));
}

//getBild(106716);

function getBilder(){
	global $sqli;
	$q="SELECT id FROM bildarchivbu WHERE id>0 ORDER BY id";
	$res=mysqli_query($sqli, $q);
	while ($fetch=mysqli_fetch_assoc($res)){
		getBild($fetch['id']);
		echo $fetch['id']."\r\n";
		flush();
		ob_flush();
	}
}

//getDetailScopeWeb(167165);
//getDetailScopeWeb(106737);

/*
$r=mysqli_query($sqli, "SELECT id FROM bildarchivbu_o");
while ($f=mysqli_fetch_assoc($r)){
	getDetailScopeWeb($f['id']);
}
*/
$nb=array(102536,102108,102539,103424,105599,106105,106346,112128,106642,112127);
foreach ($nb as $bid){
	//getDetailScopeWeb($bid);
	getBild($bid);
}

//getSRUsig("FN Fo");
//for ($x=106716;$x<108716;$x++){
// getDetailScopeWeb($x);
//} 
//getBilder();
?>