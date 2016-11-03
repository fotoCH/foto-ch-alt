<?php
$wusers=array();

function initwar(){
	global $wusers;
	global $sqli;
	if (count($wusers)>0) return;
	$wusers[0]='';
	$sql="SELECT * FROM `users` WHERE `level` >= 8 ORDER BY username";
	$result = mysqli_query($sqli, $sql);
	while ($fetch = mysqli_fetch_array($result)){
		$wusers[$fetch['id']]=$fetch['username'];
	}
	//print_r($wusers);
	return;
}

function getuserbox($name, $u){ // selectbox mit ausgewähletem user
	initwar();
	global $wusers;
	$o='<select name="'.$name.'" id="'.$name.'" class="edit" >';
	foreach ($wusers as $k=>$v){
           $o.='<option '.($u==$v?'selected="selected" ':'').'value="'.$k.'" class="edit">'.$v.'</option>';
      }
     $o.='</select>'; 
	return($o);
}

function getusername($id){
	initwar();
	global $wusers;
	return($wusers[$id]);
}
?>