<?php

if($_GET['id']==''){
	include("inventory_list.php");
} else {
	include("inventory_detail.php");
}
?>
