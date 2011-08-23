<?php
$wusers=array();

function initwar(){
	global $wusers;
	if (count($wusers)>0) return;
	$wusers[0]='';
	$sql="SELECT * FROM `users` WHERE `level` >= 8 ORDER BY username";
	$result = mysql_query($sql);
	while ($fetch = mysql_fetch_array($result)){
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