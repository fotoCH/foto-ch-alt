<?php
function pushfields(&$o,$i,$list){
	foreach ($list as $l){
		$o[$l]=$i[$l];
	}
}

function jsonout($out){
	$json = json_encode ( $out );
	if (array_key_exists ( 'callback', $_GET )) {
		
		header ( 'Content-Type: text/javascript; charset=utf8' );
		header ( 'Access-Control-Allow-Origin: http://www.example.com/' );
		header ( 'Access-Control-Max-Age: 3628800' );
		header ( 'Access-Control-Allow-Methods: GET, POST, PUT, DELETE' );
		
		$callback = $_GET ['callback'];
		echo $callback . '(' . $json . ');';
	} else {
		// normal JSON string
		header('Access-Control-Allow-Origin: *');
		header ( 'Content-Type: application/json; charset=utf8' );
		
		echo $json;
	}

}

?>
