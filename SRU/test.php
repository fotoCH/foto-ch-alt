<?php
ini_set('display_errors', 1);
error_reporting( E_WARNING | E_ERROR | E_PARSE );
echo("a");
include_once('sru.inc.php');

echo(expandSRUError());

?>
