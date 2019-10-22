<?php

ini_set('display_errors', 1);
error_reporting( E_WARNING | E_ERROR | E_PARSE );
require('../api/mysql.inc.php');

include_once('sru.inc.php');

$operation=strtolower($_GET['operation']);
$version=$_GET['version'];
$query=$_GET['query'];
$maxrecords=$_GET['maximumRecords'];

if ($operation!='searchretrieve' || $version!='1.2') {
    outputXML(expandSRUError(1,"Bad operation or version","Operation: $operation, Version: $version"));
    exit;
}

$server=print_r($_SERVER, true);
writeToLog("query: $query maxrecords: $maxrecords fromIP: $_SERVER[HTTP_X_REAL_IP]");
include("sru.php");

?>
