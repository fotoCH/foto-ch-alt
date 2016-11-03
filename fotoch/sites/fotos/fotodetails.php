<?php


$xtpl_fotodetails=new XTemplate ("././templates/item_details.xtpl");
$xtpl_fotodetails->assign("ACTION",$_GET['a']);
$xtpl_fotodetails->assign("ID",$_GET['id']);
$id = $_GET['id'];
$lang = $_GET['lang'];
$xtpl_fotodetails->assign("LANG", $_GET['lang']);
$xtpl_fotodetails->assign("TITLE", $spr['photos']);
$xtpl_fotodetails->assign("SPR",$spr);

$select = 'i.id AS institution_id, b.id AS stock_id, n.fotografen_id AS photograph_id, f.supplier_id as supp_id, f.dc_title AS titel, f.dc_description AS description, f.all as alles, CONCAT(n.vorname, " ", n.nachname) AS fotograph, f.dc_created, f.zeitraum, f.dc_coverage AS coverage, i.name AS institution, b.name AS bestand, f.dc_right AS copy, f.dcterms_medium AS medium, f.dc_identifier AS img_url, f.image_path, f.dcterms_spatial AS keywords, f.dcterms_subject AS subject';

$join .= "LEFT JOIN namen AS n ON f.dc_creator=n.fotografen_id ";
$join .= "LEFT JOIN institution AS i ON f.edm_dataprovider=i.id ";
$join .= "LEFT JOIN bestand AS b ON f.dcterms_ispart_of=b.id ";

$query = "SELECT DISTINCT $select FROM fotos AS f $join WHERE f.id=$id";
$objResult=mysqli_query($sqli, $query);

// prepare the metadata
while($arrResult=mysqli_fetch_assoc($objResult)){
	$photographID = $arrResult['photograph_id'];
	$institutionID = $arrResult['institution_id'];
	$stockID = $arrResult['stock_id'];
	$photoPath = $arrResult['image_path'];
	$alles = $arrResult['alles'];
	$supp_id = $arrResult['supp_id'];
	foreach ($arrResult as $key=>$value){
		if ($value==''){
			$value=$spr['not_available'];
		}
		$isOutput = true;
		switch ($key) {
			case 'titel':
				$value = $value.(($value!='' && $arrResult['description']!='') ? ' / ' : '').$arrResult['description'];
				$img_alt = $value;
				break;
			case 'subject':
			case 'image_path':
			case 'description':
			case 'institution_id':
			case 'stock_id':
			case 'photograph_id':
			case 'dc_created':
			case 'supp_id':
			case 'alles':
				$isOutput = false;
				break;
			case 'fotograph':
				if ($value == ' Neueintrag'){
					$value=$spr['unbekannt'];
					break;
				}
				$value = '<a href="?a=fotograph&id='.$photographID.'&lang='.($lang? $lang : 'de').'">'.$value.'</a>';
				break;
			case 'institution':
				if ($value == $spr['not_available']){
					break;
				}
				$value = '<a href="?a=institution&id='.$institutionID.'&lang='.($lang? $lang : 'de').'">'.$value.'</a>';
				$instbest=$value.': ';
				break;
			case 'bestand':
				if ($value == $spr['not_available']){
					break;
				}
				$value = '<a href="?a=bestand&id='.$stockID.'&lang='.($lang? $lang : 'de').'">'.$value.'</a>';
				$instbest.=$value;
				break;
			case 'img_url':
				$img_url=$value;
				$value = '<a href="'.$value.'" target="_blank">'.$value.'</a>';
				break;
			case 'zeitraum':
				if ($value!='') {
					//$value = date('Y', mktime(0,0,0,1,1,$value));
				} else {
					$value = $spr['not_available'];
				}
				break;
			case 'keywords':
				$value = $value.(($value!='' && $arrResult['subject']!='') ? ' , ' : '').$arrResult['subject'];
				break;

		}
		if($isOutput && !empty($value)) {
			$xtpl_fotodetails->assign("key",$spr[$key]);
			$xtpl_fotodetails->assign("value", $value);
			$xtpl_fotodetails->parse("autodetail.z.autorow");
			$xtpl_fotodetails->parse("autodetail.z");
			abstand($xtpl_fotodetails);
		}
	}
}

if ($institutionID==118){
    $xtpl_fotodetails->assign("key","Rero"); 
    $rero=$supp_id;
//    $xtpl_fotodetails->assign("value",'<a href="http://explore.rero.ch/primo_library/libweb/action/display.do?tabs=detailsTab&ct=display&doc=VTLS_RERO'.$rero.'" target="_new">link</a>');
      $xtpl_fotodetails->assign("value",'<a href="http://explore.rero.ch/primo_library/libweb/action/display.do?tabs=detailsTab&ct=display&fn=search&doc=VTLS_RERO'.$rero.'&indx=1&recIds=VTLS_RERO'.$rero.'&recIdxs=0&elementId=0&renderMode=poppedOut&displayMode=full&frbrVersion=&dscnt=3&scp.scps=scope%3A%28VTLS_GE%29%2Cscope%3A%28VTLS_IF%29%2Cscope%3A%28VTLS_NJ%29%2Cscope%3A%28rerodocmarc%29%2Cscope%3A%28SFX_RERO%29%2Cscope%3A%28VTLS_RERO%29%2Cscope%3A%28VTLS_FR%29%2Cscope%3A%28VTLS_VS%29%2Cscope%3A%28VTLS_VD%29%2Cprimo_central_multiple_fe&frbg=&tab=default_tab&srt=rank&mode=Basic&dum=true&fromLogin=true&vid=RERO_V1" target="_new">'.$rero.'</a>');
    $xtpl_fotodetails->parse("autodetail.z.autorow");
    $xtpl_fotodetails->parse("autodetail.z");
    abstand($xtpl_fotodetails);
}


