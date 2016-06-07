<?php
if(! array_key_exists('id', $_GET)){
	include("literature_list.php");
} else {
	include("literature_detail.php");
}
?>
