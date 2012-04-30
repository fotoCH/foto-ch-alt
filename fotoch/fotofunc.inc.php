<?php
error_reporting(!(E_ALL));

function clean_entry($t){ // cleanup for entries
	$from=array('<br>',' & ');
	$to  =array('<br />',' &amp; ');
	return(str_replace($from,$to,trim($t)));
}

function escposts(){
	if(!get_magic_quotes_gpc()){
		foreach ($_POST as $k=>$v){
			if (!is_array($v)){
				$w=mysql_real_escape_string($v);
				if ($w!=$v) $_POST[$k]=$w;
			}
		}
	}
}

function escrequest(){
	if(!get_magic_quotes_gpc()){
		foreach ($_REQUEST as $k=>$v){
			if (!is_array($v)){
				$w=mysql_real_escape_string($v);
				if ($w!=$v) $_REQUEST[$k]=$w;
			}
		}
	}
}

function formmon($m){
	setlocale (LC_ALL, 'de_CH');
	$mon=strftime ("%B",mktime(0, 0, 0, substr($m,4,2), 1, 2000));
	return($mon.' '.substr($m,0,4));
}

function formumfeld($t){  // expandiert Links im Umfeld
	$suchmuster = "/<.link:(.\d+)>/";
	$tref=preg_match_all($suchmuster,$t,$treffer);
	//print_r($treffer);
	//$suchmuster = "llink:";

	for ($i=0; $i< count($treffer[0]); $i++) {
		$id=$treffer[1][$i];
		//$nid=$treffer[0][$i];

		if (substr($id,0,1)=='n'){
			$id=substr($id,1);
			$n=1;

			$result=mysql_query("SELECT *  FROM (fotografen INNER JOIN namen ON fotografen.id=namen.fotografen_id) WHERE namen.id=$id ORDER BY namen.id Asc");
			//echo("SELECT *  FROM (fotografen INNER JOIN namen ON fotografen.id=namen.fotografen_id) WHERE namen.id=$id ORDER BY namen.id Asc");

		} else {
			$result=mysql_query("SELECT *  FROM (fotografen INNER JOIN namen ON fotografen.id=namen.fotografen_id) WHERE fotografen_id=$id ORDER BY namen.id Asc");
		}
		$fetch=mysql_fetch_array($result);
		$name=$fetch['vorname'] .' '.$fetch['namenszusatz'].' '.$fetch['nachname'];
		// echo $name;
		if ($n==1) $id=$fetch['fotografen_id'];
		if ($fetch['unpubliziert']==1){
			if (auth_level(USER_GUEST_READER)){
				$t=str_replace($treffer[0][$i],'<span class="text2g"><a href="?a=fotograph&amp;id='.$id.'&amp;lang='.$_GET['lang'].'">'.$name.'</a></span>',$t);
			} else {
				$t=str_replace($treffer[0][$i],$name,$t);
			}
		} else {
			/* !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! fotograph  */
			$t=str_replace($treffer[0][$i],'<a href="?a=fotograph&amp;id='.$id.'&amp;lang='.$_GET['lang'].'">'.$name.'</a>',$t);
		}
	}
	return(str_replace('<br>','<br />',$t));
}

function getname($id){   // Gibt formatierten Namen mit der id zurück
	$result=mysql_query("SELECT *  FROM namen WHERE id=$id ORDER BY id Asc");
	$fetch=mysql_fetch_array($result);
	$name=$fetch['vorname'] .' '.$fetch['namenszusatz'].' '.$fetch['nachname'];

	return($name);
}

function getnamer($id){   // Gibt formatierten Namen mit der id zurück
	$result=mysql_query("SELECT *  FROM namen WHERE id=$id ORDER BY id Asc");
	$fetch=mysql_fetch_array($result);
	$name=trim($fetch['nachname'].', '.$fetch['vorname'] .' '.$fetch['namenszusatz']);

	return($name);
}

