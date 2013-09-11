<?php
include("mysql.inc.php");
mysql_select_db("foto-ch_test");
if(isset($_GET['a'])) {
	$a = htmlentities($_GET['a']);
	$q = strtolower(htmlentities($_GET["term"]));
	if (!$q) return;
	// $a is always the imput field to be filled
	// $f is (if given) the info we have to fill it
	// i.e. $a=kont $f=ethnie means:
	// fill the kont-input with things, that match the ethnie given in $f
	switch($a) {
		
		//autocomplete the ethnie input
		case "ethnie":
			$sql = "SELECT id, name_de FROM ethnie WHERE name_de LIKE '".$q."%'";
			$rsd = mysql_query($sql);
			$array = array();
			while($rs = mysql_fetch_array($rsd)) {
				$array[$rs['id']]=$rs['name_de'];
			}
			echo json_encode($array);
		break;

		// autocomplete the kont input
		case "kont":
			// get kont from table $f at entry $q
			if(isset($_GET['f'])) {
				$f = strtolower(htmlentities($_GET["f"]));
				$sql = "SELECT kontinent FROM $f WHERE name_de LIKE '".$q."%'";
				$req = mysql_query($sql);
				while($row = mysql_fetch_array($req)) {
					$kont = $row;
				}
				$sql = "SELECT name_de FROM kontinent WHERE id=".$kont[0];
				$req = mysql_query($sql);
				while($row = mysql_fetch_array($req)) {
					$kontname = $row;
				}
				echo $kontname['name_de'];
			}
			else {
				$sql = "SELECT name_de FROM kontinent WHERE name_de LIKE '".$q."%'";
				$rsd = mysql_query($sql);
				$array = array();
				while($rs = mysql_fetch_array($rsd)) {
					array_push($array, $rs['name_de']);
				}
				echo json_encode($array);
			}
		break;
		
		// autocomplete the ort input field
		case "ort":
			$sql = "SELECT name_de FROM regiort WHERE name_de LIKE '".$q."%' AND typ='ort'";
			$rsd = mysql_query($sql);
			$array = array();
			while($rs = mysql_fetch_array($rsd)) {
				array_push($array, $rs['name_de']);
			}
			echo json_encode($array);
		break;
		
		// autocomplete the region input field
		case "region":
			$sql = "SELECT name_de FROM regiort WHERE name_de LIKE '".$q."%' AND typ='region'";
			$rsd = mysql_query($sql);
			$array = array();
			while($rs = mysql_fetch_array($rsd)) {
				array_push($array, $rs['name_de']);
			}
			echo json_encode($array);
			break;
		
		// autocomplete the prov field
		case "prov":
			$sql = "SELECT name_de FROM provsubk WHERE name_de LIKE '".$q."%' AND id>63";
			$rsd = mysql_query($sql);
			$array = array();
			while($rs = mysql_fetch_array($rsd)) {
				array_push($array, $rs['name_de']);
			}
			echo json_encode($array);
		break;
		
		// autocomplete the subk field
		case "subk":
			$sql = "SELECT name_de FROM provsubk WHERE name_de LIKE '".$q."%' AND id<64";
			$rsd = mysql_query($sql);
			$array = array();
			while($rs = mysql_fetch_array($rsd)) {
				array_push($array, $rs['name_de']);
			}
			echo json_encode($array);
		break;
	

//////////////////////////////////////////////
header("Content-type: text/html; charset=utf-8");
include_once('xtpl/xtemplate.class.php');
include_once 'mysql.inc.php';

$xtpl = new XTemplate('index.html');

$query = "SELECT * FROM `kontinent`";
$result = mysql_query("$query");
while($row = mysql_fetch_object($result)) {
	$xtpl->assign('val', $row->id);
	$xtpl->assign('text', $row->name_de);
	$xtpl->parse('main.dropdown.option');
}
$xtpl->assign('name', "kont");
$xtpl->parse('main.dropdown');
$xtpl->out('main.dropdown');
////////////////////////////////////////////
/*
 * Liste mit Orten auffüllen
 */
include_once 'mysql.inc.php';
include_once 'xtpl/xtemplate.class.php';

$prov = $_REQUEST['prov'];

$xtpl = new XTemplate('index.html');

$query = "SELECT * FROM `regiort` WHERE typ='ort' AND upper_tbl = ".$prov;
$result = mysql_query("$query");
while($row = mysql_fetch_object($result)) {
    $xtpl->assign('val', $row->id);
    $xtpl->assign('text', $row->name_de);
    $xtpl->parse('main.dropdown.option');
}
$xtpl->assign('name', "ort");
$xtpl->parse('main.dropdown');

$xtpl->out('main.dropdown');

////////////////////////////////////////////
/*
 * gibt jeweils ein json zurück, das die $_GET['term']-buchstaben enthält
 */
include('mysql.inc.php');
$q = strtolower($_GET["term"]);
if (!$q) return;

if($_REQUEST['typ']=='ort') {
    $sql = "SELECT name_de FROM regiort WHERE name_de LIKE '".$q."%' AND typ = 'ort'";
}
elseif($_REQUEST['typ']=='region') {
    $sql = "SELECT name_de FROM regiort WHERE name_de LIKE '".$q."%' AND typ = 'region'";
}
$rsd = mysql_query($sql);
$array = array();
while($rs = mysql_fetch_array($rsd)) {
	array_push($array, $rs['name_de']);
}
echo json_encode($array);

////////////////////////////////////////////
include_once 'mysql.inc.php';
include_once 'xtpl/xtemplate.class.php';

$prov = $_REQUEST['prov'];

$xtpl = new XTemplate('index.html');

$query = "SELECT * FROM `regiort` WHERE typ='region' AND upper_tbl = ".$prov;
$result = mysql_query("$query");
while($row = mysql_fetch_object($result)) {
    $xtpl->assign('val', $row->id);
    $xtpl->assign('text', $row->name_de);
    $xtpl->parse('main.dropdown.option');
}
$xtpl->assign('name', "region");
$xtpl->parse('main.dropdown');

$xtpl->out('main.dropdown');

////////////////////////////////////////////
include_once 'mysql.inc.php';
include_once 'xtpl/xtemplate.class.php';

$subk = $_REQUEST['subk'];

$xtpl = new XTemplate('index.html');

if ($subk != "*")
    $query = "SELECT * FROM `provsubk` WHERE FIND_IN_SET($subk, `super_r`)";
else
    $query = "SELECT * FROM `provsubk` WHERE id > 63";
$result = mysql_query("$query");
while($row = mysql_fetch_object($result)) {
    $xtpl->assign('val', $row->id);
    $xtpl->assign('text', $row->name_de);
    $xtpl->parse('main.dropdown.option');
}
$xtpl->assign('name', "prov");
$xtpl->parse('main.dropdown');

$xtpl->out('main.dropdown');

////////////////////////////////////////////
include_once 'mysql.inc.php';
include_once 'xtpl/xtemplate.class.php';

$xtpl = new XTemplate('index.html');

if ($_REQUEST['kont'] != "*") {
    $query = "SELECT * FROM `provsubk` WHERE FIND_IN_SET(".$_REQUEST['kont'].", `super_r`)";
}
else {
    $query = "SELECT * FROM `provsubk` WHERE id<64";
}
$result = mysql_query("$query");
while($row = mysql_fetch_object($result)) {
    $xtpl->assign('val', $row->id);
    $xtpl->assign('text', $row->name_de);
    $xtpl->parse('main.dropdown.option');
}
$xtpl->assign('name', "subk");
$xtpl->parse('main.dropdown');

$xtpl->out('main.dropdown');

////////////////////////////////////////////
// return continent of ethnie
// header("Content-type: text/html; charset=utf-8");
include_once 'mysql.inc.php';

error_reporting(false);

if($_REQUEST['ethnie'] != "")
    $a=1;
else
    $a=0;

if ($a==1)
    $query = "SELECT kontinent from ethnie where name_de LIKE '%".$_GET['ethnie']."%' LIMIT 1";
else {
    $a = explode(",", $_REQUEST['subk']);
    $query = "SELECT super_r from provsubk where id=".$a[0]." LIMIT 1";
}
$result = mysql_query("$query");
while($row = mysql_fetch_object($result)) {
    if($a==1)
    	$kont = $row->kontinent;
    else
        $kont = $row->super_r;
}

echo trim($kont);
////////////////////////////////////////////
// return prov/subk of region/ort
// header("Content-type: textdomain/html; charset=utf-8");
include_once 'mysql.inc.php';
$query = "SELECT  upper_tbl from regiort where name_de LIKE '%".$_GET['ort']."%' LIMIT 1";
$result = mysql_query("$query");
while($row = mysql_fetch_object($result)) {
    $kont = $row->upper_tbl;
}

echo trim($kont);
////////////////////////////////////////////
// return subk of prov
// header("Content-typeapplicatione: textdomain/html; charset=utf-8");
include_once 'mysql.inc.php';
$query = "SELECT  super_r from provsubk where id=".$_GET['prov']." LIMIT 1";
$result = mysql_query("$query");
while($row = mysql_fetch_object($result)) {
    $kont = $row->super_r;
}
echo trim($kont);
}}?>
