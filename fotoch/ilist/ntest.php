<?php
require("../mysql.inc.php");
require("../fotofunc.inc.php");

$foto=strtolower($_REQUEST['id']);
print_r(getallnam($foto));

?>