<?php

$id=$_GET['id'];

if (auth_level(USER_WORKER)){
	$result=mysql_query("SELECT * FROM fotos WHERE id=$id");
	$afields=array("id","typ","notiz");
	
} else {
	$result=mysql_query("SELECT * FROM fotos WHERE id=$id");
	$afields=array();
}

$select = 'f.id AS id, f.dc_created, f.zeitraum AS created, f.dc_title AS title, f.dc_description AS description, f.dc_creator, image_path, f.dc_right AS copy, f.dcterms_medium AS medium, f.dc_identifier AS img_url, f.dcterms_spatial AS dct_spatial, f.dcterms_subject AS subject, f.dc_coverage, ';
$select.= 'CONCAT(n.vorname, " ", n.nachname) AS name, ';
$select.= 'i.name AS institution, i.id as inst_id,';
$select.= 'b.name AS stock, b.id AS stock_id, f.supplier_id as supp_id ';

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

if ($inst_comment==$fetch['inst_id']){
    $result=mysql_query("SELECT * FROM fotos_comments WHERE id=$id");
    $rowCount = mysql_num_rows($result);
    if ($rowCount==0){
	$out['comment']=array('id'=>$id);
    } else {
        $out['comment']=mysql_fetch_assoc ( $result );
    }
//$out['comment']=array('name'=>'testname', 'title'=>'testt');
}
jsonout($out);
?>
