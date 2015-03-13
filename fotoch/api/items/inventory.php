<?php

include("././fotofunc.inc.php");
include("././foto-ch.inc.php");

if($_GET['id']==''){
	include("inventory_list.php");
} else {
	include("inventory_detail.php");
}
?>
