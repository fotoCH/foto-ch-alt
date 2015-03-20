<?php

include("././fotofunc.inc.php");
include("././foto-ch.inc.php");

if($_GET['id']==''){
	include("literature_list.php");
} else {
	include("literature_detail.php");
}
?>
