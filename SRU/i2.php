<?php

ini_set('display_errors', 1);
error_reporting( E_WARNING | E_ERROR | E_PARSE );

$operation=$_GET['operation'];
$version=$_GET['version'];
$query=$_GET['query'];
$maximumrecords=$_GET['maximumrecords'];

if ($version!='searchretrieve' || $version!='1.2') {
    echo("bad operation or version");
}
include("sru.php");


?>
