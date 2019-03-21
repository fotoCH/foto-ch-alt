<?php

$query = "SELECT name, de, array, ".$language." FROM pages";
$result = mysqli_query($sqli, $query);
while($fetch = mysqli_fetch_array($result)){
            $pages[$fetch['name']]=$fetch[$language];
}

jsonout($pages);
?>