function getfon($id){   // Gibt Namen zu einer Namen id als Array zurück
	$result=mysql_query("SELECT  *  FROM (fotografen INNER JOIN namen ON fotografen.id=namen.fotografen_id) WHERE `namen`.id=$id ");
	$fetch=mysql_fetch_array($result);

	$r['namen']=$fetch['vorname'] .' '.$fetch['namenszusatz'].' '.$fetch['nachname'];
	$r['gesperrt']=$fetch['unpubliziert'];
	$r['fid']=$fetch['fotografen_id'];
	$r['namen_id']=$id;
	$r['nachname']=$fetch['nachname'];
	$r['vorname']=$fetch['vorname'];
	$r['sortn']=$fetch['nachname'].', '. $fetch['vorname'] .' '.$fetch['namenszusatz'];
	return($r);
}


function getfo($id){  // Gibt ersten Namen zu einer Forografen id als Array zurück
	$result=mysql_query("SELECT *  FROM (fotografen INNER JOIN namen ON fotografen.id=namen.fotografen_id) WHERE fotografen.id=$id ORDER BY namen.id Asc");
	$fetch=mysql_fetch_array($result);
	$r['namen']=$fetch['vorname'] .' '.$fetch['namenszusatz'].' '.$fetch['nachname'];
	$r['gesperrt']=$fetch['unpubliziert'];
	$r['id']=$fetch['id'];
	$r['fid']=$id;
	$r['nachname']=$fetch['nachname'];
	$r['vorname']=$fetch['vorname'];
	$r['sortn']=$fetch['nachname'].', '. $fetch['vorname'] .' '.$fetch['namenszusatz'];
	$r['lebensdaten']=formldatesimp($fetch['geburtsdatum'],$fetch['gen_geburtsdatum'],$fetch['todesdatum'],$fetch['gen_todesdatum']);
	return($r);
}

function getallnam($id){  // Gibt alle Namen zu einer Forografen id als Array zurück
	$result=mysql_query("SELECT fotografen.id as fid, namen.id as nid, namen.nachname, namen.vorname, namen.namenszusatz, fotografen.unpubliziert  FROM (fotografen INNER JOIN namen ON fotografen.id=namen.fotografen_id) WHERE fotografen.id=$id ORDER BY namen.id Asc");
	while ($fetch=mysql_fetch_array($result)){
		$fetch['namen']=$fetch['vorname'] .' '.$fetch['namenszusatz'].' '.$fetch['nachname'];
		$fetch['sortn']=$fetch['nachname'].', '. $fetch['vorname'] .' '.$fetch['namenszusatz'];
		$r[]=$fetch;
	}
	return($r);
}

function getinst($id){ // Institutsname aus Id
	$result=mysql_query("SELECT *  FROM institution WHERE id=$id ORDER BY id Asc");
	$fetch=mysql_fetch_array($result);
	$name=$fetch['name'];
	return($name);
}

function getinsta($id){ // Institut
	$result=mysql_query("SELECT *  FROM institution WHERE id=$id ORDER BY id Asc");
	$fetch=mysql_fetch_array($result);
	//$name=$fetch['name'];
	return($fetch);
}

