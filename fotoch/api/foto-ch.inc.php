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

function formlebensdaten($gdate,$gcode,$tdate,$tcode){  // formatiert lebenszeit in Liste
        $code=10*$gcode+$tcode;
        switch ($code){
                case 0: return substr($gdate,8,2).'.'.substr($gdate,5,2).'.'.substr($gdate,0,4). ' - ' .  substr($tdate,8,2).'.'.substr($tdate,5,2).'.'.substr($tdate,0,4);
                case 1: return substr($gdate,8,2).'.'.substr($gdate,5,2).'.'.substr($gdate,0,4). ' - ' .  substr($tdate,0,4);
                case 2: return substr($gdate,8,2).'.'.substr($gdate,5,2).'.'.substr($gdate,0,4). ' - ';
                case 8: return substr($gdate,8,2).'.'.substr($gdate,5,2).'.'.substr($gdate,0,4);
                case 9: return '*'.substr($gdate,8,2).'.'.substr($gdate,5,2).'.'.substr($gdate,0,4);
                case 10: return substr($gdate,0,4). ' - ' .  substr($tdate,8,2).'.'.substr($tdate,5,2).'.'.substr($tdate,0,4);
                case 11: return substr($gdate,0,4). ' - ' .  substr($tdate,0,4);
                case 12: return substr($gdate,0,4). ' - ';
                case 18: return substr($gdate,0,4);
                case 19: return '*'.substr($gdate,0,4);
                case 20: return ' - ' .  substr($tdate,8,2).'.'.substr($tdate,5,2).'.'.substr($tdate,0,4);
                case 21: return ' - ' .  substr($tdate,0,4);
                case 22: return '†';
                case 28: return "";
                case 29: return "*";
        }
        return date("d.m.Y",strtotime($date));
}

?>
