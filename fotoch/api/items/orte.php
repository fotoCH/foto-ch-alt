<?php

$query = "SELECT * FROM arbeitsorte WHERE lat<>0";

$res=getfromselect($query);

jsonout($res);
?>
