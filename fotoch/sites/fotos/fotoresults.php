<?php

$xtpl_fotolist=new XTemplate ("././templates/fotos_list.xtpl");
$xtpl_fotolist->assign("ACTION",$_GET['a']);
$xtpl_fotolist->assign("LANG",$_GET['lang']);
$xtpl_fotolist->assign("SPR",$spr);
$id=$_GET['id'];

$xtpl_fotolist->assign("SPR",$spr);

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
            if (!empty($where)){
                $where .= ' AND ';
            }
            switch ($key){
                case 'fotograph':
                    $arrStr = explode(' ', $value);     // TODO what about names with more than three parts? e.g. Marco von Allmen
                    $where .= "(n.nachname LIKE '%$arrStr[0]%' AND n.vorname LIKE '%$arrStr[1]%') OR (n.vorname LIKE '%$arrStr[0]%' AND n.nachname LIKE '%$arrStr[1]%')";
                    break;
                case 'period_start':
                    $period_start = date('Y-m-d', mktime(0,0,0,1,1,$value));
                    $where .= "f.dc_created >= '$period_start'";
                    break;
                case 'period_end':
                    $period_end = date('Y-m-d', mktime(0,0,0,1,1,$value));
                    $where .= "f.dc_created <= '$period_end'";
                    break;
                case 'title':
                    $where .= "f.dc_title LIKE '%$value%' OR f.dc_description LIKE '%$value%' OR f.dc_coverage LIKE '%$value%'";
                    break;
                case 'institution':
                    if ($value != 0) {
                        $where .= "i.id='$value'";
                    }
                    break;
                case 'bestand':
                    if ($value != 0) {
                        $where .= "b.id='$value'";
                    }
                    break;
            }
		}
	}

    $select = 'f.id AS id, f.dc_created AS created, f.dc_title AS title, f.dc_description AS description, ';
    $select.= 'CONCAT(n.vorname, " ", n.nachname) AS name, ';
    $select.= 'i.name AS institution, ';
    $select.= 'b.name AS stock';

    $join .= "LEFT JOIN namen AS n ON f.dc_creator=n.fotografen_id ";
    $join .= "LEFT JOIN institution AS i ON f.edm_dataprovider=i.id ";
    $join .= "LEFT JOIN bestand AS b ON f.dcterms_ispart_of=b.id ";

    $query="SELECT $select FROM fotos AS f $join";
    if (!empty($where)){
        $query.=" WHERE $where";
    }

	$result=mysql_query($query);
    $rowCount = mysql_num_rows($result);
	$xtpl_fotolist->assign('RESULTCOUNT', $rowCount);
	if($rowCount > 0){
			$xtpl_fotolist->parse("list.head_fotolist");
	}

    $itrRow = 0;
	while(($fetch=mysql_fetch_assoc($result)) && $itrRow<ENDLESS_SCROLL_ITEMS){
        $rowItem['id'] = $fetch['id'];
        $rowItem['image'] = PHOTO_PATH.$fetch['id'].'.jpg';
        $rowItem['title'] = $fetch['title'];
        $rowItem['title'] .= ($rowItem['title']!='' && $fetch['description']!='' ? ' / ' : '').$fetch['description'];
        $rowItem['photograph'] = $fetch['name'];
        $rowItem['period'] = date('Y', mktime(0,0,0,1,1,$fetch['created']));
        $rowItem['institution'] = $fetch['institution'];
        $rowItem['stock'] = $fetch['stock'];

        $xtpl_fotolist->assign("FETCH",$rowItem);
        $xtpl_fotolist->parse('list.row_fotolist');
        $itrRow++;
	}

	$xtpl_fotolist->parse("list");
	$results.=$xtpl_fotolist->text("list");

    // load required scripts for the infinite scroll
    $script .= '<script src="js/jquery-1.9.1.min.js"></script>';
    $script .= '<script src="js/jquery.endless-scroll.js"></script>';
    $script .= '<script>
            $(function() {
                $(document).endlessScroll({
                    bottomPixels: 500,
                    fireDelay: 10,
                    callback: function(i) {
                        var last_item = $("#list tbody tr:last");
                        console.log(last_item);
                        last_item.after(last_item.prev().prev().prev().prev().prev().prev().clone());
                    }
                });
            });
        </script>';

//    $xtpl->assign('script', $script);
}
?>