function formlit($f){  // expandiert Verweise in Literatur, formatiert
	$suchmuster = "/<llink:(.\d+)>/";
	$tref=preg_match_all($suchmuster,$f['in'],$treffer);
	for ($i=0; $i< count($treffer[0]); $i++) {
		$id=$treffer[1][$i];
		$li=getlit($id);
		$f['in']=$li['text'];
	}



	$text=$f['verfasser_vorname'];
	if ($text!='') $text=', '.$text;
	$hg='';
	if ($f['code']=='H')  $hg=' (Hg.)';
	if ($f['verfasser_name']) $text=$f['verfasser_name'].$text.$hg.': ';
	if ($f['titel']) $text.=$f['titel'].', ';

	if ($f['in']) $text.='in: '.$f['in'].', ';
	if ($f['ort']){
		if ($f['verlag']){
			$text.=$f['ort'].', ';
		} else {
			$text.=$f['ort'].' ';
		}
	}
	if ($f['verlag']) $text.=$f['verlag'].' ';
	if ($f['code']=='T'){
		if ($f['nummer']) $text.=$f['nummer'].' ';
		if ($f['jahr']) $text.=$f['jahr'];
	} else {
		if ($f['jahr']) $text.=$f['jahr'];
		if ($f['nummer']) $text.=', '.$f['nummer'];
	}

	if ($f['code']=='U') {
		if ($text[strlen($text)-1]!=' ') $text.=', ';
		$f['url']=preg_replace("/http:\/\/(.*)/","<a href=\"http://\$1\" target=\"_new\">\$1</a> ",$f['url']);
		$text.=$f['url'];
	}

	if ($f['seite']){ if ($text[strlen($text)-1]!=' ') $text.=', ';
	$text.=$f['seite'];
	}
	if ($text[strlen($text)-1]!='.') $text.='.';
	$f['text']=clean_entry($text);
	return($f);
}

function formlit_alt($f){  // expandiert Verweise in Literatur, formatiert
	$suchmuster = "/<llink:(.\d+)>/";
	$tref=preg_match_all($suchmuster,$f['in'],$treffer);
	for ($i=0; $i< count($treffer[0]); $i++) {
		$id=$treffer[1][$i];
		$li=getlit($id);
		$f['in']=$li['text'];
	}



	$text=$f['verfasser_vorname'];
	if ($text!='') $text=', '.$text;
	$hg='';
	if ($f['code']=='H')  $hg=' (Hg.)';
	if ($f['verfasser_name']) $text=$f['verfasser_name'].$text.$hg.': ';
	if ($f['code']=='U') {
		$f['titel']=preg_replace("/http:\/\/(.*)/","<a href=\"http://\$1\">http://\$1</a>, ",$f['titel']);
		$text.=$f['titel']; } else {
			$text.=$f['titel'].', ';
		}

		if ($f['in']) $text.='in: '.$f['in'].', ';
		if ($f['ort']) $text.=$f['ort'].' ';
		if ($f['code']=='T'){
			if ($f['nummer']) $text.=$f['nummer'].' ';
			if ($f['jahr']) $text.=$f['jahr'];
		} else {
			if ($f['jahr']) $text.=$f['jahr'];
			if ($f['nummer']) $text.=', '.$f['nummer'];
		}
		if ($f['seite']){ if ($text[strlen($text)-1]!=' ') $text.=', ';
		$text.=$f['seite'];
		}
		if ($text[strlen($text)-1]!='.') $text.='.';
		$f['text']=$text;
		return($f);
}


function formlit2($f){
	$text=$f['verfasser_vorname'];
	if ($text!='') $text=', '.$text;
	$hg='';
	if ($f['code']=='H')  $hg=' (Hg.)';
	if ($f['verfasser_name']) $text=$f['verfasser_name'].$text.$hg.': ';
	if ($f['titel']) $text.=$f['titel'].', ';

	if ($f['in']) $text.='in: '.$f['in'].', ';
	//if ($f['ort']) $text.=$f['ort'].' ';
	if ($f['code']=='T'){
		if ($f['nummer']) $text.=$f['nummer'].' ';
		/*if ($f['jahr']) $text.=$f['jahr'];
		 } else {
		 if ($f['jahr']) $text.=$f['jahr']; */
		if ($f['nummer']) $text.=', '.$f['nummer'];
	}
	if ($f['code']=='U') {
		if ($text[strlen($text)-1]!=' ') $text.=', ';
		$f['url']=preg_replace("/http:\/\/(.*)/","<a href=\"http://\$1\" target=\"_new\">\$1</a> ",$f['url']);
		$text.=$f['url'];
	}

	if ($f['seite']){ if ($text[strlen($text)-1]!=' ') $text.=', ';
	$text.=$f['seite'];
	}
	//if ($text[strlen($text)-1]!='.') $text.='.';
	$f['text']=$text;
	return($f);
}

