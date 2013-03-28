<?php
require("mysql.inc.php");
require("config.inc.php");

// get the language strings
$language = $_COOKIE['lang'];
require("lang.inc.php");

$action=$_GET['action'];

switch ($action) {
    case getStock :
        // return associated stock information for the selected institution
        $id = $_GET['id'];
        if ($id!=0) {
            $query = "SELECT id, name FROM bestand WHERE inst_id=$id ORDER BY name";
        }
        else {
            $query = "SELECT id, name FROM bestand ORDER BY name";
        }
        $result = "<option value='0' selected='selected'>".$spr['all_stock']."</option>";
        $objResult=mysql_query($query);
        while($row = mysql_fetch_assoc($objResult)){
            $result .= "<option value='".$row['id']."'>".$row['name']."</option>";
        }

        echo $result;
        break;
    case getNextPhotos :
        // prepare the query based on the url
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

                        $objResult = mysql_query($query);
                        $value = mysql_fetch_assoc($objResult);
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
                case 'stock':
                    $where = ($where!='' ? ' AND ' : '')."dcterms_ispart_of=$value";
                    break;
            }
        }

        $select = 'f.id AS id, f.dc_created AS created, f.dc_title AS title, f.dc_description AS description, image_path, ';
        $select.= 'CONCAT(n.vorname, " ", n.nachname) AS name, ';
        $select.= 'i.name AS institution, ';
        $select.= 'b.name AS stock';

        $join .= "LEFT JOIN namen AS n ON f.dc_creator=n.fotografen_id ";
        $join .= "LEFT JOIN institution AS i ON f.edm_dataprovider=i.id ";
        $join .= "LEFT JOIN bestand AS b ON f.dcterms_ispart_of=b.id ";

        $query = "SELECT $select FROM fotos AS f $join";
        if (!empty($where)){
            $query .= " WHERE $where";
        }
        $query .= ' LIMIT '.$_GET['nextItem'].', '.ENDLESS_SCROLL_ITEMS;

        $objResult = mysql_query($query);
        $mode = $_GET['view'];
        $result = '';
        if ($mode==VIEW_TILE) {
            while ($arrResult = mysql_fetch_assoc($objResult)){
                $rowItem['id'] = $arrResult['id'];
                $rowItem['image_src'] = $arrResult['image_path'];

                // build the title
                $rowItem['title'] = $arrResult['title'];
                $rowItem['title'] .= (($rowItem['title']!='' && $arrResult['description']!='') ? ' / ' : '').$arrResult['description'];
                $length = strlen($rowItem['title']);
                $pos=strpos($rowItem['title'], ' ', PHOTO_TILE_TITLE_LENGTH);
                if ($pos) {
                    $rowItem['title'] = substr($rowItem['title'],0 , $pos );
                }
                $rowItem['title'] = preg_replace('/[-\,;\\/(:*\"<|&]?$/', '', $rowItem['title']);   // remove special char if exists at the end

                $rowItem['title'].= ($length>PHOTO_TILE_TITLE_LENGTH ? '...' : '');
                $rowItem['photograph'] = $arrResult['name'];

                $result .= "<div class='tile photo'>
                                <a href='./?a=fotos&amp;id=".$rowItem['id']."'>
                                    <figure>
                                        <div class='image_container'>
                                            <img src='".$rowItem['image_src']."' alt='".$rowItem['title']."'>
                                        </div>
                                        <figcaption>
                                            <span class='title'>".$rowItem['title']."</span>
                                            <span class='photograph'>".$rowItem['photograph']."</span>
                                        </figcaption>
                                    </figure>
                                </a>
                            </div>";
            }
        } else {
            while ($arrResult = mysql_fetch_assoc($objResult)){
                $rowItem['id'] = $arrResult['id'];
                $rowItem['url'] = '?a=fotos&amp;id='.$arrResult['id'];
                $rowItem['url'] .= $_GET['photograph']!='' ? ('&photograph='.urlencode($_GET['photograph'])) : '';
                $rowItem['url'] .= $_GET['period_start']!=PHOTO_PERIOD_START ? '&period_start='.$_GET['period_start'] : '';
                $rowItem['url'] .= $_GET['period_end']!=date('Y') ? '&period_end='.$_GET['period_end'] : '';
                $rowItem['url'] .= $_GET['title']!='' ? '&title='.$_GET['title'] : '';
                $rowItem['url'] .= $_GET['institution']!=0 ? '&institution='.$_GET['institution'] : '';
                $rowItem['url'] .= $_GET['stock']!=0 ? '&stock='.$_GET['stock'] : '';
                $rowItem['image_src'] = $arrResult['image_path'];
                $rowItem['title'] = $arrResult['title'];
                $rowItem['title'] .= ($rowItem['title']!='' && $arrResult['description']!='' ? ' / ' : '').$arrResult['description'];
                $rowItem['photograph'] = $arrResult['name'];
                $rowItem['period'] = ($arrResult['created']!='0000-00-00' ? date('Y', mktime(0,0,0,1,1,$arrResult['created'])) : '');
                $rowItem['institution'] = $arrResult['institution'];
                $rowItem['stock'] = $arrResult['stock'];

                $result .= "<tr>
                            <td class='list_photo'><a href='".$rowItem['url']."'><img src='".$rowItem['image_src']."' alt='".$rowItem['title']."'></a></td>
                            <td class='list_title'><a href='".$rowItem['url']."'>".$rowItem['title']."</a></td>
                            <td class='list_photograph'>".$rowItem['photograph']."</td>
                            <td class='list_period'>".$rowItem['period']."</td>
                            <td class='list_institution'>".$rowItem['institution']."</td>
                            <td class='list_stock'>".$rowItem['stock']."</td>
                       </tr>";
            }
        }
        echo $result;
        break;
}
