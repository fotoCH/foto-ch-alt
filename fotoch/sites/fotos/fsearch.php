<?php
$xtpl_fotosearch=new XTemplate ("././templates/search.xtpl");

$language = $_GET['lang'];
$xtpl_fotosearch->assign("LANG",$language);
$xtpl_fotosearch->assign("SPR",$spr);

$xtpl_fotosearch->assign("ACTION",$_GET['a']);
$xtpl_fotosearch->assign("ID",$_GET['id']);
$id=$_GET['id'];

subgenformitem($xtpl_fotosearch,'textfield',$spr['fotograph'], ($_GET['photograph'] ? $_GET['photograph'] : ''),'photograph');
$arrYears[PHOTO_PERIOD_START-1]='';
for($i=PHOTO_PERIOD_START; $i<=date('Y'); $i++){
    $arrYears[$i] = $i;
}
subgenselectitem($xtpl_fotosearch, $spr['period_start'], ($_GET['period_start'] ? $_GET['period_start'] : ''), "period_start", $arrYears, "", "", "");
unset($arrYears[PHOTO_PERIOD_START-1]);
$arrYears[date('Y')+1]='';
subgenselectitem($xtpl_fotosearch, $spr['period_end'], ($_GET['period_end'] ? $_GET['period_end'] : date('Y')+1), "period_end", $arrYears, "", "", "");

subgenformitem($xtpl_fotosearch,'textfield',$spr['titelbeschreibung'], ($_GET['title'] ? $_GET['title'] : ''),'title');

// retrieve institution data from the database
$query = 'SELECT DISTINCT institution.id, institution.name, institution.name_fr, institution.name_it  FROM `fotos` LEFT JOIN institution on fotos.`edm_dataprovider`=institution.id  WHERE gesperrt >= 0 ORDER BY name';
$objResult=mysqli_query($sqli, $query);

// prepare language specific institution array
if (!$language) $language='de';
switch ($language) {
    case 'de':
        while ($row = mysqli_fetch_assoc($objResult)){
            if ($row['name'] != '') {
                $arrInstitution[$row['id']] = $row['name'];
            } elseif ($row['name_fr'] != '') {
                $arrInstitution[$row['id']] = $row['name_fr'];
            } else {
                $arrInstitution[$row['id']] = $row['name_it'];
            }
        }
        break;
    case 'fr':
        while ($row = mysqli_fetch_assoc($objResult)){
            if ($row['name_fr'] != '') {
                $arrInstitution[$row['id']] = $row['name_fr'];
            } elseif ($row['name'] != '') {
                $arrInstitution[$row['id']] = $row['name'];
            } else {
                $arrInstitution[$row['id']] = $row['name_it'];
            }
        }
        break;
    case 'it':
        while ($row = mysqli_fetch_assoc($objResult)){
            if ($row['name_it'] != '') {
                $arrInstitution[$row['id']] = $row['name_it'];
            } elseif ($row['name'] != '') {
                $arrInstitution[$row['id']] = $row['name'];
            } else {
                $arrInstitution[$row['id']] = $row['name_fr'];
            }
        }
}
// sort the array alphabetically and insert the default selection
asort($arrInstitution);
$arrInstitution = array($spr['all_institution']) + $arrInstitution;
subgenselectitem($xtpl_fotosearch, $spr['institutionen'], ($_GET['institution'] ? $_GET['institution'] : 0), "institution", $arrInstitution, "", "", "", 'getAssociatedStock(this.value)');


// retrieve stock data from the database
$institutionIDs = array_keys($arrInstitution);
unset($institutionIDs[0]);
$institutionList = implode(",", $institutionIDs);

$query = "SELECT DISTINCT bestand.id, bestand.name FROM  `fotos` LEFT JOIN bestand ON fotos.dcterms_ispart_of = bestand.id where gesperrt >= 0 AND inst_id IN($institutionList) ORDER BY bestand.name";
//echo $query;
$objResult=mysqli_query($sqli, $query);
while ($row = mysqli_fetch_assoc($objResult)){
    if ($row['name']!='') {
        $arrBestand[$row['id']] = $row['name'];
    }
}
// sort the array alphabetically and insert the default selection
asort($arrBestand);
$arrBestand = array($spr['all_stock']) + $arrBestand;

subgenselectitem($xtpl_fotosearch, $spr['bestand'], ($_GET['stock'] ? $_GET['stock'] : 0), "stock", $arrBestand, "", "", "");

subgensubmit($xtpl_fotosearch,'submitfield',$spr['submit']);
//subgenreset($xtpl_fotosearch,'resetfield',$spr['reset']);

$script .= "
<script>
getAssociatedStock(".$_GET['institution'].");
function getAssociatedStock(institution_id){
    xmlhttp=new XMLHttpRequest();
    xmlhttp.onreadystatechange=function(){
        if (xmlhttp.readyState==4 && xmlhttp.status==200){
            document.getElementById('stock').innerHTML=xmlhttp.responseText;
        }
    }
    xmlhttp.open('GET','ajax.php?action=getStock&id='+institution_id,true);
    xmlhttp.send();
    }
</script>
";
$xtpl->assign('script', $script);

$xtpl_fotosearch->parse("suchen");
$search.=$xtpl_fotosearch->text("suchen");
?>