function formlite(&$f){  //für export
	$text=$f['verfasser_vorname'];
	if ($text!='') $text=' '.$text;
	$hg='';
	if ($f['code']=='H')  $hg=' (Hg.)';
	if ($f['verfasser_name']) $text=$f['verfasser_name'].$text.$hg.': ';
	$f['verfasser']=$text;
	return($f);
}


function formlit2_alt($f){
	$text=$f['verfasser_vorname'];
	if ($text!='') $text=', '.$text;
	$hg='';
	if ($f['code']=='H')  $hg=' (Hg.)';
	if ($f['verfasser_name']) $text=$f['verfasser_name'].$text.$hg.': ';
	if ($f['code']=='U') {
		$f['titel']=preg_replace("/http:\/\/(.*)/","<a href=\"http://\$1\">http://\$1</a>, ",$f['titel']);
		$text.=$f['titel']; } else {
			//$text.=$f['titel'].', ';
			$text.=$f['titel'];
		}

		if ($f['in']) $text.='in: '.$f['in'].', ';
		//if ($f['ort']) $text.=$f['ort'].' ';
		if ($f['code']=='T'){
			if ($f['nummer']) $text.=$f['nummer'].' ';
			/*if ($f['jahr']) $text.=$f['jahr'];
			 } else {
			 if ($f['jahr']) $text.=$f['jahr']; */
			if ($f['nummer']) $text.=', '.$f['nummer'];
		}
		if ($f['seite']){ if ($text[strlen($text)-1]!=' ') $text.=', ';
		$text.=$f['seite'];
		}
		//if ($text[strlen($text)-1]!='.') $text.='.';
		$f['text']=$text;
		return($f);
}


function getlit($id){
	$result=mysql_query("SELECT *  FROM literatur WHERE id=$id ORDER BY id Asc");
	$fetch=mysql_fetch_array($result);
	$fetch=formlit2($fetch);
	return($fetch);
}


function formaus($f){  // formatiert Ausstellung
	$text.=$f['jahr'];
	if ($f['ort']) $text.=', '.$f['ort'];
	if ($f['institution']) $text.=', '.$f['institution'];
	if ($f['titel']) $text.=', '.$f['titel'];
	$f['text']=$text;
	return($f);
}


function formdatesimp($date,$code){
	// formatiert Datum
	// Code
	// 0: TT.MM.YYYY
	// 1: YYYY
	// sonst: leer

	switch ($code){
		case 0: return substr($date,8,2).'.'.substr($date,5,2).'.'.substr($date,0,4);
		case 1: return substr($date,0,4);
		case 2: return "";
		case 8: return "";
		case 9: return "";
	}
	return date("d.m.Y",strtotime($date));
}

// Codes:
// 0: auf Tag genau bekannt
// 1: auf Jahr genau bekannt
// 2: nicht bekannt

// nur bei gcode:
// 2: gestorben
// 8: nicht beklannt, ob noch lebt
// 9: lebt noch

function formldatesimp($gdate,$gcode,$tdate,$tcode){  // formatiert lebenszeit in Liste
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
		case 22: return "&dagger;";
		case 28: return "";
		case 29: return "*";
	}
	return date("d.m.Y",strtotime($date));
}

