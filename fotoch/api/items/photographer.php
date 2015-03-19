<?php

include("././fotofunc.inc.php");
include("././foto-ch.inc.php");

if($_GET['id']==''){
	include("photographer_list.php");
} else {
	include("photographer_detail.php");
}
?>
