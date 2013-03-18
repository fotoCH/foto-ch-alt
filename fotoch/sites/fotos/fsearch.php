<?php

$searchmodes = array("ein","erw");
$searchmode=$_GET['mod'];
if(! in_array($searchmode,$searchmodes)) {
    $searchmode="ein";
}

$xtpl_fotosearch=new XTemplate ("././templates/search.xtpl");

$language = $_GET['lang'];
$xtpl_fotosearch->assign("LANG",$language);
$xtpl_fotosearch->assign("SEARCHMODE",$searchmode);
$xtpl_fotosearch->assign("SPR",$spr);

$xtpl_fotosearch->assign("ACTION",$_GET['a']);
$xtpl_fotosearch->assign("ID",$_GET['id']);
$id=$_GET['id'];

subgenformitem($xtpl_fotosearch,'textfield',$spr['fotograph'], ($_GET['fotograph'] ? $_GET['fotograph'] : ''),'fotograph');

for($i=PERIOD_START; $i<=date('Y'); $i++){
    $arrYears[$i] = $i;
}
subgenselectitem($xtpl_fotosearch, $spr['period_start'], ($_GET['period_start'] ? $_GET['period_start'] : ''), "period_start", $arrYears, "", "", "");
subgenselectitem($xtpl_fotosearch, $spr['period_end'], ($_GET['period_end'] ? $_GET['period_end'] : date('Y')), "period_end", $arrYears, "", "", "");

subgenformitem($xtpl_fotosearch,'textfield',$spr['titel'], ($_GET['title'] ? $_GET['title'] : ''),'title');

// retrieve institution data from the database
$query = 'SELECT id, name, name_fr, name_it FROM institution';
$objResult=mysql_query($query);

// prepare language specific institution array
$arrInstitution['0'] = $spr['all'];
switch ($language) {
    case 'de':
        while ($row = mysql_fetch_assoc($objResult)){
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
        while ($row = mysql_fetch_assoc($objResult)){
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
        while ($row = mysql_fetch_assoc($objResult)){
            if ($row['name_it'] != '') {
                $arrInstitution[$row['id']] = $row['name_it'];
            } elseif ($row['name'] != '') {
                $arrInstitution[$row['id']] = $row['name'];
            } else {
                $arrInstitution[$row['id']] = $row['name_fr'];
            }
        }
}
subgenselectitem($xtpl_fotosearch, $spr['institution'], ($_GET['institution'] ? $_GET['institution'] : 0), "institution", $arrInstitution, "", "", "", 'getAssociatedStock(this.value)');

// retrieve stock data from the database
$query = 'SELECT id, name FROM bestand';
$objResult=mysql_query($query);
$arrBestand['0'] = $spr['all'];
while ($row = mysql_fetch_assoc($objResult)){
    $arrBestand[$row['id']] = $row['name'];
}
subgenselectitem($xtpl_fotosearch, $spr['bestand'], ($_GET['stock'] ? $_GET['stock'] : 0), "stock", $arrBestand, "", "", "");

subgensubmit($xtpl_fotosearch,'submitfield',$spr['submit']);

$script = "
<script>
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