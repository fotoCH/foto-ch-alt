<?php
function changeurl($lan) {
	$getters = $_GET;
	$getters['lang'] = $lan;
	$string = '';
	if(!empty($getters['bildgattungen']) || !empty($getters['fotografengattungen']) || !empty($getters['kanton'])){
		$url = getInputArrayAsString($getters['bildgattungen'],"bildgattungen[]");
		$url.= getInputArrayAsString($getters['fotografengattungen'],"fotografengattungen[]");
		$url.= getInputArrayAsString($getters['kanton'],"kanton[]");	
		unset($getters['bildgattungen']);
		unset($getters['fotografengattungen']);
		unset($getters['kanton']);		
	}
	foreach($getters as $key => $value) {
		if(empty($string)) {
			$string .= $key . "=" . $value;
		} else {
			$string .= "&" . $key . "=" . $value;
		}
	}
	$string1 .= $url;
	$string1 = htmlspecialchars($string);
	return ($string1);
}
function GETasString() {
	$getters = $_GET;
	$string = '';
	$url = '';
	if(!empty($getters['bildgattungen']) || !empty($getters['fotografengattungen']) || !empty($getters['kanton'])){	
		$url = getInputArrayAsString($getters['bildgattungen'],"bildgattungen[]");
		$url.= getInputArrayAsString($getters['fotografengattungen'],"fotografengattungen[]");
		$url.= getInputArrayAsString($getters['kanton'],"kanton[]");	
		unset($getters['bildgattungen']);
		unset($getters['fotografengattungen']);
		unset($getters['kanton']);		
	}
	foreach($getters as $key => $value) {
		if(empty($string)) {
			$string .= $key . "=" . $value;
		} else {
			$string .= "&" . $key . "=" . $value;
		}
	}
	$string .= $url;
	$string1 = htmlspecialchars($string);
	return ($string1);
}



function getInputArrayAsString($inputArray,$arrayName){
	$url1 = '';
	if(!empty($inputArray)){	
		foreach($inputArray as $key => $value){
			$url1.="&".$arrayName."=".$value;
		}
	}
	else {
		$url1 = "";
	}
	return $url1;
}

?>