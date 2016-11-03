<?php

$anf = getClean('anf');
$rand= getClean('random');

// alph. suche
// if submit is empty -> listenansicht
if ($id == '') {
	$vars=array('photograph','period_start','period_end','title','photographer','institution','inventory');
	foreach ($vars as $key){
		$value=getClean($key);
		if (!empty($value)){
			switch ($key){
				case 'photograph':
					$arrName = explode(' ', $value);
					$where .= ($where!='' ? ' AND ' : '')."((n.nachname LIKE '%$arrName[0]%' AND n.vorname LIKE '%$arrName[1]%') OR (n.vorname LIKE '%$arrName[0]%' AND n.nachname LIKE '%$arrName[1]%'))";
					break;
				case 'photographer':
					$where .= ($where!='' ? ' AND ' : '')."(n.fotografen_id=$value)";
					break;
				case 'period_start':
					$period_start = "$value-01-01";
					$where .= ($where!='' ? ' AND ' : '')."(f.dc_created >= '$period_start' OR f.dc_created = 0000-00-00)";
					break;
				case 'period_end':
					$period_end = "$value-12-31";
					$where .= ($where!='' ? ' AND ' : '')."(f.dc_created <= '$period_end' OR f.dc_created = 0000-00-00)";
					break;
				case 'title':
					$where .= ($where!='' ? ' AND ' : '')."(f.dc_title LIKE '%$value%' OR f.dc_description LIKE '%$value%' OR f.dc_coverage LIKE '%$value%')";
					break;
				case 'institution':
					if ($value != 0) {
						$where .= ($where!='' ? ' AND ' : '')."i.id='$value'";
					}
					break;
				case 'inventory':
					if ($value != 0) {
						$where .= ($where!='' ? ' AND ' : '')."b.id='$value'";
					}
					break;
			}
		}
	}
	
	$select = 'f.id AS id, f.dc_created, f.zeitraum AS created, f.dc_title AS title, f.dc_description AS description, f.dcterms_ispart_of, image_path, ';
	$select.= 'CONCAT(n.vorname, " ", n.nachname) AS name, f.dc_creator as photographer_id, ';
	$select.= 'i.name AS institution, i.id as institution_id, ';
	$select.= 'b.name AS stock, b.id as stock_id';
	
	$join = '';
	$join .= "LEFT JOIN namen AS n ON f.dc_creator=n.fotografen_id ";
	$join .= "LEFT JOIN institution AS i ON f.edm_dataprovider=i.id ";
	$join .= "LEFT JOIN bestand AS b ON f.dcterms_ispart_of=b.id ";
	
	// random & limit before moving sql to temp table
	if($rand && is_numeric($rand)) {
		$query="SELECT DISTINCT $select FROM (SELECT * FROM fotos ORDER BY RAND() LIMIT ".$rand.") f $join";
	} else {
		$query="SELECT DISTINCT $select FROM fotos AS f $join";
	}
	if (!empty($where)){
		$query.=" WHERE $where";
	}
	$result=mysqli_query($sqli, $query);
	$rowCount = mysqli_num_rows($result);
	$out['result_count']= $rowCount;
	// do query
	while ( $fetch = mysqli_fetch_assoc ( $result ) ) {
		//pushfields($outl,$fetch,array('nachname','vorname','namenszusatz','id'));
		if(array_key_exists('fbearbeitungsdatum', $fetch)) {
			$outl['bearbeitungsdatum']=$fetch['fbearbeitungsdatum'];
		}
		$out['res'][]=$fetch;
	}
	//$out ['glob'] = $glob;
	jsonout($out);
}

?>
