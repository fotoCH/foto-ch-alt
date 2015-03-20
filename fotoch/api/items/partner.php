<?php

include("././fotofunc.inc.php");
include("././foto-ch.inc.php");

$query = "SELECT text_".$language." as text , bild_".$language." as bild, link_".$language." as link FROM logos ORDER by id";
$result = mysql_query($query);
while($fetch = mysql_fetch_assoc($result)){
            $pages[]=$fetch;
}

jsonout($pages);
?>
