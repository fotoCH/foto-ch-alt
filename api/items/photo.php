<?php
$id = getClean('id');

if($id==''){
	include("photo_list.php");
} else {
	if ($_SERVER['REQUEST_METHOD']=='POST'){
		include("photo_comment.php");
	    
	} else {
		include("photo_detail.php");
	}
}
?>
