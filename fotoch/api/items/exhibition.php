<?php

//include("././fotofunc.inc.php");
//include("././foto-ch.inc.php");

if($_GET['id']==''){
	include("exhibition_list.php");
} else {
	include("exhibition_detail.php");
}
?>
