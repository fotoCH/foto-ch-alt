<?php

$xtpl_fotolist=new XTemplate ("././templates/fotos_list.xtpl");
$xtpl_fotolist->assign("ACTION",$_GET['a']);
$xtpl_fotolist->assign("LANG",$_GET['lang']);
$xtpl_fotolist->assign("SPR",$spr);
$id=$_GET['id'];
$photoViewMode = $_GET['view']!='' ? $_GET['view'] : VIEW_TABLE;
if ($photoViewMode == VIEW_TABLE){
    if (strpos($_SERVER['REQUEST_URI'], 'view='.VIEW_TABLE)){
        $viewUrl = str_replace('view='.VIEW_TABLE, 'view='.VIEW_TILE, $_SERVER['REQUEST_URI']);
    } else {
        $viewUrl = $_SERVER['REQUEST_URI'].'&view='.VIEW_TILE;
    }
    $xtpl_fotolist->assign('view_switch', '<a class="switch_view" href="'.$viewUrl.'">'.$spr['switchToTileView'].'</a>');
} else {
    if (strpos($_SERVER['REQUEST_URI'], 'view='.VIEW_TILE)){
        $viewUrl = str_replace('view='.VIEW_TILE, 'view='.VIEW_TABLE, $_SERVER['REQUEST_URI']);
    } else {
        $viewUrl = $_SERVER['REQUEST_URI'].'&view='.VIEW_TABLE;
    }
    $xtpl_fotolist->assign('view_switch', '<a class="switch_view" href="'.$viewUrl.'">'.$spr['switchToTableView'].'</a>');
}

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
            switch ($key){
                case 'fotograph':
                    $arrStr = explode(' ', $value);     // TODO what about names with more than three parts? e.g. Marco von Allmen
                    $where .= ($where!='' ? ' AND ' : '')."(n.nachname LIKE '%$arrStr[0]%' AND n.vorname LIKE '%$arrStr[1]%') OR (n.vorname LIKE '%$arrStr[0]%' AND n.nachname LIKE '%$arrStr[1]%')";
                    break;
                case 'period_start':
                    $period_start = date('Y-m-d', mktime(0,0,0,1,1,$value));
                    $where .= ($where!='' ? ' AND ' : '')."f.dc_created >= '$period_start'";
                    break;
                case 'period_end':
                    $period_end = date('Y-m-d', mktime(0,0,0,1,1,$value));
                    $where .= ($where!='' ? ' AND ' : '')."f.dc_created <= '$period_end'";
                    break;
                case 'title':
                    $where .= ($where!='' ? ' AND ' : '')."f.dc_title LIKE '%$value%' OR f.dc_description LIKE '%$value%' OR f.dc_coverage LIKE '%$value%'";
                    break;
                case 'institution':
                    if ($value != 0) {
                        $where .= ($where!='' ? ' AND ' : '')."i.id='$value'";
                    }
                    break;
                case 'bestand':
                    if ($value != 0) {
                        $where .= ($where!='' ? ' AND ' : '')."b.id='$value'";
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
	$xtpl_fotolist->assign('result_count', $rowCount);
	if($rowCount > 0){
        $xtpl_fotolist->parse("list.table_view.head_fotolist");
	}

    // prepare data depending on the current view
    if ($photoViewMode==VIEW_TABLE){
        while(($fetch=mysql_fetch_assoc($result))){
            $rowItem['id'] = $fetch['id'];
            $rowItem['image_src'] = PHOTO_PATH.$fetch['id'].'.jpg';
            $rowItem['title'] = $fetch['title'];
            $rowItem['title'] .= ($rowItem['title']!='' && $fetch['description']!='' ? ' / ' : '').$fetch['description'];
            $rowItem['photograph'] = $fetch['name'];
            $rowItem['period'] = date('Y', mktime(0,0,0,1,1,$fetch['created']));
            $rowItem['institution'] = $fetch['institution'];
            $rowItem['stock'] = $fetch['stock'];

            $xtpl_fotolist->assign("row",$rowItem);
            $xtpl_fotolist->parse('list.table_view.row_fotolist');
        }
        $xtpl_fotolist->parse("list");
        $xtpl_fotolist->parse("list.table_view");
        $results.=$xtpl_fotolist->text("list");
        $results.=$xtpl_fotolist->text("list.table_view");
    } else {
        while(($fetch=mysql_fetch_assoc($result))){
            $rowItem['id'] = $fetch['id'];
            $rowItem['image_src'] = PHOTO_PATH.$fetch['id'].'.jpg';

            // build the title
            $rowItem['title'] = $fetch['title'];
            $rowItem['title'] .= ($rowItem['title']!='' && $fetch['description']!='' ? ' / ' : '').$fetch['description'];
            $pos=strpos($rowItem['title'], ' ', PHOTO_TILE_TITLE_LENGTH);
            $rowItem['title'] = substr($rowItem['title'],0 , $pos );
            $rowItem['title'] = str_replace(',', '', $rowItem['title']);
            $rowItem['title'] = str_replace('/', '', $rowItem['title']);
            $rowItem['title'] = str_replace(':', '', $rowItem['title']);

            $rowItem['photograph'] = $fetch['name'];
            $xtpl_fotolist->assign("tile",$rowItem);
            $xtpl_fotolist->parse('list.tile_view.tile');
        }
        $xtpl_fotolist->parse("list");
        $xtpl_fotolist->parse("list.tile_view");
        $results.=$xtpl_fotolist->text("list");
        $results.=$xtpl_fotolist->text("list.tile_view");
    }

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