<?php
include("mysql.inc.php");
header('Content-type: text/html; charset=utf-8');
session_start();
include("auth.inc.php");
testauthedit();
$lang = $_COOKIE['lang'];
if(isset($_GET['a'])) {
	$a = mysqli_real_escape_string($sqli, $_GET['a']);
	if(!isset($_GET['term'])) $_GET['term'] = "%"; 
	$q = strtolower(mysqli_real_escape_string($sqli, $_GET["term"]));
	if (!$q) return;
	// $a is always the input field to be filled (return type)
	// $f is (if given) the info we have to fill it
	// i.e. $a=kont $f=ethnie means:
	// fill the kont-input with things, that match the ethnie given in $q
	switch($a) {
		
		//autocomplete the ethnie input
		case "ethnie":
			if(isset($_GET['f'])) {
				$f = strtolower(mysqli_real_escape_string($sqli, $_GET["f"]));
				switch($f) {
					case 'kont'://select ethnien by specific kont id
						$sql = "SELECT id, name_".$lang." FROM ethnie WHERE kontinent=".$q;
						break;
				}
			}
			else { //load all from ethnie (standard)
				$sql = "SELECT id, name_".$lang." FROM ethnie WHERE name_".$lang." LIKE '".$q."%'";
			}
			$rsd = mysqli_query($sqli, $sql);
			$array = array();
			while($rs = mysqli_fetch_array($rsd)) {
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
				$f = strtolower(mysqli_real_escape_string($sqli, $_GET["f"]));
				switch($f) {
					case "subk":
						$sql="SELECT super_r FROM provsubk WHERE name_".$lang." LIKE '".$q."%' AND id<64";
						break;
					default:
						$sql = "SELECT kontinent FROM $f WHERE name_".$lang." LIKE '".$q."%'";
						break;
				}
				$req = mysqli_query($sqli, $sql);
				while($row = mysqli_fetch_array($req)) {
					$kont = explode(",",$row[0]);
				}
				$sql = "SELECT id, name_".$lang." FROM kontinent WHERE id=".$kont[0]."";
				$req = mysqli_query($sqli, $sql);
				while($row = mysqli_fetch_array($req)) {
					$arr = array('id' => $row['id'], 'label' => $row['name_'.$lang]);
				}
				echo $arr["id"];
				echo "|";
				echo $arr["label"];
			}
			else {
				$sql = "SELECT id, name_".$lang." FROM kontinent WHERE name_".$lang." LIKE '".$q."%'";
				$rsd = mysqli_query($sqli, $sql);
				$array = array();
				while($rs = mysqli_fetch_array($rsd)) {
					$arr = array('id' => $rs['id'], 'label' => $rs['name_'.$lang]);
					$arr = (object)$arr;
					array_push($array, $arr);
				}
				echo json_encode($array);
			}
		break;
		
		// autocomplete the ort input field
		case "ort":
			if(isset($_GET['f'])) {
				$f = strtolower(mysqli_real_escape_string($sqli, $_GET["f"]));
				switch($f) {
					case 'kont':
						// provsql
						$subksql = "SELECT DISTINCT id FROM provsubk WHERE FIND_IN_SET(".$q.", super_r) AND id<64";
						$req = mysqli_query($sqli, $subksql);
						$array = array();
						$provsql="SELECT DISTINCT id  FROM provsubk WHERE FALSE";
						while($r = mysqli_fetch_array($req)) {
							$provsql .= " OR FIND_IN_SET(".$r['id'].", super_r)";
						}
						$provsql .= " AND id>63";
			
			
						$sql = "SELECT id, name_".$lang." FROM regiort WHERE typ='ort' AND (upper_tbl IN (".$subksql.") OR upper_tbl IN (".$provsql."))";
						break;
					case 'subk':
						$sql = "SELECT id, name_de FROM regiort WHERE typ='ort' AND (upper_tbl = ".$q." OR upper_tbl IN (SELECT DISTINCT id FROM provsubk WHERE FIND_IN_SET(".$q.", super_r) AND id>63))";
						break;
					case 'prov':
						$sql = "SELECT id, name_de FROM regiort WHERE typ='ort' AND upper_tbl = ".$q;
						break;
				}
			}
			else {
				$sql = "SELECT id, name_".$lang." FROM regiort WHERE name_".$lang." LIKE '".$q."%' AND typ='ort'";
			}
			$rsd = mysqli_query($sqli, $sql);
			$array = array();
			while($rs = mysqli_fetch_array($rsd)) {
				$arr = array('id' => $rs['id'], 'label' => $rs['name_'.$lang]);
				$arr = (object)$arr;
				array_push($array, $arr);
			}
			echo json_encode($array);
			
		break;
		
		// autocomplete the region input field
		case "region":
			if(isset($_GET['f'])) {
				$f = strtolower(mysqli_real_escape_string($sqli, $_GET["f"]));
				switch($f) {
					case 'kont':
						// provsql
						$subksql = "SELECT DISTINCT id FROM provsubk WHERE FIND_IN_SET(".$q.", super_r) AND id<64";
						$req = mysqli_query($sqli, $subksql);
						$array = array();
						$provsql="SELECT DISTINCT id  FROM provsubk WHERE FALSE";
						while($r = mysqli_fetch_array($req)) {
							$provsql .= " OR FIND_IN_SET(".$r['id'].", super_r)";
						}
						$provsql .= " AND id>63";

						
						$sql = "SELECT id, name_".$lang." FROM regiort WHERE typ='region' AND (upper_tbl IN (".$subksql.") OR upper_tbl IN (".$provsql."))";
						break;
					case 'subk':
						$sql = "SELECT id, name_de FROM regiort WHERE typ='region' AND (upper_tbl = ".$q." OR upper_tbl IN (SELECT DISTINCT id FROM provsubk WHERE FIND_IN_SET(".$q.", super_r) AND id>63))";
						break;
					case 'prov':
						$sql = "SELECT id, name_de FROM regiort WHERE typ='region' AND upper_tbl = ".$q;
						break;
				}
			}
			else {
				$sql = "SELECT id, name_".$lang." FROM regiort WHERE name_".$lang." LIKE '".$q."%' AND typ='region'";
			}
			$rsd = mysqli_query($sqli, $sql);
			$array = array();
			while($rs = mysqli_fetch_array($rsd)) {
				$arr = array('id' => $rs['id'], 'label' => $rs['name_'.$lang]);
				$arr = (object)$arr;
				array_push($array, $arr);
			}
			echo json_encode($array);
		break;
		
		// autocomplete the prov field
		case "prov":
			if(isset($_GET['f'])) {
				$f = strtolower(mysqli_real_escape_string($sqli, $_GET["f"]));
				switch($f) {
					case "regiort":
						$sql="SELECT upper_tbl FROM regiort WHERE name_".$lang." LIKE '%".$q."%'";
						$req = mysqli_query($sqli, $sql);
						while($row = mysqli_fetch_array($req)) {
							$super_r = $row;
						}
						$sql = "SELECT id, name_".$lang." FROM provsubk WHERE id=".$super_r[0];
						$req = mysqli_query($sqli, $sql);
						while($row = mysqli_fetch_array($req)) {
							$arr = array('id' => $row['id'], 'label' => $row['name_'.$lang]);
						}
						echo $arr["id"];
						echo "|";
						echo $arr["label"];
						break;
					case "kont":
						$sql = "SELECT DISTINCT id FROM provsubk WHERE FIND_IN_SET(".$q.", super_r) AND id<64;";
// 						echo $sql;
						$req = mysqli_query($sqli, $sql);
						$array = array();
						$sql="SELECT DISTINCT id, name_".$lang." FROM provsubk WHERE FALSE";
						while($r = mysqli_fetch_array($req)) {
							$sql .= " OR FIND_IN_SET(".$r['id'].", super_r)";
						}
						$sql .= " AND id>63";
// 						echo $sql;
						
						$rsd = mysqli_query($sqli, $sql);
												
						while($rs = mysqli_fetch_array($rsd)) {
							$arr = array('id' => $rs['id'], 'label' => $rs['name_'.$lang]);
							$arr = (object)$arr;
							array_push($array, $arr);
						}
					
						sort($array);
						$tmp = array();
						foreach($array as $k => $v)
							$tmp[$k] = $v->label;
						
						// Find duplicates in temporary array
						$tmp = array_unique($tmp);
						
						// Remove the duplicates from original array
						foreach($array as $k => $v)
						{
							if (!array_key_exists($k, $tmp))
								unset($array[$k]);
						}
						echo json_encode($array);
						break;
					case 'subk':
						$sql = "SELECT DISTINCT id, name_".$lang." FROM provsubk WHERE FIND_IN_SET(".$q.", super_r) AND id>63;";
						$req = mysqli_query($sqli, $sql);
						$array = array();
// 						echo $sql;
						while($rs = mysqli_fetch_array($req)) {
							$arr = array('id' => $rs['id'], 'label' => $rs['name_'.$lang]);
							$arr = (object)$arr;
							array_push($array, $arr);
						}
						
						echo json_encode($array);
						break;
				}
			}
			else {
				$sql = "SELECT id, name_".$lang." FROM provsubk WHERE name_".$lang." LIKE '".$q."%' AND id>63";
				$rsd = mysqli_query($sqli, $sql);
				$array = array();
				while($rs = mysqli_fetch_array($rsd)) {
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
				$f = strtolower(mysqli_real_escape_string($sqli, $_GET["f"]));
				switch($f) {
					case "prov":
						$sql="SELECT super_r FROM provsubk WHERE name_".$lang." LIKE '%".$q."%' AND id>63;";
						$req = mysqli_query($sqli, $sql);
						while($row = mysqli_fetch_array($req)) {
							$super_r = explode(",",$row[0]);
						}
						$sql = "SELECT id, name_".$lang." FROM provsubk WHERE id=".$super_r[0];
						$req = mysqli_query($sqli, $sql);
						while($row = mysqli_fetch_array($req)) {
							$arr = array('id' => $row['id'], 'label' => $row['name_'.$lang]);
						}
						echo $arr["id"];
						echo "|";
						echo $arr["label"];
						break;
					case "kont":
						$sql="SELECT id, name_".$lang." FROM provsubk WHERE FIND_IN_SET(".$q.", super_r) AND id<64;";
						$rsd = mysqli_query($sqli, $sql);
						$array = array();
						while($rs = mysqli_fetch_array($rsd)) {
							$arr = array('id' => $rs['id'], 'label' => $rs['name_'.$lang]);
							$arr = (object)$arr;
							array_push($array, $arr);
						}
						echo json_encode($array);
						break;
				}
				
			}
			else {
				$sql = "SELECT id, name_".$lang." FROM provsubk WHERE name_".$lang." LIKE '".$q."%' AND id<64";
				$rsd = mysqli_query($sqli, $sql);
				$array = array();
				while($rs = mysqli_fetch_array($rsd)) {
					$arr = array('id' => $rs['id'], 'label' => $rs['name_'.$lang]);
					$arr = (object)$arr;
					array_push($array, $arr);
				}
				echo json_encode($array);
			}
		break;
	}
}
// zum speichern von einträgen
elseif (isset($_GET['s']) && $_GET['s']=='save') {
	$data['bid']=mysqli_real_escape_string($sqli, $_GET['bid']);
	$data['kont']=mysqli_real_escape_string($sqli, $_GET['kont']);
	$data['ethnie']=mysqli_real_escape_string($sqli, $_GET['ethnie']);
	$data['subk']=mysqli_real_escape_string($sqli, $_GET['subk']);
	$data['prov']=mysqli_real_escape_string($sqli, $_GET['prov']);
	$data['region']=mysqli_real_escape_string($sqli, $_GET['region']);
	$data['ort']=mysqli_real_escape_string($sqli, $_GET['ort']);
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
	$result=mysqli_query($sqli, $sql);
	if (!$result) { // add this check.
		die('Invalid query: ' . mysqli_error($sqli));
	}
}
?>
