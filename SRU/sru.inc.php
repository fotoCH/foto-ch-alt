<?php

include_once('sru.datecheck.inc.php');

function photographerHasFotos($id){
    global $sqli;
    $sql='SELECT count(id) FROM `fotos` WHERE dc_creator='.$id;
    $result = mysqli_query($sqli, $sql);
    $row = mysqli_fetch_row($result);
    return ($row[0]>0?'1':'0');
}

function stockHasFotos($id){
    global $sqli;
    $sql='SELECT count(id) FROM `fotos` WHERE dcterms_ispart_of='.$id;
    $result = mysqli_query($sqli, $sql);
    $row = mysqli_fetch_row($result);
    return ($row[0]>0?'1':'0');
}

function expandRecord($isad,$extradata,$position){
$rdata = <<<EOT
<record>
<recordSchema>isad</recordSchema>
<recordPacking>xml</recordPacking>
<recordData>
<isad:archivaldescription>
<isad:identity>
<isad:reference>{$isad['reference']}</isad:reference>
<isad:title>{$isad['title']}</isad:title>
<isad:date>{$isad['date']}</isad:date>
<isad:descriptionlevel>{$isad['descriptionlevel']}</isad:descriptionlevel>
<isad:extent>{$isad['extent']}</isad:extent>
</isad:identity>
<isad:context>
<isad:creator>{$isad['creator']}</isad:creator>
</isad:context>
</isad:archivaldescription>
</recordData>
<recordPosition>$position</recordPosition>
<extraRecordData>
<rel:score xmlns:rel="info:srw/extension/2/relevancy-1.0">{$extradata['score']}</rel:score>
<ap:link xmlns:ap="http://www.archivportal.ch/srw/extension/">{$extradata['link']}</ap:link>
<ap:beginDateISO xmlns:ap="http://www.archivportal.ch/srw/extension/">{$extradata['beginDateISO']}</ap:beginDateISO>
<ap:beginApprox xmlns:ap="http://www.archivportal.ch/srw/extension/">{$extradata['beginApprox']}</ap:beginApprox>
<ap:endDateISO xmlns:ap="http://www.archivportal.ch/srw/extension/">{$extradata['endDateISO']}</ap:endDateISO>
<ap:endApprox xmlns:ap="http://www.archivportal.ch/srw/extension/">{$extradata['endApprox']}</ap:endApprox>
<ap:hasDigitizedItems xmlns:ap="http://www.archivportal.ch/srw/extension/">{$extradata['hasDigitizedItems']}</ap:hasDigitizedItems>
</extraRecordData>
</record>
EOT;
return($rdata);
}

function expandRecords($records, $numberOfRecords, $nextRecordPosition=0){
$nextRecordPosition=($nextRecordPosition==0?'':'<nextRecordPosition>'.$nextRecordPosition."</nextRecordPosition>\n");
$rdata = <<<EOT
<searchRetrieveResponse xmlns="http://www.loc.gov/zing/srw/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:isad="http://www.expertisecentrumdavid.be/xmlschemas/isad.xsd" xsi:schemaLocation="http://www.loc.gov/zing/srw/ http://www.loc.gov/standards/sru/sru1-1archive/xml-files/srw-types.xsd">
<version>1.2</version>
<numberOfRecords>{$numberOfRecords}</numberOfRecords>
<records>{$records}</records>
{$nextRecordPosition}</searchRetrieveResponse>
EOT;
return($rdata);
}

function expandSRUError($errorcode=1, $message='General system error', $detail='SRU Query not supported'){
$rdata = <<<EOT
<searchRetrieveResponse xmlns="http://www.loc.gov/zing/srw/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.loc.gov/zing/srw/ http://www.loc.gov/standards/sru/sru1-1archive/xml-files/srw-types.xsd">
<version>1.2</version>
<numberOfRecords>0</numberOfRecords>
<diagnostics>
<diagnostic xmlns="http://www.loc.gov/zing/srw/diagnostic/">
<uri>info:srw/diagnostic/1/$errorcode</uri>
<details>$detail</details>
<message>$message</message>
</diagnostic>
</diagnostics>
</searchRetrieveResponse>
EOT;
return($rdata);
}

