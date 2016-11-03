<?php
header("Cache-Control: no-cache, must-revalidate");
require("mysql.inc.php");
require("config.inc.php");

// get the language strings
$language = $_COOKIE['lang'];
require("lang.inc.php");

$action=$_GET['action'];

switch ($action) {
    case 'getLang' :
        echo $language;
        break;
    case 'sendFeedback' :
        // send feedback from feedback button via mail
        $to = "info@foto-ch.ch";
        $from = $_POST['sender'];
        if( $from == '') {
            $from = "info@foto-ch.ch";
        }
        $subject = "FotoCH - Feedback Button";
        $message = $_POST['feedback'];
        $headers .= 'From: ' . $from . "\r\n";

        if(mail($to,$subject,$message,$headers)) {
            echo "Message Sent";
        }
        else {
            echo "Message Not Sent";
        }
        break;
    case 'getStock' :
        // return associated stock information for the selected institution
        $id = $_GET['id'];
        if ($id!=0) {
            $query = "SELECT DISTINCT bestand.id, bestand.name FROM  `fotos` LEFT JOIN bestand ON fotos.dcterms_ispart_of = bestand.id where gesperrt >= 0 AND inst_id=$id ORDER BY bestand.name";
        }
        else {
            $query = "SELECT DISTINCT bestand.id, bestand.name FROM  `fotos` LEFT JOIN bestand ON fotos.dcterms_ispart_of = bestand.id where gesperrt >= 0 ORDER BY bestand.name";
        }
        $result = "<option value='0' selected='selected'>".$spr['all_stock']."</option>";
        $objResult=mysqli_query($sqli, $query);
        while($row = mysqli_fetch_assoc($objResult)){
            $result .= "<option value='".$row['id']."'>".$row['name']."</option>";
        }

        echo $result;
        break;
    case 'fotoAll' :
        // return associated stock information for the selected institution
        $id = $_GET['id'];
        if ($id!=0) {
            $query = "SELECT `all` FROM fotos WHERE id=$id";
        }
        $objResult=mysqli_query($sqli, $query);
        $row = mysqli_fetch_assoc($objResult);
        $result = $row['all'];
        if ($_GET['form']){
    	    $result="<pre>".$result."</pre>";
        }

        echo( $result);
        break;
    case 'getNextPhotos' :
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
                            $query = "SELECT fotografen_id FROM namen WHERE ((nachname LIKE '%$arrName[0]%' AND vorname LIKE '%$arrName[1]%') OR (vorname LIKE '%$arrName[0]%' AND nachname LIKE '%$arrName[1]%')) LIMIT 0,1";
                        } else {
                            $query = "SELECT fotografen_id FROM namen WHERE (nachname LIKE '%$arrName[0]%' OR vorname LIKE '%$arrName[0]%') LIMIT 0,1";
                        }

                        $objResult = mysqli_query($sqli, $query);
                        $value = mysqli_fetch_assoc($objResult);
                        $value = $value['fotografen_id'];
                        if ($value==1294) $value=1295; // quickfix for "Jost"
                    }
                    $where = ($where!='' ? ' AND ' : '')."dc_creator=$value";
                    break;
                case 'period_start':
                    $period = "$value-01-01";
                    $where .= ($where!='' ? ' AND ' : '')."(dc_created >= '$period' OR dc_created ='0000-00-00')";
                    break;
                case 'period_end':
                    $period = "$value-12-31";
                    $where .= ($where!='' ? ' AND ' : '')."(dc_created <= '$period' OR dc_created ='0000-00-00')";
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

        $select = 'f.id AS id, f.dc_created, f.zeitraum AS created, f.dc_title AS title, f.dc_description AS description, image_path, ';
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

        $objResult = mysqli_query($sqli, $query);
        $mode = $_GET['view'];
        $result = '';
        if ($mode==VIEW_TILE) {
            while ($arrResult = mysqli_fetch_assoc($objResult)){
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
            while ($arrResult = mysqli_fetch_assoc($objResult)){
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
                $rowItem['period'] = ($arrResult['created']!='0000-00-00' ? $arrResult['created'] : '');
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
        echo($result);
        break;
}
