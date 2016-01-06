<?php
if ($_GET ['photos']!="1")
    $query = "SELECT *, '' as dc_right, '' as image_path FROM arbeitsorte WHERE lat<>0";
else 
    $query = "SELECT *, CONCAT(dcterms_spatial,': ', dc_title) as name, 'fotoquery' as swissname FROM fotos WHERE lat<>0";
    

$res=getfromselect($query);

jsonout($res);
?>
