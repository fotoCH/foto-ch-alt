<?php

$query = "SELECT name, de, array, ".$language." FROM pages";
$result = mysql_query($query);
while($fetch = mysql_fetch_array($result)){
            $pages[$fetch['name']]=$fetch[$language];
}

jsonout($pages);
?>
