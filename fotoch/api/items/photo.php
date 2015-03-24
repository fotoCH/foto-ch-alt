<?php
$id = getClean('id');

if($id==''){
	include("photo_list.php");
} else {
	include("photo_detail.php");
}
?>
