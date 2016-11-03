<?php
require("../mysql.inc.php");

if (!extension_loaded('yaz')) {
	throw( new JsonRpcError( 1, "no yzlib" ) );
	print "Sorry, 'yaz.so' isn't loaded....";
	exit;
}


header('Content-type: text/plain; charset=utf-8');
$config=array(  'title' => 'rero',
		'yaz_connect_string' => 'virtua.z3950.rero.ch:3950',
		//'yaz_connect_options' => array('user'=>'999','password'=>'abc'),
		'yaz_connect_options' => '',
		'yaz_record_syntax' => 'MARC21',
		//'yaz_record_syntax' => 'USmarc',
);


$id = yaz_connect($config['yaz_connect_string'],$config['yaz_connect_options']);
yaz_syntax($id, $config['yaz_record_syntax']);

//$query = '@attr 1=4 Courgevaux,\ château';
$query = '@attr 1=4 [image\ fixe]';
//$query = '@attr 1=4 de\ Courgevaux';
yaz_search($id, 'rpn', $query);
$options=array('timeout'=>'145');
yaz_wait($options);
$error = yaz_error($id);
if (!empty($error)) {
	die( "<p>Error: $error</p><br>" );
	//return false;
} else {

	$hits = yaz_hits($id); echo $hits."\r\n";
	if ($hits == '0') {
		return false;
	}
}
//yaz_present($id);
//if ($hits>1200) $hits=1200;
for ($i=9009; $i<=10855; $i++){
	print "$i:------------------------------------------\r\n";
	$rec = yaz_record($id, $i,'string');
	$rec_arr=yaz_record($id, $i,'array');
	if (testimg($rec_arr)){
		if (testbcu($rec_arr)){
			register($rec_arr,$rec);
		}
	}
	//print_r($rec_arr);
	print "------------------------------------------\r\n";
}

function testimg(&$arr){
	foreach ($arr as $a){
		if (substr($a[0],0,7)=='(3,856)'){
			return true;
		} else {
			if ((substr($a[0],3,3)+0)>856)
				return false;
		}
	}
	return false;
}

function testbcu(&$arr){
	foreach ($arr as $a){
		if (substr($a[0],0,7)=='(3,040)'){
			if ($a[1]=='RERO frbcuc'){
				return true;
			}
		} else {
			if ((substr($a[0],3,3)+0)>40)
				return false;
		}
	}
	return false;
}

function z3950parse(&$a){
	//echo("xxx:"); print_r($a); echo("uuu");
	if (!$a[1]) return false;
	$s1=substr($a[0],3,3);
	$s2=trim(substr($a[0],10,2));
	$s3=trim(substr($a[0],16,1));
	$s=$s1;
	if ($s2) $s1.='_'.$s2;
	if ($s3) $s1.='_'.$s3;
	$res=array($s1,$s,$a[1]);
	return $res;
}

function register(&$ar, $recr){
	$fields=array('040','100','245','260','300','500','700','856','981');
	$res=array();
	$res['all']=$recr;
	foreach ($ar as $a){
		$v=z3950parse($a);
		if ($v){
			if (in_array($v[1],$fields)){
				if ($res[$v[0]]){
					$res[$v[0]].="\r\n".$v[2];
				} else {
					$res[$v[0]].=$v[2];
				}
			}
		}
	}
	putToDB($res);
	return false;
}

function putToDB($r){
	global $sqli;
	$s='BCU';
	$q="REPLACE INTO bildarchivbcu SET ";
	
	foreach ($r as $k => $v){
	
		if ($k!=''){
			$q.="`$k`='".mysqli_real_escape_string ($sqli, $v)."', ";
		}
	}
	$q.="`source`='$s'";
	echo $q;
	$res=mysqli_query($sqli, $q);
	if ($e=mysqli_error($sqli)){
		echo($e);
		addMissingColums($r);
		$res=mysqli_query($sqli, $q);
	}
}

function addMissingColums($r){
	global $sqli;
	$res=mysqli_query($sqli, 'DESCRIBE bildarchivbcu');
	$fields=array();
	while ($fetch=mysqli_fetch_assoc($res)){
		$fields[]=$fetch['Field'];
	}
	foreach ($r as $k => $v){
		//$k=$v[0];
		if (!in_array($k,$fields)){
			echo"missing: $k\r\n";
			$q='ALTER TABLE  `bildarchivbcu` ADD  `'.$k.'` VARCHAR( 255 ) NOT NULL';
			mysqli_query($sqli, $q);
		}
	}
	//print_r($res);
}



?>