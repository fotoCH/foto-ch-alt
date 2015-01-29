<?php

$searchmodes = array("ein","erw");
$searchmode=$_GET['mod'];
if(! in_array($searchmode,$searchmodes)) {
	$searchmode="ein";
}

#error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
//ini_set ('error_reporting', E_ALL);
$def=new XTemplate ("././templates/search.xtpl");


$language = $_GET['lang'];
$def->assign("LANG",$language);
$def->assign("SEARCHMODE",$searchmode);
$def->assign("SPR",$spr);

#error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
#ini_set ('error_reporting', E_ALL);
//$def=new XTemplate ("./templates/isearch.xtpl");
$def->assign("ACTION",$_GET['a']);
$def->assign("ID",$_GET['id']);
$id=$_GET['id'];

if(auth_level(USER_WORKER)){

	$def->assign("NEU","<a href=\"./?a=iedit&amp;id=new&amp;lang=$language\">[&nbsp;".$spr['neuereintrag']."&nbsp;]</a><br><br>");
	
	$def->parse("ayax_i");
	$text.=$def->text("ayax_i");
	$def->assign("AJAXBAR", "$text<br />");	
	//$search.=$def->text("ayax_f");	
}


for ($an=ord('A');$an<=ord('Z');$an++){
	$def->assign("anf",chr($an));
	$def->assign('ts',($an==ord('Z')?'':''));
	$def->parse("suchen.abuch");
}
	(!auth_level(1))? $def->assign("NEU", "<h4>" . $spr['institution'] . " " . $spr['submit'] . "</h4>"):"";
	$fetch[test]="";
	
	$kantone = getKantone();
	$bildgattungen = getBildgattungen();
	
	
	//subgensubmit($def,'submitfield',$submit);
	subgenformitem($def,'textfield',$spr['name']."/".$spr['abkuerzung'],$fetch[test],'name');
	
	subgenformitem($def,'textfield',$spr['ort'],$fetch[test],'ort');
	subgenformitem($def,'textfield',$spr['sammlungsgeschichte']."/ ".$spr['sammlungsbeschreibung'],$fetch[test],'sammlungsgeschichte');	

	subgenselectitem($def, $spr['kanton'], "", "kanton", $kantone, "", "", "", "");
	subgenselectitem($def, $spr['bildgattungen'], "3", "bildgattungen[]", $bildgattungen, true, "", "", "8");
	
	subgenformitem($def,'edittext',$spr['volltextsuche'],$fetch[test],'volltext');


	subgensubmit($def,'submitfield',$spr['submit']);
	//
	//$suche = "<a href=\"./?a=repertorium&amp;mod=erw\">erweiterte Suche</a>";
	
	//$def->assign("SUCHE", $suche);
	$def->parse("suchen");
	//echo $search;
//}

	/**
	 * Bestandessuche darunter, wenn nicht eingeloggt
	 */
	
	if(!auth_level(1)){
	
		//alphabetische suche
		for ($an=ord('A');$an<=ord('Z');$an++){
			$def->assign("anf",chr($an));
			$def->parse("suchen.abuch");
			//$def->out("list");
		}
		$def->assign("NEU", "<h4>" . $spr['bestand'] . " " . $spr['submit'] . "</h4>");
		$def->assign("ACTION", "bestand");
			
		// suche nach name
		subgenformitem($def,'textfield',$spr['name'],$fetch[test],'name');
	
		// suche nach bestandsbeschreibung
		subgenformitem($def,'textfield',$spr['bestandsbeschreibung'],$fetch[test],'bestandsbeschreibung');
	
		//volltextsuche
		subgenformitem($def,'edittext',$spr['volltextsuche'],$fetch[test],'volltext');
	
		// suche nach bildgattung
		$bildgattungen = getBildgattungen();
		subgenselectitem($def, $spr['bildgattungen'], "2", "bildgattungen[]", $bildgattungen, true, "", "", "8");
	
		//such button
		subgensubmit($def,'submitfield',$spr['submit']);
	
		//write to template
		$def->parse("suchen");
	}
		//write to $out.
		$search.=$def->text("suchen");

	
	function getKantone() {
		$type = mysql_query( "SHOW COLUMNS FROM institution WHERE Field = 'Kanton'" );
		while($i = mysql_fetch_object($type)) {
			$type=$i->Type;break;
		}
		preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
		$enum = explode("','", $matches[1]);
		foreach($enum as $row) {
			$arr[$row] = $row;
		}
		return $arr;
	}
	
	function getBildgattungen() {
		$type = mysql_query( "SHOW COLUMNS FROM institution WHERE Field = 'bildgattungen_set'" );
		while($i = mysql_fetch_object($type)) {
			$type=$i->Type;break;
		}
		preg_match("/^set\(\'(.*)\'\)$/", $type, $matches);
		$enum = explode("','", $matches[1]);
		return $enum;
	}
?>

