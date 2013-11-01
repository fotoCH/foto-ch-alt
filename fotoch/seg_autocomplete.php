<?php
include("mysql.inc.php");
header('Content-type: text/html; charset=utf-8');
session_start();
include("auth.inc.php");
testauthedit();
$lang = $_COOKIE['lang'];
if(isset($_GET['a'])) {
	$a = mysql_real_escape_string($_GET['a']);
	$q = strtolower(mysql_real_escape_string($_GET["term"]));
	if (!$q) return;
	// $a is always the imput field to be filled
	// $f is (if given) the info we have to fill it
	// i.e. $a=kont $f=ethnie means:
	// fill the kont-input with things, that match the ethnie given in $q
	switch($a) {
		
		//autocomplete the ethnie input
		case "ethnie":
			$sql = "SELECT id, name_".$lang." FROM ethnie WHERE name_".$lang." LIKE '".$q."%'";
			$rsd = mysql_query($sql);
			$array = array();
			while($rs = mysql_fetch_array($rsd)) {
				$arr = array('id' => $rs['id'], 'label' => $rs['name_'.$lang]);
				$arr = (object)$arr;
				array_push($array, $arr);
			}
			echo json_encode($array);
		break;

		// autocomplete the kont input
		case "kont":
			// get kont from table $f at entry $q
			if(isset($_GET['f'])) {
				$f = strtolower(mysql_real_escape_string($_GET["f"]));
				switch($f) {
					case "subk":
						$sql="SELECT super_r FROM provsubk WHERE name_".$lang." LIKE '".$q."%' AND id<64";
						break;
					default:
						$sql = "SELECT kontinent FROM $f WHERE name_".$lang." LIKE '".$q."%'";
						break;
				}
				$req = mysql_query($sql);
				while($row = mysql_fetch_array($req)) {
					$kont = explode(",",$row[0]);
				}
				$sql = "SELECT id, name_".$lang." FROM kontinent WHERE id=".$kont[0]."";
				$req = mysql_query($sql);
				while($row = mysql_fetch_array($req)) {
					$arr = array('id' => $row['id'], 'label' => $row['name_'.$lang]);
				}
				echo $arr["id"];
				echo "|";
				echo $arr["label"];
			}
			else {
				$sql = "SELECT id, name_".$lang." FROM kontinent WHERE name_".$lang." LIKE '".$q."%'";
				$rsd = mysql_query($sql);
				$array = array();
				while($rs = mysql_fetch_array($rsd)) {
					$arr = array('id' => $rs['id'], 'label' => $rs['name_'.$lang]);
					$arr = (object)$arr;
					array_push($array, $arr);
				}
				echo json_encode($array);
			}
		break;
		
		// autocomplete the ort input field
		case "ort":
			$sql = "SELECT id, name_".$lang." FROM regiort WHERE name_".$lang." LIKE '".$q."%' AND typ='ort'";
			$rsd = mysql_query($sql);
			$array = array();
			while($rs = mysql_fetch_array($rsd)) {
				$arr = array('id' => $rs['id'], 'label' => $rs['name_'.$lang]);
				$arr = (object)$arr;
				array_push($array, $arr);
			}
			echo json_encode($array);
		break;
		
		// autocomplete the region input field
		case "region":
			$sql = "SELECT id, name_".$lang." FROM regiort WHERE name_".$lang." LIKE '".$q."%' AND typ='region'";
			$rsd = mysql_query($sql);
			$array = array();
			while($rs = mysql_fetch_array($rsd)) {
				$arr = array('id' => $rs['id'], 'label' => $rs['name_'.$lang]);
				$arr = (object)$arr;
				array_push($array, $arr);
			}
			echo json_encode($array);
			break;
		
		// autocomplete the prov field
		case "prov":
			if(isset($_GET['f'])) {
				$f = strtolower(mysql_real_escape_string($_GET["f"]));
				switch($f) {
					case "regiort":
						$sql="SELECT upper_tbl FROM regiort WHERE name_".$lang." LIKE '%".$q."%'";
						break;
				}
				$req = mysql_query($sql);
				while($row = mysql_fetch_array($req)) {
					$super_r = $row;
				}
				$sql = "SELECT id, name_".$lang." FROM provsubk WHERE id=".$super_r[0];
				$req = mysql_query($sql);
				while($row = mysql_fetch_array($req)) {
					$arr = array('id' => $row['id'], 'label' => $row['name_'.$lang]);
				}
				echo $arr["id"];
				echo "|";
				echo $arr["label"];
			}
			else {
				$sql = "SELECT id, name_".$lang." FROM provsubk WHERE name_".$lang." LIKE '".$q."%' AND id>63";
				$rsd = mysql_query($sql);
				$array = array();
				while($rs = mysql_fetch_array($rsd)) {
					$arr = array('id' => $rs['id'], 'label' => $rs['name_'.$lang]);
					$arr = (object)$arr;
					array_push($array, $arr);
				}
				echo json_encode($array);
			}
		break;
		
		// autocomplete the subk field
		case "subk":
			if(isset($_GET['f'])) {
				$f = strtolower(mysql_real_escape_string($_GET["f"]));
				switch($f) {
					case "prov":
						$sql="SELECT super_r FROM provsubk WHERE name_".$lang." LIKE '%".$q."%' AND id>63;";
						break;
				}
				$req = mysql_query($sql);
				while($row = mysql_fetch_array($req)) {
					$super_r = explode(",",$row[0]);
				}
				$sql = "SELECT id, name_".$lang." FROM provsubk WHERE id=".$super_r[0];
				$req = mysql_query($sql);
				while($row = mysql_fetch_array($req)) {
					$arr = array('id' => $row['id'], 'label' => $row['name_'.$lang]);
				}
				echo $arr["id"];
				echo "|";
				echo $arr["label"];
			}
			else {
				$sql = "SELECT id, name_".$lang." FROM provsubk WHERE name_".$lang." LIKE '".$q."%' AND id<64";
				$rsd = mysql_query($sql);
				$array = array();
				while($rs = mysql_fetch_array($rsd)) {
					$arr = array('id' => $rs['id'], 'label' => $rs['name_'.$lang]);
					$arr = (object)$arr;
					array_push($array, $arr);
				}
				echo json_encode($array);
			}
		break;
	}
}
elseif (isset($_GET['s']) && $_GET['s']=='save') {
	$data['bid']=mysql_real_escape_string($_GET['bid']);
	$data['kont']=mysql_real_escape_string($_GET['kont']);
	$data['ethnie']=mysql_real_escape_string($_GET['ethnie']);
	$data['subk']=mysql_real_escape_string($_GET['subk']);
	$data['prov']=mysql_real_escape_string($_GET['prov']);
	$data['region']=mysql_real_escape_string($_GET['region']);
	$data['ort']=mysql_real_escape_string($_GET['ort']);
	foreach ($data as $element) {
		if (is_numeric($element) || empty($element)) {
			continue;
		}
		else {
			die("Bad Parameter detected!");
		}
	}
	if(empty($data['ort']))
		$data['regiort']=$data['region'];
	elseif(empty($data['region']))
		$data['regiort']=$data['ort'];
	else {
		die("Problem with setting ort=".$data['ort']." and region=".$data['region']);
	}
	$sql="INSERT INTO `bestand_segref` (`bestand_id`, `kontinent_id`, `subk_id`, `regionort_id`, `prov_id`, `ethnien_id`) VALUES ('".$data['bid']."', '".$data['kont']."', '".$data['subk']."', '".$data['regiort']."', '".$data['prov']."', '".$data['ethnie']."');";
	$result=mysql_query($sql);
	if (!$result) { // add this check.
		die('Invalid query: ' . mysql_error());
	}
}
?>