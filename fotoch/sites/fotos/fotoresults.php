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
                    $arrName = explode(' ', $value);
                    $where .= ($where!='' ? ' AND ' : '')."((n.nachname LIKE '%$arrName[0]%' AND n.vorname LIKE '%$arrName[1]%') OR (n.vorname LIKE '%$arrName[0]%' AND n.nachname LIKE '%$arrName[1]%'))";
                    break;
                case 'period_start':
                    $period_start = "$value-01-01";
                    $where .= ($where!='' ? ' AND ' : '')."(f.dc_created >= '$period_start' OR f.dc_created = 0000-00-00)";
                    break;
                case 'period_end':
                    $period_end = "$value-12-31";
                    $where .= ($where!='' ? ' AND ' : '')."(f.dc_created <= '$period_end' OR f.dc_created = 0000-00-00)";
                    break;
                case 'title':
                    $where .= ($where!='' ? ' AND ' : '')."(f.dc_title LIKE '%$value%' OR f.dc_description LIKE '%$value%' OR f.dc_coverage LIKE '%$value%')";
                    break;
                case 'institution':
                    if ($value != 0) {
                        $where .= ($where!='' ? ' AND ' : '')."i.id='$value'";
                    }
                    break;
                case 'stock':
                    if ($value != 0) {
                        $where .= ($where!='' ? ' AND ' : '')."b.id='$value'";
                    }
                    break;
            }
		}
	}

    $select = 'f.id AS id, f.dc_created AS created, f.dc_title AS title, f.dc_description AS description, image_path, ';
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
    var_dump($query);

	$result=mysql_query($query);
    $rowCount = mysql_num_rows($result);
	$xtpl_fotolist->assign('result_count', $rowCount);
	if($rowCount > 0){
        $xtpl_fotolist->parse("list.table_view.head_fotolist");
	}

    $itrRow = 0;
    // prepare data depending on the current view
    if ($photoViewMode==VIEW_TABLE){
        while(($fetch=mysql_fetch_assoc($result))){
            $rowItem['id'] = $fetch['id'];
            // build the url including all necessary filter parameter
            $rowItem['url'] = '?a=fotos&amp;id='.$fetch['id'];
            $rowItem['url'] .= $_GET['fotograph']!='' ? '&fotograph='.urlencode($_GET['fotograph']) : '';
            $rowItem['url'] .= $_GET['period_start']!=PHOTO_PERIOD_START ? '&period_start='.$_GET['period_start'] : '';
            $rowItem['url'] .= $_GET['period_end']!=date('Y') ? '&period_end='.$_GET['period_end'] : '';
            $rowItem['url'] .= $_GET['title']!='' ? '&title='.$_GET['title'] : '';
            $rowItem['url'] .= $_GET['institution']!=0 ? '&institution='.$_GET['institution'] : '';
            $rowItem['url'] .= $_GET['stock']!=0 ? '&stock='.$_GET['stock'] : '';
            $rowItem['image_src'] = $fetch['image_path'];
            $rowItem['title'] = $fetch['title'];
            $rowItem['title'] .= ($rowItem['title']!='' && $fetch['description']!='' ? ' / ' : '').$fetch['description'];
            $rowItem['photograph'] = $fetch['name'];
            $rowItem['period'] = ($fetch['created']!='0000-00-00' ? date('Y', mktime(0,0,0,1,1,$fetch['created'])) : '');
            $rowItem['institution'] = $fetch['institution'];
            $rowItem['stock'] = $fetch['stock'];

            $xtpl_fotolist->assign("row",$rowItem);
            $xtpl_fotolist->parse('list.table_view.row_fotolist');
            $itrRow++;
        }
        $xtpl_fotolist->parse("list");
        $xtpl_fotolist->parse("list.table_view");
        $results.=$xtpl_fotolist->text("list");
        $results.=$xtpl_fotolist->text("list.table_view");
    } else {
        while(($fetch=mysql_fetch_assoc($result))) {
            $rowItem['id'] = $fetch['id'];
            $rowItem['image_src'] = $fetch['image_path'];

            // build the title
            $rowItem['title'] = $fetch['title'];
            $rowItem['title'] .= (($rowItem['title']!='' && $fetch['description']!='') ? ' / ' : '').$fetch['description'];
            $length = strlen($rowItem['title']);
            $pos=strpos($rowItem['title'], ' ', PHOTO_TILE_TITLE_LENGTH);
            if ($pos) {
                $rowItem['title'] = substr($rowItem['title'],0 , $pos );
            }
            $rowItem['title'] = preg_replace('/[-\,;\\/(:*\"<|&]?$/', '', $rowItem['title']);   // remove special char if exists at the end
            $rowItem['title'].= ($length>PHOTO_TILE_TITLE_LENGTH ? '...' : '');

            $rowItem['photograph'] = $fetch['name'];
            $xtpl_fotolist->assign("tile",$rowItem);
            $xtpl_fotolist->parse('list.tile_view.tile');
            $itrRow++;
        }
        $xtpl_fotolist->parse("list");
        $xtpl_fotolist->parse("list.tile_view");
        $results.=$xtpl_fotolist->text("list");
        $results.=$xtpl_fotolist->text("list.tile_view");
    }

    // load required scripts for the infinite scroll
    $script .= '<script src="js/jquery-1.9.1.min.js"></script>';
    $script .= '<script src="js/jquery.endless-scroll.js"></script>';
    $script .= "<script>
            $(function() {
                var loadedItemsCount = 6;
                $(document).endlessScroll({
                    bottomPixels: 500,
                    fireDelay: 10,
                    callback: function(i) {
                        var last_item = $('#list tbody tr').last();
                        console.log(last_item);
                        // get the next data portion from the server
                        xmlhttp=new XMLHttpRequest();
                        xmlhttp.onreadystatechange=function(){
                            if (xmlhttp.readyState==4 && xmlhttp.status==200){
                                console.log(xmlhttp.responseText);
                                last_item.after(xmlhttp.responseText);
                            }
                        }
                        // collect the current search parameters from the url


                        // find a way on how to store the search parameters in the session on the server


                        xmlhttp.open('GET','ajax.php?action=getNextPhotos&nextItem='+loadedItemsCount,true);
                        xmlhttp.send();
                        }
                });
            });
        </script>";

//    $xtpl->assign('script', $script);
}
?>