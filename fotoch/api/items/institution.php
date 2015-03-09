<?php

include("././fotofunc.inc.php");
include("././foto-ch.inc.php");

if($_GET['id']==''){
	include("fotograf_list.php");
} else {
	include("fotograf_detail.php");
}
?>
