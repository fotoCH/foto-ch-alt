<?php
include ("./mysql.inc.php");
include ("./fotofunc.inc.php");
testauthedit();
$editpages= new XTemplate("./templates/contents.xtpl");
$editablePages=array("partner_content", "impressum_content", "ueberuns_content", "ueberuns_index", "sitemap_content", "handbuch_content", "handbuch_index", "home_content","home_logos");
$editpages->assign("ITEM", "<h2>".$spr['editpages']."</h2>");
$editpages->assign("LANG", $_GET['lang']);
$editpages->assign("ACTION", $_GET['a']);
//$editpages->assign("PAGE", $_GET['pages']);
$editpages->assign("DATA", $spr['editpages_commend']);//"W&auml;hlen Sie die zu bearbeitende Seite aus:");
//assign option-box
$editpages->assign("OPTION","...  ");
$editpages->parse("contents.editpages.select.option");
for($i = 0 ; $i < sizeof($editablePages); $i++) {
	$editpages->assign("OPTION",$editablePages[$i]);
	$editpages->parse("contents.editpages.select.option");
}
$editpages->parse("contents.editpages.select");

if ($_POST){
 escposts();
}

if($_POST['submitbutton']!=''){
	//then the content of the fck editor wants to be saved
	$page = $_POST['page'];
	$lang = $_GET['lang'];
	$pagecontent = str_replace("'" ,"&rsquo;", $_POST['FCKeditor1']);
	
	//echo $pagecontent;
	$query = "UPDATE sprache SET $lang = '$pagecontent' WHERE name='$page'";
	$result = mysqli_query($sqli, $query);
	if($result){
		//show updated content...
		$query1 = "SELECT $lang FROM sprache WHERE name = '$page'";
		$result1 = mysqli_query($sqli, $query1);
		$editpages->assign("PAGECONTENT", htmlspecialchars(mysqli_result($result1, 0)));
		$editpages->assign("PAGE", $_POST['page']);
	}
	else {
		$editpages->assign("PAGECONTENT", "<h1>Ein Fehler ist aufgetreten!</h1><p>&Uuml;berpr&uuml;fen Sie die Eingabe auf Sonderzeichen!");		
	}
		
} else {
	if($_GET['pages']!=''){
		//user has chosen site -> display in editor
		$lang = $_GET['lang'];
		$page = $_GET['pages'];
		$query = "SELECT $lang FROM sprache WHERE name = '$page'";
		$result = mysqli_query($sqli, $query);
		$editpages->assign("PAGECONTENT", htmlspecialchars(mysqli_result($result, 0)));
		$editpages->assign("PAGE",$page);
	} else {
		// simply show the page
	}	
}
$editpages->assign("SELECT", $spr['speichern']);
$editpages->parse("contents.editpages");
$out.=$editpages->text("contents.editpages");
?>