function outputXML($s){
header('Content-Type: text/xml; charset=utf-8');
echo('<?xml version="1.0" encoding="utf-8" standalone="yes"?>'."\n");
echo($s);
}

function sruError($error){
outputXML(expandSRUError(2,"Error parsinq Query",$error));
writeToLog("error: $error, $query fromIP: $_SERVER[SERVER_ADDR]");
exit;
}

function sruNoResults(){
$rdata = <<<EOT
<searchRetrieveResponse xmlns="http://www.loc.gov/zing/srw/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:isad="http://www.expertisecentrumdavid.be/xmlschemas/isad.xsd" xsi:schemaLocation="http://www.loc.gov/zing/srw/ http://www.loc.gov/standards/sru/sru1-1archive/xml-files/srw-types.xsd">
<version>1.2</version>
<numberOfRecords>0</numberOfRecords>
<records/>
</searchRetrieveResponse>
EOT;
return($rdata);
}

function writeToLog($s){
file_put_contents('sru.log', date('Y-M-d H:i:s ').$s. "\n", FILE_APPEND);
}

function resultExample($title='testentry',$position=1){
$isad=array();
$isad['reference']='no ref';
$isad['title']=$title;
$isad['date']='1899 - 1901';
$isad['descriptionlevel']='Testdokument';
$extradata=array();
$extradata['score']='0.9';
$extradata['link']='https://foto-ch.ch/testref';
$extradata['beginDateISO']='1899-01-01';
$extradata['beginApprox']='1';
$extradata['endDateISO']='1901-01-01';
$extradata['endApprox']='1';
$extradata['hasDigitizedItems']='1';
return(expandRecord($isad,$extradata,$position));
}

function resultFromRecordFotograf($r,$position){
$isad=array();
$isad['reference']='';
$isad['title']=htmlspecialchars( $r['nachname'] . ', ' . $r['vorname'] . ' ' . $r['namenszusatz']);
$isad['date']=formldatesimp($r['geburtsdatum'], $r['gen_geburtsdatum'], $r['todesdatum'], $r['gen_todesdatum']);
$isad['descriptionlevel']='Fotograf*in';
$extradata=array();
$extradata['score']='0.9';
$extradata['link']='https://foto-ch.ch/photographer?detail='.$r['id'].'&amp;type=photographer';
$extradata['beginDateISO']=$r['geburtsdatum'];
$extradata['beginApprox']=($r['gen_geburtsdatum']==0?'0':'1');
$extradata['endDateISO']=$r['todesdatum'];
$extradata['endApprox']=($r['gen_todesdatum']==0?'0':'1');
$extradata['hasDigitizedItems']=photographerHasFotos($r['id']);
return(expandRecord($isad,$extradata,$position));
}

function resultFromRecordBestand($r,$position){
$isad=array();
$isad['reference']=$r['signatur'];
$isad['title']=htmlspecialchars( $r['name'] );
$isad['date']=$r['zeitraum'];
$isad['descriptionlevel']='Bestand';
$isad['creator']=htmlspecialchars($r['institution']);
$isad['extent']=$r['umfang'];
$extradata=array();
$extradata['score']='0.9';
$extradata['link']='https://foto-ch.ch/inventory?detail='.$r['id'].'&amp;type=inventory';
$extradata['beginDateISO']='0000-00-00';
$extradata['beginApprox']='1';
$extradata['endDateISO']='0000-00-00';
$extradata['endApprox']='1';
$extradata['hasDigitizedItems']=stockHasFotos($r['id']);
return(expandRecord($isad,$extradata,$position));
}

function resultFromRecordAusstellung($r,$position){
$isad=array();
$isad['reference']='';
$isad['title']=htmlspecialchars( $r['titel'] );
$isad['date']=$r['jahr'];
$isad['descriptionlevel']='Ausstellung';
$isad['creator']=htmlspecialchars($r['institution']);
$isad['extent']='';
$extradata=array();
$extradata['score']='0.9';
$extradata['link']='https://foto-ch.ch/exhibition?detail='.$r['id'].'&amp;type=exhibition';
$extradata['beginDateISO']='0000-00-00';
$extradata['beginApprox']='1';
$extradata['endDateISO']='0000-00-00';
$extradata['endApprox']='1';
$extradata['hasDigitizedItems']='0';
return(expandRecord($isad,$extradata,$position));
}

