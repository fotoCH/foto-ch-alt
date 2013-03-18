<?php

$xtpl_fotolist=new XTemplate ("././templates/fotos_list.xtpl");
$xtpl_fotolist->assign("ACTION",$_GET['a']);
$xtpl_fotolist->assign("LANG",$_GET['lang']);
$xtpl_fotolist->assign("SPR",$spr);
$id=$_GET['id'];

$xtpl_fotolist->assign("SPR",$spr);

/* build a query similar to:
SELECT b.name AS stock, i.name AS institution, n.nachname AS name, n.vorname AS firstname, f.dc_created AS created, f.dc_title AS title, f.dc_description AS description
FROM fotos AS f
JOIN namen AS n
ON f.dc_creator=n.fotografen_id
JOIN institution AS i
ON f.edm_dataprovider=i.id
JOIN bestand AS b
ON b.inst_id=i.id
WHERE n.vorname LIKE 'Paul'
*/
if ($_GET['submitbutton']!=""){
	$issearch=3;

	$vars=array();
	$vars=$_GET;
	unset($vars['a']);
	unset($vars['submitbutton']);
	unset($vars['lang']);
	unset($vars['mod']);
	
	foreach ($vars as $key=>$value){
		if (!empty($vars[$key])){
            switch ($key){
                case 'fotograph':
                    $arrStr = explode(' ', $value);     // TODO what about names with more than three parts? e.g. Marco von Allmen
                    $select .= 'n.nachname AS name, n.vorname AS firstname, ';
                    $join .= "JOIN namen AS n ON f.dc_creator=n.fotografen_id ";
                    $where .= (empty($where) ? '' : ' AND ')."(n.nachname LIKE '%$arrStr[0]%' AND n.vorname LIKE '%$arrStr[1]%') OR (n.vorname LIKE '%$arrStr[0]%' AND n.nachname LIKE '%$arrStr[1]%')";
                    break;
                case 'period_start':
//                    var_dump((int) $value);
//                    var_dump(mktime((int) $value));
//                    var_dump(date('Y-m-d', strtotime($value)));
//                    $where .= "dc_created >= $value";
                    break;
                case 'period_end':
//                    $where .= "dc_created <= $value";
                    break;
                case 'title':
                    $where .= (empty($where) ? '' : ' AND ')."f.dc_title LIKE '%$value%' OR f.dc_description LIKE '%$value%'";
                    break;
                case 'institution':
                    $select .= 'i.name AS institution, ';
                    $join .= "JOIN institution AS i ON f.edm_dataprovider=i.id ";
                    $where .= (empty($where) ? '' : ' AND ')."i.name LIKE '%$value%'";
                    break;
                case 'bestand':
                    $select .= 'b.name AS stock, ';
                    $join .= "JOIN bestand AS b ON b.inst_id=i.id ";    // TODO only works when institution is selected too!
                    $where .= (empty($where) ? '' : ' AND ')."b.name LIKE '%$value%'";
                    break;
            }
		}
	}

    $query="SELECT $select f.id AS id, f.dc_created AS created, f.dc_title AS title, f.dc_description AS description FROM fotos AS f $join";
    if (!empty($where)){
        $query.=" WHERE $where";        // TODO optimize the order of the results
    }

	$result=mysql_query($query);
    $rowCount = mysql_num_rows($result);
	$xtpl_fotolist->assign('RESULTCOUNT', $rowCount);
	if($rowCount > 0){
			$xtpl_fotolist->parse("list.head_fotolist");
	}

	while($fetch=mysql_fetch_assoc($result)){
        $rowItem['id'] = $fetch['id'];
        $rowItem['image'] = PHOTO_PATH.$fetch['id'].'.jpg';
        $rowItem['title'] = $fetch['title'];
        $rowItem['photograph'] = $fetch['firstname'] . ' ' . $fetch['name'];
        $rowItem['period'] = $fetch['created'];
        $rowItem['institution'] = $fetch['institution'];
        $rowItem['stock'] = $fetch['stock'];

        $xtpl_fotolist->assign("FETCH",$rowItem);
        $xtpl_fotolist->parse('list.row_fotolist');
	}

	$xtpl_fotolist->parse("list");
	$results.=$xtpl_fotolist->text("list");
}
?>