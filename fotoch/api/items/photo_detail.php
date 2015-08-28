<?php

$id=$_GET['id'];

if (auth_level(USER_WORKER)){
	$result=mysql_query("SELECT * FROM fotos WHERE id=$id");
	$afields=array("id","typ","notiz");
	
} else {
	$result=mysql_query("SELECT * FROM fotos WHERE id=$id");
	$afields=array();
}

$select = 'f.id AS id, f.dc_created, f.zeitraum AS created, f.dc_title AS title, f.dc_description AS description, image_path, ';
$select.= 'CONCAT(n.vorname, " ", n.nachname) AS name, ';
$select.= 'i.name AS institution, i.id as inst_id,';
$select.= 'b.name AS stock';

$join .= "LEFT JOIN namen AS n ON f.dc_creator=n.fotografen_id ";
$join .= "LEFT JOIN institution AS i ON f.edm_dataprovider=i.id ";
$join .= "LEFT JOIN bestand AS b ON f.dcterms_ispart_of=b.id ";

$query="SELECT DISTINCT $select FROM fotos AS f $join";

	$query.=" WHERE f.id=$id";

//echo $query;
$result=mysql_query($query);
$rowCount = mysql_num_rows($result);
$out['result_count']= $rowCount;

// do query
$fetch = mysql_fetch_assoc ( $result );
$out=$fetch;
    
jsonout($out);
?>
