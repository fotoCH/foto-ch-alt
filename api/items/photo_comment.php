<?php

//$data=json_decode(array_keys($_POST)[0]);
$data = json_decode(file_get_contents("php://input"), true);

$id=$_REQUEST['id'];

$fields=array('title','name','description','created','comments','ort','subject');

$set='';

foreach ($fields as $f){
    if ($set) $set.=', ';
    $set.='`'.$f.'`=\''.mysqli_real_escape_string($sqli, $data[$f]).'\'';
}

$sql="REPLACE fotos_comments SET $set , id=$id";

mysqli_query($sqli, $sql);
$out['sql']=$sql;
$out['sqle']=mysqli_error($sqli);

jsonout($out);
?>