function formldatesimp2($gdate,$gcode,$tdate,$tcode,$go,$to){ // formatiert lebenszeit in Detail
	if (!empty($go)) { $go=' '.$go; $go2='*'.$go; }
	if (!empty($to)) { $to=' '.$to; $to2=' &dagger;'.$to; }
	$code=10*$gcode+$tcode;

	switch ($code){
		case 0: return substr($gdate,8,2).'.'.substr($gdate,5,2).'.'.substr($gdate,0,4).$go. ' - ' .  substr($tdate,8,2).'.'.substr($tdate,5,2).'.'.substr($tdate,0,4).$to;
		case 1: return substr($gdate,8,2).'.'.substr($gdate,5,2).'.'.substr($gdate,0,4).$go. ' - ' .  substr($tdate,0,4).$to;
		case 2: return substr($gdate,8,2).'.'.substr($gdate,5,2).'.'.substr($gdate,0,4).$go. ' - '.$to;
		case 8: return substr($gdate,8,2).'.'.substr($gdate,5,2).'.'.substr($gdate,0,4).$go.$to;
		case 9: return '*'.substr($gdate,8,2).'.'.substr($gdate,5,2).'.'.substr($gdate,0,4).$go.$to;
		case 10: return substr($gdate,0,4).$go. ' - ' .  substr($tdate,8,2).'.'.substr($tdate,5,2).'.'.substr($tdate,0,4).$to;
		case 11: return substr($gdate,0,4).$go. ' - ' .  substr($tdate,0,4).$to;
		case 12: return substr($gdate,0,4).$go. ' - '.$to;
		case 18: return substr($gdate,0,4).$go.$to;
		case 19: return '*'.substr($gdate,0,4).$go.$to;
		case 20: return $go2.' - ' .  substr($tdate,8,2).'.'.substr($tdate,5,2).'.'.substr($tdate,0,4).$to;
		case 21: return $go2.' - ' .  substr($tdate,0,4).$to;
		case 22: return $go2." ".$to2;
		case 28: return $go2." ".$to2;
		case 29: return $go2." ".$to2;
	}
	return date("d.m.Y",strtotime($date));
}

function rformdate($date){
	$ndate=preg_replace( '/(\d{1,2})\W(\d{1,2})\W(\d{4})/', '$3-$2-$1', $date);
	return ($ndate);

}

function auth2(){
	global $s_uid;
	global $s_pw;
	if(mysql_num_rows(mysql_query("SELECT * FROM g_usr WHERE usr_uid='$s_uid' AND usr_pw=PASSWORD('$s_pw') AND usr_rechte!='gast'"))!=0){
		return true;
	}else{
		return false;
	}
}


function normfeld($tpl,$label,$value){
	if ($value!=''){
		$tpl->assign("label",$label);
		$tpl->assign("value",$value);
		$tpl->assign('g','');
		$tpl->parse('autodetail.z.sfeld');
		$tpl->parse('autodetail.z');
		abstand($tpl);
	}
}

function normfeldg($tpl,$label,$value,$g){  //  rot darstellen wenn g=1
	if ($value!=''){
		$tpl->assign("label",$label);
		$tpl->assign('g',($g==1?'g':''));
		$tpl->assign("value",$value);
		$tpl->parse('autodetail.z.sfeld');
		$tpl->parse('autodetail.z');
		abstand($tpl);
	}
}


function normfelda($tpl,$label,$value){
	if (!empty($value) && trim($value)!=''){
		$tpl->assign("label",$label);
		$tpl->assign("value",$value);
		$tpl->parse('autodetail.z.sfeld');
		$tpl->parse('autodetail.z');
		abstand($tpl);
	}
}

function abstand($tpl){
	$tpl->parse('autodetail.z.abstand');
	$tpl->parse('autodetail.z');
}

function getsparr(){
	return array('de','fr','it','rm','en');
}

function clangex(){
	global $clanguage;
	return ($clanguage=='de'?'':'_'.$clanguage);
}

function clangexl($l){
	return ($l=='de'?'':'_'.$l);
}

function clangcont(&$fetch, $key){
	global $language;
	global $clanguage;
	$l=$language;
	if ($_GET['clang']){
		$l=$clanguage;
		$fixed=true;
	}
	$lprefs=array(
		'de'=>array('en','fr','it','rm'),
		'fr'=>array('it','de','en','rm'),
		'it'=>array('fr','de','en','rm'),
		'en'=>array('de','fr','it','rm'),
		'rm'=>array('fr','it','de','en')
	);
	$e=$fetch[$key.clangexl($l)];
	if ($e) return $e;
	$a=0;
	while (!($e=$fetch[$key.clangexl($lprefs[($fixed?$clanguage:$language)][$a])]) && ($a<3)){
		$a++;
	}
	return($e);
}

