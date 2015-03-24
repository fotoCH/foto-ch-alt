<?php
$id = getClean('id');

if($id==''){
	include("photographer_list.php");
} else {
	include("photographer_detail.php");
}
?>
