<?php

include("././fotofunc.inc.php");
include("././foto-ch.inc.php");

if($_GET['id']==''){
	include("institution_list.php");
} else {
	include("institution_detail.php");
}
?>