function getlanglink($s,$link){
	global $clanguage;
	return ($clanguage==$s?$s.'&nbsp;':$link.$s.'">'.$s.'</a>&nbsp;');
}


function checklangsf(&$fetch,$felder,$link){
	global $spr;
	$link.='&amp;clang=';
	$os=$fetch['originalsprache'];
	$res=$spr['originalsprache'].': '.getlanglink($os,$link);
	$do=false;
	foreach (getsparr() as $sp){
		$da=false;
		foreach ($felder as $feld){
			if ($os!=$sp){
				if ($fetch[$feld.($sp=='de'?'':'_'.$sp)]){
					$da=true;
					if ($do==false){
						$res.= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$spr['artikelvorhandenin'].': ';
					}
					$do=true;
				}
			}
		}
		if ($da){
			$res.=getlanglink($sp,$link);
		}
	}
	if (!$do){
		if ($os=='de') $res='';
	}
	return $res;
}

function gensprachaus($link){
	global $spr;
	$link.='&amp;clang=';
	$res='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$spr['sprache'].' '.$spr['anzeigen'].': ';
	foreach (getsparr() as $sp){
		$res.=getlanglink($sp,$link);
	}
	return $res;
}

function dom_dump($obj) {
	if ($classname = get_class($obj)) {
		$retval = "Instance of $classname, node list: \n";
		switch (true) {
			case ($obj instanceof DOMDocument):
				$retval .= "XPath: {$obj->getNodePath()}\n".$obj->saveXML($obj);
				break;
			case ($obj instanceof DOMElement):
				$retval .= "XPath: {$obj->getNodePath()}\n".$obj->ownerDocument->saveXML($obj);
				break;
			case ($obj instanceof DOMAttr):
				$retval .= "XPath: {$obj->getNodePath()}\n".$obj->ownerDocument->saveXML($obj);
				//$retval .= $obj->ownerDocument->saveXML($obj);
				break;
			case ($obj instanceof DOMNodeList):
				for ($i = 0; $i < $obj->length; $i++) {
					$retval .= "Item #$i, XPath: {$obj->item($i)->getNodePath()}\n".
"{$obj->item($i)->ownerDocument->saveXML($obj->item($i))}\n";
				}
				break;
			default:
				return "Instance of unknown class";
		}
	} else {
		return 'no elements...';
	}
	return htmlspecialchars($retval);
}


function parseRDF($r){
	$nsp='http://www.w3.org/1999/02/22-rdf-syntax-ns#';
	$rdagr2='http://metadataregistry.org/uri/schema/RDARelationshipsGR2/';
	$pndpre="http://d-nb.info/gnd/";
	$res=array();
	foreach ($r->getElementsByTagNameNS($nsp, 'Description') as $element) {
		$pndc=$element->getAttributeNS($nsp,'about');
		if (!(strpos($pndc,$pndpre)==0)) continue;
		$pndc=substr($pndc,strlen($pndpre));

		//echo 'local name: ', $element->localName, ', prefix: ', $element->prefix, "about: ",$element->getAttributeNS($nsp,'about'), "\n";

		$resp=array();
		//echo $pndc;
		foreach ($element->getElementsByTagNameNS('*','*') as $element) {
				
			$at_res=$element->getAttributeNS($nsp,'resource');
			if ($at_res){
				$value=$at_res;
			} else {
				$at_res=$element->getAttributeNS($nsp,'parseType');
				if ($at_res) continue;
				$value=$element->nodeValue;
			}
			//echo 'local name: ', $element->localName, ', prefix: ', $element->prefix, "value: ",$value,"<br />\n";
			$resp[$element->localName]=$value;
		}
		$res[$pndc]=$resp;

	}
	//  $attrs = $element->attributes;
	//   foreach ($attrs as $attrName => $attrNode){  // attributes
	//   	echo($attrName."::".$attrNode);
	//    }
	//	}
	//	$rdf=$r->ownerDocument->saveXML($r);
	//	//echo $rdf;
	//	$dom = new DomDocument();
	//	$dom->loadXML($rdf);
	//	foreach ($dom->getElementsByTagNameNS('http://www.w3.org/1999/02/22-rdf-syntax-ns#', 'Description') as $element) {
	//    echo 'local name: ', $element->localName, ', prefix: ', $element->prefix, "\n";
	//    echo "XXX";
	//  $attrs = $element->attributes;
	//   foreach ($attrs as $attrName => $attrNode){  // attributes
	//   	echo($attrName."::".$attrNode);
	//    }
	//	}
	//	$nr=$dom->getElementsByTagNameNS("rdf","Description")->item(1);
	//	//echo dom_dump($nr);

	return($res);
}

