<?php

//$data=json_decode(array_keys($_POST)[0]);
$data = json_decode(file_get_contents("php://input"), true);

$id=$_REQUEST['id'];

$fields=array('title','name','description','created','comments');

$set='';

foreach ($fields as $f){
    if ($set) $set.=', ';
    $set.='`'.$f.'`=\''.mysql_real_escape_string($data[$f]).'\'';
}

$sql="REPLACE fotos_comments SET $set , id=$id";

mysql_query($sql);
$out['sql']=$sql;
$out['sqle']=mysql_error();

jsonout($out);
?>