function resultFromRecordFoto($r,$position){
$isad=array();
$isad['reference']='';
$isad['title']=htmlspecialchars( $r['title'] );
$isad['date']=$r['zeitraum'];
$isad['descriptionlevel']='Fotografie';
$isad['creator']=htmlspecialchars($r['institution']);
$isad['extent']='';
$extradata=array();
$extradata['score']='0.9';
$extradata['link']='https://foto-ch.ch/photo?detail='.$r['id'].'&amp;type=photo';
$extradata['beginDateISO']='0000-00-00';
$extradata['beginApprox']='1';
$extradata['endDateISO']='0000-00-00';
$extradata['endApprox']='1';
$extradata['hasDigitizedItems']='1';
return(expandRecord($isad,$extradata,$position));
}

function resultsExample(){
$records=resultExample();
$records.=resultExample('Entry 2');
$records.=resultExample('Entry 3');
return(expandRecords($records, 1, 8));
}

function toSRUfromStreamresultFotograf($results, &$xmlresults, $scount, $datearray){
    $position=$scount;
    $mycount=0;
    foreach ($results as $r){
        if ($mycount<20){
    	    if (photographerMatchesDateQuery($r,$datearray)){
                $xmlresults.=resultFromRecordFotograf($r, $position++);
	        $mycount++;
	    }
        }
    }
    return($mycount);
}

function toSRUfromStreamresultBestand($results, &$xmlresults, $scount, $datearray){
    $position=$scount;
    $mycount=0;
    foreach ($results as $r){
        if ($mycount<10){
//            $o=print_r($datearray, true)." ".print_r($r, true);
//            writeToLog($o);
    	    if (stockMatchesDateQuery($r,$datearray)){
                $xmlresults.=resultFromRecordBestand($r, $position++);
	        $mycount++;
	    }
        }
   }
    return($mycount);
}

function toSRUfromStreamresultAusstellung($results, &$xmlresults, $scount, $datearray){
    $position=$scount;
    $mycount=0;
    foreach ($results as $r){
        if ($mycount<10){
    	    if (exhibitionMatchesDateQuery($r,$datearray)){
                $xmlresults.=resultFromRecordAusstellung($r, $position++);
                $mycount++;
            }
        }
    }
    return($mycount);
}

function toSRUfromStreamresultFoto($results, &$xmlresults, $scount, $datearray){
    $position=$scount;
    $mycount=0;
    foreach ($results as $r){
        if ($mycount<10){
    	    if (photoMatchesDateQuery($r,$datearray)){
                $xmlresults.=resultFromRecordFoto($r, $position++);
	        $mycount++;
	    }
        }
    }
    return($mycount);
}


function toSRUfromStreamresults($results,$datearray){
$count=0;
$xmlresults="";
$count+=toSRUfromStreamresultFotograf($results['photographer_results'], $xmlresults, $count+1,$datearray);
$count+=toSRUfromStreamresultBestand($results['stock_results'], $xmlresults, $count+1,$datearray);
$count+=toSRUfromStreamresultAusstellung($results['exhibition_results'], $xmlresults, $count+1,$datearray);
$count+=toSRUfromStreamresultFoto($results['photos_results'], $xmlresults, $count+1,$datearray);
return (expandRecords($xmlresults, $count, 0));
}

function getStreamResults($queryarray, $datearray, $maxresults=50){
    $query=join(" ",$queryarray);
    $search = new StreamedSearch();
    $search->setLang('de');
    $search->setLimit($maxresults);
    //$search->setPhotoLimit($_GET['photolimit']);
    //$search->setType('photographer');
    //$search->setSorting($_GET['sort']);
    //$search->setSortDirection($_GET['sortdir']);
    //$search->setOffset($_GET['offset']);
    //$search->setDirectQuery($_GET['direct']);
    //$search->setGeoRef($_GET['georef']);
    $search->setQuery($query);
    $search->query();
    //print_r($search->results);
    return(toSRUfromStreamresults($search->results, $datearray));
}

?>