function rank(&$a, &$fetch){
	$r=0;
	if ($a['professionOrOccupation']=='http://d-nb.info/gnd/4045893-3'){ // Photograph
		$r+=10;
	}
	if ($a['professionOrOccupation']=='http://d-nb.info/gnd/4262252-9'){ // Photographin
		$r+=10;
	}
	if ($a['countryCodeForThePerson']=='XA-CH'){
		$r+=5;
	}

	if (stripos($a['biographicalInformation'],'photo')>0){
		$r+=10;
	}
	if ($a['surname'] && $a['foreName']){
		if (($fetch['nachname']==$a['surname']) && ($fetch['vorname']==$a['foreName']))
		$r+=5;
		else
		$r-=5;
	}
	if ($a['dateOfBirth'] && substr($fetch['geburtsdatum'],0,4)!='0000'){
		if (substr($fetch['geburtsdatum'],0,4)==$a['dateOfBirth'])
		$r+=5;
		else
		$r-=15;
	}
	if ($a['dateOfDeath'] && substr($fetch['todesdatum'],0,4)!='0000'){
		if (substr($fetch['todesdatum'],0,4)==$a['dateOfDeath'])
		$r+=5;
		else
		$r-=15;
	}
	if ($a['placeOfBirth'] && $fetch['geburtsort']!=''){
		if ($fetch['geburtsort']==$a['placeOfBirth'])
		$r+=5;
		else
		$r-=5;
	}
	if ($a['placeOfDeath'] && $fetch['todesort']!=''){
		if ($fetch['todessort']==$a['placeOfDeath'])
		$r+=5;
		else
		$r-=5;
	}
	return $r;
}

function getPNDfromName($nid, &$fetch){
	$ret=array();
	$n=getnamer($nid);
	echo $n, "<br />\n";
	$query="http://services.d-nb.de/sru/pnd?version=1.1&operation=searchRetrieve&query=PER%3D".urlencode(utf8_encode($n))."&recordSchema=RDFxml";
	echo $query;
	//$ans=file_get_contents($query);
	$dom = new DomDocument();
	$dom->load($query);
	$nr=$dom->getElementsByTagName("record");
	$c=0;
	echo ($nr->length. "Ergebnisse: \n");
	foreach($nr as $r){
		$a=parseRDF($r);
		foreach ($a as $k=>$v){
			$a[$k]['rank']=rank($v,$fetch);
		}
		print_r($a);
		$ret[]=$a;
	}

	//echo dom_dump($dom);
	//echo dom_dump($nr);

	//echo $ans;
	//$a=parseRDF($ans);
	return $ret;
}

function datest(&$da){
	if (!$da) return;
	$d=str_replace(' ','',$da);
	$a=explode('.',$d);
	if (strlen($a[2])==2) $a[2]="20".$a[2];
	$da=sprintf('%02d',$a[0]).'.'.sprintf('%02d',$a[1]).'.'.sprintf('%04d',$a[2]);
}

function datumtest(){ // testet $REQUEST vondatum und bisdatum auf falsch formatierung
	datest($_REQUEST['vondatum']);
	datest($_REQUEST['bisdatum']);
}


?>