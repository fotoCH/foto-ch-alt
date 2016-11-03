<?php

$id=$_GET['id'];

if (auth_level(USER_WORKER)){
	$result=mysqli_query($sqli, "SELECT * FROM fotos WHERE id=$id");
	$afields=array("id","typ","notiz");
	
} else {
	$result=mysqli_query($sqli, "SELECT * FROM fotos WHERE id=$id");
	$afields=array();
}

$select = 'f.id AS id, f.dc_created, f.zeitraum AS created, f.dc_title AS title, f.dc_description AS description, f.dc_creator, ';
$select .= 'image_path, f.dc_right AS copy, f.dcterms_medium AS medium, f.dc_identifier AS img_url, f.dcterms_spatial AS dct_spatial, ';
$select .= 'f.dcterms_subject AS subject, f.dc_coverage, f.edm_licence, f.edm_watermark, f.edm_order, f.url_wikimedia, ';
if(auth_level(USER_GUEST_FOTOS)){
    $select .= 'f.edm_notes, f.edm_condition, f.edm_author, f.edm_editing_date, ';
}
$select.= 'CONCAT(n.vorname, " ", n.nachname) AS name, ';
$select.= 'i.name AS institution, i.id as inst_id,';
$select.= 'b.name AS stock, b.id AS stock_id, f.supplier_id as supp_id ';

$join .= "LEFT JOIN namen AS n ON f.dc_creator=n.fotografen_id ";
$join .= "LEFT JOIN institution AS i ON f.edm_dataprovider=i.id ";
$join .= "LEFT JOIN bestand AS b ON f.dcterms_ispart_of=b.id ";

$query="SELECT DISTINCT $select FROM fotos AS f $join";

	$query.=" WHERE f.id=$id";

//echo $query;die();
$result=mysqli_query($sqli, $query);
$rowCount = mysqli_num_rows($result);
$out['result_count']= $rowCount;

// do query
$fetch = mysqli_fetch_assoc ( $result );
$out=$fetch;

if ($inst_comment==$fetch['inst_id']){
    $result=mysqli_query($sqli, "SELECT * FROM fotos_comments WHERE id=$id");
    $rowCount = mysqli_num_rows($result);
    if ($rowCount==0){
	$out['comment']=array('id'=>$id);
    } else {
        $out['comment']=mysqli_fetch_assoc ($sqli,  $result );
    }
//$out['comment']=array('name'=>'testname', 'title'=>'testt');
}
jsonout($out);
?>
