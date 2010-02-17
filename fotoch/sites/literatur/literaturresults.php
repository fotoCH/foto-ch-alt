<?php
	
	$def=new XTemplate ("././templates/list_results.xtpl");
	$def->assign("PHP_SELF",$_SERVER['PHP_SELF']);
	$def->assign("ACTION",$_GET['a']);
	$def->assign("ID",$_GET['id']);
	$def->assign("LANG", $_GET['lang']);
	$lang = $_GET['lang'];
	$def->assign("title", getLangContent("sprache",$_GET['lang'], "literatur"));
	
	$def->assign("SPR",$spr);
	
	
	$def->assign("BEARBEITEN", "&nbsp;&nbsp;[&nbsp;".$spr['bearbeiten']."&nbsp;]");
	
	
	
	$neuerEintrag = "[&nbsp;".$spr['neuereintrag']."&nbsp;]";
	$volltext = $_GET['volltext'];
	$id=$_GET['id'];
	$anf=$_GET['anf'];
	testauth();
	$def->parse("list.head_literatur");
	// Select: code
	if ($anf=='keiner'){ $result=mysql_query("SELECT * FROM literatur WHERE ISNULL(verfasser_name)  ORDER BY  titel Asc"); } 
	elseif ($anf=='in'){ $result=mysql_query("SELECT * FROM literatur WHERE code='Z'  ORDER BY  verfasser_name, verfasser_vorname, titel Asc"); } 
	elseif ($anf=='zeitschrift'){ $result=mysql_query("SELECT * FROM literatur WHERE (code='P') OR (code='T')  ORDER BY  code,verfasser_name, verfasser_vorname, titel Asc"); } 
	elseif ($anf=='url'){ $result=mysql_query("SELECT * FROM literatur WHERE (code='U') ORDER BY  code,verfasser_name, verfasser_vorname, titel Asc"); } 
	
	else { 
		
		if($volltext!=''){
			$result=mysql_query("SELECT * FROM literatur WHERE verfasser_name LIKE '%$volltext%' OR verfasser_vorname LIKE '%$volltext%' OR titel LIKE '%$volltext%' OR ort LIKE '%$volltext%' OR jahr LIKE '%$volltext%' ORDER BY titel, verfasser_name, verfasser_vorname ASC"); 
		}
	
		elseif ($anf!=''){
			$result=mysql_query("SELECT * FROM literatur WHERE verfasser_name LIKE '$anf%'  ORDER BY verfasser_name, verfasser_vorname, titel Asc"); 
		}
		else {
			$def->parse("list.head_literatur");
		}
	}
	while($fetch=mysql_fetch_array($result)){
		$fetch['verfasser']=$fetch['verfasser_name'];
		if ($fetch['verfasser_vorname']) $fetch['verfasser'].=', '.$fetch['verfasser_vorname'];
		$def->assign("FETCH",$fetch);
		$def->parse("list.row_literatur");
	}
	
	
	$def->parse("list");
	
	$results.=$def->text("list");
?>