if (auth_level(USER_WORKER)){
    $xtpl_fotodetails->assign("key","Alle Informationen");
    $xtpl_fotodetails->assign("value",'<a href="ajax.php?action=fotoAll&amp;form=1&amp;id='.$id.'" target="_new">hier</a>');
    $xtpl_fotodetails->parse("autodetail.z.autorow");
    $xtpl_fotodetails->parse("autodetail.z");
    abstand($xtpl_fotodetails);
}



// render the photo
$instbest.='<a class="ilogo" href="?a=institution&id='.$institutionID.'&lang='.($lang? $lang : 'de').'"><img src="Logos/institutionen/'.$institutionID.'.png"></a>';
$xtpl_fotodetails->assign("InstBest", $instbest);
$xtpl_fotodetails->assign("photo_src", $photoPath);
$xtpl_fotodetails->assign("photo_alt", $img_alt);
$xtpl_fotodetails->assign("bildurl", $img_url);
$xtpl_fotodetails->parse('autodetail.photo');

$xtpl_fotodetails->parse('autodetail');
$results.=$xtpl_fotodetails->text("autodetail");

// generate the links to the previous and next photo according to the GET parameters
foreach($_GET as $key=>$value) {
	if ($value==''){
		break;
	}
	switch ($key) {
		case 'photograph':
			if (!is_numeric($value)){
				// get the id of the photographer from the passed name
				$arrName = explode(' ', $value);
				if (count($arrName) > 1){
					$query = "SELECT id FROM namen WHERE ((nachname LIKE '%$arrName[0]%' AND vorname LIKE '%$arrName[1]%') OR (vorname LIKE '%$arrName[0]%' AND nachname LIKE '%$arrName[1]%')) LIMIT 0,1";
				} else {
					$query = "SELECT id FROM namen WHERE (nachname LIKE '%$arrName[0]%' OR vorname LIKE '%$arrName[0]%') LIMIT 0,1";
				}

				$objResult = mysqli_query($sqli, $query);
				$value = mysqli_fetch_assoc($objResult);
				$value = $value['id'];
			}
			$where = ($where!='' ? ' AND ' : '')."dc_creator=$value";
			break;
		case 'period_start':
			$period = "$value-01-01";
			$where .= ($where!='' ? ' AND ' : '')."dc_created >= '$period'";
			break;
		case 'period_end':
			$period = "$value-12-31";
			$where .= ($where!='' ? ' AND ' : '')."dc_created >= '$period'";
			break;
		case 'title':
			$where .= ($where!='' ? ' AND ' : '')."(dc_title LIKE '%$value%' OR dc_description LIKE '%$value%' OR dc_coverage LIKE '%$value%')";
			break;
		case 'institution':
			$where .= ($where!='' ? ' AND ' : '')."edm_dataprovider='$value'";
			break;
		case 'bestand':
			$where = ($where!='' ? ' AND ' : '')."dcterms_ispart_of=$value";
			break;
	}
}

$query = "SELECT id FROM fotos".($where!='' ? ' WHERE '.$where : '');
$nextQuery = $query.($where!='' ? ' AND ' : ' WHERE ')."id>$id LIMIT 0,1";
$objNextResult = mysqli_query($sqli, $nextQuery);
if (mysqli_num_rows($objNextResult)>0){
	$result = mysqli_fetch_assoc($objNextResult);
	$url = str_replace('&id='.$id, '&id='.$result['id'], $_SERVER['REQUEST_URI']);
	$next = '<a href="'.$url.'">'.$spr['next_photo'].'</a>';
	$xtpl_fotos->assign("next_link", $next);
}
$previousQuery = $query.($where!='' ? ' AND ' : ' WHERE ')."id<$id ORDER BY id DESC LIMIT 0,1";
$objPreviousResult = mysqli_query($sqli, $previousQuery);
if (mysqli_num_rows($objPreviousResult)>0){
	$result = mysqli_fetch_assoc($objPreviousResult);
	$url = str_replace('&id='.$id, '&id='.$result['id'], $_SERVER['REQUEST_URI']);
	$xtpl_fotos->assign("previous_link", '<a href="'.$url.'">'.$spr['previous_photo'].'</a>'.($next!='' ? ' | ' : ''));
}
$xtpl_fotos->parse('contents.content_detail.nav_panel');
?>
