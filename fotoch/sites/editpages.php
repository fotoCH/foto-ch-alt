<?php
include ("./mysql.inc.php");
include ("./fotofunc.inc.php");
$editpages= new XTemplate("./templates/contents.xtpl");
$editablePages=array("partner_content", "impressum_content", "kontakt_content", "sitemap_content", "handbuch_content", "handbuch_index", "home_content","home_logos");
$editpages->assign("ITEM", "<h2>".getLangContent("sprache",$_GET['lang'],"editpages")."</h2>");
$editpages->assign("LANG", $_GET['lang']);
$editpages->assign("ACTION", $_GET['a']);
//$editpages->assign("PAGE", $_GET['pages']);
$editpages->assign("DATA", getLangContent("sprache",$_GET['lang'],"editpages_comment"));//"W&auml;hlen Sie die zu bearbeitende Seite aus:");
//assign option-box
$editpages->assign("OPTION","...  ");
$editpages->parse("contents.editpages.select.option");
for($i = 0 ; $i < sizeof($editablePages); $i++) {
	$editpages->assign("OPTION",$editablePages[$i]);
	$editpages->parse("contents.editpages.select.option");
}
$editpages->parse("contents.editpages.select");

if($_POST) escpost();

if($_POST['submitbutton']!=''){
	//then the content of the fck editor wants to be saved
	$page = $_POST['page'];
	$lang = $_GET['lang'];
	$pagecontent = str_replace("'" ,"&rsquo;", $_POST['FCKeditor1']);
	
	//echo $pagecontent;
	$query = "UPDATE sprache SET $lang = '$pagecontent' WHERE name='$page'";
	$result = mysql_query($query);
	if($result){
		//show updated content...
		$query1 = "SELECT $lang FROM sprache WHERE name = '$page'";
		$result1 = mysql_query($query1);
		$editpages->assign("PAGECONTENT", htmlentities(mysql_result($result1, 0)));
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
		$result = mysql_query($query);
		$editpages->assign("PAGECONTENT", htmlentities(mysql_result($result, 0)));
		$editpages->assign("PAGE",$page);
	} else {
		// simply show the page
	}	
}
$editpages->assign("SELECT", getLangContent("sprache", $_GET['lang'],"speichern"));
$editpages->parse("contents.editpages");
$out.=$editpages->text("contents.editpages");
?>