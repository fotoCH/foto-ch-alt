<?php

function shakeImages() {
	$imgPlaceholders = array();		
	$imgFiles = array("Bubbadetklein.jpg","bubenbadenklein.jpg","bubenvordembadenklein.jpg","dampfschiffkklein.jpg","kinderspazieren.jpg","suesswassermatroseklein.jpg");
	
	for($j = 0; $j < 6; $j++){	
		$img = $imgFiles[rand(0,sizeof($imgFiles)-1)];
		
		if(!in_array($img, $imgPlaceholders)){
			$imgPlaceholders[$j] = $img;
		}
		else {
			$j--;
		}	
	}
	return $imgPlaceholders;
}

?>