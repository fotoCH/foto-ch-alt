<?php

function shakeImages() {
	$imgPlaceholders = array();		
	$imgFiles = array();
	
	$dir = opendir("images");
	while (($file = readdir($dir)) !== false) {
		if($file!='..' && $file!='.' && $file!=''){
			$imgFiles[] = $file;
		}
	}
	
	for($j = 0; $j < 6; $j++){	
		$img = $imgFiles[rand(0,sizeof($imgFiles)-1)];
		
		if(!in_array($img, $imgPlaceholders)){
			$imgPlaceholders[$j] = $img;
		}
		else {
			$j--;
		}	
	}
	closedir($dir);
	return $imgPlaceholders;
}

?>